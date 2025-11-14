<?php $this->load->view('includes/header'); ?>
<div class="page-content-tab">
	<div class="container-fluid">
        <div class="row">
			<div class="col-sm-12">
				<div class="page-title-box">
                    <ul class="nav nav-pills">
                        <li class="nav-item">
                            <button onclick="statusTab('pdiTable','0');" class="nav-tab btn waves-effect waves-light btn-outline-danger active" id="pending_pdi" style="outline:0px" data-bs-toggle="tab" aria-expanded="false">Pending</button>
                        </li>
                        <li class="nav-item">
                            <button onclick="statusTab('pdiTable','3');" class="nav-tab btn waves-effect waves-light btn-outline-success" id="complete_pdi" style="outline:0px" data-bs-toggle="tab" aria-expanded="false">PDI Done</button>
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
                            <table id='pdiTable' class="table table-bordered ssTable" data-url='/getDTRows/'></table>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<?php $this->load->view('includes/footer'); ?>
