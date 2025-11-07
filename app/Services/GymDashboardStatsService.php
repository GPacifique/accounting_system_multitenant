<?php

namespace App\Services;

use Carbon\Carbon;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;
use App\Models\Member;
use App\Models\Trainer;
use App\Models\FitnessClass;
use App\Models\ClassBooking;
use App\Models\Membership;
use App\Models\Equipment;
use App\Models\GymRevenue;
use App\Models\Expense;

class GymDashboardStatsService
{
    /**
     * Check if table and column exist
     */
    private function has(string $table, ?string $column = null): bool
    {
        if (!Schema::hasTable($table)) {
            return false;
        }
        return $column ? Schema::hasColumn($table, $column) : true;
    }

    /**
     * Get membership statistics
     */
    public function getMembershipStats()
    {
        $today = Carbon::today();
        $thisMonth = Carbon::now()->startOfMonth();
        $lastMonth = Carbon::now()->subMonth()->startOfMonth();

        $stats = [
            'total_members' => 0,
            'active_members' => 0,
            'inactive_members' => 0,
            'new_members_this_month' => 0,
            'new_members_last_month' => 0,
            'members_growth_rate' => 0,
            'total_memberships' => 0,
            'active_memberships' => 0,
            'expiring_memberships' => 0,
            'expired_memberships' => 0,
            'membership_retention_rate' => 0,
        ];

        if ($this->has('members')) {
            $stats['total_members'] = Member::count();
            
            if ($this->has('members', 'status')) {
                $stats['active_members'] = Member::where('status', 'active')->count();
                $stats['inactive_members'] = Member::where('status', 'inactive')->count();
            }

            $stats['new_members_this_month'] = Member::where('created_at', '>=', $thisMonth)->count();
            $stats['new_members_last_month'] = Member::whereBetween('created_at', [
                $lastMonth, $thisMonth
            ])->count();

            if ($stats['new_members_last_month'] > 0) {
                $stats['members_growth_rate'] = round(
                    (($stats['new_members_this_month'] - $stats['new_members_last_month']) / $stats['new_members_last_month']) * 100, 
                    2
                );
            }
        }

        if ($this->has('memberships')) {
            $stats['total_memberships'] = Membership::count();
            
            if ($this->has('memberships', 'status')) {
                $stats['active_memberships'] = Membership::where('status', 'active')->count();
            }

            if ($this->has('memberships', 'end_date')) {
                $stats['expiring_memberships'] = Membership::where('end_date', '<=', Carbon::now()->addDays(30))
                    ->where('end_date', '>', Carbon::now())
                    ->count();
                $stats['expired_memberships'] = Membership::where('end_date', '<', Carbon::now())->count();

                $totalMemberships = $stats['total_memberships'];
                if ($totalMemberships > 0) {
                    $stats['membership_retention_rate'] = round(
                        ($stats['active_memberships'] / $totalMemberships) * 100, 
                        2
                    );
                }
            }
        }

        return $stats;
    }

    /**
     * Get revenue statistics
     */
    public function getRevenueStats()
    {
        $today = Carbon::today();
        $thisMonth = Carbon::now()->startOfMonth();
        $lastMonth = Carbon::now()->subMonth()->startOfMonth();

        $stats = [
            'total_revenue' => 0,
            'revenue_today' => 0,
            'revenue_this_month' => 0,
            'revenue_last_month' => 0,
            'revenue_growth_rate' => 0,
            'average_daily_revenue' => 0,
            'revenue_by_type' => [],
            'revenue_by_payment_method' => [],
        ];

        if ($this->has('gym_revenues', 'amount')) {
            $stats['total_revenue'] = GymRevenue::sum('amount');
            $stats['revenue_today'] = GymRevenue::whereDate('received_at', $today)->sum('amount');
            $stats['revenue_this_month'] = GymRevenue::where('received_at', '>=', $thisMonth)->sum('amount');
            $stats['revenue_last_month'] = GymRevenue::whereBetween('received_at', [
                $lastMonth, $thisMonth
            ])->sum('amount');

            if ($stats['revenue_last_month'] > 0) {
                $stats['revenue_growth_rate'] = round(
                    (($stats['revenue_this_month'] - $stats['revenue_last_month']) / $stats['revenue_last_month']) * 100,
                    2
                );
            }

            $daysInMonth = Carbon::now()->daysInMonth;
            $stats['average_daily_revenue'] = $daysInMonth > 0 ? round($stats['revenue_this_month'] / $daysInMonth, 2) : 0;

            if ($this->has('gym_revenues', 'revenue_type')) {
                $stats['revenue_by_type'] = GymRevenue::select('revenue_type', DB::raw('SUM(amount) as total'))
                    ->groupBy('revenue_type')
                    ->pluck('total', 'revenue_type')
                    ->toArray();
            }

            if ($this->has('gym_revenues', 'payment_method')) {
                $stats['revenue_by_payment_method'] = GymRevenue::select('payment_method', DB::raw('SUM(amount) as total'))
                    ->whereNotNull('payment_method')
                    ->groupBy('payment_method')
                    ->pluck('total', 'payment_method')
                    ->toArray();
            }
        }

        return $stats;
    }

