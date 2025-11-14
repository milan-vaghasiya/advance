<?php $this->load->view('includes/header'); ?>
<div class="page-content-tab">
    <div class="container-fluid bg-container">
        <div class="row">
            <div class="col-sm-12">
                <div class="page-title-box">
                    <div class="float-start">
                        <ul class="nav nav-pills">
                            <li class="nav-item">
                                <a href="<?=base_url($headData->controller."/returnIndex/".$status)?>" class="btn waves-effect waves-light btn-outline-info mr-1 active"> Returnable </a>
                            </li>
                            <li class="nav-item">
                                <a href="<?=base_url($headData->controller)?>" class="btn waves-effect waves-light btn-outline-info mr-1"> Requisition </a>
                            </li>
                        </ul>
                    </div>
                    <div class="float-end">
                        <?php
                            $addParam = "{'modal_id' : 'bs-right-lg-modal', 'call_function':'addRequisition', 'form_id' : 'addRequisition', 'title' : 'Add Requisition'}";
                        ?>
                        <button type="button" class="btn waves-effect waves-light btn-outline-dark permission-write float-right press-add-btn" onclick="modalAction(<?=$addParam?>);"><i class="fa fa-plus"></i> Add Requisition</button>
					</div>
                    
                </div>
            </div>
        </div> 
        <div class="row">
            <div class="col-12">
                <div class="card">
                    <div class="card-body">
                        <div class="table-responsive">
                            <table id='requisitionTable' class="table table-bordered ssTable" data-url='/getReqDTRows/<?=$status?>'></table>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
<?php $this->load->view('includes/footer'); ?>