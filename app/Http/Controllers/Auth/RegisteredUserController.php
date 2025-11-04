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
use Spatie\Permission\Models\Permission;

class RegisteredUserController extends Controller
{
    /**
     * Display the registration view.
     */
    public function create(): View
    {
        // Define available roles (matching migration and seeder)
        $roles = ['admin', 'manager', 'accountant', 'user'];
        return view('auth.register', compact('roles'));
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
            'role' => ['nullable', 'string', 'in:admin,manager,accountant,user'],
        ]);

        $user = User::create([
            'name' => $request->name,
            'email' => $request->email,
            'password' => Hash::make($request->password),
            'role' => $request->input('role', 'user'), // Set role column with default 'user'
        ]);

        // Assign selected role if provided using Spatie
        if ($request->filled('role')) {
            try {
                $user->assignRole($request->input('role'));
                
                // If admin role, ensure they have ALL permissions
                if ($request->input('role') === 'admin') {
                    $this->ensureAdminFullPermissions($user);
                }
            } catch (\Throwable $e) {
                // If assignment fails, log and continue (user exists)
                report($e);
            }
        } else {
            // Assign default 'user' role if no role selected
            $user->assignRole('user');
        }

        event(new Registered($user));

        Auth::login($user);

        return redirect(route('dashboard', absolute: false));
    }

    /**
     * Ensure admin user has all available permissions
     */
    private function ensureAdminFullPermissions(User $user): void
    {
        try {
            // Get the admin role
            $adminRole = Role::firstOrCreate(['name' => 'admin']);
            
            // Get all available permissions
            $allPermissions = Permission::all();
            
            // If no permissions exist yet, create the basic ones
            if ($allPermissions->isEmpty()) {
                $this->createBasicPermissions();
                $allPermissions = Permission::all();
            }
            
            // Sync all permissions to admin role
            $adminRole->syncPermissions($allPermissions->pluck('name'));
            
            // Ensure user has admin role
            if (!$user->hasRole('admin')) {
                $user->assignRole('admin');
            }
            
            // Clear permission cache
            app()[\Spatie\Permission\PermissionRegistrar::class]->forgetCachedPermissions();
            
        } catch (\Throwable $e) {
            // Log but don't fail registration
            report($e);
        }
    }

    /**
     * Create basic permissions if none exist
     */
    private function createBasicPermissions(): void
    {
        $basicPermissions = [
            'users.view', 'users.create', 'users.edit', 'users.delete',
            'projects.view', 'projects.create', 'projects.edit', 'projects.delete',
            'expenses.view', 'expenses.create', 'expenses.edit', 'expenses.delete',
            'incomes.view', 'incomes.create', 'incomes.edit', 'incomes.delete',
            'payments.view', 'payments.create', 'payments.edit', 'payments.delete',
            'reports.view', 'reports.generate', 'reports.export',
            'employees.view', 'employees.create', 'employees.edit', 'employees.delete',
            'workers.view', 'workers.create', 'workers.edit', 'workers.delete',
            'orders.view', 'orders.create', 'orders.edit', 'orders.delete',
            'settings.view', 'settings.edit',
        ];

        foreach ($basicPermissions as $permission) {
            Permission::firstOrCreate(['name' => $permission]);
        }
    }
}
