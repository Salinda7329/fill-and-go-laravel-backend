<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <link rel="icon" type="image/png" href="{{ asset('logos/fill_and_go_logo.png') }}">
    <script src="{{ asset('js/jquery-3.7.1.min.js') }}"></script>
    <title>@yield('page_title')| Fill and Go</title>
    <link rel="stylesheet" href="{{ asset('css/publichomepage.css') }}">
    @vite(['resources/css/app.css', 'resources/js/app.js']) <!-- Loads jQuery -->
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
            <nav class="nav" style="display: flex; align-items: center; gap: 1rem;">
                @auth
                    @php
                        $roleMap = [
                            1 => 'Admin',
                            2 => 'Station Owner',
                            3 => 'Customer',
                        ];
                        $roleName = $roleMap[Auth::user()->role] ?? 'Unknown';
                    @endphp
                    <div class="greeting-box"
                        style="display: flex; flex-direction: row; align-items: center; line-height: 1.2;">
                        Hello,
                        <span class="greeting" style="font-weight: 600; color: #f26522;">
                            {{ Auth::user()->email }}</span>
                        <span class="role" style="font-size: 0.85rem; color: #888;">({{ $roleName }})</span>
                    </div>
                @else
                    <span class="greeting" style="font-weight: 600; color: #333;">Hello, Guest</span>
                @endauth
                {{-- <form action="{{ route('logout') }}" method="POST" style="margin: 0;">
                    @csrf
                    <button type="submit" class="btn btn-outline"
                        style="text-decoration: none; padding: 6px 12px; border: 1px solid #555; background-color: white; color: #333; border-radius: 4px;">Logout</button>
                </form> --}}
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
