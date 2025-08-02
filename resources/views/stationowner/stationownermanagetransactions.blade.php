@extends('stationowner.stationownerlayout')

@section('stationowner_page_title')
    Manage Transactions
@endsection

@section('stationowner_head_section')
    <meta name="csrf-token" content="{{ csrf_token() }}">
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

            .container {
                padding: 8px;
            }
        }
    </style>
@endsection

@section('stationowner_page_content')
    <div class="container" style="padding:20px;flex-grow:1;">
        <h2>Transactions</h2>
        @if (($transactions ?? collect([]))->count())
            <div class="table-responsive">
                <table id="stationOwnerTransactionsTable" class="display table" style="width:100%;">
                    <thead>
                        <tr>
                            <th>Date</th>
                            <th>Customer</th>
                            <th>Amount</th>
                            <th>Status</th>
                            <th>Proof</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach ($transactions as $t)
                            <tr>
                                <td>{{ \Carbon\Carbon::parse($t->created_at)->format('Y-m-d H:i') }}</td>
                                <td>{{ $t->user->email ?? '-' }}</td>
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
                        @endforeach
                    </tbody>
                </table>
            </div>
        @else
            <div style="padding:40px 0; text-align:center; color:#555;">
                <div style="font-size:20px; font-weight:600; color:#888;">No transactions found.</div>
                <div style="margin-top:8px;">You don’t have any transactions yet.</div>
            </div>
        @endif
    </div>
@endsection

@section('stationowner_after_body_section')
    <script>
        $(document).ready(function() {
            $('#stationOwnerTransactionsTable').DataTable({
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
