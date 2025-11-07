<?php

namespace App\Http\Controllers;

use Carbon\Carbon;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;
use App\Models\Member;
use App\Models\Trainer;
use App\Models\FitnessClass;
use App\Models\ClassBooking;
use App\Models\Membership;
use App\Models\Equipment;
use App\Models\GymRevenue;
use App\Models\Payment;
use App\Models\Transaction;
use App\Models\Expense;
use App\Services\GymDashboardStatsService;

class GymDashboardController extends Controller
{
    protected $statsService;

    public function __construct(GymDashboardStatsService $statsService)
    {
        $this->statsService = $statsService;
    }

    public function index()
    {
        $user = Auth::user();
        
        // Check if user has any meaningful permissions
        if (!$user->hasRole(['super-admin', 'admin', 'manager', 'accountant']) && 
            !$user->hasAnyPermission(['gym.view', 'members.view', 'trainers.view', 'classes.view', 'revenue.view'])) {
            return redirect('/')->with('error', 'You need proper permissions to access the gym dashboard.');
        }
        
        // Route to appropriate dashboard based on role
        if ($user->hasRole(['super-admin', 'admin'])) {
            return $this->adminGymDashboard();
        } elseif ($user->hasRole('accountant')) {
            return $this->accountantGymDashboard();
        } elseif ($user->hasRole('manager')) {
            return $this->managerGymDashboard();
        }
        
        return $this->userGymDashboard();
    }

