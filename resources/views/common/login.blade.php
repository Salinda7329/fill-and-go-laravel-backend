@extends('layouts.publicpageslayout')

@section('page_title')
    Login Form
@endsection
@section('head_section')
    <link rel="stylesheet" href="{{ asset('css/loginform.css') }}">
@endsection

@section('page_content')
    <div class="login-container" style="margin-top:100pt;">
        <h2>Login</h2>
        <form id="manualLoginForm" class="manual-login-form" method="POST" action="/api/manual-login" autocomplete="off">
            <div class="form-group">
                <label for="email">Email</label>
                <input type="email" id="email" name="email" required autocomplete="username">
                <span class="error-message" id="emailError"></span>
            </div>
            <div class="form-group">
                <label for="password">Password</label>
                <input type="password" id="password" name="password" required autocomplete="current-password">
                <span class="error-message" id="passwordError"></span>
            </div>
            <button type="submit" class="btn-login">Login</button>
            <span class="error-message" id="manualLoginGlobalError"></span>
        </form>
        <div class="or-divider"><span>OR</span></div>
        <button onclick="loginWithGoogle()" class="btn-google">Login with Google</button>
        <button onclick="loginWithFacebook()" class="btn-facebook">Login with Facebook</button>
    </div>
@endsection

@php
    $firebaseConfig = [
        'apiKey' => env('FIREBASE_API_KEY'),
        'authDomain' => env('FIREBASE_AUTH_DOMAIN'),
        'databaseURL' => env('FIREBASE_DATABASE_URL'),
        'projectId' => env('FIREBASE_PROJECT_ID'),
        'storageBucket' => env('FIREBASE_STORAGE_BUCKET'),
        'messagingSenderId' => env('FIREBASE_MESSAGING_SENDER_ID'),
        'appId' => env('FIREBASE_APP_ID'),
        'measurementId' => env('FIREBASE_MEASUREMENT_ID'),
    ];
@endphp

@section('after_body_section')
    <script type="module">
        // Firebase SDKs
        import {
            initializeApp
        } from "https://www.gstatic.com/firebasejs/12.0.0/firebase-app.js";
        import {
            getAuth,
            signInWithEmailAndPassword,
            signInWithPopup,
            GoogleAuthProvider,
            FacebookAuthProvider
        } from "https://www.gstatic.com/firebasejs/12.0.0/firebase-auth.js";

        const firebaseConfig = {!! json_encode($firebaseConfig) !!};

        const app = initializeApp(firebaseConfig);
        const auth = getAuth(app);
        // Read CSRF token from meta tag
        const csrfToken = document.querySelector('meta[name="csrf-token"]').getAttribute('content');
        const googleProvider = new GoogleAuthProvider();
        const facebookProvider = new FacebookAuthProvider();

        // Manual login form logic
        document.addEventListener('DOMContentLoaded', function() {
            const form = document.getElementById('manualLoginForm');
            if (!form) return;
            form.addEventListener('submit', async function(e) {
                e.preventDefault();
                // Clear errors
                document.getElementById('emailError').textContent = '';
                document.getElementById('passwordError').textContent = '';
                document.getElementById('manualLoginGlobalError').textContent = '';
                const email = form.email.value;
                const password = form.password.value;
                try {
                    // 1. Sign in with Firebase Auth
                    const userCredential = await signInWithEmailAndPassword(auth, email, password);
                    const user = userCredential.user;
                    const idToken = await user.getIdToken();
                    // 2. Send ID token to backend for verification
                    const response = await fetch('/create-session', {
                        method: 'POST',
                        headers: {
                            'Content-Type': 'application/json',
                            'X-CSRF-TOKEN': csrfToken
                        },
                        body: JSON.stringify({
                            idToken
                        })
                    });
                    if (response.ok) {
                        window.location.href = '/dashboard';
                    } else {
                        const result = await response.json();
                        alert(result.message || 'Login failed.');
                    }

                    const result = await response.json();
                    console.log('Backend response:', result);
                    if (response.ok && result.redirect_url) {
                        window.location.href = result.redirect_url;
                    } else {
                        document.getElementById('manualLoginGlobalError').textContent = result
                            .message || 'Login failed.';
                    }


                } catch (error) {
                    if (error.code === 'auth/user-not-found') {
                        document.getElementById('emailError').textContent =
                            'No user found with this email.';
                    } else if (error.code === 'auth/wrong-password') {
                        document.getElementById('passwordError').textContent = 'Incorrect password.';
                    } else if (error.code === 'auth/invalid-email') {
                        document.getElementById('emailError').textContent = 'Invalid email address.';
                    } else {
                        document.getElementById('manualLoginGlobalError').textContent =
                            'Login failed. Please try again.';
                    }
                }
            });
        });

        window.loginWithGoogle = async function() {
            try {
                const result = await signInWithPopup(auth, googleProvider);
                const user = result.user;
                const idToken = await user.getIdToken();

                const response = await fetch('/create-session', {
                    method: 'POST',
                    headers: {
                        'Content-Type': 'application/json',
                        'X-CSRF-TOKEN': csrfToken
                    },
                    body: JSON.stringify({
                        idToken
                    })
                });
                if (response.ok) {
                    window.location.href = '/dashboard';
                } else {
                    const result = await response.json();
                    alert(result.message || 'Login failed.');
                }


                const resultData = await response.json();

                if (response.ok) {
                    // Success
                    window.location.href = '/dashboard';
                } else {
                    // Show error message
                    alert(resultData.message || 'Login failed.');
                }

            } catch (error) {
                console.error('Google login failed', error);
                alert('Google login failed. ' + error.message);
            }
        }


        window.loginWithFacebook = async function() {
            try {
                const result = await signInWithPopup(auth, facebookProvider);
                const user = result.user;
                const idToken = await user.getIdToken();

                const response = await fetch('/create-session', {
                    method: 'POST',
                    headers: {
                        'Content-Type': 'application/json',
                        'X-CSRF-TOKEN': csrfToken
                    },
                    body: JSON.stringify({
                        idToken
                    })
                });
                if (response.ok) {
                    window.location.href = '/dashboard';
                } else {
                    const result = await response.json();
                    alert(result.message || 'Login failed.');
                }


                const resultData = await response.json();

                if (response.ok) {
                    // Success
                    window.location.href = '/dashboard';
                } else {
                    // Show error message
                    alert(resultData.message || 'Login failed.');
                }

            } catch (error) {
                console.error('Facebook login failed', error);
                alert('Facebook login failed');
            }
        }
    </script>
@endsection
