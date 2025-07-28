<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <link rel="icon" type="image/png" href="{{ asset('logos/fill_and_go_logo.png') }}">
    <title>@yield('page_title')| Fill and Go</title>
    <link rel="stylesheet" href="{{ asset('css/publichomepage.css') }}">
    @yield('head_section')
</head>

<body>
    <!-- Header -->
    <header class="header">
        <div class="container">
            <div class="logo" style="display: flex; align-items: center;">
                <a href="/" class="logo-link">
                    <img src="{{ asset('logos/fill_and_go_logo.png') }}" alt="fill and go logo" class="responsive-logo">
                    <span id="fill">&nbsp;Fill </span><span id="and">&nbsp;and </span><span
                        id="go">&nbsp;Go</span>
                </a>
            </div>
            <nav class="nav">
                <a href="/customer/register" class="btn btn-outline" style="text-decoration:none;">Register</a>
                <a href="/login" class="btn btn-primary" style="text-decoration:none;">Login</a>
            </nav>
        </div>
    </header>

    {{-- page content secion  --}}
    @yield('page_content')


    <!-- Footer -->
    <footer class="footer">
        <div class="container">
            <div class="footer-content">
                <div class="footer-brand">
                    <h3>Fill and Go</h3>
                    <p>&copy; 2025 Fill and Go. All rights reserved.</p>
                </div>
                <div class="footer-links">
                    <a href="mailto:contact@fillandgo.com">contact@fillandgo.com</a>
                    <a href="#privacy">Privacy Policy</a>
                    <a href="#terms">Terms of Service</a>
                </div>
            </div>
        </div>
    </footer>

</body>

<script src="{{ asset('js/publichomepage.js') }}"></script>
@yield('after_body_section')

</html>
