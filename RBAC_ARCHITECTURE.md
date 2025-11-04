# RBAC System Architecture Diagram

## 🏗️ System Architecture Overview

```
┌────────────────────────────────────────────────────────────────────┐
│                         User Login                                 │
│                    (auth.php routes)                               │
└────────────────────────┬───────────────────────────────────────────┘
                         │
                         ▼
        ┌────────────────────────────────────┐
        │    Dashboard Route                 │
        │  /dashboard (auth, verified)       │
        └────────────────┬───────────────────┘
                         │
                         ▼
        ┌────────────────────────────────────┐
        │  DashboardController@index()       │
        │  - Check user role                 │
        │  - Route to role dashboard         │
        └────┬─────────┬──────────┬──────────┘
             │         │          │
    ┌────────▼──┐ ┌────▼────┐ ┌──▼──────┐ ┌────────────┐
    │   ADMIN   │ │MANAGER  │ │ACCOUNTANT│ │    USER    │
    │ Dashboard │ │Dashboard│ │Dashboard│ │ Dashboard │
    │ (All KPIs)│ │(Projects)│ │(Finance) │ │(Read-only)│
    └───────────┘ └─────────┘ └─────────┘ └────────────┘
         │              │           │            │
         ▼              ▼           ▼            ▼
      admin.           manager.    accountant.  user.
      blade.php        blade.php   blade.php    blade.php
```

---

## 🔐 Role-Based Route Protection Flow

```
User Request → Route Handler
    │
    ├─→ Middleware: auth
    │   └─→ Is user authenticated?
    │       ├─→ No: Redirect to login
    │       └─→ Yes: Continue
    │
    ├─→ Middleware: verified
    │   └─→ Is email verified?
    │       ├─→ No: Verify email page
    │       └─→ Yes: Continue
    │
    ├─→ Middleware: role
    │   └─→ Does user have required role?
    │       ├─→ No: Abort 403
    │       └─→ Yes: Execute controller
    │
    └─→ Controller Action
        └─→ Return view with data
```

---

## 📊 Permission Hierarchy & Inheritance

```
ADMIN (All Permissions)
├── User Management
│   ├── users.view
│   ├── users.create
│   ├── users.edit
│   └── users.delete
│
├── Role Management
│   ├── Inherited from Spatie
│   └── Full control
│
├── Project Management
│   ├── projects.view
│   ├── projects.create
│   ├── projects.edit
│   └── projects.delete
│
├── Employee/Worker Management
│   ├── employees.*
│   ├── workers.*
│   └── orders.*
│
└── Financial Management
    ├── payments.*
    ├── incomes.*
    └── expenses.*


MANAGER (Project/Team Permissions)
├── Project Management
│   ├── projects.view ✓
│   ├── projects.create ✓
│   ├── projects.edit ✓
│   └── projects.delete ✗
│
├── Team Management
│   ├── employees.view ✓
│   ├── employees.create ✓
│   ├── employees.edit ✓
│   ├── workers.view ✓
│   ├── workers.create ✓
│   └── workers.edit ✓
│
├── Order Management
│   ├── orders.view ✓
│   ├── orders.create ✓
│   └── orders.edit ✓
│
└── Reports
    ├── reports.view ✓
    └── reports.generate ✓


ACCOUNTANT (Financial Permissions)
├── Payment Management
│   ├── payments.view ✓
│   ├── payments.create ✓
│   └── payments.edit ✓
│
├── Income Management
│   ├── incomes.view ✓
│   ├── incomes.create ✓
│   └── incomes.edit ✓
│
├── Expense Management
│   ├── expenses.view ✓
│   ├── expenses.create ✓
│   └── expenses.edit ✓
│
├── Project View (Read-only)
│   └── projects.view ✓
│
└── Reports
    ├── reports.view ✓
    ├── reports.generate ✓
    └── reports.export ✓


USER (Limited Permissions)
└── Project View
    └── projects.view ✓ (Read-only)
```

---

## 🌐 Route Access Matrix

