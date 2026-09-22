@extends('layouts.app')

@section('title', 'Counselling Order List')
@section('page_title', 'Counselling Orders List')

@section('content')

    <!-- UNIFIED SEARCH & FILTER CARD (DEFAULT BLANK) -->
    <div class="card shadow-sm mb-4 border-0 no-print">
        <div class="card-body bg-white rounded">
            <form id="filterForm" action="{{ route('counselling.list') }}" method="GET" class="row g-3 align-items-end m-0">
                
                <input type="hidden" id="show_all" name="show_all" value="{{ request('show_all') }}">

                <!-- Search by MRN -->
                <div class="col-md-3">
                    <label class="form-label fw-bold small text-primary mb-1">Search by MRN</label>
                    <input type="text" name="mrn" class="form-control form-control-sm border-secondary" placeholder="Enter MRN..." value="{{ request('mrn') }}">
                </div>

                <!-- Search by Patient Name -->
                <div class="col-md-3">
                    <label class="form-label fw-bold small text-primary mb-1">Search by Name</label>
                    <input type="text" name="patient_name" class="form-control form-control-sm border-secondary" placeholder="Enter Patient Name..." value="{{ request('patient_name') }}">
                </div>

                <!-- UNIFIED DATE FILTER -->
                <div class="col-md-4">
                    <div class="d-flex justify-content-between align-items-center mb-1">
                        <label class="form-label fw-bold small text-primary mb-0">Filter by Date</label>
                        <div class="btn-group btn-group-sm" role="group">
                            <button type="button" class="btn btn-outline-primary py-0 px-2 fw-bold" style="font-size: 0.72rem;" onclick="setDatePreset('today')">Today</button>
                            <button type="button" class="btn btn-outline-primary py-0 px-2 fw-bold" style="font-size: 0.72rem;" onclick="setDatePreset('yesterday')">Yesterday</button>
                            <button type="button" class="btn btn-outline-primary py-0 px-2 fw-bold" style="font-size: 0.72rem;" onclick="setDatePreset('all')">All</button>
                        </div>
                    </div>
                    <div class="input-group input-group-sm">
                        <span class="input-group-text bg-light border-secondary">From</span>
                        <input type="date" id="start_date" name="start_date" class="form-control border-secondary" value="{{ request('start_date') }}">
                        <span class="input-group-text bg-light border-secondary">To</span>
                        <input type="date" id="end_date" name="end_date" class="form-control border-secondary" value="{{ request('end_date') }}">
                    </div>
                </div>

                <!-- Filter Search & Reset Buttons -->
                <div class="col-md-2 d-flex gap-2">
                    <button type="submit" class="btn btn-primary btn-sm px-3 fw-bold flex-grow-1" onclick="document.getElementById('show_all').value=''">Search</button>
                    <a href="{{ route('counselling.list') }}" class="btn btn-outline-secondary btn-sm px-3">Reset</a>
                </div>

            </form>
        </div>
    </div>
    
    @if(session('success'))
        <div class="alert alert-success shadow-sm fw-bold mb-4 no-print">
            ✅ {{ session('success') }}
        </div>
    @endif

    <!-- TABLE SECTION -->
    <div class="card shadow-sm border-0 rounded">
        
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

        <!-- TITLE BANNER HEADER -->
        <div class="d-flex justify-content-between align-items-center legacy-table-header">
            <span class="fw-bold">
                @if(request('start_date') && request('end_date') && request('start_date') === request('end_date') && request('start_date') === date('Y-m-d'))
                    TODAY'S COUNSELLING ORDER RESULT
                @elseif(request('start_date') && request('end_date') && request('start_date') === request('end_date') && request('start_date') === date('Y-m-d', strtotime('-1 day')))
                    YESTERDAY'S COUNSELLING ORDER RESULT
                @elseif(request('show_all') === '1')
                    ALL COUNSELLING ORDERS RESULT
                @elseif(request('mrn') || request('patient_name') || request('start_date') || request('end_date'))
                    COUNSELLING ORDER SEARCH RESULT
                @else
                    COUNSELLING ORDER LIST
                @endif
            </span>

            <!-- PRINT BUTTON -->
            <button type="button" onclick="window.print()" class="btn btn-sm btn-success fw-bold px-3 no-print shadow-sm" {{ $records->isEmpty() ? 'disabled' : '' }}>
                🖨️ Print Data
            </button>
        </div>

        <!-- PRINTABLE TABLE CONTENT -->
        <div class="card-body p-0 table-responsive">
            <table class="table table-bordered align-middle mb-0 legacy-table">
                <thead>
                    <tr>
                        <th class="text-center" style="width: 35px;">No</th>
                        <th class="text-center" style="width: 85px;">Date</th>
                        <th class="text-center" style="width: 70px;">Time</th>
                        <th class="text-center" style="width: 65px;">Ward</th>
                        <th style="min-width: 150px;">Name</th>
                        <th class="text-center" style="width: 95px;">MRN</th>
                        <th class="text-center" style="width: 110px;">Medication Status</th>
                        <th style="min-width: 220px;">Counselling Order</th>
                        <th style="min-width: 130px;">Doctor</th>
                        <th class="text-center" style="width: 110px;">Status</th>
                        <th style="min-width: 140px;">Remarks</th>
                        <th class="text-center no-print" style="width: 80px;">Action</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($records as $index =>$item)
                        @php $rowId = $item->id ?? $item->no; @endphp
                        <tr>
                            <!-- No -->
                            <td class="text-center fw-bold">{{ $records->firstItem() +$index }}</td>
                            
                            <!-- Date -->
                            <td class="text-center fw-bold">{{ $item->date ?? '-' }}</td>
                            
                            <!-- Time Ordered -->
                            <td class="text-center fw-bold">{{ $item->time ?? '-' }}</td>
                            
                            <!-- Ward -->
                            <td class="text-center fw-bold">{{ $item->ward ?? '-' }}</td>
                            
                            <!-- Name -->
                            <td class="fw-bold text-uppercase">{{ $item->patient_name ?? $item->name ?? '-' }}</td>
                            
                            <!-- MRN -->
                            <td class="text-center fw-bold">{{ $item->mrn ?? '-' }}</td>
                            
                            <!-- Medication Status -->
                            <td class="text-center fw-bold text-uppercase">
                                {{ $item->medication_status ?? $item->med_status ?? 'FIRST TIME USER' }}
                            </td>
                            
                            <!-- Counselling Order -->
                            <td class="fw-bold text-uppercase">
                                {{ $item->counselling_order ?? $item->consult_info ?? $item->medication ?? '-' }}
                            </td>
                            
                            <!-- Doctor (Now checking the 'doc' column explicitly) -->
                            <td class="fw-bold text-uppercase">{{ $item->doc ?? $item->doctor_name ?? $item->doctor ?? '-' }}</td>
                            
                            <!-- Status Dropdown -->
                            <td class="text-center">
                                <select name="status" form="updateForm{{ $rowId }}" class="form-select form-select-sm legacy-select fw-bold">
                                    <option value="REFERRED" {{ ($item->status ?? '') == 'REFERRED' ? 'selected' : '' }}>REFERRED</option>
                                    <option value="PENDING" {{ ($item->status ?? '') == 'PENDING' ? 'selected' : '' }}>PENDING</option>
                                    <option value="COMPLETED" {{ ($item->status ?? '') == 'COMPLETED' ? 'selected' : '' }}>COMPLETED</option>
                                    <option value="CANCELLED" {{ ($item->status ?? '') == 'CANCELLED' ? 'selected' : '' }}>CANCELLED</option>
                                </select>
                            </td>
                            
                            <!-- Remarks Input -->
                            <td>
                                <input type="text" name="remarks" form="updateForm{{ $rowId }}" class="form-control form-control-sm legacy-remark-input fw-bold" 
                                       value="{{ $item->remarks ?? '' }}" placeholder="Enter remarks...">
                            </td>

                            <!-- Action Update Button -->
                            <td class="text-center no-print">
                                <form id="updateForm{{ $rowId }}" action="{{ route('counselling.update', $rowId) }}" method="POST">
                                    @csrf
                                    <!-- Using PUT method if your web.php route requires it, otherwise POST -->
                                    <button type="submit" class="btn btn-sm btn-primary fw-bold px-2 shadow-sm">Update</button>
                                </form>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="12" class="text-center py-5 text-muted bg-white fw-bold">
                                @if(!request('mrn') && !request('patient_name') && !request('start_date') && !request('end_date') && !request('show_all'))
                                    🔍 Please enter an MRN, Patient Name, or select a Date filter to view records.
                                @else
                                    No counselling orders found matching your search criteria.
                                @endif
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>

    <!-- Pagination (NO PRINT) -->
    @if($records->hasPages())
    <div class="d-flex justify-content-center mt-3 no-print">
        {{ $records->links('pagination::bootstrap-5') }}
    </div>
    @endif

@endsection

@push('scripts')
<script>
    // Date Preset Helper Logic
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