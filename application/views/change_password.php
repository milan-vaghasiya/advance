<div class="modal fade" id="change-psw" tabindex="-1" role="dialog" aria-labelledby="exampleModalLabel1" data-backdrop="static" data-keyboard="false">
    <div class="modal-dialog" role="document">
        <div class="modal-content animated slideDown">
            <div class="modal-header">
                <h4 class="modal-title" id="exampleModalLabel1">Change Password</h4>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close" data-modal_id="modal-md"></button>
            </div>
            <div class="modal-body">
                <form id="changePSW">
                    <div class="col-md-12">
						<div class="row">
							<div class="form-group col-md-12">
								<label for="old_password">Old Password</label>
								<input type="password" name="old_password" id="old_password" class="form-control" placeholder="Old Password" />
								<div class="error old_password"></div>
							</div>

							<div class="form-group col-md-12">
								<label for="new_password">New Password</label>
                                <div class="input-group"> 
                                    <input type="password" name="new_password" id="new_password" class="form-control pswType" placeholder="Enter Password" value="">
                                    <div class="input-group-append">
                                        <button type="button" class="btn waves-effect waves-light btn-outline-primary pswHideShow"><i class="fa fa-eye"></i></button>
                                    </div>
                                </div>
								<div class="error new_password"></div>
							</div>

							<div class="form-group col-md-12">
								<label for="cpassword">Confirm Password</label>
								<input type="text" name="cpassword" id="cpassword" class="form-control" placeholder="Confirm Password" />
								<div class="error cpassword"></div>
							</div>
						</div>
					</div>	
                </form>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary press-close-btn btn-close-modal save-form" data-bs-dismiss="modal" data-modal_id="modal-md"><i class="fa fa-times"></i> Close</button>
                <button type="button" class="btn btn-success waves-effect waves-light btn-save" onclick="changePsw('changePSW');"><i class="fa fa-check"></i> Save</button>
            </div>
        </div>
    </div>
</div>