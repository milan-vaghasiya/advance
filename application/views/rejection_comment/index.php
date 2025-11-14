<?php $this->load->view('includes/header'); ?>
<div class="page-content-tab">
	<div class="container-fluid">
		<div class="row">
			<div class="col-sm-12">
				<div class="page-title-box">
					<div class="float-start">
						<ul class="nav nav-pills">
							<li class="nav-item"> 
								<button onclick="statusTab('rejectionCommentTable',1);" class="nav-tab btn waves-effect waves-light btn-outline-success active" style="outline:0px" data-bs-toggle="tab" aria-expanded="false">Rejection Reason</button> 
							</li>
							<li class="nav-item"> 
								<button onclick="statusTab('rejectionCommentTable',2);" class="nav-tab btn waves-effect waves-light btn-outline-success" style="outline:0px" data-bs-toggle="tab" aria-expanded="false">Idle Reason</button> 
							</li>
							<li class="nav-item"> 
								<button onclick="statusTab('rejectionCommentTable',3);" class="nav-tab btn waves-effect waves-light btn-outline-success" style="outline:0px" data-bs-toggle="tab" aria-expanded="false">Rework Reason</button> 
							</li>
							<li class="nav-item"> 
								<button onclick="statusTab('rejectionCommentTable',4);" class="nav-tab btn waves-effect waves-light btn-outline-success" style="outline:0px" data-bs-toggle="tab" aria-expanded="false">Non Feasibility Reason</button> 
							</li>
						</ul>
					</div>
					<div class="float-end">
                        <?php
                            $addParam = "{'modal_id' : 'bs-right-md-modal', 'call_function':'addRejectionComment', 'form_id' : 'addRejectionComment', 'title' : 'Add Reason'}";
                        ?>
                        <button type="button" class="btn waves-effect waves-light btn-outline-dark permission-write float-right press-add-btn" onclick="modalAction(<?=$addParam?>);"><i class="fa fa-plus"></i> Add Reason</button>
					</div>					
				</div>
			</div>
		</div>
        <div class="row">
            <div class="col-12">
                <div class="card">
                    <div class="card-body">
                        <div class="table-responsive">
                            <table id='rejectionCommentTable' class="table table-bordered ssTable ssTable-cf" data-url='/getDTRows'></table>
                        </div>
                    </div>
                </div>
            </div>
        </div>        
    </div>
</div>

<?php $this->load->view('includes/footer'); ?>
<script>
$(document).ready(function(){
    $(document).on('change',"#type",function(){
        if($(this).val() == 1){ $("#subTypeDiv").show(); }
        else{ $("#subTypeDiv").hide(); }
    });
});
</script>