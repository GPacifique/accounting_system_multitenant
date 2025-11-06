<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use App\Models\Tenant;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\DB;
use Carbon\Carbon;

class TenantBackupCommand extends Command
{
    /**
     * The name and signature of the console command.
     */
    protected $signature = 'tenant:backup 
                           {tenant? : The tenant ID or domain to backup}
                           {--all : Backup all tenants}
                           {--format=sql : Backup format (sql|json)}
                           {--storage=local : Storage disk to use}
                           {--compress : Compress backup files}';

    /**
     * The console command description.
     */
    protected $description = 'Create backups of tenant data';

    /**
     * Execute the console command.
     */
    public function handle()
    {
        $tenantIdentifier = $this->argument('tenant');
        $backupAll = $this->option('all');
        $format = $this->option('format');
        $storageDisk = $this->option('storage');
        $compress = $this->option('compress');

        if (!$tenantIdentifier && !$backupAll) {
            $this->error('Please specify a tenant ID/domain or use --all to backup all tenants');
            return 1;
        }

        $tenants = collect();

        if ($backupAll) {
            $tenants = Tenant::all();
            $this->info("Creating backups for all " . $tenants->count() . " tenants...");
        } else {
            $tenant = $this->findTenant($tenantIdentifier);
            if (!$tenant) {
                $this->error("Tenant not found: {$tenantIdentifier}");
                return 1;
            }
            $tenants->push($tenant);
            $this->info("Creating backup for tenant: {$tenant->name}");
        }

        $successCount = 0;
        $failureCount = 0;

        foreach ($tenants as $tenant) {
            try {
                $this->line("Backing up tenant: {$tenant->name} ({$tenant->domain})");
                
                $backup = $this->createTenantBackup($tenant, $format, $storageDisk, $compress);
                
                if ($backup['success']) {
                    $this->info("✓ Backup created: {$backup['filename']}");
                    $successCount++;
                    
                    // Update tenant's last backup timestamp
                    $tenant->update(['last_backup_at' => now()]);
                } else {
                    $this->error("✗ Failed to backup {$tenant->name}: {$backup['error']}");
                    $failureCount++;
                }
            } catch (\Exception $e) {
                $this->error("✗ Exception backing up {$tenant->name}: " . $e->getMessage());
                $failureCount++;
            }
        }

        $this->newLine();
        $this->info("Backup completed: {$successCount} successful, {$failureCount} failed");
        
        return $failureCount > 0 ? 1 : 0;
    }

    /**
     * Find tenant by ID or domain.
     */
    protected function findTenant($identifier): ?Tenant
    {
        if (is_numeric($identifier)) {
            return Tenant::find($identifier);
        }
        
        return Tenant::where('domain', $identifier)->first();
    }

    /**
     * Create backup for a specific tenant.
     */
    protected function createTenantBackup(Tenant $tenant, string $format, string $storageDisk, bool $compress): array
    {
        try {
            $timestamp = Carbon::now()->format('Y-m-d_H-i-s');
            $filename = "tenant_backup_{$tenant->domain}_{$timestamp}";
            
            $data = $this->collectTenantData($tenant);
            
            switch ($format) {
                case 'json':
                    $content = json_encode($data, JSON_PRETTY_PRINT);
                    $filename .= '.json';
                    break;
                    
                case 'sql':
                    $content = $this->generateSQLBackup($tenant, $data);
                    $filename .= '.sql';
                    break;
                    
                default:
                    throw new \InvalidArgumentException("Unsupported format: {$format}");
            }
            
            if ($compress) {
                $content = gzcompress($content);
                $filename .= '.gz';
            }
            
            $path = "backups/tenants/{$tenant->domain}/{$filename}";
            
            Storage::disk($storageDisk)->put($path, $content);
            
            return [
                'success' => true,
                'filename' => $filename,
                'path' => $path,
                'size' => strlen($content),
            ];
            
        } catch (\Exception $e) {
            return [
                'success' => false,
                'error' => $e->getMessage(),
            ];
        }
    }

