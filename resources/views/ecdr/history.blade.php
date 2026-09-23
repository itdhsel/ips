@extends('layouts.app')

@section('title', 'My Orders')
@section('page_title', 'My eCDR Order History')

@section('content')

    <div class="medi-table-container mb-4">
        
        <div class="medi-table-header flex-wrap gap-3">
            <div>
                <h6 class="medi-table-title mb-1">My Submitted Cytotoxic Orders</h6>
                <div style="font-size: 11px; color: #8898aa;">Showing orders with preparation date use ±30 days from today</div>
            </div>
            
            <a href="{{ route('ecdr.create') }}" class="btn fw-bold d-flex align-items-center gap-1" style="background: #0066ff; color: #fff; border-radius: 8px; font-size: 12px; height: 36px;">
                <i class="bi bi-plus-lg"></i> New Order
            </a>
        </div>

        <div class="table-responsive">
            <table class="table medi-table mb-0 w-100">
                <thead>
                    <tr>
                        <th width="15%">Order Date</th>
                        <th width="15%">Date Use</th>
                        <th width="12%">MRN</th>
                        <th width="28%">Patient Name</th>
                        <th width="10%">Ward</th>
                        <th class="text-center" width="10%">Status</th>
                        <th class="text-center" width="10%">Action</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($myOrders ?? [] as $order)
                        @php
                            $st = strtoupper($order->status ?? 'ORDER RECEIVED');
                            $badgeClass = 'badge-stage-x';
                            if ($st === 'ORDER RECEIVED') $badgeClass = 'badge-stage-1';
                            elseif ($st === 'PROCESSING' || $st === 'IN PREPARATION') $badgeClass = 'badge-stage-2';
                            elseif ($st === 'READY FOR COLLECTION' || $st === 'READY') $badgeClass = 'badge-stage-3';
                            elseif ($st === 'COMPLETED') $badgeClass = 'badge-stage-5';
                        @endphp
                        <tr>
                            <td class="fw-bold" style="color: #172b4d;">{{ $order->date ? date('d M Y', strtotime($order->date)) : '-' }}</td>
                            <td class="fw-bold" style="color: #0066ff;">{{ $order->date_use ? date('d M Y', strtotime($order->date_use)) : '-' }}</td>
                            <td class="fw-bold" style="color: #525f7f;">{{ $order->mrn }}</td>
                            <td class="fw-bold text-uppercase" style="color: #32325d;">{{ $order->patient_name }}</td>
                            <td><span class="text-muted fw-bold">{{ $order->ward }}</span></td>
                            <td class="text-center">
                                <span class="medi-badge w-100 {{ $badgeClass }}">{{ $order->status }}</span>
                            </td>
                            <td class="text-center">
                                <div class="d-flex justify-content-center gap-1">
                                    <button type="button" class="btn btn-sm btn-view-order" data-id="{{ $order->id }}" style="background: #e3efff; color: #0066ff; border-radius: 6px; font-size: 11px; font-weight: 600;">
                                        View
                                    </button>
                                    @if($st === 'ORDER RECEIVED')
                                        <form action="{{ route('ecdr.cancel', $order->id) }}" method="POST" onsubmit="return confirm('Are you sure you want to cancel this order?');">
                                            @csrf
                                            <button type="submit" class="btn btn-sm" style="background: #ffeaea; color: #d32f2f; border-radius: 6px; font-size: 11px; font-weight: 600;">
                                                Cancel
                                            </button>
                                        </form>
                                    @endif
                                </div>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="7" class="text-center py-5">
                                <i class="bi bi-inbox fs-1 text-muted opacity-50"></i>
                                <div class="mt-2 fw-bold" style="color: #8898aa;">No active orders found in your history.</div>
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
        
        <div class="p-3 border-top" style="border-color: #f4f5f7 !important; font-size: 11px; color: #8898aa;">
            <div>* Displaying orders date use ±30 days from today. Contact Pharmacy Ext. 2043 for order amendments.</div>
            <div>* Last updated on {{ date('d M Y, H:i:s') }}</div>
        </div>
    </div>

    @include('ecdr.partials.view_modal')

@endsection