<?php $this->load->view('includes/header'); ?>
<div class="page-content-tab">
	<div class="container-fluid">
        <div class="row">
            <div class="col-sm-12">
				<div class="page-title-box">
					<div class="float-end">
                        <button type="button" class="btn waves-effect waves-light btn-success float-right save-form" onclick="customStore({'formId':'addCurrency','fnsave':'save'});" ><i class="fa fa-check"></i> Save Currency</button>
					</div>
				</div>
            </div>
		</div>
        <div class="row">
            <div class="col-12">
				<div class="col-12">
					<div class="card">
                        <div class="card-body">
                            <form id="addCurrency">
                                <div class="table-responsive">
                                    <table id='hsnTable' class="table table-bordered ssTable ssTable-cf" data-url='/getDTRows'></table>
                                </div>
                            </form>
                        </div>
					</div>
				</div>
            </div>
        </div>
    </div>
</div>
<?php $this->load->view('includes/footer'); ?>