    /**
     * Get class statistics
     */
    public function getClassStats()
    {
        $today = Carbon::today();
        $thisWeek = Carbon::now()->startOfWeek();

        $stats = [
            'total_classes' => 0,
            'classes_today' => 0,
            'classes_this_week' => 0,
            'total_bookings' => 0,
            'bookings_today' => 0,
            'bookings_this_week' => 0,
            'average_attendance_rate' => 0,
            'most_popular_classes' => [],
            'class_capacity_utilization' => 0,
        ];

        if ($this->has('fitness_classes')) {
            $stats['total_classes'] = FitnessClass::count();
            $stats['classes_today'] = FitnessClass::whereDate('start_time', $today)->count();
            $stats['classes_this_week'] = FitnessClass::where('start_time', '>=', $thisWeek)->count();
        }

        if ($this->has('class_bookings')) {
            $stats['total_bookings'] = ClassBooking::count();
            $stats['bookings_today'] = ClassBooking::whereDate('created_at', $today)->count();
            $stats['bookings_this_week'] = ClassBooking::where('created_at', '>=', $thisWeek)->count();

            if ($this->has('fitness_classes') && $this->has('fitness_classes', 'capacity')) {
                // Calculate attendance rate
                $classData = FitnessClass::leftJoin('class_bookings', 'fitness_classes.id', '=', 'class_bookings.fitness_class_id')
                    ->select('fitness_classes.capacity', DB::raw('COUNT(class_bookings.id) as bookings'))
                    ->groupBy('fitness_classes.id', 'fitness_classes.capacity')
                    ->get();

                $totalCapacity = $classData->sum('capacity');
                $totalBookings = $classData->sum('bookings');

                if ($totalCapacity > 0) {
                    $stats['average_attendance_rate'] = round(($totalBookings / $totalCapacity) * 100, 2);
                    $stats['class_capacity_utilization'] = round(($totalBookings / $totalCapacity) * 100, 2);
                }

                // Most popular classes
                $stats['most_popular_classes'] = FitnessClass::leftJoin('class_bookings', 'fitness_classes.id', '=', 'class_bookings.fitness_class_id')
                    ->select('fitness_classes.name', DB::raw('COUNT(class_bookings.id) as booking_count'))
                    ->groupBy('fitness_classes.id', 'fitness_classes.name')
                    ->orderByDesc('booking_count')
                    ->limit(5)
                    ->get()
                    ->toArray();
            }
        }

        return $stats;
    }

