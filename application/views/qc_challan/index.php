<?php $this->load->view('includes/header'); ?>
<div class="page-content-tab">
    <div class="container-fluid bg-container">
        <div class="row">
            <div class="col-sm-12">
                <div class="page-title-box">
                    <div class="float-end">
                        <!-- <div class="col-md-4"> <h4 class="card-title text-center">QC Challan</h4></h4> </div> -->
                        <!--<a href="<?=base_url($headData->controller."/addChallan")?>" class="btn waves-effect waves-light btn-outline-primary float-right permission-write"><i class="fa fa-plus"></i> Add Challan</a>-->
					</div>
                    <ul class="nav nav-pills">
                        <li class="nav-item"> <button onclick="statusTab('inChallanTable',0);" class="btn waves-effect waves-light btn-outline-danger active mr-1" data-bs-toggle="tab" aria-expanded="false">Pending</button> </li>
                        <li class="nav-item"> <button onclick="statusTab('inChallanTable',1);" class="btn waves-effect waves-light btn-outline-success" data-bs-toggle="tab" aria-expanded="false">Completed</button> </li>
                    </ul>
                </div>
            </div>
        </div> 
        <div class="row">
            <div class="col-12">
                <div class="card">
                    <div class="card-body">
                        <div class="table-responsive">
                            <table id='inChallanTable' class="table table-bordered ssTable ssTable-cf" data-url='/getDTRows'></table>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
<?php $this->load->view('includes/footer'); ?>
