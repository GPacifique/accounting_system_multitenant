<?php

namespace App\Http\Controllers;

use App\Models\Client;
use App\Models\Project;
use Illuminate\Http\Request;

class ProjectController extends Controller
{
    // List projects
    public function index()
    {
        $projects = Project::with('client')->latest()->paginate(15);
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
}
