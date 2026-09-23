@extends('layouts.app')

@section('title', 'Data Reporting')
@section('page_title', 'Data Reporting')

@section('content')
    <!-- FILTER CARD -->
    <div class="card shadow-sm mb-4 border-0 no-print">
        <div class="card-body bg-white rounded">
            <form action="{{ route('reports.index') }}" method="GET" class="row g-3 align-items-end m-0">
                <div class="col-md-6">
                    <label class="form-label fw-bold small text-primary mb-1">📊 Select Report Type</label>
                    <select name="report" class="form-select border-secondary fw-bold" required>
                        <option value="">-- Choose a Report --</option>
                        <option value="1" {{ $reportType == 1 ? 'selected' : '' }}>1. Total Patient Monthly</option>
                        <option value="2" {{ $reportType == 2 ? 'selected' : '' }}>2. Total Item Ordered Monthly</option>
                        <option value="3" {{ $reportType == 3 ? 'selected' : '' }}>3. Total Time Frame Order Monthly</option>
                        <option value="4" {{ $reportType == 4 ? 'selected' : '' }}>4. Bedside Dispensing Workload by Status</option>
                        <option value="5" {{ $reportType == 5 ? 'selected' : '' }}>5. Time Frame for Ready For Collection</option>
                    </select>
                </div>

                <div class="col-md-4">
                    <label class="form-label fw-bold small text-primary mb-1">📅 Select Year</label>
                    <select name="year" class="form-select border-secondary">
                        @for($y = date('Y'); $y >= 2018; $y--)
                            <option value="{{ $y }}" {{ $year == $y ? 'selected' : '' }}>{{ $y }}</option>
                        @endfor
                    </select>
                </div>
                
                <div class="col-md-2 d-flex gap-2">
                    <button type="submit" class="btn btn-primary fw-bold w-100">Generate</button>
                </div>
            </form>
        </div>
    </div>

    <!-- REPORT RESULTS SECTION -->
    @if($reportType)
        <div class="d-flex justify-content-between align-items-center mb-3 no-print">
            <h5 class="fw-bold text-secondary mb-0">Report Results: Year {{ $year }}</h5>
            <button type="button" onclick="window.print()" class="btn btn-success shadow-sm px-4 py-2 fw-bold">
                🖨️ Print Report
            </button>
        </div>
        
        <div class="card shadow border-0 rounded">
            <div class="legacy-table-header d-none d-print-block">
                iMONITOR REPORT: 
                @if($reportType == 1) Total Patient Monthly
                @elseif($reportType == 2) Total Item Ordered Monthly
                @elseif($reportType == 3) Total Time Frame Order Monthly
                @elseif($reportType == 4) Bedside Dispensing Workload by Status
                @elseif($reportType == 5) Time Frame for Ready For Collection
                @endif 
                ({{ $year }})
            </div>

            <div class="card-body p-0 table-responsive">
                <table class="table table-bordered table-striped align-middle mb-0 legacy-table">
                    <thead class="table-primary border-primary">
                        @if($reportType == 1 || $reportType == 2)
                            <tr>
                                <th>Month</th>
                                <th class="text-center">Total {{ $reportType == 1 ? 'Patients' : 'Items' }}</th>
                            </tr>
                        @elseif($reportType == 3 || $reportType == 4 || $reportType == 5)
                            <tr>
                                <th>Month</th>
                                <th>{{ $reportType == 3 ? 'Time Frame' : ($reportType == 4 ? 'Status' : 'Time Needed') }}</th>
                                <th class="text-center">Total</th>
                            </tr>
                        @endif
                    </thead>
                    <tbody>
                        @if($reportType == 1 || $reportType == 2)
                            @foreach($data as $month => $total)
                                <tr>
                                    <td class="fw-bold">{{ $month }}</td>
                                    <td class="text-center fw-bold text-primary">{{ $total }}</td>
                                </tr>
                            @endforeach
                        @elseif($reportType == 3 || $reportType == 4 || $reportType == 5)
                            @foreach($data as $month => $attributes)
                                @php $rowCount = count($attributes); $first = true; @endphp
                                @foreach($attributes as $key => $total)
                                    <tr>
                                        @if($first)
                                            <td rowspan="{{ $rowCount }}" class="fw-bold align-middle">{{ $month }}</td>
                                            @php $first = false; @endphp
                                        @endif
                                        <td>{{ $key }}</td>
                                        <td class="text-center fw-bold">{{ $total }}</td>
                                    </tr>
                                @endforeach
                            @endforeach
                        @endif
                    </tbody>
                </table>
            </div>
        </div>
    @else
        <div class="card shadow-sm border-0 py-5 text-center bg-white text-muted">
            <h4>📊</h4>
            <p class="fw-bold mb-0">Select a report type and year above to generate data.</p>
        </div>
    @endif
@endsection