@extends('layouts.app')

@section('title', 'New Order')
@section('page_title', 'Create New iMonitor Order')

@section('content')

    @if($errors->any())
        <div class="alert alert-danger border-0 shadow-sm mb-4" style="border-radius: 10px; background-color: #ffeaea; color: #d32f2f;">
            <ul class="mb-0 ps-3">
                @foreach ($errors->all() as $error)
                    <li>{{ $error }}</li>
                @endforeach
            </ul>
        </div>
    @endif

    @if(session('success'))
        <div class="alert alert-success border-0 shadow-sm fw-bold mb-4" style="border-radius: 10px; background-color: #e6f9ed; color: #00b341;">
            <i class="bi bi-check-circle-fill me-2"></i> {{ session('success') }}
        </div>
    @endif

    <!-- MAIN REQUEST FORM -->
    <div class="medi-card mb-4 overflow-hidden">
        
        <div class="p-4 border-bottom d-flex align-items-center justify-content-between" style="background: #ffffff; border-color: #f4f5f7 !important;">
            <div>
                <h6 class="medi-table-title mb-1"><i class="bi bi-person-plus me-2 text-primary"></i>Register Patient Order</h6>
                <div style="font-size: 12px; color: #8898aa;">Complete the patient details to add them to the live status queue</div>
            </div>
            <span class="badge" style="background: #e3efff; color: #0066ff; font-weight: 600; padding: 6px 12px; border-radius: 6px;">General Pharmacy</span>
        </div>

        <div class="p-4 bg-white">
            <form action="{{ route('monitor.store') }}" method="POST">
                @csrf
                <div class="row g-4">
                    
                    <!-- ROW 1: TIME, MRN, NAME -->
                    <div class="col-md-3">
                        <label class="form-label fw-bold" style="font-size: 12px; color: #525f7f;">Time Ordered (HH:MM) <span class="text-danger">*</span></label>
                        <input type="time" name="time" class="form-control medi-input fw-bold" value="{{ date('H:i') }}" required>
                    </div>

                    <div class="col-md-3">
                        <label class="form-label fw-bold" style="font-size: 12px; color: #525f7f;">MRN <span class="text-danger">*</span></label>
                        <div class="input-group">
                            <input type="text" id="mrn_search" name="mrn" class="form-control medi-input text-uppercase border-end-0" placeholder="e.g. 123456" required autofocus>
                            <button type="button" class="btn border-start-0" id="btnSearchMrn" style="background: #f8f9fa; border: 1px solid #e9ecef; color: #0066ff;" title="Search MRN">
                                <i class="bi bi-search"></i>
                            </button>
                        </div>
                    </div>
                    
                    <div class="col-md-6">
                        <label class="form-label fw-bold" style="font-size: 12px; color: #525f7f;">Patient Name <span class="text-danger">*</span></label>
                        <input type="text" id="patient_name" name="patient_name" class="form-control medi-input text-uppercase" placeholder="Full Patient Name" required>
                    </div>

                    <!-- ROW 2: WARD, ITEMS, QUANTITY -->
                    <div class="col-md-4">
                        <label class="form-label fw-bold" style="font-size: 12px; color: #525f7f;">Ward <span class="text-danger">*</span></label>
                        <select name="ward" class="form-select medi-input" required>
                            <option value="">-- Select Ward --</option>
                            @php
                                $wards = ["2C","4A","4B","4C","4D","5A","5B","5C","5D","6A","6B","6C","6D","7A","7B","7C","7D","8A","8B","8C","8D","9A","9B","9C","9D","10A","10B","10C","10D","11B","11C","NICU","HDW","BURN UNIT","LABOUR ROOM","ICU","ED","OTHERS"];
                            @endphp
                            @foreach ($wards as $w)
                                <option value="{{ $w }}">{{ $w }}</option>
                            @endforeach
                        </select>
                    </div>

                    <div class="col-md-4">
                        <label class="form-label fw-bold" style="font-size: 12px; color: #525f7f;">Total Items <span class="text-danger">*</span></label>
                        <input type="number" name="total_item" class="form-control medi-input" placeholder="0" value="1" required min="1">
                    </div>
                    
                    <div class="col-md-4">
                        <label class="form-label fw-bold" style="font-size: 12px; color: #525f7f;">Quantity Supplied <span class="text-danger">*</span></label>
                        <select name="total_item2" class="form-select medi-input" required>
                            <option value="1 WEEK" selected>1 WEEK</option>
                            <option value="2 WEEKS">2 WEEKS</option>
                            <option value="3 WEEKS">3 WEEKS</option>
                            <option value="1 MONTH">1 MONTH</option>
                            <option value="2 MONTHS">2 MONTHS</option>
                            <option value="3 MONTHS">3 MONTHS</option>
                            <option value="1/52">1/52</option>
                            <option value="2/52">2/52</option>
                            <option value="1/12">1/12</option>
                            <option value="2/12">2/12</option>
                            <option value="STAT">STAT</option>
                            <option value="PRN">PRN</option>
                            <option value="N/A">N/A</option>
                        </select>
                    </div>

                    <!-- ROW 3: STATUS & REMARKS -->
                    <div class="col-md-4">
                        <label class="form-label fw-bold" style="font-size: 12px; color: #525f7f;">Status <span class="text-danger">*</span></label>
                        <select name="status" class="form-select medi-input" required>
                            <option value="ORDER RECEIVED">ORDER RECEIVED</option>
                            <option value="PROCESSING" selected>PROCESSING</option>
                            <option value="READY FOR COLLECTION">READY FOR COLLECTION</option>
                        </select>
                    </div>

                    <div class="col-md-8">
                        <label class="form-label fw-bold" style="font-size: 12px; color: #525f7f;">Remarks</label>
                        <input type="text" name="remarks" class="form-control medi-input text-uppercase" placeholder="Any specific requirements or notes...">
                    </div>
                </div>

                <div class="mt-4 small fw-bold text-muted">
                    <span class="text-danger">*</span> indicates mandatory fields.
                </div>

                <!-- ACTIONS -->
                <div class="d-flex gap-2 mt-4 pt-3 border-top" style="border-color: #f4f5f7 !important;">
                    <button type="submit" class="btn fw-bold px-4 shadow-sm" style="background: #0066ff; color: #fff; border-radius: 8px; height: 42px; font-size: 13px;">
                        <i class="bi bi-check2-circle me-1"></i> Submit New Order
                    </button>
                    <button type="reset" class="btn fw-bold px-4 shadow-sm" style="background: #f4f5f7; color: #525f7f; border-radius: 8px; height: 42px; font-size: 13px;">
                        Reset Form
                    </button>
                </div>
            </form>
        </div>
    </div>

@endsection

@push('scripts')
<script>
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
        
        fetch(`/api/search-mrn?mrn=${encodeURIComponent(mrn)}`)
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