<?php $this->load->view('includes/header'); ?>
<div class="page-content-tab">
	<div class="container-fluid">
        <div class="row">
            <div class="col-sm-12">
				<div class="page-title-box">                    
					<div class="float-end">
                        <?php
                            $addParam = "{'postData':{'ref_id' : ".$parent_id."},'modal_id' : 'bs-right-md-modal', 'call_function':'addItemCategory', 'form_id' : 'addItemCategory', 'title' : 'Add Item Category'}";
                        ?>
                        <button type="button" class="btn waves-effect waves-light btn-outline-dark float-right press-add-btn" onclick="modalAction(<?=$addParam?>);"><i class="fa fa-plus"></i> Add Category</button>
					</div>       
                    <h4 class="card-title"><?='<a href="' . base_url("itemCategory/list/" . $ref_id) . '">' .$categoryName . '</a>'?></h4>             
				</div>
            </div>
		</div>
        <div class="row">
            <div class="col-12">
				<div class="col-12">
					<div class="card">
                        <div class="card-body">
                            <div class="table-responsive">
                                <table id='itemCategoryTable' class="table table-bordered ssTable ssTable-cf" data-url='/getDTRows/<?=$parent_id?>'></table>
                            </div>
                        </div>
					</div>
				</div>
            </div>
        </div>
    </div>
</div>

<?php $this->load->view('includes/footer'); ?>