    /**
     * Get trainer statistics
     */
    public function getTrainerStats()
    {
        $stats = [
            'total_trainers' => 0,
            'active_trainers' => 0,
            'inactive_trainers' => 0,
            'average_experience' => 0,
            'total_trainer_revenue' => 0,
            'average_hourly_rate' => 0,
            'top_trainers_by_revenue' => [],
            'trainers_by_specialization' => [],
        ];

        if ($this->has('trainers')) {
            $stats['total_trainers'] = Trainer::count();

            if ($this->has('trainers', 'status')) {
                $stats['active_trainers'] = Trainer::where('status', 'active')->count();
                $stats['inactive_trainers'] = Trainer::where('status', 'inactive')->count();
            }

            if ($this->has('trainers', 'experience_years')) {
                $stats['average_experience'] = round(Trainer::avg('experience_years'), 1);
            }

            if ($this->has('trainers', 'hourly_rate')) {
                $stats['average_hourly_rate'] = round(Trainer::avg('hourly_rate'), 2);
            }

            if ($this->has('gym_revenues', 'trainer_id')) {
                $stats['total_trainer_revenue'] = GymRevenue::whereNotNull('trainer_id')->sum('amount');

                $stats['top_trainers_by_revenue'] = Trainer::leftJoin('gym_revenues', 'trainers.id', '=', 'gym_revenues.trainer_id')
                    ->select('trainers.first_name', 'trainers.last_name', DB::raw('COALESCE(SUM(gym_revenues.amount), 0) as revenue'))
                    ->groupBy('trainers.id', 'trainers.first_name', 'trainers.last_name')
                    ->orderByDesc('revenue')
                    ->limit(5)
                    ->get()
                    ->toArray();
            }

            if ($this->has('trainers', 'specializations')) {
                // This would need special handling for JSON field
                $trainers = Trainer::whereNotNull('specializations')->get();
                $specializationCounts = [];
                
                foreach ($trainers as $trainer) {
                    $specializations = is_string($trainer->specializations) 
                        ? json_decode($trainer->specializations, true) 
                        : $trainer->specializations;
                    
                    if (is_array($specializations)) {
                        foreach ($specializations as $specialization) {
                            $specializationCounts[$specialization] = ($specializationCounts[$specialization] ?? 0) + 1;
                        }
                    }
                }
                
                $stats['trainers_by_specialization'] = $specializationCounts;
            }
        }

        return $stats;
    }

    /**
     * Get equipment statistics
     */
    public function getEquipmentStats()
    {
        $stats = [
            'total_equipment' => 0,
            'working_equipment' => 0,
            'maintenance_needed' => 0,
            'out_of_order' => 0,
            'equipment_utilization_rate' => 0,
            'maintenance_due_soon' => 0,
            'equipment_by_category' => [],
        ];

        if ($this->has('equipment')) {
            $stats['total_equipment'] = Equipment::count();

            if ($this->has('equipment', 'status')) {
                $stats['working_equipment'] = Equipment::where('status', 'working')->count();
                $stats['maintenance_needed'] = Equipment::where('status', 'needs_maintenance')->count();
                $stats['out_of_order'] = Equipment::where('status', 'out_of_order')->count();

                if ($stats['total_equipment'] > 0) {
                    $stats['equipment_utilization_rate'] = round(
                        ($stats['working_equipment'] / $stats['total_equipment']) * 100, 
                        2
                    );
                }
            }

            if ($this->has('equipment', 'next_maintenance_date')) {
                $stats['maintenance_due_soon'] = Equipment::where('next_maintenance_date', '<=', Carbon::now()->addDays(7))
                    ->where('next_maintenance_date', '>', Carbon::now())
                    ->count();
            }

            if ($this->has('equipment', 'category')) {
                $stats['equipment_by_category'] = Equipment::select('category', DB::raw('COUNT(*) as count'))
                    ->groupBy('category')
                    ->pluck('count', 'category')
                    ->toArray();
            }
        }

        return $stats;
    }

    /**
     * Get attendance statistics
     */
    public function getAttendanceStats()
    {
        $today = Carbon::today();
        $thisWeek = Carbon::now()->startOfWeek();
        $thisMonth = Carbon::now()->startOfMonth();

        $stats = [
            'daily_check_ins' => 0,
            'weekly_check_ins' => 0,
            'monthly_check_ins' => 0,
            'peak_hours' => [],
            'attendance_by_day' => [],
            'member_visit_frequency' => 0,
        ];

        if ($this->has('class_bookings')) {
            $stats['daily_check_ins'] = ClassBooking::whereDate('created_at', $today)->count();
            $stats['weekly_check_ins'] = ClassBooking::where('created_at', '>=', $thisWeek)->count();
            $stats['monthly_check_ins'] = ClassBooking::where('created_at', '>=', $thisMonth)->count();

            // Peak hours analysis (based on class booking times)
            if ($this->has('fitness_classes', 'start_time')) {
                $hourlyBookings = ClassBooking::join('fitness_classes', 'class_bookings.fitness_class_id', '=', 'fitness_classes.id')
                    ->select(DB::raw('HOUR(fitness_classes.start_time) as hour'), DB::raw('COUNT(*) as bookings'))
                    ->groupBy(DB::raw('HOUR(fitness_classes.start_time)'))
                    ->orderByDesc('bookings')
                    ->limit(3)
                    ->get()
                    ->toArray();

                $stats['peak_hours'] = $hourlyBookings;
            }

            // Attendance by day of week
            $stats['attendance_by_day'] = ClassBooking::select(
                DB::raw('DAYNAME(created_at) as day'),
                DB::raw('COUNT(*) as bookings')
            )
                ->groupBy(DB::raw('DAYNAME(created_at)'))
                ->orderBy(DB::raw('FIELD(DAYNAME(created_at), "Monday", "Tuesday", "Wednesday", "Thursday", "Friday", "Saturday", "Sunday")'))
                ->get()
                ->toArray();

            // Member visit frequency
            if ($this->has('members')) {
                $totalMembers = Member::count();
                if ($totalMembers > 0) {
                    $stats['member_visit_frequency'] = round($stats['monthly_check_ins'] / $totalMembers, 2);
                }
            }
        }

        return $stats;
    }

