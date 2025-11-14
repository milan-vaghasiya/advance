<?php $this->load->view('includes/header'); ?>
<div class="page-content-tab">
	<div class="container-fluid">
		<div class="row">
			<div class="col-sm-12">
				<div class="page-title-box">
					<div class="float-end">
                        <?php
                            $addParam = "{'modal_id' : 'bs-right-md-modal', 'call_function':'addCustomField', 'form_id' : 'addCustomField', 'title' : 'Add Custom Field'}";
                        ?>
						<button type="button" class="btn waves-effect waves-light btn-outline-dark float-right permission-write press-add-btn" onclick="modalAction(<?=$addParam?>);"><i class="fa fa-plus"></i> Add Field</button>
					</div>
					<h4 class="page-title">Custom Fields</h4>
				</div>
			</div>
		</div>
        <div class="row">
            <div class="col-12">
                <div class="card">
                    <div class="card-body">
                        <div class="table-responsive">
                            <table id='custmFieldTable' class="table table-bordered ssTable" data-url='/getDTRows'></table>
                        </div>
                    </div>
                </div>
            </div>
        </div>        
    </div>
</div>
<?php $this->load->view('includes/footer'); ?>
