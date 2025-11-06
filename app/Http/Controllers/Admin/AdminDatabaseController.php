<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\Artisan;
use Illuminate\Support\Facades\Log;

class AdminDatabaseController extends Controller
{
    public function __construct()
    {
        $this->middleware('auth');
        $this->middleware('role:super-admin');
    }

    /**
     * Display database management dashboard.
     */
    public function index()
    {
        $stats = [
            'total_tables' => count($this->getTables()),
            'total_size' => $this->getDatabaseSize(),
            'connections' => $this->getActiveConnections(),
            'last_backup' => $this->getLastBackupDate(),
        ];

        $tables = $this->getTablesWithInfo();
        $recentQueries = $this->getRecentQueries();

        return view('admin.database.index', compact('stats', 'tables', 'recentQueries'));
    }

    /**
     * Show table structure and data.
     */
    public function showTable(Request $request, $tableName)
    {
        if (!$this->isValidTable($tableName)) {
            abort(404, 'Table not found');
        }

        $page = (int) $request->get('page', 1);
        $perPage = 50;
        $offset = ($page - 1) * $perPage;

        $structure = $this->getTableStructure($tableName);
        $totalRows = DB::table($tableName)->count();
        $data = DB::table($tableName)->offset($offset)->limit($perPage)->get();

        $pagination = [
            'current_page' => $page,
            'per_page' => $perPage,
            'total' => $totalRows,
            'last_page' => ceil($totalRows / $perPage),
            'has_more' => $page < ceil($totalRows / $perPage),
        ];

        return view('admin.database.table', compact('tableName', 'structure', 'data', 'pagination'));
    }

    /**
     * Execute SQL query.
     */
    public function executeQuery(Request $request)
    {
        $request->validate([
            'query' => 'required|string',
        ]);

        $query = trim($request->input('query'));
        
        // Basic security check - only allow SELECT queries for safety
        if (!preg_match('/^\s*SELECT\s+/i', $query)) {
            return back()->with('error', 'Only SELECT queries are allowed for security reasons.');
        }

        try {
            $startTime = microtime(true);
            $results = DB::select($query);
            $executionTime = round((microtime(true) - $startTime) * 1000, 2);

            // Log query execution
            $this->logQuery($query, $executionTime, count($results));

            return view('admin.database.query-results', compact('query', 'results', 'executionTime'));
        } catch (\Exception $e) {
            return back()->with('error', 'Query error: ' . $e->getMessage());
        }
    }

    /**
     * Optimize database tables.
     */
    public function optimize()
    {
        try {
            $tables = $this->getTables();
            $optimizedTables = [];

            foreach ($tables as $table) {
                DB::statement("OPTIMIZE TABLE `{$table}`");
                $optimizedTables[] = $table;
            }

            return back()->with('success', 'Optimized ' . count($optimizedTables) . ' tables successfully.');
        } catch (\Exception $e) {
            return back()->with('error', 'Optimization failed: ' . $e->getMessage());
        }
    }

    /**
     * Run database migrations.
     */
    public function migrate()
    {
        try {
            Artisan::call('migrate', ['--force' => true]);
            $output = Artisan::output();

            return back()->with('success', 'Migrations completed successfully. Output: ' . $output);
        } catch (\Exception $e) {
            return back()->with('error', 'Migration failed: ' . $e->getMessage());
        }
    }

    /**
     * Seed database.
     */
    public function seed(Request $request)
    {
        $request->validate([
            'seeder' => 'nullable|string',
        ]);

        try {
            $options = ['--force' => true];
            if ($request->filled('seeder')) {
                $options['--class'] = $request->seeder;
            }

            Artisan::call('db:seed', $options);
            $output = Artisan::output();

            return back()->with('success', 'Database seeded successfully. Output: ' . $output);
        } catch (\Exception $e) {
            return back()->with('error', 'Seeding failed: ' . $e->getMessage());
        }
    }

    /**
     * Show query builder interface.
     */
    public function queryBuilder()
    {
        $tables = $this->getTables();
        return view('admin.database.query-builder', compact('tables'));
    }

