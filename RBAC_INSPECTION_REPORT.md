# Role-Based Access Control (RBAC) System Inspection Report

**Project:** SiteLedger  
**Date:** October 30, 2025  
**System:** Laravel with Spatie Permission Package

---

## Executive Summary

Your application implements a **robust RBAC system** using the **Spatie Laravel Permission** package (v6.21). The system supports multiple roles (**Admin**, **Manager**, **Accountant**) with granular permission controls. However, there are **implementation gaps** and **recommendations** for improvement.

---

## 1. Current RBAC Architecture

### 1.1 Permission Package Implementation
- **Package:** `spatie/laravel-permission: ^6.21`
- **Configuration:** `/config/permission.php` (fully configured)
- **Models:** Uses Spatie's permission models for roles and permissions

### 1.2 Defined Roles

#### Roles in the System
From `/database/seeders/RoleSeeder.php`:

| Role | Label | Status |
|------|-------|--------|
| **admin** | Administrator | ✅ Defined |
| **accountant** | Accountant | ✅ Defined |
| **manager** | Manager | ✅ Defined |

#### Permissions Matrix
From `/database/seeders/RolePermissionSeeder.php`:

| Permission | Admin | Manager | User |
|------------|-------|---------|------|
| users.view | ✅ | ❌ | ❌ |
| users.create | ✅ | ❌ | ❌ |
| users.edit | ✅ | ❌ | ❌ |
| users.delete | ✅ | ❌ | ❌ |
| projects.view | ✅ | ✅ | ✅ |
| projects.create | ✅ | ✅ | ❌ |
| projects.edit | ✅ | ✅ | ❌ |
| projects.delete | ✅ | ❌ | ❌ |
| expenses.view | ✅ | ✅ | ❌ |
| expenses.create | ✅ | ✅ | ❌ |
| expenses.edit | ✅ | ✅ | ❌ |
| expenses.delete | ✅ | ❌ | ❌ |
| reports.view | ✅ | ✅ | ❌ |
| reports.generate | ✅ | ✅ | ❌ |

**Issue:** The seeder defines permissions for **admin**, **manager**, and **user**, but **NOT** for **accountant** role!

---

## 2. Implementation Review

### 2.1 User Model
**File:** `/app/Models/User.php`

```php
use Spatie\Permission\Traits\HasRoles;

class User extends Authenticatable
{
    use HasFactory, Notifiable, HasRoles;
    protected $guard_name = 'web';
}
```

✅ **Status:** Correctly implements `HasRoles` trait with proper guard name.

### 2.2 Role Model
**File:** `/app/Models/Role.php`

```php
class Role extends Model
{
    protected $fillable = ['name', 'label'];
    
    public function users(): BelongsToMany
    {
        return $this->belongsToMany(User::class)->withTimestamps();
    }
}
```

⚠️ **Issue:** Custom Role model exists but conflicts with Spatie's Role model. The `RolePermissionSeeder.php` uses `Spatie\Permission\Models\Role`, not the custom one.

### 2.3 Middleware Setup
**File:** `/app/Http/Kernel.php`

```php
'role' => \Spatie\Permission\Middleware\RoleMiddleware::class,
'permission' => \Spatie\Permission\Middleware\PermissionMiddleware::class,
'role_or_permission' => \Spatie\Permission\Middleware\RoleOrPermissionMiddleware::class,
'custom.role' => \App\Http\Middleware\RoleMiddleware::class,
```

✅ **Status:** All Spatie middleware properly registered.

**Custom Middleware Issue:**
```php
// app/Http/Middleware/RoleMiddleware.php
public function handle(Request $request, Closure $next, string $role): Response
{
    if (! $request->user() || $request->user()->role !== $role) {
        abort(403, 'Unauthorized');
    }
    return $next($request);
}
```

