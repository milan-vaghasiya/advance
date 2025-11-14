<?php $this->load->view('includes/header'); ?>
<div class="page-content-tab">
	<div class="container-fluid">
        <div class="row">
            <div class="col-sm-12">
				<div class="page-title-box">
                    <div class="float-start">
					    <ul class="nav nav-pills">
                            <li class="nav-item">
                                <button onclick="statusTab('ncrTable','0');" class="btn waves-effect waves-light btn-outline-danger active" id="pending_cc" style="outline:0px" data-bs-toggle="tab" aria-expanded="false">Pending</button>
                            </li>
                            <li class="nav-item">
                                <button onclick="statusTab('ncrTable','1');" class="btn waves-effect waves-light btn-outline-success" id="completed_cc" style="outline:0px" data-bs-toggle="tab" aria-expanded="false">Completed</button>
                            </li>
                        </ul>
					</div>
					<div class="float-end">
                        <?php
                            $addParam = "{'modal_id' : 'bs-right-lg-modal', 'call_function':'addNCR', 'form_id' : 'addNCR', 'title' : 'Add NCR'}";
                        ?>
                        <button type="button" class="btn waves-effect waves-light btn-outline-dark permission-write float-right press-add-btn" onclick="modalAction(<?=$addParam?>);"><i class="fa fa-plus"></i> Add NCR</button>
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
                                <table id='ncrTable' class="table table-bordered ssTable ssTable-cf" data-url='/getDTRows'></table>
                            </div>
                        </div>
					</div>
				</div>
            </div>
        </div>
    </div>
</div>
<?php $this->load->view('includes/footer'); ?>