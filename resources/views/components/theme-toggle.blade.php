{{-- resources/views/components/theme-toggle.blade.php --}}
<div class="theme-toggle-container">
    <!-- Desktop Theme Toggle -->
    <button 
        type="button" 
        data-theme-toggle
        class="theme-toggle-btn hidden md:flex items-center px-3 py-2 text-sm font-medium rounded-lg transition-all duration-200 hover:theme-aware-bg-secondary dark:hover:bg-gray-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-primary"
        title="Toggle theme">
        <span class="theme-icon mr-2">
            <i class="fas fa-moon"></i>
        </span>
        <span class="theme-text hidden lg:inline">
            Dark Mode
        </span>
    </button>

    <!-- Mobile Theme Toggle -->
    <button 
        type="button" 
        data-theme-toggle
        class="theme-toggle-mobile md:hidden flex items-center justify-center w-10 h-10 rounded-lg transition-all duration-200 hover:theme-aware-bg-secondary dark:hover:bg-gray-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-primary"
        title="Toggle theme">
        <span class="theme-icon">
            <i class="fas fa-moon"></i>
        </span>
    </button>
</div>

{{-- Inline styles for theme toggle --}}
<style>
.theme-toggle-btn {
    background: var(--bg-card);
    color: var(--text-secondary);
    border: 1px solid var(--border-primary);
}

.theme-toggle-btn:hover {
    color: var(--text-primary);
    background: var(--bg-tertiary);
}

.theme-toggle-mobile {
    background: var(--bg-card);
    color: var(--text-secondary);
    border: 1px solid var(--border-primary);
}

.theme-toggle-mobile:hover {
    color: var(--text-primary);
    background: var(--bg-tertiary);
}

.theme-icon {
    display: inline-flex;
    align-items: center;
    justify-content: center;
    font-size: 16px;
}

/* Animation for icon change */
.theme-icon i {
    transition: transform 0.3s ease;
}

[data-theme="dark"] .theme-icon i {
    transform: rotate(180deg);
}

/* Responsive text visibility */
@media (max-width: 1024px) {
    .theme-text {
        display: none !important;
    }
}

/* Focus styles */
.theme-toggle-btn:focus,
.theme-toggle-mobile:focus {
    box-shadow: 0 0 0 2px var(--color-primary);
}

/* Theme transition for smooth switching */
.theme-toggle-btn,
.theme-toggle-mobile {
    transition: all 0.3s ease;
}
</style>

{{-- JavaScript for additional theme toggle functionality --}}
<script>
document.addEventListener('DOMContentLoaded', function() {
    // Add keyboard shortcut hint to tooltips
    const toggleButtons = document.querySelectorAll('[data-theme-toggle]');
    toggleButtons.forEach(button => {
        const originalTitle = button.title;
        button.title = originalTitle + ' (Ctrl+Shift+D)';
    });

    // Listen for theme changes to update ARIA attributes
    document.addEventListener('themechange', function(e) {
        toggleButtons.forEach(button => {
            button.setAttribute('aria-label', `Switch to ${e.detail.theme === 'dark' ? 'light' : 'dark'} mode`);
        });
    });

    // Set initial ARIA attributes
    toggleButtons.forEach(button => {
        const currentTheme = document.documentElement.getAttribute('data-theme') || 'light';
        button.setAttribute('aria-label', `Switch to ${currentTheme === 'dark' ? 'light' : 'dark'} mode`);
        button.setAttribute('role', 'switch');
        button.setAttribute('aria-checked', currentTheme === 'dark' ? 'true' : 'false');
    });
});
</script>