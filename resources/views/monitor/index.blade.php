<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>iMonitor v3 - Dashboard</title>
    <!-- CSS Links -->
    <link href="{{ asset('css/bootstrap.min.css') }}" rel="stylesheet">
    <link href="{{ asset('css/style.css') }}" rel="stylesheet">
</head>
<body class="bg-light overflow-x-hidden">
    <div class="d-flex w-100 overflow-x-hidden">
        
        <!-- SIDEBAR (HIDDEN ON PRINT) -->
        <div class="no-print">
            @include('partials.sidebar')
        </div>

        <!-- MAIN CONTENT WRAPPER -->
        <div class="main-content d-flex flex-column min-vh-100">
            
            <!-- HEADER (HIDDEN ON PRINT) -->
            <nav class="navbar top-header px-4 py-3 d-flex justify-content-between align-items-center no-print">
                <h4 class="mb-0 fw-bold text-dark">Live Monitoring</h4>
                <div class="text-muted small">
                    Logged in as: <strong class="text-dark">{{ auth()->user()->login_username ?? 'Unknown' }}</strong> 
                    <span class="badge bg-primary ms-2 px-2 py-1">{{ strtoupper(auth()->user()->role ?? 'User') }}</span>
                </div>
            </nav>

            <!-- PAGE CONTENT -->
            <div class="container-fluid p-4">
                
                <!-- 1. FULL WIDTH FILTER SECTION (HIDDEN ON PRINT) -->
                <div class="card shadow-sm mb-4 border-0 no-print">
                    <div class="card-body bg-white rounded">
                        <form action="{{ route('monitor.index') }}" method="GET" id="filterForm" class="row g-3 align-items-end m-0">
                            <!-- UNIFIED DATE FILTER -->
                            <div class="col-md-5">
                                <div class="d-flex justify-content-between align-items-center mb-1">
                                    <label class="form-label fw-bold small text-primary mb-0">📅 Filter by Date Range</label>
                                    <div class="btn-group btn-group-sm" role="group">
                                        <button type="button" class="btn btn-outline-primary py-0 px-2 fw-bold" style="font-size: 0.72rem;" onclick="setDatePreset('today')">Today</button>
                                        <button type="button" class="btn btn-outline-primary py-0 px-2 fw-bold" style="font-size: 0.72rem;" onclick="setDatePreset('yesterday')">Yesterday</button>
                                        <button type="button" class="btn btn-outline-primary py-0 px-2 fw-bold" style="font-size: 0.72rem;" onclick="setDatePreset('all')">All</button>
                                    </div>
                                </div>
                                <div class="input-group input-group-sm">
                                    <span class="input-group-text bg-light border-secondary">From</span>
                                    <input type="date" id="start_date" name="start_date" class="form-control border-secondary" value="{{ request('start_date', date('Y-m-d')) }}">
                                    <span class="input-group-text bg-light border-secondary">To</span>
                                    <input type="date" id="end_date" name="end_date" class="form-control border-secondary" value="{{ request('end_date', date('Y-m-d')) }}">
                                </div>
                            </div>
                            
                            <!-- WARD FILTER -->
                            <div class="col-md-5">
                                <label class="form-label fw-bold small mb-1 text-primary">🏥 Filter by Ward</label>
                                <select name="ward" id="filter_ward" class="form-select form-select-sm border-secondary">
                                    <!-- Added value="ALL" to fix validation issues -->
                                    <option value="ALL" {{ request('ward', 'ALL') == 'ALL' ? 'selected' : '' }}>-- All Wards --</option>
                                    @php
                                        $wards = ["2C","4A","4B","4C","4D","5A","5B","5C","5D","6A","6B","6C","6D","7A","7B","7C","7D","8A","8B","8C","8D","9A","9B","9C","9D","10A","10B","10C","10D","11B","11C","NICU","HDW","BURN UNIT","LABOUR ROOM","ICU","ED","OTHERS"];
                                    @endphp
                                    @foreach ($wards as $w)
                                        <option value="{{ $w }}" {{ request('ward') == $w ? 'selected' : '' }}>{{ $w }}</option>
                                    @endforeach
                                </select>
                            </div>
                            
                            <!-- ACTION BUTTONS -->
                            <div class="col-md-2 d-flex gap-2">
                                <button type="submit" class="btn btn-primary btn-sm px-3 fw-bold w-100">Search</button>
                                <a href="{{ route('monitor.index') }}" class="btn btn-outline-secondary btn-sm px-3 w-100">Reset</a>
                            </div>
                        </form>
                    </div>
                </div>
                
                @if(session('success'))
                    <div class="alert alert-success shadow-sm no-print">{{ session('success') }}</div>
                @endif
                @if($errors->any())
                    <div class="alert alert-danger shadow-sm no-print">
                        <ul class="mb-0">
                            @foreach ($errors->all() as $error)
                                <li>{{ $error }}</li>
                            @endforeach
                        </ul>
                    </div>
                @endif

                <!-- 2. DATA RESULTS HEADER & ACTION BUTTONS -->
                <div class="d-flex justify-content-between align-items-center mb-3">
                    <h5 class="fw-bold text-secondary mb-0">Data Results</h5>
                    <div class="d-flex gap-2 no-print">
                        <button type="button" onclick="window.print()" class="btn btn-secondary shadow-sm px-4 py-2 fw-bold">
                            🖨️ Print Data
                        </button>
                        @if(auth()->check() && in_array(auth()->user()->role, ['admin', 'pharmacy']))
                            <button type="button" class="btn btn-success shadow-sm px-4 py-2 fw-bold" data-bs-toggle="modal" data-bs-target="#addModal">
                                + Add New Patient
                            </button>
                        @endif
                    </div>
                </div>

                <!-- LAST UPDATED TIMESTAMP -->
                <div class="text-center fw-bold mb-3 text-dark">
                    Last updated on {{ date('d-m-Y H:i:s') }}
                </div>

                <!-- PRINT-ONLY FILTER SUMMARY (APPEARS ONLY ON PRINT) -->
                <div class="print-filter-summary d-none">
                    <strong class="text-uppercase">Active Filter Criteria:</strong>
                    <span class="ms-2">
                        <strong>Date Range:</strong> 
                        {{ request('start_date', date('Y-m-d')) }}
                        {{ request('end_date') ? ' to ' . request('end_date', date('Y-m-d')) : '' }}
                    </span>
                    @if(request('ward'))
                        <span class="ms-3"><strong>Ward:</strong> {{ request('ward') }}</span>
                    @endif
                </div>

                <!-- 3. TABLE AREA -->
                <div class="card shadow border-0 rounded">
                    <div class="card-body p-0 table-responsive">
                        <table class="table table-bordered table-hover mb-0 custom-table legacy-table">
                            <thead>
                                <tr class="legacy-table-header">
                                    <th>No</th>
                                    <th>Date</th>
                                    <th>Time Ordered</th>
                                    <th>Ward</th>
                                    <th>Name</th>
                                    <th>MRN</th>
                                    <th>Total Items</th>
                                    <th>Quantity Supplied</th>
                                    <th>Status</th>
                                    <th>Collected by</th>
                                    <th>Remarks</th>
                                    <th class="no-print">Action</th>
                                </tr>
                            </thead>
                            <tbody>
                                @forelse ($patients as $patient)
                                    <tr>
                                        <td>{{ $patient->no }}</td>
                                        <td>{{ $patient->date }}</td>
                                        <td>{{ $patient->time }}</td>
                                        <td><strong>{{ $patient->ward }}</strong></td>
                                        <td class="text-start">{{ $patient->patient_name }}</td>
                                        <td>{{ $patient->mrn }}</td>
                                        <td>{{ $patient->total_item }} / {{ $patient->total_item2 }}</td>
                                        <td>{{ $patient->supply }}</td>
                                        <td>
                                            @switch($patient->status)
                                                @case('ORDER RECEIVED')
                                                    <span class="badge status-received px-2 py-1">{{ $patient->status }}</span>
                                                    @break
                                                @case('PROCESSING')
                                                    <span class="badge status-processing px-2 py-1">{{ $patient->status }}</span>
                                                    @break
                                                @case('READY FOR COLLECTION')
                                                    <span class="badge status-ready px-2 py-1">{{ $patient->status }}</span>
                                                    @break
                                                @case('COLLECTED BY PHARMACIST OR PPK/SN')
                                                @case('COLLECTED BY STAFF NURSE/PPK')
                                                    <span class="badge status-collected px-2 py-1">{{ $patient->status }}</span>
                                                    @break
                                                @case('COMPLETED')
                                                    <span class="badge status-completed px-2 py-1">{{ $patient->status }}</span>
                                                    @break
                                                @default
                                                    <span class="badge bg-secondary px-2 py-1">{{ $patient->status }}</span>
                                            @endswitch
                                        </td>
                                        <td>{{ $patient->takenby }}</td>
                                        <td>{{ $patient->remarks }}</td>
                                        <td class="no-print">
                                            <div class="d-flex justify-content-center gap-1">
                                                @if(auth()->check() && in_array(auth()->user()->role, ['admin', 'pharmacy']))
                                                    <button type="button" class="btn btn-sm btn-outline-primary" data-bs-toggle="modal" data-bs-target="#updateModal{{ $patient->no }}">Edit</button>
                                                @endif
                                                
                                                @if(auth()->check() && auth()->user()->role === 'admin')
                                                    <form action="{{ route('monitor.destroy', $patient->no) }}" method="POST" onsubmit="return confirm('Are you sure you want to delete this record?');">
                                                        @csrf
                                                        @method('DELETE')
                                                        <button type="submit" class="btn btn-sm btn-outline-danger">Del</button>
                                                    </form>
                                                @endif
                                            </div>
                                        </td>
                                    </tr>

                                    <!-- Update Modal for this specific Patient (HIDDEN ON PRINT) -->
                                    <div class="modal fade no-print" id="updateModal{{ $patient->no }}" tabindex="-1" aria-hidden="true">
                                        <div class="modal-dialog">
                                            <form action="{{ route('monitor.update', $patient->no) }}" method="POST">
                                                @csrf
                                                <div class="modal-content text-start">
                                                    <div class="modal-header bg-light">
                                                        <h5 class="modal-title fw-bold">Update Status: {{ $patient->patient_name }}</h5>
                                                        <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                                                    </div>
                                                    <div class="modal-body">
                                                        <div class="mb-3">
                                                            <label class="form-label fw-bold">Status</label>
                                                            <select name="status" class="form-select" required>
                                                                <option value="ORDER RECEIVED" {{ $patient->status == 'ORDER RECEIVED' ? 'selected' : '' }}>ORDER RECEIVED</option>
                                                                <option value="PROCESSING" {{ $patient->status == 'PROCESSING' ? 'selected' : '' }}>PROCESSING</option>
                                                                <option value="READY FOR COLLECTION" {{ $patient->status == 'READY FOR COLLECTION' ? 'selected' : '' }}>READY FOR COLLECTION</option>
                                                                <option value="COLLECTED BY PHARMACIST OR PPK/SN" {{ in_array($patient->status, ['COLLECTED BY PHARMACIST OR PPK/SN', 'COLLECTED BY STAFF NURSE/PPK']) ? 'selected' : '' }}>COLLECTED BY PHARMACIST OR PPK/SN</option>
                                                                <option value="COMPLETED" {{ $patient->status == 'COMPLETED' ? 'selected' : '' }}>COMPLETED</option>
                                                            </select>
                                                        </div>
                                                        <div class="mb-3">
                                                            <label class="form-label fw-bold">Remarks</label>
                                                            <input type="text" name="remarks" class="form-control" value="{{ $patient->remarks }}">
                                                        </div>
                                                    </div>
                                                    <div class="modal-footer">
                                                        <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
                                                        <button type="submit" class="btn btn-primary">Save Changes</button>
                                                    </div>
                                                </div>
                                            </form>
                                        </div>
                                    </div>
                                @empty
                                    <tr>
                                        <td colspan="12" class="text-center py-4 text-muted bg-white">
                                            No data available
                                        </td>
                                    </tr>
                                @endforelse
                            </tbody>
                        </table>
                    </div>
                </div>

                <!-- WORKFLOW LEGEND (HIDDEN ON PRINT) -->
                <div class="workflow-legend mb-4 no-print">
                    <div class="legend-step status-received">ORDER RECEIVED</div>
                    <div class="legend-step status-processing">PROCESSING</div>
                    <div class="legend-step status-ready">READY FOR COLLECTION</div>
                    <div class="legend-step status-collected">COLLECTED BY PHARMACIST<br>OR PPK/SN</div>
                    <div class="legend-step status-completed">COMPLETED</div>
                </div>
                
                <!-- Pagination (HIDDEN ON PRINT) -->
                <div class="d-flex justify-content-center mt-2 no-print">
                    {{ $patients->withQueryString()->links('pagination::bootstrap-5') }}
                </div>

            </div>
            
            <!-- GLOBAL FOOTER (HIDDEN ON PRINT) -->
            <footer class="main-footer no-print">
                &copy; 2026 Hospital Selayang. Developed by Muhammad Haziq Zikri (ITD HSEL)
            </footer>

        </div>
    </div>

    <!-- SCROLL TO TOP BUTTON (HIDDEN ON PRINT) -->
    <button id="scrollTopBtn" class="no-print" title="Go to top">⬆️</button>

    <!-- Add New Patient Modal (HIDDEN ON PRINT) -->
    <div class="modal fade no-print" id="addModal" tabindex="-1" aria-hidden="true">
        <div class="modal-dialog modal-lg">
            <form action="{{ route('monitor.store') }}" method="POST">
                @csrf
                <div class="modal-content">
                    <div class="modal-header bg-success text-white">
                        <h5 class="modal-title fw-bold">Add New Patient Record</h5>
                        <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal" aria-label="Close"></button>
                    </div>
                    <div class="modal-body bg-light">
                        <div class="row">
                            <div class="col-md-6 mb-3">
                                <label class="fw-bold">Date</label>
                                <input type="date" name="date" class="form-control" value="{{ date('Y-m-d') }}" required>
                            </div>
                            <div class="col-md-6 mb-3">
                                <label class="fw-bold">Time</label>
                                <input type="time" name="time" class="form-control" value="{{ date('H:i') }}" required>
                            </div>
                            <div class="col-md-6 mb-3">
                                <label class="fw-bold">Patient Name</label>
                                <input type="text" name="patient_name" id="add_patient_name" class="form-control" placeholder="Patient Name">
                            </div>
                            <div class="col-md-6 mb-3">
                                <label class="fw-bold">MRN</label>
                                <div class="input-group">
                                    <input type="text" name="mrn" id="add_mrn" class="form-control" placeholder="Enter MRN to search">
                                    <button class="btn btn-primary" type="button" id="btn_search_mrn" title="Search Patient Name">
                                        <!-- SVG Magnifying Glass Icon -->
                                        <svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" fill="currentColor" viewBox="0 0 16 16">
                                            <path d="M11.742 10.344a6.5 6.5 0 1 0-1.397 1.398h-.001q.044.06.098.115l3.85 3.85a1 1 0 0 0 1.415-1.414l-3.85-3.85a1 1 0 0 0-.115-.1zM12 6.5a5.5 5.5 0 1 1-11 0 5.5 5.5 0 0 1 11 0"/>
                                        </svg>
                                    </button>
                                </div>
                            </div>
                            <div class="col-md-4 mb-3">
                                <label class="fw-bold">Ward</label>
                                <select name="ward" class="form-select" required>
                                    <option value="">-- Select Ward --</option>
                                    @php
                                        $wards = ["2C","4A","4B","4C","4D","5A","5B","5C","5D","6A","6B","6C","6D","7A","7B","7C","7D","8A","8B","8C","8D","9A","9B","9C","9D","10A","10B","10C","10D","11B","11C","NICU","HDW","BURN UNIT","LABOUR ROOM","ICU","ED","OTHERS"];
                                    @endphp
                                    @foreach ($wards as $w)
                                        <option value="{{ $w }}">{{ $w }}</option>
                                    @endforeach
                                </select>
                            </div>
                            <div class="col-md-4 mb-3">
                                <label class="fw-bold">Item Count</label>
                                <input type="number" name="total_item" class="form-control" value="1" required>
                            </div>
                            <div class="col-md-4 mb-3">
                                <label class="fw-bold">Item 2 Count</label>
                                <input type="number" name="total_item2" class="form-control" value="0" required>
                            </div>
                            <div class="col-md-6 mb-3">
                                <label class="fw-bold">Supply</label>
                                <select name="supply" class="form-select" required>
                                    <option value="1 MONTH">1 MONTH</option>
                                    <option value="FULL SUPPLY">FULL SUPPLY</option>
                                </select>
                            </div>
                            <div class="col-md-6 mb-3">
                                <label class="fw-bold">Status</label>
                                <select name="status" class="form-select" required>
                                    <option value="ORDER RECEIVED">ORDER RECEIVED</option>
                                    <option value="PROCESSING">PROCESSING</option>
                                    <option value="READY FOR COLLECTION">READY FOR COLLECTION</option>
                                    <option value="COLLECTED BY PHARMACIST OR PPK/SN">COLLECTED BY PHARMACIST OR PPK/SN</option>
                                    <option value="COMPLETED">COMPLETED</option>
                                </select>
                            </div>
                        </div>
                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
                        <button type="submit" class="btn btn-success fw-bold">Save Record</button>
                    </div>
                </div>
            </form>
        </div>
    </div>

