@extends('layouts.app')

@push('styles')
    <link rel="stylesheet" href="https://cdn.datatables.net/1.13.6/css/dataTables.bootstrap5.min.css">
@endpush

@section('content')
<div class="container-fluid py-4">
    <h3 class="mb-4 text-dark fw-bold">Ward Order List</h3>

    <div class="card shadow-sm border-0">
        <div class="card-body">
            <form action="{{ route('ecdr.ward_list') }}" method="GET" class="mb-4">
                <div class="row g-3 align-items-end">
                    <div class="col-md-4">
                        <label class="form-label fw-bold">Select Ward</label>
                        <select name="wad" class="form-select">
                            <option value="">-- Select Ward --</option>
                            @foreach($wards ?? [] as $w)
                                <option value="{{ $w }}" {{ request('wad') == $w ? 'selected' : '' }}>{{ $w }}</option>
                            @endforeach
                        </select>
                    </div>
                    <div class="col-md-4">
                        <label class="form-label fw-bold">Date Filter</label>
                        <select name="dayview" class="form-select">
                            <option value="0" {{ request('dayview') == '0' ? 'selected' : '' }}>Today Only</option>
                            <option value="1" {{ request('dayview') == '1' ? 'selected' : '' }}>Today & Tomorrow</option>
                            <option value="2" {{ request('dayview') == '2' ? 'selected' : '' }}>Next 3 Days</option>
                        </select>
                    </div>
                    <div class="col-md-4">
                        <button type="submit" class="btn btn-primary w-100">Filter Orders</button>
                    </div>
                </div>
            </form>

            <div class="table-responsive">
                <table class="table table-striped table-hover border">
                    <thead class="table-dark">
                        <tr>
                            <th>Date Use</th>
                            <th>MRN</th>
                            <th>Patient Name</th>
                            <th>Ward</th>
                            <th>Status</th>
                            <th>Action</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach($wardOrders ?? [] as $order)
                            <tr>
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
                                    <button type="button" class="btn btn-sm btn-info text-white btn-view-order" data-id="{{ $order->id }}">View</button>
                                </td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
        </div>
    </div>
</div>

@include('ecdr.partials.view_modal')
@endsection