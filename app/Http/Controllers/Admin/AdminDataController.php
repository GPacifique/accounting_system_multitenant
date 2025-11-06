<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Tenant;
use App\Models\User;
use App\Models\TenantSubscription;
use App\Models\TenantAuditLog;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Storage;
use Carbon\Carbon;

class AdminDataController extends Controller
{
    public function __construct()
    {
        $this->middleware('auth');
        $this->middleware('role:super-admin');
    }

    /**
     * Display data management dashboard.
     */
    public function index()
    {
        $stats = [
            'tenants' => Tenant::count(),
            'users' => User::count(),
            'subscriptions' => TenantSubscription::count(),
            'audit_logs' => TenantAuditLog::count(),
        ];

        return view('admin.data.index', compact('stats'));
    }

    /**
     * Export data.
     */
    public function export(Request $request)
    {
        $request->validate([
            'type' => 'required|in:tenants,users,subscriptions,audit_logs,all',
            'format' => 'required|in:csv,json',
            'date_from' => 'nullable|date',
            'date_to' => 'nullable|date|after_or_equal:date_from',
        ]);

        $type = $request->type;
        $format = $request->format;
        $dateFrom = $request->date_from ? Carbon::parse($request->date_from) : null;
        $dateTo = $request->date_to ? Carbon::parse($request->date_to) : null;

        switch ($type) {
            case 'tenants':
                return $this->exportTenants($format, $dateFrom, $dateTo);
            case 'users':
                return $this->exportUsers($format, $dateFrom, $dateTo);
            case 'subscriptions':
                return $this->exportSubscriptions($format, $dateFrom, $dateTo);
            case 'audit_logs':
                return $this->exportAuditLogs($format, $dateFrom, $dateTo);
            case 'all':
                return $this->exportAll($format, $dateFrom, $dateTo);
            default:
                return back()->with('error', 'Invalid export type.');
        }
    }

    /**
     * Import data.
     */
    public function import(Request $request)
    {
        $request->validate([
            'type' => 'required|in:tenants,users',
            'file' => 'required|file|mimes:csv,json|max:10240', // 10MB max
        ]);

        $type = $request->type;
        $file = $request->file('file');
        $extension = $file->getClientOriginalExtension();

        try {
            if ($extension === 'csv') {
                $result = $this->importFromCsv($type, $file);
            } else {
                $result = $this->importFromJson($type, $file);
            }

            return back()->with('success', $result['message']);
        } catch (\Exception $e) {
            return back()->with('error', 'Import failed: ' . $e->getMessage());
        }
    }

    /**
     * Export tenants.
     */
    protected function exportTenants($format, $dateFrom = null, $dateTo = null)
    {
        $query = Tenant::with('subscription');

        if ($dateFrom) {
            $query->where('created_at', '>=', $dateFrom);
        }
        if ($dateTo) {
            $query->where('created_at', '<=', $dateTo);
        }

        $tenants = $query->get();

        if ($format === 'csv') {
            return $this->generateCsvResponse($tenants->map(function ($tenant) {
                return [
                    'id' => $tenant->id,
                    'name' => $tenant->name,
                    'domain' => $tenant->domain,
                    'database' => $tenant->database,
                    'status' => $tenant->status,
                    'plan' => $tenant->subscription->plan ?? 'none',
                    'created_at' => $tenant->created_at->toDateTimeString(),
                ];
            })->toArray(), 'tenants');
        } else {
            return $this->generateJsonResponse($tenants, 'tenants');
        }
    }

    /**
     * Export users.
     */
    protected function exportUsers($format, $dateFrom = null, $dateTo = null)
    {
        $query = User::with('tenant');

        if ($dateFrom) {
            $query->where('created_at', '>=', $dateFrom);
        }
        if ($dateTo) {
            $query->where('created_at', '<=', $dateTo);
        }

        $users = $query->get();

        if ($format === 'csv') {
            return $this->generateCsvResponse($users->map(function ($user) {
                return [
                    'id' => $user->id,
                    'name' => $user->name,
                    'email' => $user->email,
                    'tenant' => $user->tenant->name ?? 'N/A',
                    'email_verified_at' => $user->email_verified_at?->toDateTimeString(),
                    'created_at' => $user->created_at->toDateTimeString(),
                ];
            })->toArray(), 'users');
        } else {
            return $this->generateJsonResponse($users, 'users');
        }
    }

    /**
     * Export subscriptions.
     */
    protected function exportSubscriptions($format, $dateFrom = null, $dateTo = null)
    {
        $query = TenantSubscription::with('tenant');

        if ($dateFrom) {
            $query->where('created_at', '>=', $dateFrom);
        }
        if ($dateTo) {
            $query->where('created_at', '<=', $dateTo);
        }

        $subscriptions = $query->get();

        if ($format === 'csv') {
            return $this->generateCsvResponse($subscriptions->map(function ($subscription) {
                return [
                    'id' => $subscription->id,
                    'tenant' => $subscription->tenant->name,
                    'plan' => $subscription->plan,
                    'status' => $subscription->status,
                    'amount' => $subscription->amount,
                    'billing_cycle' => $subscription->billing_cycle,
                    'current_period_start' => $subscription->current_period_start?->toDateTimeString(),
                    'current_period_end' => $subscription->current_period_end?->toDateTimeString(),
                    'created_at' => $subscription->created_at->toDateTimeString(),
                ];
            })->toArray(), 'subscriptions');
        } else {
            return $this->generateJsonResponse($subscriptions, 'subscriptions');
        }
    }

