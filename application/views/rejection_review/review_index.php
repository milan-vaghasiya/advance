<?php $this->load->view('includes/header'); ?>
<div class="page-content-tab">
	<div class="container-fluid">
        <div class="row">
            <div class="col-sm-12">
				<div class="page-title-box">
                    <div class="float-start">
					    <ul class="nav nav-pills">
                            <li class="nav-item">
								<a href="<?= base_url("rejectionReview/pendingReviewIndex/".$source);?>" class="nav-tab btn waves-effect waves-light btn-outline-danger">Pending Review</a>
                            </li>
                            <li class="nav-item">
								<a href="<?= base_url("rejectionReview/reviewedIndex/".$source);?>" class="nav-tab btn waves-effect waves-light btn-outline-success active">Reviewed</a>
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
                            <table id='cftTable' class="table table-bordered ssTable ssTable-cf" data-url='/getReviewDTRows/<?=$source?>'></table>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<?php $this->load->view('includes/footer'); ?>