<?php

namespace App\Http\Controllers;

use App\Models\Client;
use App\Models\Project;
use App\Traits\Downloadable;
use Illuminate\Http\Request;

class ProjectController extends Controller
{
    use Downloadable;
    // List projects
    public function index(Request $request)
    {
        $q = trim((string) $request->get('q'));

        $projectsQuery = Project::with('client');
        if ($q !== '') {
            $projectsQuery->where(function ($p) use ($q) {
                $p->where('name', 'like', "%{$q}%")
                  ->orWhere('start_date', 'like', "%{$q}%")
                  ->orWhere('end_date', 'like', "%{$q}%");
            })->orWhereHas('client', function ($c) use ($q) {
                $c->where('name', 'like', "%{$q}%");
            });
        }

        $projects = $projectsQuery->latest()->paginate(15)->appends($request->query());
        return view('projects.index', compact('projects'));
    }

    // Show create form
    public function create()
    {
        $clients = Client::orderBy('name')->get();
        return view('projects.create', compact('clients'));
    }

    // Store new project
    public function store(Request $request)
    {
        $validated = $request->validate([
            'client_id'      => 'required|exists:clients,id',
            'name'           => 'required|string|max:255',
            'start_date'     => 'nullable|date',
            'end_date'       => 'nullable|date|after_or_equal:start_date',
            'contract_value' => 'nullable|numeric|min:0',
            'amount_paid'    => 'nullable|numeric|min:0',
            'amount_remaining' => 'nullable|numeric|min:0',
            'status'         => 'nullable|string|in:planned,active,completed,on-hold',
            'notes'          => 'nullable|string|max:1000',
        ]);

        // Set default values for nullable numeric fields
        $validated['contract_value'] = $validated['contract_value'] ?? 0;
        $validated['amount_paid'] = $validated['amount_paid'] ?? 0;
        $validated['amount_remaining'] = $validated['amount_remaining'] ?? 0;
        $validated['status'] = $validated['status'] ?? 'planned';

        Project::create($validated);

        return redirect()->route('projects.index')
                         ->with('success', 'Project created successfully.');
    }

    // Display the specified project.
    public function show(Project $project)
    {
        $project->load('client', 'incomes');
        return view('projects.show', compact('project'));
    }

    // Show the form for editing the specified project.
    public function edit(Project $project)
    {
        $clients = Client::orderBy('name')->get();
        return view('projects.edit', compact('project', 'clients'));
    }

    // Update the specified project in storage.
    public function update(Request $request, Project $project)
    {
        $validated = $request->validate([
            'client_id'      => 'required|exists:clients,id',
            'name'           => 'required|string|max:255',
            'start_date'     => 'nullable|date',
            'end_date'       => 'nullable|date|after_or_equal:start_date',
            'contract_value' => 'nullable|numeric|min:0',
            'amount_paid'    => 'nullable|numeric|min:0',
            'amount_remaining' => 'nullable|numeric|min:0',
            'notes'          => 'nullable|string|max:1000',
            'status'         => 'nullable|string|in:planned,active,completed,on-hold',
        ]);

        // Set default values for nullable numeric fields
        $validated['contract_value'] = $validated['contract_value'] ?? 0;
        $validated['amount_paid'] = $validated['amount_paid'] ?? 0;
        $validated['amount_remaining'] = $validated['amount_remaining'] ?? 0;

        $project->update($validated);

        return redirect()->route('projects.show', $project)
                         ->with('success', 'Project updated successfully.');
    }

    // Remove the specified project from storage.
    public function destroy(Project $project)
    {
        $project->delete();

        return redirect()->route('projects.index')
                         ->with('success', 'Project deleted successfully.');
    }
    
    /**
     * Export projects as CSV
     */
    public function exportCsv(Request $request)
    {
        $filename = $request->get('filename', 'projects');
        
        $projects = Project::with('client')->get();
        
        $headers = [
            'id' => 'ID',
            'name' => 'Project Name',
            'client_name' => 'Client Name',
            'contract_value' => 'Contract Value (RWF)',
            'start_date' => 'Start Date',
            'end_date' => 'End Date',
            'status' => 'Status',
            'description' => 'Description',
            'created_at' => 'Created Date'
        ];
        
        // Transform data for CSV
        $csvData = $projects->map(function ($project) {
            return [
                'id' => $project->id,
                'name' => $project->name,
                'client_name' => $project->client_name ?? ($project->client ? $project->client->name : 'N/A'),
                'contract_value' => $project->contract_value ?? 0,
                'start_date' => $project->start_date ? \Carbon\Carbon::parse($project->start_date)->format('Y-m-d') : 'N/A',
                'end_date' => $project->end_date ? \Carbon\Carbon::parse($project->end_date)->format('Y-m-d') : 'N/A',
                'status' => ucfirst($project->status ?? 'N/A'),
                'description' => $project->description ?? 'N/A',
                'created_at' => $project->created_at->format('Y-m-d H:i:s')
            ];
        });
        
        return $this->downloadCsv($csvData, $filename, array_keys($headers));
    }
    
    /**
     * Export projects as PDF
     */
    public function exportPdf(Request $request)
    {
        $filename = $request->get('filename', 'projects');
        
        $projects = Project::with('client')->get();
        
        $html = $this->generatePdfHtml('exports.projects-pdf', [
            'data' => $projects,
            'title' => 'Projects Report',
            'subtitle' => 'Complete list of all projects',
            'totalRecords' => $projects->count()
        ]);
        
        return $this->downloadPdf($html, $filename);
    }
}
