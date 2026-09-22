@extends('layouts.app')

@section('title', 'Medication Collection')
@section('page_title', 'Discharge Medication Collection')

@section('content')

    @if(session('success'))
        <div class="alert alert-success shadow-sm fw-bold no-print">✅ {{ session('success') }}</div>
    @endif

    <!-- FULL WIDTH FILTER SECTION (NO PRINT) -->
    <div class="card shadow-sm mb-4 border-0 no-print">
        <div class="card-body bg-white rounded">
            <form action="{{ route('collection.index') }}" method="GET" id="filterForm" class="row g-3 align-items-end m-0">
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
                        <input type="date" id="start_date" name="start_date" class="form-control border-secondary" value="{{ $startDate }}">
                        <span class="input-group-text bg-light border-secondary">To</span>
                        <input type="date" id="end_date" name="end_date" class="form-control border-secondary" value="{{ $endDate }}">
                    </div>
                </div>
                
                <!-- WARD FILTER -->
                <div class="col-md-5">
                    <label class="form-label fw-bold small mb-1 text-primary">🏥 Filter by Ward</label>
                    <select name="ward" id="filter_ward" class="form-select form-select-sm border-secondary">
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
                    <a href="{{ route('collection.index') }}" class="btn btn-outline-secondary btn-sm px-3 w-100">Reset</a>
                </div>
            </form>
        </div>
    </div>

    <!-- PRINT-ONLY FILTER SUMMARY -->
    <div class="print-filter-summary d-none">
        <strong class="text-uppercase">Active Filter Criteria:</strong>
        <span class="ms-2">
            <strong>Date Range:</strong> 
            {{ request('start_date', request()->has('start_date') ? 'All Dates' : date('Y-m-d')) }}
            {{ request('end_date') ? ' to ' . request('end_date') : '' }}
        </span>
        @if(request('ward') && request('ward') != 'ALL')
            <span class="ms-3"><strong>Ward:</strong> {{ request('ward') }}</span>
        @endif
    </div>

    <!-- UNIFIED COLLECTION TABLE -->
    <form action="{{ route('collection.store') }}" method="POST" onsubmit="return validateForm()">
        @csrf
        
        <div class="d-flex justify-content-between align-items-center mb-2 no-print">
            <div class="bg-white p-3 rounded shadow-sm border border-primary flex-grow-1 me-3 d-flex align-items-center justify-content-between">
                <div class="d-flex align-items-center gap-3 w-50">
                    <label class="fw-bold mb-0 text-primary">Collector's Name:</label>
                    <input type="text" name="nurseName" id="nurseName" class="form-control w-75 fw-bold" style="background-color: #e9ecef; cursor: not-allowed;" value="{{ auth()->user()->name ?? auth()->user()->login_username }}" readonly>
                </div>
                <button type="submit" id="sendButton" class="btn btn-primary fw-bold px-4 shadow-sm" disabled>UPDATE SELECTED</button>
            </div>
            
            <button type="button" onclick="window.print()" class="btn btn-success shadow-sm px-4 py-3 fw-bold h-100">
                🖨️ Print Data
            </button>
        </div>
        
        <div class="card shadow-sm border-0">
            <div class="legacy-table-header d-none d-print-block">
                DISCHARGE MEDICATION COLLECTION REPORT
            </div>

            <div class="card-body p-0 table-responsive">
                <table class="table table-bordered table-striped table-hover mb-0 align-middle legacy-table">
                    <thead class="table-primary border-primary">
                        <tr>
                            <th class="text-center text-nowrap" style="width: 50px;">No</th>
                            <th class="text-center text-nowrap no-print" style="width: 100px;">
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
                        @forelse ($patients as $patient)
                            <tr>
                                <td class="text-center">{{ $loop->iteration }}</td>
                                <td class="text-center bg-white no-print">
                                    @if($patient->status == 'READY FOR COLLECTION' && empty($patient->takenby))
                                        <input type="checkbox" name="selectedData[]" value="{{ $patient->no }}" class="chk-item" style="transform: scale(1.3);">
                                    @else
                                        <span class="text-muted">-</span>
                                    @endif
                                </td>
                                <td><strong>{{ date('d/m/Y', strtotime($patient->date)) }}</strong><br><small class="text-muted d-print-none">{{ $patient->time }}</small></td>
                                <td><span class="badge bg-secondary px-2">{{ $patient->ward }}</span></td>
                                <td><strong class="text-uppercase">{{ $patient->patient_name }}</strong><br><small class="text-muted d-print-none">MRN: {{ $patient->mrn }}</small></td>
                                <td class="text-center fw-bold">{{ $patient->total_item }} / {{$patient->total_item2 }}</td>
                                <td class="text-center">
                                    @if($patient->status == 'READY FOR COLLECTION')
                                        <span class="badge bg-success px-2">{{ $patient->status }}</span>
                                    @else
                                        <span class="badge bg-dark px-2">{{ $patient->status }}</span>
                                    @endif
                                </td>
                                <td class="fw-bold text-primary">{{ $patient->takenby ?: '-' }}</td>
                            </tr>
                        @empty
                            <tr><td colspan="8" class="text-center py-5 text-muted fw-bold">No records found for this date range and ward filter.</td></tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
            
            <div class="d-flex justify-content-center mt-3 no-print">
                {{ $patients->withQueryString()->links('pagination::bootstrap-5') }}
            </div> 
        </div>
    </form>

@endsection

@push('scripts')
<script>
    // Unified Date Preset Helper
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
            if(startDateInput) startDateInput.value = todayStr;
            if(endDateInput) endDateInput.value = todayStr;
        } else if (type === 'yesterday') {
            d.setDate(d.getDate() - 1);
            let year = d.getFullYear();
            let month = String(d.getMonth() + 1).padStart(2, '0');
            let day = String(d.getDate()).padStart(2, '0');
            let yestStr = `${year}-${month}-${day}`;
            if(startDateInput) startDateInput.value = yestStr;
            if(endDateInput) endDateInput.value = yestStr;
        } else if (type === 'all') {
            if(startDateInput) startDateInput.value = '';
            if(endDateInput) endDateInput.value = '';
            if (wardSelect) wardSelect.value = 'ALL';
        }
        
        if(form) form.submit();
    }

    // Checkbox Logic
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
        if(sendButton) sendButton.disabled = !isChecked;
    }

    function validateForm() {
        if (sendButton && sendButton.disabled) {
            alert("Please select at least one record before updating.");
            return false;
        }
        return true;
    }
</script>
@endpush