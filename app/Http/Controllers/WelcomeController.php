<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class WelcomeController extends Controller
{
    /**
     * Show the welcome page for new users with no permissions
     */
    public function index()
    {
        $user = Auth::user();
        
        // If user has any significant permissions, redirect to dashboard
        if ($user && ($user->hasRole(['admin', 'manager', 'accountant']) || $user->hasAnyPermission(['projects.create', 'expenses.create', 'users.view']))) {
            return redirect()->route('dashboard');
        }
        
        return view('welcome-user', compact('user'));
    }
    
    /**
     * Show contact form for access requests
     */
    public function requestAccess()
    {
        return view('welcome-user.request-access');
    }
    
    /**
     * Handle access request submission
     */
    public function submitAccessRequest(Request $request)
    {
        $request->validate([
            'message' => 'required|string|max:1000',
            'requested_role' => 'required|in:manager,accountant',
            'business_justification' => 'required|string|max:500'
        ]);
        
        // Here you could send an email to admins, create a ticket, etc.
        // For now, we'll just show a success message
        
        return back()->with('success', 'Your access request has been submitted. An administrator will review your request and contact you soon.');
    }
}