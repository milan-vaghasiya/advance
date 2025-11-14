<?php $this->load->view('includes/header'); ?>

<div class="page-content-tab">
	<div class="container-fluid">
        <div class="row">
            <div class="col-sm-12">
				<div class="page-title-box">
                    <div class="float-start">
					    <ul class="nav nav-pills">
                            <li class="nav-item"> 
                                <button onclick="statusTab('paymentVoucherTable','BCRct');" id="received_vou" class="nav-tab btn waves-effect waves-light btn-outline-success active" style="outline:0px" data-toggle="tab" aria-expanded="false">Received Voucher</button> 
                            </li>
                            <li class="nav-item"> 
                                <button onclick="statusTab('paymentVoucherTable','BCPmt');" id="paid_vou" class="nav-tab btn waves-effect waves-light btn-outline-warning" style="outline:0px" data-toggle="tab" aria-expanded="false">Paid Voucher</button> 
                            </li>
                        </ul>
					</div>
					<div class="float-end">
                        <?php
                            $addParam = "{'modal_id' : 'bs-right-lg-modal', 'call_function':'addPaymentVoucher', 'form_id' : 'addPaymentVoucher', 'title' : 'Add Voucher','js_store_fn':'customStore'}";
                        ?>
                        <button type="button" class="btn waves-effect waves-light btn-outline-dark permission-write float-right press-add-btn" onclick="modalAction(<?=$addParam?>);"><i class="fa fa-plus"></i> Add Voucher</button>
					</div>
                    <h4 class="card-title text-center">Payment Voucher</h4>
				</div>
            </div>
		</div>
        <div class="row">
            <div class="col-12">
				<div class="col-12">
					<div class="card">
                        <div class="card-body">
                            <div class="table-responsive">
                                <table id='paymentVoucherTable' class="table table-bordered ssTable ssTable-cf" data-url='/getDtRows'></table>
                            </div>
                        </div>
					</div>
				</div>
            </div>
        </div>
    </div>
</div>
<?php $this->load->view('includes/footer'); ?>
