<!-- SIDEBAR WITH FIXED HEADER & FOOTER TOGGLE -->
<div class="sidebar bg-dark text-white d-flex flex-column vh-100 position-sticky top-0" id="sidebar" style="cursor: pointer; overflow: hidden;">
    
    <!-- 1. FIXED BRANDING HEADER (NO SCROLL) -->
    <div class="sidebar-header p-3 text-center border-bottom border-secondary flex-shrink-0">
        <h5 class="m-0 fw-bold sidebar-title text-truncate d-flex align-items-center justify-content-center gap-1" title="Integrated Pharmacy System">
            <svg xmlns="http://www.w3.org/2000/svg" width="20" height="20" fill="#0dcaf0" viewBox="0 0 16 16">
                <path d="M1.828 8.9 8.9 1.827a4 4 0 1 1 5.657 5.657L7.485 14.557A4 4 0 1 1 1.828 8.9Zm1.414 1.414a2 2 0 1 0 2.828 2.828l3.536-3.536-2.828-2.828-3.536 3.536Zm8.486-8.486a2 2 0 0 0-2.828 0L5.364 5.364l2.828 2.828 3.536-3.536a2 2 0 0 0 0-2.828Z"/>
            </svg>
            <span>IPS</span>
        </h5>
        <div class="sidebar-text text-white-50 small mt-1 fw-bold" style="line-height: 1.2;">
            Integrated<br>Pharmacy System
        </div>
    </div>
    
    <!-- 2. SCROLLABLE MODULE & SUBMODULE NAV AREA -->
    <div class="flex-grow-1 overflow-y-auto px-2 py-3 custom-sidebar-scroll">
        <ul class="nav nav-pills flex-column gap-1">
            
            <!-- DASHBOARD -->
            <li class="nav-item mb-2">
                <a href="{{ route('dashboard') }}" class="nav-link {{ request()->routeIs('dashboard') ? 'active bg-primary text-white shadow-sm' : 'text-white-50' }}" title="Dashboard">
                    <svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" fill="currentColor" class="me-2 mb-1 sidebar-icon" viewBox="0 0 16 16"><path d="M2 3a2 2 0 0 0-2 2v8a2 2 0 0 0 2 2h12a2 2 0 0 0 2-2V5a2 2 0 0 0-2-2H2zm.5 1h11a.5.5 0 0 1 .5.5v7a.5.5 0 0 1-.5.5h-11a.5.5 0 0 1-.5-.5v-7a.5.5 0 0 1 .5-.5zM3 5.5a.5.5 0 0 1 .5-.5h4a.5.5 0 0 1 0 1h-4a.5.5 0 0 1-.5-.5z"/></svg>
                    <span class="sidebar-text">Dashboard</span>
                </a>
            </li>

            @php
                // Keep iMonitor accordion open if any submodule is active
                $isMonitorActive = in_array(request()->route()->getName(), [
                    'monitor.index', 
                    'collection.index', 
                    'counselling.index', 
                    'counselling.list', 
                    'reports.index'
                ]);
            @endphp

            <!-- iMONITOR MODULE (ACCORDION) -->
            <li class="nav-item border-top border-secondary pt-2">
                <a class="nav-link d-flex justify-content-between align-items-center {{ $isMonitorActive ? 'text-white fw-bold' : 'text-white-50' }}" data-bs-toggle="collapse" href="#collapseMonitor" role="button" aria-expanded="{{ $isMonitorActive ? 'true' : 'false' }}" aria-controls="collapseMonitor">
                    <div class="d-flex align-items-center">
                        <svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" fill="currentColor" class="me-2 sidebar-icon" viewBox="0 0 16 16"><path d="M9.828 3h3.982a2 2 0 0 1 1.992 2.181l-.637 7A2 2 0 0 1 13.174 14H2.825a2 2 0 0 1-1.991-1.819l-.637-7a1.99 1.99 0 0 1 .342-1.31L.5 3a2 2 0 0 1 2-2h3.672a2 2 0 0 1 1.414.586l.828.828A2 2 0 0 0 9.828 3zm-8.322.12C1.72 3.042 1.95 3 2.19 3h5.396l-.707-.707A1 1 0 0 0 6.172 2H2.5a1 1 0 0 0-1 .981l.006.139z"/></svg>
                        <span class="sidebar-text">iMonitor</span>
                    </div>
                    <span class="sidebar-text text-muted" style="font-size: 0.75rem;">▼</span>
                </a>
                
                <!-- Submodules -->
                <div class="collapse {{ $isMonitorActive ? 'show' : '' }}" id="collapseMonitor">
                    <ul class="nav nav-pills flex-column mt-1 mb-2 gap-1" style="padding-left: 1.25rem;">
                        <li class="nav-item">
                            <a href="{{ route('monitor.index') }}" class="nav-link {{ request()->routeIs('monitor.index') ? 'active bg-primary text-white shadow-sm' : 'text-white-50' }}" style="padding: 0.4rem 1rem;" title="Status">
                                <span class="sidebar-text small">Status</span>
                            </a>
                        </li>
                        <li class="nav-item">
                            <a href="{{ route('collection.index') }}" class="nav-link {{ request()->routeIs('collection.index') ? 'active bg-primary text-white shadow-sm' : 'text-white-50' }}" style="padding: 0.4rem 1rem;" title="Collection">
                                <span class="sidebar-text small">Collection</span>
                            </a>
                        </li>
                        <li class="nav-item">
                            <a href="{{ route('counselling.index') }}" class="nav-link {{ request()->routeIs('counselling.index') ? 'active bg-primary text-white shadow-sm' : 'text-white-50' }}" style="padding: 0.4rem 1rem;" title="Counselling Req">
                                <span class="sidebar-text small">Counselling Req</span>
                            </a>
                        </li>

                        @if(auth()->check() && auth()->user()->role === 'admin')
                            <li class="nav-item">
                                <a href="{{ route('counselling.list') }}" class="nav-link {{ request()->routeIs('counselling.list') ? 'active bg-primary text-white shadow-sm' : 'text-white-50' }}" style="padding: 0.4rem 1rem;" title="Counselling List">
                                    <span class="sidebar-text small">Counselling List 🔒</span>
                                </a>
                            </li>
                            <li class="nav-item">
                                <a href="{{ route('reports.index') }}" class="nav-link {{ request()->routeIs('reports.index') ? 'active bg-primary text-white shadow-sm' : 'text-white-50' }}" style="padding: 0.4rem 1rem;" title="Reporting">
                                    <span class="sidebar-text small">Reporting 🔒</span>
                                </a>
                            </li>
                        @endif
                    </ul>
                </div>
            </li>

            <!-- eCDR MODULE (ACCORDION) -->
            <li class="nav-item border-top border-secondary pt-2">
                <a class="nav-link d-flex justify-content-between align-items-center {{ request()->routeIs('ecdr.*') ? 'text-white fw-bold' : 'text-white-50' }}" data-bs-toggle="collapse" href="#collapseEcdr" role="button" aria-expanded="{{ request()->routeIs('ecdr.*') ? 'true' : 'false' }}" aria-controls="collapseEcdr">
                    <div class="d-flex align-items-center">
                        <svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" fill="currentColor" class="me-2 sidebar-icon" viewBox="0 0 16 16"><path fill-rule="evenodd" d="M8 15A7 7 0 1 0 8 1a7 7 0 0 0 0 14zm0 1A8 8 0 1 0 8 0a8 8 0 0 0 0 16z"/><path fill-rule="evenodd" d="M11.354 4.646a.5.5 0 0 0-.708 0l-6 6a.5.5 0 0 0 .708.708l6-6a.5.5 0 0 0 0-.708z"/></svg>
                        <span class="sidebar-text">eCDR</span>
                    </div>
                    <span class="sidebar-text text-muted" style="font-size: 0.75rem;">▼</span>
                </a>
                
                <div class="collapse {{ request()->routeIs('ecdr.*') ? 'show' : '' }}" id="collapseEcdr">
                    <ul class="nav nav-pills flex-column mt-1 mb-2 gap-1" style="padding-left: 1.25rem;">
                        <li class="nav-item">
                            <a href="{{ route('ecdr.index') }}" class="nav-link {{ request()->routeIs('ecdr.*') ? 'active bg-primary text-white shadow-sm' : 'text-white-50' }}" style="padding: 0.4rem 1rem;" title="Ordering">
                                <span class="sidebar-text small">Ordering</span>
                            </a>
                        </li>
                    </ul>
                </div>
            </li>
            
            <!-- DRUG FORMULARY (STANDARDIZED MODULE ITEM) -->
            <li class="nav-item border-top border-secondary pt-2">
                <a href="https://hseldrugformulary.wistify.app/" target="_blank" class="nav-link text-white-50 d-flex justify-content-between align-items-center" title="Drug Formulary">
                    <div class="d-flex align-items-center">
                        <svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" fill="currentColor" class="me-2 sidebar-icon" viewBox="0 0 16 16">
                            <path d="M1 2.828c.885-.37 2.154-.769 3.388-.893 1.33-.134 2.458.063 3.112.752v9.746c-.652-.629-1.757-.843-3.087-.711-1.21.12-2.434.48-3.413.882V2.828zM15 2.828c-.885-.37-2.154-.769-3.388-.893-1.33-.134-2.458.063-3.112.752v9.746c.652-.629 1.757-.843 3.087-.711 1.21.12 2.434.48 3.413.882V2.828zM0 1.95v11.331c0 .727.672 1.272 1.387 1.011 1.22-.446 2.623-.88 4.015-.733 1.282.135 2.277.676 2.598 1.157.321-.481 1.316-1.022 2.598-1.157 1.392-.147 2.795.287 4.015.733.715.261 1.387-.284 1.387-1.011V1.95c0-.776-.723-1.334-1.467-1.115-1.284.38-2.671.748-4.033.612-1.08-.108-1.921-.527-2.498-.982A.508.508 0 0 0 8 0a.508.508 0 0 0-.498.465c-.577.455-1.418.874-2.498.982-1.362.136-2.749-.232-4.033-.612C.723.616 0 1.174 0 1.95z"/>
                        </svg>
                        <span class="sidebar-text">Drug Formulary</span>
                    </div>
                    <span class="sidebar-text text-muted" style="font-size: 0.75rem;">↗</span>
                </a>
            </li>

        </ul>
    </div>
    
    <!-- 3. FIXED TOGGLE BUTTON AT BOTTOM (MATCHED HEIGHT) -->
    <div class="mt-auto border-top border-secondary px-2 d-flex align-items-center justify-content-center sidebar-toggle-container flex-shrink-0 bg-dark">
        <button id="sidebarToggleBtn" class="btn btn-dark w-100 py-1 fs-5" title="Toggle Sidebar">☰</button>
    </div>

</div>

<script>
    document.addEventListener("DOMContentLoaded", function() {
        const sidebar = document.getElementById("sidebar");
        const toggleBtn = document.getElementById("sidebarToggleBtn");

        // Restore collapsed state from LocalStorage
        if (localStorage.getItem("sidebarCollapsed") === "true") {
            sidebar.classList.add("collapsed");
        }

        // Toggle button click logic
        toggleBtn.addEventListener("click", function(e) {
            e.stopPropagation();
            sidebar.classList.toggle("collapsed");
            localStorage.setItem("sidebarCollapsed", sidebar.classList.contains("collapsed"));
        });

        // Click anywhere on sidebar to restore if collapsed
        sidebar.addEventListener("click", function() {
            if (sidebar.classList.contains("collapsed")) {
                sidebar.classList.remove("collapsed");
                localStorage.setItem("sidebarCollapsed", "false");
            }
        });
    });
</script>