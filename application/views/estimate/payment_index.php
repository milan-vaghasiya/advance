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
                        <?php
                            $paymentParam = "{'modal_id':'bs-right-lg-modal','form_id':'estimatePayment','title':'Payment Voucher','call_function':'estimatePayment','fnsave':'saveEstimatePayment'}";
                        ?>
                        <a class="btn btn-outline-primary permission-write float-right" href="javascript:void(0)" onclick="modalAction(<?=$paymentParam?>);"><i class="fas fa-plus"></i> Add Voucher</a>
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
                                <table id='estimatePaymentTable' class="table table-bordered ssTable ssTable-cf" data-url='/getEstimatePaymentDTRows'></table>
                            </div>
                        </div>
					</div>
				</div>
            </div>
        </div>
    </div>
</div>
<?php $this->load->view('includes/footer'); ?>