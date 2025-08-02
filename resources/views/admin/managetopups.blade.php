@extends('admin.adminlayout')

@section('admin_page_title')
    Manage Payment Proof
@endsection

@section('head_section')
    <style>
        .btn-action {
            border: none;
            padding: 8px 20px;
            border-radius: 6px;
            font-weight: 600;
            margin: 2px;
            cursor: pointer;
        }

        .btn-approve {
            background: #16a34a;
            color: #fff;
        }

        .btn-reject {
            background: #dc2626;
            color: #fff;
        }

        .btn-action:hover {
            opacity: 0.9;
        }

        .edit-amount {
            font-weight: 600;
            color: #0a58ca;
            text-decoration: underline dotted;
            cursor: pointer;
        }

        .edit-amount:hover {
            color: #124bb5;
            text-decoration: underline;
        }

        @media (max-width: 600px) {
            .container {
                padding: 8px;
            }

            table {
                font-size: 14px;
            }
        }
    </style>
@endsection

@section('admin_page_content')
    <div class="container" style="padding:20px;flex-grow: 1">
        <h2>Pending Topups</h2>

        @if (session('success'))
            <div style="color: green;">{{ session('success') }}</div>
        @endif
        @if (session('error'))
            <div style="color: red;">{{ session('error') }}</div>
        @endif

        <div class="table-responsive">
            <table id="topupTable" class="display table" style="width:100%;">
                <thead>
                    <tr>
                        <th>Customer</th>
                        <th>Amount (Detected)</th>
                        <th>Proof</th>
                        <th>Actions</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($topups as $topup)
                        <tr>
                            <td>{{ $topup->user->email ?? 'N/A' }}</td>
                            <td>
                                <a href="#" class="edit-amount" data-id="{{ $topup->_id }}"
                                    data-amount="{{ $topup->detected_amount ?? $topup->amount }}"
                                    data-proof="{{ url('storage/' . $topup->proof_image) }}">
                                    {{ $topup->detected_amount ?? $topup->amount }}
                                </a>
                            </td>
                            <td>
                                @if ($topup->proof_image)
                                    <a href="{{ url('storage/' . $topup->proof_image) }}" class="glightbox"
                                        data-glightbox="title: Payment Proof" data-type="image">
                                        <img src="{{ url('storage/' . $topup->proof_image) }}" width="80"
                                            style="border-radius:6px;border:1px solid #eee;">
                                    </a>
                                @else
                                    No proof uploaded
                                @endif
                            </td>
                            <td>
                                <form action="{{ route('admin.topups.approve', $topup->_id) }}" method="POST"
                                    class="action-form" data-action="approve" style="display:inline;">
                                    @csrf
                                    <button type="submit" class="btn-action btn-approve">Approve</button>
                                </form>
                                <form action="{{ route('admin.topups.reject', $topup->_id) }}" method="POST"
                                    class="action-form" data-action="reject" style="display:inline;">
                                    @csrf
                                    <button type="submit" class="btn-action btn-reject">Reject</button>
                                </form>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="4">No pending topups</td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>
@endsection

@section('after_body_section')
    <script>
        $(document).ready(function() {
            $('#topupTable').DataTable();
            const lightbox = GLightbox({
                selector: '.glightbox'
            });

            // SweetAlert2 Confirm for Approve/Reject
            $('.action-form').on('submit', function(e) {
                e.preventDefault();
                var $form = $(this);
                var actionType = $form.data('action');
                var actionMsg = actionType === 'approve' ?
                    'Are you sure you want to <b style="color:#16a34a;">approve</b> this topup?' :
                    'Are you sure you want to <b style="color:#dc2626;">reject</b> this topup?';
                var btnColor = actionType === 'approve' ? '#16a34a' : '#dc2626';

                Swal.fire({
                    title: 'Confirm',
                    html: actionMsg,
                    icon: 'question',
                    showCancelButton: true,
                    confirmButtonColor: btnColor,
                    cancelButtonColor: '#aaa',
                    confirmButtonText: actionType === 'approve' ? 'Yes, Approve' : 'Yes, Reject'
                }).then((result) => {
                    if (result.isConfirmed) {
                        $form.off('submit').submit();
                    }
                });
            });

            // Edit Amount Modal logic (SweetAlert2 as full modal)
            $('.edit-amount').on('click', function(e) {
                e.preventDefault();
                let id = $(this).data('id');
                let amount = $(this).data('amount');
                let proof = $(this).data('proof');
                Swal.fire({
                    title: 'Edit Amount Detected',
                    html: `
                        <div style="margin-bottom:14px;">
                            <img src="${proof}" alt="Payment Proof"
                                 style="width:100%;max-height:55vh;object-fit:contain;
                                 border-radius:14px;box-shadow:0 2px 12px rgba(0,0,0,0.12);margin-bottom:16px;">
                            <input type="number" min="0.01" step="0.01" id="modalAmountInput"
                                   class="swal2-input" value="${amount}" style="font-size:1.15rem;">
                        </div>
                    `,
                    showCancelButton: true,
                    confirmButtonText: 'Update',
                    width: 600,
                    preConfirm: () => {
                        let newAmount = $('#modalAmountInput').val();
                        if (!newAmount || newAmount <= 0) {
                            Swal.showValidationMessage('Please enter a valid amount');
                        }
                        return newAmount;
                    }
                }).then(result => {
                    if (result.isConfirmed) {
                        let newAmount = result.value;
                        let csrf = '{{ csrf_token() }}';
                        $.ajax({
                            url: '/admin/topups/' + id + '/update-amount',
                            method: 'POST',
                            headers: {
                                'X-CSRF-TOKEN': csrf
                            },
                            data: {
                                detected_amount: newAmount
                            },
                            success: function(resp) {
                                $('a.edit-amount[data-id="' + id + '"]')
                                    .text(newAmount)
                                    .data('amount', newAmount);
                                Swal.fire('Success', 'Amount updated!', 'success');
                            },
                            error: function(xhr) {
                                Swal.fire('Error', xhr.responseJSON?.message ||
                                    'Error updating amount', 'error');
                            }
                        });
                    }
                });
            });
        });
    </script>
@endsection
