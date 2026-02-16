// assets/js/auth.js

/**
 * Modern Fetch Wrapper
 * Handles JSON parsing and error norms
 */
const api = {
    base: '/56Food/api', // Adjust if deployed elsewhere
    
    async post(endpoint, data) {
        const url = `${this.base}${endpoint}`;
        try {
            const response = await fetch(url, {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json',
                    'Accept': 'application/json'
                },
                body: JSON.stringify(data)
            });

            const result = await response.json().catch(() => ({}));
            
            if (!response.ok) {
                // Normalize error format to { error: "msg" } or { errors: ["msg"] }
                const error = result.error || result.message || 'Something went wrong';
                const errors = result.errors || [error];
                return { success: false, status: response.status, errors };
            }

            return { success: true, status: response.status, data: result };

        } catch (err) {
            return { success: false, status: 0, errors: ['Network connection failed'] };
        }
    }
};

/**
 * UI Helper: Show/Hide Loading
 */
const ui = {
    setLoading(form, isLoading) {
        const btn = form.querySelector('button[type="submit"]');
        if (!btn) return;
        
        if (isLoading) {
            btn.dataset.originalText = btn.innerHTML;
            btn.innerHTML = 'Wait...';
            btn.disabled = true;
        } else {
            btn.innerHTML = btn.dataset.originalText || 'Submit';
            btn.disabled = false;
        }
    },
    
    showMessages(containerId, type, messages) {
        const container = document.getElementById(containerId);
        if (!container) return;
        
        container.innerHTML = '';
        const div = document.createElement('div');
        div.className = `alert alert-${type === 'error' ? 'danger' : 'success'}`;
        div.style.padding = '10px';
        div.style.marginBottom = '10px';
        div.style.borderRadius = '4px';
        div.style.backgroundColor = type === 'error' ? '#f8d7da' : '#d4edda';
        div.style.color = type === 'error' ? '#721c24' : '#155724';
        
        if (messages.length === 1) {
            div.innerText = messages[0];
        } else {
            const ul = document.createElement('ul');
            ul.style.margin = '0';
            ul.style.paddingLeft = '20px';
            messages.forEach(msg => {
                const li = document.createElement('li');
                li.innerText = msg;
                ul.appendChild(li);
            });
            div.appendChild(ul);
        }
        
        container.appendChild(div);
    }
};

// Event Listeners
document.addEventListener('DOMContentLoaded', () => {

    // Register Form
    const registerForm = document.getElementById('register-form');
    if (registerForm) {
        registerForm.addEventListener('submit', async (e) => {
            e.preventDefault();
            ui.setLoading(registerForm, true);
            ui.showMessages('register-messages', 'clear', []); // clear

            const formData = new FormData(registerForm);
            const payload = Object.fromEntries(formData.entries());

            const res = await api.post('/register.php', payload);
            
            ui.setLoading(registerForm, false);

            if (res.success) {
                ui.showMessages('register-messages', 'success', [res.data.message]);
                registerForm.reset();
                // Optional: redirect to login after a delay
                setTimeout(() => window.location.href = 'login.php', 1500);
            } else {
                ui.showMessages('register-messages', 'error', res.errors);
            }
        });
    }

    // Login Form
    const loginForm = document.getElementById('login-form');
    if (loginForm) {
        loginForm.addEventListener('submit', async (e) => {
            e.preventDefault();
            ui.setLoading(loginForm, true);
            ui.showMessages('login-messages', 'clear', []);

            const formData = new FormData(loginForm);
            const payload = Object.fromEntries(formData.entries());

            const res = await api.post('/login.php', payload);

            ui.setLoading(loginForm, false);

            if (res.success) {
                ui.showMessages('login-messages', 'success', ['Login successful! Redirecting...']);
                setTimeout(() => {
                    window.location.href = res.data.redirect || 'index.php';
                }, 1000);
            } else {
                ui.showMessages('login-messages', 'error', res.errors);
            }
        });
    }
});
