<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

class TransactionController extends Controller
{
    public function index(Request $request)
    {
        // Temporary: return a string while you build the view
        // return 'transactions.index reachable';

        // If you have a Transaction model: $transactions = \App\Models\Transaction::latest()->paginate(15);
        // return view('transactions.index', compact('transactions'));

        // Quick view fallback:
        return view('transactions.index');
    }

    // Optional useful stubs:
    public function create() { return view('transactions.create'); }
    public function store(Request $r) { return redirect()->route('transactions.index'); }
    public function show($id) { return view('transactions.show', compact('id')); }
    public function edit($id) { return view('transactions.edit', compact('id')); }
    public function update(Request $r, $id) { return redirect()->route('transactions.index'); }
    public function destroy($id) { return redirect()->route('transactions.index'); }
}
