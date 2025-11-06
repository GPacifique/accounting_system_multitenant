// Enhanced Sidebar Functionality
document.addEventListener('DOMContentLoaded', function() {
    const sidebar = document.querySelector('.sidebar-wrapper');
    const overlay = document.querySelector('.sidebar-overlay');
    const mobileToggle = document.querySelector('.mobile-sidebar-toggle');
    const body = document.body;

    // Mobile sidebar toggle functionality
    if (mobileToggle) {
        mobileToggle.addEventListener('click', function() {
            toggleSidebar();
        });
    }

    // Overlay click to close sidebar
    if (overlay) {
        overlay.addEventListener('click', function() {
            closeSidebar();
        });
    }

    // Close sidebar on escape key
    document.addEventListener('keydown', function(e) {
        if (e.key === 'Escape' && sidebar && sidebar.classList.contains('show')) {
            closeSidebar();
        }
    });

    // Handle window resize
    window.addEventListener('resize', function() {
        if (window.innerWidth > 992 && sidebar && sidebar.classList.contains('show')) {
            closeSidebar();
        }
    });

    function toggleSidebar() {
        if (sidebar && overlay && mobileToggle) {
            const isOpen = sidebar.classList.contains('show');
            
            if (isOpen) {
                closeSidebar();
            } else {
                openSidebar();
            }
        }
    }

    function openSidebar() {
        if (sidebar && overlay && mobileToggle) {
            sidebar.classList.add('show');
            overlay.classList.add('show');
            mobileToggle.classList.add('active');
            body.style.overflow = 'hidden';
        }
    }

    function closeSidebar() {
        if (sidebar && overlay && mobileToggle) {
            sidebar.classList.remove('show');
            overlay.classList.remove('show');
            mobileToggle.classList.remove('active');
            body.style.overflow = '';
        }
    }

    // Enhanced link hover effects with mouse tracking
    const sidebarLinks = document.querySelectorAll('.sidebar-link, .sidebar-quick-btn, .footer-action-btn');
    
    sidebarLinks.forEach(link => {
        link.addEventListener('mousemove', function(e) {
            const rect = this.getBoundingClientRect();
            const x = ((e.clientX - rect.left) / rect.width) * 100;
            const y = ((e.clientY - rect.top) / rect.height) * 100;
            
            this.style.setProperty('--mouse-x', x + '%');
            this.style.setProperty('--mouse-y', y + '%');
        });
    });

    // Auto-collapse sidebar on route change (for mobile)
    const currentLocation = window.location.pathname;
    const sidebarNavLinks = document.querySelectorAll('.sidebar-link[href]');
    
    sidebarNavLinks.forEach(link => {
        link.addEventListener('click', function() {
            // Small delay to allow navigation to start
            setTimeout(() => {
                if (window.innerWidth <= 992) {
                    closeSidebar();
                }
            }, 150);
        });
    });

    // Add loading states for links
    sidebarNavLinks.forEach(link => {
        link.addEventListener('click', function() {
            // Don't add loading state if it's the current page
            if (this.getAttribute('href') !== currentLocation) {
                this.style.opacity = '0.7';
                this.style.pointerEvents = 'none';
                
                // Create loading indicator
                const icon = this.querySelector('.sidebar-icon');
                if (icon) {
                    const originalIcon = icon.innerHTML;
                    icon.innerHTML = '<i class="fas fa-spinner fa-spin"></i>';
                    
                    // Restore after navigation starts
                    setTimeout(() => {
                        icon.innerHTML = originalIcon;
                        this.style.opacity = '';
                        this.style.pointerEvents = '';
                    }, 1000);
                }
            }
        });
    });

    // Enhanced theme toggle functionality
    const themeToggleMini = document.querySelector('.theme-toggle-mini');
    
    if (themeToggleMini) {
        themeToggleMini.addEventListener('click', function() {
            // Trigger the main theme toggle functionality
            const mainThemeToggle = document.querySelector('.theme-toggle-btn') || 
                                  document.querySelector('[data-theme-toggle]');
            
            if (mainThemeToggle) {
                mainThemeToggle.click();
            } else {
                // Fallback theme toggle
                toggleThemeManual();
            }
        });
    }

    function toggleThemeManual() {
        const html = document.documentElement;
        const currentTheme = html.getAttribute('data-theme') || 'light';
        const newTheme = currentTheme === 'light' ? 'dark' : 'light';
        
        html.setAttribute('data-theme', newTheme);
        localStorage.setItem('theme', newTheme);
        
        // Update theme toggle icons
        updateThemeIcons(newTheme);
    }

    function updateThemeIcons(theme) {
        const themeIcons = document.querySelectorAll('.theme-toggle-mini i');
        themeIcons.forEach(icon => {
            if (theme === 'dark') {
                icon.className = 'fas fa-sun';
            } else {
                icon.className = 'fas fa-moon';
            }
        });
    }

    // Badge animation on count changes
    const badges = document.querySelectorAll('.sidebar-badge');
    badges.forEach(badge => {
        // Store original value for comparison
        const originalValue = badge.textContent.trim();
        badge.dataset.originalValue = originalValue;
        
        // Create observer for content changes
        const observer = new MutationObserver(function(mutations) {
            mutations.forEach(function(mutation) {
                if (mutation.type === 'childList' || mutation.type === 'characterData') {
                    const newValue = badge.textContent.trim();
                    if (newValue !== badge.dataset.originalValue && newValue !== '') {
                        // Animate badge change
                        badge.style.transform = 'scale(1.3)';
                        badge.style.boxShadow = '0 0 16px rgba(59, 130, 246, 0.5)';
                        
                        setTimeout(() => {
                            badge.style.transform = '';
                            badge.style.boxShadow = '';
                        }, 300);
                        
                        badge.dataset.originalValue = newValue;
                    }
                }
            });
        });
        
        observer.observe(badge, {
            childList: true,
            characterData: true,
            subtree: true
        });
    });

    // Smooth scrolling for sidebar navigation
    const sidebarNav = document.querySelector('.sidebar-nav');
    if (sidebarNav) {
        // Add smooth scrolling behavior
        sidebarNav.style.scrollBehavior = 'smooth';
        
        // Auto-scroll to active link on page load
        const activeLink = sidebarNav.querySelector('.sidebar-link.active');
        if (activeLink) {
            setTimeout(() => {
                activeLink.scrollIntoView({
                    behavior: 'smooth',
                    block: 'center'
                });
            }, 100);
        }
    }

    // Enhanced tooltip functionality for small screens
    if (window.innerWidth <= 576px) {
        const linksWithText = document.querySelectorAll('.sidebar-link');
        linksWithText.forEach(link => {
            const text = link.querySelector('.sidebar-text');
            if (text) {
                link.setAttribute('title', text.textContent);
            }
        });
    }

    // Quick action buttons functionality
    const quickBtns = document.querySelectorAll('.sidebar-quick-btn');
    quickBtns.forEach(btn => {
        btn.addEventListener('click', function() {
            // Add click animation
            this.style.transform = 'scale(0.95)';
            setTimeout(() => {
                this.style.transform = '';
            }, 150);
        });
    });

    // Initialize tooltips for action buttons
    const actionBtns = document.querySelectorAll('.footer-action-btn[title]');
    actionBtns.forEach(btn => {
        btn.addEventListener('mouseenter', function() {
            showTooltip(this, this.getAttribute('title'));
        });
        
        btn.addEventListener('mouseleave', function() {
            hideTooltip();
        });
    });

    let currentTooltip = null;

    function showTooltip(element, text) {
        hideTooltip(); // Remove any existing tooltip
        
        const tooltip = document.createElement('div');
        tooltip.className = 'sidebar-tooltip';
        tooltip.textContent = text;
        tooltip.style.cssText = `
            position: absolute;
            background: rgba(0, 0, 0, 0.8);
            color: white;
            padding: 6px 10px;
            border-radius: 6px;
            font-size: 12px;
            font-weight: 500;
            white-space: nowrap;
            z-index: 1000;
            pointer-events: none;
            opacity: 0;
            transform: translateY(5px);
            transition: opacity 0.2s ease, transform 0.2s ease;
        `;
        
        document.body.appendChild(tooltip);
        
        const rect = element.getBoundingClientRect();
        tooltip.style.left = rect.right + 10 + 'px';
        tooltip.style.top = rect.top + (rect.height / 2) - (tooltip.offsetHeight / 2) + 'px';
        
        // Trigger animation
        requestAnimationFrame(() => {
            tooltip.style.opacity = '1';
            tooltip.style.transform = 'translateY(0)';
        });
        
        currentTooltip = tooltip;
    }

    function hideTooltip() {
        if (currentTooltip) {
            currentTooltip.style.opacity = '0';
            currentTooltip.style.transform = 'translateY(5px)';
            setTimeout(() => {
                if (currentTooltip && currentTooltip.parentNode) {
                    currentTooltip.parentNode.removeChild(currentTooltip);
                }
                currentTooltip = null;
            }, 200);
        }
    }

    // Initialize sidebar animations
    initializeSidebarAnimations();

    function initializeSidebarAnimations() {
        // Stagger animation for sidebar links on page load
        const links = document.querySelectorAll('.sidebar-link');
        links.forEach((link, index) => {
            link.style.opacity = '0';
            link.style.transform = 'translateX(-20px)';
            
            setTimeout(() => {
                link.style.transition = 'opacity 0.3s ease, transform 0.3s ease';
                link.style.opacity = '1';
                link.style.transform = 'translateX(0)';
            }, 100 + (index * 50));
        });

        // Animate quick action buttons
        const quickActions = document.querySelectorAll('.sidebar-quick-btn');
        quickActions.forEach((btn, index) => {
            btn.style.opacity = '0';
            btn.style.transform = 'scale(0.8)';
            
            setTimeout(() => {
                btn.style.transition = 'opacity 0.3s ease, transform 0.3s ease';
                btn.style.opacity = '1';
                btn.style.transform = 'scale(1)';
            }, 300 + (index * 100));
        });

        // Animate user info
        const userInfo = document.querySelector('.user-info');
        if (userInfo) {
            userInfo.style.opacity = '0';
            userInfo.style.transform = 'translateY(20px)';
            
            setTimeout(() => {
                userInfo.style.transition = 'opacity 0.4s ease, transform 0.4s ease';
                userInfo.style.opacity = '1';
                userInfo.style.transform = 'translateY(0)';
            }, 800);
        }
    }

    // Performance optimization: Intersection Observer for animations
    const observerOptions = {
        threshold: 0.1,
        rootMargin: '50px'
    };

    const sidebarObserver = new IntersectionObserver((entries) => {
        entries.forEach(entry => {
            if (entry.isIntersecting) {
                entry.target.classList.add('animate-in');
            }
        });
    }, observerOptions);

    // Observe sidebar sections for scroll-based animations
    const sections = document.querySelectorAll('.sidebar-section');
    sections.forEach(section => {
        sidebarObserver.observe(section);
    });

    // Add notification system for sidebar events
    const notificationEvents = [
        { selector: '.sidebar-badge', event: 'DOMSubtreeModified' },
        { selector: '.role-badge', event: 'DOMSubtreeModified' }
    ];

    notificationEvents.forEach(({ selector, event }) => {
        const elements = document.querySelectorAll(selector);
        elements.forEach(element => {
            element.addEventListener(event, function() {
                // Pulse animation for notifications
                this.style.animation = 'none';
                requestAnimationFrame(() => {
                    this.style.animation = 'pulse-glow 0.6s ease';
                });
            });
        });
    });

    console.log('Enhanced Sidebar initialized successfully');
});