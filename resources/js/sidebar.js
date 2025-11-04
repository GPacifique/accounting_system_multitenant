/**
 * Sidebar Enhancement Script
 * Adds interactive features and smooth animations to the sidebar
 */

document.addEventListener('DOMContentLoaded', function() {
    // ===== Initialize Sidebar Elements =====
    const sidebar = document.querySelector('.sidebar-wrapper');
    const sidebarLinks = document.querySelectorAll('.sidebar-link');
    const sidebarHeader = document.querySelector('.sidebar-header');
    const sidebarFooter = document.querySelector('.sidebar-footer');

    // ===== Add Mouse Tracking for Gradient Background =====
    if (sidebar) {
        sidebar.addEventListener('mousemove', function(e) {
            const rect = sidebar.getBoundingClientRect();
            const x = e.clientX - rect.left;
            const y = e.clientY - rect.top;
            
            // Update CSS variables for mouse position
            sidebar.style.setProperty('--mouse-x', x + 'px');
            sidebar.style.setProperty('--mouse-y', y + 'px');
        });
    }

    // ===== Add Animation to Sidebar Links =====
    sidebarLinks.forEach((link, index) => {
        // Stagger animation on page load
        link.style.animation = `slideInLeft 0.5s ease ${index * 0.05}s both`;

        // Add hover effects
        link.addEventListener('mouseenter', function() {
            // Update mouse position for ripple effect
            this.addEventListener('mousemove', function(e) {
                const rect = this.getBoundingClientRect();
                const x = e.clientX - rect.left;
                const y = e.clientY - rect.top;
                
                this.style.setProperty('--mouse-x', x + 'px');
                this.style.setProperty('--mouse-y', y + 'px');
            });

            // Add scale animation
            this.style.transform = 'scale(1.02)';
        });

        link.addEventListener('mouseleave', function() {
            this.style.transform = '';
        });

        // Check if link is active and add glow effect
        if (link.classList.contains('active')) {
            link.style.animation = `fadeIn 0.5s ease`;
        }
    });

    // ===== Smooth Scrolling =====
    sidebarLinks.forEach(link => {
        link.addEventListener('click', function(e) {
            // Add click animation
            const clickEffect = document.createElement('span');
            clickEffect.style.position = 'absolute';
            clickEffect.style.width = '20px';
            clickEffect.style.height = '20px';
            clickEffect.style.background = 'rgba(251, 191, 36, 0.5)';
            clickEffect.style.borderRadius = '50%';
            clickEffect.style.pointerEvents = 'none';
            clickEffect.style.animation = 'pulse-glow 0.6s ease-out';
            
            const rect = this.getBoundingClientRect();
            clickEffect.style.left = (e.clientX - rect.left - 10) + 'px';
            clickEffect.style.top = (e.clientY - rect.top - 10) + 'px';
            
            this.appendChild(clickEffect);
            setTimeout(() => clickEffect.remove(), 600);
        });
    });

    // ===== Header Animations =====
    if (sidebarHeader) {
        sidebarHeader.style.animation = `slideInLeft 0.5s ease`;
        
        const logo = sidebarHeader.querySelector('.sidebar-logo');
        if (logo) {
            logo.addEventListener('mouseenter', function() {
                this.style.animation = `spin 0.6s ease-in-out`;
            });
        }
    }

    // ===== Footer Animations =====
    if (sidebarFooter) {
        sidebarFooter.style.animation = `slideInLeft 0.5s ease 0.2s both`;

        const roleCard = sidebarFooter.querySelector('.role-badge');
        if (roleCard) {
            roleCard.addEventListener('mouseenter', function() {
                this.style.animation = `pulse-glow 2s infinite`;
            });
            roleCard.addEventListener('mouseleave', function() {
                this.style.animation = '';
            });
        }
    }

    // ===== Add Smooth Divider Animations =====
    const dividers = document.querySelectorAll('.sidebar-divider');
    dividers.forEach((divider, index) => {
        divider.style.animation = `fadeIn 0.5s ease ${0.1 + index * 0.1}s both`;
    });

    // ===== Keyboard Navigation =====
    document.addEventListener('keydown', function(e) {
        // Ctrl/Cmd + K to focus search or navigation
        if ((e.ctrlKey || e.metaKey) && e.key === 'k') {
            e.preventDefault();
            // Add custom behavior if needed
        }

        // Arrow keys to navigate sidebar items
        if (e.key === 'ArrowDown' || e.key === 'ArrowUp') {
            const activeLink = document.querySelector('.sidebar-link.active');
            if (activeLink) {
                const allLinks = Array.from(sidebarLinks);
                const currentIndex = allLinks.indexOf(activeLink);
                let nextIndex;

                if (e.key === 'ArrowDown') {
                    nextIndex = (currentIndex + 1) % allLinks.length;
                } else {
                    nextIndex = (currentIndex - 1 + allLinks.length) % allLinks.length;
                }

                allLinks[nextIndex].focus();
                allLinks[nextIndex].scrollIntoView({ behavior: 'smooth', block: 'nearest' });
            }
        }
    });

    // ===== Add Tooltip Functionality =====
    sidebarLinks.forEach(link => {
        const title = link.getAttribute('title');
        if (!title) {
            const text = link.querySelector('.sidebar-text')?.textContent || '';
            if (text) {
                link.setAttribute('title', text);
            }
        }
    });

    // ===== Logout Button Enhancement =====
    const logoutBtn = document.querySelector('.logout-btn');
    if (logoutBtn) {
        logoutBtn.addEventListener('click', function(e) {
            // Add click feedback
            this.style.animation = `pulse-glow 0.6s ease-out`;
            
            // Optional: Add confirmation modal here
            // e.preventDefault();
        });

        logoutBtn.addEventListener('mouseenter', function() {
            this.style.transform = 'scale(1.08) rotateZ(10deg)';
        });

        logoutBtn.addEventListener('mouseleave', function() {
            this.style.transform = '';
        });
    }

    // ===== Sidebar Scroll Enhancement =====
    const nav = document.querySelector('.sidebar-nav');
    if (nav) {
        nav.addEventListener('scroll', function() {
            // Add subtle shadow effect when scrolling
            if (this.scrollTop > 0) {
                this.style.boxShadow = 'inset 0 2px 4px rgba(0, 0, 0, 0.1)';
            } else {
                this.style.boxShadow = 'none';
            }
        });
    }

    // ===== Responsive Behavior =====
    const updateResponsive = () => {
        if (window.innerWidth <= 768) {
            sidebar?.classList.add('mobile-mode');
        } else {
            sidebar?.classList.remove('mobile-mode');
        }
    };

    updateResponsive();
    window.addEventListener('resize', updateResponsive);

    // ===== Add Ripple Effect CSS if not exists =====
    if (!document.querySelector('style[data-ripple]')) {
        const rippleStyle = document.createElement('style');
        rippleStyle.setAttribute('data-ripple', 'true');
        rippleStyle.innerHTML = `
            @keyframes spin {
                from { transform: rotate(0deg); }
                to { transform: rotate(360deg); }
            }
        `;
        document.head.appendChild(rippleStyle);
    }

    // ===== Page Transition Animation =====
    window.addEventListener('beforeunload', function() {
        sidebar?.style.animation = 'slideInLeft 0.5s ease reverse';
    });

    console.log('✨ Sidebar enhancement script loaded successfully!');
});

