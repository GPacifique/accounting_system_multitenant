<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;
use Carbon\Carbon;
use App\Models\Task;
use App\Models\Project;
use App\Models\User;
use App\Models\Tenant;

class SampleTasksSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $this->command->info('Creating sample tasks for testing...');

        if (!Schema::hasTable('tasks')) {
            $this->command->error('Tasks table does not exist. Please run migrations first.');
            return;
        }

        // Get the first tenant
        $tenant = Tenant::first();
        
        if (!$tenant) {
            $this->command->warn('No tenant found. Please ensure tenants are created first.');
            return;
        }

        // Get some projects and users for assignment
        $projects = Project::limit(5)->get();
        $users = User::limit(10)->get();

        if ($projects->isEmpty()) {
            $this->command->warn('No projects found. Tasks will be created without project assignment.');
        }

        if ($users->isEmpty()) {
            $this->command->error('No users found. Cannot create tasks without users.');
            return;
        }

        $tasks = [
            [
                'title' => 'Prepare Construction Site',
                'description' => 'Clear the construction site, set up temporary fencing, and prepare access routes for heavy machinery.',
                'priority' => 'high',
                'status' => 'completed',
                'project_id' => $projects->isNotEmpty() ? $projects->first()->id : null,
                'assigned_to' => $users->random()->id,
                'created_by' => $users->first()->id,
                'start_date' => Carbon::now()->subDays(20),
                'due_date' => Carbon::now()->subDays(15),
                'completed_date' => Carbon::now()->subDays(16),
                'estimated_hours' => 24,
                'actual_hours' => 22,
                'estimated_cost' => 50000,
                'actual_cost' => 48000,
                'notes' => 'Site preparation completed ahead of schedule.',
                'created_at' => Carbon::now()->subDays(21),
                'updated_at' => Carbon::now()->subDays(16),
            ],
            [
                'title' => 'Foundation Excavation',
                'description' => 'Excavate foundation trenches according to architectural plans. Ensure proper depth and dimensions.',
                'priority' => 'high',
                'status' => 'completed',
                'project_id' => $projects->isNotEmpty() ? $projects->first()->id : null,
                'assigned_to' => $users->random()->id,
                'created_by' => $users->first()->id,
                'start_date' => Carbon::now()->subDays(15),
                'due_date' => Carbon::now()->subDays(10),
                'completed_date' => Carbon::now()->subDays(11),
                'estimated_hours' => 40,
                'actual_hours' => 38,
                'estimated_cost' => 120000,
                'actual_cost' => 115000,
                'notes' => 'Excavation completed successfully with minor soil condition adjustments.',
                'created_at' => Carbon::now()->subDays(16),
                'updated_at' => Carbon::now()->subDays(11),
            ],
            [
                'title' => 'Concrete Foundation Pour',
                'description' => 'Pour concrete foundation with proper reinforcement. Quality control and curing procedures must be followed.',
                'priority' => 'urgent',
                'status' => 'in_progress',
                'project_id' => $projects->isNotEmpty() ? $projects->first()->id : null,
                'assigned_to' => $users->random()->id,
                'created_by' => $users->first()->id,
                'start_date' => Carbon::now()->subDays(5),
                'due_date' => Carbon::now()->addDays(2),
                'estimated_hours' => 32,
                'actual_hours' => 20,
                'estimated_cost' => 250000,
                'notes' => 'Weather conditions favorable for concrete work.',
                'created_at' => Carbon::now()->subDays(8),
                'updated_at' => Carbon::now()->subDays(1),
            ],
            [
                'title' => 'Electrical Rough-In',
                'description' => 'Install electrical conduits, junction boxes, and rough wiring throughout the building structure.',
                'priority' => 'medium',
                'status' => 'pending',
                'project_id' => $projects->count() > 1 ? $projects->get(1)->id : ($projects->isNotEmpty() ? $projects->first()->id : null),
                'assigned_to' => $users->random()->id,
                'created_by' => $users->first()->id,
                'start_date' => Carbon::now()->addDays(5),
                'due_date' => Carbon::now()->addDays(12),
                'estimated_hours' => 60,
                'estimated_cost' => 180000,
                'notes' => 'Coordinate with plumbing contractor to avoid conflicts.',
                'created_at' => Carbon::now()->subDays(3),
                'updated_at' => Carbon::now()->subDays(1),
            ],
            [
                'title' => 'Plumbing Installation',
                'description' => 'Install water supply lines, drainage systems, and fixture rough-ins.',
                'priority' => 'medium',
                'status' => 'pending',
                'project_id' => $projects->count() > 1 ? $projects->get(1)->id : ($projects->isNotEmpty() ? $projects->first()->id : null),
                'assigned_to' => $users->random()->id,
                'created_by' => $users->first()->id,
                'start_date' => Carbon::now()->addDays(7),
                'due_date' => Carbon::now()->addDays(15),
                'estimated_hours' => 45,
                'estimated_cost' => 160000,
                'notes' => 'All materials ordered and ready for installation.',
                'created_at' => Carbon::now()->subDays(2),
                'updated_at' => Carbon::now(),
            ],
            [
                'title' => 'HVAC System Design Review',
                'description' => 'Review and approve HVAC system designs with mechanical engineer. Ensure compliance with building codes.',
                'priority' => 'high',
                'status' => 'in_progress',
                'project_id' => $projects->count() > 2 ? $projects->get(2)->id : ($projects->isNotEmpty() ? $projects->first()->id : null),
                'assigned_to' => $users->random()->id,
                'created_by' => $users->first()->id,
                'start_date' => Carbon::now()->subDays(3),
                'due_date' => Carbon::now()->addDays(1),
                'estimated_hours' => 16,
                'actual_hours' => 12,
                'estimated_cost' => 25000,
                'notes' => 'Initial review completed, waiting for engineer feedback.',
                'created_at' => Carbon::now()->subDays(5),
                'updated_at' => Carbon::now(),
            ],
            [
                'title' => 'Safety Inspection Preparation',
                'description' => 'Prepare all documentation and ensure site compliance for upcoming safety inspection.',
                'priority' => 'urgent',
                'status' => 'pending',
                'project_id' => $projects->count() > 2 ? $projects->get(2)->id : ($projects->isNotEmpty() ? $projects->first()->id : null),
                'assigned_to' => $users->random()->id,
                'created_by' => $users->first()->id,
                'start_date' => Carbon::now()->addDays(1),
                'due_date' => Carbon::now()->addDays(3),
                'estimated_hours' => 8,
                'estimated_cost' => 15000,
                'notes' => 'Critical deadline - inspection scheduled for next week.',
                'created_at' => Carbon::now()->subDays(1),
                'updated_at' => Carbon::now(),
            ],
            [
                'title' => 'Material Procurement - Steel Beams',
                'description' => 'Source and procure structural steel beams according to engineering specifications.',
                'priority' => 'high',
                'status' => 'in_progress',
                'project_id' => $projects->count() > 3 ? $projects->get(3)->id : ($projects->isNotEmpty() ? $projects->first()->id : null),
                'assigned_to' => $users->random()->id,
                'created_by' => $users->first()->id,
                'start_date' => Carbon::now()->subDays(10),
                'due_date' => Carbon::now()->subDays(2),
                'estimated_hours' => 12,
                'actual_hours' => 8,
                'estimated_cost' => 500000,
                'notes' => 'Supplier delays affecting delivery schedule.',
                'created_at' => Carbon::now()->subDays(12),
                'updated_at' => Carbon::now()->subDays(1),
            ],
            [
                'title' => 'Quality Control Checklist',
                'description' => 'Develop comprehensive quality control checklist for all construction phases.',
                'priority' => 'low',
                'status' => 'pending',
                'project_id' => null,
                'assigned_to' => $users->random()->id,
                'created_by' => $users->first()->id,
                'start_date' => Carbon::now()->addDays(10),
                'due_date' => Carbon::now()->addDays(20),
                'estimated_hours' => 20,
                'estimated_cost' => 30000,
                'notes' => 'Reference industry best practices and local building codes.',
                'created_at' => Carbon::now(),
                'updated_at' => Carbon::now(),
            ],
            [
                'title' => 'Project Budget Review',
                'description' => 'Review project budget allocations and update cost projections based on current progress.',
                'priority' => 'medium',
                'status' => 'pending',
                'project_id' => $projects->count() > 4 ? $projects->get(4)->id : ($projects->isNotEmpty() ? $projects->first()->id : null),
                'assigned_to' => $users->random()->id,
                'created_by' => $users->first()->id,
                'start_date' => Carbon::now()->addDays(3),
                'due_date' => Carbon::now()->addDays(8),
                'estimated_hours' => 6,
                'estimated_cost' => 10000,
                'notes' => 'Include cost analysis for potential scope changes.',
                'created_at' => Carbon::now()->subDays(1),
                'updated_at' => Carbon::now(),
            ],
        ];

        foreach ($tasks as $taskData) {
            $taskData['tenant_id'] = $tenant->id;
            Task::create($taskData);
        }

        $this->command->info('✅ Created 10 sample tasks with various statuses and priorities');
        $this->command->info('Tasks include: completed, in_progress, pending, and overdue items');
        $this->command->info('Sample data includes time tracking, cost estimates, and project assignments');
    }
}