@extends('layouts.app')

@section('title', 'Live Monitoring')
@section('page_title', 'Live Monitoring')

@section('content')

@php
    $wards = ["2C","4A","4B","4C","4D","5A","5B","5C","5D","6A","6B","6C","6D","7A","7B","7C","7D","8A","8B","8C","8D","9A","9B","9C","9D","10A","10B","10C","10D","11B","11C","NICU","HDW","BURN UNIT","LABOUR ROOM","ICU","ED","OTHERS"];
    $isAdmin = auth()->check() && in_array(auth()->user()->role, ['admin', 'pharmacy']);
@endphp

<!-- FILTER SECTION -->
<div class="medi-card p-4 mb-4 no-print">
    <form method="GET" action="{{ route('monitor.index') }}" id="filterForm" class="row g-3 align-items-end m-0">
        
        <!-- Date Range -->
        <div class="col-md-5">
            <div class="d-flex justify-content-between align-items-center mb-2">
                <label class="form-label fw-bold mb-0" style="font-size: 12px; color: #525f7f;"><i class="bi bi-calendar3 me-1"></i> Date Range</label>
                <div class="btn-group btn-group-sm" role="group">
                    <button type="button" class="btn btn-outline-secondary py-0 px-2 fw-bold border-0" style="font-size: 11px; color: #0066ff;" onclick="setDatePreset('today')">Today</button>
                    <button type="button" class="btn btn-outline-secondary py-0 px-2 fw-bold border-0" style="font-size: 11px; color: #0066ff;" onclick="setDatePreset('yesterday')">Yesterday</button>
                    <button type="button" class="btn btn-outline-secondary py-0 px-2 fw-bold border-0" style="font-size: 11px; color: #0066ff;" onclick="setDatePreset('all')">All Time</button>
                </div>
            </div>
            <div class="input-group input-group-sm">
                <span class="input-group-text bg-white border-end-0" style="border-color: #e9ecef; color: #8898aa;">From</span>
                <input type="date" id="start_date" name="start_date" class="form-control medi-input border-start-0 ps-0" value="{{ $startDate ?? request('start_date') }}">
                <span class="input-group-text bg-white border-end-0 border-start-0" style="border-color: #e9ecef; color: #8898aa;">To</span>
                <input type="date" id="end_date" name="end_date" class="form-control medi-input border-start-0 ps-0" value="{{ $endDate ?? request('end_date') }}">
            </div>
        </div>

        <!-- Ward -->
        <div class="col-md-5">
            <label class="form-label fw-bold mb-2" style="font-size: 12px; color: #525f7f;"><i class="bi bi-building me-1"></i> Ward Selection</label>
            <select name="ward" id="filter_ward" class="form-select form-select-sm medi-input py-2">
                <option value="ALL">All Wards</option>
                @foreach ($wards  as $w)
                    <option value="{{ $w }}" {{ request('ward') == $w ? 'selected' : '' }}>{{ $w }}</option>
                @endforeach
            </select>
        </div>

        <!-- Action Buttons -->
        <div class="col-md-2 d-flex gap-2">
            <button type="submit" class="btn w-100 fw-bold" style="background: #0066ff; color: #fff; font-size: 13px; border-radius: 8px;">Search</button>
            <a href="{{ route('monitor.index') }}" class="btn w-100 fw-bold" style="background: #f4f5f7; color: #525f7f; font-size: 13px; border-radius: 8px;">Reset</a>
        </div>
    </form>
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

<!-- MODERN WORKFLOW LEGEND -->
<div class="modern-legend no-print mb-4">
    <span class="medi-badge badge-stage-1">ORDER RECEIVED</span>
    <i class="bi bi-chevron-right modern-legend-arrow"></i>
    <span class="medi-badge badge-stage-2">PROCESSING</span>
    <i class="bi bi-chevron-right modern-legend-arrow"></i>
    <span class="medi-badge badge-stage-3">READY FOR COLLECTION</span>
    <i class="bi bi-chevron-right modern-legend-arrow"></i>
    <span class="medi-badge badge-stage-4">COLLECTED BY STAFF</span>
    <i class="bi bi-chevron-right modern-legend-arrow"></i>
    <span class="medi-badge badge-stage-5">COMPLETED</span>
