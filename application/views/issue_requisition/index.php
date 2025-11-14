<?php $this->load->view('includes/header'); ?>
<div class="page-content-tab">
    <div class="container-fluid bg-container">
        <div class="row">
            <div class="col-sm-12">
                <div class="page-title-box">
                    <div class="float-end">
                    <?php
                            $addParam = "{'modal_id' : 'bs-right-lg-modal', 'call_function':'addIssueRequisition', 'form_id' : 'addIssueRequisition', 'title' : 'Add Issue Requisition'}";
                        ?>
                        <button type="button" class="btn waves-effect waves-light btn-outline-dark permission-write float-right press-add-btn" onclick="modalAction(<?=$addParam?>);"><i class="fa fa-plus"></i> Add Issue Requisition</button>
					</div>
                    <ul class="nav nav-pills">
                        <li class="nav-item"> <a href="<?=base_url($headData->controller)?>" class="btn waves-effect waves-light btn-outline-info active mr-1"> Pending </a> </li>
                        <li class="nav-item"> <a href="<?=base_url($headData->controller."/issueReqIndex")?>" class="btn waves-effect waves-light btn-outline-info mr-1"> Issued </a> </li>
                    </ul>
                </div>
            </div>
        </div> 
        <div class="row">
            <div class="col-12">
                <div class="card">
                    <div class="card-body">
                        <div class="table-responsive">
                            <table id='issueRequisitionTable' class="table table-bordered ssTable" data-url='/getDTRows'></table>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
<?php $this->load->view('includes/footer'); ?>