```
                ┌────────┬──────────┬────────────┬────────┐
                │ ADMIN  │ MANAGER  │ ACCOUNTANT │  USER  │
┌───────────────┼────────┼──────────┼────────────┼────────┤
│ /users        │ ✅     │ ❌       │ ❌         │ ❌     │
│ /roles        │ ✅     │ ❌       │ ❌         │ ❌     │
│ /permissions  │ ✅     │ ❌       │ ❌         │ ❌     │
│ /settings     │ ✅     │ ❌       │ ❌         │ ❌     │
├───────────────┼────────┼──────────┼────────────┼────────┤
│ /projects     │ ✅     │ ✅       │ ❌         │ ❌     │
│ /employees    │ ✅     │ ✅       │ ❌         │ ❌     │
│ /workers      │ ✅     │ ✅       │ ❌         │ ❌     │
│ /orders       │ ✅     │ ✅       │ ❌         │ ❌     │
├───────────────┼────────┼──────────┼────────────┼────────┤
│ /expenses     │ ✅     │ ❌       │ ✅         │ ❌     │
│ /incomes      │ ✅     │ ❌       │ ✅         │ ❌     │
│ /payments     │ ✅     │ ❌       │ ✅         │ ❌     │
├───────────────┼────────┼──────────┼────────────┼────────┤
│ /reports      │ ✅     │ ✅       │ ✅         │ ❌     │
│ /clients      │ ✅     │ ✅       │ ✅         │ ✅     │
│ /transactions │ ✅     │ ✅       │ ✅         │ ✅     │
│ /finance      │ ✅     │ ✅       │ ✅         │ ✅     │
└───────────────┴────────┴──────────┴────────────┴────────┘
```

---

## 📱 Dashboard Data Flow

### Admin Dashboard
```
DashboardController::adminDashboard()
├── Workers
│   ├── Total workers count
│   ├── Active workers count
│   └── Recent workers (6 items)
├── Payments
│   ├── Total payments
│   ├── Payments this month
│   └── Recent payments (7 items)
├── Transactions
│   ├── Recent transactions (7 items)
│   └── This month sum
├── Incomes
│   ├── Total incomes
│   ├── Incomes this month
│   └── Recent incomes (7 items)
├── Expenses
│   ├── Total expenses
│   ├── Expenses this month
│   └── Recent expenses (7 items)
├── Projects
│   ├── Total projects count
│   ├── Projects this month
│   ├── Total contract value
│   └── Recent projects (7 items)
├── Project Stats (Payment Summary)
│   └── Projects with payment status
└── Charts
    ├── 6-month income trend
    ├── 6-month expenses trend
    └── 6-month payments trend
```

### Accountant Dashboard
```
DashboardController::accountantDashboard()
├── Payments
│   ├── Total payments
│   ├── Payments this month
│   └── Recent payments (10 items)
├── Incomes
│   ├── Total incomes
│   ├── Incomes this month
│   └── Recent incomes (10 items)
├── Expenses
│   ├── Total expenses
│   ├── Expenses this month
│   └── Recent expenses (10 items)
├── Net Cash Flow
│   ├── Total net cash flow
│   └── This month net cash flow
└── Charts
    ├── 6-month income trend
    ├── 6-month expenses trend
    └── 6-month payments trend
```

### Manager Dashboard
```
DashboardController::managerDashboard()
├── Workers/Team
│   ├── Total workers
│   ├── Active workers
│   └── Recent workers (10 items)
├── Projects
│   ├── Total projects
│   ├── Projects this month
│   ├── Total budget
│   └── Recent projects (10 items)
├── Project Stats
│   ├── Project budgets
│   ├── Amount paid
│   └── Amount remaining
└── Chart
    └── 6-month project budget trend
```

### User Dashboard
```
DashboardController::userDashboard()
├── Basic Info
│   ├── User name
│   └── Account status
├── Projects
│   ├── Total projects count
│   ├── This month count
│   └── Recent projects (5 items)
└── Info Message
    └── "Limited Access - Read Only"
```

---

## 🔄 Middleware Stack

```
┌─────────────────────────────────────────────────┐
│  HTTP Request                                   │
└──────────────────┬──────────────────────────────┘
                   ▼
        ┌──────────────────────┐
        │ auth middleware      │
        │ (User authenticated?)│
        └──────────┬───────────┘
                   ▼
        ┌──────────────────────┐
        │ verified middleware  │
        │ (Email verified?)    │
        └──────────┬───────────┘
                   ▼
        ┌──────────────────────┐
        │ role middleware      │
        │ (role:admin|manager) │
        └──────────┬───────────┘
                   ▼
        ┌──────────────────────┐
        │ Controller Action    │
        └──────────┬───────────┘
                   ▼
        ┌──────────────────────┐
        │ View Rendered        │
        │ with data            │
        └──────────┬───────────┘
                   ▼
       ┌───────────────────────┐
       │ HTTP Response         │
       │ (200 OK / 403 Denied) │
       └───────────────────────┘
```

