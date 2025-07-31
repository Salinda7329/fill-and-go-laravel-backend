@extends('layouts.publicpageslayout')

@section('page_title')
    Customer Registration Form
@endsection

@section('head_section')
    <link rel="stylesheet" href="{{ asset('css/customerregistrationform.css') }}">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <script src="https://code.jquery.com/jquery-3.7.1.min.js"></script>
    <script type="module">
        import {
            initializeApp
        } from "https://www.gstatic.com/firebasejs/10.5.0/firebase-app.js";
        import {
            getAuth,
            signInWithEmailAndPassword
        } from "https://www.gstatic.com/firebasejs/10.5.0/firebase-auth.js";

        const firebaseConfig = {
            apiKey: "{{ env('FIREBASE_API_KEY') }}",
            authDomain: "{{ env('FIREBASE_AUTH_DOMAIN') }}",
            projectId: "{{ env('FIREBASE_PROJECT_ID') }}",
            appId: "{{ env('FIREBASE_APP_ID') }}"
        };

        const firebaseApp = initializeApp(firebaseConfig);
        const auth = getAuth(firebaseApp);

        window.firebaseLoginAndSession = async function(email, password) {
            try {
                const userCredential = await signInWithEmailAndPassword(auth, email, password);
                const idToken = await userCredential.user.getIdToken();
                const csrfToken = $('meta[name="csrf-token"]').attr('content');
                console.log('CSRF Token:', csrfToken); // Debug CSRF token

                $.ajax({
                    url: '/create-session',
                    method: 'POST',
                    contentType: 'application/json',
                    headers: {
                        'X-CSRF-TOKEN': csrfToken
                    },
                    data: JSON.stringify({
                        id_token: idToken
                    }),
                    success: function(response) {
                        if (response.success) {
                            window.location.href = '/dashboard';
                        } else {
                            showModal('Login Error', response.message || 'Login failed.');
                        }
                    },
                    error: function(xhr) {
                        console.error('Firebase login failed:', xhr.responseJSON);
                        showModal('Login Error', xhr.responseJSON?.message ||
                            'Login failed. Please try again.');
                    }
                });
            } catch (error) {
                console.error('Firebase login failed:', error);
                showModal('Login Error',
                    'Registration succeeded but automatic login failed. Please try logging in manually.');
            }
        };
    </script>
@endsection

@section('page_content')
    <section class="registration-section">
        <div class="container">
            <h2 class="section-title">Register Today With Fill and Go</h2>

            <!-- Modal -->
            <div id="messageModal" class="modal"
                style="display:none; position:fixed; z-index:9999; left:0; top:0; width:100vw; height:100vh; background:rgba(0,0,0,0.4); align-items:center; justify-content:center;">
                <div
                    style="background:#fff; padding:32px 24px; border-radius:12px; max-width:90vw; width:350px; text-align:center; box-shadow:0 8px 32px rgba(0,0,0,0.2); position:relative;">
                    <h3 id="modalTitle" style="margin-bottom:16px; color:#002060;"></h3>
                    <p id="modalMessage" style="margin-bottom:24px;"></p>
                    <a id="modalLoginLink" href="/login" style="display:none; color:#f26522; font-weight:600;">Go to
                        Login</a>
                    <button id="closeModalBtn"
                        style="background:#f26522; color:#fff; border:none; border-radius:8px; padding:10px 28px; font-size:16px; font-weight:600; cursor:pointer; margin-top:16px;">Close</button>
                </div>
            </div>

            <div class="new customer registration form">
                <form id="registrationForm" class="registration-form" method="POST" action="/customer/registerdata">
                    @csrf
                    <div class="form-group">
                        <label for="email">Email Address *</label>
                        <input type="email" id="email" name="email" required>
                        <span class="error-message" id="emailError"></span>
                    </div>
                    <div class="form-group">
                        <label for="password">Password *</label>
                        <input type="password" id="password" name="password" required minlength="8">
                        <span class="error-message" id="passwordError"></span>
                    </div>
                    <div class="form-group">
                        <label for="password_confirmation">Confirm Password *</label>
                        <input type="password" id="password_confirmation" name="password_confirmation" required
                            minlength="8">
                        <span class="error-message" id="passwordConfirmationError"></span>
                    </div>
                    <button type="submit" class="btn btn-submit">Register Now</button>
                </form>
            </div>
        </div>
    </section>
@endsection

@section('after_body_section')
    <script>
        $(document).ready(function() {
            const $form = $('#registrationForm');
            const $emailInput = $('#email');
            const $passwordInput = $('#password');
            const $passwordConfirmationInput = $('#password_confirmation');
            const $emailError = $('#emailError');
            const $passwordError = $('#passwordError');
            const $passwordConfirmationError = $('#passwordConfirmationError');
            const $modal = $('#messageModal');
            const $modalTitle = $('#modalTitle');
            const $modalMessage = $('#modalMessage');
            const $modalLoginLink = $('#modalLoginLink');
            const $closeModalBtn = $('#closeModalBtn');

            function validatePassword(password) {
                const regex = /^(?=.*[A-Za-z])(?=.*\d)(?=.*[!@#$%^&*()_+\-=[\]{};':"\\|,.<>/?]).{8,}$/;
                return regex.test(password);
            }

            function showError($element, message) {
                $element.text(message).addClass('show');
            }

            function clearError($element) {
                $element.text('').removeClass('show');
            }

            function showModal(title, message, showLogin = false) {
                $modalTitle.text(title);
                $modalMessage.text(message);
                $modalLoginLink.css('display', showLogin ? 'inline-block' : 'none');
                $modal.css('display', 'flex');
            }

            $form.on('submit', function(e) {
                e.preventDefault();

                let valid = true;
                const email = $emailInput.val().trim();
                const password = $passwordInput.val();
                const passwordConfirmation = $passwordConfirmationInput.val();

                clearError($emailError);
                if (!validatePassword(password)) {
                    showError($passwordError,
                        'Password must be at least 8 characters and include letters, numbers, and special characters.'
                    );
                    valid = false;
                } else {
                    clearError($passwordError);
                }

                if (password !== passwordConfirmation) {
                    showError($passwordConfirmationError, 'Passwords do not match.');
                    valid = false;
                } else {
                    clearError($passwordConfirmationError);
                }

                if (!valid) return;

                const csrfToken = $('meta[name="csrf-token"]').attr('content');
                console.log('CSRF Token for registration:', csrfToken); // Debug CSRF token

                $.ajax({
                    url: '/customer/registerdata',
                    method: 'POST',
                    contentType: 'application/json',
                    headers: {
                        'X-CSRF-TOKEN': csrfToken
                    },
                    data: JSON.stringify({
                        email: email,
                        password: password,
                        password_confirmation: passwordConfirmation
                    }),
                    success: function(data) {
                        if (data.success) {
                            // Attempt Firebase login after successful registration
                            window.firebaseLoginAndSession(email, password);
                        } else {
                            showError($emailError, data.message);
                            showModal('Registration Error', data.message ||
                                'Registration failed.');
                        }
                    },
                    error: function(xhr) {
                        console.error('Registration failed:', xhr.responseJSON);
                        const message = xhr.responseJSON?.message ||
                            'An unexpected error occurred.';
                        showError($emailError, message);
                        showModal('Registration Error', message);
                    }
                });
            });

            $closeModalBtn.on('click', function() {
                $modal.css('display', 'none');
            });

            $modal.on('click', function(e) {
                if (e.target === $modal[0]) {
                    $modal.css('display', 'none');
                }
            });
        });
    </script>
@endsection
