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
document.querySelectorAll(".btn-danger").forEach((btn) => {
    btn.addEventListener("click", (e) => {
        if (!confirm("Are you sure you want to delete this item?")) {
            e.preventDefault();
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
