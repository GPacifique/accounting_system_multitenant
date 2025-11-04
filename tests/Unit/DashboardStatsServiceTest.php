<?php

namespace Tests\Unit;

use Carbon\Carbon;
use App\Services\DashboardStatsService;
use App\Models\Income;
use App\Models\Expense;
use App\Models\Project;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class DashboardStatsServiceTest extends TestCase
{
    use RefreshDatabase;

    protected $service;

    protected function setUp(): void
    {
        parent::setUp();
        $this->service = new DashboardStatsService();
    }

    /** @test */
    public function it_can_get_daily_stats_for_last_30_days()
    {
        $today = Carbon::today();
        
        // Create test data
        Income::factory()->create([
            'received_at' => $today,
            'amount_received' => 1000,
        ]);
        
        Expense::factory()->create([
            'date' => $today,
            'amount' => 300,
        ]);

        $dailyStats = $this->service->getDailyStats(30);

        $this->assertCount(30, $dailyStats);
        $this->assertArrayHasKey('date', $dailyStats[0]);
        $this->assertArrayHasKey('income', $dailyStats[0]);
        $this->assertArrayHasKey('expense', $dailyStats[0]);
        $this->assertArrayHasKey('balance', $dailyStats[0]);
    }

    /** @test */
    public function it_can_calculate_income_by_category()
    {
        $project = Project::factory()->create(['name' => 'Test Project']);
        
        Income::factory(3)->create([
            'project_id' => $project->id,
            'amount_received' => 500,
        ]);

        $incomeByCategory = $this->service->getIncomeByCategory();

        $this->assertIsArray($incomeByCategory);
        if (!empty($incomeByCategory)) {
            $this->assertArrayHasKey('category', $incomeByCategory[0]);
            $this->assertArrayHasKey('total', $incomeByCategory[0]);
            $this->assertArrayHasKey('count', $incomeByCategory[0]);
        }
    }

    /** @test */
    public function it_can_calculate_expense_by_category()
    {
        Expense::factory(5)->create([
            'category' => 'Materials',
            'amount' => 250,
        ]);

        $expenseByCategory = $this->service->getExpenseByCategory();

        $this->assertIsArray($expenseByCategory);
        if (!empty($expenseByCategory)) {
            $this->assertArrayHasKey('category', $expenseByCategory[0]);
            $this->assertArrayHasKey('total', $expenseByCategory[0]);
            $this->assertArrayHasKey('count', $expenseByCategory[0]);
        }
    }

    /** @test */
    public function it_can_get_weekly_stats()
    {
        $today = Carbon::today();
        
        Income::factory()->create([
            'received_at' => $today,
            'amount_received' => 2000,
        ]);
        
        Expense::factory()->create([
            'date' => $today,
            'amount' => 500,
        ]);

        $weeklyStats = $this->service->getWeeklyStats(12);

        $this->assertCount(12, $weeklyStats);
        $this->assertArrayHasKey('week_start', $weeklyStats[0]);
        $this->assertArrayHasKey('week_end', $weeklyStats[0]);
        $this->assertArrayHasKey('income', $weeklyStats[0]);
        $this->assertArrayHasKey('expense', $weeklyStats[0]);
        $this->assertArrayHasKey('balance', $weeklyStats[0]);
    }

    /** @test */
    public function it_can_get_financial_summary()
    {
        $today = Carbon::today();
        $startOfMonth = $today->copy()->startOfMonth();

        // Today
        Income::factory()->create([
            'received_at' => $today,
            'amount_received' => 1500,
        ]);
        
        Expense::factory()->create([
            'date' => $today,
            'amount' => 400,
        ]);

        // This month
        Income::factory()->create([
            'received_at' => $startOfMonth->copy()->addDay(),
            'amount_received' => 2000,
        ]);

        $summary = $this->service->getFinancialSummary();

        $this->assertArrayHasKey('today', $summary);
        $this->assertArrayHasKey('this_month', $summary);
        $this->assertArrayHasKey('this_year', $summary);
        $this->assertArrayHasKey('all_time', $summary);

        // Verify structure
        foreach (['today', 'this_month', 'this_year', 'all_time'] as $period) {
            $this->assertArrayHasKey('income', $summary[$period]);
            $this->assertArrayHasKey('expense', $summary[$period]);
            $this->assertArrayHasKey('balance', $summary[$period]);
        }
    }

    /** @test */
    public function financial_summary_calculates_correct_balance()
    {
        $today = Carbon::today();

        Income::factory()->create([
            'received_at' => $today,
            'amount_received' => 1000,
        ]);
        
        Expense::factory()->create([
            'date' => $today,
            'amount' => 300,
        ]);

        $summary = $this->service->getFinancialSummary();

        $this->assertEquals(1000, $summary['today']['income']);
        $this->assertEquals(300, $summary['today']['expense']);
        $this->assertEquals(700, $summary['today']['balance']);
    }

    /** @test */
    public function it_can_get_top_projects_by_income()
    {
        $project1 = Project::factory()->create(['contract_value' => 5000]);
        $project2 = Project::factory()->create(['contract_value' => 3000]);
        
        Income::factory(5)->create([
            'project_id' => $project1->id,
            'amount_received' => 500,
        ]);
        
        Income::factory(2)->create([
            'project_id' => $project2->id,
            'amount_received' => 800,
        ]);

        $topProjects = $this->service->getTopProjects(5);

        $this->assertIsArray($topProjects);
        if (!empty($topProjects)) {
            $this->assertArrayHasKey('id', $topProjects[0]);
            $this->assertArrayHasKey('name', $topProjects[0]);
            $this->assertArrayHasKey('income', $topProjects[0]);
            $this->assertArrayHasKey('target', $topProjects[0]);
            $this->assertArrayHasKey('completion_percent', $topProjects[0]);
        }
    }

    /** @test */
    public function it_can_get_cash_flow_analysis()
    {
        $today = Carbon::today();
        
        for ($i = 0; $i < 6; $i++) {
            $date = $today->copy()->subMonths($i);
            Income::factory()->create([
                'received_at' => $date,
                'amount_received' => 3000,
            ]);
            
            Expense::factory()->create([
                'date' => $date,
                'amount' => 1000,
            ]);
        }

        $cashFlow = $this->service->getCashFlowAnalysis(6);

        $this->assertCount(6, $cashFlow);
        $this->assertArrayHasKey('month', $cashFlow[0]);
        $this->assertArrayHasKey('income', $cashFlow[0]);
        $this->assertArrayHasKey('expense', $cashFlow[0]);
        $this->assertArrayHasKey('net_cash_flow', $cashFlow[0]);
        $this->assertArrayHasKey('margin', $cashFlow[0]);
    }

    /** @test */
    public function it_can_get_payment_status_breakdown()
    {
        Income::factory(3)->create(['payment_status' => 'Paid']);
        Income::factory(2)->create(['payment_status' => 'Pending']);
        Income::factory(1)->create(['payment_status' => 'Overdue']);

        $breakdown = $this->service->getPaymentStatusBreakdown();

        $this->assertIsArray($breakdown);
        $this->assertGreaterThan(0, count($breakdown));
        
        foreach ($breakdown as $item) {
            $this->assertArrayHasKey('status', $item);
            $this->assertArrayHasKey('count', $item);
            $this->assertArrayHasKey('total', $item);
        }
    }

    /** @test */
    public function it_can_get_outstanding_receivables()
    {
        Income::factory(2)->create(['payment_status' => 'Pending', 'amount_remaining' => 500]);
        Income::factory(1)->create(['payment_status' => 'Overdue', 'amount_remaining' => 300]);
        Income::factory(3)->create(['payment_status' => 'Paid', 'amount_remaining' => 0]);

        $outstanding = $this->service->getOutstandingReceivables();

        $this->assertArrayHasKey('total_outstanding', $outstanding);
        $this->assertArrayHasKey('count', $outstanding);
        $this->assertArrayHasKey('pending_count', $outstanding);
        $this->assertArrayHasKey('overdue_count', $outstanding);

        $this->assertEquals(800, $outstanding['total_outstanding']);
        $this->assertEquals(3, $outstanding['count']);
        $this->assertEquals(2, $outstanding['pending_count']);
        $this->assertEquals(1, $outstanding['overdue_count']);
    }

    /** @test */
    public function it_can_get_expense_by_payment_method()
    {
        Expense::factory(3)->create(['method' => 'cash', 'amount' => 100]);
        Expense::factory(2)->create(['method' => 'card', 'amount' => 150]);
        Expense::factory(1)->create(['method' => 'bank', 'amount' => 200]);

        $byMethod = $this->service->getExpenseByMethod();

        $this->assertIsArray($byMethod);
        foreach ($byMethod as $item) {
            $this->assertArrayHasKey('method', $item);
            $this->assertArrayHasKey('total', $item);
            $this->assertArrayHasKey('count', $item);
        }
    }

    /** @test */
    public function it_can_get_quick_stats()
    {
        $today = Carbon::today();
        
        Income::factory()->create([
            'received_at' => $today,
            'amount_received' => 2000,
        ]);
        
        Expense::factory()->create([
            'date' => $today,
            'amount' => 600,
        ]);

        $quickStats = $this->service->getQuickStats();

        $this->assertArrayHasKey('today_income', $quickStats);
        $this->assertArrayHasKey('today_expense', $quickStats);
        $this->assertArrayHasKey('month_income', $quickStats);
        $this->assertArrayHasKey('month_expense', $quickStats);
        $this->assertArrayHasKey('outstanding', $quickStats);
        $this->assertArrayHasKey('total_transactions', $quickStats);

        $this->assertEquals(2000, $quickStats['today_income']);
        $this->assertEquals(600, $quickStats['today_expense']);
    }

    /** @test */
    public function daily_stats_correctly_calculates_balance()
    {
        $today = Carbon::today();
        
        Income::factory()->create([
            'received_at' => $today,
            'amount_received' => 5000,
        ]);
        
        Expense::factory()->create([
            'date' => $today,
            'amount' => 2000,
        ]);

        $dailyStats = $this->service->getDailyStats(1);

        // Last day should be today
        $todayStats = end($dailyStats);
        $this->assertEquals(5000, $todayStats['income']);
        $this->assertEquals(2000, $todayStats['expense']);
        $this->assertEquals(3000, $todayStats['balance']);
    }

    /** @test */
    public function cash_flow_analysis_calculates_margin_correctly()
    {
        $today = Carbon::today();
        $date = $today->copy()->subMonths(1);
        
        Income::factory()->create([
            'received_at' => $date,
            'amount_received' => 10000,
        ]);
        
        Expense::factory()->create([
            'date' => $date,
            'amount' => 4000,
        ]);

        $cashFlow = $this->service->getCashFlowAnalysis(2);

        $lastMonth = $cashFlow[0];
        $expectedMargin = (($lastMonth['income'] - $lastMonth['expense']) / $lastMonth['income']) * 100;
        
        $this->assertEquals(round($expectedMargin, 2), $lastMonth['margin']);
    }
}
