import './bootstrap';

import Alpine from 'alpinejs';

window.Alpine = Alpine;

Alpine.start();
/* ===== GENERAL JS FOR DASHBOARD ===== */

// ✅ Sidebar Toggle (for mobile)
document.addEventListener("DOMContentLoaded", () => {
    const sidebar = document.querySelector(".sidebar");
    const toggleBtn = document.querySelector("#sidebarToggle");

    if (toggleBtn && sidebar) {
        toggleBtn.addEventListener("click", () => {
            sidebar.classList.toggle("active");
        });
    }
});

// ✅ Dropdown Menus
document.querySelectorAll(".dropdown-toggle").forEach((btn) => {
    btn.addEventListener("click", (e) => {
        e.preventDefault();
        const menu = btn.nextElementSibling;
        if (menu) {
            menu.classList.toggle("show");
        }
    });
});

// ✅ Auto-hide alerts
setTimeout(() => {
    document.querySelectorAll(".alert").forEach((alert) => {
        alert.style.transition = "opacity 0.5s ease";
        alert.style.opacity = "0";
        setTimeout(() => alert.remove(), 500);
    });
}, 3000);

// ✅ Confirm before deleting
document.querySelectorAll("[data-confirm], .btn-danger[data-confirm]").forEach((el) => {
    el.addEventListener("click", (e) => {
        const msg = el.getAttribute('data-confirm') || "Are you sure you want to proceed?";
        if (!confirm(msg)) {
            e.preventDefault();
            e.stopPropagation();
        }
    });
});

// ✅ Simple table search (by input with id="tableSearch")
document.addEventListener("input", (e) => {
    if (e.target.id === "tableSearch") {
        const search = e.target.value.toLowerCase();
        document.querySelectorAll(".table tbody tr").forEach((row) => {
            row.style.display = row.innerText.toLowerCase().includes(search)
                ? ""
                : "none";
        });
    }
});

/* ===== BUTTON ENHANCEMENTS ===== */
document.addEventListener('click', function (e) {
    const target = e.target.closest('.btn');
    if (!target) return;

    // Ripple
    const rect = target.getBoundingClientRect();
    let ripple = target.querySelector('.ripple');
    if (!ripple) {
        ripple = document.createElement('span');
        ripple.className = 'ripple';
        target.appendChild(ripple);
    }
    const rx = (e.clientX || (rect.left + rect.width/2)) - rect.left;
    const ry = (e.clientY || (rect.top + rect.height/2)) - rect.top;
    ripple.style.setProperty('--x', rx + 'px');
    ripple.style.setProperty('--y', ry + 'px');
    ripple.style.left = '0';
    ripple.style.top = '0';
    const after = getComputedStyle(ripple, '::after');
    // trigger animation by toggling class
    target.classList.remove('rippling');
    // force reflow
    void target.offsetWidth;
    // position pseudo element
    ripple.style.setProperty('--mouse-x', rx + 'px');
    ripple.style.setProperty('--mouse-y', ry + 'px');
    target.classList.add('rippling');
    setTimeout(() => target.classList.remove('rippling'), 500);
});

// Auto loading state on form submits
document.addEventListener('submit', function (e) {
    const form = e.target;
    // find the button that triggered submit if possible
    const submitter = form.querySelector('[type="submit"].btn');
    if (!submitter || submitter.hasAttribute('data-noloading')) return;
    if (submitter.dataset.loading === 'true') return;

    const labelNodes = Array.from(submitter.childNodes).filter(n => n.nodeType === 3 || n.nodeType === 1);
    labelNodes.forEach(n => { if (n.nodeType === 1) n.classList.add('btn-label'); });
    submitter.dataset.loading = 'true';

    const spinner = document.createElement('span');
    spinner.className = 'btn-spinner';
    submitter.appendChild(spinner);
});

// Loading state on direct button clicks (non-form actions)
document.addEventListener('click', function (e) {
    const btn = e.target.closest('.btn[data-loading-on-click]');
    if (!btn) return;
    if (btn.dataset.loading === 'true') return;
    btn.dataset.loading = 'true';
    const spinner = document.createElement('span');
    spinner.className = 'btn-spinner';
    btn.appendChild(spinner);
});

// Tooltips via simple title attribute (opt-in)
document.querySelectorAll('[data-tooltip]').forEach(el => {
    el.addEventListener('mouseenter', () => {
        const title = el.getAttribute('data-tooltip');
        if (!title) return;
        el.setAttribute('title', title);
    }, { once: true });
});
