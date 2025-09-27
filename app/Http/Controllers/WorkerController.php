<?php


namespace App\Http\Controllers;


use App\Models\Worker;
use Illuminate\Http\Request;
use Illuminate\Routing\Redirector;


class WorkerController extends Controller
{
// Display a listing of workers
public function index()
{
$workers = Worker::orderBy('last_name')->paginate(15);
return view('workers.index', compact('workers'));
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


$worker = Worker::create($data);


return redirect()->route('workers.show', $worker)->with('success', 'Worker created.');
}


// Display the specified worker
public function show(Worker $worker)
{
return view('workers.show', compact('worker'));
}


// Show the form for editing the specified worker
public function edit(Worker $worker)
{
return view('workers.edit', compact('worker'));
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
}

