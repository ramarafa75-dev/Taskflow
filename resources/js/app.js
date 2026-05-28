// resources/js/app.js

import 'bootstrap';
import Chart from 'chart.js/auto';

// ============================================
// 1. CSRF Token setup untuk fetch requests
// ============================================
const csrfToken = document.querySelector('meta[name="csrf-token"]')?.content;

// ============================================
// 2. AUTO-DISMISS FLASH TOAST
// ============================================
document.addEventListener('DOMContentLoaded', () => {
    const toast = document.getElementById('flashToast');
    if (toast) {
        setTimeout(() => {
            toast.style.transition = 'opacity 0.5s';
            toast.style.opacity = '0';
            setTimeout(() => toast.remove(), 500);
        }, 4000);
    }
});

// ============================================
// 3. SIDEBAR TOGGLE (mobile)
// ============================================
document.addEventListener('DOMContentLoaded', () => {
    const toggleBtn = document.getElementById('sidebarToggle');
    const sidebar   = document.querySelector('.sidebar');
    const overlay   = document.getElementById('sidebarOverlay');

    if (toggleBtn) {
        toggleBtn.addEventListener('click', () => {
            sidebar.classList.toggle('open');
            overlay.classList.toggle('show');
        });
    }

    if (overlay) {
        overlay.addEventListener('click', () => {
            sidebar.classList.remove('open');
            overlay.classList.remove('show');
        });
    }
});

// ============================================
// 4. CHARTS (Dashboard only)
// ============================================
document.addEventListener('DOMContentLoaded', () => {

    // --- Activity Bar Chart ---
    const activityCtx = document.getElementById('activityChart');
    if (activityCtx && typeof chartLabels !== 'undefined') {
        new Chart(activityCtx, {
            type: 'bar',
            data: {
                labels: chartLabels,
                datasets: [
                    {
                        label: 'Selesai',
                        data: chartCompleted,
                        backgroundColor: 'rgba(56, 161, 105, 0.85)',
                        borderRadius: 6,
                        borderSkipped: false,
                    },
                    {
                        label: 'Dibuat',
                        data: chartPending,
                        backgroundColor: 'rgba(226, 232, 240, 0.9)',
                        borderRadius: 6,
                        borderSkipped: false,
                    },
                ],
            },
            options: {
                responsive: true,
                maintainAspectRatio: false,
                plugins: {
                    legend: { display: false },
                    tooltip: {
                        backgroundColor: '#1a0505',
                        titleFont: { family: "'Plus Jakarta Sans', sans-serif", weight: '700' },
                        bodyFont: { family: "'Plus Jakarta Sans', sans-serif" },
                        cornerRadius: 8,
                        padding: 12,
                    },
                },
                scales: {
                    x: {
                        grid: { display: false },
                        border: { display: false },
                        ticks: {
                            font: { family: "'Plus Jakarta Sans', sans-serif", size: 11 },
                            color: '#7a6060',
                        },
                    },
                    y: {
                        grid: { color: 'rgba(0,0,0,0.04)' },
                        border: { display: false },
                        ticks: {
                            stepSize: 1,
                            font: { family: "'DM Mono', monospace", size: 11 },
                            color: '#7a6060',
                        },
                        beginAtZero: true,
                    },
                },
            },
        });
    }

    // --- Category Donut Chart ---
    const categoryCtx = document.getElementById('categoryChart');
    if (categoryCtx && typeof categoryData !== 'undefined') {
        const catLabels = ['Personal', 'Kerja', 'Belanja', 'Kesehatan', 'Lainnya'];
        const catValues = [
            categoryData.personal,
            categoryData.work,
            categoryData.shopping,
            categoryData.health,
            categoryData.other,
        ];
        const catColors = ['#e53e3e', '#667eea', '#d69e2e', '#38a169', '#a0aec0'];

        new Chart(categoryCtx, {
            type: 'doughnut',
            data: {
                labels: catLabels,
                datasets: [{
                    data: catValues,
                    backgroundColor: catColors,
                    borderColor: '#fff',
                    borderWidth: 3,
                    hoverOffset: 6,
                }],
            },
            options: {
                responsive: true,
                cutout: '68%',
                plugins: {
                    legend: {
                        position: 'bottom',
                        labels: {
                            font: { family: "'Plus Jakarta Sans', sans-serif", size: 11 },
                            color: '#7a6060',
                            padding: 12,
                            usePointStyle: true,
                            pointStyleWidth: 8,
                        },
                    },
                    tooltip: {
                        backgroundColor: '#1a0505',
                        titleFont: { family: "'Plus Jakarta Sans', sans-serif", weight: '700' },
                        bodyFont: { family: "'Plus Jakarta Sans', sans-serif" },
                        cornerRadius: 8,
                        padding: 10,
                    },
                },
            },
        });
    }
});

