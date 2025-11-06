<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\File;
use Illuminate\Support\Facades\Storage;

class AdminLogController extends Controller
{
    public function __construct()
    {
        $this->middleware('auth');
        $this->middleware('role:super-admin');
    }

    /**
     * Display system logs.
     */
    public function index()
    {
        $logPath = storage_path('logs');
        $logFiles = [];

        if (File::exists($logPath)) {
            $files = File::files($logPath);
            foreach ($files as $file) {
                if (pathinfo($file, PATHINFO_EXTENSION) === 'log') {
                    $logFiles[] = [
                        'name' => $file->getFilename(),
                        'path' => $file->getPathname(),
                        'size' => $this->formatBytes($file->getSize()),
                        'modified' => date('Y-m-d H:i:s', $file->getMTime()),
                        'lines' => $this->countLines($file->getPathname()),
                    ];
                }
            }
        }

        // Sort by modification time, newest first
        usort($logFiles, function($a, $b) {
            return strtotime($b['modified']) - strtotime($a['modified']);
        });

        return view('admin.logs.index', compact('logFiles'));
    }

    /**
     * Display specific log file.
     */
    public function show(Request $request, $filename)
    {
        $logPath = storage_path('logs/' . $filename);
        
        if (!File::exists($logPath) || !str_ends_with($filename, '.log')) {
            abort(404, 'Log file not found');
        }

        $lines = [];
        $totalLines = 0;
        $page = (int) $request->get('page', 1);
        $perPage = 100;
        $offset = ($page - 1) * $perPage;

        // Count total lines
        $totalLines = $this->countLines($logPath);

        // Read file in chunks for better performance
        $handle = fopen($logPath, 'r');
        if ($handle) {
            $currentLine = 0;
            while (($line = fgets($handle)) !== false) {
                if ($currentLine >= $offset && $currentLine < ($offset + $perPage)) {
                    $lines[] = [
                        'number' => $currentLine + 1,
                        'content' => rtrim($line),
                        'level' => $this->getLogLevel($line),
                        'timestamp' => $this->extractTimestamp($line),
                    ];
                }
                $currentLine++;
                if ($currentLine >= ($offset + $perPage)) {
                    break;
                }
            }
            fclose($handle);
        }

        // Reverse to show newest first
        $lines = array_reverse($lines);

        $pagination = [
            'current_page' => $page,
            'per_page' => $perPage,
            'total' => $totalLines,
            'last_page' => ceil($totalLines / $perPage),
            'has_more' => $page < ceil($totalLines / $perPage),
        ];

        return view('admin.logs.show', compact('filename', 'lines', 'pagination'));
    }

    /**
     * Download log file.
     */
    public function download($filename)
    {
        $logPath = storage_path('logs/' . $filename);
        
        if (!File::exists($logPath) || !str_ends_with($filename, '.log')) {
            abort(404, 'Log file not found');
        }

        return response()->download($logPath);
    }

    /**
     * Delete log file.
     */
    public function delete($filename)
    {
        $logPath = storage_path('logs/' . $filename);
        
        if (!File::exists($logPath) || !str_ends_with($filename, '.log')) {
            abort(404, 'Log file not found');
        }

        File::delete($logPath);

        return redirect()->route('admin.logs.index')
            ->with('success', "Log file '{$filename}' deleted successfully.");
    }

    /**
     * Clear all log files.
     */
    public function clear()
    {
        $logPath = storage_path('logs');
        $files = File::files($logPath);
        $deletedCount = 0;

        foreach ($files as $file) {
            if (pathinfo($file, PATHINFO_EXTENSION) === 'log') {
                File::delete($file);
                $deletedCount++;
            }
        }

        return redirect()->route('admin.logs.index')
            ->with('success', "Cleared {$deletedCount} log files.");
    }

    /**
     * Search through log files.
     */
    public function search(Request $request)
    {
        $request->validate([
            'query' => 'required|string|min:3',
            'files' => 'array',
        ]);

        $query = $request->input('query');
        $selectedFiles = $request->input('files', []);
        $results = [];

        $logPath = storage_path('logs');
        $files = File::files($logPath);

        foreach ($files as $file) {
            if (pathinfo($file, PATHINFO_EXTENSION) !== 'log') {
                continue;
            }

            $filename = $file->getFilename();
            
            // Skip if specific files selected and this isn't one of them
            if (!empty($selectedFiles) && !in_array($filename, $selectedFiles)) {
                continue;
            }

            $matches = [];
            $handle = fopen($file->getPathname(), 'r');
            
            if ($handle) {
                $lineNumber = 0;
                while (($line = fgets($handle)) !== false) {
                    $lineNumber++;
                    if (stripos($line, $query) !== false) {
                        $matches[] = [
                            'line_number' => $lineNumber,
                            'content' => trim($line),
                            'level' => $this->getLogLevel($line),
                            'timestamp' => $this->extractTimestamp($line),
                        ];
                        
                        // Limit matches per file
                        if (count($matches) >= 50) {
                            break;
                        }
                    }
                }
                fclose($handle);
            }

            if (!empty($matches)) {
                $results[$filename] = $matches;
            }
        }

        return view('admin.logs.search', compact('query', 'results', 'selectedFiles'));
    }

    /**
     * Count lines in a file.
     */
    protected function countLines($filepath)
    {
        $file = new \SplFileObject($filepath, 'r');
        $file->seek(PHP_INT_MAX);
        return $file->key() + 1;
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

    /**
     * Extract log level from log line.
     */
    protected function getLogLevel($line)
    {
        if (preg_match('/\[(.*?)\].*?\[(.*?)\]/', $line, $matches)) {
            return strtolower($matches[2] ?? 'info');
        }
        
        // Fallback patterns
        if (stripos($line, 'error') !== false) return 'error';
        if (stripos($line, 'warning') !== false) return 'warning';
        if (stripos($line, 'debug') !== false) return 'debug';
        
        return 'info';
    }

    /**
     * Extract timestamp from log line.
     */
    protected function extractTimestamp($line)
    {
        if (preg_match('/\[(.*?)\]/', $line, $matches)) {
            return $matches[1] ?? null;
        }
        
        return null;
    }
}