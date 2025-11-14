<?php $this->load->view('includes/header'); ?>
<div class="page-wrapper">
    <div class="container-fluid bg-container">
        <div class="row">
            <div class="col-12">
                <div class="card">
                    <div class="card-header">
                        <div class="row">
                        <div class="col-md-4">
                            <ul class="nav nav-pills">
                                <li class="nav-item"> 
                                    <button onclick="statusTab('geTable',0);" class="nav-tab btn waves-effect waves-light btn-outline-danger active" id="pending_ge" style="outline:0px" data-toggle="tab" aria-expanded="false">Pending</button> 
                                </li>
                                <li class="nav-item"> 
                                    <button onclick="statusTab('geTable',1);" class="nav-tab btn waves-effect waves-light btn-outline-success" id="completed_ge" style="outline:0px" data-toggle="tab" aria-expanded="false">Completed</button> 
                                </li>
                            </ul>
                        </div>
                            <div class="col-md-4">
                                <h4 class="card-title">Gate Entry Register</h4>
                            </div>
                            <div class="col-md-4">
                                <button type="button" class="btn waves-effect waves-light btn-outline-primary float-right permission-write addNew press-add-btn" data-button="both" data-modal_id="modal-lg" data-function="addGateEntry" data-form_title="Add Gate Entry"><i class="fa fa-plus"></i> Add GE</button>
                            </div>                             
                        </div>                                         
                    </div>
                    <div class="card-body">
                        <div class="table-responsive">
                            <table id='geTable' class="table table-bordered ssTable" data-url='/getDTRows'></table>
                        </div>
                    </div>
                </div>
            </div>
        </div>        
    </div>
</div>
<?php $this->load->view('includes/footer'); ?>

