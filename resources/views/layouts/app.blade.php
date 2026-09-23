<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>IPS - @yield('title', 'Integrated Pharmacy System')</title>
    
    <!-- Core CSS -->
    <link href="{{ asset('css/bootstrap.min.css') }}" rel="stylesheet">
    <link href="{{ asset('css/style.css') }}" rel="stylesheet">
    <!-- Load the new MediSphere Studio UI globally -->
    <link href="{{ asset('css/studio-ui.css') }}" rel="stylesheet">
    
    <!-- Bootstrap Icons -->
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.css">
    
    @stack('styles')
</head>
<body class="m-0 p-0 overflow-hidden" style="background-color: #f4f7fa;">
    <div class="d-flex w-100 vh-100 overflow-hidden">
        
        <!-- FIXED SIDEBAR -->
        <div class="no-print flex-shrink-0" style="z-index: 1000;">
            @include('partials.sidebar')
        </div>

        <!-- MAIN CONTENT WRAPPER -->
        <div class="main-content d-flex flex-column h-100 flex-grow-1 overflow-hidden" style="background-color: #f4f7fa;">
            
            <!-- TOP NAVBAR (PERMANENTLY FIXED) -->
            <nav class="navbar px-4 py-3 d-flex justify-content-between align-items-center no-print flex-shrink-0" style="background: #ffffff; border-bottom: 1px solid #f4f5f7; z-index: 90;">
                <h4 class="mb-0 fw-bold" style="color: #172b4d; letter-spacing: -0.3px;">@yield('page_title', 'Integrated Pharmacy System')</h4>
                
                <!-- Modern User Profile Block -->
                <div class="d-flex align-items-center gap-3">
                    <div class="text-end d-none d-md-block" style="line-height: 1.2;">
                        <div style="font-size: 13px; font-weight: 700; color: #172b4d;">
                            {{ auth()->user()->name ?? 'Unknown' }}
                        </div>
                        <div style="font-size: 11px; font-weight: 500; color: #8898aa; text-transform: uppercase; letter-spacing: 0.5px;">
                            {{ auth()->user()->role ?? 'User' }}
                        </div>
                    </div>
                    <div style="width: 38px; height: 38px; border-radius: 10px; background: #e3efff; color: #0066ff; display: flex; align-items: center; justify-content: center;">
                        <i class="bi bi-person-fill fs-5"></i>
                    </div>
                </div>
            </nav>

            <!-- SCROLLABLE CONTENT BODY (ONLY THIS AREA SCROLLS) -->
            <div class="content-body flex-grow-1 overflow-y-auto p-4 custom-sidebar-scroll">
                @yield('content')
            </div>

            <!-- PINNED GLOBAL FOOTER -->
            <footer class="no-print flex-shrink-0 d-flex align-items-center justify-content-center" style="background: #ffffff; border-top: 1px solid #f4f5f7; height: 50px; color: #8898aa; font-size: 12px; font-weight: 500;">
                &copy; {{ date('Y') }} Hospital Selayang. Developed by Muhammad Haziq Zikri (ITD HSEL)
            </footer>

        </div>
    </div>

    <!-- SCROLL TO TOP BUTTON -->
    <button id="scrollTopBtn" class="no-print d-flex align-items-center justify-content-center" title="Go to top" style="width: 40px; height: 40px;">
        <i class="bi bi-arrow-up"></i>
    </button>

    <!-- Core Scripts -->
    <script src="{{ asset('js/bootstrap.bundle.min.js') }}"></script>
    <script>
        // Scroll to top logic for the internal scroll container[cite: 10]
        const contentBody = document.querySelector('.content-body');
        const upButton = document.getElementById("scrollTopBtn");

        if (contentBody && upButton) {
            contentBody.onscroll = function() {
                if (contentBody.scrollTop > 120) {
                    upButton.style.display = "flex";
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