<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Artisan;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\File;
use Carbon\Carbon;

class AdminBackupController extends Controller
{
    public function __construct()
    {
        $this->middleware('auth');
        $this->middleware('role:super-admin');
    }

    /**
     * Display backup management.
     */
    public function index()
    {
        $backups = $this->getBackupFiles();
        
        return view('admin.backups.index', compact('backups'));
    }

    /**
     * Create new backup.
     */
    public function create(Request $request)
    {
        $request->validate([
            'type' => 'required|in:full,database,files',
            'description' => 'nullable|string|max:255',
        ]);

        try {
            $filename = $this->generateBackupFilename($request->type);
            
            switch ($request->type) {
                case 'full':
                    $this->createFullBackup($filename);
                    break;
                case 'database':
                    $this->createDatabaseBackup($filename);
                    break;
                case 'files':
                    $this->createFilesBackup($filename);
                    break;
            }

            return back()->with('success', "Backup '{$filename}' created successfully.");
        } catch (\Exception $e) {
            return back()->with('error', 'Backup failed: ' . $e->getMessage());
        }
    }

    /**
     * Download backup file.
     */
    public function download($filename)
    {
        $path = storage_path('app/backups/' . $filename);
        
        if (!File::exists($path)) {
            abort(404, 'Backup file not found');
        }

        return response()->download($path);
    }

    /**
     * Delete backup file.
     */
    public function destroy($filename)
    {
        $path = storage_path('app/backups/' . $filename);
        
        if (!File::exists($path)) {
            abort(404, 'Backup file not found');
        }

        File::delete($path);

        return back()->with('success', "Backup '{$filename}' deleted successfully.");
    }

    /**
     * Restore from backup.
     */
    public function restore(Request $request, $filename)
    {
        $path = storage_path('app/backups/' . $filename);
        
        if (!File::exists($path)) {
            abort(404, 'Backup file not found');
        }

        try {
            if (str_contains($filename, 'database')) {
                $this->restoreDatabaseBackup($path);
            } else {
                return back()->with('error', 'Only database backups can be restored automatically.');
            }

            return back()->with('success', "Database restored from '{$filename}' successfully.");
        } catch (\Exception $e) {
            return back()->with('error', 'Restore failed: ' . $e->getMessage());
        }
    }

    /**
     * Schedule automatic backups.
     */
    public function schedule(Request $request)
    {
        $request->validate([
            'enabled' => 'boolean',
            'frequency' => 'required|in:daily,weekly,monthly',
            'time' => 'required|date_format:H:i',
            'type' => 'required|in:full,database,files',
            'retention_days' => 'required|integer|min:1|max:365',
        ]);

        // Store backup schedule in config/cache
        $schedule = [
            'enabled' => $request->boolean('enabled'),
            'frequency' => $request->frequency,
            'time' => $request->time,
            'type' => $request->type,
            'retention_days' => $request->retention_days,
        ];

        cache()->put('backup_schedule', $schedule, now()->addYears(1));

        return back()->with('success', 'Backup schedule updated successfully.');
    }

    /**
     * Get backup files.
     */
    protected function getBackupFiles()
    {
        $backupPath = storage_path('app/backups');
        
        if (!File::exists($backupPath)) {
            File::makeDirectory($backupPath, 0755, true);
            return [];
        }

        $files = File::files($backupPath);
        $backups = [];

        foreach ($files as $file) {
            $backups[] = [
                'filename' => $file->getFilename(),
                'size' => $this->formatBytes($file->getSize()),
                'created_at' => Carbon::createFromTimestamp($file->getMTime()),
                'type' => $this->getBackupType($file->getFilename()),
            ];
        }

        // Sort by creation time, newest first
        usort($backups, function($a, $b) {
            return $b['created_at']->timestamp - $a['created_at']->timestamp;
        });

        return $backups;
    }

    /**
     * Generate backup filename.
     */
    protected function generateBackupFilename($type)
    {
        return sprintf(
            '%s_backup_%s_%s.sql',
            $type,
            config('app.name', 'app'),
            now()->format('Y-m-d_H-i-s')
        );
    }

    /**
     * Create full backup.
     */
    protected function createFullBackup($filename)
    {
        // For simplicity, create database backup
        // In production, you might want to include files as well
        $this->createDatabaseBackup($filename);
    }

    /**
     * Create database backup.
     */
    protected function createDatabaseBackup($filename)
    {
        $database = config('database.connections.mysql.database');
        $username = config('database.connections.mysql.username');
        $password = config('database.connections.mysql.password');
        $host = config('database.connections.mysql.host');
        $port = config('database.connections.mysql.port', 3306);

        $backupPath = storage_path('app/backups/' . $filename);

        $command = sprintf(
            'mysqldump --user=%s --password=%s --host=%s --port=%s %s > %s',
            escapeshellarg($username),
            escapeshellarg($password),
            escapeshellarg($host),
            escapeshellarg($port),
            escapeshellarg($database),
            escapeshellarg($backupPath)
        );

        $output = [];
        $returnVar = 0;
        exec($command, $output, $returnVar);

        if ($returnVar !== 0) {
            throw new \Exception('Database backup failed: ' . implode('\n', $output));
        }
    }

    /**
     * Create files backup.
     */
    protected function createFilesBackup($filename)
    {
        $zipFilename = str_replace('.sql', '.zip', $filename);
        $backupPath = storage_path('app/backups/' . $zipFilename);

        $zip = new \ZipArchive();
        if ($zip->open($backupPath, \ZipArchive::CREATE) !== TRUE) {
            throw new \Exception('Cannot create zip file');
        }

        $this->addFilesToZip($zip, storage_path('app'), 'storage');
        $this->addFilesToZip($zip, public_path(), 'public');

        $zip->close();
    }

    /**
     * Add files to zip recursively.
     */
    protected function addFilesToZip($zip, $path, $zipPath = '')
    {
        $files = File::allFiles($path);
        
        foreach ($files as $file) {
            $relativePath = $zipPath . '/' . $file->getRelativePathname();
            $zip->addFile($file->getRealPath(), $relativePath);
        }
    }

    /**
     * Restore database backup.
     */
    protected function restoreDatabaseBackup($path)
    {
        $database = config('database.connections.mysql.database');
        $username = config('database.connections.mysql.username');
        $password = config('database.connections.mysql.password');
        $host = config('database.connections.mysql.host');
        $port = config('database.connections.mysql.port', 3306);

        $command = sprintf(
            'mysql --user=%s --password=%s --host=%s --port=%s %s < %s',
            escapeshellarg($username),
            escapeshellarg($password),
            escapeshellarg($host),
            escapeshellarg($port),
            escapeshellarg($database),
            escapeshellarg($path)
        );

        $output = [];
        $returnVar = 0;
        exec($command, $output, $returnVar);

        if ($returnVar !== 0) {
            throw new \Exception('Database restore failed: ' . implode('\n', $output));
        }
    }

    /**
     * Get backup type from filename.
     */
    protected function getBackupType($filename)
    {
        if (str_contains($filename, 'full')) return 'Full';
        if (str_contains($filename, 'database')) return 'Database';
        if (str_contains($filename, 'files')) return 'Files';
        return 'Unknown';
    }

    /**
     * Format bytes to human readable format.
     */
    protected function formatBytes($size, $precision = 2)
    {
        $units = ['B', 'KB', 'MB', 'GB', 'TB'];
        
        for ($i = 0; $size >= 1024 && $i < count($units) - 1; $i++) {
            $size /= 1024;
        }
        
        return round($size, $precision) . ' ' . $units[$i];
    }
}