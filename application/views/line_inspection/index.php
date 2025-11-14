<?php $this->load->view('includes/header'); ?>
<div class="page-content-tab">
	<div class="container-fluid">
        <div class="row">
			<div class="col-sm-12">
				<div class="page-title-box">
                    <ul class="nav nav-pills">
                    <li class="nav-item"> 
                            <a href="<?=base_url($headData->controller.'/index');?>" class=" btn waves-effect waves-light btn-outline-info active" style="outline:0px"  >Running Jobs</a>
                        </li>
                        <li class="nav-item"> 
                            <a href="<?=base_url($headData->controller.'/iprIndex');?>" class=" btn waves-effect waves-light btn-outline-info " style="outline:0px" >Filled Reports</a>
                        </li>
                    </ul>
				</div>
			</div>
		</div>
        <div class="row">
            <div class="col-12">
                <div class="card">
                    <div class="card-body">
                        <div class="table-responsive">
                            <table id='lineInspectionTable' class="table table-bordered ssTable ssTable-cf" data-url='/getDTRows/'></table>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<?php $this->load->view('includes/footer'); ?>
