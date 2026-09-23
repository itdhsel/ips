@extends('layouts.app')

@section('title', 'Dashboard')
@section('page_title', 'Dashboard')

@push('styles')
    <link href="{{ asset('css/studio-ui.css') }}" rel="stylesheet">
    <style>
        /* Extra custom colors to match your 5-step workflow image exactly */
        .theme-orange .medi-kpi-top { background-color: #fff3e0; }
        .theme-orange .medi-kpi-icon { color: #f57c00; }
        .theme-cyan .medi-kpi-top { background-color: #e0f7fa; }
        .theme-cyan .medi-kpi-icon { color: #00acc1; }
    </style>
@endpush

@section('content')

    <!-- ==========================================
         SECTION 1: GENERAL PHARMACY (iMONITOR)
         ========================================== -->
    <div class="d-flex align-items-center mb-3">
        <h5 class="fw-bold mb-0" style="color: #172b4d;"><i class="bi bi-display me-2 text-primary"></i>iMonitor Overview</h5>
    </div>

    <!-- iMonitor KPIs (5 Cards) -->
    <div class="row row-cols-1 row-cols-md-5 g-3 mb-4">
        <div class="col">
            <div class="medi-card theme-red h-100">
                <div class="medi-kpi-top h-100">
                    <div class="medi-kpi-info">
                        <span class="medi-kpi-title">Order Received</span>
                        <span class="medi-kpi-val">{{ $kpi['received'] ?? 0 }}</span>
                    </div>
                    <div class="medi-kpi-icon"><i class="bi bi-inbox"></i></div>
                </div>
            </div>
        </div>

        <div class="col">
            <div class="medi-card theme-orange h-100">
                <div class="medi-kpi-top h-100">
                    <div class="medi-kpi-info">
                        <span class="medi-kpi-title">Processing</span>
                        <span class="medi-kpi-val">{{ $kpi['processing'] ?? 0 }}</span>
                    </div>
                    <div class="medi-kpi-icon"><i class="bi bi-arrow-repeat"></i></div>
                </div>
            </div>
        </div>

        <div class="col">
            <div class="medi-card theme-green h-100">
                <div class="medi-kpi-top h-100">
                    <div class="medi-kpi-info">
                        <span class="medi-kpi-title">Ready for Collection</span>
                        <span class="medi-kpi-val">{{ $kpi['ready'] ?? 0 }}</span>
                    </div>
                    <div class="medi-kpi-icon"><i class="bi bi-box2"></i></div>
                </div>
            </div>
        </div>

        <div class="col">
            <div class="medi-card theme-cyan h-100">
                <div class="medi-kpi-top h-100">
                    <div class="medi-kpi-info">
                        <span class="medi-kpi-title">Collected</span>
                        <span class="medi-kpi-val">{{ $kpi['collected'] ?? 0 }}</span>
                    </div>
                    <div class="medi-kpi-icon"><i class="bi bi-person-check"></i></div>
                </div>
            </div>
        </div>

        <div class="col">
            <div class="medi-card theme-purple h-100">
                <div class="medi-kpi-top h-100">
                    <div class="medi-kpi-info">
                        <span class="medi-kpi-title">Completed</span>
                        <span class="medi-kpi-val">{{ $kpi['completed'] ?? 0 }}</span>
                    </div>
                    <div class="medi-kpi-icon"><i class="bi bi-check2-all"></i></div>
                </div>
            </div>
        </div>
    </div>

    <!-- iMonitor Queue -->
    <div class="medi-table-container mb-5">
        <div class="medi-table-header">
            <h6 class="medi-table-title">Today Order Queue</h6>
            <a href="{{ route('monitor.index') }}" class="btn-medi-action text-decoration-none">View All iMonitor</a>
        </div>
        <div class="table-scroll" style="max-height: 300px;">
            <table class="table medi-table mb-0 w-100">
                <thead class="sticky-top">
                    <tr>
                        <th width="15%">Time</th>
                        <th width="20%">Ward</th>
                        <th width="40%">Patient Name</th>
                        <th class="text-center" width="25%">Status</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse ($activeQueue ?? [] as $patient)
                        <tr>
                            <td class="fw-bold">{{ $patient->time }}</td>
                            <td><span class="text-muted">{{ $patient->ward }}</span></td>
                            <td class="fw-bold">{{ $patient->patient_name }}</td>
                            <td class="text-center">
                                <span class="medi-badge w-100 
                                    {{ $patient->status === 'ORDER RECEIVED' ? 'badge-pending' : 
                                      ($patient->status === 'PROCESSING' ? 'badge-processing' : 
                                      ($patient->status === 'READY FOR COLLECTION' ? 'badge-completed' : 'badge-cancelled')) }}">
                                    {{ $patient->status }}
                                </span>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="4" class="text-center py-4 text-muted">No active general orders for today.</td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>

    <hr style="border-color: #e9ecef; margin: 2rem 0 3rem 0;">

    <!-- ==========================================
         SECTION 2: CYTOTOXIC UNIT (eCDR)
         ========================================== -->
    <div class="d-flex align-items-center mb-3 justify-content-between">
        <h5 class="fw-bold mb-0" style="color: #172b4d;"><i class="bi bi-prescription2 me-2 text-info"></i>Cytotoxic Unit (eCDR)</h5>
        <span class="medi-badge badge-processing px-3 py-2" style="font-size: 12px;">Next Prep Day: {{ in_array(now()->dayOfWeek, [1, 2, 3]) ? 'Wednesday' : 'Monday' }}</span>
    </div>

    <!-- eCDR KPIs (5 Cards) -->
    <div class="row row-cols-1 row-cols-md-5 g-3 mb-4">
        <div class="col">
            <div class="medi-card theme-blue h-100">
                <div class="medi-kpi-top h-100">
                    <div class="medi-kpi-info">
                        <span class="medi-kpi-title">Total Orders</span>
                        <span class="medi-kpi-val">{{ $ecdrKpi['total'] ?? 0 }}</span>
                    </div>
                    <div class="medi-kpi-icon"><i class="bi bi-files"></i></div>
                </div>
            </div>
        </div>

        <div class="col">
            <div class="medi-card theme-red h-100">
                <div class="medi-kpi-top h-100">
                    <div class="medi-kpi-info">
                        <span class="medi-kpi-title">Order Received</span>
                        <span class="medi-kpi-val">{{ $ecdrKpi['received'] ?? 0 }}</span>
                    </div>
                    <div class="medi-kpi-icon"><i class="bi bi-inbox"></i></div>
                </div>
            </div>
        </div>

        <div class="col">
            <div class="medi-card theme-orange h-100">
                <div class="medi-kpi-top h-100">
                    <div class="medi-kpi-info">
                        <span class="medi-kpi-title">In Preparation</span>
                        <span class="medi-kpi-val">{{ $ecdrKpi['preparing'] ?? 0 }}</span>
                    </div>
                    <div class="medi-kpi-icon"><i class="bi bi-moisture"></i></div>
                </div>
            </div>
        </div>

        <div class="col">
            <div class="medi-card theme-green h-100">
                <div class="medi-kpi-top h-100">
                    <div class="medi-kpi-info">
                        <span class="medi-kpi-title">Ready</span>
                        <span class="medi-kpi-val">{{ $ecdrKpi['ready'] ?? 0 }}</span>
                    </div>
                    <div class="medi-kpi-icon"><i class="bi bi-box2"></i></div>
                </div>
            </div>
        </div>
        
        <div class="col">
            <div class="medi-card theme-purple h-100">
                <div class="medi-kpi-top h-100">
                    <div class="medi-kpi-info">
                        <span class="medi-kpi-title">Dispensed</span>
                        <span class="medi-kpi-val">{{ $ecdrKpi['dispensed'] ?? 0 }}</span>
                    </div>
                    <div class="medi-kpi-icon"><i class="bi bi-send-check"></i></div>
                </div>
            </div>
        </div>
    </div>

    <!-- eCDR Queue -->
    <div class="medi-table-container mb-4">
        <div class="medi-table-header">
            <h6 class="medi-table-title">Cytotoxic Order Queue</h6>
            <a href="{{ route('ecdr.ward_list') }}" class="btn-medi-action text-decoration-none">Manage eCDR</a>
        </div>
        <div class="table-scroll" style="max-height: 300px;">
            <table class="table medi-table mb-0 w-100">
                <thead class="sticky-top">
                    <tr>
                        <th width="15%">Date Use</th>
                        <th width="20%">Ward</th>
                        <th width="40%">Patient Name</th>
                        <th class="text-center" width="25%">Status</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse ($ecdrQueue ?? [] as $order)
                        <tr>
                            <td class="fw-bold">{{ $order->date_use }}</td>
                            <td><span class="text-muted">{{ $order->ward }}</span></td>
                            <td class="fw-bold">{{ $order->patient_name }}</td>
                            <td class="text-center">
                                <span class="medi-badge w-100 {{ $order->status == 'ORDER RECEIVED' ? 'badge-pending' : ($order->status == 'CANCELLED' ? 'badge-cancelled' : 'badge-completed') }}">
                                    {{ $order->status }}
                                </span>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="4" class="text-center py-4 text-muted">No active cytotoxic orders.</td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>

@endsection

@push('scripts')
<script>
    setInterval(function() {
        window.location.reload();
    }, 30000);
</script>
@endpush