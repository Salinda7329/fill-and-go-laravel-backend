@extends('layouts.publicpageslayout')

@section('page_title')
    Login Form
@endsection

@section('head_section')
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <link rel="stylesheet" href="{{ asset('css/loginform.css') }}">
@endsection

@section('page_content')
    <div class="login-container" style="margin-top:100pt;">
        <h2>Login</h2>

        <form id="manualLoginForm" class="manual-login-form" autocomplete="off">
            @csrf
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

        <button id="googleLoginButton" class="btn-google">Login with Google</button>
        <button id="facebookLoginButton" class="btn-facebook">Login with Facebook</button>
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

        // Firebase config from .env
        const firebaseConfig = {!! json_encode($firebaseConfig) !!};

        const app = initializeApp(firebaseConfig);
        const auth = getAuth(app);
        const csrfToken = document.querySelector('meta[name="csrf-token"]').getAttribute('content');

        const googleProvider = new GoogleAuthProvider();
        const facebookProvider = new FacebookAuthProvider();

        /**
         * Send ID token to backend to create session
         */
        async function sendIdTokenToBackend(idToken) {
            const response = await fetch('{{ route('createSession') }}', {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json',
                    'X-CSRF-TOKEN': csrfToken,
                    'Accept': 'application/json'
                },
                body: JSON.stringify({
                    id_token: idToken
                }) // FIXED: backend expects "id_token"
            });

            const data = await response.json();
            return {
                status: response.status,
                data
            };
        }

        document.addEventListener('DOMContentLoaded', function() {
            const form = document.getElementById('manualLoginForm');

            form.addEventListener('submit', async function(e) {
                e.preventDefault();

                document.getElementById('emailError').textContent = '';
                document.getElementById('passwordError').textContent = '';
                document.getElementById('manualLoginGlobalError').textContent = '';

                const email = form.email.value;
                const password = form.password.value;

                try {
                    const userCredential = await signInWithEmailAndPassword(auth, email, password);
                    const idToken = await userCredential.user.getIdToken();

                    const {
                        status,
                        data
                    } = await sendIdTokenToBackend(idToken);

                    if (status === 200 && data.success && data.redirect_url) {
                        window.location.href = data.redirect_url;
                    } else {
                        document.getElementById('manualLoginGlobalError').textContent = data.message ||
                            'Login failed.';
                    }
                } catch (error) {
                    console.error('Manual login error:', error);
                    if (error.code === 'auth/user-not-found') {
                        document.getElementById('emailError').textContent =
                            'No user found with this email.';
                    } else if (error.code === 'auth/wrong-password') {
                        document.getElementById('passwordError').textContent = 'Incorrect password.';
                    } else if (error.code === 'auth/invalid-email') {
                        document.getElementById('emailError').textContent = 'Invalid email address.';
                    } else {
                        document.getElementById('manualLoginGlobalError').textContent =
                            'Login failed: ' + (error.message || 'Unknown error.');
                    }
                }
            });

            // Google login button
            document.getElementById('googleLoginButton').addEventListener('click', async () => {
                try {
                    const result = await signInWithPopup(auth, googleProvider);
                    const idToken = await result.user.getIdToken();
                    const {
                        status,
                        data
                    } = await sendIdTokenToBackend(idToken);

                    if (status === 200 && data.success && data.redirect_url) {
                        window.location.href = data.redirect_url;
                    } else {
                        document.getElementById('manualLoginGlobalError').textContent = data.message ||
                            'Google login failed.';
                    }
                } catch (error) {
                    console.error('Google login failed:', error);
                    document.getElementById('manualLoginGlobalError').textContent =
                        'Google login failed: ' + error.message;
                }
            });

            // Facebook login button
            document.getElementById('facebookLoginButton').addEventListener('click', async () => {
                try {
                    const result = await signInWithPopup(auth, facebookProvider);
                    const idToken = await result.user.getIdToken();
                    const {
                        status,
                        data
                    } = await sendIdTokenToBackend(idToken);

                    if (status === 200 && data.success && data.redirect_url) {
                        window.location.href = data.redirect_url;
                    } else {
                        document.getElementById('manualLoginGlobalError').textContent = data.message ||
                            'Facebook login failed.';
                    }
                } catch (error) {
                    console.error('Facebook login failed:', error);
                    document.getElementById('manualLoginGlobalError').textContent =
                        'Facebook login failed: ' + error.message;
                }
            });
        });
    </script>
@endsection
