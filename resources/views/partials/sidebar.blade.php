<!-- SIDEBAR WITH FIXED HEADER & FOOTER TOGGLE -->
<div class="sidebar bg-dark text-white d-flex flex-column vh-100 position-sticky top-0" id="sidebar" style="cursor: pointer; overflow: hidden;">
    
    <!-- 1. FIXED BRANDING HEADER (NO SCROLL) -->
    <div class="sidebar-header p-3 text-center border-bottom border-secondary flex-shrink-0">
        <h5 class="m-0 fw-bold sidebar-title text-truncate" title="Integrated Pharmacy System">🏥 IPS</h5>
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
                <a class="nav-link text-white-50 d-flex justify-content-between align-items-center" data-bs-toggle="collapse" href="#collapseEcdr" role="button" aria-expanded="false" aria-controls="collapseEcdr">
                    <div class="d-flex align-items-center">
                        <svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" fill="currentColor" class="me-2 sidebar-icon" viewBox="0 0 16 16"><path fill-rule="evenodd" d="M8 15A7 7 0 1 0 8 1a7 7 0 0 0 0 14zm0 1A8 8 0 1 0 8 0a8 8 0 0 0 0 16z"/><path fill-rule="evenodd" d="M11.354 4.646a.5.5 0 0 0-.708 0l-6 6a.5.5 0 0 0 .708.708l6-6a.5.5 0 0 0 0-.708z"/></svg>
                        <span class="sidebar-text">eCDR</span>
                    </div>
                    <span class="sidebar-text text-muted" style="font-size: 0.75rem;">▼</span>
                </a>
                
                <div class="collapse" id="collapseEcdr">
                    <ul class="nav nav-pills flex-column mt-1 mb-2 gap-1" style="padding-left: 1.25rem;">
                        <li class="nav-item">
                            <a href="#" class="nav-link text-white-50" style="padding: 0.4rem 1rem;" title="Cytotoxic Drug">
                                <span class="sidebar-text small">Cytotoxic Drug</span>
                            </a>
                        </li>
                    </ul>
                </div>
            </li>
            
        </ul>
    </div>
    
    <!-- 3. FIXED TOGGLE BUTTON AT BOTTOM (NO SCROLL) -->
    <div class="mt-auto border-top border-secondary p-2 d-flex justify-content-center sidebar-toggle-container flex-shrink-0 bg-dark">
        <button id="sidebarToggleBtn" class="btn btn-dark w-100 fs-5" title="Toggle Sidebar">☰</button>
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