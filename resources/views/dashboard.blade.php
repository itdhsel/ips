<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>IPS - Live Dashboard</title>
    <link href="{{ asset('css/bootstrap.min.css') }}" rel="stylesheet">
    <link href="{{ asset('css/style.css') }}" rel="stylesheet">
    <style>
        /* New Styled Cards based on the reference image */
        .kpi-card {
            position: relative;
            border-radius: 6px;
            color: #fff;
            padding: 1.25rem 1.25rem 1rem 1.25rem;
            min-height: 125px;
            box-shadow: 0 4px 6px rgba(0,0,0,0.05);
            transition: transform 0.2s ease, box-shadow 0.2s ease;
            overflow: hidden;
            display: flex;
            flex-direction: column;
            justify-content: space-between;
        }
        
        .kpi-card:hover { 
            transform: translateY(-3px); 
            box-shadow: 0 6px 15px rgba(0,0,0,0.15); 
        }
        
        /* Subtle Geometric Diamond Overlay Pattern */
        .kpi-card::before {
            content: '';
            position: absolute;
            top: 0; left: 0; right: 0; bottom: 0;
            background-image: url("data:image/svg+xml,%3Csvg xmlns='http://www.w3.org/2000/svg' width='60' height='60' viewBox='0 0 60 60'%3E%3Cpath d='M30 0l30 30-30 30L0 30 30 0zm0 10L10 30l20 20 20-20-20-20zm0 10l10 10-10 10-10-10 10-10z' fill='%23ffffff' fill-opacity='0.07' fill-rule='evenodd'/%3E%3C/svg%3E");
            z-index: 1;
            pointer-events: none;
        }

        /* Solid Color Palette matching the image */
        .bg-card-yellow { background-color: #d3a30a; }
        .bg-card-magenta { background-color: #bc1c5c; }
        .bg-card-green { background-color: #98bf23; }
        .bg-card-purple { background-color: #8846c4; }

        /* Typography */
        .kpi-title { 
            position: relative; 
            z-index: 2; 
            font-size: 1.05rem; 
            font-weight: 700; 
            letter-spacing: -0.2px; 
            margin-bottom: 8px;
        }
        
        .kpi-value { 
            position: relative; 
            z-index: 2; 
            font-size: 2.2rem; 
            font-weight: 800; 
            line-height: 1; 
        }

        /* Bottom-Right Icon Positioning */
        .kpi-icon { 
            position: absolute; 
            z-index: 2; 
            bottom: 12px; 
            right: 15px; 
            opacity: 0.85;
        }
    </style>
</head>
<body class="bg-light overflow-x-hidden">
    <div class="d-flex w-100 overflow-x-hidden">
        
        @include('partials.sidebar')

        <div class="main-content d-flex flex-column min-vh-100">
            <nav class="navbar top-header px-4 py-3 d-flex justify-content-between align-items-center">
                <div>
                    <h4 class="mb-0 fw-bold text-dark">Live Status Dashboard</h4>
                    <span class="text-muted small">Real-time overview of active medication orders</span>
                </div>
                <div class="text-end">
                    <span class="badge bg-danger shadow-sm px-3 py-2 fs-6">🔴 LIVE</span>
                    <div class="small text-muted mt-1 fw-bold" id="clock"></div>
                </div>
            </nav>

            <div class="container-fluid p-4">
                
                <!-- KPI METRICS ROW -->
                <div class="row g-3 mb-4">
                    <!-- Total Orders (Yellow) -->
                    <div class="col-md-3">
                        <div class="kpi-card bg-card-yellow">
                            <div>
                                <div class="kpi-title">Total Orders</div>
                                <div class="kpi-value">{{ $kpi['total'] }}</div>
                            </div>
                            <div class="kpi-icon">
                                <svg xmlns="http://www.w3.org/2000/svg" width="30" height="30" fill="#ffffff" viewBox="0 0 16 16">
                                    <path d="M1.5 2A1.5 1.5 0 0 0 0 3.5v9A1.5 1.5 0 0 0 1.5 14h13a1.5 1.5 0 0 0 1.5-1.5v-9A1.5 1.5 0 0 0 14.5 2h-13zM2 3h12v2H2V3zm0 3h12v6.5a.5.5 0 0 1-.5.5h-11a.5.5 0 0 1-.5-.5V6z"/>
                                </svg>
                            </div>
                        </div>
                    </div>
                    
                    <!-- Processing (Magenta) -->
                    <div class="col-md-3">
                        <div class="kpi-card bg-card-magenta">
                            <div>
                                <div class="kpi-title">Processing</div>
                                <div class="kpi-value">{{ $kpi['processing'] }}</div>
                            </div>
                            <div class="kpi-icon">
                                <svg xmlns="http://www.w3.org/2000/svg" width="30" height="30" fill="#ffffff" viewBox="0 0 16 16">
                                    <path d="M8 3.5a.5.5 0 0 0-1 0V9a.5.5 0 0 0 .252.434l3.5 2a.5.5 0 0 0 .496-.868L8 8.71V3.5z"/>
                                    <path d="M8 16A8 8 0 1 0 8 0a8 8 0 0 0 0 16zm7-8A7 7 0 1 1 1 8a7 7 0 0 1 14 0z"/>
                                </svg>
                            </div>
                        </div>
                    </div>
                    
                    <!-- Ready (Green) -->
                    <div class="col-md-3">
                        <div class="kpi-card bg-card-green">
                            <div>
                                <div class="kpi-title">Ready for Collection</div>
                                <div class="kpi-value">{{ $kpi['ready'] }}</div>
                            </div>
                            <div class="kpi-icon">
                                <svg xmlns="http://www.w3.org/2000/svg" width="30" height="30" fill="#ffffff" viewBox="0 0 16 16">
                                    <path d="M13.854 3.646a.5.5 0 0 1 0 .708l-7 7a.5.5 0 0 1-.708 0l-3.5-3.5a.5.5 0 1 1 .708-.708L6.5 10.293l6.646-6.647a.5.5 0 0 1 .708 0z"/>
                                    <path d="M2 14.5a.5.5 0 0 0 .5.5h11a.5.5 0 0 0 .5-.5V8.5a.5.5 0 0 1 1 0V14.5A1.5 1.5 0 0 1 13.5 16h-11A1.5 1.5 0 0 1 1 14.5V8.5a.5.5 0 0 1 1 0V14.5z"/>
                                </svg>
                            </div>
                        </div>
                    </div>
                    
                    <!-- Completed (Purple) -->
                    <div class="col-md-3">
                        <div class="kpi-card bg-card-purple">
                            <div>
                                <div class="kpi-title">Completed Today</div>
                                <div class="kpi-value">{{ $kpi['completed'] }}</div>
                            </div>
                            <div class="kpi-icon">
                                <svg xmlns="http://www.w3.org/2000/svg" width="30" height="30" fill="#ffffff" viewBox="0 0 16 16">
                                    <path d="M8.186 1.113a.5.5 0 0 0-.372 0L1.846 3.5 8 5.961 14.154 3.5 8.186 1.113zM15 4.239l-6.5 2.6v7.922l6.5-2.6V4.24zM7.5 14.762V6.838L1 4.239v7.923l6.5 2.6zM7.443.184a1.5 1.5 0 0 1 1.114 0l7.129 2.852A.5.5 0 0 1 16 3.5v8.662a1 1 0 0 1-.629.928l-7.185 2.874a.5.5 0 0 1-.372 0L.63 13.09a1 1 0 0 1-.63-.928V3.5a.5.5 0 0 1 .314-.464L7.443.184z"/>
                                </svg>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- ACTIVE QUEUE TABLE -->
                <div class="card shadow-sm border-0 rounded">
                    <div class="card-header bg-white border-bottom-0 pt-4 pb-2 px-4 d-flex justify-content-between align-items-center">
                        <h5 class="fw-bold text-dark mb-0">Active Queue</h5>
                        <a href="{{ route('monitor.index') }}" class="btn btn-sm btn-outline-primary fw-bold">View Full History &rarr;</a>
                    </div>
                    <div class="card-body p-0 table-responsive">
                        <table class="table table-hover align-middle mb-0 custom-table">
                            <thead>
                                <tr>
                                    <th class="text-center" style="width: 80px;">Time</th>
                                    <th class="text-center" style="width: 80px;">Ward</th>
                                    <th>Patient Name</th>
                                    <th class="text-center" style="width: 120px;">MRN</th>
                                    <th class="text-center" style="width: 180px;">Status</th>
                                </tr>
                            </thead>
                            <tbody>
                                @forelse ($activeQueue as $patient)
                                    <tr>
                                        <td class="text-center fw-bold text-danger">{{ $patient->time }}</td>
                                        <td class="text-center fw-bold">{{ $patient->ward }}</td>
                                        <td class="fw-bold text-uppercase">{{ $patient->patient_name }}</td>
                                        <td class="text-center">{{ $patient->mrn }}</td>
                                        <td class="text-center">
                                            @if($patient->status === 'ORDER RECEIVED')
                                                <span class="badge status-received px-3 py-2 w-100">{{ $patient->status }}</span>
                                            @elseif($patient->status === 'PROCESSING')
                                                <span class="badge status-processing px-3 py-2 w-100">{{ $patient->status }}</span>
                                            @elseif($patient->status === 'READY FOR COLLECTION')
                                                <span class="badge status-ready px-3 py-2 w-100">{{ $patient->status }}</span>
                                            @else
                                                <span class="badge bg-secondary px-3 py-2 w-100">{{ $patient->status }}</span>
                                            @endif
                                        </td>
                                    </tr>
                                @empty
                                    <tr>
                                        <td colspan="5" class="text-center py-5 text-muted fw-bold bg-white">
                                            🎉 All active orders have been cleared for today!
                                        </td>
                                    </tr>
                                @endforelse
                            </tbody>
                        </table>
                    </div>
                </div>

            </div>
            
            <footer class="main-footer mt-auto">
                &copy; 2026 Hospital Selayang. Developed by Muhammad Haziq Zikri (ITD HSEL)
            </footer>
        </div>
    </div>

    <script src="{{ asset('js/bootstrap.bundle.min.js') }}"></script>
    <script>
        // Live Clock
        function updateClock() {
            const now = new Date();
            document.getElementById('clock').innerText = now.toLocaleString('en-MY', { 
                day: '2-digit', month: 'short', year: 'numeric', 
                hour: '2-digit', minute: '2-digit', second: '2-digit' 
            });
        }
        setInterval(updateClock, 1000);
        updateClock();

        // Auto-refresh Dashboard every 30 seconds
        setInterval(function() {
            window.location.reload();
        }, 30000);
    </script>
</body>
</html>