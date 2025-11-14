<?php $this->load->view('includes/header'); ?>
<div class="page-content-tab">
	<div class="container-fluid">
		<div class="row">
			<div class="col-sm-12">
				<div class="page-title-box">
					<div class="float-end">
					<?php
                        $addParam = "{'modal_id' : 'bs-right-lg-modal', 'call_function':'addAdvance', 'form_id' : 'addAdvance', 'title' : 'Add Advance'}";
                    ?>
                    <button type="button" class="btn waves-effect waves-light btn-outline-dark permission-write float-right press-add-btn" onclick="modalAction(<?=$addParam?>);"><i class="fa fa-plus"></i> Add Advance</button>
					<?php
                        $bulkParam = "{'modal_id' : 'bs-right-lg-modal', 'call_function':'bulkAdvance', 'form_id' : 'bulkAdvance', 'title' : 'Bulk Advance','fnsave' : 'saveBulkAdvance'}";
                    ?>
                    <button type="button" class="btn waves-effect waves-light btn-outline-dark permission-write float-right press-add-btn" onclick="modalAction(<?=$bulkParam?>);"><i class="fa fa-plus"></i> Bulk Advance</button>
                          
					</div>
					<ul class="nav nav-pills">
						<li class="nav-item">
							<button onclick="statusTab('advanceSalaryTable',0);" class=" btn waves-effect waves-light btn-outline-info active" style="outline:0px" data-bs-toggle="tab" aria-expanded="false">Pending</button> 
						</li>
						<li class="nav-item">
							<button onclick="statusTab('advanceSalaryTable',1);" class=" btn waves-effect waves-light btn-outline-warning" style="outline:0px" data-bs-toggle="tab" aria-expanded="false">Sanctioned</button> 
						</li>
					</ul>
				</div>
			</div>
		</div>
        <div class="row">
            <div class="col-12">
                <div class="card">
                    <div class="card-body">
                        <div class="table-responsive">
                            <table id='advanceSalaryTable' class="table table-bordered ssTable ssTable-cf" data-url="/getDTRows"></table>
                        </div>
                    </div>
                </div>
            </div>
        </div>        
    </div>
</div>
<?php $this->load->view('includes/footer'); ?>