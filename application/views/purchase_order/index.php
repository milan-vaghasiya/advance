<?php $this->load->view('includes/header'); ?>
<div class="page-content-tab">
	<div class="container-fluid">
        <div class="row">
            <div class="col-sm-12">
				<div class="page-title-box">
                    <div class="float-start">
					    <ul class="nav nav-pills">
                            <li class="nav-item"> 
                                <button onclick="statusTab('purchaseOrderTable',0);" id="pending_so" class="nav-tab btn waves-effect waves-light btn-outline-danger active" style="outline:0px" data-toggle="tab" aria-expanded="false">Pending</button> 
                            </li>
                            <li class="nav-item"> 
                                <button onclick="statusTab('purchaseOrderTable',3);" id="pending_so" class="nav-tab btn waves-effect waves-light btn-outline-warning" style="outline:0px" data-toggle="tab" aria-expanded="false">Approved</button> 
                            </li>
                            <li class="nav-item"> 
                                <button onclick="statusTab('purchaseOrderTable',1);" id="complete_so" class="nav-tab btn waves-effect waves-light btn-outline-success" style="outline:0px" data-toggle="tab" aria-expanded="false">Completed</button> 
                            </li>
							<li class="nav-item"> 
                                <button onclick="statusTab('purchaseOrderTable',2);" id="close_so" class="nav-tab btn waves-effect waves-light btn-outline-primary" style="outline:0px" data-toggle="tab" aria-expanded="false">Short Close</button> 
                            </li>
                        </ul>
					</div>
					<div class="float-end">
                        <a href="javascript:void(0)" onclick="window.location.href='<?=base_url($headData->controller.'/addOrder')?>'" data-txt_editor="conditions" class="btn waves-effect waves-light btn-outline-dark float-right permission-write press-add-btn"><i class="fa fa-plus"></i> Add Order</a>
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
                                <table id='purchaseOrderTable' class="table table-bordered ssTable ssTable-cf" data-url='/getDTRows'></table>
                            </div>
                        </div>
					</div>
				</div>
            </div>
        </div>
    </div>
</div>

<div class="modal fade" id="print_dialog_order" data-bs-backdrop="static" data-bs-keyboard="false">
	<div class="modal-dialog" style="min-width:30%;">
		<div class="modal-content animated zoomIn border-light">
			<div class="modal-header bg-light">
				<h5 class="modal-title text-dark"><i class="fa fa-print"></i> Print Options</h5>
				<button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
			</div>
			<form id="printOrderModel" method="post" action="" target="_blank">
				<div class="modal-body">
					<div class="col-md-12">
						<div class="row">
                            <input type="hidden" name="id" id="id" value="0">
                            
							<div class="col-lg-12 col-md-12 col-sm-12 col-xs-12">
								<div class="col-md-12 form-group">
									<label for="test_type">Test Type</label>
									<select name="test_type[]" id="test_type" class="form-control select2 " multiple>
										<?php
											foreach($testTypeList as $row):
												echo '<option value="'.$row->id.'">'.$row->test_name.'</option>';
											endforeach;
										?>
									</select>
								</div>
							</div>
						</div>
					</div>
				</div>
				<div class="modal-footer">
					<button type="button" class="btn btn-secondary press-close-btn save-form" data-bs-dismiss="modal"><i class="fa fa-times"></i> Close</button>
					<button type="submit" class="btn btn-success" onclick="printModal('print_dialog_order');"><i class="fa fa-print"></i> Print</button>
				</div>
			</form>
		</div>
	</div>
</div>

<?php $this->load->view('includes/footer'); ?>
<script>
$(document).ready(function(){
	$(document).on("click",".printOrderDialog",function(){
		$("#printOrderModel").attr('action',base_url + controller + "/" + ($(this).data('fn_name') || ""));
		$("#printOrderModel #id").val($(this).data('id'));
		$("#printOrderModel #test_type").val('test_type');
		$("#print_dialog_order").modal("show");
		initSelect2();
	});
});
</script>