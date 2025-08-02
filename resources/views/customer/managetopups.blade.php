@extends('customer.customerlayout')

@section('customer_page_title')
    My Topup Requests
@endsection

@section('customer_head_section')
    <style>
        .status-badge {
            padding: 3px 10px;
            border-radius: 8px;
            font-size: 0.97em;
            font-weight: 600;
            display: inline-block;
        }

        .status-pending {
            background: #f59e42;
            color: #fff;
        }

        .status-approved {
            background: #1eae66;
            color: #fff;
        }

        .status-rejected {
            background: #e53e3e;
            color: #fff;
        }

        .proof-thumb {
            border-radius: 5px;
            border: 1px solid #eee;
        }

        @media (max-width: 600px) {
            table {
                font-size: 14px;
            }
        }
    </style>
@endsection

@section('customer_page_content')
    <div class="container" style="padding:20px;flex-grow:1">
        <h2>My Topup Requests</h2>
        @if (($topups ?? collect([]))->count())
            <div class="table-responsive">
                <table id="customerTopupTable" class="display table" style="width:100%;">
                    <thead>
                        <tr>
                            <th>Date</th>
                            <th>Reference</th>
                            <th>Amount</th>
                            <th>Detected</th>
                            <th>Status</th>
                            <th>Proof</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach ($topups as $t)
                            <tr>
                                <td>{{ \Carbon\Carbon::parse($t->created_at)->format('Y-m-d H:i') }}</td>
                                <td>{{ $t->reference_number ?? '-' }}</td>
                                <td>{{ number_format($t->amount, 2) }}</td>
                                <td>{{ $t->detected_amount ?? '-' }}</td>
                                <td>
                                    <span class="status-badge status-{{ $t->status }}">
                                        {{ ucfirst($t->status) }}
                                    </span>
                                </td>
                                <td>
                                    @if ($t->proof_image)
                                        <a href="{{ url('storage/' . $t->proof_image) }}" class="glightbox"
                                            data-glightbox="title: Payment Proof">
                                            <img src="{{ url('storage/' . $t->proof_image) }}" width="50"
                                                class="proof-thumb">
                                        </a>
                                    @else
                                        -
                                    @endif
                                </td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
        @else
            <div class="no-topups-message" style="padding:40px 0; text-align:center; color:#555;">
                <svg width="80" height="80" fill="none" viewBox="0 0 24 24"
                    style="margin-bottom:16px;opacity:0.4;">
                </svg>
                <div style="font-size:20px; font-weight:600; color:#888;">No topup requests found.</div>
                <div style="margin-top:8px;">You haven’t made any topup requests yet.</div>
            </div>
        @endif
    </div>

@endsection

@section('customer_after_body_section')
    <script>
        $(document).ready(function() {
            if ($('#customerTopupTable').length) {
                $('#customerTopupTable').DataTable({
                    order: [
                        [0, 'desc']
                    ],
                    responsive: true
                });
                GLightbox({
                    selector: '.glightbox'
                });
            }
        });
    </script>
@endsection
