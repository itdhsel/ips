@extends('layouts.app')

@section('title', 'Live Monitoring')
@section('page_title', 'Live Monitoring')

@section('content')

@php
    $wards = ["2C","4A","4B","4C","4D","5A","5B","5C","5D","6A","6B","6C","6D","7A","7B","7C","7D","8A","8B","8C","8D","9A","9B","9C","9D","10A","10B","10C","10D","11B","11C","NICU","HDW","BURN UNIT","LABOUR ROOM","ICU","ED","OTHERS"];
    $isAdmin = auth()->check() && in_array(auth()->user()->role, ['admin', 'pharmacy']);
@endphp

<!-- FILTER CARD -->
<div class="card shadow-sm mb-4 border-0 no-print">
    <div class="card-body bg-white rounded">
        <form method="GET" action="{{ route('monitor.index') }}" id="filterForm" class="row g-3 align-items-end m-0">
            
            <!-- Date Range -->
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
                    <input type="date" id="start_date" name="start_date" class="form-control border-secondary" value="{{ $startDate ?? request('start_date') }}">
                    <span class="input-group-text bg-light border-secondary">To</span>
                    <input type="date" id="end_date" name="end_date" class="form-control border-secondary" value="{{ $endDate ?? request('end_date') }}">
                </div>
            </div>

            <!-- Ward -->
            <div class="col-md-5">
                <label class="form-label fw-bold small mb-1 text-primary">🏥 Filter by Ward</label>
                <select name="ward" id="filter_ward" class="form-select form-select-sm border-secondary">
                    <option value="ALL">-- All Wards --</option>
                    @foreach ($wards as $w)
                        <option value="{{ $w }}" {{ request('ward') == $w ? 'selected' : '' }}>{{ $w }}</option>
                    @endforeach
                </select>
            </div>

            <!-- Action Buttons -->
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

<!-- HEADER -->
<div class="d-flex justify-content-between align-items-center mb-3">
    <h5 class="fw-bold text-secondary mb-0">Data Results</h5>
    <div class="d-flex gap-2 no-print">
        <button onclick="window.print()" class="btn btn-secondary shadow-sm px-3 fw-bold">🖨️ Print Data</button>
        @if($isAdmin)
            <button class="btn btn-success shadow-sm px-3 fw-bold" data-bs-toggle="modal" data-bs-target="#addPatientModal">+ Add New Patient</button>
        @endif
    </div>
</div>

<div class="text-center small fw-bold text-muted mb-2">
    Last updated on {{ date('d-m-Y H:i:s') }}
</div>

