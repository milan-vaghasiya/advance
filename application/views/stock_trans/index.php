<?php $this->load->view('includes/header'); ?>
<div class="page-content-tab">
	<div class="container-fluid">
        <div class="row">
            <div class="col-sm-12">
				<div class="page-title-box">
                    <div class="float-start">
                        <a href="<?= base_url($headData->controller."/stockRegister") ?>" class="btn btn-outline-primary">Stock Register</a>
                        <a href="<?= base_url($headData->controller) ?>" class="btn btn-outline-primary active">FG Stock Inward</a>
                    </div>
					<div class="float-end">
                        <?php
                            $addParam = "{'modal_id' : 'bs-right-md-modal', 'call_function':'addStock', 'form_id' : 'addStock', 'title' : 'Gate Stock'}";
                        ?>
                        <button type="button" class="btn waves-effect waves-light btn-outline-dark permission-write float-right press-add-btn" onclick="modalAction(<?=$addParam?>);"><i class="fa fa-plus"></i> Add Stock</button>
					</div>
                    <h4 class="card-title text-center">FG Stock Inward</h4>
				</div>
            </div>
		</div>
        <div class="row">
            <div class="col-12">
				<div class="col-12">
					<div class="card">
                        <div class="card-body">
                            <div class="table-responsive">
                                <table id='itemStockTable' class="table table-bordered ssTable ssTable-cf" data-url='/getDTRows'></table>
                            </div>
                        </div>
					</div>
				</div>
            </div>
        </div>
    </div>
</div>
<?php $this->load->view('includes/footer'); ?>