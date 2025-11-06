<?php

namespace App\Http\Controllers;

use App\Models\Client;
use App\Models\Project;
use App\Services\BusinessQueryService;
use App\Traits\Downloadable;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class ProjectController extends Controller
{
    use Downloadable;
    
    protected BusinessQueryService $queryService;

    public function __construct(BusinessQueryService $queryService)
    {
        $this->middleware('auth');
        $this->middleware('tenant.data');
        $this->queryService = $queryService;
    }

    // List projects with role-based filtering
    public function index(Request $request)
    {
        $q = trim((string) $request->get('q'));

        // Use BusinessQueryService for role-based filtering
        $projectsQuery = $this->queryService->buildRoleBasedQuery('projects');
        
        if ($q !== '') {
            $projectsQuery->where(function ($query) use ($q) {
                $query->where('name', 'like', "%{$q}%")
                      ->orWhere('description', 'like', "%{$q}%")
                      ->orWhere('project_code', 'like', "%{$q}%");
            });
        }

        // Apply additional filters
        if ($request->filled('status')) {
            $projectsQuery->where('status', $request->status);
        }

        if ($request->filled('client_id')) {
            $projectsQuery->where('client_id', $request->client_id);
        }

        if ($request->filled('manager_id')) {
            $projectsQuery->where('manager_id', $request->manager_id);
        }

        $projects = $projectsQuery->latest()->paginate(15)->appends($request->query());

        // Get statistics with role-based access
        $stats = $this->queryService->getDashboardStats()['projects'] ?? [];
        
        // Get clients for filter dropdown (role-based)
        $clients = $this->queryService->buildRoleBasedQuery('clients')->get();
        
        // Get managers for filter dropdown (if user has access)
        $managers = [];
        if ($this->queryService->canAccessUserData()) {
            $managers = $this->queryService->buildRoleBasedQuery('users')
                             ->whereHas('roles', function($q) {
                                 $q->whereIn('name', ['manager', 'admin']);
                             })
                             ->get();
        }

        return view('projects.index', compact('projects', 'stats', 'clients', 'managers'));
    }

    // Show create form
    public function create()
    {
        // Get clients with role-based access
        $clients = $this->queryService->buildRoleBasedQuery('clients')->orderBy('name')->get();
        
        // Get potential managers (if user has access)
        $managers = [];
        if ($this->queryService->canAccessUserData()) {
            $managers = $this->queryService->buildRoleBasedQuery('users')
                             ->whereHas('roles', function($q) {
                                 $q->whereIn('name', ['manager', 'admin']);
                             })
                             ->orderBy('name')
                             ->get();
        }
        
        return view('projects.create', compact('clients', 'managers'));
    }

    // Store new project with tenant awareness
    public function store(Request $request)
    {
        $validated = $request->validate([
            'client_id'      => 'required|exists:clients,id',
            'name'           => 'required|string|max:255',
            'project_code'   => 'nullable|string|max:50|unique:projects,project_code,NULL,id,tenant_id,' . app('currentTenant')->id,
            'description'    => 'nullable|string',
            'start_date'     => 'nullable|date',
            'end_date'       => 'nullable|date|after_or_equal:start_date',
            'budget'         => 'nullable|numeric|min:0',
            'contract_value' => 'nullable|numeric|min:0',
            'manager_id'     => 'nullable|exists:users,id',
            'status'         => 'nullable|string|in:planned,active,completed,on-hold,cancelled',
            'priority'       => 'nullable|string|in:low,medium,high,urgent',
            'client_visible' => 'boolean',
            'notes'          => 'nullable|string',
        ]);

        // Add tenant and creator information
        $validated['tenant_id'] = app('currentTenant')->id;
        $validated['created_by'] = Auth::id();
        $validated['status'] = $validated['status'] ?? 'planned';
        $validated['priority'] = $validated['priority'] ?? 'medium';

        // Validate client belongs to current tenant
        $clientExists = $this->queryService->buildRoleBasedQuery('clients')
                             ->where('id', $validated['client_id'])
                             ->exists();

        if (!$clientExists) {
            return back()->withErrors(['client_id' => 'Invalid client selected.']);
        }

        Project::create($validated);

        return redirect()->route('projects.index')
            ->with('success', 'Project created successfully.');
    }

    // Display the specified project with role-based access
    public function show(Project $project)
    {
        // Ensure project belongs to current tenant and user has access
        $projectData = $this->queryService->buildRoleBasedQuery('projects')
                            ->where('id', $project->id)
                            ->first();

        if (!$projectData) {
            abort(404, 'Project not found or access denied.');
        }

        $project->load('client');

        // Get project statistics with role-based access
        $stats = [
            'total_tasks' => $this->queryService->buildRoleBasedQuery('tasks')
                                  ->where('project_id', $project->id)
                                  ->count(),
            'completed_tasks' => $this->queryService->buildRoleBasedQuery('tasks')
                                      ->where('project_id', $project->id)
                                      ->where('status', 'completed')
                                      ->count(),
            'total_time' => $this->queryService->buildRoleBasedQuery('time_entries')
                                 ->where('project_id', $project->id)
                                 ->sum('hours'),
            'total_expenses' => $this->queryService->buildRoleBasedQuery('expenses')
                                     ->where('project_id', $project->id)
                                     ->sum('amount'),
        ];

        return view('projects.show', compact('project', 'stats'));
    }

    // Show the form for editing the specified project
    public function edit(Project $project)
    {
        // Ensure project belongs to current tenant and user has access
        $projectData = $this->queryService->buildRoleBasedQuery('projects')
                            ->where('id', $project->id)
                            ->first();

        if (!$projectData) {
            abort(404, 'Project not found or access denied.');
        }

        $clients = $this->queryService->buildRoleBasedQuery('clients')->orderBy('name')->get();
        
        // Get managers if user has access
        $managers = [];
        if ($this->queryService->canAccessUserData()) {
            $managers = $this->queryService->buildRoleBasedQuery('users')
                             ->whereHas('roles', function($q) {
                                 $q->whereIn('name', ['manager', 'admin']);
                             })
                             ->orderBy('name')
                             ->get();
        }

        return view('projects.edit', compact('project', 'clients', 'managers'));
    }

    // Update the specified project with role-based validation
    public function update(Request $request, Project $project)
    {
        // Ensure project belongs to current tenant and user has access
        $projectData = $this->queryService->buildRoleBasedQuery('projects')
                            ->where('id', $project->id)
                            ->first();

        if (!$projectData) {
            abort(404, 'Project not found or access denied.');
        }

        $validated = $request->validate([
            'client_id'      => 'required|exists:clients,id',
            'name'           => 'required|string|max:255',
            'project_code'   => 'nullable|string|max:50|unique:projects,project_code,' . $project->id . ',id,tenant_id,' . app('currentTenant')->id,
            'description'    => 'nullable|string',
            'start_date'     => 'nullable|date',
            'end_date'       => 'nullable|date|after_or_equal:start_date',
            'budget'         => 'nullable|numeric|min:0',
            'contract_value' => 'nullable|numeric|min:0',
            'manager_id'     => 'nullable|exists:users,id',
            'status'         => 'nullable|string|in:planned,active,completed,on-hold,cancelled',
            'priority'       => 'nullable|string|in:low,medium,high,urgent',
            'client_visible' => 'boolean',
            'notes'          => 'nullable|string',
        ]);

        $validated['updated_by'] = Auth::id();
        $project->update($validated);

        return redirect()->route('projects.index')
            ->with('success', 'Project updated successfully.');
    }

    // Remove the specified project
    public function destroy(Project $project)
    {
        // Ensure project belongs to current tenant and user has access
        $projectData = $this->queryService->buildRoleBasedQuery('projects')
                            ->where('id', $project->id)
                            ->first();

        if (!$projectData) {
            abort(404, 'Project not found or access denied.');
        }

        // Check for dependencies
        $hasTasks = $this->queryService->buildRoleBasedQuery('tasks')
                         ->where('project_id', $project->id)
                         ->exists();

        if ($hasTasks) {
            return back()->with('error', 'Cannot delete project with existing tasks.');
        }

        $project->delete();

        return redirect()->route('projects.index')
            ->with('success', 'Project deleted successfully.');
    }
    
    /**
     * Export projects as CSV
     */
    public function exportCsv(Request $request)
    {
        // Check permission for project export
        if (!Auth::user()->can('projects.export')) {
            abort(403, 'You do not have permission to export projects.');
        }
        
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
        // Check permission for project export
        if (!Auth::user()->can('projects.export')) {
            abort(403, 'You do not have permission to export projects.');
        }
        
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