---

## 🔑 Authentication vs Authorization Flow

```
┌──────────────────────────────────────────────────────┐
│                  AUTHENTICATION                      │
│         (Is this really the person they         │
│              claim to be?)                          │
│                                                      │
│  User → Password Check → Session Created → Token    │
│                                                      │
└──────────────────┬───────────────────────────────────┘
                   │
                   ▼
      ┌────────────────────────────────────┐
      │   User is Authenticated             │
      │   (Logged in)                       │
      └────────────────┬────────────────────┘
                       │
                       ▼
        ┌──────────────────────────────────┐
        │      AUTHORIZATION                │
        │  (What can this user do?)         │
        │                                   │
        │  Check: Does user have:           │
        │  ├── role:admin?                  │
        │  ├── role:manager?                │
        │  ├── role:accountant?             │
        │  ├── role:user?                   │
        │  └── permission:X?                │
        └──────────────┬────────────────────┘
                       │
        ┌──────────────┴──────────────┐
        ▼                             ▼
    ✅ Allowed                    ❌ Forbidden
    (200 OK)                      (403 Forbidden)
    Execute Action               Abort Request
```

---

## 📚 File Structure

```
/home/gashumba/siteledger/
│
├── app/Http/
│   ├── Controllers/
│   │   ├── DashboardController.php (✏️ MODIFIED)
│   │   ├── UserController.php
│   │   ├── RoleController.php
│   │   └── ... other controllers
│   │
│   ├── Middleware/
│   │   ├── RoleMiddleware.php (❌ DELETED)
│   │   └── ... other middleware
│   │
│   └── Kernel.php (✏️ MODIFIED)
│
├── app/Models/
│   ├── Role.php (❌ DELETED)
│   ├── User.php
│   └── ... other models
│
├── database/
│   ├── seeders/
│   │   ├── RolePermissionSeeder.php (✏️ MODIFIED)
│   │   ├── RoleSeeder.php (✏️ MODIFIED)
│   │   └── DatabaseSeeder.php
│   │
│   └── migrations/
│       ├── 2025_09_25_114306_create_permission_tables.php
│       └── ... other migrations
│
├── routes/
│   └── web.php (✏️ MODIFIED)
│
├── resources/views/
│   ├── dashboard.blade.php (Original - can delete)
│   │
│   └── dashboard/
│       ├── admin.blade.php (✨ CREATED)
│       ├── accountant.blade.php (✨ CREATED)
│       ├── manager.blade.php (✨ CREATED)
│       └── user.blade.php (✨ CREATED)
│
└── RBAC_*.md (Documentation files)
```

---

## ✅ Implementation Checklist

- [x] Removed conflicting custom middleware
- [x] Removed conflicting custom Role model
- [x] Updated all seeders to use Spatie models
- [x] Added complete permission matrix
- [x] Protected all routes with appropriate middleware
- [x] Refactored DashboardController for role awareness
- [x] Created 4 role-specific dashboard views
- [x] Database seeded successfully
- [x] Code has no compilation errors
- [ ] Manual testing in browser (Todo)
- [ ] Performance testing (Todo - Optional)
- [ ] Audit logging setup (Todo - Optional)

---

## 🎓 Learning Resources

For understanding this RBAC system:

1. **Spatie Laravel Permission** - Used for role/permission management
   - Docs: `vendor/spatie/laravel-permission/README.md`
   - Config: `config/permission.php`

2. **Laravel Middleware** - Protects routes
   - Docs: `routes/web.php` middleware definitions
   - Usage: `middleware(['auth', 'role:admin'])`

3. **Blade Directives** - For view-level authorization (optional)
   - `@role('admin')` ... `@endrole`
   - `@can('users.create')` ... `@endcan`

4. **Laravel Policies** - For model-level authorization (optional)
   - Create with: `php artisan make:policy ProjectPolicy`
   - Use in controller: `$this->authorize('update', $project)`

---

**System Status:** ✅ READY FOR TESTING

All architecture is in place. Follow the test scenarios in `RBAC_QUICK_REFERENCE.md` to verify!