</div>

<!-- DATA TABLE CONTAINER -->
<div class="medi-table-container mb-4">
    <div class="medi-table-header flex-wrap gap-3">
        <div>
            <h6 class="medi-table-title">Live Patient Status Queue</h6>
            <div style="font-size: 11px; color: #8898aa; margin-top: 4px;">Last updated on {{ date('d M Y, H:i') }}</div>
        </div>
        <div class="d-flex gap-2 no-print">
            <button onclick="window.print()" class="btn-medi-action" style="background: #f4f5f7; color: #525f7f;"><i class="bi bi-printer me-1"></i> Print</button>
            @if($isAdmin)
                <button class="btn-medi-action" data-bs-toggle="modal" data-bs-target="#addPatientModal"><i class="bi bi-plus-lg me-1"></i> Add Patient</button>
            @endif
        </div>
    </div>
    
    <div class="table-responsive">
        <table class="table medi-table mb-0 w-100">
            <thead>
                <tr>
                    <th width="5%">#</th>
                    <th width="15%">Time / Date</th>
                    <th width="10%">Ward</th>
                    <th width="25%">Patient Name</th>
                    <th class="text-center" width="8%">Qty</th>
                    <th class="text-center" width="22%">Status</th>
                    <th width="10%">Collected By</th>
                    @if($isAdmin)
                        <th class="text-center no-print" width="5%">Action</th>
                    @endif
                </tr>
            </thead>
            <tbody>
                @forelse ($patients as $patient)
                    @php
                        $badgeClass = 'badge-stage-x';
                        if ($patient->status === 'ORDER RECEIVED')$badgeClass = 'badge-stage-1';
                        elseif ($patient->status === 'PROCESSING')$badgeClass = 'badge-stage-2';
                        elseif ($patient->status === 'READY FOR COLLECTION')$badgeClass = 'badge-stage-3';
                        elseif (str_contains($patient->status, 'COLLECTED'))$badgeClass = 'badge-stage-4';
                        elseif ($patient->status === 'COMPLETED')$badgeClass = 'badge-stage-5';
                    @endphp
                    <tr>
                        <td class="text-muted fw-bold">{{ $loop->iteration }}</td>
                        <td>
                            <div class="fw-bold" style="color: #172b4d;">{{ $patient->time }}</div>
                            <div style="font-size: 11px; color: #8898aa;">{{ date('d M Y', strtotime($patient->date)) }}</div>
                        </td>
                        <td><span class="text-muted fw-bold">{{ $patient->ward }}</span></td>
                        <td>
                            <div class="fw-bold text-uppercase" style="color: #32325d;">{{ $patient->patient_name }}</div>
                            <div style="font-size: 11px; color: #8898aa;">MRN: {{ $patient->mrn }}</div>
                            @if($patient->remarks)
                                <div style="font-size: 11px; color: #d32f2f; margin-top: 2px;"><i class="bi bi-exclamation-circle"></i> {{ $patient->remarks }}</div>
                            @endif
                        </td>
                        <td class="text-center">
                            <div class="fw-bold">{{ $patient->total_item2 }}</div>
                            <div style="font-size: 11px; color: #8898aa;">({{ $patient->total_item }} items)</div>
                        </td>
                        <td class="text-center">
                            <span class="medi-badge w-100 {{ $badgeClass }}">{{ $patient->status }}</span>
                        </td>
                        <td class="text-muted" style="font-size: 12px;">{{ $patient->takenby ?: '-' }}</td>
                        
                        @if($isAdmin)
                            <td class="text-center no-print">
                                <button class="btn btn-sm" style="background: transparent; color: #8898aa;" data-bs-toggle="modal" data-bs-target="#editModal{{ $patient->no }}" title="Edit Order">
                                    <i class="bi bi-pencil-square fs-6"></i>
                                </button>
                            </td>
                        @endif
                    </tr>
                @empty
                    <tr>
                        <td colspan="{{ $isAdmin ? 8 : 7 }}" class="text-center py-5">
                            <i class="bi bi-inbox fs-1 text-muted opacity-50"></i>
                            <div class="mt-2 fw-bold" style="color: #8898aa;">No data available for the selected filters.</div>
                        </td>
                    </tr>
                @endforelse
            </tbody>
        </table>
    </div>
    
    <!-- Pagination -->
    @if($patients->hasPages())
        <div class="d-flex justify-content-center p-3 border-top no-print" style="border-color: #f4f5f7 !important;">
            {{ $patients->withQueryString()->links('pagination::bootstrap-5') }}
        </div>
    @endif
