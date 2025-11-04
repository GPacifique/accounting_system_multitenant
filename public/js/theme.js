/**
 * Theme Management Service
 * Handles light/dark mode toggling and persistence
 */

class ThemeManager {
    constructor() {
        this.STORAGE_KEY = 'siteledger-theme';
        this.LIGHT_THEME = 'light';
        this.DARK_THEME = 'dark';
        this.currentTheme = this.getStoredTheme() || this.getSystemTheme();
        
        this.init();
    }

    /**
     * Initialize theme manager
     */
    init() {
        this.applyTheme(this.currentTheme);
        this.bindEvents();
        this.updateToggleButton();
        
        // Listen for system theme changes
        if (window.matchMedia) {
            window.matchMedia('(prefers-color-scheme: dark)').addEventListener('change', (e) => {
                if (!this.getStoredTheme()) {
                    this.setTheme(e.matches ? this.DARK_THEME : this.LIGHT_THEME);
                }
            });
        }
    }

    /**
     * Get stored theme from localStorage
     */
    getStoredTheme() {
        return localStorage.getItem(this.STORAGE_KEY);
    }

    /**
     * Get system theme preference
     */
    getSystemTheme() {
        if (window.matchMedia && window.matchMedia('(prefers-color-scheme: dark)').matches) {
            return this.DARK_THEME;
        }
        return this.LIGHT_THEME;
    }

    /**
     * Apply theme to document
     */
    applyTheme(theme) {
        document.documentElement.setAttribute('data-theme', theme);
        document.body.className = document.body.className.replace(/theme-\w+/g, '');
        document.body.classList.add(`theme-${theme}`);
        
        // Update meta theme-color for mobile browsers
        const metaThemeColor = document.querySelector('meta[name="theme-color"]');
        if (metaThemeColor) {
            metaThemeColor.setAttribute('content', theme === this.DARK_THEME ? '#1f2937' : '#ffffff');
        }
    }

    /**
     * Set theme and persist to storage
     */
    setTheme(theme) {
        this.currentTheme = theme;
        localStorage.setItem(this.STORAGE_KEY, theme);
        this.applyTheme(theme);
        this.updateToggleButton();
        this.dispatchThemeChangeEvent();
    }

    /**
     * Toggle between light and dark theme
     */
    toggleTheme() {
        const newTheme = this.currentTheme === this.LIGHT_THEME ? this.DARK_THEME : this.LIGHT_THEME;
        this.setTheme(newTheme);
    }

    /**
     * Get current theme
     */
    getCurrentTheme() {
        return this.currentTheme;
    }

    /**
     * Check if current theme is dark
     */
    isDarkMode() {
        return this.currentTheme === this.DARK_THEME;
    }

    /**
     * Bind event listeners
     */
    bindEvents() {
        // Theme toggle buttons
        document.addEventListener('click', (e) => {
            if (e.target.matches('[data-theme-toggle]') || e.target.closest('[data-theme-toggle]')) {
                e.preventDefault();
                this.toggleTheme();
            }
        });

        // Keyboard shortcut (Ctrl/Cmd + Shift + D)
        document.addEventListener('keydown', (e) => {
            if ((e.ctrlKey || e.metaKey) && e.shiftKey && e.key === 'D') {
                e.preventDefault();
                this.toggleTheme();
            }
        });
    }

    /**
     * Update toggle button appearance
     */
    updateToggleButton() {
        const toggleButtons = document.querySelectorAll('[data-theme-toggle]');
        toggleButtons.forEach(button => {
            const icon = button.querySelector('.theme-icon');
            const text = button.querySelector('.theme-text');
            
            if (icon) {
                icon.innerHTML = this.isDarkMode() ? 
                    '<i class="fas fa-sun"></i>' : 
                    '<i class="fas fa-moon"></i>';
            }
            
            if (text) {
                text.textContent = this.isDarkMode() ? 'Light Mode' : 'Dark Mode';
            }

            // Update button title/tooltip
            button.title = `Switch to ${this.isDarkMode() ? 'light' : 'dark'} mode`;
        });
    }

    /**
     * Dispatch custom theme change event
     */
    dispatchThemeChangeEvent() {
        const event = new CustomEvent('themechange', {
            detail: { theme: this.currentTheme }
        });
        document.dispatchEvent(event);
    }

    /**
     * Add smooth transition for theme changes
     */
    enableTransitions() {
        document.body.style.transition = 'background-color 0.3s ease, color 0.3s ease';
        
        // Remove transition after animation completes
        setTimeout(() => {
            document.body.style.transition = '';
        }, 300);
    }
}

// Initialize theme manager when DOM is loaded
if (document.readyState === 'loading') {
    document.addEventListener('DOMContentLoaded', () => {
        window.themeManager = new ThemeManager();
    });
} else {
    window.themeManager = new ThemeManager();
}

// Export for module usage
if (typeof module !== 'undefined' && module.exports) {
    module.exports = ThemeManager;
}