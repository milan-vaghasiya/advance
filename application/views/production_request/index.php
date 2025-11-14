<?php $this->load->view('includes/header'); ?>
<div class="page-content-tab">
	<div class="container-fluid">
        <div class="row">
			<div class="col-sm-12">
				<div class="page-title-box">
                    <div class="float-start">
						<ul class="nav nav-pills">
							<li class="nav-item"> <a href="<?= base_url($headData->controller . "/index/4") ?>" class="nav-tab btn btn-outline-info <?=($status == 4)?'active':''?>">Plan Request</a> </li>
							<li class="nav-item"> <a href="<?= base_url($headData->controller . "/index/5") ?>" class="nav-tab btn btn-outline-info <?=($status == 5)?'active':''?>">Handover Request</a> </li>
							<li class="nav-item"> <a href="<?= base_url($headData->controller . "/index/6") ?>" class="nav-tab btn btn-outline-info <?=($status == 6)?'active':''?>">Acceptance</a> </li>
						</ul>
					</div>
				</div>
            </div>
			<div class="row">
				<div class="col-12">
					<div class="col-12">
						<div class="card">
							<div class="card-body">
								<div class="table-responsive">
									<table id='requestTable' class="table table-bordered ssTable ssTable-cf" data-url='/getDTRows/<?=$status?>'></table>
								</div>
							</div>
						</div>
					</div>
				</div>
			</div>  
        </div>        
    </div>
</div>

<?php $this->load->view('includes/footer'); ?>