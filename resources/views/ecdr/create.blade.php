@extends('layouts.app')

@section('content')
<div class="container-fluid py-4">
    <h3 class="mb-4 text-dark fw-bold">Place New eCDR Order</h3>

    <div class="card shadow-sm border-0">
        <div class="card-header bg-primary text-white text-uppercase fw-bold">
            <i class="bi bi-prescription2 me-2"></i> New Cytotoxic Drug Reconstitution Order
        </div>
        <div class="card-body p-4">
            <!-- Instruction Banner -->
            <div class="alert border-info bg-light text-dark mb-4 shadow-sm" style="border-left: 5px solid #0dcaf0;">
                <h6 class="fw-bold text-info text-uppercase mb-2">Cytotoxic Drug Reconstitution Service</h6>
                <ul class="mb-0 small text-secondary">
                    <li>Cytotoxic drugs will be reconstituted on <strong>Monday</strong> and <strong>Wednesday</strong>.</li>
                    <li>Order form has to be completed at least one day before preparation day (before 3:00 pm).</li>
                    <li>For any emergency request, please contact the pharmacist at <strong>ext 2043</strong> and the order form should be sent before 10:00 am.</li>
                </ul>
            </div>

            <!-- Main Form -->
            <form action="{{ route('ecdr.store') }}" method="POST">
                @csrf
                <!-- 1. Patient Demographics -->
                <h6 class="fw-bold text-primary mb-3 border-bottom pb-2">1. Patient & Regimen Demographics</h6>
                <div class="row g-3 mb-4">
                    <div class="col-md-3">
                        <label class="fw-bold small mb-1">MRN <span class="text-danger">*</span></label>
                        <div class="input-group">
                            <input type="text" name="mrn" id="mrn" class="form-control" placeholder="ENTER MRN..." required>
                            <button class="btn btn-outline-secondary" type="button" id="btnSearchMrn">🔍</button>
                        </div>
                    </div>
                    <div class="col-md-6">
                        <label class="fw-bold small mb-1">Patient Name <span class="text-danger">*</span></label>
                        <input type="text" name="patient_name" id="patient_name" class="form-control" placeholder="ENTER FULL NAME..." required>
                    </div>
                    <div class="col-md-3">
                        <label class="fw-bold small mb-1">Ward <span class="text-danger">*</span></label>
                        <select name="ward" id="ward" class="form-select" required>
                            <option value="">-- Select Ward --</option>
                            @foreach($wards ?? [] as $w)
                                <option value="{{ $w }}">{{ $w }}</option>
                            @endforeach
                        </select>
                    </div>
                    
                    <div class="col-md-2">
                        <label class="fw-bold small mb-1">Age</label>
                        <input type="text" name="age" id="age" class="form-control" placeholder="Years">
                    </div>
                    <div class="col-md-2">
                        <label class="fw-bold small mb-1">Gender</label>
                        <select name="sex" id="sex" class="form-select">
                            <option value="">Select</option>
                            <option value="Male">Male</option>
                            <option value="Female">Female</option>
                        </select>
                    </div>
                    <div class="col-md-2">
                        <label class="fw-bold small mb-1">Weight (kg)</label>
                        <input type="number" step="0.01" name="weight" id="ecdr_weight" class="form-control" placeholder="kg">
                    </div>
                    <div class="col-md-3">
                        <label class="fw-bold small mb-1">Height (cm)</label>
                        <input type="number" step="0.01" name="height" id="ecdr_height" class="form-control" placeholder="cm">
                    </div>
                    <div class="col-md-3">
                        <label class="fw-bold small mb-1">BSA (m²)</label>
                        <input type="text" name="bsa" id="ecdr_bsa" class="form-control" placeholder="e.g. 1.73" readonly>
                        <small class="text-muted" style="font-size: 0.7em;">Auto-calculated</small>
                    </div>

                    <div class="col-md-6">
                        <label class="fw-bold small mb-1">Diagnosis</label>
                        <input type="text" name="diagnosis" id="diagnosis" class="form-control" placeholder="Diagnosis...">
                    </div>
                    <div class="col-md-6">
                        <label class="fw-bold small mb-1">Protocol / Regimen</label>
                        <input type="text" name="protocol" id="protocol" class="form-control" placeholder="e.g. FOLFOX / AC-T">
                    </div>
                </div>

                <!-- 2. Multiple Cycles / Start Dates -->
                <h6 class="fw-bold text-primary mb-3 border-bottom pb-2 mt-4">2. Order Details (Treatment Cycles)</h6>
                <div class="table-responsive mb-4">
                    <table class="table table-bordered table-sm" id="cycleTable">
                        <thead class="table-light">
                            <tr>
                                <th width="15%">Cycle</th>
                                <th>Start Date <span class="text-danger">*</span></th>
                                <th width="10%" class="text-center">Action</th>
                            </tr>
                        </thead>
                        <tbody id="cycleBody">
                            <tr>
                                <td class="align-middle fw-bold cycle-number">CYCLE 1</td>
                                <td><input type="date" name="date_use[]" class="form-control" required></td>
                                <td class="text-center align-middle">
                                    <button type="button" class="btn btn-sm btn-danger btn-delete-cycle" disabled>Delete</button>
                                </td>
                            </tr>
                        </tbody>
                    </table>
                    <button type="button" class="btn btn-sm btn-secondary" id="btnAddCycle">+ Add Cycle</button>
                </div>

                <!-- 3. Multiple Drug Entries -->
                <h6 class="fw-bold text-primary mb-3 border-bottom pb-2 mt-4">3. Cytotoxic Drug Details</h6>
                <div class="table-responsive mb-4">
                    <table class="table table-bordered table-sm" id="drugTable">
                        <thead class="table-light">
                            <tr>
                                <th width="5%">#</th>
                                <th>Drug Name <span class="text-danger">*</span></th>
                                <th>Dose <span class="text-danger">*</span></th>
                                <th>Diluent</th>
                                <th>Volume</th>
                                <th width="10%" class="text-center">Action</th>
                            </tr>
                        </thead>
                        <tbody id="drugBody">
                            <tr>
                                <td class="align-middle fw-bold drug-number">1</td>
                                <td><input type="text" name="drug_name[]" class="form-control" placeholder="E.G. PACLITAXEL" required></td>
                                <td><input type="text" name="dose[]" class="form-control" placeholder="e.g. 175 mg/m2" required></td>
                                <td><input type="text" name="diluent[]" class="form-control" placeholder="e.g. NS / D5%"></td>
                                <td><input type="text" name="volume[]" class="form-control" placeholder="e.g. 500 mL"></td>
                                <td class="text-center align-middle">
                                    <button type="button" class="btn btn-sm btn-danger btn-delete-drug" disabled>Delete</button>
                                </td>
                            </tr>
                        </tbody>
                    </table>
                    <button type="button" class="btn btn-sm btn-secondary" id="btnAddDrug">+ Add Drug</button>
                </div>

                <!-- Remarks -->
                <div class="row g-3 mb-4">
                    <div class="col-12">
                        <label class="fw-bold small mb-1">Remarks / Special Instructions</label>
                        <textarea name="remarks" class="form-control" rows="2" placeholder="Enter remarks..."></textarea>
                    </div>
                </div>

                <!-- 4. Physician Details -->
                <h6 class="fw-bold text-primary mb-3 border-bottom pb-2 mt-4">4. Physician Details</h6>
                <div class="row g-3 mb-4">
                    <div class="col-md-6">
                        <label class="fw-bold small mb-1">Physician</label>
                        <input type="text" name="orderedby" class="form-control bg-light" value="{{ auth()->user()->name ?? 'N/A' }}" readonly>
                    </div>
                    <div class="col-md-6">
                        <label class="fw-bold small mb-1">Designation</label>
                        <input type="text" name="orderbydetails" class="form-control bg-light" value="{{ auth()->user()->jawatan ?? auth()->user()->role ?? 'Pegawai Perubatan' }}" readonly>
                    </div>
                </div>

                <div class="text-end">
                    <button type="reset" class="btn btn-outline-secondary me-2">Reset</button>
                    <button type="submit" class="btn btn-success fw-bold">Submit CDR Order</button>
                </div>
            </form>
        </div>
    </div>