    /**
     * Admin sees all gym data and statistics
     */
    private function adminGymDashboard()
    {
        $today = Carbon::today();
        $startOfMonth = $today->copy()->startOfMonth();
        $endOfToday = $today->endOfDay();

        $has = function (string $table, ?string $column = null): bool {
            if (!Schema::hasTable($table)) {
                return false;
            }
            return $column ? Schema::hasColumn($table, $column) : true;
        };

        // Get gym-specific stats from service
        $membershipStats = $this->statsService->getMembershipStats();
        $revenueStats = $this->statsService->getRevenueStats();
        $classStats = $this->statsService->getClassStats();
        $trainerStats = $this->statsService->getTrainerStats();
        $equipmentStats = $this->statsService->getEquipmentStats();
        $attendanceStats = $this->statsService->getAttendanceStats();
        $financialSummary = $this->statsService->getFinancialSummary();

        // Member Statistics
        $totalMembers = $has('members') ? Member::count() : 0;
        $activeMembers = $has('members', 'status') 
            ? Member::where('status', 'active')->count() 
            : $totalMembers;
        $newMembersThisMonth = $has('members')
            ? Member::whereBetween('created_at', [$startOfMonth, $endOfToday])->count()
            : 0;
        $recentMembers = $has('members') ? Member::latest()->limit(8)->get() : collect();

        // Trainer Statistics
        $totalTrainers = $has('trainers') ? Trainer::count() : 0;
        $activeTrainers = $has('trainers', 'status')
            ? Trainer::where('status', 'active')->count()
            : $totalTrainers;
        $recentTrainers = $has('trainers') ? Trainer::latest()->limit(6)->get() : collect();

        // Class Statistics
        $totalClasses = $has('fitness_classes') ? FitnessClass::count() : 0;
        $classesToday = $has('fitness_classes')
            ? FitnessClass::whereDate('start_time', $today)->count()
            : 0;
        $upcomingClasses = $has('fitness_classes')
            ? FitnessClass::where('start_time', '>', now())->limit(10)->get()
            : collect();

        // Booking Statistics
        $totalBookings = $has('class_bookings') ? ClassBooking::count() : 0;
        $bookingsToday = $has('class_bookings')
            ? ClassBooking::whereDate('created_at', $today)->count()
            : 0;
        $recentBookings = $has('class_bookings') 
            ? ClassBooking::with(['member', 'fitnessClass'])->latest()->limit(8)->get() 
            : collect();

        // Revenue Statistics
        $totalRevenue = $has('gym_revenues', 'amount') ? GymRevenue::sum('amount') : 0;
        $revenueToday = $has('gym_revenues', 'amount')
            ? GymRevenue::whereDate('received_at', $today)->sum('amount')
            : 0;
        $revenueThisMonth = $has('gym_revenues', 'amount')
            ? GymRevenue::whereBetween('received_at', [$startOfMonth, $endOfToday])->sum('amount')
            : 0;
        $recentRevenues = $has('gym_revenues') ? GymRevenue::latest()->limit(8)->get() : collect();

        // Equipment Statistics
        $totalEquipment = $has('equipment') ? Equipment::count() : 0;
        $workingEquipment = $has('equipment', 'status')
            ? Equipment::where('status', 'working')->count()
            : $totalEquipment;
        $maintenanceNeeded = $has('equipment', 'status')
            ? Equipment::where('status', 'needs_maintenance')->count()
            : 0;

        // Membership Statistics
        $totalMemberships = $has('memberships') ? Membership::count() : 0;
        $activeMemberships = $has('memberships', 'status')
            ? Membership::where('status', 'active')->count()
            : $totalMemberships;
        $expiringMemberships = $has('memberships')
            ? Membership::where('end_date', '<=', Carbon::now()->addDays(30))->count()
            : 0;

        // Expense Statistics (gym-specific)
        $expensesThisMonth = $has('expenses', 'amount')
            ? Expense::whereBetween('date', [$startOfMonth, $endOfToday])->sum('amount')
            : 0;
        $recentExpenses = $has('expenses') ? Expense::latest()->limit(8)->get() : collect();

        // Daily / Weekly / Monthly / Yearly balances
        $expensesToday = $has('expenses', 'amount')
            ? Expense::whereDate('date', $today)->sum('amount')
            : 0;

        $startOfWeek = $today->copy()->startOfWeek();
        $endOfWeek = $today->copy()->endOfWeek();

        $expensesThisWeek = $has('expenses', 'amount')
            ? Expense::whereBetween('date', [$startOfWeek, $endOfWeek])->sum('amount')
            : 0;

        $revenueThisWeek = $has('gym_revenues', 'amount')
            ? GymRevenue::whereBetween('received_at', [$startOfWeek, $endOfWeek])->sum('amount')
            : 0;

        $startOfYear = $today->copy()->startOfYear();
        $endOfYear = $today->copy()->endOfYear();

        $expensesThisYear = $has('expenses', 'amount')
            ? Expense::whereBetween('date', [$startOfYear, $endOfYear])->sum('amount')
            : 0;

        $revenueThisYear = $has('gym_revenues', 'amount')
            ? GymRevenue::whereBetween('received_at', [$startOfYear, $endOfYear])->sum('amount')
            : 0;

        // Balances
        $balanceToday = $revenueToday - $expensesToday;
        $balanceWeek = $revenueThisWeek - $expensesThisWeek;
        $balanceMonth = $revenueThisMonth - $expensesThisMonth;
        $balanceYear = $revenueThisYear - $expensesThisYear;

        // Revenue by category
        $revenueByCategory = [];
        if ($has('gym_revenues', 'revenue_type')) {
            $revenueByCategory = GymRevenue::select('revenue_type', DB::raw('SUM(amount) as total'))
                ->groupBy('revenue_type')
                ->pluck('total', 'revenue_type')
                ->toArray();
        }

        // Monthly revenue trend
        $months = [];
        $monthlyRevenue = [];
        $monthlyExpenses = [];
        $monthlyMembers = [];

        for ($i = 5; $i >= 0; $i--) {
            $dt = Carbon::now()->subMonths($i);
            $months[] = $dt->format('M Y');

            $mStart = $dt->copy()->startOfMonth();
            $mEnd = $dt->copy()->endOfMonth();

            $monthlyRevenue[] = $has('gym_revenues', 'amount')
                ? GymRevenue::whereBetween('received_at', [$mStart, $mEnd])->sum('amount')
                : 0;

            $monthlyExpenses[] = $has('expenses', 'amount')
                ? Expense::whereBetween('date', [$mStart, $mEnd])->sum('amount')
                : 0;

            $monthlyMembers[] = $has('members')
                ? Member::whereBetween('created_at', [$mStart, $mEnd])->count()
                : 0;
        }

        // Top trainers by revenue/classes
        $topTrainers = [];
        if ($has('trainers') && $has('gym_revenues', 'trainer_id')) {
            // Use subquery to avoid GROUP BY issues
            $revenueSubquery = DB::table('gym_revenues')
                ->select('trainer_id', DB::raw('SUM(amount) as total_revenue'))
                ->groupBy('trainer_id');
            
            $topTrainers = Trainer::leftJoinSub($revenueSubquery, 'revenue_data', function ($join) {
                $join->on('trainers.id', '=', 'revenue_data.trainer_id');
            })
            ->select('trainers.*', DB::raw('COALESCE(revenue_data.total_revenue, 0) as total_revenue'))
            ->orderByDesc('total_revenue')
            ->limit(5)
            ->get();
        }

        // Class attendance rates
        $classAttendance = [];
        if ($has('fitness_classes') && $has('class_bookings')) {
            $classAttendance = FitnessClass::leftJoin('class_bookings', 'fitness_classes.id', '=', 'class_bookings.fitness_class_id')
                ->select('fitness_classes.name', 'fitness_classes.max_capacity', 
                         DB::raw('COUNT(class_bookings.id) as bookings'))
                ->groupBy('fitness_classes.id', 'fitness_classes.name', 'fitness_classes.max_capacity')
                ->orderByDesc('bookings')
                ->limit(10)
                ->get();
        }

        return view('gym.dashboard.admin', compact(
            'membershipStats', 'revenueStats', 'classStats', 'trainerStats', 
            'equipmentStats', 'attendanceStats', 'financialSummary',
            'totalMembers', 'activeMembers', 'newMembersThisMonth', 'recentMembers',
            'totalTrainers', 'activeTrainers', 'recentTrainers',
            'totalClasses', 'classesToday', 'upcomingClasses',
            'totalBookings', 'bookingsToday', 'recentBookings',
            'totalRevenue', 'revenueToday', 'revenueThisMonth', 'recentRevenues',
            'totalEquipment', 'workingEquipment', 'maintenanceNeeded',
            'totalMemberships', 'activeMemberships', 'expiringMemberships',
            'expensesThisMonth', 'recentExpenses', 'revenueByCategory',
            // short term expense/revenue/balance metrics
            'expensesToday', 'expensesThisWeek', 'revenueThisWeek', 'expensesThisYear', 'revenueThisYear',
            'balanceToday', 'balanceWeek', 'balanceMonth', 'balanceYear',
            'months', 'monthlyRevenue', 'monthlyExpenses', 'monthlyMembers',
            'topTrainers', 'classAttendance'
        ));
    }

