<?php $this->load->view('includes/header'); ?>
<div class="page-content-tab">
    <div class="container-fluid bg-container">
        <div class="row">
            <div class="col-sm-12">
                <div class="page-title-box">
                    <!-- <div class="float-end">
                        <button type="button" class="btn waves-effect waves-light btn-outline-success float-right addNew permission-write" data-button="both" data-modal_id="modal-lg" data-function="addInspection" data-form_title="Add Inspection"><i class="fa fa-plus"></i> Add Inspection</button>
					</div> -->
                    <ul class="nav nav-pills">
                        <li class="nav-item"> <button onclick="statusTab('requisitionTable',0);" class="btn waves-effect waves-light btn-outline-info active mr-1" data-bs-toggle="tab" aria-expanded="false">Pending</button> </li>
                        <li class="nav-item"> <button onclick="statusTab('requisitionTable',1);" class="btn waves-effect waves-light btn-outline-info mr-1" data-bs-toggle="tab" aria-expanded="false">Complete</button> </li>
                    </ul>
                </div>
            </div>
        </div> 
        <div class="row">
            <div class="col-12">
                <div class="card">
                    <div class="card-body">
                        <div class="table-responsive">
                            <table id='requisitionTable' class="table table-bordered ssTable" data-url='/getDTRows'></table>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
<?php $this->load->view('includes/footer'); ?>