</div>

<!-- EDIT MODALS (MOVED OUTSIDE TABLE BODY) -->
@if($isAdmin)
    @foreach ($patients as $patient)
        <div class="modal fade text-start" id="editModal{{ $patient->no }}" tabindex="-1" aria-hidden="true">
            <div class="modal-dialog modal-dialog-centered">
                <form method="POST" action="{{ route('monitor.update', $patient->no) }}" class="w-100">
                    @csrf
                    <div class="modal-content border-0 shadow" style="border-radius: 16px; overflow: hidden;">
                        <div class="modal-header" style="background: #e3efff; border-bottom: none; padding: 20px;">
                            <h6 class="modal-title fw-bold" style="color: #0066ff;"><i class="bi bi-pencil-square me-2"></i>Edit Order: {{ $patient->mrn }}</h6>
                            <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                        </div>
                        <div class="modal-body p-4 bg-white">
                            <div class="mb-3">
                                <label class="form-label fw-bold" style="font-size: 12px; color: #525f7f;">Status</label>
                                <select name="status" class="form-select medi-input fw-bold">
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
                                <label class="form-label fw-bold" style="font-size: 12px; color: #525f7f;">Collected By</label>
                                <input type="text" name="takenby" class="form-control medi-input" value="{{ $patient->takenby }}">
                            </div>
                            <div class="mb-1">
                                <label class="form-label fw-bold" style="font-size: 12px; color: #525f7f;">Remarks</label>
                                <input type="text" name="remarks" class="form-control medi-input" value="{{ $patient->remarks }}">
                            </div>
                        </div>
                        <div class="modal-footer" style="border-top: 1px solid #f4f5f7;">
                            <button type="button" class="btn fw-bold" style="color: #8898aa;" data-bs-dismiss="modal">Cancel</button>
                            <button type="submit" class="btn fw-bold px-4" style="background: #0066ff; color: #fff; border-radius: 8px;">Update Status</button>
                        </div>
                    </div>
                </form>
            </div>
        </div>
    @endforeach
@endif

