// Chart.js Dark Mode Enhancements
document.addEventListener('DOMContentLoaded', function() {
    // Chart.js theme-aware colors
    function getChartColors() {
        const isDark = document.documentElement.getAttribute('data-theme') === 'dark';
        return {
            textColor: isDark ? '#f8fafc' : '#0f172a',
            gridColor: isDark ? 'rgba(148, 163, 184, 0.1)' : 'rgba(15, 23, 42, 0.04)',
            borderColor: isDark ? 'rgba(148, 163, 184, 0.2)' : 'rgba(15, 23, 42, 0.1)',
            backgroundColor: isDark ? 'rgba(30, 41, 59, 0.8)' : 'rgba(255, 255, 255, 0.9)'
        };
    }

    // Function to update existing charts
    function updateChartsForTheme() {
        const colors = getChartColors();
        
        // Update all Chart.js instances
        Chart.instances.forEach(chart => {
            // Update scales
            if (chart.options.scales) {
                if (chart.options.scales.x) {
                    chart.options.scales.x.ticks = chart.options.scales.x.ticks || {};
                    chart.options.scales.x.ticks.color = colors.textColor;
                    chart.options.scales.x.grid = chart.options.scales.x.grid || {};
                    chart.options.scales.x.grid.color = colors.gridColor;
                }
                if (chart.options.scales.y) {
                    chart.options.scales.y.ticks = chart.options.scales.y.ticks || {};
                    chart.options.scales.y.ticks.color = colors.textColor;
                    chart.options.scales.y.grid = chart.options.scales.y.grid || {};
                    chart.options.scales.y.grid.color = colors.gridColor;
                }
            }
            
            // Update legend
            if (chart.options.plugins && chart.options.plugins.legend) {
                chart.options.plugins.legend.labels = chart.options.plugins.legend.labels || {};
                chart.options.plugins.legend.labels.color = colors.textColor;
            }
            
            // Update tooltip
            if (chart.options.plugins && chart.options.plugins.tooltip) {
                chart.options.plugins.tooltip.backgroundColor = colors.backgroundColor;
                chart.options.plugins.tooltip.titleColor = colors.textColor;
                chart.options.plugins.tooltip.bodyColor = colors.textColor;
            }
            
            chart.update();
        });
    }

    // Listen for theme changes
    document.addEventListener('themechange', updateChartsForTheme);
    
    // Apply initial theme
    updateChartsForTheme();
});