    /**
     * Collect all data related to a tenant.
     */
    protected function collectTenantData(Tenant $tenant): array
    {
        return [
            'tenant' => $tenant->toArray(),
            'users' => $tenant->users()->withPivot('role', 'is_admin')->get()->toArray(),
            'projects' => $tenant->execute(function() {
                return \App\Models\Project::with(['client', 'incomes', 'expenses'])->get()->toArray();
            }),
            'tasks' => $tenant->execute(function() {
                return \App\Models\Task::with(['project', 'assignedUser', 'createdBy'])->get()->toArray();
            }),
            'clients' => $tenant->execute(function() {
                return \App\Models\Client::with('projects')->get()->toArray();
            }),
            'workers' => $tenant->execute(function() {
                return \App\Models\Worker::with(['tasks', 'payments'])->get()->toArray();
            }),
            'employees' => $tenant->execute(function() {
                return \App\Models\Employee::with('payments')->get()->toArray();
            }),
            'incomes' => $tenant->execute(function() {
                return \App\Models\Income::with('project')->get()->toArray();
            }),
            'expenses' => $tenant->execute(function() {
                return \App\Models\Expense::get()->toArray();
            }),
            'payments' => $tenant->execute(function() {
                return \App\Models\Payment::with('employee')->get()->toArray();
            }),
            'backup_info' => [
                'created_at' => now()->toISOString(),
                'version' => config('app.version', '1.0.0'),
                'php_version' => PHP_VERSION,
                'laravel_version' => app()->version(),
            ],
        ];
    }

    /**
     * Generate SQL backup from tenant data.
     */
    protected function generateSQLBackup(Tenant $tenant, array $data): string
    {
        $sql = "-- Tenant Backup for: {$tenant->name} ({$tenant->domain})\n";
        $sql .= "-- Created at: " . now()->toISOString() . "\n";
        $sql .= "-- Format: SQL\n\n";
        
        $sql .= "SET FOREIGN_KEY_CHECKS = 0;\n\n";
        
        // Add tenant record
        $sql .= "-- Tenant Information\n";
        $sql .= $this->generateInsertSQL('tenants', [$data['tenant']]);
        
        // Add users (tenant_users pivot)
        if (!empty($data['users'])) {
            $sql .= "\n-- Tenant Users\n";
            foreach ($data['users'] as $user) {
                $pivotData = [
                    'tenant_id' => $tenant->id,
                    'user_id' => $user['id'],
                    'role' => $user['pivot']['role'] ?? 'user',
                    'is_admin' => $user['pivot']['is_admin'] ?? false,
                ];
                $sql .= $this->generateInsertSQL('tenant_users', [$pivotData]);
            }
        }
        
        // Add other tenant data
        foreach (['projects', 'tasks', 'clients', 'workers', 'employees', 'incomes', 'expenses', 'payments'] as $table) {
            if (!empty($data[$table])) {
                $sql .= "\n-- " . ucfirst($table) . "\n";
                $sql .= $this->generateInsertSQL($table, $data[$table]);
            }
        }
        
        $sql .= "\nSET FOREIGN_KEY_CHECKS = 1;\n";
        
        return $sql;
    }

    /**
     * Generate SQL INSERT statements.
     */
    protected function generateInsertSQL(string $table, array $records): string
    {
        if (empty($records)) {
            return "";
        }
        
        $sql = "";
        
        foreach ($records as $record) {
            // Remove pivot data and other non-database fields
            $record = array_filter($record, function($key) {
                return !in_array($key, ['pivot', 'relationships']);
            }, ARRAY_FILTER_USE_KEY);
            
            $columns = array_keys($record);
            $values = array_map(function($value) {
                if (is_null($value)) {
                    return 'NULL';
                } elseif (is_bool($value)) {
                    return $value ? '1' : '0';
                } elseif (is_numeric($value)) {
                    return $value;
                } else {
                    return "'" . addslashes($value) . "'";
                }
            }, array_values($record));
            
            $sql .= "INSERT INTO {$table} (" . implode(', ', $columns) . ") VALUES (" . implode(', ', $values) . ");\n";
        }
        
        return $sql;
    }
}
