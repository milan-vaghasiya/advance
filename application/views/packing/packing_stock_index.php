<?php $this->load->view('includes/header'); ?>
<div class="page-content-tab">
	<div class="container-fluid">
		<div class="row">
			<div class="col-sm-12">
				<div class="page-title-box">
					<div class="float-end">
                        <?php
                            $addParam = "{'modal_id' : 'bs-right-lg-modal', 'call_function':'addPacking', 'form_id' : 'addPacking', 'title' : 'Add Packing','fnsave':'save'}";
                        ?>
					</div>
					<ul class="nav nav-pills">
						<li class="nav-item"> 
							<a href="<?= base_url($headData->controller."/packingStock");?>" class="nav-tab btn waves-effect waves-light btn-outline-info active" style="outline:0px">Pending for Packing</a> 
						</li>
						<li class="nav-item"> 
							<a href="<?= base_url($headData->controller);?>" class="nav-tab btn waves-effect waves-light btn-outline-info" style="outline:0px">Packing</a> 
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
                            <table id='packingTable' class="table table-bordered ssTable" data-url='/getPackingStockDTRows'></table>
                        </div>
                    </div>
                </div>
            </div>
        </div>        
    </div>
</div>
<?php $this->load->view('includes/footer'); ?>