@extends('layouts.publicpageslayout')

@section('page_title')
    Customer Registration Form
@endsection
@section('head_section')
    <link rel="stylesheet" href="{{ asset('css/customerregistrationform.css') }}">
@endsection


@section('page_content')
    <!-- Customer Registration Form Section -->
    <section class="registration-section">
        <div class="container">
            <h2 class="section-title">Register Today With Fill and Go</h2>
            <!-- Modal for success/error -->
            <div id="messageModal" class="modal" style="display:none; position:fixed; z-index:9999; left:0; top:0; width:100vw; height:100vh; background:rgba(0,0,0,0.4); align-items:center; justify-content:center;">
                <div style="background:#fff; padding:32px 24px; border-radius:12px; max-width:90vw; width:350px; text-align:center; box-shadow:0 8px 32px rgba(0,0,0,0.2); position:relative;">
                    <h3 id="modalTitle" style="margin-bottom:16px; color:#002060;"></h3>
                    <p id="modalMessage" style="margin-bottom:24px;"></p>
                    <a id="modalLoginLink" href="/login" style="display:none; color:#f26522; font-weight:600;">Go to Login</a>
                    <button id="closeModalBtn" style="background:#f26522; color:#fff; border:none; border-radius:8px; padding:10px 28px; font-size:16px; font-weight:600; cursor:pointer; margin-top:16px;">Close</button>
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
                        <input type="password" id="password_confirmation" name="password_confirmation" required minlength="8">
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
        document.addEventListener('DOMContentLoaded', function() {
            const form = document.getElementById('registrationForm');
            const passwordInput = document.getElementById('password');
            const passwordConfirmationInput = document.getElementById('password_confirmation');
            const passwordError = document.getElementById('passwordError');
            const passwordConfirmationError = document.getElementById('passwordConfirmationError');
            const modal = document.getElementById('messageModal');
            const modalTitle = document.getElementById('modalTitle');
            const modalMessage = document.getElementById('modalMessage');
            const modalLoginLink = document.getElementById('modalLoginLink');
            const closeModalBtn = document.getElementById('closeModalBtn');

            function validatePassword(password) {
                // At least 8 chars, 1 letter, 1 number, 1 special char
                const regex = /^(?=.*[A-Za-z])(?=.*\d)(?=.*[!@#$%^&*()_+\-=[\]{};':"\\|,.<>/?]).{8,}$/;
                return regex.test(password);
            }

            function showError(element, message) {
                element.textContent = message;
                element.classList.add('show');
            }
            function clearError(element) {
                element.textContent = '';
                element.classList.remove('show');
            }

            passwordInput.addEventListener('input', function() {
                if (!validatePassword(passwordInput.value)) {
                    showError(passwordError, 'Password must be at least 8 characters and include letters, numbers, and special characters.');
                } else {
                    clearError(passwordError);
                }
            });

            passwordConfirmationInput.addEventListener('input', function() {
                if (passwordConfirmationInput.value !== passwordInput.value) {
                    showError(passwordConfirmationError, 'Passwords do not match.');
                } else {
                    clearError(passwordConfirmationError);
                }
            });

            form.addEventListener('submit', function(e) {
                let valid = true;
                // Password validation
                if (!validatePassword(passwordInput.value)) {
                    showError(passwordError, 'Password must be at least 8 characters and include letters, numbers, and special characters.');
                    valid = false;
                } else {
                    clearError(passwordError);
                }
                // Password confirmation
                if (passwordConfirmationInput.value !== passwordInput.value) {
                    showError(passwordConfirmationError, 'Passwords do not match.');
                    valid = false;
                } else {
                    clearError(passwordConfirmationError);
                }
                if (!valid) {
                    e.preventDefault();
                }
            });

            // Show modal if there is a session message
            @if(session('success'))
                modalTitle.textContent = 'Registration Successful!';
                modalMessage.textContent = @json(session('success'));
                modalLoginLink.style.display = 'inline-block';
                modal.style.display = 'flex';
            @elseif($errors->any())
                modalTitle.textContent = 'Registration Error';
                modalMessage.textContent = @json($errors->first());
                modalLoginLink.style.display = 'none';
                modal.style.display = 'flex';
            @endif

            closeModalBtn.addEventListener('click', function() {
                modal.style.display = 'none';
            });
            modal.addEventListener('click', function(e) {
                if (e.target === modal) {
                    modal.style.display = 'none';
                }
            });
        });
    </script>
@endsection
