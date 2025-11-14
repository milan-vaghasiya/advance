<?php $this->load->view('includes/header'); ?>
<div class="page-content-tab">
	<div class="container-fluid">
        <div class="row">
            <div class="col-12">
                <div class="page-title-box">
                    <div class="row">
                        <div class="col-md-4">
                            <h4 class="card-title">Leave Type</h4>
                        </div>
                        <div class="col-md-8">
                        <?php
                            $addParam = "{'modal_id' : 'bs-right-md-modal', 'call_function':'addLeaveType', 'form_id' : 'addLeaveType', 'title' : 'Add Leave Type'}";
                        ?>
                        <button type="button" class="btn waves-effect waves-light btn-outline-dark permission-write float-right press-add-btn" onclick="modalAction(<?=$addParam?>);"><i class="fa fa-plus"></i> Add Leave Type</button>
                        </div>                             
                    </div>                                         
                </div>
                <div class="card">
                    <div class="card-body">
                        <div class="table-responsive">
                            <table id='leaveTypeTable' class="table table-bordered ssTable ssTable-cf" data-url="/getDTRows"></table>
                        </div>
                    </div>
                </div>
            </div>
        </div>        
    </div>
</div>

<?php $this->load->view('includes/footer'); ?>