// Login Form Validation and Security
document.addEventListener('DOMContentLoaded', function() {
    const form = document.getElementById('loginForm');
    const username = document.getElementById('username');
    const password = document.getElementById('password');
    const submitButton = form.querySelector('button[type="submit"]');
    
    let loginAttempts = parseInt(localStorage.getItem('loginAttempts') || '0');
    let lastAttemptTime = parseInt(localStorage.getItem('lastAttemptTime') || '0');
    
    // Check if user is rate limited
    checkRateLimit();
    
    // Real-time validation
    username.addEventListener('input', validateUsername);
    password.addEventListener('input', validatePassword);
    
    // Form submission
    form.addEventListener('submit', function(e) {
        if (!validateForm()) {
            e.preventDefault();
            return;
        }
        
        // Update attempt tracking
        updateLoginAttempt();
        
        // Add loading state
        submitButton.disabled = true;
        submitButton.textContent = 'Logging in...';
    });
    
    function validateUsername() {
        const value = username.value.trim();
        const errorElement = document.getElementById('usernameError');
        
        if (value.length === 0) {
            showError(errorElement, 'Username is required');
            return false;
        }
        
        if (value.length > 50) {
            showError(errorElement, 'Username is too long');
            return false;
        }
        
        hideError(errorElement);
        return true;
    }
    
    function validatePassword() {
        const value = password.value;
        const errorElement = document.getElementById('passwordError');
        
        if (value.length === 0) {
            showError(errorElement, 'Password is required');
            return false;
        }
        
        hideError(errorElement);
        return true;
    }
    
    function validateForm() {
        const isUsernameValid = validateUsername();
        const isPasswordValid = validatePassword();
        
        return isUsernameValid && isPasswordValid;
    }
    
    function showError(element, message) {
        if (element) {
            element.textContent = message;
            element.classList.add('show');
        }
    }
    
    function hideError(element) {
        if (element) {
            element.textContent = '';
            element.classList.remove('show');
        }
    }
    
    function checkRateLimit() {
        const now = Date.now();
        const timeDiff = now - lastAttemptTime;
        const cooldownPeriod = 15 * 60 * 1000; // 15 minutes
        
        // Reset attempts if cooldown period has passed
        if (timeDiff > cooldownPeriod) {
            loginAttempts = 0;
            localStorage.removeItem('loginAttempts');
            localStorage.removeItem('lastAttemptTime');
        }
        
        // Check if too many attempts
        if (loginAttempts >= 5) {
            const remainingTime = Math.ceil((cooldownPeriod - timeDiff) / 1000 / 60);
            if (remainingTime > 0) {
                disableForm(`Too many failed attempts. Please wait ${remainingTime} minutes.`);
            }
        }
    }
    
    function updateLoginAttempt() {
        loginAttempts++;
        lastAttemptTime = Date.now();
        
        localStorage.setItem('loginAttempts', loginAttempts.toString());
        localStorage.setItem('lastAttemptTime', lastAttemptTime.toString());
    }
    
    function disableForm(message) {
        submitButton.disabled = true;
        submitButton.textContent = 'Blocked';
        
        // Show rate limit message
        const alertDiv = document.createElement('div');
        alertDiv.className = 'alert alert-error';
        alertDiv.innerHTML = `<p>${message}</p>`;
        
        const existingAlert = document.querySelector('.alert');
        if (existingAlert) {
            existingAlert.remove();
        }
        
        form.parentNode.insertBefore(alertDiv, form);
    }
    
    // Clear form on successful login (if redirected back)
    const urlParams = new URLSearchParams(window.location.search);
    if (urlParams.get('message') === 'logged_out') {
        // Reset login attempts on successful logout
        localStorage.removeItem('loginAttempts');
        localStorage.removeItem('lastAttemptTime');
        
        // Show logout message
        const alertDiv = document.createElement('div');
        alertDiv.className = 'alert alert-success';
        alertDiv.innerHTML = '<p>You have been successfully logged out.</p>';
        
        form.parentNode.insertBefore(alertDiv, form);
        
        // Clear URL parameter
        window.history.replaceState({}, document.title, window.location.pathname);
    }
    
    // Security: Clear sensitive data on page unload
    window.addEventListener('beforeunload', function() {
        if (password.value) {
            password.value = '';
        }
    });
    
    // Security: Prevent password field from being cached
    password.setAttribute('autocomplete', 'current-password');
    
    // Add focus management for accessibility
    username.addEventListener('keydown', function(e) {
        if (e.key === 'Enter') {
            e.preventDefault();
            password.focus();
        }
    });
    
    password.addEventListener('keydown', function(e) {
        if (e.key === 'Enter' && validateForm()) {
            form.submit();
        }
    });
    
    // Prevent form resubmission on page refresh
    if (window.history.replaceState) {
        window.history.replaceState(null, null, window.location.href);
    }
});