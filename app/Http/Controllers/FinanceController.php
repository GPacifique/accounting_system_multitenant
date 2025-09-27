<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

class FinanceController extends Controller
{
    public function __construct()
    {
        // Only accountants (or admins if you want) can access this controller
        $this->middleware('role:accountant|admin');
    }

    public function index()
    {
        // Example: finance dashboard
        return view('finance.index'); 
    }

    public function reports()
    {
        // Example: financial reports
        return view('finance.reports');
    }

    public function transactions()
    {
        // Example: list of transactions
        return view('finance.transactions');
    }
}
