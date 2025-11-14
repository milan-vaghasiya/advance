<?php $this->load->view('includes/header'); ?>
<div class="page-content-tab">
	<div class="container-fluid">
        <div class="row">
            <div class="col-sm-12">
				<div class="page-title-box">
                    <div class="float-start">
					    <ul class="nav nav-pills">
                            <li class="nav-item">
                                <button onclick="statusTab('sopTable','1');" class="nav-tab btn waves-effect waves-light btn-outline-success active mr-2" id="planned_index" style="outline:0px" data-bs-toggle="tab" aria-expanded="false">Planing Jobs</button>
                            </li>
                            <li class="nav-item">
                                <button onclick="statusTab('sopTable','2');" class="nav-tab btn waves-effect waves-light btn-outline-success mr-2" id="progress_index" style="outline:0px" data-bs-toggle="tab" aria-expanded="false">In Progress</button>
                            </li>
                            <li class="nav-item">
                                <button onclick="statusTab('sopTable','3');" class="nav-tab btn waves-effect waves-light btn-outline-success mr-2" id="completed_index" style="outline:0px" data-bs-toggle="tab" aria-expanded="false">Completed</button>
                            </li>
                            <li class="nav-item">
                                <button onclick="statusTab('sopTable','4');" class="nav-tab btn waves-effect waves-light btn-outline-success mr-2" id="onhold_index" style="outline:0px" data-bs-toggle="tab" aria-expanded="false">On Hold</button>
                            </li>
                            <li class="nav-item">
                                <button onclick="statusTab('sopTable','5');" class="nav-tab btn waves-effect waves-light btn-outline-success mr-2" id="close_index" style="outline:0px" data-bs-toggle="tab" aria-expanded="false">Short Closed</button>
                            </li>
                        </ul>
					</div>
                    <div class="float-end">
                        <?php
                            $addParam = "{'modal_id' : 'bs-right-lg-modal', 'call_function':'addPRC', 'form_id' : 'addPRC', 'title' : 'New PRC', 'fnsave' : 'savePRC'}";
                        ?>
                        <button type="button" class="btn btn-info permission-write press-add-btn" onclick="modalAction(<?=$addParam?>);"><i class="fa fa-plus"></i> New PRC</button>
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
                                <table id='sopTable' class="table table-bordered ssTable ssTable-cf" data-url='/getSopDTRows'></table>
                            </div>
                        </div>
					</div>
				</div>
            </div>
        </div>
    </div>
</div>
<?php $this->load->view('includes/footer'); ?>
<script src="<?=base_url()?>assets/js/custom/sop_desk.js?v=<?=time()?>"></script>
