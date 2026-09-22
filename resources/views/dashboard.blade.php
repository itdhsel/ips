@extends('layouts.app')

@section('title', 'Live Dashboard')
@section('page_title', 'Live Status Dashboard')

@push('styles')
<style>
    .kpi-card {
        position: relative;
        border-radius: 6px;
        color: #fff;
        padding: 1.25rem;
        min-height: 125px;
        box-shadow: 0 4px 6px rgba(0,0,0,0.05);
        transition: transform 0.2s ease;
        overflow: hidden;
        display: flex;
        flex-direction: column;
        justify-content: space-between;
    }
    .kpi-card:hover { transform: translateY(-3px); }
    .bg-card-received { background-color: #ff7f8f; }
    .bg-card-processing { background-color: #fdb042; }
    .bg-card-ready { background-color: #a5d677; }
    .bg-card-completed { background-color: #b467c9; }
    .kpi-title { font-size: 1.05rem; font-weight: 700; margin-bottom: 8px; text-shadow: 1px 1px 2px rgba(0,0,0,0.15); }
    .kpi-value { font-size: 2.2rem; font-weight: 800; line-height: 1; text-shadow: 1px 1px 2px rgba(0,0,0,0.15); }
    .kpi-icon { position: absolute; bottom: 12px; right: 15px; opacity: 0.85; }
</style>
@endpush

@section('content')
    <!-- KPI METRICS ROW -->
    <div class="row g-3 mb-4">
        <div class="col-md-3">
            <div class="kpi-card bg-card-received">
                <div>
                    <div class="kpi-title">Total Orders</div>
                    <div class="kpi-value">{{ $kpi['total'] }}</div>
                </div>
            </div>
        </div>
        <div class="col-md-3">
            <div class="kpi-card bg-card-processing">
                <div>
                    <div class="kpi-title">Processing</div>
                    <div class="kpi-value">{{ $kpi['processing'] }}</div>
                </div>
            </div>
        </div>
        <div class="col-md-3">
            <div class="kpi-card bg-card-ready">
                <div>
                    <div class="kpi-title">Ready for Collection</div>
                    <div class="kpi-value">{{ $kpi['ready'] }}</div>
                </div>
            </div>
        </div>
        <div class="col-md-3">
            <div class="kpi-card bg-card-completed">
                <div>
                    <div class="kpi-title">Completed Today</div>
                    <div class="kpi-value">{{ $kpi['completed'] }}</div>
                </div>
            </div>
        </div>
    </div>

    <!-- ACTIVE QUEUE TABLE -->
    <div class="card shadow-sm border-0 rounded">
        <div class="card-header bg-white border-bottom-0 pt-4 pb-2 px-4 d-flex justify-content-between align-items-center">
            <h5 class="fw-bold text-dark mb-0">Active Queue</h5>
            <a href="{{ route('monitor.index') }}" class="btn btn-sm btn-outline-primary fw-bold">View Full History &rarr;</a>
        </div>
        <div class="card-body p-0 table-responsive">
            <table class="table table-hover align-middle mb-0 custom-table">
                <thead>
                    <tr>
                        <th class="text-center" style="width: 80px;">Time</th>
                        <th class="text-center" style="width: 80px;">Ward</th>
                        <th>Patient Name</th>
                        <th class="text-center" style="width: 120px;">MRN</th>
                        <th class="text-center" style="width: 180px;">Status</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse ($activeQueue as $patient)
                        <tr>
                            <td class="text-center fw-bold text-danger">{{ $patient->time }}</td>
                            <td class="text-center fw-bold">{{ $patient->ward }}</td>
                            <td class="fw-bold text-uppercase">{{ $patient->patient_name }}</td>
                            <td class="text-center">{{ $patient->mrn }}</td>
                            <td class="text-center">
                                @if($patient->status === 'ORDER RECEIVED')
                                    <span class="badge status-received px-3 py-2 w-100">{{ $patient->status }}</span>
                                @elseif($patient->status === 'PROCESSING')
                                    <span class="badge status-processing px-3 py-2 w-100">{{ $patient->status }}</span>
                                @elseif($patient->status === 'READY FOR COLLECTION')
                                    <span class="badge status-ready px-3 py-2 w-100">{{ $patient->status }}</span>
                                @else
                                    <span class="badge bg-secondary px-3 py-2 w-100">{{ $patient->status }}</span>
                                @endif
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="5" class="text-center py-5 text-muted fw-bold bg-white">
                                🎉 All active orders have been cleared for today!
                            </td>
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