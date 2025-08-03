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

    </div>

@endsection

@section('customer_after_body_section')
    {{-- <script>
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
    </script> --}}
@endsection
