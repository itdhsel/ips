@extends('layouts.app')

@section('title', 'Counselling Request')
@section('page_title', 'Pharmacy Counselling Request')

@section('content')

    @if(session('success'))
        <div class="alert alert-success border-0 shadow-sm fw-bold mb-4" style="border-radius: 10px; background-color: #e6f9ed; color: #00b341;">
            <i class="bi bi-check-circle-fill me-2"></i> {{ session('success') }}
        </div>
    @endif
    @if($errors->any())
        <div class="alert alert-danger border-0 shadow-sm mb-4" style="border-radius: 10px; background-color: #ffeaea; color: #d32f2f;">
            <ul class="mb-0 ps-3">
                @foreach($errors->all() as $error) <li>{{$error }}</li> @endforeach
            </ul>
        </div>
    @endif

    <!-- GENTLE REMINDER CARD -->
    <div class="medi-card p-4 mb-4" style="border-left: 4px solid #f57c00 !important; background: #fffcf8;">
        <div class="d-flex align-items-center gap-2 mb-2" style="color: #f57c00;">
            <i class="bi bi-exclamation-triangle-fill fs-5"></i>
            <h6 class="fw-bold mb-0">Counselling Schedule Reminders</h6>
        </div>
        <ul class="mb-0 small text-muted ps-4" style="line-height: 1.6;">
            <li>Referrals submitted after <strong>4:00 PM</strong> will be counselled on the next working day.</li>
            <li>Referrals on <strong>Saturday & Sunday</strong> will be counselled on the next working day.</li>
            <li>No counselling on public holidays. For urgent referrals, contact Pharmacy at <strong>Ext. 2069 / 2140</strong>.</li>
        </ul>
    </div>

    <!-- MAIN REQUEST FORM -->
    <div class="medi-card mb-4 overflow-hidden">
        <div class="p-4 border-bottom d-flex align-items-center justify-content-between" style="background: #ffffff; border-color: #f4f5f7 !important;">
            <div>
                <h6 class="medi-table-title mb-1"><i class="bi bi-file-earmark-medical me-2 text-primary"></i>Pharmacy Counselling Request Form</h6>
                <div style="font-size: 12px; color: #8898aa;">Complete the patient details and medication order below</div>
            </div>
            <span class="badge" style="background: #e3efff; color: #0066ff; font-weight: 600; padding: 6px 12px; border-radius: 6px;">New Referral</span>
        </div>

        <div class="p-4 bg-white">
            <form action="{{ route('counselling.store') }}" method="POST" id="counsellingForm">
                @csrf
                <div class="row g-4">
                    
                    <!-- PATIENT IDENTIFICATION -->
                    <div class="col-md-4">
                        <label class="form-label fw-bold" style="font-size: 12px; color: #525f7f;">MRN <span class="text-danger">*</span></label>
                        <div class="input-group">
                            <input type="text" id="mrn_search" name="mrn" class="form-control medi-input text-uppercase border-end-0" placeholder="e.g. 123456" required>
                            <button type="button" class="btn border-start-0" id="btnSearchMrn" style="background: #f8f9fa; border: 1px solid #e9ecef; color: #0066ff;" title="Search MRN">
                                <i class="bi bi-search"></i>
                            </button>
                        </div>
                    </div>
                    <div class="col-md-8">
                        <label class="form-label fw-bold" style="font-size: 12px; color: #525f7f;">Patient Name <span class="text-danger">*</span></label>
                        <input type="text" id="patient_name" name="patient_name" class="form-control medi-input text-uppercase" placeholder="Full Patient Name" required>
                    </div>

                    <!-- LOCATION DETAILS -->
                    <div class="col-md-6">
                        <label class="form-label fw-bold" style="font-size: 12px; color: #525f7f;">Ward / Unit <span class="text-danger">*</span></label>
                        <select name="ward" class="form-select medi-input" required>
                            <option value="">-- Select Ward --</option>
                            @php
                                $wards = ["2C","4A","4B","4C","4D","5A","5B","5C","5D","6A","6B","6C","6D","7A","7B","7C","7D","8A","8B","8C","8D","9A","9B","9C","9D","10A","10B","10C","10D","11B","11C","NICU","HDW","BURN UNIT","LABOUR ROOM","ICU","ED","OTHERS"];
                            @endphp
                            @foreach($wards as $w)
                                <option value="{{ $w }}">{{ $w }}</option>
                            @endforeach
                        </select>
                    </div>
                    <div class="col-md-6">
                        <label class="form-label fw-bold" style="font-size: 12px; color: #525f7f;">Bed No <span class="text-danger">*</span></label>
                        <input type="text" name="bed" class="form-control medi-input" placeholder="e.g. Bed 12A" required>
                    </div>

                    <!-- MEDICATION ORDER BUILDER -->
                    <div class="col-12 mt-4">
                        <div class="p-3 border rounded-3" style="background: #f8f9fa; border-color: #e9ecef !important;">
                            <label class="fw-bold mb-3 d-flex align-items-center gap-2" style="font-size: 13px; color: #172b4d;">
                                <i class="bi bi-capsule text-primary"></i> Counselling Order
                            </label>
                            
                            <div class="row g-2 align-items-end mb-3">
                                <div class="col-md-6">
                                    <label class="form-label mb-1" style="font-size: 11px; color: #8898aa;">Select Medication:</label>
                                    <select id="medSelect" class="form-select medi-input">
                                        <option value="">-- Choose Item --</option>
                                        <optgroup label="-- INHALER --">
                                            <option value="MDI Salbutamol 100mcg">MDI Salbutamol 100mcg</option>
                                            <option value="MDI Budesonide 200mcg">MDI Budesonide 200mcg</option>
                                            <option value="MDI Ipratropium/Fenoterol 20/50mcg (BEROUAL)">MDI Ipratropium/Fenoterol 20/50mcg (BEROUAL)</option>
                                            <option value="MDI Fluticasone 125mcg (FLIXOTIDE)">MDI Fluticasone 125mcg (FLIXOTIDE)</option>
                                            <option value="MDI Beclomethasone 100mcg">MDI Beclomethasone 100mcg</option>
                                            <option value="Easyhaler Budesonide 200mcg">Easyhaler Budesonide 200mcg</option>
                                            <option value="Easyhaler Salbutamol 200mcg">Easyhaler Salbutamol 200mcg</option>
                                            <option value="MDI Beclomethasone Dipropionate /Formoterol Fumarate Dehydrate 100/6mcg (FOSTER®)">MDI Beclomethasone Dipropionate /Formoterol Fumarate Dehydrate 100/6mcg (FOSTER®)</option>
                                            <option value="Turbuhaler Budesonide/Formoterol 160/4.5mcg (SYMBICORT®)">Turbuhaler Budesonide/Formoterol 160/4.5mcg (SYMBICORT®)</option>
                                            <option value="Turbuhaler Budesonide/Formoterol 320/9mcg (SYMBICORT®)">Turbuhaler Budesonide/Formoterol 320/9mcg (SYMBICORT®)</option>
                                            <option value="Tiotropium 2.5mcg (SPIOLTO RESPIMAT)">Tiotropium 2.5mcg (SPIOLTO RESPIMAT)</option>
                                            <option value="Olodaterol 2.5mcg (SPIOLTO RESPIMAT)">Olodaterol 2.5mcg (SPIOLTO RESPIMAT)</option>
                                            <option value="Salmeterol /Fluticasone 50/250mcg (SERETIDE ACCUHALER)">Salmeterol /Fluticasone 50/250mcg (SERETIDE ACCUHALER)</option>
                                            <option value="Salmeterol /Fluticasone 25/125mcg (SERETIDE EVOHALER)">Salmeterol /Fluticasone 25/125mcg (SERETIDE EVOHALER)</option>
                                            <option value="Tiotoprium 18mcg capsule (SPIRIVA HANDIHALER)">Tiotoprium 18mcg capsule (SPIRIVA HANDIHALER)</option>
                                            <option value="Tiotropium2.5mcg solution for inhalation (RESPIMAT)">Tiotropium2.5mcg solution for inhalation (RESPIMAT)</option>
                                            <option value="Indacaterol maleate 110mcg Inhalation Powder (ULTIBRO BREEZHALER)">Indacaterol maleate 110mcg Inhalation Powder (ULTIBRO BREEZHALER)</option>
                                            <option value="Glycopyrronium bromide 50mcg Inhalation Powder (ULTIBRO BREEZHALER)">Glycopyrronium bromide 50mcg Inhalation Powder (ULTIBRO BREEZHALER)</option>
                                        </optgroup>
                                        <optgroup label="-- INSULIN --">
                                            <option value="Short-acting Insulin (NOVOPEN)">Short-acting Insulin (NOVOPEN)</option>
                                            <option value="Intermediate-acting Insulin (NOVOPEN)">Intermediate-acting Insulin (NOVOPEN)</option>
                                            <option value="Premixed 30/70 Insulin (NOVOPEN)">Premixed 30/70 Insulin (NOVOPEN)</option>
                                            <option value="Short-acting Insulin (INSUPEN)">Short-acting Insulin (INSUPEN)</option>
                                            <option value="Intermediate-acting Insulin (INSUPEN)">Intermediate-acting Insulin (INSUPEN)</option>
                                            <option value="Premixed 30/70 Insulin (INSUPEN)">Premixed 30/70 Insulin (INSUPEN)</option>
                                        </optgroup>
                                        <optgroup label="-- ANTICOAGULANT --">
                                            <option value="Warfarin">Warfarin</option>
                                            <option value="Dabigatran">Dabigatran</option>
                                            <option value="Enoxaparin (CLEXANE)">Enoxaparin (CLEXANE)</option>
                                            <option value="Fondaparinux (ARIXTRA)">Fondaparinux (ARIXTRA)</option>
                                            <option value="Heparin">Heparin</option>
                                            <option value="Apixaban (ELIQUIS)">Apixaban (ELIQUIS)</option>
                                            <option value="Rivaroxaban (ZARELTO)">Rivaroxaban (ZARELTO)</option>
                                        </optgroup>
                                        <optgroup label="-- OTHERS --">
                                            <option value="Mometasone 50mcg Nasal Spray (NASONEX)">Mometasone 50mcg Nasal Spray (NASONEX)</option>
                                            <option value="Oxymetazoline 0.025% Nasal Spray (OXYNASE)">Oxymetazoline 0.025% Nasal Spray (OXYNASE)</option>
                                            <option value="Oxymetazoline 0.05% Nasal Spray (OXYNASE)">Oxymetazoline 0.05% Nasal Spray (OXYNASE)</option>
                                            <option value="Budesonide 64mcg Nasal Spray">Budesonide 64mcg Nasal Spray</option>
                                            <option value="Tuberculosis (TB) medications">Tuberculosis (TB) medications</option>
                                            <option value="ANALOGUE (KINDLY SPECIFY)">ANALOGUE (KINDLY SPECIFY)</option>
                                        </optgroup>
                                    </select>
                                </div>
                                <div class="col-md-4">
                                    <label class="form-label mb-1" style="font-size: 11px; color: #8898aa;">Dosage & Frequency: <span class="text-danger">*</span></label>
                                    <input type="text" id="dosInput" class="form-control medi-input" placeholder="e.g. 2 puffs BD">
                                </div>
                                <div class="col-md-2">
                                    <button type="button" class="btn w-100 fw-bold" style="background: #e3efff; color: #0066ff; border-radius: 8px; height: 38px; font-size: 12px;" onclick="addMedication()">
                                        <i class="bi bi-plus-lg me-1"></i> Add Item
                                    </button>
                                </div>
                            </div>

                            <textarea name="consult_info" id="txtConsult" rows="4" class="form-control medi-input" placeholder="Selected medications will accumulate here..." required></textarea>
                        </div>
                    </div>

                    <!-- USER STATUS & REQUESTS -->
                    <div class="col-md-12">
                        <label class="form-label fw-bold d-block mb-2" style="font-size: 12px; color: #525f7f;">Medication Status: <span class="text-danger">*</span></label>
                        <div class="d-flex gap-4">
                            <div class="form-check">
                                <input class="form-check-input" type="radio" name="medstatus" id="medFirst" value="First time user" required style="cursor: pointer;">
                                <label class="form-check-label fw-medium" for="medFirst" style="font-size: 13px; color: #32325d; cursor: pointer;">First time user</label>
                            </div>
                            <div class="form-check">
                                <input class="form-check-input" type="radio" name="medstatus" id="medReassess" value="Re-assessment" required style="cursor: pointer;">
                                <label class="form-check-label fw-medium" for="medReassess" style="font-size: 13px; color: #32325d; cursor: pointer;">Re-assessment</label>
                            </div>
                        </div>
                    </div>

                    <!-- SPECIAL REQUEST -->
                    <div class="col-12">
                        <label class="form-label fw-bold" style="font-size: 12px; color: #525f7f;">Special Request (Optional)</label>
                        <input type="text" name="special_request" class="form-control medi-input" placeholder="Any specific requirements or notes for the pharmacist...">
                    </div>

                    <!-- REQUESTING DOCTOR -->
                    <div class="col-md-6">
                        <label class="form-label fw-bold" style="font-size: 12px; color: #525f7f;">Requesting Doctor <span class="text-danger">*</span></label>
                        <input type="text" name="doc" class="form-control medi-input fw-bold" style="background-color: #f4f5f7; color: #172b4d; cursor: not-allowed;" value="{{ auth()->user()->name ?? auth()->user()->login_username }}" required readonly>
                    </div>

                </div>
                
                <div class="mt-4 small fw-bold text-muted">
                    <span class="text-danger">*</span> indicates mandatory fields.
                </div>

                <div class="d-flex gap-2 mt-4 pt-3 border-top" style="border-color: #f4f5f7 !important;">
                    <button type="submit" class="btn fw-bold px-4" style="background: #0066ff; color: #fff; border-radius: 8px; height: 42px; font-size: 13px;">
                        <i class="bi bi-send me-1"></i> Send Counselling Order
                    </button>
                    <button type="reset" class="btn fw-bold px-4" style="background: #f4f5f7; color: #525f7f; border-radius: 8px; height: 42px; font-size: 13px;">
                        Reset Form
                    </button>
                </div>
            </form>
        </div>
    </div>

