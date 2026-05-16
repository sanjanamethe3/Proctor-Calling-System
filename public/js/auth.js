// Login Form Handler
document.getElementById('loginForm')?.addEventListener('submit', async function(e) {
    e.preventDefault();
    
    const email = document.getElementById('email').value;
    const password = document.getElementById('password').value;
    const alertBox = document.getElementById('alertBox');
    
    try {
        const response = await fetch('../backend/api/auth.php', {
            method: 'POST',
            headers: {
                'Content-Type': 'application/json',
            },
            body: JSON.stringify({
                action: 'login',
                email: email,
                password: password
            })
        });
        
        const data = await response.json();
        
        if (data.success) {
            // Store session data
            sessionStorage.setItem('user', JSON.stringify(data.user));
            showAlert(alertBox, 'success', 'Login successful! Redirecting...');
            
            // Redirect based on role
            setTimeout(() => {
                switch(data.user.role) {
                    case 'admin':
                        window.location.href = 'dashboard.html';
                        break;
                    case 'faculty':
                        window.location.href = 'faculty-panel.html';
                        break;
                    case 'proctor':
                        window.location.href = 'proctor-panel.html';
                        break;
                    case 'hod':
                        window.location.href = 'hod-panel.html';
                        break;
                    default:
                        window.location.href = 'dashboard.html';
                }
            }, 1500);
        } else {
            showAlert(alertBox, 'error', data.message || 'Invalid credentials!');
        }
    } catch (error) {
        console.error('Login error:', error);
        showAlert(alertBox, 'error', 'Connection error. Please try again.');
    }
});

// Forgot Password Modal
function openForgotPassword(e) {
    e.preventDefault();
    document.getElementById('forgotPasswordModal').classList.add('show');
}

function closeForgotPassword() {
    document.getElementById('forgotPasswordModal').classList.remove('show');
    document.getElementById('forgotPasswordForm').reset();
}

function handleForgotPassword(e) {
    e.preventDefault();
    const email = document.getElementById('resetEmail').value;
    const forgotAlert = document.getElementById('forgotAlert');
    
    showAlert(forgotAlert, 'success', `Reset link sent to ${email}. Please check your email.`);
    
    setTimeout(() => {
        closeForgotPassword();
    }, 2000);
}

// Signup Modal
function openSignup(e) {
    e.preventDefault();
    document.getElementById('signupModal').classList.add('show');
}

function closeSignup() {
    document.getElementById('signupModal').classList.remove('show');
    document.getElementById('signupForm').reset();
}

function handleSignup(e) {
    e.preventDefault();
    
    const name = document.getElementById('signupName').value;
    const email = document.getElementById('signupEmail').value;
    const password = document.getElementById('signupPassword').value;
    const confirmPassword = document.getElementById('signupConfirm').value;
    const role = document.getElementById('signupRole').value;
    const signupAlert = document.getElementById('signupAlert');
    
    if (password !== confirmPassword) {
        showAlert(signupAlert, 'error', 'Passwords do not match!');
        return;
    }
    
    if (password.length < 6) {
        showAlert(signupAlert, 'error', 'Password must be at least 6 characters!');
        return;
    }
    
    // Here you would typically send to backend
    showAlert(signupAlert, 'success', 'Account created successfully! Please login.');
    
    setTimeout(() => {
        closeSignup();
    }, 2000);
}

// Close modal when clicking outside
window.onclick = function(event) {
    const forgotModal = document.getElementById('forgotPasswordModal');
    const signupModal = document.getElementById('signupModal');
    
    if (event.target === forgotModal) {
        closeForgotPassword();
    }
    if (event.target === signupModal) {
        closeSignup();
    }
}

// Alert Helper Function
function showAlert(element, type, message) {
    element.className = `alert ${type} show`;
    element.textContent = message;
    
    if (type === 'error') {
        setTimeout(() => {
            element.classList.remove('show');
        }, 5000);
    }
}

// Check authentication on page load
window.addEventListener('load', function() {
    // This page doesn't require authentication
    const user = sessionStorage.getItem('user');
    if (user && window.location.pathname.includes('login.html')) {
        const userData = JSON.parse(user);
        // Redirect if already logged in
        switch(userData.role) {
            case 'admin':
                window.location.href = 'dashboard.html';
                break;
            case 'faculty':
                window.location.href = 'faculty-panel.html';
                break;
            case 'proctor':
                window.location.href = 'proctor-panel.html';
                break;
            case 'hod':
                window.location.href = 'hod-panel.html';
                break;
        }
    }
});
