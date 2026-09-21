<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>iMonitor v3 - Counselling Request</title>
    <link href="{{ asset('css/bootstrap.min.css') }}" rel="stylesheet">
    <link href="{{ asset('css/style.css') }}" rel="stylesheet">
</head>
<body class="bg-light overflow-x-hidden">
    <div class="d-flex w-100 overflow-x-hidden">
        
        <!-- SIDEBAR -->
        @include('partials.sidebar')

        <!-- MAIN CONTENT -->
        <div class="main-content d-flex flex-column min-vh-100">            
            <nav class="navbar top-header px-4 py-3 d-flex justify-content-between align-items-center">
                <h4 class="mb-0 fw-bold text-dark">Pharmacy Counselling Request</h4>
                <div class="text-muted small">
                    Logged in as: <strong class="text-dark">{{ auth()->user()->login_username ?? 'Unknown' }}</strong> 
                    <span class="badge bg-primary ms-2 px-2 py-1">{{ strtoupper(auth()->user()->role ?? 'User') }}</span>
                </div>
            </nav>

            <div class="container-fluid p-4">
                
                @if(session('success'))
                    <div class="alert alert-success shadow-sm fw-bold">
                        ✅ {{ session('success') }}
                    </div>
                @endif
                @if($errors->any())
                    <div class="alert alert-danger shadow-sm">
                        <ul class="mb-0">
                            @foreach($errors->all() as $error) <li>{{ $error }}</li> @endforeach
                        </ul>
                    </div>
                @endif

                <!-- GENTLE REMINDER ALERT -->
                <div class="alert alert-warning border-warning shadow-sm">
                    <h6 class="alert-heading fw-bold text-danger">⚠️ GENTLE REMINDER!!</h6>
                    <ul class="mb-0 small text-dark">
                        <li>Counselling referral after <strong>4:00 PM</strong> will be counselled on the next working day.</li>
                        <li>Counselling referral on <strong>Saturday & Sunday</strong> will be counselled on the next working day.</li>
                        <li>No Counselling done on public holidays. For urgent referrals, please call Pharmacy at <strong>2069/2140</strong>.</li>
                    </ul>
                </div>

                <!-- REQUEST FORM -->
                <div class="card shadow border-0 rounded mt-4">
                    <div class="card-header bg-dark text-white fw-bold py-3">
                        PHARMACY COUNSELLING REQUEST FORM
                    </div>
                    <div class="card-body bg-white p-4">
                        <form action="{{ route('counselling.store') }}" method="POST" id="counsellingForm">
                            @csrf
                            <div class="row g-4">
                                
                                <!-- Patient Name & MRN -->
                                <div class="col-md-8">
                                    <label class="fw-bold mb-1">Patient Name <span class="text-danger">*</span></label>
                                    <input type="text" name="patient_name" class="form-control bg-light" required>
                                </div>
                                <div class="col-md-4">
                                    <label class="fw-bold mb-1">MRN <span class="text-danger">*</span></label>
                                    <input type="text" name="mrn" class="form-control bg-light" required>
                                </div>

                                <!-- Ward/Unit & Bed No -->
                                <div class="col-md-6">
                                    <label class="fw-bold mb-1">Ward/Unit <span class="text-danger">*</span></label>
                                    <select name="ward" class="form-select bg-light" required>
                                        <option value="">-- Select Ward --</option>
                                        @foreach($wards as $w)
                                            <option value="{{ $w }}">{{ $w }}</option>
                                        @endforeach
                                    </select>
                                </div>
                                <div class="col-md-6">
                                    <label class="fw-bold mb-1">Bed No <span class="text-danger">*</span></label>
                                    <input type="text" name="bed" class="form-control bg-light" required>
                                </div>

                                <!-- Counselling Order Section -->
                                <div class="col-12 mt-4">
                                    <div class="p-3 border rounded bg-light">
                                        <!-- 1. Changed title color to black by removing text-primary -->
                                        <label class="fw-bold mb-3 text-dark">Counselling Order</label>
                                        
                                        <div class="row g-2 align-items-end mb-3">
                                            <div class="col-md-6">
                                                <label class="small text-muted mb-1">Select medication:</label>
                                                <select id="medSelect" class="form-select border-secondary">
                                                    <option value="">Select Medication</option>
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
                                                <label class="small text-muted mb-1">Dosage: <span class="text-danger">*</span> (Please include dosage unit)</label>
                                                <input type="text" id="dosInput" class="form-control border-secondary">
                                            </div>
                                            <div class="col-md-2">
                                                <button type="button" class="btn btn-secondary w-100" onclick="addMedication()">Add Medication</button>
                                            </div>
                                        </div>

                                        <textarea name="consult_info" id="txtConsult" rows="4" class="form-control border-secondary" required></textarea>
                                    </div>
                                </div>

                                <!-- Medication Status -->
                                <div class="col-md-12">
                                    <label class="fw-bold me-3">Medication Status: <span class="text-danger">*</span></label>
                                    <div class="form-check form-check-inline">
                                        <input class="form-check-input" type="radio" name="medstatus" value="First time user" required>
                                        <label class="form-check-label">First time user</label>
                                    </div>
                                    <div class="form-check form-check-inline">
                                        <input class="form-check-input" type="radio" name="medstatus" value="Re-assessment" required>
                                        <label class="form-check-label">Re-assessment</label>
                                    </div>
                                </div>

                                <!-- Special Request -->
                                <div class="col-12">
                                    <label class="fw-bold mb-1">Special Request (Optional)</label>
                                    <input type="text" name="special_request" class="form-control bg-light">
                                </div>

                                <!-- 2. Requesting Doctor auto-filled with user's full name -->
                                <div class="col-md-6">
                                    <label class="fw-bold mb-1">Requesting Doctor <span class="text-danger">*</span></label>
                                    <input type="text" name="doc" class="form-control bg-light" value="{{ auth()->user()->name ?? auth()->user()->login_username }}" required readonly>                                </div>

                            </div>
                            
                            <div class="mt-3 small fw-bold text-muted">
                                * indicate Mandatory fields.
                            </div>

                            <hr class="my-4">
                            
                            <div class="d-flex gap-2">
                                <button type="submit" class="btn btn-success fw-bold px-4">Send Counselling Order</button>
                                <button type="reset" class="btn btn-outline-secondary px-4">Reset</button>
                            </div>
                        </form>
                    </div>
                </div>
            </div>

            <footer class="main-footer mt-auto">
                &copy; 2026 Hospital Selayang. Developed by Muhammad Haziq Zikri (ITD HSEL)
            </footer>
        </div>
    </div>

    <script src="{{ asset('js/bootstrap.bundle.min.js') }}"></script>
    
    <script>
        function addMedication() {
            let med = document.getElementById('medSelect').value;
            let dos = document.getElementById('dosInput').value;
            let textArea = document.getElementById('txtConsult');

            if (med === "") {
                alert("Please select a medication first.");
                return;
            }

            let newText = med + (dos ? " " + dos : "") + "\n";
            textArea.value += newText;

            // Reset inputs after adding
            document.getElementById('medSelect').value = "";
            document.getElementById('dosInput').value = "";
        }
    </script>
</body>
</html>