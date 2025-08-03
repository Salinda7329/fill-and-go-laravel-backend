@extends('customer.customerlayout')

@section('customer_page_title')
    My Vehicles
@endsection

@section('customer_page_content')
    <div class="container" style="padding:20px;flex-grow:1">
        <h2>My Registered Vehicles</h2>
        @if ($vehicles->count())
            <div class="table-responsive">
                <table class="display table" style="width:100%;">
                    <thead>
                        <tr>
                            <th>Vehicle Number</th>
                            <th>Fuel Type</th>
                            <th>Status</th>
                            <th>Registered At</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach ($vehicles as $vehicle)
                            <tr>
                                <td>{{ $vehicle->vehicle_number }}</td>
                                <td>{{ $vehicle->fuel_type ?? '-' }}</td>
                                <td>
                                    @if ($vehicle->status == 1)
                                        <span style="color: #16a34a;font-weight:600;">Active</span>
                                    @else
                                        <span style="color: #dc2626;font-weight:600;">Inactive</span>
                                    @endif
                                </td>
                                <td>{{ \Carbon\Carbon::parse($vehicle->created_at)->format('Y-m-d H:i') }}</td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
        @else
            <div class="no-topups-message" style="padding:40px 0; text-align:center; color:#555;">
                <div style="font-size:20px; font-weight:600; color:#888;">No vehicles found.</div>
                <div style="margin-top:8px;">You haven’t registered any vehicles yet.</div>
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
