<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\File;
use Carbon\Carbon;

class BackupDatabase extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'backup:database 
                            {--path= : Custom backup path}
                            {--compress : Compress the backup file}
                            {--tenant= : Backup specific tenant data only}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Create a backup of the database with optional compression and tenant filtering';

    /**
     * Execute the console command.
     */
    public function handle()
    {
        $this->info('🔄 Starting database backup...');

        $timestamp = Carbon::now()->format('Y-m-d_H-i-s');
        $tenantId = $this->option('tenant');
        $compress = $this->option('compress');
        
        $filename = 'backup_' . $timestamp;
        if ($tenantId) {
            $filename .= '_tenant_' . $tenantId;
        }
        $filename .= $compress ? '.sql.gz' : '.sql';

        // Create backups directory if it doesn't exist
        $backupPath = $this->option('path') ?: storage_path('app/backups');
        if (!File::exists($backupPath)) {
            File::makeDirectory($backupPath, 0755, true);
        }

        $fullPath = $backupPath . '/' . $filename;

        try {
            // For SQLite, we can simply copy the database file
            if (config('database.default') === 'sqlite') {
                $this->backupSQLite($fullPath, $tenantId, $compress);
            } else {
                $this->backupMySQL($fullPath, $tenantId, $compress);
            }

            $fileSize = $this->formatBytes(filesize($fullPath));
            
            $this->info("✅ Backup completed successfully!");
            $this->info("📁 File: {$filename}");
            $this->info("📊 Size: {$fileSize}");
            $this->info("📍 Location: {$fullPath}");

            // Log the backup
            $this->logBackup($filename, $fileSize, $tenantId);

        } catch (\Exception $e) {
            $this->error("❌ Backup failed: " . $e->getMessage());
            return 1;
        }

        return 0;
    }

    /**
     * Backup SQLite database
     */
    protected function backupSQLite(string $fullPath, ?string $tenantId, bool $compress): void
    {
        $dbPath = database_path('database.sqlite');
        
        if (!File::exists($dbPath)) {
            throw new \Exception('SQLite database file not found');
        }

        if ($tenantId) {
            // For tenant-specific backup, we need to dump specific tables/data
            $this->dumpTenantData($fullPath, $tenantId, $compress);
        } else {
            // Full database backup
            if ($compress) {
                // Compress while copying
                $input = fopen($dbPath, 'rb');
                $output = gzopen($fullPath, 'wb9');
                
                while (!feof($input)) {
                    gzwrite($output, fread($input, 4096));
                }
                
                fclose($input);
                gzclose($output);
            } else {
                File::copy($dbPath, $fullPath);
            }
        }
    }

    /**
     * Backup MySQL database
     */
    protected function backupMySQL(string $fullPath, ?string $tenantId, bool $compress): void
    {
        $config = config('database.connections.mysql');
        $host = $config['host'];
        $port = $config['port'];
        $database = $config['database'];
        $username = $config['username'];
        $password = $config['password'];

        $mysqldumpPath = $this->findMysqldump();
        
        $command = sprintf(
            '%s -h%s -P%s -u%s -p%s %s',
            $mysqldumpPath,
            $host,
            $port,
            $username,
            $password,
            $database
        );

        if ($tenantId) {
            // Add tenant-specific where clauses for relevant tables
            $command .= ' --where="tenant_id=' . $tenantId . '"';
        }

        if ($compress) {
            $command .= ' | gzip > ' . $fullPath;
        } else {
            $command .= ' > ' . $fullPath;
        }

        $result = shell_exec($command . ' 2>&1');
        
        if (!File::exists($fullPath)) {
            throw new \Exception('Backup command failed: ' . $result);
        }
    }

    /**
     * Dump tenant-specific data for SQLite
     */
    protected function dumpTenantData(string $fullPath, string $tenantId, bool $compress): void
    {
        // This would require more complex SQL extraction logic
        // For now, we'll just copy the full database and add a note
        $dbPath = database_path('database.sqlite');
        
        if ($compress) {
            $input = fopen($dbPath, 'rb');
            $output = gzopen($fullPath, 'wb9');
            
            while (!feof($input)) {
                gzwrite($output, fread($input, 4096));
            }
            
            fclose($input);
            gzclose($output);
        } else {
            File::copy($dbPath, $fullPath);
        }
        
        $this->warn("⚠️  Note: Tenant-specific backup for SQLite contains full database.");
        $this->warn("    Use --tenant option with MySQL for filtered backups.");
    }

    /**
     * Find mysqldump executable
     */
    protected function findMysqldump(): string
    {
        $paths = ['/usr/bin/mysqldump', '/usr/local/bin/mysqldump', 'mysqldump'];
        
        foreach ($paths as $path) {
            if (shell_exec("which {$path}")) {
                return $path;
            }
        }
        
        throw new \Exception('mysqldump not found in system PATH');
    }

    /**
     * Format bytes to human readable format
     */
    protected function formatBytes(int $bytes): string
    {
        $units = ['B', 'KB', 'MB', 'GB', 'TB'];
        
        for ($i = 0; $bytes > 1024 && $i < count($units) - 1; $i++) {
            $bytes /= 1024;
        }
        
        return round($bytes, 2) . ' ' . $units[$i];
    }

    /**
     * Log the backup operation
     */
    protected function logBackup(string $filename, string $fileSize, ?string $tenantId): void
    {
        $logEntry = [
            'timestamp' => Carbon::now()->toISOString(),
            'filename' => $filename,
            'size' => $fileSize,
            'tenant_id' => $tenantId,
            'type' => $tenantId ? 'tenant' : 'full',
        ];

        $logFile = storage_path('app/backups/backup_log.json');
        $existingLog = File::exists($logFile) ? json_decode(File::get($logFile), true) : [];
        $existingLog[] = $logEntry;
        
        // Keep only last 100 entries
        if (count($existingLog) > 100) {
            $existingLog = array_slice($existingLog, -100);
        }
        
        File::put($logFile, json_encode($existingLog, JSON_PRETTY_PRINT));
    }
}
