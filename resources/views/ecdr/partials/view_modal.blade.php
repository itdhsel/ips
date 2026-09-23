<!-- VIEW ORDER DETAIL MODAL -->
<div class="modal fade" id="viewOrderModal" tabindex="-1" aria-labelledby="viewOrderModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-lg modal-dialog-centered">
        <div class="modal-content border-0 shadow-lg" style="border-radius: 16px; overflow: hidden;">
            
            <!-- MODAL HEADER -->
            <div class="modal-header px-4 py-3" style="background: #ffffff; border-bottom: 1px solid #f4f5f7;">
                <div class="d-flex align-items-center gap-2">
                    <div style="width: 36px; height: 36px; border-radius: 10px; background: #e3efff; color: #0066ff; display: flex; align-items: center; justify-content: center;">
                        <i class="bi bi-prescription2 fs-5"></i>
                    </div>
                    <div>
                        <h6 class="modal-title fw-bold text-dark mb-0" id="viewOrderModalLabel">Cytotoxic Reconstitution Order Details</h6>
                        <span class="text-muted" style="font-size: 11px;">Order ID: <strong id="modal_order_id" style="color: #0066ff;">#--</strong></span>
                    </div>
                </div>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>

            <!-- MODAL BODY -->
            <div class="modal-body p-4" style="background: #f8f9fa;">
                
                <!-- PATIENT & DEMOGRAPHICS SUMMARY -->
                <div class="p-3 mb-3 bg-white rounded-3 shadow-sm" style="border: 1px solid #f4f5f7;">
                    <div class="d-flex justify-content-between align-items-start mb-2">
                        <div>
                            <h6 class="fw-bold text-uppercase mb-0" id="modal_patient_name" style="color: #172b4d; font-size: 15px;">-</h6>
                            <div class="text-muted" style="font-size: 12px;">MRN: <span id="modal_mrn" class="fw-bold text-dark">-</span></div>
                        </div>
                        <span id="modal_status_badge" class="medi-badge badge-stage-1 px-3 py-1">ORDER RECEIVED</span>
                    </div>
                    
                    <hr class="my-2" style="border-color: #f4f5f7;">

                    <div class="row g-2" style="font-size: 12px;">
                        <div class="col-md-3">
                            <span class="text-muted d-block" style="font-size: 11px;">Ward / Bed</span>
                            <strong id="modal_ward" style="color: #32325d;">-</strong>
                        </div>
                        <div class="col-md-3">
                            <span class="text-muted d-block" style="font-size: 11px;">Age / Gender</span>
                            <strong id="modal_age_sex" style="color: #32325d;">-</strong>
                        </div>
                        <div class="col-md-3">
                            <span class="text-muted d-block" style="font-size: 11px;">Weight / Height</span>
                            <strong id="modal_weight_height" style="color: #32325d;">-</strong>
                        </div>
                        <div class="col-md-3">
                            <span class="text-muted d-block" style="font-size: 11px;">BSA (m²)</span>
                            <strong id="modal_bsa" style="color: #0066ff;">-</strong>
                        </div>
                    </div>
                </div>

                <!-- DIAGNOSIS & REGIMEN -->
                <div class="p-3 mb-3 bg-white rounded-3 shadow-sm" style="border: 1px solid #f4f5f7;">
                    <div class="row g-2" style="font-size: 12px;">
                        <div class="col-md-6">
                            <span class="text-muted d-block" style="font-size: 11px;">Clinical Diagnosis</span>
                            <strong id="modal_diagnosis" style="color: #32325d;">-</strong>
                        </div>
                        <div class="col-md-6">
                            <span class="text-muted d-block" style="font-size: 11px;">Protocol / Regimen</span>
                            <strong id="modal_protocol" style="color: #32325d;">-</strong>
                        </div>
                    </div>
                </div>

                <!-- DRUG DETAILS TABLE -->
                <div class="medi-table-container mb-3 bg-white">
                    <div class="p-2 px-3 border-bottom fw-bold" style="font-size: 12px; color: #525f7f; background: #fafbfc;">
                        <i class="bi bi-capsule me-1 text-primary"></i> Prescribed Cytotoxic Items
                    </div>
                    <div class="table-responsive">
                        <table class="table medi-table mb-0 w-100" style="font-size: 12px;">
                            <thead>
                                <tr>
                                    <th width="40%">Drug Name</th>
                                    <th width="20%">Dose</th>
                                    <th width="20%">Diluent</th>
                                    <th width="20%">Volume</th>
                                </tr>
                            </thead>
                            <tbody id="modal_drug_list">
                                <tr>
                                    <td colspan="4" class="text-center text-muted py-3">Loading drug specifications...</td>
                                </tr>
                            </tbody>
                        </table>
                    </div>
                </div>

                <!-- REMARKS & PHYSICIAN INFO -->
                <div class="p-3 bg-white rounded-3 shadow-sm" style="border: 1px solid #f4f5f7; font-size: 12px;">
                    <div class="mb-2">
                        <span class="text-muted d-block" style="font-size: 11px;">Remarks / Special Instructions</span>
                        <div id="modal_remarks" class="fw-medium text-dark">-</div>
                    </div>
                    <div class="d-flex justify-content-between align-items-center mt-2 pt-2 border-top" style="border-color: #f4f5f7 !important;">
                        <div>
                            <span class="text-muted" style="font-size: 11px;">Ordered By:</span> 
                            <strong id="modal_orderedby" style="color: #172b4d;">-</strong>
                        </div>
                        <div>
                            <span class="text-muted" style="font-size: 11px;">Preparation Date:</span> 
                            <strong id="modal_date_use" style="color: #0066ff;">-</strong>
                        </div>
                    </div>
                </div>

            </div>

            <!-- MODAL FOOTER -->
            <div class="modal-footer px-4 py-3" style="background: #ffffff; border-top: 1px solid #f4f5f7;">
                <button type="button" class="btn fw-bold px-4" style="background: #f4f5f7; color: #525f7f; border-radius: 8px; font-size: 12px;" data-bs-dismiss="modal">Close</button>
                <button type="button" class="btn fw-bold px-4" onclick="window.print()" style="background: #0066ff; color: #ffffff; border-radius: 8px; font-size: 12px;">
                    <i class="bi bi-printer me-1"></i> Print Slip
                </button>
            </div>

        </div>
    </div>
</div>