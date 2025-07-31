@extends('customer.customerlayout')

@section('customer_page_title')
    Vehicle Registration Form
@endsection

@section('customer_head_section')
    <link rel="stylesheet" href="{{ asset('css/customerregistrationform.css') }}">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <script src="https://code.jquery.com/jquery-3.7.1.min.js"></script>
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
                    <input type="hidden" id="customeremail" name="customeremail" value="{{ Auth::user()->email }}">
                    <input type="hidden" id="firebase_uid" name="firebase_uid" value="{{ Auth::user()->firebase_uid }}">

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
        $(document).ready(function() {
            const $form = $('#registrationForm');
            const $vehicleNumberInput = $('#vehicle_number');
            const $fuelTypeInputs = $('input[name="fuel_type"]');
            const $emailInput = $('#customeremail');
            const $firebaseUidInput = $('#firebase_uid');
            const $vehicleNumberError = $('#vehicleNumberError');
            const $fuelTypeError = $('#fuelTypeError');
            const $emailError = $('#emailError');
            const $firebaseUidError = $('#firebaseUidError');
            const $modal = $('#messageModal');
            const $modalTitle = $('#modalTitle');
            const $modalMessage = $('#modalMessage');
            const $modalLoginLink = $('#modalLoginLink');
            const $closeModalBtn = $('#closeModalBtn');

            function validateVehicleNumber(vehicleNumber) {
                const regex = /^[A-Z]{2,3}\s?\d{3,4}$/;
                return regex.test(vehicleNumber.toUpperCase().trim());
            }

            function getFuelType() {
                return $fuelTypeInputs.filter(':checked').val();
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
                const vehicleNumber = $vehicleNumberInput.val().trim();
                const fuelType = getFuelType();
                const customeremail = $emailInput.val().trim();
                const firebaseUid = $firebaseUidInput.val().trim();

                clearError($vehicleNumberError);
                clearError($fuelTypeError);
                clearError($emailError);
                clearError($firebaseUidError);

                if (!customeremail || !firebaseUid) {
                    showModal('Authentication Error', 'Please log in to register a vehicle.', true);
                    return;
                }

                if (!validateVehicleNumber(vehicleNumber)) {
                    showError($vehicleNumberError,
                        'Vehicle number must be in format AB 1234, AAA123, or AAA 1234.');
                    valid = false;
                }

                if (!fuelType) {
                    showError($fuelTypeError, 'Please select a fuel type.');
                    valid = false;
                }

                if (!valid) return;

                const csrfToken = $('meta[name="csrf-token"]').attr('content');
                console.log('CSRF Token for vehicle registration:', csrfToken); // Debug CSRF token

                $.ajax({
                    url: '/customer/registervehicledata',
                    method: 'POST',
                    contentType: 'application/json',
                    headers: {
                        'X-CSRF-TOKEN': csrfToken
                    },
                    data: JSON.stringify({
                        vehicle_number: vehicleNumber,
                        fuel_type: fuelType,
                        customeremail: customeremail,
                        firebase_uid: firebaseUid
                    }),
                    success: function(data) {
                        if (data.success) {
                            const vehicleId = data.vehicle && data.vehicle._id ? data.vehicle
                                ._id : 'N/A';

                            showModal(
                                'Vehicle Registration Success',
                                'Vehicle registered successfully. Vehicle ID: ' + vehicleId
                            );

                            // Delay redirect so user sees the success message
                            setTimeout(() => {
                                window.location.href = '/customer/dashboard';
                            }, 2000);
                        } else {
                            showError($vehicleNumberError, data.message);
                            showModal('Vehicle Registration Error', data.message ||
                                'Vehicle registration failed.');
                        }
                    },
                    error: function(xhr) {
                        console.error('Vehicle registration failed:', xhr.responseJSON);
                        const message = xhr.responseJSON?.message ||
                            'An unexpected error occurred.';
                        showError($vehicleNumberError, message);
                        showModal('Vehicle Registration Error', message);
                    }
                });
            });

            $form.on('reset', function() {
                clearError($vehicleNumberError);
                clearError($fuelTypeError);
                clearError($emailError);
                clearError($firebaseUidError);
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
