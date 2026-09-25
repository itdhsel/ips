@extends('layouts.app')

@section('title', 'Update eCDR Order')
@section('page_title', 'Update Cytotoxic Drug Order')

@section('content')

    <div class="medi-card mb-4 overflow-hidden">
        <div class="p-4 border-bottom d-flex align-items-center justify-content-between" style="background: #ffffff; border-color: #f4f5f7 !important;">
            <div>
                <h6 class="medi-table-title mb-1"><i class="bi bi-pencil-square me-2 text-primary"></i>Update Order #{{ $master->cdr_id }}</h6>
                <div style="font-size: 12px; color: #8898aa;">Modify patient demographics, treatment cycles, and medication dosage</div>
            </div>
            <span class="badge bg-warning text-dark px-3 py-2">EDIT MODE</span>
        </div>

        <div class="p-4 bg-white">
            <form action="{{ route('ecdr.update', $master->cdr_id) }}" method="POST">
                @csrf
                
                <!-- 1. PATIENT DEMOGRAPHICS -->
                <h6 class="fw-bold mb-3 pb-2 border-bottom" style="color: #172b4d;">Patient & Regimen Demographics</h6>
                <div class="row g-3 mb-4">
                    <div class="col-md-3">
                        <label class="form-label fw-bold" style="font-size: 12px;">MRN <span class="text-danger">*</span></label>
                        <input type="text" name="mrn" id="mrn" class="form-control medi-input text-uppercase" value="{{ $master->mrn }}" required>
                    </div>
                    <div class="col-md-6">
                        <label class="form-label fw-bold" style="font-size: 12px;">Patient Name <span class="text-danger">*</span></label>
                        <input type="text" name="patient_name" id="patient_name" class="form-control medi-input text-uppercase" value="{{ $master->name }}" required>
                    </div>
                    <div class="col-md-3">
                        <label class="form-label fw-bold" style="font-size: 12px;">Ward <span class="text-danger">*</span></label>
                        <select name="ward" id="ward" class="form-select medi-input" required>
                            @foreach($wards as $w)
                                <option value="{{ $w }}" {{ $master->ward == $w ? 'selected' : '' }}>{{ $w }}</option>
                            @endforeach
                        </select>
                    </div>
                    
                    <div class="col-md-2">
                        <label class="form-label fw-bold" style="font-size: 12px;">Age</label>
                        <input type="text" name="age" id="age" class="form-control medi-input" value="{{ $master->age }}">
                    </div>
                    <div class="col-md-2">
                        <label class="form-label fw-bold" style="font-size: 12px;">Gender</label>
                        <select name="sex" id="sex" class="form-select medi-input">
                            <option value="Male" {{ $master->sex == 'Male' ? 'selected' : '' }}>Male</option>
                            <option value="Female" {{ $master->sex == 'Female' ? 'selected' : '' }}>Female</option>
                        </select>
                    </div>
                    <div class="col-md-2">
                        <label class="form-label fw-bold" style="font-size: 12px;">Weight (kg)</label>
                        <input type="number" step="0.01" name="weight" id="ecdr_weight" class="form-control medi-input" value="{{ $master->weight }}">
                    </div>
                    <div class="col-md-3">
                        <label class="form-label fw-bold" style="font-size: 12px;">Height (cm)</label>
                        <input type="number" step="0.01" name="height" id="ecdr_height" class="form-control medi-input" value="{{ $master->height }}">
                    </div>
                    <div class="col-md-3">
                        <label class="form-label fw-bold" style="font-size: 12px;">BSA (m²)</label>
                        <input type="text" name="bsa" id="ecdr_bsa" class="form-control medi-input fw-bold" style="background-color: #f4f5f7;" value="{{ $master->bsa }}" readonly>
                    </div>

                    <div class="col-md-6">
                        <label class="form-label fw-bold" style="font-size: 12px;">Diagnosis</label>
                        <input type="text" name="diagnosis" id="diagnosis" class="form-control medi-input" value="{{ $master->diagnosis }}">
                    </div>
                    <div class="col-md-6">
                        <label class="form-label fw-bold" style="font-size: 12px;">Protocol / Regimen</label>
                        <input type="text" name="protocol" id="protocol" class="form-control medi-input" value="{{ $master->protocol }}">
                    </div>
                </div>

                <!-- 2. TREATMENT CYCLES -->
                <h6 class="fw-bold mb-3 pb-2 border-bottom mt-5" style="color: #172b4d;">Treatment Cycles</h6>
                <div class="medi-table-container mb-3">
                    <table class="table medi-table mb-0 w-100" id="cycleTable">
                        <thead>
                            <tr>
                                <th width="20%">Cycle #</th>
                                <th>Start Date <span class="text-danger">*</span></th>
                                <th width="10%" class="text-center">Action</th>
                            </tr>
                        </thead>
                        <tbody id="cycleBody">
                            @foreach($cycles as $index => $cycle)
                            <tr>
                                <td class="fw-bold align-middle cycle-number" style="color: #0066ff;">CYCLE {{ $index + 1 }}</td>
                                <td><input type="date" name="date_use[]" class="form-control medi-input" value="{{ $cycle->date_use }}" required></td>
                                <td class="text-center align-middle">
                                    <button type="button" class="btn btn-sm btn-delete-cycle" style="color: #d32f2f; background: transparent;" {{ $cycles->count() == 1 ? 'disabled' : '' }}>
                                        <i class="bi bi-trash fs-6"></i>
                                    </button>
                                </td>
                            </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>
                <button type="button" class="btn fw-bold mb-4" style="background: #e3efff; color: #0066ff; border-radius: 8px; font-size: 12px;" id="btnAddCycle">
                    <i class="bi bi-plus-lg me-1"></i> Add Treatment Cycle
                </button>

                <!-- 3. CYTOTOXIC DRUG DETAILS -->
                <h6 class="fw-bold mb-3 pb-2 border-bottom mt-4" style="color: #172b4d;">Cytotoxic Drug Details</h6>
                <div class="medi-table-container mb-3">
                    <table class="table medi-table mb-0 w-100" id="drugTable">
                        <thead>
                            <tr>
                                <th width="5%">#</th>
                                <th width="35%">Drug Name (Ubat) <span class="text-danger">*</span></th>
                                <th width="20%">Dose (Dos) <span class="text-danger">*</span></th>
                                <th width="35%">Remarks (Catatan)</th>
                                <th width="5%" class="text-center">Action</th>
                            </tr>
                        </thead>
                        <tbody id="drugBody">
                            @foreach($drugs as $index => $drug)
                            <tr>
                                <td class="fw-bold align-middle drug-number text-muted">{{ $index + 1 }}</td>
                                <td><input type="text" name="drug_name[]" class="form-control medi-input text-uppercase" value="{{ $drug->ubat }}" required></td>
                                <td><input type="text" name="dose[]" class="form-control medi-input" value="{{ $drug->dos }}" required></td>
                                <td><input type="text" name="catatan[]" class="form-control medi-input" value="{{ $drug->catatan }}"></td>
                                <td class="text-center align-middle">
                                    <button type="button" class="btn btn-sm btn-delete-drug" style="color: #d32f2f; background: transparent;" {{ $drugs->count() == 1 ? 'disabled' : '' }}>
                                        <i class="bi bi-trash fs-6"></i>
                                    </button>
                                </td>
                            </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>
                <button type="button" class="btn fw-bold mb-4" style="background: #e3efff; color: #0066ff; border-radius: 8px; font-size: 12px;" id="btnAddDrug">
                    <i class="bi bi-plus-lg me-1"></i> Add Drug Entry
                </button>

                <!-- REMARKS & SUBMIT -->
                <div class="row g-3 mb-4">
                    <div class="col-12">
                        <label class="form-label fw-bold" style="font-size: 12px; color: #525f7f;">Master Order Remarks</label>
                        <textarea name="remarks" class="form-control medi-input" rows="2">{{ $master->nota }}</textarea>
                    </div>
                </div>

                <div class="d-flex gap-2 mt-4 pt-3 border-top" style="border-color: #f4f5f7 !important;">
                    <button type="button" class="btn fw-bold px-4" style="background: #f4f5f7; color: #525f7f; border-radius: 8px; height: 42px; font-size: 13px;" onclick="history.back()">
                        Cancel Edit
                    </button>
                    <button type="submit" class="btn fw-bold px-4" style="background: #2e7d32; color: #fff; border-radius: 8px; height: 42px; font-size: 13px;">
                        <i class="bi bi-check2-circle me-1"></i> Save Changes
                    </button>
                </div>
            </form>
        </div>
    </div>
@endsection

@push('scripts')
<script>
    // Copy the same Javascript block from create.blade.php here to handle BSA calculation, Add Drug, and Add Cycle rows!
    // (Omitted here for brevity, just paste it below this line)
</script>
@endpush