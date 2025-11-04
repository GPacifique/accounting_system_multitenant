# Dashboard Components Quick Reference

## 🎨 Card Components

### Stat Card
Display a single metric with optional trend

```blade
<x-dashboard.stat-card
    title="Daily Income"
    value="$2,500.00"
    icon="📊"
    iconColor="text-green-500"
    borderColor="border-green-500"
    trend="12"
    trendLabel="vs last week"
    subtitle="Invoices received"
/>
```

**Parameters:**
- `title` (string) - Card heading
- `value` (string) - Main metric to display
- `icon` (string) - Emoji or HTML icon
- `iconColor` (string) - Tailwind color class for icon
- `borderColor` (string) - Tailwind color class for left border
- `trend` (float|null) - % change (positive/negative)
- `trendLabel` (string) - Comparison period description
- `subtitle` (string|null) - Optional secondary text

---

### Chart Card
Container for Chart.js canvas with title

```blade
<x-dashboard.chart-card
    chartId="incomeChart"
    title="Income Trends"
    subtitle="Last 30 days"
    height="350px"
    :chartData="[
        'labels' => ['Mon', 'Tue', 'Wed'],
        'datasets' => [...]
    ]"
    :chartOptions="[
        'responsive' => true,
        'maintainAspectRatio' => false
    ]"
/>
```

**Parameters:**
- `chartId` (string) - Unique canvas ID
- `title` (string) - Card heading
- `subtitle` (string|null) - Optional description
- `height` (string) - CSS height value (default: 400px)
- `chartData` (array) - Chart.js data configuration
- `chartOptions` (array) - Chart.js options configuration

**Chart Types:**
- `line` - Trend lines
- `bar` - Comparisons
- `doughnut` - Distributions
- `mixed` - Combined line + bar

---

### Category Card
List items with horizontal progress bars

```blade
<x-dashboard.category-card
    title="Expenses by Category"
    :items="$expenseByCategory"
    countLabel="transactions"
    barColor="red"
    maxValue="10000"
    currencySymbol="$"
    emptyMessage="No expenses found"
/>
```

**Item Structure:**
```php
[
    'category' => 'Materials',
    'total' => 2500.50,
    'count' => 12
]
```

**Parameters:**
- `title` (string) - Card heading
- `items` (array) - Array of category items
- `countLabel` (string) - Plural label for count
- `barColor` (string) - Tailwind color: blue|green|red|yellow
- `maxValue` (float) - Maximum bar width reference
- `currencySymbol` (string) - Display prefix (default: $)
- `emptyMessage` (string) - Message when no items

---

### Transaction List
Recent transactions with status badges

```blade
<x-dashboard.transaction-list
    title="Recent Incomes"
    :items="$recentIncomes"
    dateField="received_at"
    amountField="amount_received"
    emptyMessage="No transactions"
/>
```

**Item Structure (Income):**
```php
$item->project->name
$item->received_at
$item->amount_received
$item->payment_status
```

**Item Structure (Expense):**
```php
$item->client->name
$item->date
$item->amount
$item->status
```

**Parameters:**
- `title` (string) - Card heading
- `items` (Collection) - Transaction models
- `dateField` (string) - Date property name
- `amountField` (string) - Amount property name
- `currencySymbol` (string) - Display prefix (default: $)
- `emptyMessage` (string) - Message when no items

**Status Colors:**
- Paid → Green
- Pending → Yellow
- Overdue → Red
- Other → Gray

---

## 📊 Service Methods

### DashboardStatsService

```php
$service = app(DashboardStatsService::class);
```

#### Daily Stats
```php
$dailyStats = $service->getDailyStats(30);
// Returns: Array of 30 days with income, expense, balance
```

#### Category Analysis
```php
$byProject = $service->getIncomeByCategory($startDate, $endDate);
$byCategory = $service->getExpenseByCategory($startDate, $endDate);
$byMethod = $service->getExpenseByMethod($startDate, $endDate);
```

#### Trends
```php
$weeklyStats = $service->getWeeklyStats(12);
$cashFlowAnalysis = $service->getCashFlowAnalysis(6);
```

#### Financial Summary
```php
$summary = $service->getFinancialSummary();
// $summary['today']['income']
// $summary['this_month']['balance']
// $summary['this_year']['expense']
// $summary['all_time']['income']
```

#### Project Analysis
```php
$topProjects = $service->getTopProjects(5);
// Returns: [id, name, income, target, completion_percent]
```

#### Payment Analysis
```php
$breakdown = $service->getPaymentStatusBreakdown();
$outstanding = $service->getOutstandingReceivables();
```

#### Quick Stats
```php
$kpis = $service->getQuickStats();
// $kpis['today_income']
// $kpis['today_expense']
// $kpis['month_income']
// $kpis['month_expense']
// $kpis['outstanding']
// $kpis['total_transactions']
```

---

## 🎨 Color Palette

```css
/* Status Colors */
.status-paid      { background: rgba(34, 197, 94, 0.8); }    /* Green */
.status-pending   { background: rgba(249, 115, 22, 0.8); }   /* Orange */
.status-overdue   { background: rgba(239, 68, 68, 0.8); }    /* Red */
.status-unknown   { background: rgba(107, 114, 128, 0.8); }  /* Gray */

/* Border Colors */
.border-green     { border-left-color: #10b981; }
.border-red       { border-left-color: #ef4444; }
.border-blue      { border-left-color: #3b82f6; }
.border-orange    { border-left-color: #f97316; }
.border-purple    { border-left-color: #8b5cf6; }
.border-indigo    { border-left-color: #6366f1; }
```

