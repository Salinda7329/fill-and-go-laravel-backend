<style>
    /* Reset and Base Styles */
    * {
        margin: 0;
        padding: 0;
        box-sizing: border-box;
    }

    body {
        font-family: -apple-system, BlinkMacSystemFont, 'Segoe UI', Roboto, Oxygen, Ubuntu, Cantarell, sans-serif;
        background-color: #f8f9fa;
        color: #333;
    }

    /* Sidebar Container */
    .sidebar {
        position: fixed;
        top: 80px;
        left: 0;
        width: 280px;
        height: 90vh;
        background: linear-gradient(135deg, #ffffff 0%, #f8f9fa 100%);
        box-shadow: 2px 0 15px rgba(0, 0, 0, 0.1);
        transition: all 0.3s cubic-bezier(0.4, 0, 0.2, 1);
        z-index: 1000;
        overflow-y: auto;
        overflow-x: hidden;
    }

    .sidebar.collapsed {
        width: 70px;
    }

    .sidebar.mobile-hidden {
        transform: translateX(-100%);
    }

    /* Sidebar Header */
    .sidebar-header {
        margin-top: 40px;
        padding: 20px;
        border-bottom: 1px solid #e9ecef;
        background: white;
        position: relative;
    }

    .sidebar.collapsed .sidebar-header {
        padding: 20px 10px;
    }

    /* Logo Styles */
    .logo-link {
        display: flex;
        align-items: center;
        text-decoration: none;
        transition: all 0.3s ease;
    }

    .responsive-logo {
        width: 40px;
        height: 40px;
        margin-right: 12px;
        border-radius: 8px;
        transition: all 0.3s ease;
        flex-shrink: 0;
    }

    .sidebar.collapsed .responsive-logo {
        margin-right: 0;
    }

    .logo-text {
        font-size: 20px;
        font-weight: 700;
        transition: all 0.3s ease;
        white-space: nowrap;
        overflow: hidden;
    }

    .sidebar.collapsed .logo-text {
        opacity: 0;
        width: 0;
    }

    #fill {
        color: #f26522;
        text-shadow: 0 0 8px rgba(242, 101, 34, 0.3);
    }

    #and {
        color: #002060;
        text-shadow: 0 0 6px rgba(0, 32, 96, 0.2);
    }

    #go {
        background: linear-gradient(135deg, #f26522, #002060);
        -webkit-background-clip: text;
        -webkit-text-fill-color: transparent;
        background-clip: text;
        text-shadow: 0 0 10px rgba(242, 101, 34, 0.2);
    }

    /* Toggle Button */
    .sidebar-toggle {
        position: absolute;
        top: 50%;
        right: -5px;
        transform: translateY(-50%);
        width: 30px;
        height: 30px;
        background: #f26522;
        border: none;
        border-radius: 50%;
        cursor: pointer;
        display: flex;
        align-items: center;
        justify-content: center;
        box-shadow: 0 2px 8px rgba(242, 101, 34, 0.3);
        transition: all 0.3s ease;
        z-index: 1001;
    }

    .sidebar-toggle:hover {
        background: #e55a1f;
        transform: translateY(-50%) scale(1.1);
        box-shadow: 0 4px 12px rgba(242, 101, 34, 0.4);
    }

    .sidebar-toggle svg {
        width: 16px;
        height: 16px;
        fill: white;
        transition: transform 0.3s ease;
    }

    .sidebar.collapsed .sidebar-toggle svg {
        transform: rotate(180deg);
    }

    /* Mobile Toggle Button */
    .mobile-toggle {
        display: none;
        position: fixed;
        top: 20px;
        left: 20px;
        width: 45px;
        height: 45px;
        background: #f26522;
        border: none;
        border-radius: 8px;
        cursor: pointer;
        z-index: 1001;
        box-shadow: 0 4px 12px rgba(242, 101, 34, 0.3);
        transition: all 0.3s ease;
    }

    .mobile-toggle:hover {
        background: #e55a1f;
        transform: scale(1.05);
    }

    .mobile-toggle svg {
        width: 24px;
        height: 24px;
        fill: white;
    }

    /* Navigation Menu */
    .sidebar-nav {
        padding: 20px 0;
    }

    .nav-item {
        margin-bottom: 4px;
    }

    .nav-link {
        display: flex;
        align-items: center;
        padding: 12px 20px;
        color: #333;
        text-decoration: none;
        transition: all 0.3s ease;
        position: relative;
        border-radius: 0 25px 25px 0;
        margin-right: 20px;
    }

    .sidebar.collapsed .nav-link {
        padding: 12px 22px;
        margin-right: 0;
        border-radius: 0;
        justify-content: center;
    }

    .nav-link:hover {
        background: linear-gradient(135deg, rgba(242, 101, 34, 0.1), rgba(0, 32, 96, 0.05));
        color: #f26522;
        transform: translateX(5px);
    }

    .sidebar.collapsed .nav-link:hover {
        transform: none;
    }

    .nav-link.active {
        background: linear-gradient(135deg, #f26522, #e55a1f);
        color: white;
        box-shadow: 0 4px 12px rgba(242, 101, 34, 0.3);
    }

    .nav-link.active::before {
        content: '';
        position: absolute;
        left: 0;
        top: 0;
        bottom: 0;
        width: 4px;
        background: #002060;
    }

    .nav-icon {
        width: 20px;
        height: 20px;
        margin-right: 12px;
        flex-shrink: 0;
        fill: currentColor;
    }

    .sidebar.collapsed .nav-icon {
        margin-right: 0;
    }

    .nav-text {
        font-weight: 500;
        transition: all 0.3s ease;
        white-space: nowrap;
        overflow: hidden;
    }

    .sidebar.collapsed .nav-text {
        opacity: 0;
        width: 0;
    }

    .nav-arrow {
        width: 16px;
        height: 16px;
        margin-left: auto;
        transition: transform 0.3s ease;
        fill: currentColor;
    }

    .sidebar.collapsed .nav-arrow {
        display: none;
    }

    /* Dropdown Styles */
    .nav-dropdown {
        max-height: 0;
        overflow: hidden;
        transition: max-height 0.3s ease;
        background: rgba(248, 249, 250, 0.5);
    }

    .nav-dropdown.open {
        max-height: 300px;
    }

    .sidebar.collapsed .nav-dropdown {
        display: none;
    }

    .nav-dropdown .nav-link {
        padding: 10px 20px 10px 52px;
        font-size: 14px;
        margin-right: 10px;
        border-radius: 0 20px 20px 0;
    }

    .nav-dropdown .nav-link::before {
        content: '';
        position: absolute;
        left: 32px;
        top: 50%;
        width: 6px;
        height: 6px;
        background: #dee2e6;
        border-radius: 50%;
        transform: translateY(-50%);
        transition: all 0.3s ease;
    }

    .nav-dropdown .nav-link:hover::before,
    .nav-dropdown .nav-link.active::before {
        background: #f26522;
        transform: translateY(-50%) scale(1.2);
    }

    .nav-item.open .nav-arrow {
        transform: rotate(90deg);
    }

    /* Logout Button */
    .logout-btn {
        position: absolute;
        bottom: 20px;
        left: 5px;
        right: 20px;
        background: linear-gradient(135deg, #dc3545, #c82333);
        color: white;
        border: none;
        padding: 12px;
        border-radius: 8px;
        cursor: pointer;
        font-weight: 500;
        transition: all 0.3s ease;
        display: flex;
        align-items: center;
        justify-content: center;
    }

    .sidebar.collapsed .logout-btn {
        left: 10px;
        right: 10px;
        padding: 12px 8px;
    }

    .logout-btn:hover {
        background: linear-gradient(135deg, #c82333, #bd2130);
        transform: translateY(-2px);
        box-shadow: 0 4px 12px rgba(220, 53, 69, 0.3);
    }

    .logout-btn svg {
        width: 18px;
        height: 18px;
        margin-right: 8px;
        fill: currentColor;
    }

    .sidebar.collapsed .logout-btn svg {
        margin-right: 0;
    }

    .logout-btn .nav-text {
        font-size: 14px;
    }

    /* Overlay for Mobile */
    .sidebar-overlay {
        display: none;
        position: fixed;
        top: 0;
        left: 0;
        width: 100%;
        height: 100%;
        background: rgba(0, 0, 0, 0.5);
        z-index: 999;
        opacity: 0;
        transition: opacity 0.3s ease;
        pointer-events: none;
        /* This is the key change */
    }

    .sidebar-overlay.active {
        opacity: 1;
        pointer-events: auto;
        /* Only allow clicks when active */
    }

    /* Responsive Design */
    @media (max-width: 768px) {
        .sidebar {
            width: 280px;
            transform: translateX(-100%);
            z-index: 1000;
        }

        .sidebar.mobile-visible {
            transform: translateX(0);
        }

        .sidebar.collapsed {
            width: 280px;
        }

        .mobile-toggle {
            display: flex;
            align-items: center;
            justify-content: center;
            z-index: 1001;
        }

        .sidebar-toggle {
            display: none;
        }

        .sidebar-overlay {
            display: block;
        }

        .sidebar.collapsed .logo-text,
        .sidebar.collapsed .nav-text {
            opacity: 1;
            width: auto;
        }

        .sidebar.collapsed .nav-link {
            padding: 12px 20px;
            justify-content: flex-start;
        }

        .sidebar.collapsed .nav-icon {
            margin-right: 12px;
        }

        .sidebar.collapsed .nav-arrow {
            display: block;
        }

        .sidebar.collapsed .nav-dropdown {
            display: block;
        }

        .sidebar.collapsed .logout-btn {
            left: 20px;
            right: 20px;
            padding: 12px;
        }

        .sidebar.collapsed .logout-btn svg {
            margin-right: 8px;
        }

        .nav-dropdown {
            background: rgba(255, 255, 255, 0.9);
            border-radius: 0 0 8px 8px;
            margin-top: -4px;
            box-shadow: 0 4px 6px rgba(0, 0, 0, 0.1);
        }

        .nav-dropdown .nav-link {
            padding: 12px 20px 12px 60px !important;
            margin-right: 0 !important;
            border-radius: 0 !important;
        }

        .nav-dropdown .nav-link:hover {
            background: rgba(242, 101, 34, 0.1) !important;
            transform: none !important;
        }

        .nav-item.open .nav-dropdown {
            display: block !important;
            max-height: 500px !important;
        }

        .nav-item.open .nav-arrow {
            transform: rotate(90deg);
        }
    }

    /* Scrollbar Styling */
    .sidebar::-webkit-scrollbar {
        width: 4px;
    }

    .sidebar::-webkit-scrollbar-track {
        background: transparent;
    }

    .sidebar::-webkit-scrollbar-thumb {
        background: rgba(242, 101, 34, 0.3);
        border-radius: 2px;
    }

    .sidebar::-webkit-scrollbar-thumb:hover {
        background: rgba(242, 101, 34, 0.5);
    }

    /* Animation for menu items */
    .nav-item {
        animation: slideInLeft 0.3s ease forwards;
        opacity: 0;
        transform: translateX(-20px);
    }

    .nav-item:nth-child(1) {
        animation-delay: 0.1s;
    }

    .nav-item:nth-child(2) {
        animation-delay: 0.2s;
    }

    .nav-item:nth-child(3) {
        animation-delay: 0.3s;
    }

    .nav-item:nth-child(4) {
        animation-delay: 0.4s;
    }

    .nav-item:nth-child(5) {
        animation-delay: 0.5s;
    }

    @keyframes slideInLeft {
        to {
            opacity: 1;
            transform: translateX(0);
        }
    }

    /* Tooltip for collapsed state */
    .nav-link[data-tooltip] {
        position: relative;
    }

    .sidebar.collapsed .nav-link[data-tooltip]:hover::after {
        content: attr(data-tooltip);
        position: absolute;
        left: 70px;
        top: 50%;
        transform: translateY(-50%);
        background: #333;
        color: white;
        padding: 8px 12px;
        border-radius: 6px;
        font-size: 12px;
        white-space: nowrap;
        z-index: 1000;
        opacity: 0;
        animation: tooltipFadeIn 0.3s ease forwards;
    }

    .sidebar.collapsed .nav-link[data-tooltip]:hover::before {
        content: '';
        position: absolute;
        left: 65px;
        top: 50%;
        transform: translateY(-50%);
        border: 5px solid transparent;
        border-right-color: #333;
        z-index: 1000;
    }

    @keyframes tooltipFadeIn {
        to {
            opacity: 1;
        }
    }

    /* Main content styling */
    .main-content {
        margin-left: 280px;
        padding: 30px;
        transition: margin-left 0.3s ease;
    }

    .sidebar.collapsed~.main-content {
        margin-left: 70px;
    }

    @media (max-width: 768px) {
        .main-content {
            margin-left: 0;
            padding: 20px;
        }
    }
</style>

<body>
    <!-- Mobile Toggle Button -->
    <button class="mobile-toggle" id="mobileToggle">
        <svg viewBox="0 0 24 24">
            <path d="M3 18h18v-2H3v2zm0-5h18v-2H3v2zm0-7v2h18V6H3z" />
        </svg>
    </button>

    <!-- Sidebar Overlay for Mobile -->
    <div class="sidebar-overlay" id="sidebarOverlay"></div>

    <!-- Sidebar -->
    <!-- Sidebar -->
    <aside class="sidebar" id="sidebar">
        <div class="sidebar-header">
            <button class="sidebar-toggle" id="sidebarToggle">
                <svg viewBox="0 0 24 24">
                    <path d="M15.41 7.41L14 6l-6 6 6 6 1.41-1.41L10.83 12z" />
                </svg>
            </button>
            <!-- You can add your logo here if you want -->
        </div>

        <nav class="sidebar-nav">
            <!-- Dashboard -->
            <div class="nav-item">
                <a href="{{ route('customer.dashboard') }}"
                    class="nav-link {{ request()->routeIs('customer.dashboard') ? 'active' : '' }}"
                    data-tooltip="Dashboard">
                    <svg class="nav-icon" viewBox="0 0 24 24">
                        <path d="M3 13h8V3H3v10zm0 8h8v-6H3v6zm10 0h8V11h-8v10zm0-18v6h8V3h-8z" />
                    </svg>
                    <span class="nav-text">Dashboard</span>
                </a>
            </div>

            <!-- Register Vehicle -->
            <div class="nav-item">
                <a href="{{ route('customer.registervehicle') }}"
                    class="nav-link {{ request()->routeIs('customer.registervehicle') ? 'active' : '' }}"
                    data-tooltip="Register Vehicle">
                    <svg class="nav-icon" viewBox="0 0 24 24">
                        <path
                            d="M4 17v1a2 2 0 002 2h2a2 2 0 002-2v-1m4 0v1a2 2 0 002 2h2a2 2 0 002-2v-1M5 10l1-2h12l1 2M7 10V6a2 2 0 012-2h6a2 2 0 012 2v4" />
                    </svg>
                    <span class="nav-text">Register Vehicle</span>
                </a>
            </div>

            <!-- Upload Payment Proof -->
            <div class="nav-item">
                <a href="{{ url('/customer/payment-proof') }}"
                    class="nav-link {{ request()->is('customer/payment-proof') ? 'active' : '' }}"
                    data-tooltip="Upload Payment Proof">
                    <svg class="nav-icon" viewBox="0 0 24 24">
                        <path d="M19 21H5a2 2 0 01-2-2V5a2 2 0 012-2h7l2 2h5a2 2 0 012 2v12a2 2 0 01-2 2z" />
                    </svg>
                    <span class="nav-text">Upload Payment Proof</span>
                </a>
            </div>

            <!-- My Topup History -->
            <div class="nav-item">
                <a href="{{ route('customer.topup.history') }}"
                    class="nav-link {{ request()->routeIs('customer.topup.history') ? 'active' : '' }}"
                    data-tooltip="Topup History">
                    <svg class="nav-icon" viewBox="0 0 24 24">
                        <path d="M3 6v6a9 9 0 009 9 9 9 0 009-9V6" />
                        <path d="M9 10v2a2 2 0 002 2h2" />
                        <path d="M12 8v4" />
                    </svg>
                    <span class="nav-text">My Topup History</span>
                </a>
            </div>

            <!-- Add Vehicle List if needed (optional) -->
            <div class="nav-item">
                <a href="{{ route('customer.vehicles') }}"
                    class="nav-link {{ request()->routeIs('customer.vehicles') ? 'active' : '' }}"
                    data-tooltip="My Vehicles">
                    <svg class="nav-icon" viewBox="0 0 24 24">
                        <path
                            d="M4 17v1a2 2 0 002 2h12a2 2 0 002-2v-1M5 10l1-2h12l1 2M7 10V6a2 2 0 012-2h6a2 2 0 012 2v4" />
                    </svg>
                    <span class="nav-text">My Vehicles</span>
                </a>
            </div>

        </nav>

        <!-- Logout Button -->
        <form method="POST" action="{{ route('logout') }}"
            style="position: absolute; bottom: 20px; left: 20px; right: 20px;">
            @csrf
            <button type="submit" class="logout-btn">
                <svg viewBox="0 0 24 24">
                    <path
                        d="M17 7l-1.41 1.41L18.17 11H8v2h10.17l-2.58 2.59L17 17l5-5zM4 5h8V3H4c-1.1 0-2 .9-2 2v14c0 1.1.9 2 2 2h8v-2H4V5z" />
                </svg>
                <span class="nav-text">Logout</span>
            </button>
        </form>
    </aside>


    <!-- Main Content -->
    <main class="main-content">
        <!-- Your form and other content goes here -->
        <form>
            <input type="text" placeholder="Test input field">
            <button type="submit">Submit</button>
        </form>
    </main>

    <script>
        document.addEventListener('DOMContentLoaded', function() {
            const sidebar = document.getElementById('sidebar');
            const sidebarToggle = document.getElementById('sidebarToggle');
            const mobileToggle = document.getElementById('mobileToggle');
            const sidebarOverlay = document.getElementById('sidebarOverlay');
            const dropdownToggles = document.querySelectorAll('.dropdown-toggle');

            // Desktop sidebar toggle
            sidebarToggle.addEventListener('click', function() {
                sidebar.classList.toggle('collapsed');

                // Close all dropdowns when collapsing
                if (sidebar.classList.contains('collapsed')) {
                    document.querySelectorAll('.nav-item').forEach(item => {
                        item.classList.remove('open');
                        const dropdown = item.querySelector('.nav-dropdown');
                        if (dropdown) dropdown.classList.remove('open');
                    });
                }

                localStorage.setItem('sidebarCollapsed', sidebar.classList.contains('collapsed'));
            });

            // Mobile sidebar toggle
            mobileToggle.addEventListener('click', function() {
                sidebar.classList.toggle('mobile-visible');
                sidebarOverlay.classList.toggle('active');
                document.body.style.overflow = sidebar.classList.contains('mobile-visible') ? 'hidden' : '';
            });

            // Close mobile sidebar when clicking overlay
            sidebarOverlay.addEventListener('click', function() {
                sidebar.classList.remove('mobile-visible');
                sidebarOverlay.classList.remove('active');
                document.body.style.overflow = '';
            });

            // Dropdown functionality
            // Update the dropdown functionality section
            dropdownToggles.forEach(toggle => {
                toggle.addEventListener('click', function(e) {
                    // For mobile view, handle dropdowns differently
                    if (window.innerWidth <= 768) {
                        e.preventDefault();
                        e.stopPropagation(); // Prevent the click from bubbling up

                        const navItem = this.closest('.nav-item');
                        const dropdown = navItem.querySelector('.nav-dropdown');
                        const isOpen = navItem.classList.contains('open');

                        // Toggle current dropdown
                        if (isOpen) {
                            navItem.classList.remove('open');
                            dropdown.classList.remove('open');
                        } else {
                            // Close all other dropdowns first
                            document.querySelectorAll('.nav-item').forEach(item => {
                                if (item !== navItem) {
                                    item.classList.remove('open');
                                    const otherDropdown = item.querySelector(
                                        '.nav-dropdown');
                                    if (otherDropdown) otherDropdown.classList.remove(
                                        'open');
                                }
                            });

                            navItem.classList.add('open');
                            dropdown.classList.add('open');
                        }
                        return;
                    }

                    // Original desktop behavior
                    e.preventDefault();

                    if (sidebar.classList.contains('collapsed') && window.innerWidth > 768) {
                        return;
                    }

                    const navItem = this.closest('.nav-item');
                    const dropdown = navItem.querySelector('.nav-dropdown');
                    const isOpen = navItem.classList.contains('open');

                    // Close all other dropdowns
                    document.querySelectorAll('.nav-item').forEach(item => {
                        if (item !== navItem) {
                            item.classList.remove('open');
                            const otherDropdown = item.querySelector('.nav-dropdown');
                            if (otherDropdown) otherDropdown.classList.remove('open');
                        }
                    });

                    // Toggle current dropdown
                    if (isOpen) {
                        navItem.classList.remove('open');
                        dropdown.classList.remove('open');
                    } else {
                        navItem.classList.add('open');
                        dropdown.classList.add('open');
                    }
                });
            });

            // Auto-open dropdown if child is active
            document.querySelectorAll('.nav-dropdown .nav-link.active').forEach(activeChild => {
                const navItem = activeChild.closest('.nav-item');
                const dropdown = navItem.querySelector('.nav-dropdown');
                navItem.classList.add('open');
                dropdown.classList.add('open');
            });

            // Restore sidebar state from localStorage
            const sidebarCollapsed = localStorage.getItem('sidebarCollapsed');
            if (sidebarCollapsed === 'true') {
                sidebar.classList.add('collapsed');
            }

            // Handle window resize
            window.addEventListener('resize', function() {
                if (window.innerWidth > 768) {
                    sidebar.classList.remove('mobile-visible');
                    sidebarOverlay.classList.remove('active');
                    document.body.style.overflow = '';
                }
            });

            // Close mobile menu when clicking on nav links
            // Update the nav-link click handler
            document.querySelectorAll('.nav-link:not(.dropdown-toggle), .nav-dropdown .nav-link').forEach(link => {
                link.addEventListener('click', function(e) {
                    // Don't close sidebar if clicking on dropdown items
                    if (this.closest('.nav-dropdown') && window.innerWidth <= 768) {
                        return;
                    }

                    if (window.innerWidth <= 768) {
                        sidebar.classList.remove('mobile-visible');
                        sidebarOverlay.classList.remove('active');
                        document.body.style.overflow = '';
                    }
                });
            });
        });
    </script>
</body>
