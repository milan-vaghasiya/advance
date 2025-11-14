<?php $this->load->view('includes/header'); ?>
<div class="page-content-tab">
	<div class="container-fluid">
		<div class="row">
			<div class="col-sm-12">
				<div class="page-title-box">
                    <div class="row">
                        <div class="col-md-8">
                            <h4 class="card-title">Leave Request</h4>
                        </div>
                        <div class="col-md-4">
                            <?php
                                $addParam = "{'modal_id' : 'bs-right-lg-modal', 'call_function':'addLeave', 'form_id' : 'addLeave', 'title' : 'Add Leave'}";
                            ?>
                            <button type="button" class="btn waves-effect waves-light btn-outline-dark permission-write float-right press-add-btn" onclick="modalAction(<?=$addParam?>);"><i class="fa fa-plus"></i> Add Leave</button>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        <div class="row">
            <div class="col-12">
                <div class="card">
                    <div class="card-body">
                        <div class="table-responsive">
                            <table id='leaveTable' class="table table-bordered ssTable ssTable-cf" data-url="/getDTRows"></table>
                        </div>
                    </div>
                </div>
            </div>
        </div>      
    </div>
</div>
<?php $this->load->view('includes/footer'); ?>
<script src="<?php echo base_url();?>assets/js/custom/leave.js?v=<?=time()?>"></script>