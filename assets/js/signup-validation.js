// Signup Form Validation
document.addEventListener('DOMContentLoaded', function() {
    const form = document.getElementById('signupForm');
    const username = document.getElementById('username');
    const email = document.getElementById('email');
    const password = document.getElementById('password');
    const confirmPassword = document.getElementById('confirm_password');
    
    // Real-time validation
    username.addEventListener('input', validateUsername);
    email.addEventListener('input', validateEmail);
    password.addEventListener('input', validatePassword);
    confirmPassword.addEventListener('input', validateConfirmPassword);
    
    // Form submission validation
    form.addEventListener('submit', function(e) {
        const isValid = validateForm();
        if (!isValid) {
            e.preventDefault();
        }
    });
    
    function validateUsername() {
        const value = username.value.trim();
        const errorElement = document.getElementById('usernameError');
        
        if (value.length === 0) {
            showError(errorElement, '');
            return false;
        }
        
        if (value.length < 3) {
            showError(errorElement, 'Username must be at least 3 characters long');
            return false;
        }
        
        if (value.length > 50) {
            showError(errorElement, 'Username cannot exceed 50 characters');
            return false;
        }
        
        if (!/^[a-zA-Z0-9_-]+$/.test(value)) {
            showError(errorElement, 'Username can only contain letters, numbers, underscores, and hyphens');
            return false;
        }
        
        hideError(errorElement);
        return true;
    }
    
    function validateEmail() {
        const value = email.value.trim();
        const errorElement = document.getElementById('emailError');
        
        if (value.length === 0) {
            showError(errorElement, '');
            return false;
        }
        
        if (!/^[^\s@]+@[^\s@]+\.[^\s@]+$/.test(value)) {
            showError(errorElement, 'Please enter a valid email address');
            return false;
        }
        
        if (value.length > 100) {
            showError(errorElement, 'Email address is too long');
            return false;
        }
        
        hideError(errorElement);
        return true;
    }
    
    function validatePassword() {
        const value = password.value;
        const errorElement = document.getElementById('passwordError');
        const strengthElement = document.getElementById('passwordStrength');
        
        if (value.length === 0) {
            showError(errorElement, '');
            strengthElement.textContent = '';
            return false;
        }
        
        if (value.length < 8) {
            showError(errorElement, 'Password must be at least 8 characters long');
            strengthElement.textContent = 'Too short';
            strengthElement.className = 'password-strength weak';
            return false;
        }
        
        const hasUpper = /[A-Z]/.test(value);
        const hasLower = /[a-z]/.test(value);
        const hasNumber = /\d/.test(value);
        const hasSpecial = /[@$!%*?&]/.test(value);
        
        const strength = [hasUpper, hasLower, hasNumber, hasSpecial].filter(Boolean).length;
        
        if (strength < 4) {
            showError(errorElement, 'Password must contain uppercase, lowercase, number, and special character');
            
            if (strength <= 1) {
                strengthElement.textContent = 'Very weak';
                strengthElement.className = 'password-strength weak';
            } else if (strength === 2) {
                strengthElement.textContent = 'Weak';
                strengthElement.className = 'password-strength weak';
            } else {
                strengthElement.textContent = 'Medium';
                strengthElement.className = 'password-strength medium';
            }
            return false;
        }
        
        hideError(errorElement);
        strengthElement.textContent = 'Strong';
        strengthElement.className = 'password-strength strong';
        return true;
    }
    
    function validateConfirmPassword() {
        const value = confirmPassword.value;
        const passwordValue = password.value;
        const errorElement = document.getElementById('confirmPasswordError');
        
        if (value.length === 0) {
            showError(errorElement, '');
            return false;
        }
        
        if (value !== passwordValue) {
            showError(errorElement, 'Passwords do not match');
            return false;
        }
        
        hideError(errorElement);
        return true;
    }
    
    function validateForm() {
        const isUsernameValid = validateUsername();
        const isEmailValid = validateEmail();
        const isPasswordValid = validatePassword();
        const isConfirmPasswordValid = validateConfirmPassword();
        
        return isUsernameValid && isEmailValid && isPasswordValid && isConfirmPasswordValid;
    }
    
    function showError(element, message) {
        element.textContent = message;
        element.classList.add('show');
    }
    
    function hideError(element) {
        element.textContent = '';
        element.classList.remove('show');
    }
    
    // Prevent form resubmission on page refresh
    if (window.history.replaceState) {
        window.history.replaceState(null, null, window.location.href);
    }
});