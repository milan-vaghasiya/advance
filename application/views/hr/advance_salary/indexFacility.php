<?php $this->load->view('includes/header'); ?>
<div class="page-wrapper">
    <div class="container-fluid bg-container">
        <div class="row">
            <div class="col-12">
                <div class="card">
                    <div class="card-header">
                        <div class="row">
                            <div class="col-md-6">
								<h4 class="card-title">Facility</h4>
							</div>
                            <div class="col-md-6">
                                <button type="button" class="btn waves-effect waves-light btn-outline-primary float-right addNew permission-write" data-button="both" data-fnsave="saveFacility" data-modal_id="modal-lg" data-function="addFacility" data-form_title="Add Facility"><i class="fa fa-plus"></i> Add Facility</button>
                            </div>                              
                        </div>                                         
                    </div>
                    <div class="card-body">
                        <div class="table-responsive">
                            <table id='advanceSalaryTable' class="table table-bordered ssTable" data-url="/getDTRowsForFacility/<?= $type?>"></table>
                        </div>
                    </div>
                </div>
            </div>
        </div>        
    </div>
</div>

<?php $this->load->view('includes/footer'); ?>