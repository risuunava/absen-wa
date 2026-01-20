<!DOCTYPE html>
<html lang="id" class="scroll-smooth">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0, maximum-scale=5.0">
<meta name="csrf-token" content="{{ csrf_token() }}">
    <title>Sistem Absensi Sekolah - @yield('title', 'Beranda')</title>
    
    <!-- Tailwind CSS -->
    <script src="https://cdn.tailwindcss.com"></script>
    
    <!-- Font Inter -->
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@300;400;500;600;700&display=swap" rel="stylesheet">
    
    <!-- Icons -->
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.10.0/font/bootstrap-icons.css">
    
    <style>
        :root {
            --primary-black: #0a0a0a;
            --secondary-black: #1a1a1a;
            --light-gray: #f5f5f5;
            --medium-gray: #e5e5e5;
            --dark-gray: #404040;
            --accent-green: #10b981;
            --accent-red: #ef4444;
            --accent-yellow: #f59e0b;
            --accent-blue: #3b82f6;
        }
        
        body {
            font-family: 'Inter', sans-serif;
            background: #ffffff;
            min-height: 100vh;
            color: var(--primary-black);
        }
        
        .glass-card {
            background: rgba(255, 255, 255, 0.98);
            backdrop-filter: blur(20px);
            border: 1px solid rgba(0, 0, 0, 0.08);
            box-shadow: 
                0 2px 4px -1px rgba(0, 0, 0, 0.02),
                0 1px 2px -1px rgba(0, 0, 0, 0.01),
                0 8px 20px -12px rgba(0, 0, 0, 0.1);
        }
        
        .mobile-card {
            background: white;
            border: 1px solid var(--medium-gray);
            border-radius: 12px;
            box-shadow: 0 2px 8px rgba(0, 0, 0, 0.04);
        }
        
        .mobile-padding {
            padding: 1rem;
        }
        
        @media (max-width: 768px) {
            .mobile-padding {
                padding: 0.75rem;
            }
            
            .mobile-card {
                border-radius: 10px;
            }
        }
        
        .hover-lift {
            transition: all 0.25s cubic-bezier(0.4, 0, 0.2, 1);
        }
        
        .hover-lift:hover {
            transform: translateY(-2px);
            box-shadow: 0 10px 25px -5px rgba(0, 0, 0, 0.08);
        }
        
        .mobile-hover:active {
            transform: scale(0.98);
        }
        
        .status-dot {
            display: inline-block;
            width: 6px;
            height: 6px;
            border-radius: 50%;
            margin-right: 6px;
        }
        
        .btn-primary {
            background: var(--primary-black);
            color: white;
            border: none;
            border-radius: 10px;
            font-weight: 500;
            transition: all 0.25s ease;
        }
        
        .btn-primary:hover {
            background: var(--dark-gray);
            transform: translateY(-1px);
        }
        
        .btn-primary:active {
            transform: scale(0.98);
        }
        
        .btn-secondary {
            background: white;
            color: var(--primary-black);
            border: 1px solid var(--medium-gray);
            border-radius: 10px;
            font-weight: 500;
            transition: all 0.25s ease;
        }
        
        .btn-secondary:hover {
            background: var(--light-gray);
            border-color: var(--dark-gray);
        }
        
        .btn-danger {
            background: var(--accent-red);
            color: white;
            border: none;
            border-radius: 10px;
            font-weight: 500;
            transition: all 0.25s ease;
        }
        
        .btn-danger:hover {
            background: #dc2626;
            transform: translateY(-1px);
        }
        
        .btn-success {
            background: var(--accent-green);
            color: white;
            border: none;
            border-radius: 10px;
            font-weight: 500;
            transition: all 0.25s ease;
        }
        
        .btn-success:hover {
            background: #0da271;
            transform: translateY(-1px);
        }
        
        .mobile-grid {
            display: grid;
            gap: 1rem;
        }
        
        @media (max-width: 640px) {
            .mobile-grid {
                gap: 0.75rem;
            }
        }
        
        .animate-fade-in {
            animation: fadeIn 0.4s ease-out;
        }
        
        @keyframes fadeIn {
            from { opacity: 0; transform: translateY(10px); }
            to { opacity: 1; transform: translateY(0); }
        }
        
        .mobile-text-sm {
            font-size: 0.875rem;
            line-height: 1.25rem;
        }
        
        .mobile-text-xs {
            font-size: 0.75rem;
            line-height: 1rem;
        }
        
        .mobile-text-lg {
            font-size: 1.125rem;
            line-height: 1.5rem;
        }
        
        .mobile-text-xl {
            font-size: 1.25rem;
            line-height: 1.75rem;
        }
        
        .mobile-icon-sm {
            font-size: 1rem;
        }
        
        .mobile-icon-md {
            font-size: 1.25rem;
        }
        
        .mobile-icon-lg {
            font-size: 1.5rem;
        }
        
        .camera-container {
            position: relative;
            width: 100%;
            background: var(--secondary-black);
            border-radius: 12px;
            overflow: hidden;
        }
        
        @media (max-width: 768px) {
            .camera-container {
                border-radius: 10px;
            }
        }
        
        .camera-controls {
            position: absolute;
            bottom: 1rem;
            left: 0;
            right: 0;
            display: flex;
            justify-content: center;
            gap: 0.75rem;
            padding: 0 1rem;
        }
        
        .camera-btn {
            background: rgba(0, 0, 0, 0.7);
            backdrop-filter: blur(10px);
            color: white;
            border: 1px solid rgba(255, 255, 255, 0.2);
            border-radius: 50px;
            padding: 0.75rem 1.25rem;
            font-size: 0.875rem;
            font-weight: 500;
            display: flex;
            align-items: center;
            gap: 0.5rem;
            transition: all 0.25s ease;
        }
        
        .camera-btn:hover {
            background: rgba(0, 0, 0, 0.9);
            border-color: rgba(255, 255, 255, 0.3);
        }
        
        .camera-btn:active {
            transform: scale(0.95);
        }
        
        /* MOBILE NAVIGATION STYLES */
        @media (max-width: 768px) {
            .desktop-only {
                display: none !important;
            }
            
            .mobile-only {
                display: block !important;
            }
            
            /* MOBILE BOTTOM NAVIGATION */
            .mobile-bottom-nav {
                display: flex;
                position: fixed;
                bottom: 0;
                left: 0;
                right: 0;
                background: white;
                border-top: 1px solid var(--medium-gray);
                padding: 0.75rem 1rem;
                z-index: 1000;
                height: 60px;
                align-items: center;
            }
            
            .mobile-bottom-nav .nav-container {
                display: flex;
                width: 100%;
                justify-content: space-around;
                align-items: center;
                gap: 0.5rem;
            }
            
            .mobile-bottom-nav .nav-item {
                flex: 1;
                display: flex;
                flex-direction: column;
                align-items: center;
                justify-content: center;
                padding: 0.5rem;
                color: var(--dark-gray);
                text-decoration: none;
                font-size: 0.75rem;
                border-radius: 8px;
                transition: all 0.2s ease;
                min-height: 40px;
            }
            
            .mobile-bottom-nav .nav-item.active {
                color: var(--primary-black);
                background: var(--light-gray);
            }
            
            .mobile-bottom-nav .nav-item:active {
                background: var(--medium-gray);
            }
            
            .mobile-bottom-nav .nav-item i {
                font-size: 1.1rem;
                margin-bottom: 3px;
            }
            
            /* Adjust main content for bottom navigation */
            .mobile-bottom-safe {
                padding-bottom: 70px !important;
            }
        }
        
        /* TABLET NAVIGATION (768px - 1024px) */
        @media (min-width: 769px) and (max-width: 1024px) {
            .mobile-only {
                display: none !important;
            }
            
            .desktop-only {
                display: block !important;
            }
            
            /* Tablet: Gunakan navbar desktop tapi lebih compact */
            .desktop-only nav {
                padding: 0.5rem 1rem;
            }
            
            .desktop-only nav h1 {
                font-size: 0.875rem;
            }
            
            .desktop-only nav p {
                font-size: 0.7rem;
            }
        }
        
        .mobile-status-bar {
            background: var(--primary-black);
            color: white;
            padding: 0.5rem 1rem;
            display: flex;
            justify-content: space-between;
            align-items: center;
            font-size: 0.75rem;
        }
        
        .mobile-bottom-safe {
            padding-bottom: env(safe-area-inset-bottom, 1rem);
        }
        
        .mobile-top-safe {
            padding-top: env(safe-area-inset-top, 1rem);
        }
        
        /* User name truncation */
        .username-truncate {
            max-width: 80px;
            white-space: nowrap;
            overflow: hidden;
            text-overflow: ellipsis;
            display: inline-block;
        }
        
        @media (max-width: 640px) {
            .username-truncate {
                max-width: 60px;
                font-size: 0.7rem;
            }
        }
    </style>
