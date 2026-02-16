// assets/js/admin.js
// Handles admin forms: create user (JSON) and create menu (FormData with file)

async function postJSON(url, data) {
    const res = await fetch(url, {
        method: 'POST',
        credentials: 'same-origin',
        headers: { 'Content-Type': 'application/json', 'Accept': 'application/json' },
        body: JSON.stringify(data)
    });
    const json = res.headers.get('content-type')?.includes('application/json') ? await res.json() : null;
    return { ok: res.ok, status: res.status, json };
}

async function postForm(url, formData) {
    const res = await fetch(url, {
        method: 'POST',
        credentials: 'same-origin',
        body: formData
    });
    const json = res.headers.get('content-type')?.includes('application/json') ? await res.json() : null;
    return { ok: res.ok, status: res.status, json };
}

function showAdminMessage(container, type, messages) {
    container.innerHTML = '';
    const box = document.createElement('div');
    box.className = 'ajax-message ' + type;
    box.innerHTML = Array.isArray(messages) ? '• ' + messages.join('<br>• ') : messages;
    container.appendChild(box);
}

document.addEventListener('DOMContentLoaded', () => {
    const userForm = document.getElementById('add-user-form');
    if (userForm) {
        const container = document.getElementById('add-user-messages');
        userForm.addEventListener('submit', async (e) => {
            e.preventDefault();
            container.innerHTML = '';
            const fd = new FormData(userForm);
            const payload = Object.fromEntries(fd.entries());
            try {
                const r = await postJSON('/56Food/api/admin/users_create.php', payload);
                if (r.ok) {
                    showAdminMessage(container, 'success', [r.json?.message || 'User created']);
                    userForm.reset();
                } else if (r.status === 400) {
                    showAdminMessage(container, 'error', r.json?.errors || [r.json?.error || 'Bad request']);
                } else {
                    showAdminMessage(container, 'error', [r.json?.error || 'Server error']);
                }
            } catch (err) {
                showAdminMessage(container, 'error', ['Network error']);
            }
        });
    }

    const menuForm = document.getElementById('add-menu-form');
    if (menuForm) {
        const container = document.getElementById('add-menu-messages');
        menuForm.addEventListener('submit', async (e) => {
            e.preventDefault();
            container.innerHTML = '';
            const fd = new FormData(menuForm);
            try {
                const r = await postForm('/56Food/api/admin/menus_create.php', fd);
                if (r.ok) {
                    showAdminMessage(container, 'success', [r.json?.message || 'Menu created']);
                    menuForm.reset();
                } else if (r.status === 400) {
                    showAdminMessage(container, 'error', r.json?.errors || [r.json?.error || 'Bad request']);
                } else if (r.status === 401) {
                    showAdminMessage(container, 'error', [r.json?.error || 'Unauthorized']);
                } else {
                    showAdminMessage(container, 'error', [r.json?.error || 'Server error']);
                }
            } catch (err) {
                showAdminMessage(container, 'error', ['Network error']);
            }
        });
    }

    // Expose helper for inline onchange handlers to submit via requestSubmit
    window.submitAjaxForm = function(el) {
        const form = el.closest('form');
        if (!form) return;
        if (typeof form.requestSubmit === 'function') {
            form.requestSubmit();
        } else {
            // Fallback: dispatch submit event
            form.dispatchEvent(new Event('submit', { bubbles: true, cancelable: true }));
        }
    };

    // Generic AJAX handlers for table forms (delete, role change, toggle)
    document.querySelectorAll('form.ajax-form').forEach(form => {
        form.addEventListener('submit', async (e) => {
            e.preventDefault();
            const api = form.dataset.api; // e.g. /56Food/api/admin/users_action.php
            if (!api) return form.submit();

            // confirm if data-confirm attribute present
            const confirmMsg = form.dataset.confirm;
            if (confirmMsg && !window.confirm(confirmMsg)) return;

            const fd = new FormData(form);
            const obj = {};
            for (const [k,v] of fd.entries()) {
                obj[k] = v;
            }

            try {
                const r = await postJSON(api, obj);
                if (r.ok) {
                    // If row has data-row-id, remove it for delete, otherwise reload
                    if (obj.action && (obj.action === 'delete_user' || obj.action === 'delete')) {
                        const row = form.closest('tr');
                        if (row) row.remove();
                    } else {
                        // simple UI update: reload page to reflect changes
                        window.location.reload();
                    }
                } else {
                    alert(r.json?.error || (r.json?.errors && r.json.errors.join('\n')) || 'Request failed');
                }
            } catch (err) {
                alert('Network error');
            }
        });
    });

    // Edit user form
    const editUserForm = document.getElementById('edit-user-form');
    if (editUserForm) {
        const container = document.getElementById('edit-user-messages');
        editUserForm.addEventListener('submit', async (e) => {
            e.preventDefault();
            container.innerHTML = '';
            const fd = new FormData(editUserForm);
            const data = Object.fromEntries(fd.entries());
            // include user id from data attribute
            data.id = editUserForm.dataset.userId;
            try {
                const r = await postJSON('/56Food/api/admin/users_update.php', data);
                if (r.ok) {
                    container.innerHTML = '<div class="ajax-message success">' + (r.json?.message || 'Updated') + '</div>';
                    setTimeout(() => window.location.href = '/56Food/admin/users.php', 800);
                } else {
                    container.innerHTML = '<div class="ajax-message error">' + (r.json?.errors ? r.json.errors.join('<br>') : (r.json?.error || 'Failed')) + '</div>';
                }
            } catch (err) {
                container.innerHTML = '<div class="ajax-message error">Network error</div>';
            }
        });
    }

    // Edit menu form
    const editMenuForm = document.getElementById('edit-menu-form');
    if (editMenuForm) {
        const container = document.getElementById('edit-menu-messages');
        editMenuForm.addEventListener('submit', async (e) => {
            e.preventDefault();
            container.innerHTML = '';
            const fd = new FormData(editMenuForm);
            // append menu id if set on form dataset
            if (editMenuForm.dataset.menuId) fd.append('menu_id', editMenuForm.dataset.menuId);
            try {
                const r = await postForm('/56Food/api/admin/menus_update.php', fd);
                if (r.ok) {
                    container.innerHTML = '<div class="ajax-message success">' + (r.json?.message || 'Updated') + '</div>';
                    setTimeout(() => window.location.href = '/56Food/admin/menus.php', 800);
                } else {
                    container.innerHTML = '<div class="ajax-message error">' + (r.json?.errors ? r.json.errors.join('<br>') : (r.json?.error || 'Failed')) + '</div>';
                }
            } catch (err) {
                container.innerHTML = '<div class="ajax-message error">Network error</div>';
            }
        });
    }
});