❌ **Critical Issue:** Custom middleware checks for `$user->role` column (doesn't exist). Should use Spatie's role relationship instead.

### 2.4 Route Protection
**File:** `/routes/web.php`

```php
Route::middleware(['role:admin'])->group(function () {
    Route::resource('users', UserController::class);
});
```

✅ **Status:** Correctly uses `role:admin` middleware for user management.

**However, other resources lack role restrictions:**
- Projects, Expenses, Payments, Orders, Incomes - **NO role checks**
- Reports - Only checks `['auth', 'verified']`
- Clients, Workers - **NO role checks**

---

## 3. Controller Implementation

### 3.1 User Controller
**File:** `/app/Http/Controllers/UserController.php`

✅ **Good:**
- Creates users with role assignment
- Validates roles against database
- Properly uses `assignRole()` and `syncRoles()`

### 3.2 Role Controller
**File:** `/app/Http/Controllers/RoleController.php`

✅ **Good:**
- Full CRUD operations for roles
- Properly syncs permissions to roles
- Validates permissions array

### 3.3 Permission Controller
**File:** `/app/Http/Controllers/PermissionController.php`

✅ **Status:** Implements permission management (list, create, edit, delete).

### 3.4 Other Controllers (Dashboard, Orders, Settings, etc.)

- **SettingController:** Correctly enforces `['auth', 'role:admin']`
- **OrderController:** No role enforcement (TODO)
- **DashboardController:** Returns same view for all authenticated users (role-agnostic)

---

## 4. Database Schema

### 4.1 Spatie Permission Tables
From `/config/permission.php`:

| Table Name | Purpose |
|------------|---------|
| `roles` | Stores role definitions |
| `permissions` | Stores permission definitions |
| `model_has_roles` | Maps users to roles |
| `model_has_permissions` | Maps users to permissions directly |
| `role_has_permissions` | Maps roles to permissions |

✅ **Status:** All tables configured in migration `/database/migrations/2025_09_25_114306_create_permission_tables.php`.

### 4.2 Users Table
The custom `Role` model suggests a previous design with a `role` column. This **conflicts** with Spatie's design.

---

## 5. Issues & Vulnerabilities

### 🔴 Critical Issues

1. **Incomplete Permission Seeding**
   - **Accountant role has NO permissions** (defined in seeder but not assigned any permissions)
   - **No finance/accounting-specific permissions** (e.g., `incomes.view`, `expenses.view`, `payments.view`)

2. **Custom Middleware Conflict**
   - `/app/Http/Middleware/RoleMiddleware.php` assumes `$user->role` column
   - Should use Spatie's `HasRoles` trait methods instead
   - Will cause runtime errors

3. **Unprotected Routes**
   - Most resources lack role/permission middleware
   - Anyone authenticated can access Projects, Orders, Expenses, Payments, Incomes, Workers, Clients
   - Only Users and Settings are properly protected (admin-only)

4. **Role Model Confusion**
   - Custom `/app/Models/Role.php` conflicts with Spatie's role model
   - Seeders use Spatie's model, controllers don't
   - Creates maintainability issues

### 🟡 Medium Issues

5. **No Role-Based Dashboard**
   - Dashboard shows same data for all authenticated users
   - Should filter content based on role
   - Manager should see only their projects
   - Accountant should see financial summaries

6. **No Permission Checks in Views**
   - Blade templates don't use `@role()`, `@can()` directives
   - Users can see UI elements they can't access
   - Creates confusion and poor UX

7. **Dashboard Controller Not Role-Aware**
   - Should filter data based on user's role:
     - **Admin:** All data
     - **Manager:** Only their projects
     - **Accountant:** Financial data only

8. **Incomplete Accountant Role**
   - No specific financial permissions
   - No access restrictions for accounting-related resources

### 🟢 What's Working Well

✅ Core Spatie integration is correct  
✅ User role assignment/management works  
✅ Role and permission controllers are functional  
✅ Database schema properly configured  

---

## 6. Recommendations

### Phase 1: Fix Critical Issues (URGENT)

#### 1.1 Remove/Fix Custom Role Middleware
**File:** `/app/Http/Middleware/RoleMiddleware.php`

**Option A - Remove it (Recommended):**
```bash
rm /app/Http/Middleware/RoleMiddleware.php
```
Update `/app/Http/Kernel.php`:
```php
// Remove this line:
// 'custom.role' => \App\Http\Middleware\RoleMiddleware::class,
```

**Option B - Fix it:**
```php
<?php
namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;

class RoleMiddleware
{
    public function handle(Request $request, Closure $next, string $role): Response
    {
        if (!$request->user() || !$request->user()->hasRole($role)) {
            abort(403, 'Unauthorized access');
        }
        return $next($request);
    }
}
```

#### 1.2 Consolidate Role Model
**Choose ONE approach:**

**Option A (Recommended):** Use ONLY Spatie's model
- Remove custom `/app/Models/Role.php`
- Update RoleController to use `Spatie\Permission\Models\Role`

**Option B:** Extend Spatie's model
```php
<?php
namespace App\Models;

use Spatie\Permission\Models\Role as SpatieRole;

class Role extends SpatieRole
{
    protected $fillable = ['name', 'label'];
    // Add custom methods here
}
```

#### 1.3 Protect Unprotected Routes
**File:** `/routes/web.php`

```php
// Add role/permission middleware to all resource routes
Route::middleware(['auth', 'verified'])->group(function () {
    // Only managers and admins can manage projects
    Route::middleware('role:admin|manager')->group(function () {
        Route::resource('projects', ProjectController::class);
    });
    
    // Only accountants and admins can manage expenses
    Route::middleware('role:admin|accountant')->group(function () {
        Route::resource('expenses', ExpenseController::class);
    });
    
    // Only accountants and admins can manage payments
    Route::middleware('role:admin|accountant')->group(function () {
        Route::resource('payments', PaymentController::class);
    });
    
    // Similar for other resources...
});
```

### Phase 2: Complete Permission Setup

#### 2.1 Update Permission Seeder
**File:** `/database/seeders/RolePermissionSeeder.php`

```php
<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Spatie\Permission\Models\Role;
use Spatie\Permission\Models\Permission;

class RolePermissionSeeder extends Seeder
{
    public function run(): void
    {
        app()[\Spatie\Permission\PermissionRegistrar::class]->forgetCachedPermissions();

        // Define all permissions
        $permissions = [
            // User Management
            'users.view', 'users.create', 'users.edit', 'users.delete',
            
            // Project Management
            'projects.view', 'projects.create', 'projects.edit', 'projects.delete',
            
            // Expense Management
            'expenses.view', 'expenses.create', 'expenses.edit', 'expenses.delete',
            
            // Income Management
            'incomes.view', 'incomes.create', 'incomes.edit', 'incomes.delete',
            
            // Payment Management
            'payments.view', 'payments.create', 'payments.edit', 'payments.delete',
            
            // Report Management
            'reports.view', 'reports.generate', 'reports.export',
            
            // Employee/Worker Management
            'employees.view', 'employees.create', 'employees.edit', 'employees.delete',
            'workers.view', 'workers.create', 'workers.edit', 'workers.delete',
            
            // Order Management
            'orders.view', 'orders.create', 'orders.edit', 'orders.delete',
            
            // Settings
            'settings.view', 'settings.edit',
        ];

        foreach ($permissions as $permission) {
            Permission::firstOrCreate(['name' => $permission]);
        }

        // Define role permissions
        $rolePermissions = [
            'admin' => $permissions, // All permissions
            
            'manager' => [
                'projects.view', 'projects.create', 'projects.edit',
                'employees.view', 'employees.create', 'employees.edit',
                'workers.view', 'workers.create', 'workers.edit',
                'orders.view', 'orders.create', 'orders.edit',
                'reports.view', 'reports.generate',
            ],
            
            'accountant' => [
                'payments.view', 'payments.create', 'payments.edit',
                'incomes.view', 'incomes.create', 'incomes.edit',
                'expenses.view', 'expenses.create', 'expenses.edit',
                'reports.view', 'reports.generate', 'reports.export',
                'projects.view',
            ],
        ];

        foreach ($rolePermissions as $roleName => $perms) {
            $role = Role::firstOrCreate(['name' => $roleName]);
            $role->syncPermissions($perms);
        }
    }
}
```

#### 2.2 Reseed the Database
```bash
php artisan migrate:refresh --seed
# or
php artisan db:seed --class=RolePermissionSeeder
```

### Phase 3: Implement Role-Based Views

#### 3.1 Update Dashboard Controller
**File:** `/app/Http/Controllers/DashboardController.php`

```php
public function index()
{
    $user = auth()->user();
    
    if ($user->hasRole('admin')) {
        return $this->adminDashboard();
    } elseif ($user->hasRole('manager')) {
        return $this->managerDashboard();
    } elseif ($user->hasRole('accountant')) {
        return $this->accountantDashboard();
    }
    
    return $this->userDashboard();
}

private function adminDashboard() { /* ... */ }
private function managerDashboard() { /* ... */ }
private function accountantDashboard() { /* ... */ }
private function userDashboard() { /* ... */ }
```

#### 3.2 Add Role-Based Blade Directives
In views, use:
```blade
@role('admin')
    <div>Only admins see this</div>
@endrole

@role('accountant|admin')
    <div>Accountants and admins see this</div>
@endrole

@can('incomes.create')
    <button>Create Income</button>
@endcan
```

### Phase 4: Add Authorization Checks

#### 4.1 Use Policies (Optional)
```bash
php artisan make:policy ProjectPolicy --model=Project
```

#### 4.2 Add `authorize()` in Controllers
```php
public function edit(Project $project)
{
    $this->authorize('update', $project);
    // ...
}
```

---

## 7. Testing Checklist

- [ ] Test Admin role - can access everything
- [ ] Test Manager role - can manage projects, employees, workers
- [ ] Test Accountant role - can only access payments, incomes, expenses
- [ ] Test unprotected routes - verify middleware blocks unauthorized access
- [ ] Test role assignment/removal works correctly
- [ ] Test permission caching is invalidated on role changes
- [ ] Test views correctly show/hide elements based on role

---

## 8. Security Best Practices

1. **Always use middleware** for sensitive routes
2. **Double-check authorization** in controllers with `authorize()`
3. **Cache permissions** but invalidate on changes (Spatie does this)
4. **Audit role changes** - log when roles are assigned/removed
5. **Use policies** for model-level authorization
6. **Hide UI elements** that user can't access (using `@can` directives)
7. **Never trust client-side** authorization checks

---

## Files to Review/Modify

| File | Issue | Action |
|------|-------|--------|
| `/app/Http/Middleware/RoleMiddleware.php` | Custom middleware conflicts with Spatie | Delete or Fix |
| `/app/Models/Role.php` | Conflicts with Spatie's model | Remove or Extend |
| `/routes/web.php` | Unprotected routes | Add middleware |
| `/database/seeders/RolePermissionSeeder.php` | Incomplete accountant permissions | Update |
| `/app/Http/Controllers/DashboardController.php` | Not role-aware | Refactor |
| `resources/views/dashboard.blade.php` | No role-based filtering | Add @role directives |

---

## Summary Table

| Category | Status | Priority |
|----------|--------|----------|
| Core RBAC Implementation | ✅ Good | - |
| Role Definitions | ⚠️ Incomplete | 🔴 High |
| Permission Definitions | ⚠️ Incomplete | 🔴 High |
| Route Protection | ⚠️ Weak | 🔴 High |
| Middleware Setup | ⚠️ Conflicting | 🔴 High |
| Role Model | ❌ Conflicting | 🔴 High |
| Dashboard Authorization | ❌ Missing | 🟡 Medium |
| View-Level Authorization | ❌ Missing | 🟡 Medium |

---

**Next Step:** Start with Phase 1 to fix critical issues, then proceed with Phases 2-4 for full implementation.
