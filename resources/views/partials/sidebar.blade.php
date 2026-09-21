<div id="sidebar" class="d-flex flex-column flex-shrink-0 p-3 text-white bg-dark shadow-sm">
    
    <!-- 1. Top Row: System Branding (+ Icon & System Title) -->
    <a href="/" class="d-flex align-items-center text-white text-decoration-none mb-4 border-bottom border-secondary pb-3 w-100 brand-container">
        <div class="bg-primary text-white rounded d-flex justify-content-center align-items-center me-2 shadow-sm brand-icon" style="width: 40px; height: 40px; flex-shrink: 0;">
            <!-- Pharmacy Cross (+) SVG -->
            <svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" fill="currentColor" viewBox="0 0 16 16">
                <path d="M13.5 8a.5.5 0 0 1-.5.5h-4v4a.5.5 0 0 1-1 0v-4h-4a.5.5 0 0 1 0-1h4v-4a.5.5 0 0 1 1 0v4h4a.5.5 0 0 1 .5.5z"/>
            </svg>
        </div>
        <div class="sidebar-text">
            <span class="fs-6 fw-bold d-block text-uppercase" style="letter-spacing: 0.5px;">Integrated</span>
            <span class="fs-6 fw-bold d-block text-uppercase text-primary" style="letter-spacing: 0.5px;">Pharmacy System</span>
        </div>
    </a>

    <!-- Navigation List -->
    <ul class="nav nav-pills flex-column mb-auto gap-2">
        
        <!-- 1. Main Dashboard -->
        <li class="nav-item">
            <a href="#" class="nav-link text-white-50" title="Dashboard">
                <svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" fill="currentColor" class="me-2 mb-1" viewBox="0 0 16 16"><path d="M2 3a2 2 0 0 0-2 2v8a2 2 0 0 0 2 2h12a2 2 0 0 0 2-2V5a2 2 0 0 0-2-2H2zm.5 1h11a.5.5 0 0 1 .5.5v7a.5.5 0 0 1-.5.5h-11a.5.5 0 0 1-.5-.5v-7a.5.5 0 0 1 .5-.5zM3 5.5a.5.5 0 0 1 .5-.5h4a.5.5 0 0 1 0 1h-4a.5.5 0 0 1-.5-.5z"/></svg>
                <span class="sidebar-text">Dashboard</span>
            </a>
        </li>

        <hr class="text-secondary my-1">

        <!-- 2. iMonitor Group -->
        @php
            $isImonitorActive = request()->routeIs('monitor.*') || request()->routeIs('counselling.*') || request()->routeIs('collection.*');
        @endphp
        
        <li class="nav-item">
            <a href="#imonitorSubmenu" data-bs-toggle="collapse" class="nav-link {{ $isImonitorActive ? 'text-white fw-bold' : 'text-white-50' }} d-flex justify-content-between align-items-center" aria-expanded="{{ $isImonitorActive ? 'true' : 'false' }}" title="iMonitor">
                <span>
                    <svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" fill="currentColor" class="me-2 mb-1" viewBox="0 0 16 16"><path d="M12.5 16a3.5 3.5 0 1 0 0-7 3.5 3.5 0 0 0 0 7Zm1.679-4.493-1.335 2.226a.75.75 0 0 1-1.174.144l-.774-.773a.5.5 0 0 1 .708-.708l.547.548 1.17-1.951a.5.5 0 1 1 .858.514ZM8 1c-1.573 0-3.022.289-4.096.777C2.875 2.227 2 3.066 2 4s.875 1.773 1.904 2.223C4.978 6.711 6.427 7 8 7s3.022-.289 4.096-.777C13.125 5.773 14 4.934 14 4s-.875-1.773-1.904-2.223C11.022 1.289 9.573 1 8 1Z"/></svg>
                    <span class="sidebar-text">iMonitor</span>
                </span>
                <svg xmlns="http://www.w3.org/2000/svg" width="12" height="12" fill="currentColor" viewBox="0 0 16 16" style="transition: transform 0.2s;" class="chevron-icon {{ $isImonitorActive ? 'rotate-180' : '' }}">
                    <path fill-rule="evenodd" d="M1.646 4.646a.5.5 0 0 1 .708 0L8 10.293l5.646-5.647a.5.5 0 0 1 .708.708l-6 6a.5.5 0 0 1-.708 0l-6-6a.5.5 0 0 1 0-.708z"/>
                </svg>
            </a>
            
            <!-- Collapsible Submenu -->
            <ul class="collapse {{ $isImonitorActive ? 'show' : '' }} nav flex-column ms-3 mt-2 border-start border-secondary ps-2" id="imonitorSubmenu">
                <li class="nav-item mb-1">
                    <a href="{{ route('monitor.index') }}" class="nav-link small {{ request()->routeIs('monitor.*') ? 'active bg-primary text-white shadow-sm' : 'text-white-50' }}">
                        Discharge Medications Status
                    </a>
                </li>
                <li class="nav-item mb-1">
                    <a href="{{ route('counselling.index') }}" class="nav-link small {{ request()->routeIs('counselling.index') ? 'active bg-primary text-white shadow-sm' : 'text-white-50' }}">
                        Counselling Request
                    </a>
                </li>

                <!-- Admin Only: Counselling List Submodule -->
                @if(auth()->check() && auth()->user()->role === 'admin')
                <li class="nav-item mb-1">
                    <a href="{{ route('counselling.list') }}" class="nav-link small {{ request()->routeIs('counselling.list') ? 'active bg-primary text-white shadow-sm' : 'text-white-50' }}">
                        Counselling List
                    </a>
                </li>
                @endif

                <li class="nav-item">
                    <a href="{{ route('collection.index') }}" class="nav-link small {{ request()->routeIs('collection.*') ? 'active bg-primary text-white shadow-sm' : 'text-white-50' }}">
                        Discharge Medication Collection
                    </a>
                </li>
            </ul>
        </li>

        <hr class="text-secondary my-1">

        <!-- 3. eCDR Group -->
        <li class="nav-item">
            <a href="#" class="nav-link text-white-50" title="eCDR">
                <svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" fill="currentColor" class="me-2 mb-1" viewBox="0 0 16 16"><path d="M4 0h8a2 2 0 0 1 2 2v12a2 2 0 0 1-2 2H4a2 2 0 0 1-2-2V2a2 2 0 0 1 2-2zm0 1a1 1 0 0 0-1 1v12a1 1 0 0 0 1 1h8a1 1 0 0 0 1-1V2a1 1 0 0 0-1-1H4z"/></svg>
                <span class="sidebar-text">eCDR (Cytotoxic Drug)</span>
            </a>
        </li>
    </ul>

    <!-- 2. Bottom Row: Sidebar Toggle Button (☰) -->
    <div class="mt-auto pt-3 border-top border-secondary w-100 toggle-container">
        <button type="button" onclick="toggleSidebar(event)" class="sidebar-toggle-btn w-100 d-flex align-items-center justify-content-center gap-2" title="Toggle Sidebar">
            <span class="fs-6">☰</span>
            <span class="sidebar-text small fw-bold">Collapse Sidebar</span>
        </button>
    </div>
</div>

<script>
    // Toggle Button Handler
    function toggleSidebar(e) {
        if (e) e.stopPropagation(); // Prevents triggering container click event
        const sidebar = document.getElementById('sidebar');
        if (sidebar) {
            sidebar.classList.toggle('collapsed');
            localStorage.setItem('ips_sidebar_collapsed', sidebar.classList.contains('collapsed'));
        }
    }

    document.addEventListener('DOMContentLoaded', function() {
        const sidebar = document.getElementById('sidebar');

        if (sidebar) {
            // Restore saved collapsed state
            if (localStorage.getItem('ips_sidebar_collapsed') === 'true') {
                sidebar.classList.add('collapsed');
            }

            // Click anywhere on collapsed sidebar to automatically expand
            sidebar.addEventListener('click', function(e) {
                if (sidebar.classList.contains('collapsed')) {
                    sidebar.classList.remove('collapsed');
                    localStorage.setItem('ips_sidebar_collapsed', 'false');
                }
            });
        }
    });
</script>