<!-- ADD PATIENT MODAL -->
@if($isAdmin)
<div class="modal fade" id="addPatientModal" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog modal-lg modal-dialog-centered">
        <form method="POST" action="{{ route('monitor.store') }}">
            @csrf
            <div class="modal-content border-0 shadow" style="border-radius: 16px; overflow: hidden;">
                <div class="modal-header" style="background: #e6f9ed; border-bottom: none; padding: 20px;">
                    <h6 class="modal-title fw-bold" style="color: #00b341;"><i class="bi bi-person-plus-fill me-2"></i>New Data Entry</h6>
                    <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                </div>
                <div class="modal-body p-4 bg-white">
                    <div class="row g-3">
                        <div class="col-md-4">
                            <label class="form-label fw-bold" style="font-size: 12px; color: #525f7f;">Time ordered <span class="text-danger">*</span></label>
                            <input type="time" name="time" class="form-control medi-input" value="{{ date('H:i') }}" required>
                        </div>
                        <div class="col-md-4">
                            <label class="form-label fw-bold" style="font-size: 12px; color: #525f7f;">Ward <span class="text-danger">*</span></label>
                            <select name="ward" class="form-select medi-input" required>
                                <option value="">Select Ward</option>
                                @foreach ($wards as $w)
                                    <option value="{{ $w }}">{{ $w }}</option>
                                @endforeach
                            </select>
                        </div>
                        <div class="col-md-4">
                            <label class="form-label fw-bold" style="font-size: 12px; color: #525f7f;">Initial Status <span class="text-danger">*</span></label>
                            <select name="status" class="form-select medi-input fw-bold text-primary" required>
                                <option value="ORDER RECEIVED">ORDER RECEIVED</option>
                                <option value="PROCESSING" selected>PROCESSING</option>
                                <option value="READY FOR COLLECTION">READY FOR COLLECTION</option>
                            </select>
                        </div>
                        <div class="col-md-6">
                            <label class="form-label fw-bold" style="font-size: 12px; color: #525f7f;">MRN <span class="text-danger">*</span></label>
                            <div class="input-group">
                                <input type="text" name="mrn" id="add_mrn" class="form-control medi-input text-uppercase border-end-0" required>
                                <button type="button" class="btn border-start-0" id="btnSearchMrnAdd" style="background: #f8f9fa; border: 1px solid #e9ecef; color: #0066ff;"><i class="bi bi-search"></i></button>
                            </div>
                        </div>
                        <div class="col-md-6">
                            <label class="form-label fw-bold" style="font-size: 12px; color: #525f7f;">Patient name <span class="text-danger">*</span></label>
                            <input type="text" name="patient_name" id="add_patient_name" class="form-control medi-input text-uppercase" required>
                        </div>
                        <div class="col-md-6">
                            <label class="form-label fw-bold" style="font-size: 12px; color: #525f7f;">Total items <span class="text-danger">*</span></label>
                            <select name="total_item" class="form-select medi-input" required>
                                @for($i = 1; $i <= 20; $i++)
                                    <option value="{{ $i }}">{{ $i }}</option>
                                @endfor
                            </select>
                        </div>
                        <div class="col-md-6">
                            <label class="form-label fw-bold" style="font-size: 12px; color: #525f7f;">Quantity supplied <span class="text-danger">*</span></label>
                            <select name="total_item2" class="form-select medi-input" required>
                                <option value="1 WEEK" selected>1 WEEK</option>
                                <option value="2 WEEKS">2 WEEKS</option>
                                <option value="1 MONTH">1 MONTH</option>
                                <option value="2 MONTHS">2 MONTHS</option>
                                <option value="FULL SUPPLY">FULL SUPPLY</option>
                            </select>
                        </div>
                        <div class="col-md-12">
                            <label class="form-label fw-bold" style="font-size: 12px; color: #525f7f;">Remarks</label>
                            <input type="text" name="remarks" class="form-control medi-input text-uppercase" placeholder="Optional notes...">
                        </div>
                    </div>
                </div>
                <div class="modal-footer" style="border-top: 1px solid #f4f5f7;">
                    <button type="reset" class="btn fw-bold" style="color: #8898aa;">Reset Fields</button>
                    <button type="submit" class="btn fw-bold px-4" style="background: #00b341; color: #fff; border-radius: 8px;">Create Record</button>
                </div>
            </div>
        </form>
    </div>
</div>
@endif
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

    document.getElementById('btnSearchMrnAdd')?.addEventListener('click', function() {
        let mrn = document.getElementById('add_mrn').value.trim();
        if (!mrn) {
            alert('Please enter an MRN to search.');
            return;
        }
        
        let btn = this;
        let originalIcon = btn.innerHTML;
        btn.innerHTML = '<span class="spinner-border spinner-border-sm" role="status" aria-hidden="true"></span>';
        
        fetch(`/api/search-mrn?mrn=${encodeURIComponent(mrn)}`)
            .then(response => response.json())
            .then(data => {
                if (data.success && data.patient_name) {
                    document.getElementById('add_patient_name').value = data.patient_name;
                } else {
                    alert('MRN not found in master list.');
                }
            })
            .catch(error => console.error('Error fetching MRN:', error))
            .finally(() => {
                btn.innerHTML = originalIcon;
            });
    });
</script>
@endpush