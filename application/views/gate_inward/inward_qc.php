<?php $this->load->view('includes/header'); ?>
<div class="page-content-tab">
	<div class="container-fluid">
        <div class="row">
            <div class="col-sm-12">
				<div class="page-title-box">
                    <div class="float-start">
                        <ul class="nav nav-pills">
                            <li class="nav-item"> 
                                <a href="<?= base_url("gateInward/inwardQC/1/1");?>" class="nav-tab btn waves-effect waves-light btn-outline-danger <?= (($type == 1 && $status == 1) ? "active" : "");?>" style="outline:0px">Pending Inward</a> 
                            </li>
                            <li class="nav-item"> 
                                <a href="<?= base_url("gateInward/inwardQC/1/2");?>" class="nav-tab btn waves-effect waves-light btn-outline-success <?= (($type == 1 && $status == 2) ? "active" : "");?>" style="outline:0px">Inward Done</a> 
                            </li>
                            <li class="nav-item"> 
                                <a href="<?= base_url("gateInward/inwardQC/2/3");?>" class="nav-tab btn waves-effect waves-light btn-outline-primary <?= (($type == 2 && $status == 3) ? "active" : "");?>" style="outline:0px">Pending T.P. QC</a> 
                            </li>
                        </ul>
					</div>					
				</div>
            </div>
		</div>
        <div class="row">
            <div class="col-12">
				<div class="col-12">
					<div class="card">
                        <div class="card-body">
                            <div class="table-responsive">
                                <table id='giTable' class="table table-bordered ssTable ssTable-cf" data-url='/getInwardQcDTRows/<?= $type?>/<?= $status;?>'></table>
                            </div>
                        </div>
					</div>
				</div>
            </div>
        </div>
    </div>
</div>
<?php $this->load->view('includes/footer'); ?>
<script src="<?php echo base_url();?>assets/js/custom/gate-inward.js?V=<?=time()?>"></script>