// ===== Utility Functions =====

/**
 * Check if a link is currently active
 */
function isLinkActive(link) {
    return link.classList.contains('active');
}

/**
 * Highlight a link
 */
function highlightLink(selector) {
    document.querySelectorAll('.sidebar-link').forEach(link => {
        link.classList.remove('active');
    });
    document.querySelector(selector)?.classList.add('active');
}

/**
 * Scroll sidebar to element
 */
function scrollSidebarToElement(element) {
    const nav = document.querySelector('.sidebar-nav');
    if (nav && element) {
        element.scrollIntoView({ behavior: 'smooth', block: 'nearest' });
    }
}

/**
 * Add custom notification to footer
 */
function showSidebarNotification(message, duration = 3000) {
    const notification = document.createElement('div');
    notification.style.cssText = `
        position: fixed;
        bottom: 100px;
        left: 30px;
        background: linear-gradient(135deg, #fbbf24 0%, #f59e0b 100%);
        color: #166534;
        padding: 12px 16px;
        border-radius: 8px;
        font-weight: 600;
        font-size: 14px;
        animation: slideInLeft 0.3s ease, slideInLeft 0.3s ease 2.7s reverse;
        box-shadow: 0 4px 12px rgba(251, 191, 36, 0.4);
        z-index: 1001;
    `;
    notification.textContent = message;
    document.body.appendChild(notification);
    
    setTimeout(() => notification.remove(), duration);
}

// Export for use in other scripts
window.SidebarUtils = {
    isLinkActive,
    highlightLink,
    scrollSidebarToElement,
    showSidebarNotification
};
