@extends('layouts.app')

@section('title', 'Counselling Order List')
@section('page_title', 'Counselling Orders List')

@section('content')

    @if(session('success'))
        <div class="alert alert-success border-0 shadow-sm fw-bold no-print mb-4" style="border-radius: 10px; background-color: #e6f9ed; color: #00b341;">
            <i class="bi bi-check-circle-fill me-2"></i> {{ session('success') }}
        </div>
    @endif

    <!-- UNIFIED SEARCH & FILTER CONTAINER (NO PRINT) -->
    <div class="medi-card p-4 mb-4 no-print">
        <form id="filterForm" action="{{ route('counselling.list') }}" method="GET" class="row g-3 align-items-end m-0">
            
            <input type="hidden" id="show_all" name="show_all" value="{{ request('show_all') }}">

            <!-- Search by MRN -->
            <div class="col-md-3">
                <label class="form-label fw-bold mb-2" style="font-size: 12px; color: #525f7f;"><i class="bi bi-search me-1"></i> MRN Number</label>
                <input type="text" name="mrn" class="form-control medi-input text-uppercase" placeholder="Enter MRN..." value="{{ request('mrn') }}">
            </div>

            <!-- Search by Patient Name -->
            <div class="col-md-3">
                <label class="form-label fw-bold mb-2" style="font-size: 12px; color: #525f7f;"><i class="bi bi-person me-1"></i> Patient Name</label>
                <input type="text" name="patient_name" class="form-control medi-input text-uppercase" placeholder="Enter Patient Name..." value="{{ request('patient_name') }}">
            </div>

            <!-- DATE FILTER -->
            <div class="col-md-4">
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
                    <input type="date" id="start_date" name="start_date" class="form-control medi-input border-start-0 ps-0" value="{{ request('start_date') }}">
                    <span class="input-group-text bg-white border-end-0 border-start-0" style="border-color: #e9ecef; color: #8898aa;">To</span>
                    <input type="date" id="end_date" name="end_date" class="form-control medi-input border-start-0 ps-0" value="{{ request('end_date') }}">
                </div>
            </div>

            <!-- Action Buttons -->
            <div class="col-md-2 d-flex gap-2">
                <button type="submit" class="btn w-100 fw-bold" style="background: #0066ff; color: #fff; font-size: 13px; border-radius: 8px;" onclick="document.getElementById('show_all').value=''">Search</button>
                <a href="{{ route('counselling.list') }}" class="btn w-100 fw-bold d-flex align-items-center justify-content-center" style="background: #f4f5f7; color: #525f7f; font-size: 13px; border-radius: 8px;">Reset</a>
            </div>

        </form>
    </div>

    <!-- PRINT-ONLY FILTER SUMMARY -->
    <div class="print-filter-summary d-none">
        <strong class="text-uppercase">Active Filter Criteria:</strong>
        <span class="ms-2">
            <strong>Date Range:</strong> 
            {{ request('start_date') ? request('start_date') : (request('show_all') ? 'All Dates' : 'None') }}
            {{ request('end_date') ? ' to ' . request('end_date') : '' }}
        </span>
        @if(request('mrn'))
            <span class="ms-3"><strong>MRN:</strong> {{ request('mrn') }}</span>
        @endif
        @if(request('patient_name'))
            <span class="ms-3"><strong>Patient Name:</strong> {{ request('patient_name') }}</span>
        @endif
    </div>

    <!-- MAIN COUNSELLING DATA TABLE -->
    <div class="medi-table-container mb-4">
        
        <!-- HEADER TITLE BANNER -->
        <div class="medi-table-header flex-wrap gap-3">
            <div>
                <h6 class="medi-table-title mb-1">
                    @if(request('start_date') && request('end_date') && request('start_date') === request('end_date') && request('start_date') === date('Y-m-d'))
                        Today's Counselling Orders
                    @elseif(request('start_date') && request('end_date') && request('start_date') === request('end_date') && request('start_date') === date('Y-m-d', strtotime('-1 day')))
                        Yesterday's Counselling Orders
                    @elseif(request('show_all') === '1')
                        All Counselling Orders
                    @elseif(request('mrn') || request('patient_name') || request('start_date') || request('end_date'))
                        Counselling Order Search Results
                    @else
                        Counselling Order Queue
                    @endif
                </h6>
                <div style="font-size: 11px; color: #8898aa;">Showing records based on current search filter</div>
            </div>

            <!-- PRINT BUTTON -->
            <button type="button" onclick="window.print()" class="btn-medi-action no-print" style="background: #f4f5f7; color: #525f7f; height: 38px; padding: 0 16px;" {{ $records->isEmpty() ? 'disabled' : '' }}>
                <i class="bi bi-printer me-1"></i> Print Queue
            </button>
        </div>

        <!-- PRINTABLE TABLE CONTENT -->
        <div class="table-responsive">
            <table class="table medi-table mb-0 w-100">
                <thead>
                    <tr>
                        <th class="text-center" width="4%">#</th>
                        <th width="12%">Date & Time</th>
                        <th width="8%">Ward</th>
                        <th width="20%">Patient Name</th>
                        <th width="22%">Counselling Order</th>
                        <th width="12%">Doctor</th>
                        <th class="text-center" width="10%">Status</th>
                        <th width="12%">Remarks</th>
                        <th class="text-center no-print" width="5%">Action</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($records as $index =>$item)
                        @php $rowId = $item->id ?? $item->no; @endphp
                        <tr>
                            <!-- # -->
                            <td class="text-center text-muted fw-bold">{{ $records->firstItem() +$index }}</td>
                            
                            <!-- Date & Time -->
                            <td>
                                <div class="fw-bold" style="color: #172b4d;">{{ $item->date ? date('d M Y', strtotime($item->date)) : '-' }}</div>
                                <div style="font-size: 11px; color: #8898aa;">{{ $item->time ?? '-' }}</div>
                            </td>
                            
                            <!-- Ward -->
                            <td><span class="text-muted fw-bold">{{ $item->ward ?? '-' }}</span></td>
                            
                            <!-- Patient Name & MRN -->
                            <td>
                                <div class="fw-bold text-uppercase" style="color: #32325d;">{{ $item->patient_name ?? $item->name ?? '-' }}</div>
                                <div style="font-size: 11px; color: #8898aa;">MRN: {{ $item->mrn ?? '-' }}</div>
                                <div style="font-size: 10px; color: #0066ff;" class="text-uppercase fw-bold mt-1">
                                    <i class="bi bi-tag me-1"></i>{{ $item->medication_status ?? $item->med_status ?? 'FIRST TIME USER' }}
                                </div>
                            </td>
                            
                            <!-- Counselling Order Details -->
                            <td>
                                <div class="fw-medium text-uppercase" style="font-size: 12px; color: #172b4d; whitespace: pre-line;">
                                    {{ $item->counselling_order ?? $item->consult_info ?? $item->medication ?? '-' }}
                                </div>
                            </td>
                            
                            <!-- Requesting Doctor -->
                            <td>
                                <div class="fw-bold text-uppercase" style="font-size: 12px; color: #525f7f;">
                                    {{ $item->doc ?? $item->doctor_name ?? $item->doctor ?? '-' }}
                                </div>
                            </td>
                            
                            <!-- Status Selector -->
                            <td class="text-center">
                                @php
                                    $st = strtoupper($item->status ?? 'REFERRED');
                                    $statusBg = '#e3efff'; $statusFg = '#0066ff';
                                    if ($st === 'COMPLETED') { $statusBg = '#e6f9ed'; $statusFg = '#00b341'; }
                                    elseif ($st === 'PENDING') { $statusBg = '#fff3e0'; $statusFg = '#f57c00'; }
                                    elseif ($st === 'CANCELLED') { $statusBg = '#ffeaea'; $statusFg = '#d32f2f'; }
                                @endphp
                                <select name="status" form="updateForm{{ $rowId }}" class="form-select form-select-sm medi-input fw-bold text-center" style="background-color: {{ $statusBg }}; color: {{$statusFg }}; font-size: 11px;">
                                    <option value="REFERRED" {{ $st == 'REFERRED' ? 'selected' : '' }}>REFERRED</option>
                                    <option value="PENDING" {{ $st == 'PENDING' ? 'selected' : '' }}>PENDING</option>
                                    <option value="COMPLETED" {{ $st == 'COMPLETED' ? 'selected' : '' }}>COMPLETED</option>
                                    <option value="CANCELLED" {{ $st == 'CANCELLED' ? 'selected' : '' }}>CANCELLED</option>
                                </select>
                            </td>
                            
                            <!-- Remarks Input -->
                            <td>
                                <input type="text" name="remarks" form="updateForm{{ $rowId }}" class="form-control form-control-sm medi-input" 
                                       value="{{ $item->remarks ?? '' }}" placeholder="Enter notes...">
                            </td>

                            <!-- Action Update Button -->
                            <td class="text-center no-print">
                                <form id="updateForm{{ $rowId }}" action="{{ route('counselling.update', $rowId) }}" method="POST">
                                    @csrf
                                    <button type="submit" class="btn btn-sm fw-bold px-2" style="background: #e3efff; color: #0066ff; border-radius: 6px; font-size: 11px;" title="Save updates">
                                        Update
                                    </button>
                                </form>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="9" class="text-center py-5">
                                <i class="bi bi-inbox fs-1 text-muted opacity-50"></i>
                                <div class="mt-2 fw-bold" style="color: #8898aa;">
                                    @if(!request('mrn') && !request('patient_name') && !request('start_date') && !request('end_date') && !request('show_all'))
                                        Enter an MRN, Patient Name, or select a Date filter to query counselling orders.
                                    @else
                                        No counselling orders found matching your search criteria.
                                    @endif
                                </div>
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>

        <!-- Pagination (NO PRINT) -->
        @if($records->hasPages())
            <div class="d-flex justify-content-center p-3 border-top no-print" style="border-color: #f4f5f7 !important;">
                {{ $records->withQueryString()->links('pagination::bootstrap-5') }}
            </div>
        @endif
    </div>

@endsection

@push('scripts')
<script>
    function setDatePreset(type) {
        let startDateInput = document.getElementById('start_date');
        let endDateInput = document.getElementById('end_date');
        let showAllInput = document.getElementById('show_all');
        let form = document.getElementById('filterForm');
        let d = new Date();

        if (type === 'today') {
            showAllInput.value = '';
            let year = d.getFullYear();
            let month = String(d.getMonth() + 1).padStart(2, '0');
            let day = String(d.getDate()).padStart(2, '0');
            let todayStr = `${year}-${month}-${day}`;
            startDateInput.value = todayStr;
            endDateInput.value = todayStr;
        } else if (type === 'yesterday') {
            showAllInput.value = '';
            d.setDate(d.getDate() - 1);
            let year = d.getFullYear();
            let month = String(d.getMonth() + 1).padStart(2, '0');
            let day = String(d.getDate()).padStart(2, '0');
            let yestStr = `${year}-${month}-${day}`;
            startDateInput.value = yestStr;
            endDateInput.value = yestStr;
        } else if (type === 'all') {
            startDateInput.value = '';
            endDateInput.value = '';
            showAllInput.value = '1';
        }

        form.submit();
    }
</script>
@endpush