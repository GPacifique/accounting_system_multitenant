use Carbon\Carbon;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;
use App\Models\Worker;
use App\Models\Payment;
use App\Models\Transaction;
use App\Models\Income;
use App\Models\Expense;
use App\Models\Project;

public function index()
{
    $today = Carbon::today();
    $startOfMonth = $today->copy()->startOfMonth();

    // Workers
    $totalWorkers = Schema::hasTable('workers') ? Worker::count() : 0;
    $activeWorkers = Schema::hasTable('workers') && Schema::hasColumn('workers', 'status')
        ? Worker::where('status','active')->count()
        : $totalWorkers;
    $recentWorkers = Schema::hasTable('workers') ? Worker::latest()->limit(6)->get() : collect();

    // Payments
    $paymentsTotal = Schema::hasTable('payments') && Schema::hasColumn('payments','amount')
        ? Payment::sum('amount')
        : 0;
    $paymentsThisMonth = Schema::hasTable('payments') && Schema::hasColumn('payments','amount')
        ? Payment::whereBetween('created_at', [$startOfMonth, $today->endOfDay()])->sum('amount')
        : 0;
    $recentPayments = Schema::hasTable('payments') ? Payment::latest()->limit(7)->get() : collect();

    // Transactions
    $recentTransactions = Schema::hasTable('transactions') ? Transaction::latest()->limit(7)->get() : collect();
    $transactionsThisMonth = Schema::hasTable('transactions') && Schema::hasColumn('transactions','amount')
        ? Transaction::whereBetween('created_at', [$startOfMonth, $today->endOfDay()])->sum('amount')
        : 0;

    // Incomes
    $incomesTotal = Schema::hasTable('incomes') && Schema::hasColumn('incomes','amount_received')
        ? Income::sum('amount_received')
        : 0;
    $incomesThisMonth = Schema::hasTable('incomes') && Schema::hasColumn('incomes','amount_received')
        ? Income::whereBetween('received_at', [$startOfMonth, $today->endOfDay()])->sum('amount_received')
        : 0;
    $recentIncomes = Schema::hasTable('incomes') ? Income::latest()->limit(7)->get() : collect();

    // Expenses
    $expensesTotal = Schema::hasTable('expenses') && Schema::hasColumn('expenses','amount')
        ? Expense::sum('amount')
        : 0;
    $expensesThisMonth = Schema::hasTable('expenses') && Schema::hasColumn('expenses','amount')
        ? Expense::whereBetween('created_at', [$startOfMonth, $today->endOfDay()])->sum('amount')
        : 0;
    $recentExpenses = Schema::hasTable('expenses') ? Expense::latest()->limit(7)->get() : collect();

    // Projects
    $projectsCount = Schema::hasTable('projects') ? Project::count() : 0;
    $projectsThisMonth = Schema::hasTable('projects') ? Project::whereBetween('created_at', [$startOfMonth, $today->endOfDay()])->count() : 0;
    $projectsTotal = Schema::hasTable('projects') && Schema::hasColumn('projects','contract_value')
        ? Project::sum('contract_value')
        : null;
    $recentProjects = Schema::hasTable('projects') ? Project::latest()->limit(7)->get() : collect();

    // Project Stats — important to avoid undefined variable
    $projectStats = collect();
    if(Schema::hasTable('projects') && Schema::hasTable('incomes')) {
        $projectStats = DB::table('projects')
            ->leftJoin('incomes', 'projects.id', '=', 'incomes.project_id')
            ->select(
                'projects.name as project_name',
                DB::raw('COALESCE(SUM(incomes.amount_received),0) as amount_paid'),
                DB::raw('projects.contract_value as total_amount'),
                DB::raw('(projects.contract_value - COALESCE(SUM(incomes.amount_received),0)) as amount_remaining')
            )
            ->groupBy('projects.id', 'projects.name', 'projects.contract_value')
            ->get();
    }

    // Monthly series for last 6 months
    $months = [];
    $paymentsMonthly = [];
    $expensesMonthly = [];
    $incomeMonthly = [];
    for ($i = 5; $i >= 0; $i--) {
        $dt = Carbon::now()->subMonths($i);
        $months[] = $dt->format('M Y');

        $mStart = $dt->copy()->startOfMonth();
        $mEnd = $dt->copy()->endOfMonth();

        $paymentsMonthly[] = Schema::hasTable('payments') ? Payment::whereBetween('created_at', [$mStart, $mEnd])->sum('amount') : 0;
        $expensesMonthly[] = Schema::hasTable('expenses') ? Expense::whereBetween('created_at', [$mStart, $mEnd])->sum('amount') : 0;
        $incomeMonthly[] = Schema::hasTable('incomes') ? Income::whereBetween('received_at', [$mStart, $mEnd])->sum('amount_received') : 0;
    }

    return view('dashboard', compact(
        'totalWorkers','activeWorkers','recentWorkers',
        'paymentsTotal','paymentsThisMonth','recentPayments',
        'recentTransactions','transactionsThisMonth',
        'incomesTotal','incomesThisMonth','recentIncomes',
        'expensesTotal','expensesThisMonth','recentExpenses',
        'projectsCount','projectsThisMonth','projectsTotal','recentProjects',
        'projectStats','months','paymentsMonthly','expensesMonthly','incomeMonthly'
    ));
}
