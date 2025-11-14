<?php $this->load->view('includes/header'); ?>
<div class="page-content-tab">
    <div class="container-fluid">
        <div class="row">
            <div class="col-sm-12">
                <div class="page-title-box">
                    <!-- <div class="float-start">
                        <ul class="nav nav-pills">
                            <li class="nav-item"> <button onclick="statusTab('dieProductionTable',1);" class="btn waves-effect waves-light btn-outline-info active" data-toggle="tab" aria-expanded="false">Pending</button> </li>
                            <li class="nav-item"> <button onclick="statusTab('dieProductionTable',2);" class="btn waves-effect waves-light btn-outline-info" data-toggle="tab" aria-expanded="false">In Progress</button> </li>
                            <li class="nav-item"> <button onclick="statusTab('dieProductionTable',5);" class="btn waves-effect waves-light btn-outline-info" data-toggle="tab" aria-expanded="false">M/C Done</button> </li>
                            <li class="nav-item"> <button onclick="statusTab('dieProductionTable',6);" class="btn waves-effect waves-light btn-outline-info" data-toggle="tab" aria-expanded="false">POP Done</button> </li>
                            <li class="nav-item"> <button onclick="statusTab('dieProductionTable',9);" class="btn waves-effect waves-light btn-outline-info" data-toggle="tab" aria-expanded="false">Approve</button> </li>
                        </ul>
                    </div> -->
                    <!-- <div class="float-end">
                        <?php
                            // $addParam = "{'modal_id' : 'bs-right-md-modal', 'call_function':'addDieProduction', 'form_id' : 'addDieProduction', 'title' : 'Add Die Production'}";
                        ?>
                        <button type="button" class="btn waves-effect waves-light btn-outline-dark float-right press-add-btn" onclick="modalAction(<?=$addParam?>);"><i class="fa fa-plus"></i> Add Die Production</button>
                    </div> -->
                </div>
            </div>
        </div>
        <div class="row">
            <div class="col-12">
                <div class="card">
                    <div class="card-body">
                        <div class="table-responsive">
                            <table id='dieScrapTable' class="table table-bordered ssTable ssTable-cf" data-url='/getScrapDTRows'></table>
                        </div>
                    </div>
                </div>
            </div>
        </div>       
    </div>
</div>

<?php $this->load->view('includes/footer'); ?>