    /**
     * Accountant sees financial gym data
     */
    private function accountantGymDashboard()
    {
        $financialSummary = $this->statsService->getFinancialSummary();
        $revenueStats = $this->statsService->getRevenueStats();
        $membershipStats = $this->statsService->getMembershipStats();

        $has = function (string $table, ?string $column = null): bool {
            if (!Schema::hasTable($table)) {
                return false;
            }
            return $column ? Schema::hasColumn($table, $column) : true;
        };

        // Revenue analysis
        $revenueByCategory = [];
        if ($has('gym_revenues', 'revenue_type')) {
            $revenueByCategory = GymRevenue::select('revenue_type', DB::raw('SUM(amount) as total'))
                ->groupBy('revenue_type')
                ->pluck('total', 'revenue_type')
                ->toArray();
        }

        $revenueByPaymentMethod = [];
        if ($has('gym_revenues', 'payment_method')) {
            $revenueByPaymentMethod = GymRevenue::select('payment_method', DB::raw('SUM(amount) as total'))
                ->whereNotNull('payment_method')
                ->groupBy('payment_method')
                ->pluck('total', 'payment_method')
                ->toArray();
        }

        // Expense analysis by category
        $expensesByCategory = [];
        if ($has('expenses', 'category')) {
            $expensesByCategory = Expense::select('category', DB::raw('SUM(amount) as total'))
                ->groupBy('category')
                ->pluck('total', 'category')
                ->toArray();
        }

        // Recent financial transactions
        $recentRevenues = $has('gym_revenues') ? GymRevenue::latest()->limit(15)->get() : collect();
        $recentExpenses = $has('expenses') ? Expense::latest()->limit(15)->get() : collect();
        $recentPayments = $has('payments') ? Payment::latest()->limit(15)->get() : collect();

        // Monthly financial trends
        $months = [];
        $monthlyRevenue = [];
        $monthlyExpenses = [];
        $monthlyProfit = [];

        for ($i = 11; $i >= 0; $i--) {
            $dt = Carbon::now()->subMonths($i);
            $months[] = $dt->format('M Y');

            $mStart = $dt->copy()->startOfMonth();
            $mEnd = $dt->copy()->endOfMonth();

            $revenue = $has('gym_revenues', 'amount')
                ? GymRevenue::whereBetween('received_at', [$mStart, $mEnd])->sum('amount')
                : 0;

            $expenses = $has('expenses', 'amount')
                ? Expense::whereBetween('date', [$mStart, $mEnd])->sum('amount')
                : 0;

            $monthlyRevenue[] = $revenue;
            $monthlyExpenses[] = $expenses;
            $monthlyProfit[] = $revenue - $expenses;
        }

        // Short-term balances for accountant view
        $today = Carbon::today();
        $expensesToday = $has('expenses', 'amount') ? Expense::whereDate('date', $today)->sum('amount') : 0;
        $revenueToday = $has('gym_revenues', 'amount') ? GymRevenue::whereDate('received_at', $today)->sum('amount') : 0;
        $startOfWeek = $today->copy()->startOfWeek();
        $endOfWeek = $today->copy()->endOfWeek();
        $revenueThisWeek = $has('gym_revenues', 'amount') ? GymRevenue::whereBetween('received_at', [$startOfWeek, $endOfWeek])->sum('amount') : 0;
        $expensesThisWeek = $has('expenses', 'amount') ? Expense::whereBetween('date', [$startOfWeek, $endOfWeek])->sum('amount') : 0;
        $startOfYear = $today->copy()->startOfYear();
        $endOfYear = $today->copy()->endOfYear();
        $revenueThisYear = $has('gym_revenues', 'amount') ? GymRevenue::whereBetween('received_at', [$startOfYear, $endOfYear])->sum('amount') : 0;
        $expensesThisYear = $has('expenses', 'amount') ? Expense::whereBetween('date', [$startOfYear, $endOfYear])->sum('amount') : 0;
        $balanceToday = $revenueToday - $expensesToday;
        $balanceWeek = $revenueThisWeek - $expensesThisWeek;
        $balanceMonth = array_sum($monthlyRevenue) - array_sum($monthlyExpenses);
        $balanceYear = $revenueThisYear - $expensesThisYear;

        return view('gym.dashboard.accountant', compact(
            'financialSummary', 'revenueStats', 'membershipStats',
            'revenueByCategory', 'revenueByPaymentMethod', 'expensesByCategory',
            'recentRevenues', 'recentExpenses', 'recentPayments',
            'months', 'monthlyRevenue', 'monthlyExpenses', 'monthlyProfit'
        ));
    }

