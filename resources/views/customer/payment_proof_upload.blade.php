@extends('customer.customerlayout')

@section('customer_page_title')
    Upload Payment Proof
@endsection

@section('customer_head_section')
    <link rel="stylesheet" href="{{ asset('css/customerregistrationform.css') }}">
    <meta name="csrf-token" content="{{ csrf_token() }}">
@endsection

@section('customer_page_content')
    <section class="registration-section" style="padding: 10px">
        <div class="container">
            <h3 class="section-title">Upload Payment Proof</h3>

            <div id="messageModal" class="modal"
                style="display:none; position:fixed; z-index:9999; left:0; top:0; width:100vw; height:100vh; background:rgba(0,0,0,0.4); align-items:center; justify-content:center;">
                <div
                    style="background:#fff; padding:32px 24px; border-radius:12px; max-width:90vw; width:350px; text-align:center; box-shadow:0 8px 32px rgba(0,0,0,0.2); position:relative;">
                    <h3 id="modalTitle" style="margin-bottom:16px; color:#002060;"></h3>
                    <p id="modalMessage" style="margin-bottom:24px;"></p>
                    <button id="closeModalBtn"
                        style="background:#f26522; color:#fff; border:none; border-radius:8px; padding:10px 28px; font-size:16px; font-weight:600; cursor:pointer; margin-top:16px;">Close</button>
                </div>
            </div>

            <form id="paymentProofForm" class="registration-form" enctype="multipart/form-data">
                @csrf
                <div class="form-group">
                    <label for="amount">Amount *</label>
                    <input type="number" id="amount" name="amount" required step="0.01">
                    <span class="error-message" id="amountError"></span>
                </div>

                <div class="form-group">
                    <label for="reference_number">Reference Number (if available)</label>
                    <input type="text" id="reference_number" name="reference_number">
                </div>

                <div class="form-group">
                    <label for="proof_image">Upload Payment Screenshot *</label>
                    <input type="file" id="proof_image" name="proof_image" accept="image/*" required>
                    <span class="error-message" id="proofImageError"></span>
                </div>

                <input type="hidden" name="customeremail" value="{{ Auth::user()->email }}">
                <input type="hidden" name="firebase_uid" value="{{ Auth::user()->firebase_uid }}">

                <div class="form-buttons">
                    <button type="submit" class="btn btn-submit">Submit Proof</button>
                    <button type="reset" class="btn btn-reset">Reset</button>
                </div>
            </form>
        </div>
    </section>
@endsection

@section('customer_after_body_section')
    <script>
        $(document).ready(function() {
            const $modal = $('#messageModal');
            const $modalTitle = $('#modalTitle');
            const $modalMessage = $('#modalMessage');
            const $closeModalBtn = $('#closeModalBtn');

            function showModal(title, message) {
                $modalTitle.text(title);
                $modalMessage.text(message);
                $modal.css('display', 'flex');
            }

            $closeModalBtn.on('click', () => $modal.hide());
            $modal.on('click', function(e) {
                if (e.target === $modal[0]) $modal.hide();
            });

            $('#paymentProofForm').on('submit', function(e) {
                e.preventDefault();
                const formData = new FormData(this);

                $.ajax({
                    url: '/customer/upload-payment-proof',
                    method: 'POST',
                    data: formData,
                    processData: false,
                    contentType: false,
                    success: function(data) {
                        if (data.success) {
                            showModal('Upload Success', data.message);
                        } else {
                            showModal('Upload Failed', data.message || 'An error occurred.');
                        }
                    },
                    error: function(xhr) {
                        const msg = xhr.responseJSON?.message || 'An error occurred.';
                        showModal('Error', msg);
                    }
                });
            });
        });
    </script>
@endsection
