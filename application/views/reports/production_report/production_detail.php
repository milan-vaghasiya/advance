<?php $this->load->view('includes/header'); ?>
<div class="page-content-tab">
	<div class="container-fluid">
		<div class="row">
            <div class="col-sm-12">
				<div class="page-title-box">
					<div class="float-end">
						<div class="input-group">
						</div>
					</div>
					<h4 class="card-title pageHeader">Production Detail</h4>
				</div>
            </div>
		</div>
        <div class="row">
            <div class="col-12">				
                <div class="card">
                    <div class="card-body reportDiv" style="min-height:75vh">
                        <div class="table-responsive">
                            <table id='reportTable' class="table table-bordered">
								<thead class="thead-dark" id="theadData">
									<tr>
										<th style="min-width:25px;">#</th>
										<th style="min-width:25px;">Start Time</th>
										<th style="min-width:25px;">End Time</th>
										<th style="min-width:25px;">Prc No.</th>
										<th style="min-width:25px;">Process</th>
										<th style="min-width:25px;">Machine </th>
										<th style="min-width:25px;">Operator</th>
										<th style="min-width:25px;">Shift</th>
										<th style="min-width:25px;">Total Production Time</th>
										<th style="min-width:25px;">Ideal Qty</th>
										<th style="min-width:25px;">Actual Qty</th>
										<th style="min-width:25px;">Ok Qty</th>
										<th style="min-width:25px;">Reject Qty</th>
										<th style="min-width:25px;">Lost Reason</th>
									</tr>
								</thead>
								<tbody id="tbodyData">
                                <?php echo $tbodyData; ?>
                                </tbody>
							</table>
                        </div>
                    </div>
                </div>
            </div>
        </div>        
    </div>
</div>


<?php $this->load->view('includes/footer'); ?>
