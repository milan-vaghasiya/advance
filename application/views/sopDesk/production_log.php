<?php $this->load->view('includes/header'); ?>
<style>li.nav-item{padding:0px 3px;}.process-tags span{font-size:0.75rem;color:#000;box-shadow: 0px 1px 1px rgba(9, 30, 66, 0.25), 0px 0px 1px 1px rgba(9, 30, 66, 0.13);}.prcList{padding:0.4rem;}</style>
<div class="page-content-tab">
	<div class="container-fluid">
        <div class="row">
            <div class="col-sm-12">
				<div class="page-title-box">
                    <div class="float-start">
                        <ul class="nav nav-pills">
                            <li class="nav-item">
                                <button onclick="statusTab('sopTable','1');" class="nav-tab btn waves-effect waves-light btn-outline-success active mr-2" id="planned_index" style="outline:0px" data-bs-toggle="tab" aria-expanded="false">Regular</button>
                            </li>
                            <li class="nav-item">
                                <button onclick="statusTab('sopTable','2');" class="nav-tab btn waves-effect waves-light btn-outline-success mr-2" id="progress_index" style="outline:0px" data-bs-toggle="tab" aria-expanded="false">Rework</button>
                            </li>
                        </ul>
					</div>
                    
                    <h4 class="card-title text-center"><?=$processData->process_name?></h4>
				</div>
            </div>
		</div>
        <div class="row">
            <div class="col-12">
				<div class="col-12">
					<div class="card">
                        <div class="card-body">
                            <div class="table-responsive">
                                <table id='sopTable' class="table table-bordered ssTable ssTable-cf" data-url='/getLogDTRows/<?=$process_id?>'></table>
                            </div>
                        </div>
					</div>
				</div>
            </div>
        </div>
    </div>
</div>
<?php $this->load->view('includes/footer'); ?>
<script src="<?=base_url()?>assets/js/custom/sop_desk.js?v=<?=time()?>"></script>
