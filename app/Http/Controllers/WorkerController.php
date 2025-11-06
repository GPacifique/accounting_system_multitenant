<?php


namespace App\Http\Controllers;


use App\Models\Worker;
use App\Traits\Downloadable;
use Illuminate\Http\Request;
use Illuminate\Routing\Redirector;
use Illuminate\Support\Facades\Auth;


class WorkerController extends Controller
{
    use Downloadable;

// Display a listing of workers
public function index(Request $request)
{
	$q = trim((string) $request->get('q'));

	$workersQuery = Worker::query();
	if ($q !== '') {
		$workersQuery->where(function ($w) use ($q) {
			$w->where('first_name', 'like', "%{$q}%")
			  ->orWhere('last_name', 'like', "%{$q}%")
			  ->orWhere('email', 'like', "%{$q}%")
			  ->orWhere('phone', 'like', "%{$q}%")
			  ->orWhere('position', 'like', "%{$q}%");
		});
	}
	$workers = $workersQuery->orderBy('last_name')->paginate(15)->appends($request->query());

	// Daily payments history from first payment date to today
	$firstPaymentDate = \App\Models\WorkerPayment::min('paid_on');
	$payments = \App\Models\WorkerPayment::with('worker')
		->when($q !== '', function ($pq) use ($q) {
			$pq->whereHas('worker', function ($w) use ($q) {
				$w->where('first_name', 'like', "%{$q}%")
				  ->orWhere('last_name', 'like', "%{$q}%");
			});
		})
		->orderByDesc('paid_on')
		->orderByDesc('id')
		->paginate(25)
		->appends($request->query());

	return view('workers.index', compact('workers', 'payments', 'firstPaymentDate'));
}


// Show the form for creating a new worker
public function create()
{
return view('workers.create');
}


// Store a newly created worker
public function store(Request $request)
{
$data = $request->validate([
'first_name' => 'required|string|max:100',
'last_name' => 'required|string|max:100',
'email' => 'nullable|email|unique:workers,email',
'phone' => 'nullable|string|max:30',
'position' => 'nullable|string|max:100',
'salary' => 'nullable|numeric|min:0',
'currency' => 'nullable|string|size:3',
'hired_at' => 'nullable|date',
'status' => 'nullable|string|max:50',
'notes' => 'nullable|string',
]);
if (isset($data['salary'])) {
$data['salary_cents'] = (int) round($data['salary'] * 100);
unset($data['salary']);
}

$data = $this->ensureTenantId($data);
$worker = Worker::create($data);


return redirect()->route('workers.show', $worker)->with('success', 'Worker created.');
}


// Display the specified worker
public function show(Worker $worker)
{
	// eager load recent payments
	$worker->load(['payments' => function($q){ $q->orderByDesc('paid_on')->limit(30); }]);
	return view('workers.show', compact('worker'));
}


// Show the form for editing the specified worker
public function edit(Worker $worker)
{
return view('workers.edit', compact('worker'));
}

// BULK store daily payments for selected workers
public function bulkStorePayments(Request $request)
{
    // Check permission for worker payments
    if (!Auth::user()->can('workers.payments')) {
        abort(403, 'You do not have permission to process worker payments.');
    }
    
	$data = $request->validate([
		'paid_on' => 'required|date',
		'worker_ids' => 'required|array',
		'worker_ids.*' => 'exists:workers,id',
		'amounts' => 'required|array',
	]);

	$paidOn = $data['paid_on'];
	$workerIds = $data['worker_ids'];
	$amounts = $request->input('amounts', []);

	$created = 0; $updated = 0;
	foreach ($workerIds as $wid) {
		$amount = (float) ($amounts[$wid] ?? 0);
		if ($amount <= 0) { continue; }

		// upsert by (worker_id, paid_on)
		$existing = \App\Models\WorkerPayment::where('worker_id', $wid)
			->whereDate('paid_on', $paidOn)
			->first();

		if ($existing) {
			$existing->update(['amount' => $amount]);
			$updated++;
		} else {
			\App\Models\WorkerPayment::create([
				'worker_id' => $wid,
				'paid_on' => $paidOn,
				'amount' => $amount,
			]);
			$created++;
		}
	}

	return redirect()->route('workers.index')
		->with('success', "Payments saved: {$created} new, {$updated} updated.");
}
// Update the specified worker
public function update(Request $request, Worker $worker)
{
$data = $request->validate([
'first_name' => 'required|string|max:100',
'last_name' => 'required|string|max:100',
'email' => 'nullable|email|unique:workers,email,' . $worker->id,
'phone' => 'nullable|string|max:30',
'position' => 'nullable|string|max:100',
'salary' => 'nullable|numeric|min:0',
'currency' => 'nullable|string|size:3',
'hired_at' => 'nullable|date',
'status' => 'nullable|string|max:50',
'notes' => 'nullable|string',
]);


if (isset($data['salary'])) {
$data['salary_cents'] = (int) round($data['salary'] * 100);
unset($data['salary']);
}


$worker->update($data);


return redirect()->route('workers.show', $worker)->with('success', 'Worker updated.');
}


// Remove the specified worker
public function destroy(Worker $worker)
{
$worker->delete();
return redirect()->route('workers.index')->with('success', 'Worker deleted.');
}

/**
 * Export workers as CSV
 */
public function exportCsv(Request $request)
{
    // Check permission for worker export
    if (!Auth::user()->can('workers.export')) {
        abort(403, 'You do not have permission to export workers.');
    }
    
    $filename = $request->get('filename', 'workers');
    
    $workers = Worker::latest()->get();
    
    $headers = [
        'id' => 'ID',
        'name' => 'Name',
        'position' => 'Position',
        'contact' => 'Contact',
        'status' => 'Status',
        'daily_rate' => 'Daily Rate (RWF)',
        'created_at' => 'Hired Date'
    ];
    
    // Transform data for CSV
    $csvData = $workers->map(function ($worker) {
        return [
            'id' => $worker->id,
            'name' => $worker->name ?? 'N/A',
            'position' => $worker->position ?? 'N/A',
            'contact' => $worker->contact ?? 'N/A',
            'status' => ucfirst($worker->status ?? 'active'),
            'daily_rate' => $worker->daily_rate ?? 0,
            'created_at' => $worker->created_at->format('Y-m-d H:i:s')
        ];
    });
    
    return $this->downloadCsv($csvData, $filename, array_keys($headers));
}

/**
 * Export workers as PDF
 */
public function exportPdf(Request $request)
{
    // Check permission for worker export
    if (!Auth::user()->can('workers.export')) {
        abort(403, 'You do not have permission to export workers.');
    }
    
    $filename = $request->get('filename', 'workers');
    
    $workers = Worker::latest()->get();
    
    $html = $this->generatePdfHtml('exports.workers-pdf', [
        'data' => $workers,
        'title' => 'Workers Report',
        'subtitle' => 'Complete list of all workers',
        'totalRecords' => $workers->count()
    ]);
    
    return $this->downloadPdf($html, $filename);
}
}

