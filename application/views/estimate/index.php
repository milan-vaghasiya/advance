<?php $this->load->view('includes/header'); ?>
<div class="page-content-tab">
	<div class="container-fluid">
        <div class="row">
            <div class="col-sm-12">
				<div class="page-title-box">
                    <div class="float-start">
                        <ul class="nav nav-pills">
                            <li class="nav-item"> 
                                <a href="<?=base_url("estimate")?>" class="btn btn-outline-info waves-effect waves-light">Estimate</a>
                            </li>
                            <li class="nav-item"> 
                                <a href="<?=base_url("estimate/payments")?>" class="btn btn-outline-info waves-effect waves-light">Payment</a>
                            </li>
                            <li class="nav-item"> 
                                <a href="<?=base_url("estimate/ledger")?>" class="btn btn-outline-success waves-effect waves-light">Estimate Ledger</a>
                            </li>
                            <li class="nav-item"> 
                                <a href="<?=base_url("estimate/openingBalance")?>" class="btn btn-outline-success waves-effect waves-light" target="_blank">Op. Balance</a>
                            </li>
                        </ul>
                    </div>
					<div class="float-end">
                        <a href="javascript:void(0)" class="btn waves-effect waves-light btn-outline-dark float-right permission-write press-add-btn" onclick="window.location.href='<?=base_url($headData->controller.'/addEstimate')?>'"><i class="fa fa-plus"></i> Add Estimate</a>
					</div>
                    <h4 class="card-title text-center">Estimate</h4>
				</div>
            </div>
		</div>
        <div class="row">
            <div class="col-12">
				<div class="col-12">
					<div class="card">
                        <div class="card-body">
                            <div class="table-responsive">
                                <table id='estimateTable' class="table table-bordered ssTable ssTable-cf" data-url='/getDTRows'></table>
                            </div>
                        </div>
					</div>
				</div>
            </div>
        </div>
    </div>
</div>
<?php $this->load->view('includes/footer'); ?>