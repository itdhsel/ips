@extends('layouts.app')

@section('title', 'View eCDR Order')
@section('page_title', 'View Cytotoxic Drug Order')

@section('content')

    <!-- PRINT STYLES -->
    <style>
        @media print {
            body { background: white !important; }
            .medi-sidebar, .medi-header, .btn, .badge, .no-print { display: none !important; }
            .medi-card { border: none !important; box-shadow: none !important; padding: 0 !important; margin: 0 !important; }
            .main-content { margin-left: 0 !important; padding: 0 !important; }
            .print-title { display: block !important; text-align: center; margin-bottom: 20px; font-weight: bold; }
        }
        .print-title { display: none; }
    </style>

    <div class="medi-card mb-4 overflow-hidden">
        <div class="p-4 border-bottom d-flex align-items-center justify-content-between" style="background: #ffffff; border-color: #f4f5f7 !important;">
            <div>
                <h6 class="medi-table-title mb-1 print-title">CYTOTOXIC DRUG RECONSTITUTION ORDER</h6>
                <h6 class="medi-table-title mb-1 no-print"><i class="bi bi-file-earmark-text me-2 text-primary"></i>Order #{{ $master->cdr_id }} Details</h6>
            </div>
            <span class="badge bg-secondary px-3 py-2 no-print">READ ONLY</span>
        </div>

        <div class="p-4 bg-white">
            
            <!-- 1. PATIENT DEMOGRAPHICS -->
            <h6 class="fw-bold mb-3 pb-2 border-bottom" style="color: #172b4d;">Patient & Regimen Demographics</h6>
            <div class="row g-3 mb-4">
                <div class="col-md-3">
                    <label class="form-label fw-bold text-muted" style="font-size: 12px;">MRN</label>
                    <input type="text" class="form-control medi-input fw-bold" value="{{ $master->mrn }}" readonly>
                </div>
                <div class="col-md-6">
                    <label class="form-label fw-bold text-muted" style="font-size: 12px;">Patient Name</label>
                    <input type="text" class="form-control medi-input fw-bold" value="{{ $master->name }}" readonly>
                </div>
                <div class="col-md-3">
                    <label class="form-label fw-bold text-muted" style="font-size: 12px;">Ward</label>
                    <input type="text" class="form-control medi-input fw-bold" value="{{ $master->ward }}" readonly>
                </div>
                
                <div class="col-md-2">
                    <label class="form-label fw-bold text-muted" style="font-size: 12px;">Age</label>
                    <input type="text" class="form-control medi-input" value="{{ $master->age }}" readonly>
                </div>
                <div class="col-md-2">
                    <label class="form-label fw-bold text-muted" style="font-size: 12px;">Gender</label>
                    <input type="text" class="form-control medi-input" value="{{ $master->sex }}" readonly>
                </div>
                <div class="col-md-2">
                    <label class="form-label fw-bold text-muted" style="font-size: 12px;">Weight (kg)</label>
                    <input type="text" class="form-control medi-input" value="{{ $master->weight }}" readonly>
                </div>
                <div class="col-md-3">
                    <label class="form-label fw-bold text-muted" style="font-size: 12px;">Height (cm)</label>
                    <input type="text" class="form-control medi-input" value="{{ $master->height }}" readonly>
                </div>
                <div class="col-md-3">
                    <label class="form-label fw-bold text-muted" style="font-size: 12px;">BSA (m²)</label>
                    <input type="text" class="form-control medi-input fw-bold" value="{{ $master->bsa }}" readonly>
                </div>

                <div class="col-md-6">
                    <label class="form-label fw-bold text-muted" style="font-size: 12px;">Diagnosis</label>
                    <input type="text" class="form-control medi-input" value="{{ $master->diagnosis }}" readonly>
                </div>
                <div class="col-md-6">
                    <label class="form-label fw-bold text-muted" style="font-size: 12px;">Protocol / Regimen</label>
                    <input type="text" class="form-control medi-input" value="{{ $master->protocol }}" readonly>
                </div>
            </div>

            <!-- 2. TREATMENT CYCLES -->
            <h6 class="fw-bold mb-3 pb-2 border-bottom mt-5" style="color: #172b4d;">Treatment Cycles</h6>
            <div class="table-responsive mb-4">
                <table class="table medi-table table-bordered mb-0">
                    <thead style="background: #f4f5f7;">
                        <tr>
                            <th width="10%">Cycle</th>
                            <th width="20%">Start Date</th>
                            <th width="25%">Status</th>
                            <th>Cycle Remarks</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach($cycles as $index => $cycle)
                        <tr>
                            <td class="fw-bold align-middle">Cycle {{ $index + 1 }}</td>
                            <td class="align-middle">{{ $cycle->date_use }}</td>
                            <td class="align-middle fw-bold">{{ $cycle->orderstatus }}</td>
                            <td class="align-middle">{{ $cycle->remarks ?? '-' }}</td>
                        </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>

            <!-- 3. DRUG DETAILS -->
            <h6 class="fw-bold mb-3 pb-2 border-bottom mt-5" style="color: #172b4d;">Cytotoxic Drug Details</h6>
            <div class="table-responsive mb-4">
                <table class="table medi-table table-bordered mb-0">
                    <thead style="background: #f4f5f7;">
                        <tr>
                            <th width="5%">#</th>
                            <th width="40%">Drug Name</th>
                            <th width="25%">Dose</th>
                            <th width="30%">Remarks</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach($drugs as $index => $drug)
                        <tr>
                            <td class="align-middle">{{ $index + 1 }}</td>
                            <td class="align-middle fw-bold">{{ $drug->ubat }}</td>
                            <td class="align-middle">{{ $drug->dos }}</td>
                            <td class="align-middle">{{ $drug->catatan }}</td>
                        </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>

            <!-- 4. PHYSICIAN DETAILS & FOOTER -->
            <h6 class="fw-bold mb-3 pb-2 border-bottom mt-5" style="color: #172b4d;">Physician Authorization</h6>
            <div class="row g-3 mb-4">
                <div class="col-md-6">
                    <label class="form-label fw-bold text-muted" style="font-size: 12px;">Ordering Physician</label>
                    <input type="text" class="form-control medi-input fw-bold" value="{{ $master->orderedby }}" readonly>
                </div>
                <div class="col-md-6">
                    <label class="form-label fw-bold text-muted" style="font-size: 12px;">Master Order Remarks</label>
                    <input type="text" class="form-control medi-input" value="{{ $master->nota }}" readonly>
                </div>
            </div>

            <div class="d-flex gap-2 mt-5 pt-3 border-top no-print">
                <button type="button" class="btn fw-bold px-4" style="background: #f4f5f7; color: #525f7f; border-radius: 8px;" onclick="history.back()">
                    <i class="bi bi-arrow-left me-1"></i> Back
                </button>
                <button type="button" class="btn fw-bold px-4" style="background: #0066ff; color: #fff; border-radius: 8px;" onclick="window.print()">
                    <i class="bi bi-printer me-1"></i> Print Order
                </button>
            </div>
        </div>
    </div>
@endsection