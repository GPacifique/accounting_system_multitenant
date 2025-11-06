<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use App\Models\User;
use Illuminate\Auth\Events\Registered;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Validation\Rules;
use Illuminate\View\View;
use Spatie\Permission\Models\Role;
use App\Notifications\UserRegistered;
use Illuminate\Support\Facades\Notification;

class RegisteredUserController extends Controller
{
    /**
     * Display the registration view.
     */
    public function create(): View
    {
        return view('auth.register');
    }

    /**
     * Handle an incoming registration request.
     *
     * @throws \Illuminate\Validation\ValidationException
     */
    public function store(Request $request): RedirectResponse
    {
        $request->validate([
            'name' => ['required', 'string', 'max:255'],
            'email' => ['required', 'string', 'lowercase', 'email', 'max:255', 'unique:'.User::class],
            'password' => ['required', 'confirmed', Rules\Password::defaults()],
        ]);

        $user = User::create([
            'name' => $request->name,
            'email' => $request->email,
            'password' => Hash::make($request->password),
            'role' => 'user', // All new registrations default to 'user' role
        ]);

        // Assign default 'user' role using Spatie
        $user->assignRole('user');

        event(new Registered($user));

        // Check if there's a current tenant context
        $currentTenant = app()->bound('currentTenant') ? app('currentTenant') : null;
        
        if ($currentTenant) {
            // Add user to the current tenant with basic user role
            $user->addToTenant($currentTenant->id, 'user', false);
            
            // Send welcome email for this tenant
            if (config('notifications.send_welcome_email', true)) {
                $user->notify(new UserRegistered($user));
            }
            
            // Notify tenant admins about the new registration
            if (config('notifications.notify_admins_new_user', true)) {
                $tenantAdmins = $currentTenant->users()->wherePivot('is_admin', true)->get();
                if ($tenantAdmins->isNotEmpty()) {
                    Notification::send($tenantAdmins, new UserRegistered($user));
                }
            }
        } else {
            // No tenant context - this might be a super admin or system-level registration
            // Send welcome email to the new user (if enabled)
            if (config('notifications.send_welcome_email', true)) {
                $user->notify(new UserRegistered($user));
            }
        }

        Auth::login($user);

        // Redirect based on tenant context
        if ($currentTenant) {
            return redirect()->route('dashboard');
        } else {
            // No tenant context - redirect to tenant selection or welcome page
            return redirect()->route('welcome.index');
        }
    }
}