// ============================================
// 5. TOGGLE TODO (AJAX)
// ============================================
window.toggleTodo = function(id, btn) {
    fetch(`/todos/${id}/toggle`, {
        method: 'PATCH',
        headers: {
            'X-CSRF-TOKEN': csrfToken,
            'Accept': 'application/json',
        },
    })
    .then(r => r.json())
    .then(data => {
        if (!data.success) return;

        const item = document.getElementById(`todo-${id}`);
        const icon = btn.querySelector('i');

        if (data.is_completed) {
            item.classList.add('done');
            icon.className = 'bi bi-check-circle-fill';
            showToast(data.message, 'success');
        } else {
            item.classList.remove('done');
            icon.className = 'bi bi-circle';
            showToast(data.message, 'info');
        }
    })
    .catch(() => showToast('Terjadi kesalahan.', 'error'));
};

// ============================================
// 6. DELETE TODO (AJAX)
// ============================================
window.deleteTodo = function(id) {
    if (!confirm('Yakin ingin menghapus to-do ini?')) return;

    fetch(`/todos/${id}`, {
        method: 'DELETE',
        headers: {
            'X-CSRF-TOKEN': csrfToken,
            'Accept': 'application/json',
        },
    })
    .then(r => r.json())
    .then(data => {
        if (!data.success) return;
        const item = document.getElementById(`todo-${id}`);
        item.style.transition = 'opacity 0.3s, transform 0.3s';
        item.style.opacity    = '0';
        item.style.transform  = 'translateX(20px)';
        setTimeout(() => item.remove(), 300);
        showToast(data.message, 'success');
    })
    .catch(() => showToast('Terjadi kesalahan.', 'error'));
};

// ============================================
// 7. EDIT TODO (AJAX)
// ============================================
let currentEditId = null;

window.openEdit = function(id, title, description, priority, category, dueDate) {
    currentEditId = id;
    document.getElementById('editTitle').value       = title;
    document.getElementById('editDescription').value = description;
    document.getElementById('editPriority').value    = priority;
    document.getElementById('editCategory').value    = category;
    document.getElementById('editDueDate').value     = dueDate || '';

    const modal = new bootstrap.Modal(document.getElementById('editTodoModal'));
    modal.show();
};

document.addEventListener('DOMContentLoaded', () => {
    const saveBtn = document.getElementById('saveEditBtn');
    if (saveBtn) {
        saveBtn.addEventListener('click', () => {
            if (!currentEditId) return;

            const payload = {
                title:       document.getElementById('editTitle').value,
                description: document.getElementById('editDescription').value,
                priority:    document.getElementById('editPriority').value,
                category:    document.getElementById('editCategory').value,
                due_date:    document.getElementById('editDueDate').value || null,
                _method:     'PUT',
            };

            fetch(`/todos/${currentEditId}`, {
                method: 'POST',
                headers: {
                    'X-CSRF-TOKEN': csrfToken,
                    'Content-Type': 'application/json',
                    'Accept': 'application/json',
                },
                body: JSON.stringify(payload),
            })
            .then(r => r.json())
            .then(data => {
                if (!data.success) return;
                bootstrap.Modal.getInstance(document.getElementById('editTodoModal')).hide();
                showToast(data.message, 'success');
                setTimeout(() => window.location.reload(), 800);
            })
            .catch(() => showToast('Terjadi kesalahan.', 'error'));
        });
    }
});

// ============================================
// 8. CLEAR COMPLETED (AJAX)
// ============================================
window.clearCompleted = function() {
    if (!confirm('Hapus semua to-do yang sudah selesai?')) return;

    fetch('/todos/action/clear-completed', {
        method: 'DELETE',
        headers: {
            'X-CSRF-TOKEN': csrfToken,
            'Accept': 'application/json',
        },
    })
    .then(r => r.json())
    .then(data => {
        showToast(data.message, 'success');
        setTimeout(() => window.location.reload(), 800);
    });
};

// ============================================
// 9. TOAST HELPER
// ============================================
function showToast(message, type = 'success') {
    const existing = document.querySelector('.alert-toast');
    if (existing) existing.remove();

    const icon = type === 'success' ? 'bi-check-circle-fill' : 'bi-info-circle-fill';
    const toast = document.createElement('div');
    toast.className = `alert-toast ${type === 'success' ? 'success' : 'error'}`;
    toast.innerHTML = `
        <i class="bi ${icon} me-2"></i>
        ${message}
        <button onclick="this.parentElement.remove()" class="toast-close"><i class="bi bi-x"></i></button>
    `;
    document.body.appendChild(toast);

    setTimeout(() => {
        toast.style.transition = 'opacity 0.5s';
        toast.style.opacity    = '0';
        setTimeout(() => toast.remove(), 500);
    }, 4000);
}