</div>
@endsection

@push('scripts')
<script>
    document.addEventListener('DOMContentLoaded', function () {
        // --- MRN Search Fetch Logic ---
        const btnSearchMrn = document.getElementById('btnSearchMrn');
        const mrnInput = document.getElementById('mrn');

        btnSearchMrn?.addEventListener('click', function () {
            const mrn = mrnInput.value.trim();
            if (!mrn) {
                alert('Please enter an MRN first.');
                return;
            }

            btnSearchMrn.disabled = true;
            btnSearchMrn.innerHTML = '⏳';

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
                    btnSearchMrn.innerHTML = '🔍';
                });
        });

        // --- BSA Auto-Calculation ---
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

        // --- Dynamic Table Logic for Cycles ---
        const cycleBody = document.getElementById('cycleBody');
        const btnAddCycle = document.getElementById('btnAddCycle');

        btnAddCycle?.addEventListener('click', function() {
            const rowCount = cycleBody.querySelectorAll('tr').length + 1;
            const newRow = document.createElement('tr');
            newRow.innerHTML = `
                <td class="align-middle fw-bold cycle-number">CYCLE ${rowCount}</td>
                <td><input type="date" name="date_use[]" class="form-control" required></td>
                <td class="text-center align-middle">
                    <button type="button" class="btn btn-sm btn-danger btn-delete-cycle">Delete</button>
                </td>
            `;
            cycleBody.appendChild(newRow);
            updateCycleButtons();
        });

        cycleBody?.addEventListener('click', function(e) {
            if (e.target.classList.contains('btn-delete-cycle')) {
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
            rows.forEach(row => row.querySelector('.btn-delete-cycle').disabled = rows.length === 1);
        }

        // --- Dynamic Table Logic for Drugs ---
        const drugBody = document.getElementById('drugBody');
        const btnAddDrug = document.getElementById('btnAddDrug');

        btnAddDrug?.addEventListener('click', function() {
            const rowCount = drugBody.querySelectorAll('tr').length + 1;
            const newRow = document.createElement('tr');
            newRow.innerHTML = `
                <td class="align-middle fw-bold drug-number">${rowCount}</td>
                <td><input type="text" name="drug_name[]" class="form-control" placeholder="E.G. PACLITAXEL" required></td>
                <td><input type="text" name="dose[]" class="form-control" placeholder="e.g. 175 mg/m2" required></td>
                <td><input type="text" name="diluent[]" class="form-control" placeholder="e.g. NS / D5%"></td>
                <td><input type="text" name="volume[]" class="form-control" placeholder="e.g. 500 mL"></td>
                <td class="text-center align-middle">
                    <button type="button" class="btn btn-sm btn-danger btn-delete-drug">Delete</button>
                </td>
            `;
            drugBody.appendChild(newRow);
            updateDrugButtons();
        });

        drugBody?.addEventListener('click', function(e) {
            if (e.target.classList.contains('btn-delete-drug')) {
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
            rows.forEach(row => row.querySelector('.btn-delete-drug').disabled = rows.length === 1);
        }
    });
</script>
@endpush