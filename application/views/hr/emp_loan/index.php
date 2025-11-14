<?php $this->load->view('includes/header'); ?>
<div class="page-content-tab">
	<div class="container-fluid">
		<div class="row">
			<div class="col-sm-12">
				<div class="page-title-box">
					<div class="float-end">
						<?php
                            $addParam = "{'modal_id' : 'bs-right-md-modal', 'call_function':'addLoan', 'form_id' : 'addLoan', 'title' : 'Add Loan'}";
                        ?>
                        <button type="button" class="btn waves-effect waves-light btn-outline-dark permission-write float-right press-add-btn" onclick="modalAction(<?=$addParam?>);"><i class="fa fa-plus"></i> Add Loan</button>
					</div>
					<ul class="nav nav-pills">
						<li class="nav-item"> <button onclick="statusTab('empLoanTable',0);" class="btn waves-effect waves-light btn-outline-info active" data-toggle="tab" aria-expanded="false">Pending</button> </li>
						<li class="nav-item"> <button onclick="statusTab('empLoanTable',1);" class="btn waves-effect waves-light btn-outline-info" data-toggle="tab" aria-expanded="false">Approved</button> </li>
						<li class="nav-item"> <button onclick="statusTab('empLoanTable',2);" class="btn waves-effect waves-light btn-outline-info" data-toggle="tab" aria-expanded="false">Senctioned</button> </li>
					</ul>
				</div>
			</div>
		</div>
        <div class="row">
            <div class="col-12">
                <div class="card">
                    <div class="card-body">
                        <div class="table-responsive">
                            <table id='empLoanTable' class="table table-bordered ssTable ssTable-cf" data-url="/getDTRows"></table>
                        </div>
                    </div>
                </div>
            </div>
        </div>        
    </div>
</div>
<?php $this->load->view('includes/footer'); ?>