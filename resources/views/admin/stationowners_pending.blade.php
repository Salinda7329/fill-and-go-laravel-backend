@extends('admin.adminlayout')

@section('admin_page_title')
    Manage Station Owner Registrations
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

        @media (max-width: 700px) {
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
    <div class="container" style="padding:20px;flex-grow:1">
        <h2>Pending Station Owners</h2>
        @if (session('success'))
            <div style="color:green">{{ session('success') }}</div>
        @endif
        @if (session('error'))
            <div style="color:red">{{ session('error') }}</div>
        @endif

        @if (($owners ?? collect([]))->count())
            <div class="table-responsive">
                <table id="ownersTable" class="display table" style="width:100%;">
                    <thead>
                        <tr>
                            <th>Email</th>
                            <th>Station Name</th>
                            <th>Station Address</th>
                            <th>Contact Number</th>
                            <th>Registered At</th>
                            <th>Actions</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach ($owners as $owner)
                            <tr>
                                <td>{{ $owner->email }}</td>
                                <td>{{ $owner->station_name ?? '-' }}</td>
                                <td>{{ $owner->station_address ?? '-' }}</td>
                                <td>{{ $owner->contact_number ?? '-' }}</td>
                                <td>{{ \Carbon\Carbon::parse($owner->created_at)->format('Y-m-d H:i') }}</td>
                                <td>
                                    <form action="{{ route('admin.stationowners.approve', $owner->_id) }}" method="POST"
                                        class="action-form" data-action="approve" style="display:inline;">
                                        @csrf
                                        <button type="submit" class="btn-action btn-approve">Approve</button>
                                    </form>
                                    <form action="{{ route('admin.stationowners.reject', $owner->_id) }}" method="POST"
                                        class="action-form" data-action="reject" style="display:inline;">
                                        @csrf
                                        <button type="submit" class="btn-action btn-reject">Reject</button>
                                    </form>
                                </td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
        @else
            <div class="no-topups-message" style="padding:40px 0; text-align:center; color:#555;">
                <div style="font-size:20px; font-weight:600; color:#888;">No pending station owners.</div>
                <div style="margin-top:8px;">There are no pending station owner registrations at the moment.</div>
            </div>
        @endif
    </div>
@endsection

@section('after_body_section')
    <script>
        $(document).ready(function() {
            $('#ownersTable').DataTable({
                responsive: true
            });

            // SweetAlert2 Confirm for Approve/Reject
            $('.action-form').on('submit', function(e) {
                e.preventDefault();
                var $form = $(this);
                var actionType = $form.data('action');
                var actionMsg = actionType === 'approve' ?
                    'Are you sure you want to <b style="color:#16a34a;">approve</b> this station owner?' :
                    'Are you sure you want to <b style="color:#dc2626;">reject</b> this station owner?';
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
