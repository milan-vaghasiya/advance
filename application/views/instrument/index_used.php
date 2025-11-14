<?php $this->load->view('includes/header'); ?>
<div class="page-content-tab">
    <div class="container-fluid bg-container">
        <div class="row">
            <div class="col-sm-12">
                <div class="page-title-box">
					
                    <ul class="nav nav-pills">    
                        <li class="nav-item"> <a href="<?= base_url($headData->controller . "/index/1") ?>" class="btn waves-effect waves-light btn-outline-info <?=($status == 1)?'active':''?>">In Stock</a> </li>
                        <li class="nav-item"> <a href="<?= base_url($headData->controller . "/index/0") ?>" class="btn waves-effect waves-light btn-outline-info <?=($status == 0)?'active':''?>">New Inward</a> </li>
                        <li class="nav-item"> <a href="<?= base_url($headData->controller . "/indexUsed/2") ?>" class="btn waves-effect waves-light btn-outline-info <?=($status == 2)?'active':''?>">Issued</a> </li>
                        <li class="nav-item"> <a href="<?= base_url($headData->controller . "/indexUsed/3") ?>" class="btn waves-effect waves-light btn-outline-info <?=($status == 3)?'active':''?>">In Calibration</a> </li>
                        <li class="nav-item"> <a href="<?= base_url($headData->controller . "/index/4") ?>" class="btn waves-effect waves-light btn-outline-info <?=($status == 4)?'active':''?>">Rejected</a> </li>
                        <li class="nav-item"> <a href="<?= base_url($headData->controller . "/index/5") ?>" class="btn waves-effect waves-light btn-outline-info <?=($status == 5)?'active':''?>">Due For Calibration</a> </li>
                    </ul>
                </div>
            </div>
        </div>
        <div class="row">
            <div class="col-12">
                <div class="card">
                    <div class="card-body">
                        <div class="table-responsive">
                            <table id='instrumentTable' class="table table-bordered ssTable ssTable-cf" data-url='/getChallanDTRows/<?=$status?>'></table>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
<?php $this->load->view('includes/footer'); ?>
