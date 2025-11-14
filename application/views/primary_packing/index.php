<?php $this->load->view('includes/header'); ?>
<div class="page-content-tab">
	<div class="container-fluid">
		<div class="row">
			<div class="col-sm-12">
				<div class="page-title-box">
					<div class="float-end">
                        <?php
                            $addParam = "{'modal_id' : 'bs-right-xl-modal', 'call_function':'addPacking', 'form_id' : 'addPacking', 'title' : 'Add Packing','fnsave':'save','js_store_fn':'customStore'}";
                        ?>
                        <button type="button" class="btn waves-effect waves-light btn-outline-dark permission-write float-right press-add-btn" onclick="modalAction(<?=$addParam?>);"><i class="fa fa-plus"></i> Add Packing</button>
						
					</div>
					<ul class="nav nav-pills">
						<li class="nav-item"> 
							<button onclick="statusTab('packingTable',1);" class="nav-tab btn waves-effect waves-light btn-outline-info active" style="outline:0px" data-bs-toggle="tab" aria-expanded="false">Packed</button> 
						</li>
						<li class="nav-item"> 
							<button onclick="statusTab('packingTable',2);" class="nav-tab btn waves-effect waves-light btn-outline-info" style="outline:0px" data-bs-toggle="tab" aria-expanded="false">Completed</button> 
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
                            <table id='packingTable' class="table table-bordered ssTable ssTable-cf" data-url='/getDTRows'></table>
                        </div>
                    </div>
                </div>
            </div>
        </div>        
    </div>
</div>
<?php $this->load->view('includes/footer'); ?>