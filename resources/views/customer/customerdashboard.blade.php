@extends('customer.customerlayout')

@section('customer_page_title')
    Dashboard
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
        <!-- Dashboard Stats Cards -->
        <div id="dashboardCards" style="display: flex; gap: 32px; flex-wrap: wrap; justify-content: flex-start;">
            <!-- Cards will be loaded here by AJAX -->
        </div>
    </div>
@endsection

@section('customer_after_body_section')
    <script>
        $(document).ready(function() {
            function loadDashboardStats() {
                $.ajax({
                    url: '{{ route('customer.dashboard.stats') }}',
                    method: 'GET',
                    success: function(data) {
                        $('#dashboardCards').html(`
    <div style="
        background: #fff;
        border-radius: 18px; box-shadow:0 6px 32px rgba(242,101,34,0.06);
        padding: 24px 36px; min-width:240px; flex:1 1 220px; max-width:340px;
        display: flex; flex-direction: column; align-items: flex-start;">
        <div style="font-size:17px;font-weight:600;color:#002060;margin-bottom:10px;">
            Current Balance
        </div>
        <div style="font-size:2.2rem;font-weight:700;color:#f26522;margin-bottom:2px;">
            Rs. ${parseFloat(data.balance).toLocaleString('en-US', {minimumFractionDigits:2})}
        </div>
        <span style="color:#666;font-size:13px;">Available for top-ups & fuel</span>
    </div>
    <div style="
        background: #fff;
        border-radius: 18px; box-shadow:0 6px 32px rgba(0,32,96,0.06);
        padding: 24px 36px; min-width:240px; flex:1 1 220px; max-width:340px;
        display: flex; flex-direction: column; align-items: flex-start;">
        <div style="font-size:17px;font-weight:600;color:#002060;margin-bottom:10px;">
            Top-up Requests
        </div>
        <div style="font-size:2.2rem;font-weight:700;color:#002060;margin-bottom:2px;">
            ${data.topupCount}
        </div>
        <span style="color:#666;font-size:13px;">Total submitted top-ups</span>
    </div>
`);

                    },
                    error: function() {
                        $('#dashboardCards').html(
                            '<div style="color:red;">Failed to load dashboard stats.</div>');
                    }
                });
            }

            loadDashboardStats();
        });
    </script>
@endsection