<!-- TABLE -->
<div class="card shadow-sm border-0 rounded mb-4">
    <div class="card-body p-0 table-responsive">
        <table class="table table-bordered table-striped table-hover mb-0 align-middle custom-table">
            <thead>
                <tr>
                    <th style="width: 50px;">NO</th>
                    <th style="width: 100px;">DATE</th>
                    <th style="width: 110px;">TIME</th>
                    <th style="width: 70px;">WARD</th>
                    <th>NAME</th>
                    <th style="width: 110px;">MRN</th>
                    <th style="width: 100px;">TOTAL</th>
                    <th style="width: 100px;">QTY</th>
                    <th style="width: 160px;">STATUS</th>
                    <th style="width: 130px;">COLLECTED BY</th>
                    <th>REMARKS</th>
                    @if($isAdmin)
                        <th style="width: 90px;" class="no-print">ACTION</th>
                    @endif
                </tr>
            </thead>
            <tbody>
                @forelse ($patients as $patient)
                    <tr>
                        <td class="text-center">{{ $loop->iteration }}</td>
                        <td class="text-center">{{ date('d/m/Y', strtotime($patient->date)) }}</td>
                        <td class="text-center">{{ $patient->time }}</td>
                        <td class="text-center"><span class="badge bg-secondary">{{ $patient->ward }}</span></td>
                        <td class="fw-bold text-uppercase text-start">{{ $patient->patient_name }}</td>
                        <td class="text-center">{{ $patient->mrn }}</td>
                        <td class="text-center">{{ $patient->total_item }}</td>
                        <td class="text-center">{{ $patient->total_item2 }}</td>
                        <td class="text-center">
                            @if($patient->status === 'ORDER RECEIVED')
                                <span class="badge status-received px-2 py-1 w-100">{{ $patient->status }}</span>
                            @elseif($patient->status === 'PROCESSING')
                                <span class="badge status-processing px-2 py-1 w-100">{{ $patient->status }}</span>
                            @elseif($patient->status === 'READY FOR COLLECTION')
                                <span class="badge status-ready px-2 py-1 w-100">{{ $patient->status }}</span>
                            @elseif(
                                    $patient->status === 'COLLECTED BY PHARMACIST OR PPK/SN'
                                    ||
                                    $patient->status === 'COLLECTED BY STAFF NURSE/PPK'
                                    )
                                <span class="badge status-collected px-2 py-1 w-100">{{ $patient->status }}</span>
                            @elseif($patient->status === 'COMPLETED')
                                <span class="badge status-completed px-2 py-1 w-100">{{ $patient->status }}</span>
                            @elseif($patient->status === 'CANCELLED')
                                <span class="badge bg-danger px-2 py-1 w-100">{{ $patient->status }}</span>
                            @else
                                <span class="badge bg-secondary px-2 py-1 w-100">{{ $patient->status }}</span>
                            @endif
                        </td>
                        <td class="text-center">{{ $patient->takenby ?: '-' }}</td>
                        <td class="text-center">{{ $patient->remarks ?: '-' }}</td>
                        
                        @if($isAdmin)
                            <td class="text-center no-print">
                                <button class="btn btn-sm btn-outline-primary px-2" data-bs-toggle="modal" data-bs-target="#editModal{{ $patient->no }}">Edit</button>
                            </td>

                            <!-- EDIT MODAL INSIDE LOOP -->
                            <div class="modal fade text-start" id="editModal{{ $patient->no }}" tabindex="-1" aria-hidden="true">
                                <div class="modal-dialog">
                                    <form method="POST" action="{{ route('monitor.update', $patient->no) }}">
                                        @csrf
                                        <div class="modal-content border-0 shadow">
                                            <div class="modal-header bg-primary text-white">
                                                <h5 class="modal-title fw-bold">✏️ Edit Patient Order</h5>
                                                <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal"></button>
                                            </div>
                                            <div class="modal-body bg-light">
                                                <div class="mb-3">
                                                    <label class="fw-bold small mb-1">Status</label>
                                                    <select name="status" class="form-select fw-bold">
                                                        <option value="ORDER RECEIVED" {{ $patient->status == 'ORDER RECEIVED' ? 'selected' : '' }}>ORDER RECEIVED</option>
                                                        <option value="PROCESSING" {{ $patient->status == 'PROCESSING' ? 'selected' : '' }}>PROCESSING</option>
                                                        <option value="READY FOR COLLECTION" {{ $patient->status == 'READY FOR COLLECTION' ? 'selected' : '' }}>READY FOR COLLECTION</option>
                                                        <option value="COLLECTED BY PHARMACIST OR PPK/SN" {{ $patient->status == 'COLLECTED BY PHARMACIST OR PPK/SN' ? 'selected' : '' }}>COLLECTED BY PHARMACIST OR PPK/SN</option>
                                                        <option value="COLLECTED BY STAFF NURSE/PPK" {{ $patient->status == 'COLLECTED BY STAFF NURSE/PPK' ? 'selected' : '' }}>COLLECTED BY STAFF NURSE/PPK</option>
                                                        <option value="COMPLETED" {{ $patient->status == 'COMPLETED' ? 'selected' : '' }}>COMPLETED</option>
                                                        <option value="CANCELLED" {{ $patient->status == 'CANCELLED' ? 'selected' : '' }}>CANCELLED</option>
                                                    </select>
                                                </div>
                                                <div class="mb-3">
                                                    <label class="fw-bold small mb-1">Collected By</label>
                                                    <input type="text" name="takenby" class="form-control" value="{{ $patient->takenby }}">
                                                </div>
                                                <div class="mb-1">
                                                    <label class="fw-bold small mb-1">Remarks</label>
                                                    <input type="text" name="remarks" class="form-control" value="{{ $patient->remarks }}">
                                                </div>
                                            </div>
                                            <div class="modal-footer">
                                                <button type="button" class="btn btn-outline-secondary fw-bold" data-bs-dismiss="modal">Cancel</button>
                                                <button type="submit" class="btn btn-primary fw-bold px-4">Update Order</button>
                                            </div>
                                        </div>
                                    </form>
                                </div>
                            </div>
                        @endif
                    </tr>
                @empty
                    <tr>
                        <td colspan="12" class="text-center py-5 text-muted fw-bold">No data available</td>
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

