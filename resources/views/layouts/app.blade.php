<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>IPS - @yield('title', 'Integrated Pharmacy System')</title>
    <!-- Core CSS -->
    <link href="{{ asset('css/bootstrap.min.css') }}" rel="stylesheet">
    <link href="{{ asset('css/style.css') }}" rel="stylesheet">
    @stack('styles')
</head>
<body class="bg-light overflow-hidden m-0 p-0">
    <div class="d-flex w-100 vh-100 overflow-hidden">
        
        <!-- FIXED SIDEBAR -->
        <div class="no-print flex-shrink-0">
            @include('partials.sidebar')
        </div>

        <!-- MAIN CONTENT WRAPPER -->
        <div class="main-content d-flex flex-column h-100 flex-grow-1 overflow-hidden">
            
            <!-- TOP NAVBAR (PERMANENTLY FIXED) -->
            <nav class="navbar top-header px-4 py-3 d-flex justify-content-between align-items-center no-print flex-shrink-0">
                <h4 class="mb-0 fw-bold text-dark">@yield('page_title', 'Integrated Pharmacy System')</h4>
                <div class="text-muted small">
                    Logged in as: <strong class="text-dark">{{ auth()->user()->login_username ?? 'Unknown' }}</strong> 
                    <span class="badge bg-primary ms-2 px-2 py-1">{{ strtoupper(auth()->user()->role ?? 'User') }}</span>
                </div>
            </nav>

            <!-- SCROLLABLE CONTENT BODY (ONLY THIS AREA SCROLLS) -->
            <div class="content-body flex-grow-1 overflow-y-auto p-4">
                @yield('content')
            </div>

            <!-- PINNED GLOBAL FOOTER (PERMANENTLY FIXED AT BOTTOM) -->
            <footer class="main-footer no-print flex-shrink-0">
                &copy; 2026 Hospital Selayang. Developed by Muhammad Haziq Zikri (ITD HSEL)
            </footer>

        </div>
    </div>

    <!-- SCROLL TO TOP BUTTON -->
    <button id="scrollTopBtn" class="no-print" title="Go to top">⬆️</button>

    <!-- Core Scripts -->
    <script src="{{ asset('js/bootstrap.bundle.min.js') }}"></script>
    <script>
        // Scroll to top logic for the internal scroll container
        const contentBody = document.querySelector('.content-body');
        const upButton = document.getElementById("scrollTopBtn");

        if (contentBody && upButton) {
            contentBody.onscroll = function() {
                if (contentBody.scrollTop > 120) {
                    upButton.style.display = "block";
                } else {
                    upButton.style.display = "none";
                }
            };
            upButton.onclick = function() {
                contentBody.scrollTo({top: 0, behavior: 'smooth'});
            };
        }
    </script>
    @stack('scripts')
</body>
</html>