    /**
     * Manager sees operational gym data
     */
    private function managerGymDashboard()
    {
        $membershipStats = $this->statsService->getMembershipStats();
        $classStats = $this->statsService->getClassStats();
        $trainerStats = $this->statsService->getTrainerStats();
        $equipmentStats = $this->statsService->getEquipmentStats();

        $has = function (string $table, ?string $column = null): bool {
            if (!Schema::hasTable($table)) {
                return false;
            }
            return $column ? Schema::hasColumn($table, $column) : true;
        };

        $today = Carbon::today();

        // Operational metrics
        $totalMembers = $has('members') ? Member::count() : 0;
        $activeMembers = $has('members', 'status') 
            ? Member::where('status', 'active')->count() 
            : $totalMembers;

        $totalTrainers = $has('trainers') ? Trainer::count() : 0;
        $activeTrainers = $has('trainers', 'status')
            ? Trainer::where('status', 'active')->count()
            : $totalTrainers;

        $classesToday = $has('fitness_classes')
            ? FitnessClass::whereDate('start_time', $today)->count()
            : 0;

        $bookingsToday = $has('class_bookings')
            ? ClassBooking::whereDate('created_at', $today)->count()
            : 0;

        // Recent activities
        $recentMembers = $has('members') ? Member::latest()->limit(10)->get() : collect();
        $recentTrainers = $has('trainers') ? Trainer::latest()->limit(8)->get() : collect();
        $upcomingClasses = $has('fitness_classes')
            ? FitnessClass::where('start_time', '>', now())->limit(12)->get()
            : collect();

        // Equipment status
        $equipmentWorking = $has('equipment', 'status')
            ? Equipment::where('status', 'working')->count()
            : 0;
        $equipmentMaintenance = $has('equipment', 'status')
            ? Equipment::where('status', 'needs_maintenance')->count()
            : 0;

        // Class popularity
        $popularClasses = [];
        if ($has('fitness_classes') && $has('class_bookings')) {
            $popularClasses = FitnessClass::leftJoin('class_bookings', 'fitness_classes.id', '=', 'class_bookings.fitness_class_id')
                ->select('fitness_classes.name', DB::raw('COUNT(class_bookings.id) as bookings'))
                ->groupBy('fitness_classes.id', 'fitness_classes.name')
                ->orderByDesc('bookings')
                ->limit(8)
                ->get();
        }

        return view('gym.dashboard.manager', compact(
            'membershipStats', 'classStats', 'trainerStats', 'equipmentStats',
            'totalMembers', 'activeMembers', 'totalTrainers', 'activeTrainers',
            'classesToday', 'bookingsToday', 'recentMembers', 'recentTrainers',
            'upcomingClasses', 'equipmentWorking', 'equipmentMaintenance', 'popularClasses'
        ));
    }

