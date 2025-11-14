<?php $this->load->view('includes/header'); ?>
<div class="page-content-tab">
    <div class="container-fluid bg-container">
        <div class="row">
            <div class="col-sm-12">
                <div class="page-title-box">
                    <div class="float-end">
                        <a href="<?=base_url($headData->controller."/addChallan")?>" class="btn waves-effect waves-light btn-outline-dark float-right press-add-btn permission-write"><i class="fa fa-plus"></i> Add Challan</a>
					</div>
                    <ul class="nav nav-pills">
                        <li class="nav-item"> <button onclick="statusTab('dieChallanTable',0);" class="btn waves-effect waves-light btn-outline-info active mr-1" data-bs-toggle="tab" aria-expanded="false">Pending</button> </li>
                        <li class="nav-item"> <button onclick="statusTab('dieChallanTable',1);" class="btn waves-effect waves-light btn-outline-info" data-bs-toggle="tab" aria-expanded="false">Completed</button> </li> 
                    </ul>
                </div>
            </div>
        </div> 
        <div class="row">
            <div class="col-12">
                <div class="card">
                    <div class="card-body">
                        <div class="table-responsive">
                            <table id='dieChallanTable' class="table table-bordered ssTable ssTable-cf" data-url='/getDTRows'></table>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
<?php $this->load->view('includes/footer'); ?>