    /**
     * Export table data.
     */
    public function exportTable(Request $request, $tableName)
    {
        if (!$this->isValidTable($tableName)) {
            abort(404, 'Table not found');
        }

        $format = $request->get('format', 'csv');
        $data = DB::table($tableName)->get();

        if ($format === 'json') {
            return response()->json($data, 200, [
                'Content-Disposition' => "attachment; filename=\"{$tableName}_export.json\"",
            ]);
        } else {
            // CSV export
            $filename = $tableName . '_export_' . now()->format('Y-m-d_H-i-s') . '.csv';

            $headers = [
                'Content-Type' => 'text/csv',
                'Content-Disposition' => "attachment; filename=\"{$filename}\"",
            ];

            $callback = function() use ($data, $tableName) {
                $file = fopen('php://output', 'w');
                
                if ($data->isNotEmpty()) {
                    // Write header row
                    fputcsv($file, array_keys((array) $data->first()));
                    
                    // Write data rows
                    foreach ($data as $row) {
                        fputcsv($file, (array) $row);
                    }
                }
                
                fclose($file);
            };

            return response()->stream($callback, 200, $headers);
        }
    }

    /**
     * Get all database tables.
     */
    protected function getTables()
    {
        $tables = DB::select('SHOW TABLES');
        $tableKey = 'Tables_in_' . config('database.connections.mysql.database');
        
        return array_map(function($table) use ($tableKey) {
            return $table->$tableKey;
        }, $tables);
    }

    /**
     * Get tables with additional information.
     */
    protected function getTablesWithInfo()
    {
        $database = config('database.connections.mysql.database');
        
        return DB::select("
            SELECT 
                TABLE_NAME as name,
                TABLE_ROWS as rows,
                ROUND(((DATA_LENGTH + INDEX_LENGTH) / 1024 / 1024), 2) as size_mb,
                CREATE_TIME as created_at,
                UPDATE_TIME as updated_at
            FROM information_schema.TABLES 
            WHERE TABLE_SCHEMA = ?
            ORDER BY TABLE_NAME
        ", [$database]);
    }

    /**
     * Get table structure.
     */
    protected function getTableStructure($tableName)
    {
        return DB::select("DESCRIBE `{$tableName}`");
    }

    /**
     * Check if table name is valid.
     */
    protected function isValidTable($tableName)
    {
        return in_array($tableName, $this->getTables());
    }

    /**
     * Get database size.
     */
    protected function getDatabaseSize()
    {
        $database = config('database.connections.mysql.database');
        
        $result = DB::selectOne("
            SELECT 
                ROUND(SUM(DATA_LENGTH + INDEX_LENGTH) / 1024 / 1024, 2) as size_mb
            FROM information_schema.TABLES 
            WHERE TABLE_SCHEMA = ?
        ", [$database]);

        return $result->size_mb . ' MB';
    }

    /**
     * Get active database connections.
     */
    protected function getActiveConnections()
    {
        try {
            $result = DB::selectOne("SHOW STATUS LIKE 'Threads_connected'");
            return $result->Value ?? 0;
        } catch (\Exception $e) {
            return 'N/A';
        }
    }

    /**
     * Get last backup date.
     */
    protected function getLastBackupDate()
    {
        $backupPath = storage_path('app/backups');
        if (!file_exists($backupPath)) {
            return 'Never';
        }

        $files = glob($backupPath . '/*.sql');
        if (empty($files)) {
            return 'Never';
        }

        $latestFile = max(array_map('filemtime', $files));
        return date('Y-m-d H:i:s', $latestFile);
    }

    /**
     * Get recent queries (placeholder - would need query logging enabled).
     */
    protected function getRecentQueries()
    {
        // This would require MySQL general log to be enabled
        // For now, return empty array
        return [];
    }

    /**
     * Log query execution.
     */
    protected function logQuery($query, $executionTime, $rowCount)
    {
        // Log to Laravel log file
        Log::info('Database Query Executed', [
            'query' => $query,
            'execution_time_ms' => $executionTime,
            'row_count' => $rowCount,
            'user_id' => auth()->id(),
            'ip_address' => request()->ip(),
        ]);
    }
}