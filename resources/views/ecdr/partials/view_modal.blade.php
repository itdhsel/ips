<!-- View Order Modal -->
<div class="modal fade" id="viewOrderModal" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog modal-lg">
        <div class="modal-content">
            <div class="modal-header bg-primary text-white">
                <h5 class="modal-title">Order Details</h5>
                <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body">
                <div id="modalLoading" class="text-center py-4">
                    <div class="spinner-border text-primary" role="status"></div>
                    <p class="mt-2">Loading details...</p>
                </div>
                <div id="modalContent" style="display: none;">
                    <h6 class="fw-bold border-bottom pb-1">Patient Details</h6>
                    <div class="row mb-3 small">
                        <div class="col-md-6"><strong>Name:</strong> <span id="mdl_name"></span></div>
                        <div class="col-md-6"><strong>MRN:</strong> <span id="mdl_mrn"></span></div>
                        <div class="col-md-6"><strong>Ward:</strong> <span id="mdl_ward"></span></div>
                        <div class="col-md-6"><strong>Protocol:</strong> <span id="mdl_protocol"></span></div>
                        <div class="col-md-4"><strong>Height:</strong> <span id="mdl_height"></span> cm</div>
                        <div class="col-md-4"><strong>Weight:</strong> <span id="mdl_weight"></span> kg</div>
                        <div class="col-md-4"><strong>BSA:</strong> <span id="mdl_bsa"></span> m²</div>
                    </div>

                    <h6 class="fw-bold border-bottom pb-1">Treatment Cycles</h6>
                    <ul id="mdl_cycles" class="mb-3 small text-primary fw-bold"></ul>

                    <h6 class="fw-bold border-bottom pb-1">Cytotoxic Drugs</h6>
                    <div class="table-responsive">
                        <table class="table table-sm table-bordered small">
                            <thead class="table-light">
                                <tr><th>Drug</th><th>Dose</th><th>Diluent</th><th>Volume</th></tr>
                            </thead>
                            <tbody id="mdl_drugs"></tbody>
                        </table>
                    </div>
                    
                    <div class="mt-2 small">
                        <strong>Remarks:</strong> <span id="mdl_remarks"></span>
                    </div>
                </div>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Close</button>
            </div>
        </div>
    </div>
</div>

<script src="https://code.jquery.com/jquery-3.7.1.min.js"></script>
<script src="https://cdn.datatables.net/1.13.6/js/jquery.dataTables.min.js"></script>
<script src="https://cdn.datatables.net/1.13.6/js/dataTables.bootstrap5.min.js"></script>
<script>
    document.addEventListener('DOMContentLoaded', function () {
        // Initialize DataTables
        if (typeof $!== 'undefined') {$('.table-striped').DataTable({
                "pageLength": 10,
                "ordering": false,
                "language": {
                    "emptyTable": "There is currently no DATA on order list."
                }
            });
        }

        // Modal Fetch Logic
        const viewModal = new bootstrap.Modal(document.getElementById('viewOrderModal'));
        const viewButtons = document.querySelectorAll('.btn-view-order');

        viewButtons.forEach(btn => {
            btn.addEventListener('click', function() {
                const orderId = this.getAttribute('data-id');
                
                document.getElementById('modalLoading').style.display = 'block';
                document.getElementById('modalContent').style.display = 'none';
                viewModal.show();

                fetch(`/ecdr/show/${orderId}`)
                    .then(response => response.json())
                    .then(data => {
                        if(data.success) {
                            document.getElementById('mdl_name').textContent = data.master.name;
                            document.getElementById('mdl_mrn').textContent = data.master.mrn;
                            document.getElementById('mdl_ward').textContent = data.master.ward;
                            document.getElementById('mdl_protocol').textContent = data.master.protocol;
                            document.getElementById('mdl_height').textContent = data.master.height;
                            document.getElementById('mdl_weight').textContent = data.master.weight;
                            document.getElementById('mdl_bsa').textContent = data.master.bsa;
                            document.getElementById('mdl_remarks').textContent = data.master.nota;

                            const cyclesList = document.getElementById('mdl_cycles');
                            cyclesList.innerHTML = '';
                            data.cycles.forEach(cycle => {
                                cyclesList.innerHTML += `<li>Date Use: ${cycle.date_use} (${cycle.orderstatus})</li>`;
                            });

                            const drugsBody = document.getElementById('mdl_drugs');
                            drugsBody.innerHTML = '';
                            data.drugs.forEach(drug => {
                                drugsBody.innerHTML += `<tr><td>${drug.drug_name}</td><td>${drug.dose}</td><td>${drug.diluent}</td><td>${drug.volume}</td></tr>`;
                            });

                            document.getElementById('modalLoading').style.display = 'none';
                            document.getElementById('modalContent').style.display = 'block';
                        } else {
                            alert('Failed to load order details.');
                            viewModal.hide();
                        }
                    })
                    .catch(err => {
                        console.error(err);
                        alert('Network error loading order.');
                        viewModal.hide();
                    });
            });
        });
    });
</script>