<?php $this->load->view('includes/header'); ?>
<style>li.nav-item{padding:0px 3px;}.process-tags span{font-size:0.75rem;color:#000;box-shadow: 0px 1px 1px rgba(9, 30, 66, 0.25), 0px 0px 1px 1px rgba(9, 30, 66, 0.13);}.prcList{padding:0.4rem;}</style>
<div class="page-content-tab">
	<div class="container-fluid">
        <div class="row">
            <div class="col-sm-12">
				<div class="page-title-box">
                    <div class="float-start">
					    <ul class="nav nav-pills"> 
                            <!-- <li class="nav-item">
                                <button onclick="statusTab('outsourceTable','0');" class="nav-tab btn waves-effect waves-light btn-outline-danger active" id="pending_receive" style="outline:0px" data-bs-toggle="tab" aria-expanded="false">Pending</button>
                            </li>
                            <li class="nav-item">
                                <button onclick="statusTab('outsourceTable','1');" class="nav-tab btn waves-effect waves-light btn-outline-success" id="completed_receive" style="outline:0px" data-bs-toggle="tab" aria-expanded="false">Completed</button>
                            </li> -->
							 <li class="nav-item">
                                <button onclick="statusTab('outsourceTable','0','getProductionDtHeader','outsource');" class="nav-tab btn waves-effect waves-light btn-outline-danger active"  style="outline:0px" data-bs-toggle="tab" aria-expanded="false">Pending</button>
                            </li>

                            <li class="nav-item">
                                <button onclick="statusTab('outsourceTable','1','getProductionDtHeader','outsource');" class="nav-tab btn waves-effect waves-light btn-outline-success" style="outline:0px" data-bs-toggle="tab" aria-expanded="false">Completed</button>
                            </li>

							<li class="nav-item">
                                <button onclick="statusTab('outsourceTable','2','getProductionDtHeader','rmChallan');" class="nav-tab btn waves-effect waves-light btn-outline-primary"  style="outline:0px" data-bs-toggle="tab" aria-expanded="false">RM Challan</button>
                            </li>
							<li class="nav-item"> <a href="<?=base_url($headData->controller.'/jobWorkBillIndex')?>" target="_blank" class="btn waves-effect waves-light btn-outline-info">Jobwork Bill</a></li>
                        </ul>
					</div>
					<div class="float-end">
                        <a href="javascript:void(0)" class="btn waves-effect waves-light btn-outline-dark float-right permission-write press-add-btn" onclick="window.location.href='<?=base_url($headData->controller.'/addChallan')?>'"><i class="fa fa-plus"></i> Add Challan</a>
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
                                <table id='outsourceTable' class="table table-bordered ssTable ssTable-cf" data-url='/getDTRows'></table>
                            </div>
                        </div>
					</div>
				</div>
            </div>
        </div>
    </div>
</div>

<div class="modal fade" id="print_dialog_challan" data-bs-backdrop="static" data-bs-keyboard="false">
	<div class="modal-dialog" style="min-width:30%;">
		<div class="modal-content animated zoomIn border-light">
			<div class="modal-header bg-light">
				<h5 class="modal-title text-dark"><i class="fa fa-print"></i> Print Options</h5>
				<button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
			</div>
			<form id="printChallanModel" method="post" action="" target="_blank">
				<div class="modal-body">
					<div class="col-md-12">
						<div class="row">
                            <input type="hidden" name="id" id="id" value="0">
                            <input type="hidden" name="req_id" id="req_id" value="0">
                            <input type="hidden" name="print_format" id="print_format" value="">
                            
							<div class="col-lg-5 col-md-5 col-sm-12 col-xs-12">
								<div class="custom-control custom-checkbox">
									<input type="checkbox" name="original" id="original" class="filled-in chk-col-success" value="1" checked>
									<label for="original">Original</label>
								</div>
								<div class="custom-control custom-checkbox">
									<input type="checkbox" name="duplicate" id="duplicate" class="filled-in chk-col-success" value="1" checked>
									<label for="duplicate">Duplicate</label>
								</div>
								<div class="custom-control custom-checkbox">
									<input type="checkbox" name="triplicate" id="triplicate" class="filled-in chk-col-success" value="1">
									<label for="triplicate">Triplicate</label>
								</div>
								<div class="custom-control custom-checkbox">
									<input type="checkbox" name="header_footer" id="header_footer" class="filled-in chk-col-success" value="1" checked>
									<label for="header_footer">Header/Footer</label>
								</div>
							</div>
							<div class="col-lg-7 col-md-7 col-sm-12 col-xs-12">
                                <div class="col-md-12 form-group">
                                    <label>No. of Extra Copy</label>
                                    <input type="text" name="extra_copy" id="extra_copy" class="form-control numericOnly" value="0">
                                </div>
								<div class="col-md-12 form-group">
									<label for="test_type">Test Type</label>
									<select name="test_type[]" id="test_type" class="form-control select2 " multiple>
										<?php
											foreach($testTypeList as $row):
												echo '<option value="'.$row->id.'">'.$row->test_name.'</option>';
											endforeach;
										?>
									</select>
									<div class="error test_type"></div>
								</div>

                                <!-- <div class="col-md-12 form-group">
                                    <label>Max Lines Per Page</label>
                                    <input type="text" name="max_lines" id="max_lines" class="form-control numericOnly" placeholder="Max Lines Per Page" value="">
                                </div> -->
								<label class="error_extra_copy text-danger"></label>
							</div>
						</div>
					</div>
				</div>
				<div class="modal-footer">
					<button type="button" class="btn btn-secondary press-close-btn save-form" data-bs-dismiss="modal"><i class="fa fa-times"></i> Close</button>
					<button type="submit" class="btn btn-success" onclick="printModal('print_dialog_challan');"><i class="fa fa-print"></i> Print</button>
				</div>
			</form>
		</div>
	</div>
</div>

<?php $this->load->view('includes/footer'); ?>
<script src="<?=base_url()?>assets/js/custom/sop_desk.js?v=<?=time()?>"></script>
<script>
$(document).ready(function(){
	$(document).on("click",".printChallanDialog",function(){
		$("#printChallanModel").attr('action',base_url + controller + "/" + ($(this).data('fn_name') || ""));
		$("#printChallanModel #print_format").val($(this).data('print_format'));
		$("#printChallanModel #id").val($(this).data('id'));
		$("#printChallanModel #req_id").val($(this).data('req_id'));
		$("#printChallanModel #test_type").val('test_type');
		$("#print_dialog_challan").modal("show");
	});
});
</script>
<script src="<?=base_url()?>assets/js/custom/e-bill.js?v=<?=time()?>"></script>