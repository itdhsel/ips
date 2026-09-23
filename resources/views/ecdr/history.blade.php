@extends('layouts.app')

@push('styles')
    <link rel="stylesheet" href="https://cdn.datatables.net/1.13.6/css/dataTables.bootstrap5.min.css">
@endpush

@section('content')
<div class="container-fluid py-4">
    <h3 class="mb-4 text-dark fw-bold">View My Orders</h3>

    <div class="card shadow-sm border-0">
        <div class="card-body">
            <div class="table-responsive">
                <table class="table table-striped table-hover border">
                    <thead class="table-dark">
                        <tr>
                            <th>Order Date</th>
                            <th>Date Use</th>
                            <th>MRN</th>
                            <th>Patient Name</th>
                            <th>Ward</th>
                            <th>Status</th>
                            <th>Action</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach($myOrders ?? [] as $order)
                            <tr>
                                <td>{{ $order->date }}</td>
                                <td>{{ $order->date_use }}</td>
                                <td>{{ $order->mrn }}</td>
                                <td>{{ $order->patient_name }}</td>
                                <td>{{ $order->ward }}</td>
                                <td>
                                    <span class="badge {{ $order->status == 'ORDER RECEIVED' ? 'bg-warning text-dark' : ($order->status == 'CANCELLED' ? 'bg-danger' : 'bg-success') }}">
                                        {{ $order->status }}
                                    </span>
                                </td>
                                <td>
                                    <div class="d-flex gap-1">
                                        <button type="button" class="btn btn-sm btn-info text-white btn-view-order" data-id="{{ $order->id }}">View</button>
                                        @if($order->status == 'ORDER RECEIVED')
                                            <form action="{{ route('ecdr.cancel', $order->id) }}" method="POST" onsubmit="return confirm('Are you sure you want to cancel this order?');">
                                                @csrf
                                                <button type="submit" class="btn btn-sm btn-danger">Cancel</button>
                                            </form>
                                        @endif
                                    </div>
                                </td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
            
            <div class="mt-3 text-muted small">
                <p class="mb-0">Note: Displaying orders date use +/- 30 days from today. Please contact Pharmacist if you need to make amendments for your order.</p>
                <p class="mb-0">Last updated on {{ now()->format('d-m-Y H:i:s') }}</p>
            </div>
        </div>
    </div>
</div>

@include('ecdr.partials.view_modal')
@endsection