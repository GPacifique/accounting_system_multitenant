# Enhanced Styling Guide

All views in SiteLedger now use consistent, professional styling that matches the sidebar design. This guide shows you how to apply these styles.

## 🎨 Core Principles

- **Gradient backgrounds** - Subtle gradients for depth
- **Smooth animations** - All interactions are animated
- **Hover effects** - Cards, buttons, and links respond to hover
- **Consistent colors** - Green theme (#166534) throughout
- **Professional shadows** - Layered shadows for depth

## 📦 Components Available

### 1. Page Header Component

```blade
<x-page-header 
    title="Dashboard" 
    subtitle="Welcome to your financial overview">
    
    <x-slot name="actions">
        <x-enhanced-button type="primary" icon="fas fa-plus" href="{{ route('projects.create') }}">
            New Project
        </x-enhanced-button>
    </x-slot>
</x-page-header>
```

**Output:** Beautiful header with title, subtitle, and action buttons

---

### 2. Enhanced Card Component

```blade
<x-enhanced-card title="Recent Transactions" subtitle="Last 30 days">
    <!-- Your content here -->
    <ul>
        <li>Transaction 1</li>
        <li>Transaction 2</li>
    </ul>
</x-enhanced-card>
```

**Features:**
- Hover effect with lift animation
- Top border gradient on hover
- Shadow depth changes
- Fade-in animation on load

---

### 3. Enhanced Button Component

```blade
<!-- Primary Button -->
<x-enhanced-button type="primary" icon="fas fa-save">
    Save Changes
</x-enhanced-button>

<!-- With Link -->
<x-enhanced-button type="success" icon="fas fa-plus" href="{{ route('expenses.create') }}">
    Add Expense
</x-enhanced-button>

<!-- Different Sizes -->
<x-enhanced-button type="danger" size="sm">Small</x-enhanced-button>
<x-enhanced-button type="info" size="md">Medium</x-enhanced-button>
<x-enhanced-button type="warning" size="lg">Large</x-enhanced-button>
```

**Available Types:**
- `primary` - Green gradient
- `secondary` - Gray gradient
- `success` - Green gradient
- `danger` - Red gradient
- `warning` - Orange gradient
- `info` - Blue gradient

---

### 4. Enhanced Alert Component

```blade
<!-- Success Alert -->
<x-enhanced-alert type="success">
    Your changes have been saved successfully!
</x-enhanced-alert>

<!-- Warning Alert -->
<x-enhanced-alert type="warning" dismissible>
    Please review your payment details.
</x-enhanced-alert>

<!-- Danger Alert -->
<x-enhanced-alert type="danger">
    Error: Unable to process payment.
</x-enhanced-alert>

<!-- Info Alert -->
<x-enhanced-alert type="info">
    New feature available: Export to Excel
</x-enhanced-alert>
```

---

## 🎯 CSS Classes Available

### Card Styles

```html
<!-- Standard Enhanced Card -->
<div class="enhanced-card">
    <!-- Content -->
</div>

<!-- Stat Card with Icon -->
<div class="stat-card" style="--border-color: #10b981;">
    <div class="stat-card-icon">📊</div>
    <div class="stat-card-title">Total Revenue</div>
    <div class="stat-card-value">$125,430</div>
    <div class="stat-card-subtitle">+12% from last month</div>
</div>
```

### Table Styles

```html
<table class="enhanced-table">
    <thead>
        <tr>
            <th>Name</th>
            <th>Email</th>
            <th>Role</th>
            <th>Actions</th>
        </tr>
    </thead>
    <tbody>
        <tr>
            <td>John Doe</td>
            <td>john@example.com</td>
            <td><span class="badge-enhanced badge-success">Admin</span></td>
            <td>
                <button class="btn-enhanced btn-enhanced-info btn-sm">Edit</button>
            </td>
        </tr>
    </tbody>
</table>
```

### Grid Layouts

```html
<!-- 4-column grid (responsive) -->
<div class="grid-enhanced grid-cols-4">
    <div class="stat-card">Card 1</div>
    <div class="stat-card">Card 2</div>
    <div class="stat-card">Card 3</div>
    <div class="stat-card">Card 4</div>
</div>

<!-- 3-column grid -->
<div class="grid-enhanced grid-cols-3">
    <div class="enhanced-card">Card 1</div>
    <div class="enhanced-card">Card 2</div>
    <div class="enhanced-card">Card 3</div>
</div>
```

**Note:** Grids automatically become 1-column on mobile

### Badge Styles

```html
<span class="badge-enhanced badge-success">Active</span>
<span class="badge-enhanced badge-warning">Pending</span>
<span class="badge-enhanced badge-danger">Inactive</span>
<span class="badge-enhanced badge-info">New</span>
<span class="badge-enhanced badge-secondary">Draft</span>
```

### Form Styles

```html
<form class="form-enhanced">
    <div class="form-group-enhanced">
        <label class="form-label-enhanced" for="name">Full Name</label>
        <input type="text" id="name" class="form-input-enhanced" placeholder="Enter your name">
    </div>
    
    <div class="form-group-enhanced">
        <label class="form-label-enhanced" for="email">Email</label>
        <input type="email" id="email" class="form-input-enhanced" placeholder="your@email.com">
    </div>
    
    <button type="submit" class="btn-enhanced btn-enhanced-primary">
        <i class="fas fa-save"></i> Submit
    </button>
</form>
```

### Chart Containers

```html
<div class="chart-container-enhanced">
    <h3 class="chart-title-enhanced">Monthly Revenue</h3>
    <canvas id="myChart"></canvas>
</div>
```

### Empty States

```html
<div class="empty-state-enhanced">
    <div class="empty-state-icon">📭</div>
    <h3 class="empty-state-title">No data available</h3>
    <p class="empty-state-description">Start by adding your first entry</p>
    <button class="btn-enhanced btn-enhanced-primary mt-4">
        <i class="fas fa-plus"></i> Add Entry
    </button>
</div>
```

### Loading Spinner

```html
<div class="text-center py-8">
    <div class="loading-spinner"></div>
    <p class="text-gray-600 mt-4">Loading...</p>
</div>
```

---

## 🎨 Color Palette

### Primary Colors
- **Green Primary:** `#166534`
- **Green Secondary:** `#15803d`

### Status Colors
- **Success:** `#10b981` (Green)
- **Warning:** `#f59e0b` (Amber)
- **Danger:** `#ef4444` (Red)
- **Info:** `#3b82f6` (Blue)

### Neutral Colors
- **Gray 50:** `#f9fafb`
- **Gray 100:** `#f3f4f6`
- **Gray 600:** `#4b5563`
- **Gray 900:** `#111827`

---

## ✨ Animation Classes

All elements with these classes animate on load:
- `fadeIn` - Fade in from top
- `slideInLeft` - Slide from left
- `slideInRight` - Slide from right
- `pulse-glow` - Pulsing glow effect

---

## 📱 Responsive Design

All styles are fully responsive:

- **Desktop (>992px):** Full sidebar, 4-column grids
- **Tablet (768px-992px):** Collapsible sidebar, 2-column grids
- **Mobile (<768px):** Hidden sidebar (toggle), 1-column grids

---

## 🔧 Example: Full Page Layout

```blade
@extends('layouts.app')

@section('content')
<div class="py-6">
    <!-- Page Header -->
    <x-page-header 
        title="Projects" 
        subtitle="Manage all construction projects">
        
        <x-slot name="actions">
            <x-enhanced-button type="success" icon="fas fa-plus" href="{{ route('projects.create') }}">
                New Project
            </x-enhanced-button>
            <x-enhanced-button type="secondary" icon="fas fa-download">
                Export
            </x-enhanced-button>
        </x-slot>
    </x-page-header>

    <!-- Stats Grid -->
    <div class="grid-enhanced grid-cols-4 mb-6">
        <div class="stat-card">
            <div class="stat-card-icon">📊</div>
            <div class="stat-card-title">Total Projects</div>
            <div class="stat-card-value">24</div>
            <div class="stat-card-subtitle">+3 this month</div>
        </div>
        <!-- More stat cards... -->
    </div>

    <!-- Main Content Card -->
    <x-enhanced-card title="All Projects" subtitle="List of active and completed projects">
        <table class="enhanced-table">
            <thead>
                <tr>
                    <th>Project Name</th>
                    <th>Client</th>
                    <th>Status</th>
                    <th>Budget</th>
                    <th>Actions</th>
                </tr>
            </thead>
            <tbody>
                @forelse($projects as $project)
                    <tr>
                        <td>{{ $project->name }}</td>
                        <td>{{ $project->client->name }}</td>
                        <td>
                            <span class="badge-enhanced badge-success">
                                {{ ucfirst($project->status) }}
                            </span>
                        </td>
                        <td>${{ number_format($project->budget, 2) }}</td>
                        <td>
                            <x-enhanced-button 
                                type="info" 
                                size="sm" 
                                href="{{ route('projects.show', $project) }}">
                                View
                            </x-enhanced-button>
                        </td>
                    </tr>
                @empty
                    <tr>
                        <td colspan="5">
                            <div class="empty-state-enhanced">
                                <div class="empty-state-icon">📁</div>
                                <h3 class="empty-state-title">No projects yet</h3>
                                <p class="empty-state-description">Create your first project to get started</p>
                            </div>
                        </td>
                    </tr>
                @endforelse
            </tbody>
        </table>
    </x-enhanced-card>
</div>
@endsection
```

---

## 🚀 Quick Migration Guide

### Before (Old Style):
```html
<div class="bg-white p-6 rounded shadow">
    <h3 class="text-lg font-bold">Title</h3>
    <p>Content</p>
</div>
```

### After (New Style):
```html
<x-enhanced-card title="Title">
    <p>Content</p>
</x-enhanced-card>
```

**Benefits:**
- Automatic hover effects
- Consistent styling
- Animation on load
- Responsive design
- Less code to write

---

## 📖 Best Practices

1. **Use Components** - Prefer `<x-enhanced-card>` over manual `<div class="enhanced-card">`
2. **Consistent Buttons** - Always use `<x-enhanced-button>` for actions
3. **Grid Layouts** - Use `grid-enhanced` classes for responsive layouts
4. **Page Headers** - Start every page with `<x-page-header>`
5. **Status Indicators** - Use badge classes for status display
6. **Tables** - Apply `enhanced-table` class to all data tables
7. **Forms** - Wrap forms in `form-enhanced` class
8. **Alerts** - Use `<x-enhanced-alert>` for user feedback

---

## 🔄 Need More?

If you need custom styling:
1. Check `/resources/css/app.css` for all available classes
2. Follow the existing gradient and animation patterns
3. Use the color palette defined above
4. Maintain consistency with sidebar styling

---

**All views now have professional, consistent styling that matches the beautiful sidebar design! 🎉**