    /**
     * Export audit logs.
     */
    protected function exportAuditLogs($format, $dateFrom = null, $dateTo = null)
    {
        $query = TenantAuditLog::with(['tenant', 'user']);

        if ($dateFrom) {
            $query->where('created_at', '>=', $dateFrom);
        }
        if ($dateTo) {
            $query->where('created_at', '<=', $dateTo);
        }

        $logs = $query->orderBy('created_at', 'desc')->get();

        if ($format === 'csv') {
            return $this->generateCsvResponse($logs->map(function ($log) {
                return [
                    'id' => $log->id,
                    'tenant' => $log->tenant->name ?? 'N/A',
                    'user' => $log->user->name ?? 'System',
                    'action' => $log->action,
                    'resource_type' => $log->resource_type,
                    'resource_id' => $log->resource_id,
                    'ip_address' => $log->ip_address,
                    'description' => $log->description,
                    'created_at' => $log->created_at->toDateTimeString(),
                ];
            })->toArray(), 'audit_logs');
        } else {
            return $this->generateJsonResponse($logs, 'audit_logs');
        }
    }

    /**
     * Export all data.
     */
    protected function exportAll($format, $dateFrom = null, $dateTo = null)
    {
        $data = [
            'tenants' => Tenant::with('subscription')->get(),
            'users' => User::with('tenant')->get(),
            'subscriptions' => TenantSubscription::with('tenant')->get(),
            'audit_logs' => TenantAuditLog::with(['tenant', 'user'])->get(),
        ];

        return $this->generateJsonResponse($data, 'complete_export');
    }

    /**
     * Generate CSV response.
     */
    protected function generateCsvResponse($data, $filename)
    {
        $filename = $filename . '_' . now()->format('Y-m-d_H-i-s') . '.csv';

        $headers = [
            'Content-Type' => 'text/csv',
            'Content-Disposition' => "attachment; filename=\"{$filename}\"",
        ];

        $callback = function() use ($data) {
            $file = fopen('php://output', 'w');
            
            if (!empty($data)) {
                // Write header row
                fputcsv($file, array_keys($data[0]));
                
                // Write data rows
                foreach ($data as $row) {
                    fputcsv($file, $row);
                }
            }
            
            fclose($file);
        };

        return response()->stream($callback, 200, $headers);
    }

    /**
     * Generate JSON response.
     */
    protected function generateJsonResponse($data, $filename)
    {
        $filename = $filename . '_' . now()->format('Y-m-d_H-i-s') . '.json';

        return response()->json($data, 200, [
            'Content-Disposition' => "attachment; filename=\"{$filename}\"",
        ]);
    }

    /**
     * Import from CSV.
     */
    protected function importFromCsv($type, $file)
    {
        $path = $file->storeTemporarily();
        $csv = array_map('str_getcsv', file($path));
        $headers = array_shift($csv);
        
        $imported = 0;
        $errors = [];

        foreach ($csv as $index => $row) {
            try {
                $data = array_combine($headers, $row);
                $this->importRecord($type, $data);
                $imported++;
            } catch (\Exception $e) {
                $errors[] = "Row " . ($index + 2) . ": " . $e->getMessage();
            }
        }

        unlink($path);

        $message = "Imported {$imported} records.";
        if (!empty($errors)) {
            $message .= " Errors: " . implode(', ', array_slice($errors, 0, 5));
        }

        return ['message' => $message];
    }

    /**
     * Import from JSON.
     */
    protected function importFromJson($type, $file)
    {
        $content = file_get_contents($file->getRealPath());
        $data = json_decode($content, true);

        if (json_last_error() !== JSON_ERROR_NONE) {
            throw new \Exception('Invalid JSON file');
        }

        $imported = 0;
        $errors = [];

        foreach ($data as $index => $record) {
            try {
                $this->importRecord($type, $record);
                $imported++;
            } catch (\Exception $e) {
                $errors[] = "Record " . ($index + 1) . ": " . $e->getMessage();
            }
        }

        $message = "Imported {$imported} records.";
        if (!empty($errors)) {
            $message .= " Errors: " . implode(', ', array_slice($errors, 0, 5));
        }

        return ['message' => $message];
    }

    /**
     * Import individual record.
     */
    protected function importRecord($type, $data)
    {
        switch ($type) {
            case 'tenants':
                Tenant::create([
                    'name' => $data['name'],
                    'domain' => $data['domain'],
                    'database' => $data['database'] ?? null,
                ]);
                break;
            case 'users':
                User::create([
                    'name' => $data['name'],
                    'email' => $data['email'],
                    'password' => bcrypt($data['password'] ?? 'password'),
                    'tenant_id' => $data['tenant_id'] ?? null,
                ]);
                break;
            default:
                throw new \Exception('Unsupported import type');
        }
    }
}