// Theme validation script for testing
document.addEventListener('DOMContentLoaded', function() {
    console.log('🌙 Theme Toggle Validation');
    
    // Check if theme manager is loaded
    if (window.themeManager) {
        console.log('✅ Theme Manager loaded successfully');
        console.log('Current theme:', window.themeManager.getCurrentTheme());
    } else {
        console.log('❌ Theme Manager not found');
    }
    
    // Check if theme toggle buttons exist
    const toggleButtons = document.querySelectorAll('[data-theme-toggle]');
    console.log(`Found ${toggleButtons.length} theme toggle button(s)`);
    
    // Check if CSS variables are defined
    const root = document.documentElement;
    const primaryBg = getComputedStyle(root).getPropertyValue('--bg-primary');
    const textPrimary = getComputedStyle(root).getPropertyValue('--text-primary');
    
    if (primaryBg && textPrimary) {
        console.log('✅ CSS variables are defined');
        console.log('Background:', primaryBg);
        console.log('Text:', textPrimary);
    } else {
        console.log('❌ CSS variables not found');
    }
    
    // Test theme switching
    if (window.themeManager) {
        console.log('Testing theme switch...');
        const originalTheme = window.themeManager.getCurrentTheme();
        
        setTimeout(() => {
            window.themeManager.toggleTheme();
            console.log('Theme switched to:', window.themeManager.getCurrentTheme());
            
            setTimeout(() => {
                window.themeManager.setTheme(originalTheme);
                console.log('Theme restored to:', window.themeManager.getCurrentTheme());
            }, 1000);
        }, 1000);
    }
});