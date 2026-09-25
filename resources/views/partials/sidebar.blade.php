<!-- SIDEBAR WITH FLOATING TOGGLE -->
<div class="sidebar d-flex flex-column vh-100 position-sticky top-0" id="sidebar" style="background-color: #ffffff !important; border-right: 1px solid #f4f5f7; z-index: 1000; transition: width 0.3s; width: 260px; min-width: 260px; position: relative;">
    
    <!-- 1. BRANDING HEADER -->
    <div class="sidebar-header p-3 text-center flex-shrink-0 mt-2 d-flex align-items-center gap-2" style="padding-left: 20px !important;">
        <div style="color: #20c997;">
            <i class="bi bi-grid-3x3-gap-fill fs-4"></i>
        </div>
        <span class="sidebar-text fw-bold text-dark" style="font-size: 15px; text-align: left; line-height: 1.1;">Integrated<br>Pharmacy System</span>
    </div>

    <!-- FLOATING TOGGLE BUTTON (TOP RIGHT) -->
    <button id="sidebarToggleBtn" class="floating-toggle-btn" title="Toggle Sidebar">
        <i class="bi bi-chevron-left" id="toggleIcon" style="font-size: 12px; font-weight: bold;"></i>
    </button>

    <!-- HOSPITAL CARD -->
    <div class="hospital-card flex-shrink-0">
        <div class="hospital-card-icon">
            <i class="bi bi-building"></i>
        </div>
        <div class="sidebar-text text-start">
            <div class="hospital-title">Hospital Selayang</div>
            <div class="hospital-subtitle">JKS Selangor, MY</div>
        </div>
    </div>
    
    <!-- 2. SCROLLABLE NAV AREA -->
    <div class="flex-grow-1 overflow-y-auto pb-4 custom-sidebar-scroll">
        <ul class="nav flex-column">
            
            <!-- DASHBOARD -->
            <li class="nav-item">
                <a href="{{ route('dashboard') }}" class="medi-nav-link {{ request()->routeIs('dashboard') ? 'active' : '' }}">
                    <i class="bi bi-grid-1x2-fill"></i>
                    <span class="sidebar-text">Dashboard</span>
                </a>
            </li>

            @php
                $isMonitorActive = in_array(request()->route()->getName(), [
                    'monitor.index', 'collection.index', 'counselling.index', 'counselling.list', 'reports.index'
                ]);
            @endphp

            <!-- CATEGORY 1 -->
            <div class="sidebar-category">Discharge Medications</div>
            <li class="nav-item">
                <a class="medi-nav-link d-flex justify-content-between align-items-center" data-bs-toggle="collapse" href="#collapseMonitor" role="button" aria-expanded="{{ $isMonitorActive ? 'true' : 'false' }}">
                    <div class="d-flex align-items-center">
                        <i class="bi bi-display"></i>
                        <span class="sidebar-text">iMonitor</span>
                    </div>
                    <i class="bi bi-chevron-down collapse-icon" style="font-size: 10px; margin-right: 0;"></i>
                </a>
                
                <div class="collapse {{ $isMonitorActive ? 'show' : '' }}" id="collapseMonitor">
                    <div class="medi-submenu flex-column d-flex">
                            
                        @if(auth()->check() && auth()->user()->role === 'admin')
                        <a href="{{ route('monitor.create') }}" class="medi-sub-link {{ request()->routeIs('monitor.create') ? 'active' : '' }}">New Order</a>
                        @endif
                            
                        <a href="{{ route('counselling.index') }}" class="medi-sub-link {{ request()->routeIs('counselling.index') ? 'active' : '' }}">New Counselling</a>
                        <a href="{{ route('monitor.index') }}" class="medi-sub-link {{ request()->routeIs('monitor.index') ? 'active' : '' }}">Order Status</a>
                        <a href="{{ route('collection.index') }}" class="medi-sub-link {{ request()->routeIs('collection.index') ? 'active' : '' }}">Collection</a>
                            
                        @if(auth()->check() && auth()->user()->role === 'admin')
                        <a href="{{ route('counselling.list') }}" class="medi-sub-link {{ request()->routeIs('counselling.list') ? 'active' : '' }}">Counselling List</a>
                        <a href="{{ route('reports.index') }}" class="medi-sub-link {{ request()->routeIs('reports.index') ? 'active' : '' }}">Reporting</a>
                        @endif
                            
                    </div>
                </div>
            </li>

            <!-- CATEGORY 2 -->
            <div class="sidebar-category">Cytotoxic Management</div>
            <li class="nav-item">
                <a class="medi-nav-link d-flex justify-content-between align-items-center" data-bs-toggle="collapse" href="#collapseEcdr" role="button" aria-expanded="{{ request()->routeIs('ecdr.*') ? 'true' : 'false' }}">
                    <div class="d-flex align-items-center">
                        <i class="bi bi-prescription2"></i>
                        <span class="sidebar-text">eCDR</span>
                    </div>
                    <i class="bi bi-chevron-down collapse-icon" style="font-size: 10px; margin-right: 0;"></i>
                </a>
                
                <div class="collapse {{ request()->routeIs('ecdr.*') ? 'show' : '' }}" id="collapseEcdr">
                    <div class="medi-submenu flex-column d-flex">
                        <a href="{{ route('ecdr.create') }}" class="medi-sub-link {{ request()->routeIs('ecdr.create') ? 'active' : '' }}">New Order</a>
                        <a href="{{ route('ecdr.history') }}" class="medi-sub-link {{ request()->routeIs('ecdr.history') ? 'active' : '' }}">My Orders</a>
                        <a href="{{ route('ecdr.ward_list') }}" class="medi-sub-link {{ request()->routeIs('ecdr.ward_list') ? 'active' : '' }}">Ward Order List</a>
                    </div>
                </div>
            </li>
            
            <!-- CATEGORY 3 -->
            <div class="sidebar-category">Resources</div>
            <li class="nav-item">
                <a href="https://hseldrugformulary.wistify.app/" target="_blank" class="medi-nav-link d-flex justify-content-between align-items-center">
                    <div class="d-flex align-items-center">
                        <i class="bi bi-journal-medical"></i>
                        <span class="sidebar-text">Drug Formulary</span>
                    </div>
                    <i class="bi bi-box-arrow-up-right collapse-icon" style="font-size: 10px; margin-right: 0;"></i>
                </a>
            </li>

        </ul>
    </div>

