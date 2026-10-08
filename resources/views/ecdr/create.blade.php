@extends('layouts.app')

@section('title', 'Place eCDR Order')
@section('page_title', 'Place Cytotoxic Drug Order')

@section('content')

    <!-- SERVICE INSTRUCTION BANNER -->
    <div class="medi-card p-4 mb-4" style="border-left: 4px solid #0066ff !important; background: #fffcf8;">
        <div class="d-flex align-items-center gap-2 mb-2" style="color: #0066ff;">
            <i class="bi bi-info-circle-fill fs-5"></i>
            <h6 class="fw-bold mb-0">Cytotoxic Drug Reconstitution Service Guidelines</h6>
        </div>
        <ul class="mb-0 small text-muted ps-4" style="line-height: 1.6;">
            <li>Cytotoxic drugs are reconstituted on <strong>Monday</strong> and <strong>Wednesday</strong>.</li>
            <li>Order forms must be completed at least <strong>one day before preparation</strong> (before 3:00 PM).</li>
            <li>For emergency requests, contact Pharmacy at <strong>ext. 2043</strong> (order must be sent before 10:00 AM).</li>
        </ul>
    </div>

    <!-- MAIN ORDER FORM CONTAINER -->
    <div class="medi-card mb-4 overflow-hidden">
        <div class="p-4 border-bottom d-flex align-items-center justify-content-between" style="background: #ffffff; border-color: #f4f5f7 !important;">
            <div>
                <h6 class="medi-table-title mb-1">
                    <i class="bi bi-prescription2 me-2 text-primary"></i>
                    {{ isset($reorderMaster) ? 'Cytotoxic Drug Re-Order Form' : 'Cytotoxic Drug Reconstitution Order Form' }}
                </h6>
                <div style="font-size: 12px; color: #8898aa;">Complete patient demographics, treatment cycles, and medication dosage</div>
            </div>
            <span class="medi-badge badge-stage-2 px-3 py-2">eCDR Module</span>
        </div>

        <div class="p-4 bg-white">
            <form action="{{ route('ecdr.store') }}" method="POST">
                @csrf
                
                <!-- 1. PATIENT & REGIMEN DEMOGRAPHICS -->
                <div class="d-flex align-items-center gap-2 mb-3 pb-2 border-bottom" style="border-color: #f4f5f7 !important;">
                    <span class="badge rounded-circle p-2" style="background: #e3efff; color: #0066ff; width: 26px; height: 26px; display: flex; align-items: center; justify-content: center; font-size: 12px;">1</span>
                    <h6 class="fw-bold mb-0" style="color: #172b4d; font-size: 14px;">Patient & Regimen Demographics</h6>
                </div>

                <div class="row g-3 mb-4">
                    <div class="col-md-3">
                        <label class="form-label fw-bold" style="font-size: 12px; color: #525f7f;">MRN <span class="text-danger">*</span></label>
                        <div class="input-group">
                            <input type="text" name="mrn" id="mrn" class="form-control medi-input text-uppercase border-end-0" placeholder="Enter MRN..." value="{{ $reorderMaster->mrn ?? '' }}" required>
                            <button type="button" class="btn border-start-0" id="btnSearchMrn" style="background: #f8f9fa; border: 1px solid #e9ecef; color: #0066ff;" title="Search MRN">
                                <i class="bi bi-search"></i>
                            </button>
                        </div>
                    </div>
                    <div class="col-md-6">
                        <label class="form-label fw-bold" style="font-size: 12px; color: #525f7f;">Patient Name <span class="text-danger">*</span></label>
                        <input type="text" name="patient_name" id="patient_name" class="form-control medi-input text-uppercase" placeholder="Full Patient Name" value="{{ $reorderMaster->name ?? '' }}" required>
                    </div>
                    <div class="col-md-3">
                        <label class="form-label fw-bold" style="font-size: 12px; color: #525f7f;">Ward <span class="text-danger">*</span></label>
                        <select name="ward" id="ward" class="form-select medi-input" required>
                            <option value="">Select Ward</option>
                            @foreach($wards ?? [] as $w)
                                <option value="{{ $w }}" {{ (isset($reorderMaster) &&$reorderMaster->ward == $w) ? 'selected' : '' }}>{{$w }}</option>
                            @endforeach
                        </select>
                    </div>
                    
                    <div class="col-md-2">
                        <label class="form-label fw-bold" style="font-size: 12px; color: #525f7f;">Age</label>
                        <input type="text" name="age" id="age" class="form-control medi-input" placeholder="Years" value="{{ $reorderMaster->age ?? '' }}">
                    </div>
                    <div class="col-md-2">
                        <label class="form-label fw-bold" style="font-size: 12px; color: #525f7f;">Gender</label>
                        <select name="sex" id="sex" class="form-select medi-input">
                            <option value="">Select</option>
                            <option value="Male" {{ (isset($reorderMaster) &&$reorderMaster->sex == 'Male') ? 'selected' : '' }}>Male</option>
                            <option value="Female" {{ (isset($reorderMaster) &&$reorderMaster->sex == 'Female') ? 'selected' : '' }}>Female</option>
                        </select>
                    </div>
                    <div class="col-md-2">
                        <label class="form-label fw-bold" style="font-size: 12px; color: #525f7f;">Weight (kg)</label>
                        <input type="number" step="0.01" name="weight" id="ecdr_weight" class="form-control medi-input" placeholder="0.00" value="{{ $reorderMaster->weight ?? '' }}">
                    </div>
                    <div class="col-md-3">
                        <label class="form-label fw-bold" style="font-size: 12px; color: #525f7f;">Height (cm)</label>
                        <input type="number" step="0.01" name="height" id="ecdr_height" class="form-control medi-input" placeholder="0.00" value="{{ $reorderMaster->height ?? '' }}">
                    </div>
                    <div class="col-md-3">
                        <label class="form-label fw-bold" style="font-size: 12px; color: #525f7f;">BSA (m²)</label>
                        <input type="text" name="bsa" id="ecdr_bsa" class="form-control medi-input fw-bold" placeholder="Auto-calculated" style="background-color: #f4f5f7;" value="{{ $reorderMaster->bsa ?? '' }}" readonly>
                    </div>

                    <div class="col-md-6">
                        <label class="form-label fw-bold" style="font-size: 12px; color: #525f7f;">Diagnosis</label>
                        <input type="text" name="diagnosis" id="diagnosis" class="form-control medi-input" placeholder="Clinical diagnosis..." value="{{ $reorderMaster->diagnosis ?? '' }}">
                    </div>
                    <div class="col-md-6">
                        <label class="form-label fw-bold" style="font-size: 12px; color: #525f7f;">Protocol / Regimen</label>
                        <input type="text" name="protocol" id="protocol" class="form-control medi-input" placeholder="e.g. FOLFOX / AC-T" value="{{ $reorderMaster->protocol ?? '' }}">
                    </div>
                </div>

                <!-- 2. TREATMENT CYCLES -->
                <div class="d-flex align-items-center gap-2 mb-3 pb-2 border-bottom mt-5" style="border-color: #f4f5f7 !important;">
                    <span class="badge rounded-circle p-2" style="background: #e3efff; color: #0066ff; width: 26px; height: 26px; display: flex; align-items: center; justify-content: center; font-size: 12px;">2</span>
                    <h6 class="fw-bold mb-0" style="color: #172b4d; font-size: 14px;">Treatment Cycles</h6>
                </div>

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
                            <tr>
                                <td class="fw-bold align-middle cycle-number" style="color: #0066ff;">CYCLE 1</td>
                                <td><input type="date" name="date_use[]" class="form-control medi-input" required></td>
                                <td class="text-center align-middle">
                                    <button type="button" class="btn btn-sm btn-delete-cycle" style="color: #d32f2f; background: transparent;" disabled>
                                        <i class="bi bi-trash fs-6"></i>
                                    </button>
                                </td>
                            </tr>
                        </tbody>
                    </table>
                </div>
                <button type="button" class="btn fw-bold mb-4" style="background: #e3efff; color: #0066ff; border-radius: 8px; font-size: 12px;" id="btnAddCycle">
                    <i class="bi bi-plus-lg me-1"></i> Add Treatment Cycle
                </button>

                <!-- 3. CYTOTOXIC DRUG DETAILS -->
                <div class="d-flex align-items-center gap-2 mb-3 pb-2 border-bottom mt-4" style="border-color: #f4f5f7 !important;">
                    <span class="badge rounded-circle p-2" style="background: #e3efff; color: #0066ff; width: 26px; height: 26px; display: flex; align-items: center; justify-content: center; font-size: 12px;">3</span>
                    <h6 class="fw-bold mb-0" style="color: #172b4d; font-size: 14px;">Cytotoxic Drug Details</h6>
                </div>

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
                            @if(isset($reorderDrugs) &&$reorderDrugs->count() > 0)
                                <!-- RE-ORDER MODE: Generate previous drugs -->
                                @foreach($reorderDrugs as $index =>$drug)
                                <tr>
                                    <td class="fw-bold align-middle drug-number text-muted">{{ $index + 1 }}</td>
                                    <td><input type="text" name="drug_name[]" class="form-control medi-input text-uppercase" value="{{ $drug->ubat }}" required></td>
                                    <td><input type="text" name="dose[]" class="form-control medi-input" value="{{ $drug->dos }}" required></td>
                                    <td><input type="text" name="catatan[]" class="form-control medi-input" value="{{ $drug->catatan }}"></td>
                                    <td class="text-center align-middle">
                                        <button type="button" class="btn btn-sm btn-delete-drug" style="color: #d32f2f; background: transparent;" {{ $reorderDrugs->count() == 1 ? 'disabled' : '' }}>
                                            <i class="bi bi-trash fs-6"></i>
                                        </button>
                                    </td>
                                </tr>
                                @endforeach
                            @else
                                <!-- NEW ORDER MODE: Standard empty row -->
                                <tr>
                                    <td class="fw-bold align-middle drug-number text-muted">1</td>
                                    <td><input type="text" name="drug_name[]" class="form-control medi-input text-uppercase" placeholder="e.g. PACLITAXEL" required></td>
                                    <td><input type="text" name="dose[]" class="form-control medi-input" placeholder="e.g. 175 mg/m²" required></td>
                                    <td><input type="text" name="catatan[]" class="form-control medi-input" placeholder="Drug remarks..."></td>
                                    <td class="text-center align-middle">
                                        <button type="button" class="btn btn-sm btn-delete-drug" style="color: #d32f2f; background: transparent;" disabled>
                                            <i class="bi bi-trash fs-6"></i>
                                        </button>
                                    </td>
                                </tr>
                            @endif
                        </tbody>
                    </table>
                </div>
                <button type="button" class="btn fw-bold mb-4" style="background: #e3efff; color: #0066ff; border-radius: 8px; font-size: 12px;" id="btnAddDrug">
                    <i class="bi bi-plus-lg me-1"></i> Add Drug Entry
                </button>

                <!-- 4. PHYSICIAN DETAILS -->
                <div class="d-flex align-items-center gap-2 mb-3 pb-2 border-bottom mt-4" style="border-color: #f4f5f7 !important;">
                    <span class="badge rounded-circle p-2" style="background: #e3efff; color: #0066ff; width: 26px; height: 26px; display: flex; align-items: center; justify-content: center; font-size: 12px;">4</span>
                    <h6 class="fw-bold mb-0" style="color: #172b4d; font-size: 14px;">Physician Authorization</h6>
                </div>

                <div class="row g-3 mb-4">
                    <div class="col-md-6">
                        <label class="form-label fw-bold" style="font-size: 12px; color: #525f7f;">Ordering Physician</label>
                        <input type="text" name="orderedby" class="form-control medi-input fw-bold" style="background-color: #f4f5f7; color: #172b4d;" value="{{ auth()->user()->name ?? 'N/A' }}" readonly>
                    </div>
                    <div class="col-md-6">
                        <label class="form-label fw-bold" style="font-size: 12px; color: #525f7f;">Designation / Role</label>
                        <input type="text" name="orderbydetails" class="form-control medi-input fw-bold" style="background-color: #f4f5f7; color: #172b4d;" value="{{ auth()->user()->jawatan ?? auth()->user()->role ?? 'Pegawai Perubatan' }}" readonly>
                    </div>
                </div>

                <div class="d-flex gap-2 mt-4 pt-3 border-top" style="border-color: #f4f5f7 !important;">
                    <button type="submit" class="btn fw-bold px-4" style="background: #0066ff; color: #fff; border-radius: 8px; height: 42px; font-size: 13px;">
                        <i class="bi bi-check2-circle me-1"></i> Submit CDR Order
                    </button>
                    <a href="{{ route('ecdr.create') }}" class="btn fw-bold px-4 d-flex align-items-center justify-content-center" style="background: #f4f5f7; color: #525f7f; border-radius: 8px; height: 42px; font-size: 13px; text-decoration: none;">
                        Reset Form
                    </a>
                </div>
            </form>
        </div>
    </div>

