# Sidebar Navigation Quick Reference

## Sidebar Structure

### For All Authenticated Users:

```
BuildMate Logo
├── Dashboard (with chart-line icon)
├── Projects (with project-diagram icon)
├── Employees (with users icon)
├── Expenses (with money-bill-wave icon)
├── Incomes (with coins icon)
├── Transactions (with exchange-alt icon)
└── Reports (with file-alt icon)
```

### For Admin Users Only (Additional Section):

```
ADMINISTRATION (uppercase header)
├── Manage Users (with user-cog icon)
├── Manage Roles (with user-shield icon)
├── Permissions (with lock icon)
└── Settings (with cog icon)
```

### Footer (All Users):

```
User Info Card:
├── "Logged in as" label
├── User Name
├── User Email
├── Current Role Badge
└── Logout Button (with sign-out-alt icon)
```

---

## Navigation Colors

- **Background:** `bg-green-800` (Dark green)
- **Text:** `text-green-100` (Light green text)
- **Hover:** `hover:bg-green-700` (Medium green on hover)
- **Active:** `bg-green-900` (Very dark green for active routes)
- **Borders:** `border-green-700` (Medium green borders)
- **Admin Section:** `text-green-300` (Lighter green for header)

---

## Icon Reference

| Feature | Icon | Class |
|---------|------|-------|
| Dashboard | Chart Line | `fas fa-chart-line` |
| Projects | Project Diagram | `fas fa-project-diagram` |
| Employees | Users | `fas fa-users` |
| Expenses | Money Bill Wave | `fas fa-money-bill-wave` |
| Incomes | Coins | `fas fa-coins` |
| Transactions | Exchange Alt | `fas fa-exchange-alt` |
| Reports | File Alt | `fas fa-file-alt` |
| Manage Users | User Cog | `fas fa-user-cog` |
| Manage Roles | User Shield | `fas fa-user-shield` |
| Permissions | Lock | `fas fa-lock` |
| Settings | Cog | `fas fa-cog` |
| Logout | Sign Out Alt | `fas fa-sign-out-alt` |

---

## User Management Pages

### Users Index Page
**Route:** `GET /users`  
**Authorization:** Admin only  
**Features:**
- List all users in card format
- Display user roles with badges
- Quick actions: View, Edit, Delete
- Pagination support
- Create new user button

### Create User Page
**Route:** `GET /users/create` | `POST /users`  
**Authorization:** Admin only  
**Features:**
- Two-column layout (form + roles)
- Role selection with descriptions
- Password confirmation
- Role information guide
- Form validation feedback

### Edit User Page
**Route:** `GET /users/{user}/edit` | `PUT /users/{user}`  
**Authorization:** Admin only  
**Features:**
- Two-column layout (form + roles)
- Update user information
- Change assigned roles
- Optional password change
- Current roles display
- Role descriptions

### User Details Page
**Route:** `GET /users/{user}`  
**Authorization:** Admin only  
**Features:**
- User basic information
- Assigned roles with permission counts
- Effective permissions list
- Account status
- Quick action buttons
- Edit and delete options

---

## Conditional Rendering

The admin section is conditionally rendered using:

```blade
@auth
    @if(auth()->user()->hasRole('admin'))
        <!-- Admin navigation items appear here -->
    @endif
@endauth
```

This ensures:
- Only authenticated users see the section
- Only users with 'admin' role see admin items
- Non-admin users see a clean sidebar

---

## Route Protection

All admin routes are protected by the `role:admin` middleware:

```php
Route::middleware(['role:admin'])->group(function () {
    Route::resource('users', UserController::class);
    Route::resource('roles', RoleController::class);
    Route::resource('permissions', PermissionController::class);
    Route::get('/settings', [SettingController::class, 'index'])->name('settings.index');
});
```

**Non-admin users attempting to access:**
- `/users` - Middleware blocks, shows 403 Forbidden
- `/roles` - Middleware blocks, shows 403 Forbidden
- `/permissions` - Middleware blocks, shows 403 Forbidden

---

## Responsive Behavior

### Desktop (≥992px):
- Sidebar fully visible on left
- Content area takes remaining space
- All navigation items visible
- User info card fully displayed