</div>

<script>
    document.addEventListener("DOMContentLoaded", function() {
        const sidebar = document.getElementById("sidebar");
        const toggleBtn = document.getElementById("sidebarToggleBtn");
        const toggleIcon = document.getElementById("toggleIcon");

        // Function to update the floating chevron icon direction
        const updateIcon = () => {
            if (sidebar.classList.contains("collapsed")) {
                toggleIcon.classList.remove("bi-chevron-left");
                toggleIcon.classList.add("bi-chevron-right");
            } else {
                toggleIcon.classList.remove("bi-chevron-right");
                toggleIcon.classList.add("bi-chevron-left");
            }
        };

        // 1. Restore state on page load
        if (localStorage.getItem("sidebarCollapsed") === "true") {
            sidebar.classList.add("collapsed");
            updateIcon();
        }

        // 2. Toggle button click (shrinks/expands)
        toggleBtn.addEventListener("click", function(e) {
            e.stopPropagation(); // Prevent the click from bubbling up to the sidebar
            sidebar.classList.toggle("collapsed");
            localStorage.setItem("sidebarCollapsed", sidebar.classList.contains("collapsed"));
            updateIcon();
        });

        // 3. Click ANYWHERE on a shrunk sidebar to expand it
        sidebar.addEventListener("click", function(e) {
            if (sidebar.classList.contains("collapsed")) {
                e.preventDefault(); // Stop accidental link navigation while it's shrunk
                sidebar.classList.remove("collapsed");
                localStorage.setItem("sidebarCollapsed", "false");
                updateIcon();
            }
        });
    });
</script>