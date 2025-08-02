@extends('admin.adminlayout')

@section('admin_page_title')
    Manage Payment Proof
@endsection

@section('admin_page_content')
    <div class="container" style="padding:20px;">
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
                            <td>{{ $topup->amount }}</td>
                            <td>
                                @if ($topup->proof_image)
                                    <a href="{{ asset('storage/' . $topup->proof_image) }}" class="glightbox"
                                        data-glightbox="title: Payment Proof" data-type="image">
                                        <img src="{{ asset('storage/' . $topup->proof_image) }}" width="80"
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

@section('after_body_section')
    <!-- DataTables JS -->
    <script src="https://cdn.datatables.net/1.13.6/js/jquery.dataTables.min.js"></script>
    <!-- GLightbox -->
    <script src="https://cdn.jsdelivr.net/npm/glightbox/dist/js/glightbox.min.js"></script>
    <!-- SweetAlert2 -->
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>

    <script>
        $(document).ready(function() {
            // DataTables init
            $('#topupTable').DataTable();

            // GLightbox init
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
        });
    </script>
@endsection
