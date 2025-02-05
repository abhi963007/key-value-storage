// DOM Elements
const loginSection = document.getElementById('login-section');
const registerSection = document.getElementById('register-section');
const dashboardSection = document.getElementById('dashboard-section');
const loginForm = document.getElementById('login-form');
const registerForm = document.getElementById('register-form');
const showRegisterLink = document.getElementById('show-register');
const showLoginLink = document.getElementById('show-login');
const logoutBtn = document.getElementById('logout-btn');
const userNameSpan = document.getElementById('user-name');

// API URLs
const API_BASE_URL = 'http://localhost/New%20folder/api';
const LOGIN_URL = `${API_BASE_URL}/login.php`;
const REGISTER_URL = `${API_BASE_URL}/register.php`;

// Check if user is already logged in
document.addEventListener('DOMContentLoaded', () => {
    const token = localStorage.getItem('token');
    if (token) {
        const userData = JSON.parse(localStorage.getItem('user'));
        showDashboard(userData);
    }
    initGoogleSignIn();
});

// Show/Hide Forms
showRegisterLink.addEventListener('click', (e) => {
    e.preventDefault();
    loginSection.style.display = 'none';
    registerSection.style.display = 'block';
});

showLoginLink.addEventListener('click', (e) => {
    e.preventDefault();
    registerSection.style.display = 'none';
    loginSection.style.display = 'block';
});

// Handle Register
registerForm.addEventListener('submit', async (e) => {
    e.preventDefault();
    
    const username = document.getElementById('register-username').value;
    const email = document.getElementById('register-email').value;
    const password = document.getElementById('register-password').value;

    try {
        const response = await fetch(REGISTER_URL, {
            method: 'POST',
            headers: {
                'Content-Type': 'application/json'
            },
            body: JSON.stringify({
                username,
                email,
                password
            })
        });

        const data = await response.json();

        if (response.ok) {
            alert('Registration successful! Please login.');
            registerSection.style.display = 'none';
            loginSection.style.display = 'block';
            registerForm.reset();
        } else {
            alert(data.message || 'Registration failed!');
        }
    } catch (error) {
        console.error('Error:', error);
        alert('An error occurred during registration.');
    }
});

// Handle Login
loginForm.addEventListener('submit', async (e) => {
    e.preventDefault();
    
    const email = document.getElementById('login-email').value;
    const password = document.getElementById('login-password').value;

    try {
        const response = await fetch(LOGIN_URL, {
            method: 'POST',
            headers: {
                'Content-Type': 'application/json'
            },
            body: JSON.stringify({
                email,
                password
            })
        });

        const data = await response.json();

        if (response.ok) {
            localStorage.setItem('token', data.token);
            localStorage.setItem('user', JSON.stringify(data.user));
            showDashboard(data.user);
            loginForm.reset();
        } else {
            alert(data.message || 'Login failed!');
        }
    } catch (error) {
        console.error('Error:', error);
        alert('An error occurred during login.');
    }
});

// Google Sign-In
function initGoogleSignIn() {
    const script = document.createElement('script');
    script.src = 'https://accounts.google.com/gsi/client';
    script.async = true;
    script.defer = true;
    document.head.appendChild(script);

    script.onload = function() {
        google.accounts.id.initialize({
            client_id: '124938168618-chnchutbuof1fs3nj69280bpcicc23nm.apps.googleusercontent.com',
            callback: handleGoogleSignIn,
            auto_select: false
        });
    };
}

async function handleGoogleSignIn(response) {
    try {
        const id_token = response.credential;
        const apiResponse = await fetch(`${API_BASE_URL}/google_login.php`, {
            method: 'POST',
            headers: {
                'Content-Type': 'application/json'
            },
            body: JSON.stringify({
                google_token: id_token
            })
        });

        const data = await apiResponse.json();

        if (apiResponse.ok) {
            localStorage.setItem('token', data.token);
            localStorage.setItem('user', JSON.stringify(data.user));
            localStorage.setItem('isGoogleLogin', 'true');
            showDashboard(data.user);
        } else {
            alert(data.message || 'Google sign-in failed!');
        }
    } catch (error) {
        console.error('Error:', error);
        alert('An error occurred during Google sign-in.');
    }
}

// Handle Logout
logoutBtn.addEventListener('click', () => {
    const isGoogleLogin = localStorage.getItem('isGoogleLogin');
    
    if (isGoogleLogin === 'true') {
        // Google Sign-Out
        google.accounts.id.disableAutoSelect();
        google.accounts.id.revoke(localStorage.getItem('user').email, () => {
            performLogout();
        });
    } else {
        performLogout();
    }
});

function performLogout() {
    localStorage.removeItem('token');
    localStorage.removeItem('user');
    localStorage.removeItem('isGoogleLogin');
    dashboardSection.style.display = 'none';
    loginSection.style.display = 'block';
    
    // Reset the Google Sign-In button
    if (typeof google !== 'undefined' && google.accounts && google.accounts.id) {
        google.accounts.id.renderButton(
            document.querySelector('.g_id_signin'),
            {
                type: 'icon',
                size: 'large',
                shape: 'circle',
                theme: 'outline'
            }
        );
    }
}

// Utility Functions
function showDashboard(userData) {
    loginSection.style.display = 'none';
    registerSection.style.display = 'none';
    dashboardSection.style.display = 'block';
    userNameSpan.textContent = userData.username;
}

// Add Authorization Header to Protected Requests
function getAuthHeader() {
    const token = localStorage.getItem('token');
    return {
        'Authorization': `Bearer ${token}`
    };
} 