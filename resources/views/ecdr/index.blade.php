@extends('layouts.app')

@section('title', 'eCDR Module')
@section('page_title', 'Cytotoxic Drug Reconstitution (eCDR)')

@section('content')

    <!-- MODERN MEDISPHERE TAB BAR -->
    <div class="medi-card p-2 mb-4">
        <ul class="nav nav-pills gap-2" id="ecdrTabs" role="tablist">
            <li class="nav-item" role="presentation">
                <button class="nav-link active fw-bold px-4 py-2" id="order-tab" data-bs-toggle="tab" data-bs-target="#order" type="button" role="tab" style="border-radius: 8px; font-size: 13px;">
                    <i class="bi bi-plus-circle me-2"></i>Place New Order
                </button>
            </li>
            <li class="nav-item" role="presentation">
                <button class="nav-link fw-bold px-4 py-2" id="ward-list-tab" data-bs-toggle="tab" data-bs-target="#ward-list" type="button" role="tab" style="border-radius: 8px; font-size: 13px;">
                    <i class="bi bi-building me-2"></i>Ward Order List
                </button>
            </li>
            <li class="nav-item" role="presentation">
                <button class="nav-link fw-bold px-4 py-2" id="my-orders-tab" data-bs-toggle="tab" data-bs-target="#my-orders" type="button" role="tab" style="border-radius: 8px; font-size: 13px;">
                    <i class="bi bi-journal-check me-2"></i>View My Orders
                </button>
            </li>
        </ul>
    </div>

    <!-- TAB CONTENTS -->
    <div class="tab-content" id="ecdrTabsContent">
        <div class="tab-pane fade show active" id="order" role="tabpanel">
            @include('ecdr.partials.place_order')
        </div>

        <div class="tab-pane fade" id="ward-list" role="tabpanel">
            @include('ecdr.partials.ward_list')
        </div>

        <div class="tab-pane fade" id="my-orders" role="tabpanel">
            @include('ecdr.partials.my_orders')
        </div>
    </div>

    @include('ecdr.partials.modals')

@endsection