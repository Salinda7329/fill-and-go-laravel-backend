@extends('customer.customerlayout')

@section('customer_page_title')
    My Vehicle Logs
@endsection

@section('customer_page_content')
    <div class="container" style="padding:20px;flex-grow:1">
        <h2 style="margin-bottom: 18px;">My Vehicle Logs</h2>

        @if (count($logs) > 0)
            <div class="table-responsive">
                <table class="display table" style="width:100%;">
                    <thead>
                        <tr>
                            <th>#</th>
                            <th>Vehicle Number</th>
                            <th>Amount (Rs.)</th>
                            <th>Litres</th>
                            <th>Entry Time</th>
                            <th>Exit Time</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach ($logs as $idx => $log)
                            <tr>
                                <td>{{ $idx + 1 }}</td>
                                <td>{{ $log->vehicle_number ?? ($log['vehicle_number'] ?? '') }}</td>
                                <td>{{ $log->amount ?? ($log['amount'] ?? '') }}</td>
                                <td>{{ $log->litres ?? ($log['litres'] ?? '') }}</td>
                                <td>
                                    {{ !empty($log->gate_open_time ?? ($log['gate_open_time'] ?? null))
                                        ? \Carbon\Carbon::parse($log->gate_open_time ?? $log['gate_open_time'])->format('Y-m-d H:i:s')
                                        : '-' }}
                                </td>
                                <td>
                                    {{ !empty($log->exit_time ?? ($log['exit_time'] ?? null))
                                        ? \Carbon\Carbon::parse($log->exit_time ?? $log['exit_time'])->format('Y-m-d H:i:s')
                                        : '-' }}
                                </td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
        @else
            <div class="no-topups-message" style="padding:40px 0; text-align:center; color:#555;">
                <div style="font-size:20px; font-weight:600; color:#888;">No vehicle logs found.</div>
                <div style="margin-top:8px;">You have no vehicle activity yet.</div>
            </div>
        @endif
    </div>
@endsection

@section('customer_after_body_section')
    <script>
        $(document).ready(function() {
            $('.table').DataTable();
        });
    </script>
@endsection
