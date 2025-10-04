<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Worker;
use App\Models\Task;
use Illuminate\Support\Facades\DB;

class ReportController extends Controller
{
    public function index(Request $request)
    {
        // Optional: filter by date
        $date = $request->input('date', now()->toDateString());

        // Total workers
        $totalWorkers = Worker::count();

        // Fetch tasks for the day with worker info
        $tasks = Task::with('worker')
            ->where('date', $date)
            ->get();

        // Total daily wages
        $totalDailyWages = $tasks->sum('daily_wage');

        return view('reports.index', compact(
            'tasks', 'totalWorkers', 'totalDailyWages', 'date'
        ));
    }
}
