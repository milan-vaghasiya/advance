<?php $this->load->view('includes/header'); ?>
<div class="page-content-tab">
	<div class="container-fluid">
        <div class="row">
            <div class="col-sm-12">
				<div class="page-title-box">
					<div class="float-end">
                        <?php
                            $addParam = "{'postData':{'id':''},'modal_id' : 'bs-right-md-modal', 'call_function':'addMachineBreakdown', 'form_id' : 'addMachineBreakdown', 'title' : 'Add Machine BreakDown'}";
                        ?>
                        <button type="button" class="btn waves-effect waves-light btn-outline-dark float-right permission-write press-add-btn" onclick="modalAction(<?=$addParam?>);"><i class="fa fa-plus"></i> Add Machine Breakdown</button>
                    </div>
                    <div class="float-start">
					    <ul class="nav nav-pills">
                            <li class="nav-item"> 
                                <button onclick="statusTab('itemTable',1);" class="nav-tab btn waves-effect waves-light btn-outline-info active" style="outline:0px" data-toggle="tab" aria-expanded="false">In Maintenance</button> 
                            </li>
                            <li class="nav-item"> 
                                <button onclick="statusTab('itemTable',2);" class="nav-tab btn waves-effect waves-light btn-outline-info" style="outline:0px" data-toggle="tab" aria-expanded="false">Completed</button> 
                            </li>
                        </ul>
					</div>
				</div>
            </div>
		</div>
        <div class="row">
            <div class="col-12">
				<div class="col-12">
					<div class="card">
                        <div class="card-body">
                            <div class="table-responsive">
                                <table id='itemTable' class="table table-bordered ssTable ssTable-cf" data-url='/getDTRows'></table>
                            </div>
                        </div>
					</div>
				</div>
            </div>
        </div>
    </div>
</div>
<?php $this->load->view('includes/footer'); ?>
