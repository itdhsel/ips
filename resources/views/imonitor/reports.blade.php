@extends('layouts.app')

@section('title', 'Data Reporting')
@section('page_title', 'iMonitor Analytics & Reports')

@section('content')

    <!-- FILTER SECTION (NO PRINT) -->
    <div class="medi-card p-4 mb-4 no-print">
        <form action="{{ route('reports.index') }}" method="GET" class="row g-3 align-items-end m-0">
            <!-- REPORT TYPE SELECTOR -->
            <div class="col-md-6">
                <label class="form-label fw-bold mb-2" style="font-size: 12px; color: #525f7f;">
                    <i class="bi bi-bar-chart-line me-1"></i> Report Type
                </label>
                <select name="report" class="form-select form-select-sm medi-input py-2 fw-semibold" required>
                    <option value="">-- Select Report Category --</option>
                    <option value="1" {{ $reportType == 1 ? 'selected' : '' }}>1. Total Patient Monthly Summary</option>
                    <option value="2" {{ $reportType == 2 ? 'selected' : '' }}>2. Total Items Ordered Monthly</option>
                    <option value="3" {{ $reportType == 3 ? 'selected' : '' }}>3. Total Time Frame Orders Monthly</option>
                    <option value="4" {{ $reportType == 4 ? 'selected' : '' }}>4. Bedside Dispensing Workload by Status</option>
                    <option value="5" {{ $reportType == 5 ? 'selected' : '' }}>5. Time Frame for Ready For Collection</option>
                </select>
            </div>

            <!-- YEAR SELECTOR -->
            <div class="col-md-4">
                <label class="form-label fw-bold mb-2" style="font-size: 12px; color: #525f7f;">
                    <i class="bi bi-calendar-event me-1"></i> Analysis Year
                </label>
                <select name="year" class="form-select form-select-sm medi-input py-2">
                    @for($y = date('Y'); $y >= 2018; $y--)
                        <option value="{{ $y }}" {{ $year == $y ? 'selected' : '' }}>{{ $y }}</option>
                    @endfor
                </select>
            </div>
            
            <!-- SUBMIT BUTTON -->
            <div class="col-md-2 d-flex gap-2">
                <button type="submit" class="btn w-100 fw-bold" style="background: #0066ff; color: #fff; font-size: 13px; border-radius: 8px; height: 38px;">
                    <i class="bi bi-pie-chart me-1"></i> Generate
                </button>
            </div>
        </form>
    </div>

    <!-- REPORT RESULTS SECTION -->
    @if($reportType)
        <div class="medi-table-container mb-4">
            
            <!-- HEADER TOOLBAR -->
            <div class="medi-table-header flex-wrap gap-3">
                <div>
                    <h6 class="medi-table-title mb-1">
                        @if($reportType == 1) Total Patient Monthly Summary
                        @elseif($reportType == 2) Total Items Ordered Monthly
                        @elseif($reportType == 3) Total Time Frame Orders Monthly
                        @elseif($reportType == 4) Bedside Dispensing Workload by Status
                        @elseif($reportType == 5) Time Frame for Ready For Collection
                        @endif
                    </h6>
                    <div style="font-size: 11px; color: #8898aa;">Analytics summary for Year {{ $year }}</div>
                </div>

                <button type="button" onclick="window.print()" class="btn-medi-action no-print" style="background: #f4f5f7; color: #525f7f; height: 38px; padding: 0 16px;">
                    <i class="bi bi-printer me-1"></i> Print Report
                </button>
            </div>

            <!-- PRINTABLE BANNER (PRINT-ONLY) -->
            <div class="legacy-table-header d-none d-print-block p-3 fw-bold text-uppercase border-bottom">
                iMONITOR REPORT: 
                @if($reportType == 1) Total Patient Monthly
                @elseif($reportType == 2) Total Item Ordered Monthly
                @elseif($reportType == 3) Total Time Frame Order Monthly
                @elseif($reportType == 4) Bedside Dispensing Workload by Status
                @elseif($reportType == 5) Time Frame for Ready For Collection
                @endif 
                ({{ $year }})
            </div>

            <!-- TABLE RESULTS -->
            <div class="table-responsive">
                <table class="table medi-table mb-0 w-100">
                    <thead>
                        @if($reportType == 1 || $reportType == 2)
                            <tr>
                                <th width="50%">Month</th>
                                <th class="text-center" width="50%">Total {{ $reportType == 1 ? 'Patients' : 'Items' }}</th>
                            </tr>
                        @elseif($reportType == 3 || $reportType == 4 || $reportType == 5)
                            <tr>
                                <th width="30%">Month</th>
                                <th width="40%">{{ $reportType == 3 ? 'Time Frame' : ($reportType == 4 ? 'Status' : 'Time Needed') }}</th>
                                <th class="text-center" width="30%">Total Count</th>
                            </tr>
                        @endif
                    </thead>
                    <tbody>
                        @if($reportType == 1 || $reportType == 2)
                            @foreach($data as $month => $total)
                                <tr>
                                    <td class="fw-bold" style="color: #172b4d;">{{ $month }}</td>
                                    <td class="text-center fw-bold" style="color: #0066ff; font-size: 14px;">{{ number_format($total) }}</td>
                                </tr>
                            @endforeach
                        @elseif($reportType == 3 || $reportType == 4 || $reportType == 5)
                            @foreach($data as $month => $attributes)
                                @php $rowCount = count($attributes); $first = true; @endphp
                                @foreach($attributes as $key => $total)
                                    <tr>
                                        @if($first)
                                            <td rowspan="{{ $rowCount }}" class="fw-bold align-middle" style="color: #172b4d; background-color: #fafbfc;">
                                                {{ $month }}
                                            </td>
                                            @php $first = false; @endphp
                                        @endif
                                        <td class="fw-medium" style="color: #32325d;">{{ $key }}</td>
                                        <td class="text-center fw-bold" style="color: #172b4d;">{{ number_format($total) }}</td>
                                    </tr>
                                @endforeach
                            @endforeach
                        @endif
                    </tbody>
                </table>
            </div>
        </div>
    @else
        <!-- EMPTY INITIAL STATE -->
        <div class="medi-card p-5 text-center my-4">
            <div style="width: 60px; height: 60px; border-radius: 50%; background: #e3efff; color: #0066ff; display: flex; align-items: center; justify-content: center; margin: 0 auto 15px auto;">
                <i class="bi bi-file-earmark-bar-graph fs-2"></i>
            </div>
            <h6 class="fw-bold" style="color: #172b4d;">No Report Selected</h6>
            <p class="mb-0" style="font-size: 13px; color: #8898aa;">Select a report category and year above, then click <strong>Generate</strong> to view statistics.</p>
        </div>
    @endif

@endsection