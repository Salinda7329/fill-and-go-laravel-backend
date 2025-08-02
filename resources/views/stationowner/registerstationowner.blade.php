@extends('layouts.publicpageslayout')

@section('page_title', 'Station Owner Registration')

@section('head_section')
    <link rel="stylesheet" href="{{ asset('css/customerregistrationform.css') }}">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <script src="https://code.jquery.com/jquery-3.7.1.min.js"></script>
    <!-- Firebase scripts as in customer -->
@endsection

@section('page_content')
    <section class="registration-section">
        <div class="container"  style="margin-top: 50px; padding: 20px;">
            <h2>Register as a Station Owner</h2>
            <form id="stationOwnerForm" class="registration-form" method="POST" action="/stationowner/registerdata">
                @csrf
                <div class="form-group">
                    <label for="station_name">Station Name *</label>
                    <input type="text" id="station_name" name="station_name" required>
                </div>
                <div class="form-group">
                    <label for="station_address">Station Address *</label>
                    <input type="text" id="station_address" name="station_address" required>
                </div>
                <div class="form-group">
                    <label for="contact_number">Contact Number *</label>
                    <input type="text" id="contact_number" name="contact_number" required>
                </div>
                <div class="form-group">
                    <label for="email">Email Address *</label>
                    <input type="email" id="email" name="email" required>
                </div>
                <div class="form-group">
                    <label for="password">Password *</label>
                    <input type="password" id="password" name="password" required minlength="8">
                </div>
                <div class="form-group">
                    <label for="password_confirmation">Confirm Password *</label>
                    <input type="password" id="password_confirmation" name="password_confirmation" required minlength="8">
                </div>
                <button type="submit" class="btn btn-submit">Register Now</button>
            </form>
        </div>
    </section>
@endsection

@section('after_body_section')
    <script>
        $(document).ready(function() {
            $('#stationOwnerForm').on('submit', function(e) {
                e.preventDefault();
                var form = $(this);
                var data = form.serialize();
                $.ajax({
                    url: '/stationowner/registerdata',
                    method: 'POST',
                    data: data,
                    headers: {
                        'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
                    },
                    success: function(resp) {
                        alert(resp.message || 'Registered! Pending admin approval.');
                        form.trigger('reset');
                    },
                    error: function(xhr) {
                        alert(xhr.responseJSON?.message || 'Registration failed');
                    }
                });
            });
        });
    </script>
@endsection