<!-- Bootstrap JS -->
<script src="{{ asset('js/bootstrap.bundle.min.js') }}"></script>
    
    <!-- Custom Scripts -->
    <script>
        // 1. Smart Auto Refresh Script (Every 30 secs)
        // setInterval(function() {
        //     if (!document.body.classList.contains('modal-open')) {
        //         window.location.reload();
        //     }
        // }, 30000);

        // 2. Scroll to Top Button Logic
        let upButton = document.getElementById("scrollTopBtn");

        if (upButton) {
            window.onscroll = function() {
                if (document.body.scrollTop > 100 || document.documentElement.scrollTop > 100) {
                    upButton.style.display = "block";
                } else {
                    upButton.style.display = "none";
                }
            };

            upButton.onclick = function() {
                window.scrollTo({top: 0, behavior: 'smooth'});
            };
        }

        // 3. Unified Date Preset Helper
        function setDatePreset(type) {
            let startDateInput = document.getElementById('start_date');
            let endDateInput = document.getElementById('end_date');
            let form = document.getElementById('filterForm');
            let wardSelect = document.getElementById('filter_ward');
            let d = new Date();

            if (type === 'today') {
                let year = d.getFullYear();
                let month = String(d.getMonth() + 1).padStart(2, '0');
                let day = String(d.getDate()).padStart(2, '0');
                let todayStr = `${year}-${month}-${day}`;
                startDateInput.value = todayStr;
                endDateInput.value = todayStr;
            } else if (type === 'yesterday') {
                d.setDate(d.getDate() - 1);
                let year = d.getFullYear();
                let month = String(d.getMonth() + 1).padStart(2, '0');
                let day = String(d.getDate()).padStart(2, '0');
                let yestStr = `${year}-${month}-${day}`;
                startDateInput.value = yestStr;
                endDateInput.value = yestStr;
            } else if (type === 'all') {
                // Clear dates and explicitly reset the ward to ALL
                startDateInput.value = '';
                endDateInput.value = '';
                if (wardSelect) wardSelect.value = 'ALL';
            }
            form.submit();
        }

        // 4. MRN Search with Magnifying Glass
        document.addEventListener('DOMContentLoaded', function() {
            let searchBtn = document.getElementById('btn_search_mrn');
            let mrnInput = document.getElementById('add_mrn');
            let nameInput = document.getElementById('add_patient_name');

            if (searchBtn && mrnInput && nameInput) {
                searchBtn.addEventListener('click', function() {
                    let mrn = mrnInput.value.trim();
                    
                    if (mrn !== '') {
                        let originalIcon = searchBtn.innerHTML;
                        searchBtn.innerHTML = '⏳'; 
                        searchBtn.disabled = true;
                        nameInput.placeholder = "Searching...";
                        nameInput.value = ''; // clear previous value
                        
                        // Call the Laravel API route
                        fetch(`{{ url('/api/search-mrn') }}?mrn=${mrn}`)
                        .then(response => response.json())
                            .then(data => {
                                if (data.success) {
                                    nameInput.value = data.patient_name;
                                } else {
                                    nameInput.placeholder = "No record found. Type manually.";
                                    alert("No previous record found for this MRN.");
                                }
                            })
                            .catch(error => {
                                console.error('Error fetching MRN:', error);
                                nameInput.placeholder = "Error fetching data";
                            })
                            .finally(() => {
                                searchBtn.innerHTML = originalIcon;
                                searchBtn.disabled = false;
                            });
                    } else {
                        alert("Please enter an MRN to search.");
                    }
                });
            }
        });
    </script>
</body>
</html>