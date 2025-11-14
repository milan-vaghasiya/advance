<?php $this->load->view('includes/header'); ?>
<div class="page-content-tab">
	<div class="container-fluid">
		<div class="row">
			<div class="col-sm-12">
				<div class="page-title-box">
                    <div class="float-start">
                        <ul class="nav nav-pills">
                            <li class="nav-item"> 
                                <button onclick="statusTab('dispatchPlanTable',0,'getSalesDtHeader','pendingPlan');" class="nav-tab btn waves-effect waves-light btn-outline-info active" style="outline:0px" data-toggle="tab" aria-expanded="false">Pending</button> 
                            </li>
                            <li class="nav-item"> 
                                <button onclick="statusTab('dispatchPlanTable',1,'getSalesDtHeader','dispatchPlan');" class="nav-tab btn waves-effect waves-light btn-outline-info" style="outline:0px" data-toggle="tab" aria-expanded="false">Planned</button> 
                            </li>
                        </ul>
                    </div>
				</div>
			</div>
		</div>
		<div class="row">
            <div class="col-12">
				<div class="card">
					<div class="card-body">
						<div class="table-responsive">
							<table id='dispatchPlanTable' class="table table-bordered ssTable ssTable-cf" data-url='/getDTRows'></table>
						</div>
					</div>
				</div>
            </div>
        </div> 
    </div>
</div>
<?php $this->load->view('includes/footer'); ?>