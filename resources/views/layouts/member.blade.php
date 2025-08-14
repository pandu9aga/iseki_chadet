<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="icon" type="image/png" href="{{asset('assets/img/favicon.png')}}">
    <title>Iseki Chadet - Chasis Detector</title>
    
    <link rel="stylesheet" href="{{ asset('assets/css/templatemo-glossy-touch.css') }}">
    
<!--

TemplateMo 592 glossy touch

https://templatemo.com/tm-592-glossy-touch

-->
    @yield('style')
    @vite(['resources/js/app.js'])
    <script>var Module;</script>
    <style>
        
    </style>
</head>
<body>
    <div class="bg-shapes">
        <div class="shape"></div>
        <div class="shape"></div>
        <div class="shape"></div>
        <div class="shape"></div>
        <div class="shape"></div>
        <div class="shape"></div>
    </div>

    <!-- SINGLE NAVIGATION HEADER -->
    <header>
        <div class="container">
            <nav class="glass">
                <div class="logo">
                    <div class="logo-icon">
                        <svg viewBox="0 0 48 48" fill="white" xmlns="http://www.w3.org/2000/svg">
                            <!-- Grid of circles with varying sizes -->
                            <circle cx="16" cy="16" r="5" opacity="0.9"/>
                            <circle cx="32" cy="16" r="4" opacity="0.8"/>
                            <circle cx="16" cy="32" r="4" opacity="0.7"/>
                            <circle cx="32" cy="32" r="5" opacity="0.85"/>
                            <!-- Small accent dots -->
                            <circle cx="24" cy="8" r="2" opacity="1"/>
                            <circle cx="8" cy="24" r="2" opacity="0.9"/>
                            <circle cx="40" cy="24" r="2" opacity="0.9"/>
                            <circle cx="24" cy="40" r="2" opacity="1"/>
                            <!-- Tiny corner dots -->
                            <circle cx="8" cy="8" r="1" opacity="0.6"/>
                            <circle cx="40" cy="8" r="1" opacity="0.6"/>
                            <circle cx="8" cy="40" r="1" opacity="0.6"/>
                            <circle cx="40" cy="40" r="1" opacity="0.6"/>
                        </svg>
                    </div>
                    <span>Iseki Chadet</span>
                </div>
                <div class="nav-links">
                    <a href="{{ route('home') }}" class="{{ $page === 'home' ? 'active' : '' }}">Home</a>
                    <a href="{{ route('user') }}" class="{{ $page === 'user' ? 'active' : '' }}">user</a>
                    <a href="{{ route('record') }}" class="{{ $page === 'record' ? 'active' : '' }}">Record</a>
                    <a href="{{ route('login') }}">Logout</a>
                </div>
            </nav>
        </div>
    </header>

    <!-- PAGE -->
    @yield('content')

    <!-- SINGLE FOOTER -->
    <div id="footer">
        <div class="container">
            <footer class="glass">
                <div class="footer-content">
                    <div class="footer-links">
                        <a href="{{ route('home') }}">Home</a>
                        <a href="{{ route('user') }}">User</a>
                        <a href="{{ route('record') }}">Record</a>
                    </div>
                    <div class="copyright">
                        &copy; <script>document.write(new Date().getFullYear())</script> Iseki Chadet - Chasis Detector
                    </div>
                </div>
            </footer>
        </div>
    </div>
<script src="{{ asset('assets/js/templatemo-glossy-touch.js') }}"></script>
@yield('script')
</body>
</html>