@endsection

@push('scripts')
<script>
    function addMedication() {
        let med = document.getElementById('medSelect').value;
        let dos = document.getElementById('dosInput').value;
        let textArea = document.getElementById('txtConsult');

        if (med === "") {
            alert("Please select a medication first.");
            return;
        }

        let newText = med + (dos ? " - " + dos : "") + "\n";
        textArea.value += newText;

        // Reset selector inputs
        document.getElementById('medSelect').value = "";
        document.getElementById('dosInput').value = "";
    }

    // MRN Search API Handler
    document.getElementById('btnSearchMrn')?.addEventListener('click', function() {
        let mrn = document.getElementById('mrn_search').value.trim();
        if (!mrn) {
            alert('Please enter an MRN to search.');
            return;
        }
        
        let btn = this;
        let originalIcon = btn.innerHTML;
        btn.innerHTML = '<span class="spinner-border spinner-border-sm" role="status" aria-hidden="true"></span>';
        
        fetch(`/api/search-counselling-mrn?mrn=${encodeURIComponent(mrn)}`)
            .then(response => response.json())
            .then(data => {
                if (data.success && data.patient_name) {
                    document.getElementById('patient_name').value = data.patient_name;
                } else {
                    alert('MRN not found in master list.');
                }
            })
            .catch(error => console.error('Error fetching MRN:', error))
            .finally(() => {
                btn.innerHTML = originalIcon;
            });
    });
</script>
@endpush