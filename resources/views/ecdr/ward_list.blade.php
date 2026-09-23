@extends('layouts.app')

@section('title', 'Ward Order List')
@section('page_title', 'Ward Cytotoxic Order Queue')

@section('content')

    <!-- FILTER SECTION -->
    <div class="medi-card p-4 mb-4">
        <form action="{{ route('ecdr.ward_list') }}" method="GET" class="row g-3 align-items-end m-0">
            <div class="col-md-5">
                <label class="form-label fw-bold mb-2" style="font-size: 12px; color: #525f7f;"><i class="bi bi-building me-1"></i> Select Ward</label>
                <select name="wad" class="form-select form-select-sm medi-input py-2">
                    <option value="">All Wards</option>
                    @foreach($wards ?? [] as $w)
                        <option value="{{ $w }}" {{ request('wad') == $w ? 'selected' : '' }}>{{ $w }}</option>
                    @endforeach
                </select>
            </div>
            
            <div class="col-md-5">
                <label class="form-label fw-bold mb-2" style="font-size: 12px; color: #525f7f;"><i class="bi bi-calendar-range me-1"></i> Date Filter</label>
                <select name="dayview" class="form-select form-select-sm medi-input py-2">
                    <option value="0" {{ request('dayview') == '0' ? 'selected' : '' }}>Today Only</option>
                    <option value="1" {{ request('dayview') == '1' ? 'selected' : '' }}>Today & Tomorrow</option>
                    <option value="2" {{ request('dayview') == '2' ? 'selected' : '' }}>Next 3 Days</option>
                </select>
            </div>
            
            <div class="col-md-2">
                <button type="submit" class="btn w-100 fw-bold" style="background: #0066ff; color: #fff; font-size: 13px; border-radius: 8px; height: 38px;">
                    Filter Queue
                </button>
            </div>
        </form>
    </div>

    <!-- MAIN QUEUE TABLE -->
    <div class="medi-table-container mb-4">
        <div class="medi-table-header flex-wrap gap-3">
            <div>
                <h6 class="medi-table-title mb-1">Ward Cytotoxic Reconstitution Orders</h6>
                <div style="font-size: 11px; color: #8898aa;">Live status of patient preparations for selected ward</div>
            </div>
            
            <button onclick="window.print()" class="btn-medi-action" style="background: #f4f5f7; color: #525f7f; height: 36px; padding: 0 16px;">
                <i class="bi bi-printer me-1"></i> Print Queue
            </button>
        </div>

        <div class="table-responsive">
            <table class="table medi-table mb-0 w-100">
                <thead>
                    <tr>
                        <th width="15%">Date Use</th>
                        <th width="15%">MRN</th>
                        <th width="35%">Patient Name</th>
                        <th width="12%">Ward</th>
                        <th class="text-center" width="13%">Status</th>
                        <th class="text-center" width="10%">Action</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($wardOrders ?? [] as $order)
                        @php
                            $st = strtoupper($order->status ?? 'ORDER RECEIVED');
                            $badgeClass = 'badge-stage-x';
                            if ($st === 'ORDER RECEIVED') $badgeClass = 'badge-stage-1';
                            elseif ($st === 'PROCESSING' || $st === 'IN PREPARATION') $badgeClass = 'badge-stage-2';
                            elseif ($st === 'READY FOR COLLECTION' || $st === 'READY') $badgeClass = 'badge-stage-3';
                            elseif ($st === 'COMPLETED') $badgeClass = 'badge-stage-5';
                        @endphp
                        <tr>
                            <td class="fw-bold" style="color: #0066ff;">{{ $order->date_use ? date('d M Y', strtotime($order->date_use)) : '-' }}</td>
                            <td class="fw-bold" style="color: #525f7f;">{{ $order->mrn }}</td>
                            <td class="fw-bold text-uppercase" style="color: #32325d;">{{ $order->patient_name }}</td>
                            <td><span class="text-muted fw-bold">{{ $order->ward }}</span></td>
                            <td class="text-center">
                                <span class="medi-badge w-100 {{ $badgeClass }}">{{ $order->status }}</span>
                            </td>
                            <td class="text-center">
                                <button type="button" class="btn btn-sm btn-view-order" data-id="{{ $order->id }}" style="background: #e3efff; color: #0066ff; border-radius: 6px; font-size: 11px; font-weight: 600;">
                                    View
                                </button>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="6" class="text-center py-5">
                                <i class="bi bi-inbox fs-1 text-muted opacity-50"></i>
                                <div class="mt-2 fw-bold" style="color: #8898aa;">No orders found for the selected ward/date criteria.</div>
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>

    @include('ecdr.partials.view_modal')

@endsection