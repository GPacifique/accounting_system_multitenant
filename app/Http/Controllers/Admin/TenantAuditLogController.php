<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\TenantAuditLog;
use App\Models\Tenant;
use App\Models\User;
use Illuminate\Http\Request;
use Carbon\Carbon;

class TenantAuditLogController extends Controller
{
    public function __construct()
    {
        $this->middleware('auth');
        $this->middleware('role:super-admin');
    }

    /**
     * Display a listing of audit logs.
     */
    public function index(Request $request)
    {
        $query = TenantAuditLog::with(['tenant', 'user']);

        // Apply filters
        if ($request->filled('tenant_id')) {
            $query->where('tenant_id', $request->tenant_id);
        }

        if ($request->filled('action')) {
            $query->where('action', $request->action);
        }

        if ($request->filled('date_from')) {
            $query->where('created_at', '>=', Carbon::parse($request->date_from)->startOfDay());
        }

        if ($request->filled('date_to')) {
            $query->where('created_at', '<=', Carbon::parse($request->date_to)->endOfDay());
        }

        if ($request->filled('search')) {
            $search = $request->search;
            $query->where(function($q) use ($search) {
                $q->where('description', 'LIKE', "%{$search}%")
                  ->orWhere('ip_address', 'LIKE', "%{$search}%")
                  ->orWhereHas('user', function($userQuery) use ($search) {
                      $userQuery->where('name', 'LIKE', "%{$search}%")
                               ->orWhere('email', 'LIKE', "%{$search}%");
                  });
            });
        }

        $logs = $query->orderBy('created_at', 'desc')->paginate(25);

        // Get filter data
        $tenants = Tenant::select('id', 'name')->get();
        $actions = TenantAuditLog::distinct()->pluck('action');

        $stats = [
            'total_logs' => TenantAuditLog::count(),
            'today_logs' => TenantAuditLog::whereDate('created_at', today())->count(),
            'security_events' => TenantAuditLog::where('action', 'LIKE', '%login%')
                                             ->orWhere('action', 'LIKE', '%logout%')
                                             ->orWhere('action', 'LIKE', '%failed%')
                                             ->count(),
            'critical_events' => TenantAuditLog::whereIn('action', [
                                    TenantAuditLog::ACTION_DELETED,
                                    TenantAuditLog::ACTION_EXPORT,
                                    TenantAuditLog::ACTION_BACKUP,
                                    TenantAuditLog::ACTION_RESTORE
                                ])->count(),
        ];

        return view('admin.audit-logs.index', compact('logs', 'tenants', 'actions', 'stats'));
    }

    /**
     * Display the specified audit log.
     */
    public function show(TenantAuditLog $auditLog)
    {
        $auditLog->load(['tenant', 'user']);
        return view('admin.audit-logs.show', compact('auditLog'));
    }

    /**
     * Export audit logs to CSV.
     */
    public function export(Request $request)
    {
        $query = TenantAuditLog::with(['tenant', 'user']);

        // Apply same filters as index
        if ($request->filled('tenant_id')) {
            $query->where('tenant_id', $request->tenant_id);
        }

        if ($request->filled('action')) {
            $query->where('action', $request->action);
        }

        if ($request->filled('date_from')) {
            $query->where('created_at', '>=', Carbon::parse($request->date_from)->startOfDay());
        }

        if ($request->filled('date_to')) {
            $query->where('created_at', '<=', Carbon::parse($request->date_to)->endOfDay());
        }

        $logs = $query->orderBy('created_at', 'desc')->get();

        $filename = 'audit_logs_' . now()->format('Y-m-d_H-i-s') . '.csv';

        $headers = [
            'Content-Type' => 'text/csv',
            'Content-Disposition' => "attachment; filename=\"{$filename}\"",
        ];

        $callback = function() use ($logs) {
            $file = fopen('php://output', 'w');
            
            // CSV headers
            fputcsv($file, [
                'Date/Time',
                'Tenant',
                'User',
                'Action',
                'Description',
                'IP Address',
                'User Agent',
                'Risk Level',
                'Resource Type',
                'Resource ID'
            ]);

            // Data rows
            foreach ($logs as $log) {
                fputcsv($file, [
                    $log->created_at->format('Y-m-d H:i:s'),
                    $log->tenant->name ?? 'N/A',
                    $log->user->name ?? 'System',
                    $log->action,
                    $log->description,
                    $log->ip_address,
                    $log->user_agent,
                    $log->getRiskLevel(),
                    $log->resource_type ? class_basename($log->resource_type) : 'N/A',
                    $log->resource_id,
                ]);
            }

            fclose($file);
        };

        return response()->stream($callback, 200, $headers);
    }

    /**
     * Get audit log statistics for dashboard.
     */
    public function stats()
    {
        $stats = [
            'total' => TenantAuditLog::count(),
            'today' => TenantAuditLog::whereDate('created_at', today())->count(),
            'this_week' => TenantAuditLog::whereBetween('created_at', [
                now()->startOfWeek(),
                now()->endOfWeek()
            ])->count(),
            'this_month' => TenantAuditLog::whereMonth('created_at', now()->month)
                                       ->whereYear('created_at', now()->year)
                                       ->count(),
        ];

        // Activity by day (last 7 days)
        $dailyActivity = [];
        for ($i = 6; $i >= 0; $i--) {
            $date = now()->subDays($i);
            $dailyActivity[] = [
                'date' => $date->format('M j'),
                'count' => TenantAuditLog::whereDate('created_at', $date)->count()
            ];
        }

        // Top actions
        $topActions = TenantAuditLog::selectRaw('action, COUNT(*) as count')
                                   ->groupBy('action')
                                   ->orderBy('count', 'desc')
                                   ->limit(5)
                                   ->get();

        // Security events
        $securityEvents = TenantAuditLog::whereIn('action', [
                                            TenantAuditLog::ACTION_LOGIN,
                                            TenantAuditLog::ACTION_LOGOUT,
                                            TenantAuditLog::ACTION_DELETED,
                                            TenantAuditLog::ACTION_EXPORT
                                        ])
                                       ->orderBy('created_at', 'desc')
                                       ->limit(10)
                                       ->get();

        return response()->json([
            'stats' => $stats,
            'daily_activity' => $dailyActivity,
            'top_actions' => $topActions,
            'security_events' => $securityEvents
        ]);
    }

    /**
     * Clear old audit logs.
     */
    public function cleanup(Request $request)
    {
        $request->validate([
            'days' => 'required|integer|min:30|max:3650', // 30 days to 10 years
        ]);

        $cutoffDate = now()->subDays($request->days);
        $deletedCount = TenantAuditLog::where('created_at', '<', $cutoffDate)->delete();

        return back()->with('success', "Deleted {$deletedCount} audit log entries older than {$request->days} days.");
    }

    /**
     * Show cleanup form.
     */
    public function cleanupForm()
    {
        $stats = [
            'total_logs' => TenantAuditLog::count(),
            'older_than_30_days' => TenantAuditLog::where('created_at', '<', now()->subDays(30))->count(),
            'older_than_90_days' => TenantAuditLog::where('created_at', '<', now()->subDays(90))->count(),
            'older_than_365_days' => TenantAuditLog::where('created_at', '<', now()->subDays(365))->count(),
        ];

        return view('admin.audit-logs.cleanup', compact('stats'));
    }
}
