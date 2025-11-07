<?php

namespace App\Http\Controllers\Gym;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Member;
use App\Models\Attendance;
use App\Models\GymRevenue;
use Carbon\Carbon;

class ReportsController extends Controller
{
    protected function tenantId()
    {
        $tenant = app('currentTenant');
        return $tenant->id ?? null;
    }

    public function index(Request $request)
    {
        $tenantId = $this->tenantId();

        $totalMembers = Member::when($tenantId, fn($q) => $q->where('tenant_id', $tenantId))->count();
        $activeMembers = Member::when($tenantId, fn($q) => $q->where('tenant_id', $tenantId))->active()->count();
        $newMembersLast30 = Member::when($tenantId, fn($q) => $q->where('tenant_id', $tenantId))
            ->where('joined_at', '>=', Carbon::now()->subDays(30))->count();

        $revenueToday = GymRevenue::when($tenantId, fn($q) => $q->where('tenant_id', $tenantId))->today()->completed()->sum('amount');
        $revenueThisMonth = GymRevenue::when($tenantId, fn($q) => $q->where('tenant_id', $tenantId))->thisMonth()->completed()->sum('amount');

        $recentAttendances = Attendance::when($tenantId, fn($q) => $q->where('tenant_id', $tenantId))
            ->with('member')
            ->orderByDesc('checked_in_at')
            ->limit(10)
            ->get();

        return view('gym.reports.index', compact(
            'totalMembers', 'activeMembers', 'newMembersLast30', 'revenueToday', 'revenueThisMonth', 'recentAttendances'
        ));
    }

    public function financial(Request $request)
    {
        $tenantId = $this->tenantId();
        $start = Carbon::now()->subDays(29)->startOfDay();
        $end = Carbon::now()->endOfDay();

        $series = GymRevenue::when($tenantId, fn($q) => $q->where('tenant_id', $tenantId))
            ->completed()
            ->whereBetween('transaction_date', [$start, $end])
            ->selectRaw('DATE(transaction_date) as date, SUM(amount) as total')
            ->groupBy('date')
            ->orderBy('date')
            ->get()
            ->keyBy('date');

        $labels = [];
        $data = [];
        for ($d = $start->copy(); $d <= $end; $d->addDay()) {
            $labels[] = $d->format('Y-m-d');
            $data[] = isset($series[$d->format('Y-m-d')]) ? (float) $series[$d->format('Y-m-d')]->total : 0.0;
        }

        $byType = GymRevenue::getRevenueByType($tenantId, $start, $end);
        $topMembers = GymRevenue::getTopMembers($tenantId, 10, $start, $end);

        return view('gym.reports.financial', compact('labels', 'data', 'byType', 'topMembers'));
    }

    public function membership(Request $request)
    {
        $tenantId = $this->tenantId();
        $start = Carbon::now()->subDays(29)->startOfDay();
        $end = Carbon::now()->endOfDay();

        $daily = Member::when($tenantId, fn($q) => $q->where('tenant_id', $tenantId))
            ->whereBetween('joined_at', [$start, $end])
            ->selectRaw('DATE(joined_at) as date, COUNT(*) as total')
            ->groupBy('date')
            ->orderBy('date')
            ->get()
            ->keyBy('date');

        $labels = [];
        $data = [];
        for ($d = $start->copy(); $d <= $end; $d->addDay()) {
            $labels[] = $d->format('Y-m-d');
            $data[] = isset($daily[$d->format('Y-m-d')]) ? (int) $daily[$d->format('Y-m-d')]->total : 0;
        }

        $byType = Member::when($tenantId, fn($q) => $q->where('tenant_id', $tenantId))
            ->selectRaw('membership_type, COUNT(*) as total')
            ->groupBy('membership_type')
            ->pluck('total', 'membership_type');

        $active = Member::when($tenantId, fn($q) => $q->where('tenant_id', $tenantId))->active()->count();
        $expired = Member::when($tenantId, fn($q) => $q->where('tenant_id', $tenantId))->expired()->count();

        return view('gym.reports.membership', compact('labels', 'data', 'byType', 'active', 'expired'));
    }

    public function attendance(Request $request)
    {
        $tenantId = $this->tenantId();
        $start = Carbon::now()->subDays(29)->startOfDay();
        $end = Carbon::now()->endOfDay();

        $daily = Attendance::when($tenantId, fn($q) => $q->where('tenant_id', $tenantId))
            ->whereBetween('checked_in_at', [$start, $end])
            ->selectRaw('DATE(checked_in_at) as date, COUNT(*) as total')
            ->groupBy('date')
            ->orderBy('date')
            ->get()
            ->keyBy('date');

        $labels = [];
        $data = [];
        for ($d = $start->copy(); $d <= $end; $d->addDay()) {
            $labels[] = $d->format('Y-m-d');
            $data[] = isset($daily[$d->format('Y-m-d')]) ? (int) $daily[$d->format('Y-m-d')]->total : 0;
        }

        $recent = Attendance::when($tenantId, fn($q) => $q->where('tenant_id', $tenantId))
            ->with('member')
            ->orderByDesc('checked_in_at')
            ->limit(50)
            ->get();

        return view('gym.reports.attendance', compact('labels', 'data', 'recent'));
    }
}