---

## 📈 Chart.js Examples

### Line Chart (Income/Expense Trend)
```php
$chartData = [
    'labels' => ['Mon', 'Tue', 'Wed', 'Thu', 'Fri'],
    'datasets' => [
        [
            'label' => 'Income',
            'data' => [1000, 1500, 1200, 1800, 2000],
            'borderColor' => '#10b981',
            'backgroundColor' => 'rgba(16, 185, 129, 0.1)',
            'borderWidth' => 2,
            'fill' => true,
            'tension' => 0.4,
        ],
        [
            'label' => 'Expense',
            'data' => [400, 350, 500, 420, 380],
            'borderColor' => '#ef4444',
            'backgroundColor' => 'rgba(239, 68, 68, 0.1)',
            'borderWidth' => 2,
            'fill' => true,
            'tension' => 0.4,
        ]
    ]
];

$chartOptions = [
    'responsive' => true,
    'maintainAspectRatio' => false,
    'plugins' => ['legend' => ['position' => 'top']],
    'scales' => ['y' => ['beginAtZero' => true]]
];
```

### Bar Chart (Weekly Comparison)
```php
$chartData = [
    'labels' => ['Week 1', 'Week 2', 'Week 3'],
    'datasets' => [
        [
            'label' => 'Revenue',
            'data' => [5000, 6200, 5800],
            'backgroundColor' => 'rgba(16, 185, 129, 0.7)',
        ]
    ]
];

$chartOptions = [
    'indexAxis' => 'y',
    'responsive' => true,
    'scales' => ['x' => ['beginAtZero' => true]]
];
```

### Doughnut Chart (Status Distribution)
```php
$chartData = [
    'labels' => ['Paid', 'Pending', 'Overdue'],
    'datasets' => [
        [
            'data' => [45, 30, 25],
            'backgroundColor' => [
                'rgba(34, 197, 94, 0.8)',
                'rgba(249, 115, 22, 0.8)',
                'rgba(239, 68, 68, 0.8)',
            ]
        ]
    ]
];

$chartOptions = [
    'responsive' => true,
    'plugins' => ['legend' => ['position' => 'bottom']]
];
```

---

## 🧪 Testing Examples

```php
// Test a service method
public function test_it_calculates_daily_stats()
{
    Income::factory()->create([
        'received_at' => now(),
        'amount_received' => 1000
    ]);
    
    $stats = $this->service->getDailyStats(30);
    
    $this->assertCount(30, $stats);
    $this->assertEquals(1000, $stats[29]['income']);
}

// Test card rendering
public function test_stat_card_renders()
{
    $view = view('components.dashboard.stat-card', [
        'title' => 'Test',
        'value' => '$100',
        'icon' => '📊'
    ]);
    
    $this->assertStringContainsString('Test', $view);
    $this->assertStringContainsString('$100', $view);
}
```

---

## 🚀 Common Patterns

### Daily Dashboard Summary
```blade
<div class="grid grid-cols-4 gap-6">
    <x-dashboard.stat-card title="Today Income" value="{{ $kpis['today_income'] }}" />
    <x-dashboard.stat-card title="Today Expense" value="{{ $kpis['today_expense'] }}" />
    <x-dashboard.stat-card title="Month Income" value="{{ $kpis['month_income'] }}" />
    <x-dashboard.stat-card title="Outstanding" value="{{ $kpis['outstanding'] }}" />
</div>
```

### Period Comparison
```blade
<div class="grid grid-cols-3 gap-6">
    @foreach(['today', 'this_month', 'this_year'] as $period)
        <div class="bg-white p-6 rounded-lg">
            <h3>{{ ucfirst(str_replace('_', ' ', $period)) }}</h3>
            <div class="flex justify-between">
                <div>Income: ${{ $summary[$period]['income'] }}</div>
                <div>Expense: ${{ $summary[$period]['expense'] }}</div>
            </div>
        </div>
    @endforeach
</div>
```

### Full Dashboard Layout
```blade
<div class="space-y-8">
    <!-- Quick Stats -->
    <div class="grid grid-cols-4 gap-6">...</div>
    
    <!-- Charts -->
    <div class="grid grid-cols-2 gap-6">...</div>
    
    <!-- Category Breakdown -->
    <div class="grid grid-cols-2 gap-6">...</div>
    
    <!-- Recent Transactions -->
    <div class="grid grid-cols-3 gap-6">...</div>
</div>
```

---

## 📚 File References

- **Service:** `app/Services/DashboardStatsService.php`
- **Components:** `resources/views/components/dashboard/*.blade.php`
- **Views:** `resources/views/dashboard/*.blade.php`
- **Tests:** `tests/Unit/DashboardStatsServiceTest.php`
- **Controller:** `app/Http/Controllers/DashboardController.php`

---

## 🎯 Best Practices

1. **Always use the service** - Never write dashboard queries directly in controller
2. **Reuse components** - Don't duplicate card markup
3. **Pass data as arrays** - Components expect array structures
4. **Test all methods** - Use the test template as guide
5. **Keep charts responsive** - Always set `maintainAspectRatio: false`
6. **Use semantic dates** - Always convert to Carbon instances
7. **Handle empty states** - Every list needs an empty message
8. **Cache when needed** - For heavy queries, consider caching results

---

**Last Updated:** October 30, 2025  
**Version:** 2.0 - Dashboard Enhancement Complete