### Tablet (768px - 991px):
- Sidebar may collapse with hamburger menu (depends on layout implementation)
- Icons and labels visible
- Responsive grid in forms

### Mobile (<768px):
- Sidebar should collapse or become drawer
- Forms stack vertically
- Buttons stack or wrap
- User cards displayed full width

---

## Active Route Detection

Navigation items use `request()->routeIs()` to show active state:

```blade
class="block px-4 py-2 rounded hover:bg-green-700 
       {{ request()->routeIs('users.*') ? 'bg-green-900' : '' }}"
```

Active routes detected:
- `projects.*` - Projects section
- `employees.*` - Employees section
- `expenses.*` - Expenses section
- `incomes.*` - Incomes section
- `transactions.*` - Transactions section
- `reports.*` - Reports section
- `users.*` - Users management
- `roles.*` - Roles management
- `permissions.*` - Permissions management
- `settings` - Settings

---

## Admin Quick Tips

### Creating a User:
1. Click "Manage Users" in sidebar
2. Click "Create New User"
3. Fill in user details
4. Select roles for the user
5. Click "Create User"

### Editing User Roles:
1. Click "Manage Users" in sidebar
2. Find user and click "Edit"
3. Update roles in right sidebar
4. Click "Update Roles" or "Save Changes"
5. Roles are synced (old roles replaced with new ones)

### Deleting a User:
1. Click "Manage Users" in sidebar
2. Click "Delete" on user card or "Edit" then "Delete User"
3. Confirm deletion in modal
4. User is permanently removed

### Managing System Roles:
1. Click "Manage Roles" in sidebar
2. View existing roles
3. Create new role or edit existing
4. Assign permissions to roles
5. Users with that role inherit all permissions

### Viewing User Details:
1. Click "Manage Users" in sidebar
2. Click "View" on any user
3. See complete user profile
4. View assigned roles and permissions
5. Quick edit or delete options

---

## Common Issues & Solutions

### Admin Section Not Visible
**Problem:** Admin user doesn't see admin section  
**Check:**
1. User has 'admin' role assigned
2. `auth()->user()->hasRole('admin')` returns true
3. Clear cache: `php artisan view:clear`

### Cannot Access User Management
**Problem:** Getting 403 Forbidden on `/users`  
**Check:**
1. User has 'admin' role
2. Middleware configured: `middleware(['role:admin'])`
3. Service provider registered: `Spatie\Permission\PermissionServiceProvider`
4. Clear route cache: `php artisan route:clear`

### Roles Not Showing in Form
**Problem:** Role checkboxes empty in create/edit  
**Check:**
1. Roles exist in database
2. `Role::all()` or `Role::pluck('name')` returns results
3. Roles table populated correctly

### Icons Not Displaying
**Problem:** Sidebar shows boxes or blank spaces  
**Check:**
1. Font Awesome library loaded in `@vite`
2. CDN link working correctly
3. Check browser console for 404 errors
4. Verify Font Awesome class names are correct

---

## Customization Examples

### Add New Admin Feature to Sidebar:
```blade
<!-- In sidebar.blade.php, inside admin section -->
<a href="{{ route('new-feature.index') }}" 
   class="block px-4 py-2 rounded hover:bg-green-700 
          {{ request()->routeIs('new-feature.*') ? 'bg-green-900' : '' }}">
    <i class="fas fa-icon-name mr-2"></i> New Feature
</a>
```

### Change Admin Section Color:
Replace `bg-green-*` with desired color:
- `bg-blue-800`, `bg-blue-700`, `bg-blue-900`
- `bg-purple-800`, `bg-purple-700`, `bg-purple-900`
- `bg-indigo-800`, `bg-indigo-700`, `bg-indigo-900`

### Hide a Navigation Item (Non-Admin):
```blade
@if(!request()->routeIs('users.*', 'roles.*'))
    <!-- Navigation item -->
@endif
```

---

## Performance Notes

- **Route Caching:** After adding/modifying routes, clear cache
- **View Caching:** After updating blade files, clear cache
- **Query Optimization:** User list uses `.with('roles')` for eager loading
- **Icon Loading:** Font Awesome should be cached by browser
- **Mobile Performance:** Sidebar scrollable with `overflow-y-auto`

