@extends('layouts.app')

@section('title', 'Medication Collection')
@section('page_title', 'Discharge Medication Collection')

@section('content')

    @if(session('success'))
        <div class="alert alert-success border-0 shadow-sm fw-bold no-print mb-4" style="border-radius: 10px; background-color: #e6f9ed; color: #00b341;">
            <i class="bi bi-check-circle-fill me-2"></i> {{ session('success') }}
        </div>
    @endif

    <!-- FILTER SECTION (NO PRINT) -->
    <div class="medi-card p-4 mb-4 no-print">
        <form action="{{ route('collection.index') }}" method="GET" id="filterForm" class="row g-3 align-items-end m-0">
            <!-- DATE FILTER -->
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
                    <input type="date" id="start_date" name="start_date" class="form-control medi-input border-start-0 ps-0" value="{{ $startDate }}">
                    <span class="input-group-text bg-white border-end-0 border-start-0" style="border-color: #e9ecef; color: #8898aa;">To</span>
                    <input type="date" id="end_date" name="end_date" class="form-control medi-input border-start-0 ps-0" value="{{ $endDate }}">
                </div>
            </div>
            
            <!-- WARD FILTER -->
            <div class="col-md-5">
                <label class="form-label fw-bold mb-2" style="font-size: 12px; color: #525f7f;"><i class="bi bi-building me-1"></i> Ward Selection</label>
                <select name="ward" id="filter_ward" class="form-select form-select-sm medi-input py-2">
                    <option value="ALL" {{ request('ward', 'ALL') == 'ALL' ? 'selected' : '' }}>All Wards</option>
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
                <button type="submit" class="btn w-100 fw-bold" style="background: #0066ff; color: #fff; font-size: 13px; border-radius: 8px;">Search</button>
                <a href="{{ route('collection.index') }}" class="btn w-100 fw-bold" style="background: #f4f5f7; color: #525f7f; font-size: 13px; border-radius: 8px;">Reset</a>
            </div>
        </form>
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

    <!-- MAIN COLLECTION FORM & DATA TABLE -->
    <form action="{{ route('collection.store') }}" method="POST" onsubmit="return validateForm()">
        @csrf
        
        <!-- ACTION BAR -->
        <div class="medi-card p-3 mb-4 no-print d-flex align-items-center justify-content-between flex-wrap gap-3">
            <div class="d-flex align-items-center gap-3 flex-grow-1" style="max-width: 500px;">
                <label class="fw-bold text-nowrap mb-0" style="font-size: 12px; color: #525f7f;"><i class="bi bi-person-badge me-1"></i> Collector's Name:</label>
                <input type="text" name="nurseName" id="nurseName" class="form-control medi-input fw-bold" style="background-color: #f4f5f7; color: #172b4d; cursor: not-allowed;" value="{{ auth()->user()->name ?? auth()->user()->login_username }}" readonly>
            </div>
            
            <div class="d-flex gap-2 ms-auto">
                <button type="button" onclick="window.print()" class="btn-medi-action" style="background: #f4f5f7; color: #525f7f; height: 40px; padding: 0 16px;">
                    <i class="bi bi-printer me-1"></i> Print Data
                </button>
                <button type="submit" id="sendButton" class="btn fw-bold px-4" style="background: #0066ff; color: #fff; border-radius: 8px; height: 40px; font-size: 13px;" disabled>
                    <i class="bi bi-box-arrow-down me-1"></i> Update Selected
                </button>
            </div>
        </div>
        
        <!-- TABLE CONTAINER -->
        <div class="medi-table-container mb-4">
            <div class="legacy-table-header d-none d-print-block p-3 fw-bold text-uppercase border-bottom">
                DISCHARGE MEDICATION COLLECTION REPORT
            </div>

            <div class="table-responsive">
                <table class="table medi-table mb-0 w-100">
                    <thead>
                        <tr>
                            <th class="text-center" width="4%">#</th>
                            <th class="text-center no-print" width="8%">
                                <div class="d-flex align-items-center justify-content-center gap-2">
                                    <input type="checkbox" id="checkAll" class="form-check-input" style="cursor: pointer;">
                                </div>
                            </th>
                            <th width="15%">Date & Time</th>
                            <th width="10%">Ward</th>
                            <th width="30%">Patient Name</th>
                            <th class="text-center" width="10%">Items</th>
                            <th class="text-center" width="15%">Status</th>
                            <th width="10%">Collected By</th>
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
                                <td class="text-center text-muted fw-bold">{{ $loop->iteration }}</td>
                                <td class="text-center no-print">
                                @if ($patient->status == 'READY FOR COLLECTION' && (empty($patient->takenby) || $patient->takenby === '-'))                                        <input type="checkbox" name="selectedData[]" value="{{ $patient->no }}" class="chk-item form-check-input" style="cursor: pointer; width: 18px; height: 18px;">
                                    @else
                                        <span class="text-muted">-</span>
                                    @endif
                                </td>
                                <td>
                                    <div class="fw-bold" style="color: #172b4d;">{{ date('d M Y', strtotime($patient->date)) }}</div>
                                    <div style="font-size: 11px; color: #8898aa;" class="d-print-none">{{ $patient->time }}</div>
                                </td>
                                <td><span class="text-muted fw-bold">{{ $patient->ward }}</span></td>
                                <td>
                                    <div class="fw-bold text-uppercase" style="color: #32325d;">{{ $patient->patient_name }}</div>
                                    <div style="font-size: 11px; color: #8898aa;" class="d-print-none">MRN: {{ $patient->mrn }}</div>
                                </td>
                                <td class="text-center">
                                    <div class="fw-bold" style="color: #172b4d;">
                                        {{ (!empty($patient->total_item2) && $patient->total_item2 !== '0') ? $patient->total_item2 : $patient->total_item . ' ITEMS' }}
                                    </div>
                                    @if(!empty($patient->total_item2) && $patient->total_item2 !== '0')
                                        <div style="font-size: 11px; color: #8898aa;">({{ $patient->total_item }} items)</div>
                                    @endif
                                </td>
                                <td class="text-center">
                                    <span class="medi-badge w-100 {{ $badgeClass }}">{{ $patient->status }}</span>
                                </td>
                                <td class="fw-bold" style="color: #0066ff; font-size: 12px;">{{ $patient->takenby ?: '-' }}</td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="8" class="text-center py-5">
                                    <i class="bi bi-inbox fs-1 text-muted opacity-50"></i>
                                    <div class="mt-2 fw-bold" style="color: #8898aa;">No records found for the selected filters.</div>
                                </td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
            
            @if($patients->hasPages())
                <div class="d-flex justify-content-center p-3 border-top no-print" style="border-color: #f4f5f7 !important;">
                    {{ $patients->withQueryString()->links('pagination::bootstrap-5') }}
                </div>
            @endif
        </div>
    </form>

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