<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>iMonitor v3 - Medication Collection</title>
    <link href="{{ asset('css/bootstrap.min.css') }}" rel="stylesheet">
    <link href="{{ asset('css/style.css') }}" rel="stylesheet">
</head>
<body class="bg-light overflow-x-hidden">
    <div class="d-flex w-100 overflow-x-hidden">
        
        @include('partials.sidebar')

        <!-- MAIN CONTENT -->
        <div class="main-content d-flex flex-column min-vh-100">            
            <nav class="navbar top-header px-4 py-3 d-flex justify-content-between align-items-center">
                <h4 class="mb-0 fw-bold text-dark">Discharge Medication Collection</h4>
                <div class="text-muted small">
                    Logged in as: <strong class="text-dark">{{ auth()->user()->login_username ?? 'Unknown' }}</strong> 
                    <span class="badge bg-primary ms-2 px-2 py-1">{{ strtoupper(auth()->user()->role ?? 'User') }}</span>
                </div>
            </nav>

            <div class="container-fluid p-4">
                
                @if(session('success'))
                    <div class="alert alert-success shadow-sm fw-bold">✅ {{ session('success') }}</div>
                @endif

                <!-- FULL WIDTH FILTER SECTION -->
                <div class="card shadow-sm mb-4 border-0">
                    <div class="card-body bg-white rounded">
                        <form action="{{ route('collection.index') }}" method="GET" class="row g-3 align-items-end m-0">
                            
                            <!-- Date Range -->
                            <div class="col-md-5">
                                <div class="d-flex justify-content-between align-items-end mb-1">
                                    <label class="form-label fw-bold small mb-0 text-primary">📅 Filter by Date Range</label>
                                    <button type="button" class="btn btn-sm btn-outline-primary py-0 px-2 fw-bold" style="font-size: 0.75rem;" onclick="setToday()">Set Today</button>
                                </div>
                                <div class="input-group input-group-sm">
                                    <span class="input-group-text bg-light border-secondary">From</span>
                                    <input type="date" id="start_date" name="start_date" class="form-control border-secondary" value="{{ request('start_date', date('Y-m-d')) }}">
                                    <span class="input-group-text bg-light border-secondary">To</span>
                                    <input type="date" id="end_date" name="end_date" class="form-control border-secondary" value="{{ request('end_date', date('Y-m-d')) }}">
                                </div>
                            </div>
                            
                            <!-- Ward Dropdown -->
                            <div class="col-md-5">
                                <label class="form-label fw-bold small mb-1 text-primary">🏥 Filter by Ward</label>
                                <select name="ward" class="form-select form-select-sm border-secondary">
                                    <option value="">-- All Wards --</option>
                                    @foreach($wards as $w)
                                        <option value="{{ $w }}" {{ request('ward') == $w ? 'selected' : '' }}>{{ $w }}</option>
                                    @endforeach
                                </select>
                            </div>
                            
                            <!-- Search & Reset Buttons -->
                            <div class="col-md-2 d-flex gap-2">
                                <button type="submit" class="btn btn-primary btn-sm px-3 fw-bold w-100">Search</button>
                                <a href="{{ route('collection.index') }}" class="btn btn-outline-secondary btn-sm px-3 w-100">Reset</a>
                            </div>
                        </form>
                    </div>
                </div>

                <!-- UNIFIED COLLECTION TABLE -->
                <form action="{{ route('collection.store') }}" method="POST" onsubmit="return validateForm()">
                    @csrf
                    <div class="bg-white p-3 rounded shadow-sm border border-primary mb-3 d-flex align-items-center justify-content-between">
                        <div class="d-flex align-items-center gap-3 w-50">
                            <label class="fw-bold mb-0 text-primary">Collector's Name:</label>
                            <input type="text" name="nurseName" id="nurseName" class="form-control w-75" style="background-color: #e9ecef; cursor: not-allowed;" value="{{ auth()->user()->name ?? auth()->user()->login_username }}" readonly>
                        </div>
                        <button type="submit" id="sendButton" class="btn btn-primary fw-bold px-4" disabled>UPDATE SELECTED</button>
                    </div>
                    
                    <div class="card shadow-sm border-0">
                        <div class="card-body p-0 table-responsive">
                            <table class="table table-bordered table-striped table-hover mb-0 align-middle">
                                <thead class="table-primary border-primary">
                                    <tr>
                                    <th class="text-center text-nowrap" style="width: 50px;">No</th>
                                        <th class="text-center text-nowrap" style="width: 100px;">
                                            <div class="d-flex align-items-center justify-content-center gap-2">
                                                Select <input type="checkbox" id="checkAll" style="transform: scale(1.2);">
                                            </div>
                                        </th>
                                        <th>Date & Time</th>
                                        <th>Ward</th>
                                        <th>Patient Name (MRN)</th>
                                        <th>Items</th>
                                        <th>Status</th>
                                        <th>Collected By</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @forelse($patients as $patient)
                                        <tr>
                                            <td class="text-center">{{ $loop->iteration }}</td>
                                            
                                            <!-- Smart Checkbox: Only shows if Ready and Not Taken -->
                                            <td class="text-center bg-white">
                                                @if($patient->status == 'READY FOR COLLECTION' && empty($patient->takenby))
                                                    <input type="checkbox" name="selectedData[]" value="{{ $patient->no }}" class="chk-item" style="transform: scale(1.3);">
                                                @else
                                                    <span class="text-muted">-</span>
                                                @endif
                                            </td>

                                            <td><strong>{{ date('d/m/Y', strtotime($patient->date)) }}</strong><br><small class="text-muted">{{ $patient->time }}</small></td>
                                            <td><span class="badge bg-secondary">{{ $patient->ward }}</span></td>
                                            <td><strong>{{ $patient->patient_name }}</strong><br><small class="text-muted">MRN: {{ $patient->mrn }}</small></td>
                                            <td>{{ $patient->total_item }} / {{ $patient->total_item2 }}</td>
                                            <td>
                                                @if($patient->status == 'READY FOR COLLECTION')
                                                    <span class="badge bg-success">{{ $patient->status }}</span>
                                                @else
                                                    <span class="badge bg-dark">{{ $patient->status }}</span>
                                                @endif
                                            </td>
                                            <td class="fw-bold text-primary">{{ $patient->takenby ?: '-' }}</td>
                                        </tr>
                                    @empty
                                        <tr><td colspan="8" class="text-center py-5 text-muted">No records found for this date range and ward filter.</td></tr>
                                    @endforelse
                                </tbody>
                            </table>
                        </div>
                        <!-- Pagination Links (Max 50) -->
                        <div class="d-flex justify-content-center mt-3">
                            {{ $patients->withQueryString()->links('pagination::bootstrap-5') }}
                        </div> 
                    </div>
                </form>

            </div>

            <footer class="main-footer mt-auto">
                &copy; 2026 Hospital Selayang. Developed by Muhammad Haziq Zikri (ITD HSEL)
            </footer>
        </div>
    </div>

    <script src="{{ asset('js/bootstrap.bundle.min.js') }}"></script>
    
    <script>
        // 1. Date Range "Today" Button Logic
        function setToday() {
            let d = new Date();
            let year = d.getFullYear();
            let month = String(d.getMonth() + 1).padStart(2, '0');
            let day = String(d.getDate()).padStart(2, '0');
            let todayString = `${year}-${month}-${day}`;
            
            document.getElementById('start_date').value = todayString;
            document.getElementById('end_date').value = todayString;
        }

        // 2. Checkbox Logic
        let checkAll = document.getElementById('checkAll');
        let checkboxes = document.querySelectorAll('.chk-item');
        let sendButton = document.getElementById('sendButton');

        if(checkAll) {
            checkAll.addEventListener('change', function () {
                checkboxes.forEach(chk => chk.checked = this.checked);
                validateSendButton();
            });
        }

        checkboxes.forEach(function (checkbox) {
            checkbox.addEventListener('change', validateSendButton);
        });

        function validateSendButton() {
            let isChecked = Array.from(checkboxes).some(chk => chk.checked);
            sendButton.disabled = !isChecked;
        }

        function validateForm() {
            if (sendButton.disabled) {
                alert("Please select at least one record before updating.");
                return false;
            }
            return true;
        }
    </script>
</body>
</html>