@extends('layouts.app')

@push('styles')
    <link rel="stylesheet" href="https://cdn.datatables.net/1.13.6/css/dataTables.bootstrap5.min.css">
@endpush

@section('content')
<div class="container-fluid py-4">
    <h3 class="mb-4 text-dark fw-bold">eCDR - Cytotoxic Drug Ordering</h3>

    <!-- Tabs -->
    <ul class="nav nav-tabs mb-4" id="ecdrTabs" role="tablist">
        <li class="nav-item" role="presentation">
            <button class="nav-link active" id="order-tab" data-bs-toggle="tab" data-bs-target="#order" type="button" role="tab" aria-controls="order" aria-selected="true">
                <i class="bi bi-plus-circle me-1"></i> Place New Order
            </button>
        </li>
        <li class="nav-item" role="presentation">
            <button class="nav-link" id="ward-list-tab" data-bs-toggle="tab" data-bs-target="#ward-list" type="button" role="tab" aria-controls="ward-list" aria-selected="false">
                <i class="bi bi-card-list me-1"></i> Ward Order List
            </button>
        </li>
        <li class="nav-item" role="presentation">
            <button class="nav-link" id="my-orders-tab" data-bs-toggle="tab" data-bs-target="#my-orders" type="button" role="tab" aria-controls="my-orders" aria-selected="false">
                <i class="bi bi-person-lines-fill me-1"></i> View My Orders
            </button>
        </li>
    </ul>

    <div class="tab-content" id="ecdrTabsContent">
        <div class="tab-pane fade show active" id="order" role="tabpanel" aria-labelledby="order-tab">
            @include('ecdr.partials.place_order')
        </div>

        <div class="tab-pane fade" id="ward-list" role="tabpanel" aria-labelledby="ward-list-tab">
            @include('ecdr.partials.ward_list')
        </div>

        <div class="tab-pane fade" id="my-orders" role="tabpanel" aria-labelledby="my-orders-tab">
            @include('ecdr.partials.my_orders')
        </div>
    </div>
</div>

@include('ecdr.partials.modals')

@endsection

@push('scripts')
<script src="https://code.jquery.com/jquery-3.7.1.min.js"></script>
<script src="https://cdn.datatables.net/1.13.6/js/jquery.dataTables.min.js"></script>
<script src="https://cdn.datatables.net/1.13.6/js/dataTables.bootstrap5.min.js"></script>
<script>
    // Keep your exact existing JavaScript block here from the previous file.
    // It remains unchanged as it now targets elements inside the includes.
</script>
@endpush