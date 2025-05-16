document.addEventListener('DOMContentLoaded', () => {
    const loginForm = document.getElementById('loginForm');
    const registerForm = document.getElementById('registerForm');

    if (loginForm) {
        loginForm.addEventListener('submit', handleLogin);
    }

    if (registerForm) {
        registerForm.addEventListener('submit', handleRegister);
    }
});

function handleLogin(event) {
    event.preventDefault();
    
    const email = document.getElementById('email').value;
    const password = document.getElementById('password').value;
    const rememberMe = document.querySelector('input[name="remember"]').checked;

    // Basic validation
    if (!email || !password) {
        showError('Please fill in all fields');
        return;
    }

    // Here you would typically make an API call to your backend
    console.log('Login attempt:', { email, password, rememberMe });
    
    // Simulate successful login
    showSuccess('Login successful! Redirecting...');
    setTimeout(() => {
        window.location.href = 'dashboard.php';
    }, 1500);
}

function handleRegister(event) {
    event.preventDefault();
    
    const formData = {
        firstName: document.getElementById('firstName').value,
        lastName: document.getElementById('lastName').value,
        email: document.getElementById('email').value,
        phone: document.getElementById('phone').value,
        cnic: document.getElementById('cnic').value,
        password: document.getElementById('password').value,
        confirmPassword: document.getElementById('confirmPassword').value,
        education: document.getElementById('education').value,
        province: document.getElementById('province').value,
        terms: document.querySelector('input[name="terms"]').checked
    };

    // Basic validation
    if (!validateForm(formData)) {
        return;
    }

    // Here you would typically make an API call to your backend
    console.log('Registration attempt:', formData);
    
    // Simulate successful registration
    showSuccess('Registration successful! Redirecting to login...');
    setTimeout(() => {
        window.location.href = 'login.php';
    }, 1500);
}

function validateForm(formData) {
    // Check if all required fields are filled
    for (const [key, value] of Object.entries(formData)) {
        if (key !== 'terms' && !value) {
            showError(`Please fill in the ${key} field`);
            return false;
        }
    }

    // Validate email format
    const emailRegex = /^[^\s@]+@[^\s@]+\.[^\s@]+$/;
    if (!emailRegex.test(formData.email)) {
        showError('Please enter a valid email address');
        return false;
    }

    // Validate password strength
    if (formData.password.length < 8) {
        showError('Password must be at least 8 characters long');
        return false;
    }

    // Check if passwords match
    if (formData.password !== formData.confirmPassword) {
        showError('Passwords do not match');
        return false;
    }

    // Validate CNIC format (13 digits)
    const cnicRegex = /^\d{13}$/;
    if (!cnicRegex.test(formData.cnic)) {
        showError('CNIC must be 13 digits');
        return false;
    }

    // Validate phone number format
    const phoneRegex = /^\+92\d{10}$/;
    if (!phoneRegex.test(formData.phone)) {
        showError('Please enter a valid Pakistani phone number (e.g., +923001234567)');
        return false;
    }

    // Check terms acceptance
    if (!formData.terms) {
        showError('Please accept the Terms of Service and Privacy Policy');
        return false;
    }

    return true;
}

function showError(message) {
    // Create error message element
    const errorDiv = document.createElement('div');
    errorDiv.className = 'error-message';
    errorDiv.textContent = message;

    // Remove any existing error messages
    const existingError = document.querySelector('.error-message');
    if (existingError) {
        existingError.remove();
    }

    // Add the new error message
    const form = document.querySelector('form');
    form.insertBefore(errorDiv, form.firstChild);

    // Remove error message after 5 seconds
    setTimeout(() => {
        errorDiv.remove();
    }, 5000);
}

function showSuccess(message) {
    // Create success message element
    const successDiv = document.createElement('div');
    successDiv.className = 'success-message';
    successDiv.textContent = message;

    // Remove any existing messages
    const existingMessage = document.querySelector('.success-message');
    if (existingMessage) {
        existingMessage.remove();
    }

    // Add the new success message
    const form = document.querySelector('form');
    form.insertBefore(successDiv, form.firstChild);

    // Remove success message after 5 seconds
    setTimeout(() => {
        successDiv.remove();
    }, 5000);
} 