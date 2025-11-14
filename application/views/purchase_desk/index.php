<?php $this->load->view('includes/header'); ?>
<div class="page-content-tab">
	<div class="container-fluid">
        <div class="row">
            <div class="col-sm-12">
				<div class="page-title-box">
                    <div class="float-start">
					    <ul class="nav nav-pills">
                            <li class="nav-item"> 
                                <button onclick="statusTab('purchaseDeskTable',1);" id="pending_so" class="nav-tab btn waves-effect waves-light btn-outline-info active" style="outline:0px" data-toggle="tab" aria-expanded="false">Pending</button> 
                            </li>
							<li class="nav-item"> 
                                <button onclick="statusTab('purchaseDeskTable',4);" id="pending_so" class="nav-tab btn waves-effect waves-light btn-outline-info" style="outline:0px" data-toggle="tab" aria-expanded="false">Quotation Received</button> 
                            </li>
                            <li class="nav-item"> 
                                <button onclick="statusTab('purchaseDeskTable',2);" id="complete_so" class="nav-tab btn waves-effect waves-light btn-outline-info" style="outline:0px" data-toggle="tab" aria-expanded="false">Approved</button> 
                            </li>
							<li class="nav-item"> 
                                <button onclick="statusTab('purchaseDeskTable',5);" id="close_so" class="nav-tab btn waves-effect waves-light btn-outline-info" style="outline:0px" data-toggle="tab" aria-expanded="false">Completed</button> 
                            </li>
							<li class="nav-item"> 
                                <button onclick="statusTab('purchaseDeskTable',3);" id="close_so" class="nav-tab btn waves-effect waves-light btn-outline-info" style="outline:0px" data-toggle="tab" aria-expanded="false">Rejected/Not Feasible</button> 
                            </li>
                        </ul>
					</div>
					<div class="float-end">
						<?php
							$addParam = "{'modal_id' : 'bs-right-lg-modal', 'call_function':'addEnquiry', 'form_id' : 'addEnquiry', 'title' : 'New Enquiry', 'fnsave' : 'saveEnquiry', 'txt_editor' : 'item_remark', 'js_store_fn' : 'storeEnquiry'}";
						?>
						<a href="javascript:void(0)" onclick="window.location.href='<?=base_url($headData->controller.'/addEnquiry')?>'" data-txt_editor="conditions" class="btn waves-effect waves-light btn-outline-dark float-right permission-write press-add-btn"><i class="fa fa-plus"></i> Add Enquiry</a>
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
                                <table id='purchaseDeskTable' class="table table-bordered ssTable ssTable-cf" data-url='/getDTRows'></table>
                            </div>
                        </div>
					</div>
				</div>
            </div>
        </div>
    </div>
</div>
<?php $this->load->view('includes/footer'); ?>
<script src="<?=base_url()?>assets/js/custom/purchase_desk.js?v=<?=time()?>"></script>