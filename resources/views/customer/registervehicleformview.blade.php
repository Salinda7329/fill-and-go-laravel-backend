@extends('customer.customerlayout')

@section('customer_page_title')
    Vehicle Registration Form
@endsection

@section('customer_head_section')
    <link rel="stylesheet" href="{{ asset('css/customerregistrationform.css') }}">
@endsection

@section('customer_page_content')
    <section class="registration-section" style="padding: -10px">
        <div class="container">
            <h3 class="section-title">Register Your Vehicle With Fill and Go</h3>

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
                <form id="registrationForm" class="registration-form" method="POST">
                    @csrf
                    <div class="form-group">
                        <label for="vehicle_number">Vehicle Number *</label>
                        <input type="text" id="vehicle_number" name="vehicle_number" required>
                        <span class="error-message" id="vehicleNumberError"></span>
                    </div>
                    <div class="form-group">
                        <label>Fuel Type *</label>
                        <div class="radio-group">
                            <label class="radio-option">
                                <input type="radio" name="fuel_type" value="Petrol" required>
                                <span class="radio-custom"></span>
                                <span class="radio-label">Petrol</span>
                            </label>
                            <label class="radio-option">
                                <input type="radio" name="fuel_type" value="Diesel">
                                <span class="radio-custom"></span>
                                <span class="radio-label">Diesel</span>
                            </label>
                        </div>
                        <span class="error-message" id="fuelTypeError"></span>
                    </div>
                    <div class="form-buttons">
                        <button type="submit" class="btn btn-submit">Register Vehicle</button>
                        <button type="reset" class="btn btn-reset">Reset</button>
                    </div>
                </form>
            </div>
        </div>
    </section>
@endsection

@section('customer_after_body_section')
    <script>
        document.addEventListener('DOMContentLoaded', function() {
            const form = document.getElementById('registrationForm');
            const vehicleNumberInput = document.getElementById('vehicle_number');
            const fuelTypeInputs = document.getElementsByName('fuel_type');
            const vehicleNumberError = document.getElementById('vehicleNumberError');
            const fuelTypeError = document.getElementById('fuelTypeError');
            const modal = document.getElementById('messageModal');
            const modalTitle = document.getElementById('modalTitle');
            const modalMessage = document.getElementById('modalMessage');
            const modalLoginLink = document.getElementById('modalLoginLink');
            const closeModalBtn = document.getElementById('closeModalBtn');

            function validateVehicleNumber(vehicleNumber) {
                // Updated regex pattern:
                // ^[A-Z]{2,3} - Starts with 2 or 3 uppercase letters
                // \s? - Optional single space
                // \d{3,4}$ - Ends with 3 or 4 digits
                const regex = /^[A-Z]{2,3}\s?\d{3,4}$/;
                return regex.test(vehicleNumber.toUpperCase().trim());
            }

            function getFuelType() {
                for (const input of fuelTypeInputs) {
                    if (input.checked) return input.value;
                }
                return null;
            }

            function showError(element, message) {
                element.textContent = message;
                element.classList.add('show');
            }

            function clearError(element) {
                element.textContent = '';
                element.classList.remove('show');
            }

            function showModal(title, message, showLogin = false) {
                modalTitle.textContent = title;
                modalMessage.textContent = message;
                modalLoginLink.style.display = showLogin ? 'inline-block' : 'none';
                modal.style.display = 'flex';
            }

            form.addEventListener('submit', async function(e) {
                e.preventDefault();

                let valid = true;
                const vehicleNumber = vehicleNumberInput.value.trim();
                const fuelType = getFuelType();
                const customeremail = '{{ Auth::user()->email }}';

                if (!validateVehicleNumber(vehicleNumber)) {
                    showError(vehicleNumberError,
                        'Vehicle number must be in format AB 1234, AAA123, or AAA 1234.'
                    );
                    valid = false;
                } else {
                    clearError(vehicleNumberError);
                }

                if (!fuelType) {
                    showError(fuelTypeError, 'Please select a fuel type.');
                    valid = false;
                } else {
                    clearError(fuelTypeError);
                }

                if (!valid) return;

                try {
                    // Send registration request to Laravel
                    const res = await fetch('/customer/registervehicledata', {
                        method: 'POST',
                        headers: {
                            'Content-Type': 'application/json',
                            'X-CSRF-TOKEN': '{{ csrf_token() }}'
                        },
                        body: JSON.stringify({
                            vehicle_number: vehicleNumber,
                            fuel_type: fuelType,
                            customeremail: customeremail
                        })
                    });

                    const data = await res.json();

                    if (res.ok) {
                        showModal("Vehicle Registration Successful",
                            "Your vehicle has been registered.", true);
                    } else {
                        showModal("Registration Error",
                            data.message ||
                            "Vehicle registration failed. The vehicle number may already be registered."
                        );
                    }
                } catch (err) {
                    console.error(err);
                    showModal("Error", "An unexpected error occurred.");
                }
            });

            form.addEventListener('reset', function() {
                clearError(vehicleNumberError);
                clearError(fuelTypeError);
            });

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