    /**
     * Regular user sees limited gym overview
     */
    private function userGymDashboard()
    {
        $has = function (string $table, ?string $column = null): bool {
            if (!Schema::hasTable($table)) {
                return false;
            }
            return $column ? Schema::hasColumn($table, $column) : true;
        };

        $today = Carbon::today();

        // Limited view for employees/staff
        $totalMembers = $has('members') ? Member::count() : 0;
        $classesToday = $has('fitness_classes')
            ? FitnessClass::whereDate('start_time', $today)->count()
            : 0;
        $upcomingClasses = $has('fitness_classes')
            ? FitnessClass::where('start_time', '>', now())->limit(6)->get()
            : collect();

        return view('gym.dashboard.user', compact(
            'totalMembers', 'classesToday', 'upcomingClasses'
        ));
    }

    /**
     * Display gym analytics dashboard
     */
    public function analytics()
    {
        $user = Auth::user();
        
        if (!$user->hasRole(['super-admin', 'admin', 'manager']) && 
            !$user->hasPermission('gym.analytics.view')) {
            return redirect()->route('gym.dashboard')
                ->with('error', 'You do not have permission to view gym analytics.');
        }
        
        $analyticsData = [
            'membershipStats' => $this->statsService->getMembershipStats(),
            'revenueStats' => $this->statsService->getRevenueStats(),
            'classStats' => $this->statsService->getClassStats(),
            'trainerStats' => $this->statsService->getTrainerStats(),
            'equipmentStats' => $this->statsService->getEquipmentStats(),
            'attendanceStats' => $this->statsService->getAttendanceStats(),
            'financialSummary' => $this->statsService->getFinancialSummary(),
        ];
        
        return view('gym.dashboard.analytics', $analyticsData);
    }
}