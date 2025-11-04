<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class RoleSwitcherController extends Controller
{
    /**
     * Switch the active role view for the current user
     */
    public function switch(Request $request, string $role)
    {
        $user = Auth::user();
        
        // Validate that the user has the requested role
        if (!$user->hasRole($role)) {
            return redirect()->back()->with('error', 'You do not have access to this role.');
        }

        // Store the active role in session
        session(['active_role' => $role]);

        return redirect()->route('dashboard')->with('success', "Switched to {$role} view");
    }

    /**
     * Clear active role (return to default multi-role view)
     */
    public function clear(Request $request)
    {
        session()->forget('active_role');
        
        return redirect()->route('dashboard')->with('success', 'Returned to default view');
    }

    /**
     * Get the current active role from session
     */
    public static function getActiveRole()
    {
        return session('active_role');
    }
}
