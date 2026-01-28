// Email validation regex
function isValidEmail(email) {
    const emailRegex = /^[^\s@]+@[^\s@]+\.[^\s@]+$/;
    return emailRegex.test(email);
}

// Password validation (minimum 8 characters with mixed case, numbers, and special characters)
function isValidPassword(password) {
    // Check minimum length
    if (password.length < 8) {
        return false;
    }

    // Check for uppercase letter
    if (!/[A-Z]/.test(password)) {
        return false;
    }

    // Check for lowercase letter
    if (!/[a-z]/.test(password)) {
        return false;
    }

    // Check for number
    if (!/[0-9]/.test(password)) {
        return false;
    }

    // Check for special character
    if (!/[!@#$%^&*()_+\-=\[\]{};':"\\|,.<>\/?]/.test(password)) {
        return false;
    }

    return true;
}

// Login form validation
function validateLoginForm(event) {
    event.preventDefault();

    const email = document.querySelector('input[name="email"]').value.trim();
    const password = document.querySelector('input[name="password"]').value.trim();

    // Clear previous error messages
    clearErrors();

    let isValid = true;

    // Validate email
    if (email === '') {
        showError('Email is required');
        isValid = false;
    } else if (!isValidEmail(email)) {
        showError('Please enter a valid email address');
        isValid = false;
    }

    // Validate password
    if (password === '') {
        showError('Password is required');
        isValid = false;
    } else if (!isValidPassword(password)) {
        showError('Password must be at least 8 characters with uppercase, lowercase, number, and special character (!@#$%^&*)');
        isValid = false;
    }

    // If valid, submit the form
    if (isValid) {
        event.target.submit();
    }
}

// Register form validation
function validateRegisterForm(event) {
    event.preventDefault();

    const name = document.querySelector('input[name="name"]').value.trim();
    const email = document.querySelector('input[name="email"]').value.trim();
    const password = document.querySelector('input[name="password"]').value.trim();
    const confirmPassword = document.querySelector('input[name="confirm_password"]').value.trim();

    // Clear previous error messages
    clearErrors();

    let isValid = true;

    // Validate name
    if (name === '') {
        showError('Full name is required');
        isValid = false;
    } else if (name.length < 3) {
        showError('Name must be at least 3 characters long');
        isValid = false;
    }

    // Validate email
    if (email === '') {
        showError('Email is required');
        isValid = false;
    } else if (!isValidEmail(email)) {
        showError('Please enter a valid email address');
        isValid = false;
    }

    // Validate password
    if (password === '') {
        showError('Password is required');
        isValid = false;
    } else if (!isValidPassword(password)) {
        showError('Password must be at least 8 characters with uppercase, lowercase, number, and special character (!@#$%^&*)');
        isValid = false;
    }

    // Validate confirm password
    if (confirmPassword === '') {
        showError('Please confirm your password');
        isValid = false;
    } else if (password !== confirmPassword) {
        showError('Passwords do not match');
        isValid = false;
    }

    // If valid, submit the form
    if (isValid) {
        event.target.submit();
    }
}

// Display error message
function showError(message) {
    const errorDiv = document.getElementById('errorMessage') || createErrorDiv();
    errorDiv.textContent = message;
    errorDiv.style.display = 'block';
}

// Clear error messages
function clearErrors() {
    const errorDiv = document.getElementById('errorMessage');
    if (errorDiv) {
        errorDiv.textContent = '';
        errorDiv.style.display = 'none';
    }
}

// Create error message container
function createErrorDiv() {
    const errorDiv = document.createElement('div');
    errorDiv.id = 'errorMessage';
    errorDiv.style.cssText = 'color: red; background-color: #ffebee; padding: 10px; margin-bottom: 15px; border-radius: 5px; text-align: center; display: none;';

    const form = document.querySelector('form');
    form.parentNode.insertBefore(errorDiv, form);

    return errorDiv;
}

// Attach event listeners when page loads
document.addEventListener('DOMContentLoaded', function () {
    const form = document.querySelector('form');

    if (form) {
        // Detect if it's login or register form
        if (form.id === 'loginForm' || document.querySelector('input[name="password"]')) {
            const isRegisterForm = document.querySelector('input[name="confirm_password"]') !== null;

            if (isRegisterForm) {
                form.addEventListener('submit', validateRegisterForm);
            } else {
                form.addEventListener('submit', validateLoginForm);
            }
        }
    }
});

