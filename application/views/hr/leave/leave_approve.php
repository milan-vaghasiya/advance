<?php $this->load->view('includes/header'); ?>
<div class="page-content-tab">
	<div class="container-fluid">
		<div class="row">
			<div class="col-sm-12">
				<div class="page-title-box">					
					<ul class="nav nav-pills">
						<li class="nav-item"> <button onclick="statusTab('leaveApproveTable',0);" class=" btn waves-effect waves-light btn-outline-info active" style="outline:0px" data-toggle="tab" aria-expanded="false">Pending</button> </li>
						<li class="nav-item"> <button onclick="statusTab('leaveApproveTable',2);" class=" btn waves-effect waves-light btn-outline-success" style="outline:0px" data-toggle="tab" aria-expanded="false">Approved</button> </li>
						<li class="nav-item"> <button onclick="statusTab('leaveApproveTable',3);" class=" btn waves-effect waves-light btn-outline-danger" style="outline:0px" data-toggle="tab" aria-expanded="false">Rejected</button> </li>        
					</ul>
				</div>
			</div>
		</div>
        <div class="row">
            <div class="col-12">
                <div class="card">
                    <div class="card-body">
                        <div class="table-responsive">
							<table id="leaveApproveTable" class="table table-bordered ssTable ssTable-cf" data-url="/getDTRows"></table>
                        </div>
                    </div>
                </div>
            </div>
        </div>        
    </div>
</div>

<div class="modal fade" id="approveLeaveModal" tabindex="-1" role="dialog" data-bs-backdrop="static" data-bs-keyboard="false">
    <div class="modal-dialog" role="document">
        <div class="modal-content animated slideDown">
            <div class="modal-header">
                <h4 class="modal-title">Leave Action</h4>
				<button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body">
				<form id="approveLeaveForm" autocomplete="off">
					<div class="col-md-12">
						<div class="row">
							<input type="hidden" name="id" id="id" value="" />
							<input type="hidden" name="fla_id" id="fla_id" value="" />
							<div class="col-md-12 form-group"><div class="error generalError"></div></div>
							<div class="col-md-12 form-group">
								<label for="approve_status">Status</label>
								<select name="approve_status" id="approve_status" class="form-control single-select req">
									<option value="2">Approve</option>
									<option value="4">Decline</option>
								</select>
							</div>
							<div class="col-md-12 form-group">
								<label for="final_notes">Dicision Comments</label>
								<textarea rows="2" name="final_notes" class="form-control" placeholder="Dicision Comments" ></textarea>
							</div>
							<div class="col-md-12 form-group">
                				<span class="badge badge-pill bg-primary max-leave font-14 font-medium"></span>
                				<span class="badge badge-pill bg-danger used-leave font-14 font-medium"></span>
                				<span class="badge badge-pill bg-success remain-leave font-14 font-medium"></span>
                			</div>
						</div>
					</div>
				</form>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary press-close-btn save-form" data-bs-dismiss="modal"><i class="fa fa-times"></i> Close</button>
                <button type="button" class="btn waves-effect waves-light btn-outline-success btn-approveLeave"><i class="fa fa-check"></i> Save</button>
            </div>
        </div>
    </div>
</div>

<?php $this->load->view('includes/footer'); ?>
<script src="<?php echo base_url();?>assets/js/custom/leave-approve.js?v=<?=time()?>"></script>