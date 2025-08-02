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
        <div class="table-responsive">
            <table id="customerTopupTable" class="display table" style="width:100%;">
                <thead>
                    <tr>
                        <th>Date</th>
                        <th>Reference</th>
                        <th>Amount</th>
                        <th>Status</th>
                        <th>Proof</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($topups as $t)
                        <tr>
                            <td>{{ \Carbon\Carbon::parse($t->created_at)->format('Y-m-d H:i') }}</td>
                            <td>{{ $t->reference_number ?? '-' }}</td>
                            <td>{{ number_format($t->amount, 2) }}</td>
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
                    @empty
                        <tr>
                            <td colspan="6">No topups found.</td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>
@endsection

@section('customer_after_body_section')
    <script>
        $(document).ready(function() {
            $('#customerTopupTable').DataTable({
                order: [
                    [0, 'desc']
                ],
                responsive: true
            });
            GLightbox({
                selector: '.glightbox'
            });
        });
    </script>
@endsection