</head>
<body class="antialiased">
    <!-- Mobile Status Bar -->
    <div class="mobile-status-bar mobile-only">
        <div class="flex items-center gap-2">
            <i class="bi bi-calendar-check mobile-icon-sm"></i>
            <span class="font-medium">SAS</span>
        </div>
        <div id="mobileTime" class="font-medium"></div>
    </div>

    <!-- DESKTOP & TABLET NAVIGATION (tampil di 769px+) -->
    <nav class="bg-white/95 backdrop-blur-lg border-b border-gray-100 sticky top-0 z-50 desktop-only">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
            <div class="flex justify-between items-center h-16">
                <!-- Logo -->
                <div class="flex items-center gap-3">
                    <div class="w-10 h-10 bg-black rounded-lg flex items-center justify-center">
                        <i class="bi bi-calendar-check text-white mobile-icon-md"></i>
                    </div>
                    <div>
                        <h1 class="text-base font-bold text-gray-900">SAS</h1>
                        <p class="text-xs text-gray-500 font-medium">ATTENDANCE SYSTEM</p>
                    </div>
                </div>

                <!-- Desktop & Tablet Menu -->
                <div class="flex items-center gap-4">
                    @if(session()->has('user_id'))
                        <!-- User Info -->
                        <div class="hidden md:flex items-center gap-3">
                            <div class="w-8 h-8 bg-gray-100 rounded-full flex items-center justify-center border border-gray-200">
                                <i class="bi bi-person text-gray-600 mobile-icon-sm"></i>
                            </div>
                            <div>
                                <p class="text-sm font-medium text-gray-900">
                                    {{ session('full_name') ?? session('user_name') }}
                                </p>
                                <p class="text-xs text-gray-500">
                                    <span class="px-2 py-0.5 bg-gray-100 rounded-full border border-gray-200">
                                        {{ ucfirst(session('user_role')) }}
                                    </span>
                                </p>
                            </div>
                        </div>

                        <!-- Dashboard Link untuk semua user yang login -->
                        @if(session('user_role') === 'admin')
                            <a href="/admin" class="inline-flex items-center gap-2 text-sm text-gray-600 hover:text-gray-900 transition-colors font-medium px-3 py-2 rounded-lg hover:bg-gray-50">
                                <i class="bi bi-speedometer2"></i>
                                <span>Dashboard</span>
                            </a>
                        @else
                            <a href="/" class="inline-flex items-center gap-2 text-sm text-gray-600 hover:text-gray-900 transition-colors font-medium px-3 py-2 rounded-lg hover:bg-gray-50">
                                <i class="bi bi-house"></i>
                                <span>Beranda</span>
                            </a>
                        @endif

                        <!-- Logout Button -->
                        <form action="{{ route('logout') }}" method="POST" class="inline">
                            @csrf
                            <button type="submit" class="inline-flex items-center gap-2 text-sm text-gray-600 hover:text-gray-900 transition-colors font-medium px-3 py-2 rounded-lg hover:bg-gray-50 mobile-hover">
                                <i class="bi bi-box-arrow-right"></i>
                                <span>Keluar</span>
                            </button>
                        </form>
                    @else
                        <!-- Login Button -->
                        <a href="{{ route('login') }}" class="inline-flex items-center gap-2 bg-black text-white text-sm font-medium px-4 py-2.5 rounded-lg hover:bg-gray-800 transition-colors mobile-hover">
                            <i class="bi bi-box-arrow-in-right"></i>
                            <span>Masuk</span>
                        </a>
                    @endif
                </div>
            </div>
        </div>
    </nav>

    <!-- Main Content -->
    <main class="max-w-7xl mx-auto px-3 sm:px-4 lg:px-8 py-4 sm:py-6 md:py-8 mobile-bottom-safe">
        <!-- Alert Messages -->
        @if(session('success'))
            <div class="mb-4 sm:mb-6 animate-fade-in">
                <div class="bg-green-50 border border-green-200 rounded-xl p-3 sm:p-4">
                    <div class="flex items-center">
                        <div class="flex-shrink-0">
                            <i class="bi bi-check-circle text-green-500 mobile-icon-md"></i>
                        </div>
                        <div class="ml-3">
                            <p class="mobile-text-sm font-medium text-green-800">{{ session('success') }}</p>
                        </div>
                        <button type="button" class="ml-auto" onclick="this.parentElement.parentElement.style.display='none'">
                            <i class="bi bi-x text-green-500 mobile-icon-md"></i>
                        </button>
                    </div>
                </div>
            </div>
        @endif

        @if(session('error'))
            <div class="mb-4 sm:mb-6 animate-fade-in">
                <div class="bg-red-50 border border-red-200 rounded-xl p-3 sm:p-4">
                    <div class="flex items-center">
                        <div class="flex-shrink-0">
                            <i class="bi bi-exclamation-circle text-red-500 mobile-icon-md"></i>
                        </div>
                        <div class="ml-3">
                            <p class="mobile-text-sm font-medium text-red-800">{{ session('error') }}</p>
                        </div>
                        <button type="button" class="ml-auto" onclick="this.parentElement.parentElement.style.display='none'">
                            <i class="bi bi-x text-red-500 mobile-icon-md"></i>
                        </button>
                    </div>
                </div>
            </div>
        @endif

        <!-- Page Content -->
        @yield('content')
    </main>

    <!-- MOBILE BOTTOM NAVIGATION (hanya tampil di ≤768px) -->
    @if(session()->has('user_id'))
    <nav class="mobile-bottom-nav mobile-only">
        <div class="nav-container">
            <a href="/" class="nav-item {{ Request::is('/') ? 'active' : '' }}">
                <i class="bi bi-house"></i>
                <span>Beranda</span>
            </a>
            
            @if(session('user_role') === 'admin')
            <a href="/admin" class="nav-item {{ Request::is('admin*') ? 'active' : '' }}">
                <i class="bi bi-speedometer2"></i>
                <span>Dashboard</span>
            </a>
            @else
            <div class="nav-item">
                <i class="bi bi-person"></i>
                <span class="username-truncate">{{ session('user_name') }}</span>
            </div>
            @endif
            
            <form action="{{ route('logout') }}" method="POST" class="contents">
                @csrf
                <button type="submit" class="nav-item">
                    <i class="bi bi-box-arrow-right"></i>
                    <span>Keluar</span>
                </button>
            </form>
        </div>
    </nav>
    @else
    <nav class="mobile-bottom-nav mobile-only">
        <div class="nav-container">
            <a href="/" class="nav-item {{ Request::is('/') ? 'active' : '' }}">
                <i class="bi bi-house"></i>
                <span>Beranda</span>
            </a>
            
            <a href="{{ route('login') }}" class="nav-item {{ Request::is('login*') ? 'active' : '' }}">
                <i class="bi bi-box-arrow-in-right"></i>
                <span>Login</span>
            </a>
        </div>
    </nav>
    @endif

    <!-- Footer -->
    <footer class="border-t border-gray-100 mt-8 md:mt-12 bg-white desktop-only">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-4 sm:py-6">
            <div class="flex flex-col md:flex-row justify-between items-center">
                <div class="mb-4 md:mb-0">
                    <div class="flex items-center gap-2">
                        <div class="w-8 h-8 bg-black rounded-lg flex items-center justify-center">
                            <i class="bi bi-calendar-check text-white mobile-icon-sm"></i>
                        </div>
                        <div>
                            <span class="text-sm font-bold text-gray-900">SAS v3.0</span>
                            <p class="text-xs text-gray-500">Digital Attendance System</p>
                        </div>
                    </div>
                </div>
                <div class="text-xs text-gray-500">
                    <p>© {{ date('Y') }} Sistem Absensi Sekolah</p>
                </div>
            </div>
        </div>
    </footer>

    <!-- Scripts -->
    <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
    @stack('scripts')
    
    <script>
        // Update mobile time
        function updateMobileTime() {
            const now = new Date();
            const timeString = now.toLocaleTimeString('id-ID', { 
                hour12: false,
                hour: '2-digit',
                minute: '2-digit'
            });
            
            if (document.getElementById('mobileTime')) {
                document.getElementById('mobileTime').textContent = timeString;
            }
        }
        
        // Initialize mobile time
        updateMobileTime();
        setInterval(updateMobileTime, 60000);
        
        // Smooth scroll for anchor links
        document.querySelectorAll('a[href^="#"]').forEach(anchor => {
            anchor.addEventListener('click', function (e) {
                e.preventDefault();
                const target = document.querySelector(this.getAttribute('href'));
                if (target) {
                    target.scrollIntoView({ 
                        behavior: 'smooth',
                        block: 'start'
                    });
                }
            });
        });
        
        // Mobile touch effects
        document.querySelectorAll('.mobile-hover').forEach(element => {
            element.addEventListener('touchstart', function() {
                this.style.transform = 'scale(0.98)';
            });
            
            element.addEventListener('touchend', function() {
                this.style.transform = '';
            });
        });
        
        // Prevent zoom on double tap
        let lastTouchEnd = 0;
        document.addEventListener('touchend', function(event) {
            const now = (new Date()).getTime();
            if (now - lastTouchEnd <= 300) {
                event.preventDefault();
            }
            lastTouchEnd = now;
        }, false);
        
        // Check screen size and hide/show appropriate menus
        function checkScreenSize() {
            const isMobile = window.innerWidth <= 768;
            const mobileElements = document.querySelectorAll('.mobile-only');
            const desktopElements = document.querySelectorAll('.desktop-only');
            
            if (isMobile) {
                // Show mobile elements, hide desktop
                mobileElements.forEach(el => el.style.display = 'block');
                desktopElements.forEach(el => el.style.display = 'none');
            } else {
                // Show desktop elements, hide mobile
                mobileElements.forEach(el => el.style.display = 'none');
                desktopElements.forEach(el => el.style.display = 'block');
            }
        }
        
        // Initial check
        checkScreenSize();
        
        // Check on resize
        window.addEventListener('resize', checkScreenSize);
    </script>
</body>
</html>