@endsection

@push('scripts')
<script>
    document.addEventListener('DOMContentLoaded', function () {
        const btnSearchMrn = document.getElementById('btnSearchMrn');
        const mrnInput = document.getElementById('mrn');

        btnSearchMrn?.addEventListener('click', function () {
            const mrn = mrnInput.value.trim();
            if (!mrn) {
                alert('Please enter an MRN first.');
                return;
            }

            btnSearchMrn.disabled = true;
            btnSearchMrn.innerHTML = '<span class="spinner-border spinner-border-sm" role="status" aria-hidden="true"></span>';

            fetch(`/api/search-mrn/ecdr?mrn=${encodeURIComponent(mrn)}`)
                .then(response => response.json())
                .then(data => {
                    if (data.success && data.patient) {
                        document.getElementById('patient_name').value = data.patient.patient_name || '';
                        const wardSelect = document.getElementById('ward');
                        if (data.patient.ward) {
                            Array.from(wardSelect.options).forEach(opt => {
                                if (opt.value === data.patient.ward) opt.selected = true;
                            });
                        }
                        document.getElementById('age').value = data.patient.age || '';
                        const sexSelect = document.getElementById('sex');
                        if (data.patient.sex) {
                            Array.from(sexSelect.options).forEach(opt => {
                                if (opt.value === data.patient.sex) opt.selected = true;
                            });
                        }
                        document.getElementById('ecdr_weight').value = data.patient.weight || '';
                        document.getElementById('ecdr_height').value = data.patient.height || '';
                        document.getElementById('ecdr_bsa').value = data.patient.bsa || '';
                        document.getElementById('diagnosis').value = data.patient.diagnosis || '';
                        document.getElementById('protocol').value = data.patient.protocol || '';
                        
                        calculateBSA();
                    } else {
                        alert(data.message || 'Patient not found in eCDR database.');
                    }
                })
                .catch(error => {
                    console.error('Error fetching MRN:', error);
                    alert('Error querying MRN record.');
                })
                .finally(() => {
                    btnSearchMrn.disabled = false;
                    btnSearchMrn.innerHTML = '<i class="bi bi-search"></i>';
                });
        });

        // BSA Calculation
        const weightInput = document.getElementById('ecdr_weight');
        const heightInput = document.getElementById('ecdr_height');
        const bsaInput = document.getElementById('ecdr_bsa');

        function calculateBSA() {
            const w = parseFloat(weightInput.value);
            const h = parseFloat(heightInput.value);
            if (w > 0 && h > 0) {
                bsaInput.value = Math.sqrt((w * h) / 3600).toFixed(2);
            } else {
                bsaInput.value = '';
            }
        }

        weightInput?.addEventListener('input', calculateBSA);
        heightInput?.addEventListener('input', calculateBSA);

        // Cycle Rows
        const cycleBody = document.getElementById('cycleBody');
        const btnAddCycle = document.getElementById('btnAddCycle');

        btnAddCycle?.addEventListener('click', function() {
            const rowCount = cycleBody.querySelectorAll('tr').length + 1;
            const newRow = document.createElement('tr');
            newRow.innerHTML = `
                <td class="fw-bold align-middle cycle-number" style="color: #0066ff;">CYCLE ${rowCount}</td>
                <td><input type="date" name="date_use[]" class="form-control medi-input" required></td>
                <td class="text-center align-middle">
                    <button type="button" class="btn btn-sm btn-delete-cycle" style="color: #d32f2f; background: transparent;">
                        <i class="bi bi-trash fs-6"></i>
                    </button>
                </td>
            `;
            cycleBody.appendChild(newRow);
            updateCycleButtons();
        });

        cycleBody?.addEventListener('click', function(e) {
            if (e.target.closest('.btn-delete-cycle')) {
                e.target.closest('tr').remove();
                updateCycleNumbers();
                updateCycleButtons();
            }
        });

        function updateCycleNumbers() {
            document.querySelectorAll('.cycle-number').forEach((cell, index) => {
                cell.textContent = `CYCLE ${index + 1}`;
            });
        }
        function updateCycleButtons() {
            const rows = cycleBody.querySelectorAll('tr');
            rows.forEach(row => {
                const btn = row.querySelector('.btn-delete-cycle');
                if (btn) btn.disabled = rows.length === 1;
            });
        }

        // Drug Rows
        const drugBody = document.getElementById('drugBody');
        const btnAddDrug = document.getElementById('btnAddDrug');

        btnAddDrug?.addEventListener('click', function() {
            const rowCount = drugBody.querySelectorAll('tr').length + 1;
            const newRow = document.createElement('tr');
            newRow.innerHTML = `
                <td class="fw-bold align-middle drug-number text-muted">${rowCount}</td>
                <td><input type="text" name="drug_name[]" class="form-control medi-input text-uppercase" placeholder="e.g. PACLITAXEL" required></td>
                <td><input type="text" name="dose[]" class="form-control medi-input" placeholder="e.g. 175 mg/m²" required></td>
                <td><input type="text" name="catatan[]" class="form-control medi-input" placeholder="Drug remarks..."></td>
                <td class="text-center align-middle">
                    <button type="button" class="btn btn-sm btn-delete-drug" style="color: #d32f2f; background: transparent;">
                        <i class="bi bi-trash fs-6"></i>
                    </button>
                </td>
            `;
            drugBody.appendChild(newRow);
            updateDrugButtons();
        });

        drugBody?.addEventListener('click', function(e) {
            if (e.target.closest('.btn-delete-drug')) {
                e.target.closest('tr').remove();
                updateDrugNumbers();
                updateDrugButtons();
            }
        });

        function updateDrugNumbers() {
            document.querySelectorAll('.drug-number').forEach((cell, index) => {
                cell.textContent = index + 1;
            });
        }
        function updateDrugButtons() {
            const rows = drugBody.querySelectorAll('tr');
            rows.forEach(row => {
                const btn = row.querySelector('.btn-delete-drug');
                if (btn) btn.disabled = rows.length === 1;
            });
        }
    });
</script>
@endpush