    /**
     * Get financial summary specific to gym operations
     */
    public function getFinancialSummary()
    {
        $thisMonth = Carbon::now()->startOfMonth();
        $lastMonth = Carbon::now()->subMonth()->startOfMonth();

        $summary = [
            'total_revenue' => 0,
            'total_expenses' => 0,
            'net_profit' => 0,
            'revenue_this_month' => 0,
            'expenses_this_month' => 0,
            'profit_this_month' => 0,
            'revenue_last_month' => 0,
            'expenses_last_month' => 0,
            'profit_last_month' => 0,
            'revenue_growth' => 0,
            'expense_growth' => 0,
            'profit_margin' => 0,
        ];

        if ($this->has('gym_revenues', 'amount')) {
            $summary['total_revenue'] = GymRevenue::sum('amount');
            $summary['revenue_this_month'] = GymRevenue::where('received_at', '>=', $thisMonth)->sum('amount');
            $summary['revenue_last_month'] = GymRevenue::whereBetween('received_at', [
                $lastMonth, $thisMonth
            ])->sum('amount');

            if ($summary['revenue_last_month'] > 0) {
                $summary['revenue_growth'] = round(
                    (($summary['revenue_this_month'] - $summary['revenue_last_month']) / $summary['revenue_last_month']) * 100,
                    2
                );
            }
        }

        if ($this->has('expenses', 'amount')) {
            $summary['total_expenses'] = Expense::sum('amount');
            $summary['expenses_this_month'] = Expense::where('date', '>=', $thisMonth)->sum('amount');
            $summary['expenses_last_month'] = Expense::whereBetween('date', [
                $lastMonth, $thisMonth
            ])->sum('amount');

            if ($summary['expenses_last_month'] > 0) {
                $summary['expense_growth'] = round(
                    (($summary['expenses_this_month'] - $summary['expenses_last_month']) / $summary['expenses_last_month']) * 100,
                    2
                );
            }
        }

        $summary['net_profit'] = $summary['total_revenue'] - $summary['total_expenses'];
        $summary['profit_this_month'] = $summary['revenue_this_month'] - $summary['expenses_this_month'];
        $summary['profit_last_month'] = $summary['revenue_last_month'] - $summary['expenses_last_month'];

        if ($summary['revenue_this_month'] > 0) {
            $summary['profit_margin'] = round(
                ($summary['profit_this_month'] / $summary['revenue_this_month']) * 100,
                2
            );
        }

        return $summary;
    }

    /**
     * Get daily stats for charts
     */
    public function getDailyStats($days = 30)
    {
        $endDate = Carbon::today();
        $startDate = $endDate->copy()->subDays($days - 1);

        $dailyData = [];

        for ($i = $days - 1; $i >= 0; $i--) {
            $date = $endDate->copy()->subDays($i);
            $dateStr = $date->format('Y-m-d');

            $revenue = 0;
            $expenses = 0;
            $bookings = 0;
            $newMembers = 0;

            if ($this->has('gym_revenues', 'amount')) {
                $revenue = GymRevenue::whereDate('received_at', $dateStr)->sum('amount');
            }

            if ($this->has('expenses', 'amount')) {
                $expenses = Expense::whereDate('date', $dateStr)->sum('amount');
            }

            if ($this->has('class_bookings')) {
                $bookings = ClassBooking::whereDate('created_at', $dateStr)->count();
            }

            if ($this->has('members')) {
                $newMembers = Member::whereDate('created_at', $dateStr)->count();
            }

            $dailyData[] = [
                'date' => $dateStr,
                'revenue' => $revenue,
                'expenses' => $expenses,
                'profit' => $revenue - $expenses,
                'bookings' => $bookings,
                'new_members' => $newMembers,
            ];
        }

        return $dailyData;
    }
}