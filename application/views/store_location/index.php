<?php $this->load->view('includes/header'); ?>
<div class="page-content-tab">
	<div class="container-fluid">
        <div class="row">
            <div class="col-sm-12">
				<div class="page-title-box">
					<div class="float-end">
                        <?php
                            $addParam = "{'postData':{'ref_id':".$parent_id.",'location':'".$locationName."'},'modal_id' : 'bs-right-md-modal', 'call_function':'addStoreLocation', 'form_id' : 'addStoreLocation', 'title' : 'Add Store Location'}";
                        ?>
                        <button type="button" class="btn waves-effect waves-light btn-outline-dark permission-write float-right press-add-btn" onclick="modalAction(<?=$addParam?>);"><i class="fa fa-plus"></i> Add Location</button>
					</div>
                    <h4 class="card-title"><?='<a href="' . base_url("storeLocation/list/" . $ref_id) . '">' .$storeName . '</a>'?></h4>
				</div>
            </div>
		</div>
        <div class="row">
            <div class="col-12">
				<div class="col-12">
					<div class="card">
                        <div class="card-body">
                            <div class="table-responsive">
                                <table id='storeLocationTable' class="table table-bordered ssTable ssTable-cf" data-url='/getDTRows/<?=$parent_id?>'></table>
                            </div>
                        </div>
					</div>
				</div>
            </div>
        </div>
    </div>
</div>

<?php $this->load->view('includes/footer'); ?>