@extends('layouts.app')

@section('title', 'Live Monitoring')
@section('page_title', 'Live Monitoring')

@section('content')

    <!-- FULL WIDTH FILTER SECTION -->
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
                        <input type="date" id="start_date" name="start_date" class="form-control border-secondary" value="{{ $startDate ?? request('start_date', date('Y-m-d')) }}">
                        <span class="input-group-text bg-light border-secondary">To</span>
                        <input type="date" id="end_date" name="end_date" class="form-control border-secondary" value="{{ $endDate ?? request('end_date', date('Y-m-d')) }}">
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
                    <a href="{{ route('monitor.index') }}" class="btn btn-outline-secondary btn-sm px-3 w-100">Reset</a>
                </div>
            </form>
        </div>
    </div>

    <!-- PRINT SUMMARY (PRINT-ONLY) -->
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

    <!-- ACTION BUTTONS & HEADING -->
    <div class="d-flex justify-content-between align-items-center mb-3">
        <h5 class="fw-bold text-secondary mb-0">Data Results</h5>
        <div class="d-flex gap-2 no-print">
            <button type="button" onclick="window.print()" class="btn btn-secondary shadow-sm px-3 fw-bold">
                🖨️ Print Data
            </button>
            @if(auth()->check() && in_array(auth()->user()->role, ['admin', 'pharmacy']))
                <button type="button" class="btn btn-success shadow-sm px-3 fw-bold" data-bs-toggle="modal" data-bs-target="#addPatientModal">
                    + Add New Patient
                </button>
            @endif
        </div>
    </div>

    <div class="text-center small fw-bold text-muted mb-2">
        Last updated on {{ date('d-m-Y H:i:s') }}
    </div>

    <!-- DATA TABLE CARD -->
    <div class="card shadow-sm border-0 rounded mb-4">
        <div class="card-body p-0 table-responsive">
            <table class="table table-bordered table-striped table-hover mb-0 align-middle custom-table">
                <thead>
                    <tr>
                        <th style="width: 50px;">NO</th>
                        <th style="width: 100px;">DATE</th>
                        <th style="width: 110px;">TIME ORDERED</th>
                        <th style="width: 70px;">WARD</th>
                        <th>NAME</th>
                        <th style="width: 110px;">MRN</th>
                        <th style="width: 100px;">TOTAL ITEMS</th>
                        <th style="width: 140px;">QUANTITY SUPPLIED</th>
                        <th style="width: 160px;">STATUS</th>
                        <th style="width: 130px;">COLLECTED BY</th>
                        <th>REMARKS</th>
                        @if(auth()->check() && in_array(auth()->user()->role, ['admin', 'pharmacy']))
                            <th style="width: 90px;" class="no-print">ACTION</th>
                        @endif
                    </tr>
                </thead>
                <tbody>
                    @forelse ($patients as $patient)
                        <tr>
                            <td>{{ $loop->iteration }}</td>
                            <td>{{ date('d/m/Y', strtotime($patient->date)) }}</td>
                            <td>{{ $patient->time }}</td>
                            <td><span class="badge bg-secondary">{{ $patient->ward }}</span></td>
                            <td class="text-start fw-bold text-uppercase">{{ $patient->patient_name }}</td>
                            <td>{{ $patient->mrn }}</td>
                            <td>{{ $patient->total_item }}</td>
                            <td>{{ $patient->total_item2 }}</td>
                            <td>
                            @if($patient->status === 'ORDER RECEIVED')
                                <span class="badge status-received px-2 py-1 w-100">{{ $patient->status }}</span>

                            @elseif($patient->status === 'PROCESSING')
                                <span class="badge status-processing px-2 py-1 w-100">{{ $patient->status }}</span>

                            @elseif($patient->status === 'READY FOR COLLECTION')
                                <span class="badge status-ready px-2 py-1 w-100">{{ $patient->status }}</span>

                            @elseif($patient->status === 'COLLECTED BY PHARMACIST OR PPK/SN' || $patient->status === 'COLLECTED BY STAFF NURSE/PPK')
                                <span class="badge status-collected px-2 py-1 w-100">{{ $patient->status }}</span>

                            @elseif($patient->status === 'COMPLETED')
                                <span class="badge status-completed px-2 py-1 w-100">{{ $patient->status }}</span>

                            @else
                                <span class="badge bg-secondary px-2 py-1 w-100">{{ $patient->status }}</span>
                            @endif
                            </td>
                            <td>{{ $patient->takenby ?: '-' }}</td>
                            <td>{{ $patient->remarks ?: '-' }}</td>
                            @if(auth()->check() && in_array(auth()->user()->role, ['admin', 'pharmacy']))
                                <td class="no-print">
                                    <button class="btn btn-sm btn-outline-primary px-2" data-bs-toggle="modal" data-bs-target="#editModal{{ $patient->no }}">Edit</button>
                                </td>
                            @endif
                        </tr>
                    @empty
                        <tr>
                            <td colspan="12" class="text-center py-4 text-muted fw-bold">No data available</td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
        @if($patients->hasPages())
            <div class="d-flex justify-content-center p-3 no-print">
                {{ $patients->withQueryString()->links('pagination::bootstrap-5') }}
            </div>
        @endif
    </div>

    <!-- WORKFLOW LEGEND -->
    <div class="workflow-legend no-print mb-4">
        <div class="legend-step status-received">ORDER RECEIVED</div>
        <div class="legend-step status-processing">PROCESSING</div>
        <div class="legend-step status-ready">READY FOR COLLECTION</div>
        <div class="legend-step status-collected">COLLECTED BY PHARMACIST OR PPK/SN</div>
        <div class="legend-step status-completed">COMPLETED</div>
    </div>

@endsection

@push('scripts')
<script>
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
</script>
@endpush