<!-- ADD PATIENT MODAL (OUTSIDE LOOP) -->
@if($isAdmin)
<div class="modal fade" id="addPatientModal" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog modal-lg">
        <form method="POST" action="{{ route('monitor.store') }}">
            @csrf
            <div class="modal-content border-0 shadow">
                <div class="modal-header bg-success text-white">
                    <h5 class="modal-title fw-bold">➕ Add New Patient Order</h5>
                    <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal"></button>
                </div>
                <div class="modal-body bg-light">
                    <div class="row g-3">
                        <div class="col-md-4">
                            <label class="fw-bold small mb-1">MRN <span class="text-danger">*</span></label>
                            <div class="input-group">
                                <input type="text" name="mrn" id="add_mrn" class="form-control text-uppercase" required>
                                <button type="button" class="btn btn-outline-secondary fw-bold" id="btnSearchMrnAdd">🔍</button>
                            </div>
                        </div>
                        <div class="col-md-8">
                            <label class="fw-bold small mb-1">Patient Name <span class="text-danger">*</span></label>
                            <input type="text" name="patient_name" id="add_patient_name" class="form-control text-uppercase" required>
                        </div>
                        <div class="col-md-4">
                            <label class="fw-bold small mb-1">Ward <span class="text-danger">*</span></label>
                            <select name="ward" class="form-select" required>
                                <option value="">-- Select Ward --</option>
                                @foreach ($wards as $w)
                                    <option value="{{ $w }}">{{ $w }}</option>
                                @endforeach
                            </select>
                        </div>
                        <div class="col-md-4">
                            <label class="fw-bold small mb-1">Total Items <span class="text-danger">*</span></label>
                            <input type="number" name="total_item" class="form-control" value="0" required min="0">
                        </div>
                        <div class="col-md-4">
                            <label class="fw-bold small mb-1">Quantity Supplied</label>
                            <input type="number" name="total_item2" class="form-control" value="0" min="0">
                        </div>
                        <div class="col-md-12">
                            <label class="fw-bold small mb-1">Remarks (Optional)</label>
                            <input type="text" name="remarks" class="form-control">
                        </div>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-outline-secondary fw-bold" data-bs-dismiss="modal">Cancel</button>
                    <button type="submit" class="btn btn-success fw-bold px-4">Save Patient</button>
                </div>
            </div>
        </form>
    </div>
</div>
@endif
@endsection

@push('scripts')
<script>
    // Rapid Date Preset Handlers
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

    // MRN Auto-Fill Search API
    document.getElementById('btnSearchMrnAdd')?.addEventListener('click', function() {
        let mrn = document.getElementById('add_mrn').value.trim();
        if (!mrn) {
            alert('Please enter an MRN to search.');
            return;
        }
        fetch(`/api/search-mrn?mrn=${encodeURIComponent(mrn)}`)
            .then(response => response.json())
            .then(data => {
                if (data.success && data.patient_name) {
                    document.getElementById('add_patient_name').value = data.patient_name;
                } else {
                    alert('MRN not found in master list.');
                }
            })
            .catch(error => console.error('Error fetching MRN:', error));
    });
</script>
@endpush