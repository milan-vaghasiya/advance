<?php $this->load->view('includes/header'); ?>
<div class="page-content-tab">
	<div class="container-fluid">
		<div class="row">
            <div class="col-sm-12">
				<div class="page-title-box">
					<div class="row">
						<div class="col-md-4">
							
						</div>
						<div class="col-md-6">
							<select name="prc_id" id="prc_id" class="form-control select2">
								<option value="">Select PRC</option>
								<?php   
								if(!empty($prcList)):
									foreach($prcList as $row): 
										echo '<option value="'.$row->id.'">'.$row->prc_number.' [ '.$row->item_name.' ] '.'</option>';
									endforeach; 
								endif;
								?>
							</select>
						</div>
						<div class="col-md-2">
							<div class="input-group">
								<div class="input-group-append">
									<button type="button" class="btn btn-block waves-effect waves-light btn-success loadData" data-pdf="0" title="Load Data" >
										<i class="fas fa-sync-alt"></i> Load
									</button>
									</div>

									<div class="input-group-append">
                                    <button type="button" class="btn btn-block waves-effect waves-light btn-warning float-right loadData" data-pdf="1" title="PDF" >
										<i class="fas fa-sync-alt"></i> PDF
									</button>
								</div>
							</div>
							
						</div>
					</div>
				</div>
			</div>
		</div>
				    
	
        <div class="row">
            <div class="col-12">
                <div class="card">
                    <div class="card-body reportDiv" style="min-height:75vh">
						<div class="col-md-12">
							<h5>PRC Detail : </h5>
							<div class="table-responsive" >
								<table class="table table-bordered">
									<thead>
										<tr class="bg-light text-center">
											<th>PRC  Number</th>
											<th>PRC  Date</th>
											<th>Product</th>
											<th>Prc Qty</th>
										</tr>
									</thead>
									<tbody  id="prcDetail" class="text-center">
										<tr>
											<th colspan="4" class="text-center">No Data available</th>
										</tr>
									</tbody>
								</table>
							</div>
						</div>
						<div class="col-md-12">
							<h5>Material Cost : </h5>
							<div class="table-responsive">
								<table class="table table-bordered">
									<thead  class="bg-light text-center">
										<tr>
											<th>Material Name</th>
											<th>Qty</th>
											<th>RM Rate </th>
											<th>Costing/Pcs </th>
											<th>Total Cost</th>
										</tr>
									</thead>
									<tbody id="materialDetail">
										<tr>
											<th colspan="5" class="text-center">No Data available</th>
										</tr>
									</tbody>
								</table>
							</div>
						</div>
                        <div class="col-md-12">
							<h5>Process Cost : </h5>
							<div class="table-responsive">
								<table id='reportTable' class="table table-bordered">
									<thead id="theadData" class="bg-light text-center">
										<tr>
											<th>#</th>
											<th>Process Name</th>
											<th>OK Qty.</th>
											<th>Rej Qty.</th>
											<th>Scrap Qty.</th>
											<th>Costing/Pcs</th>
											<th>Total Costing</th>
											<th>Scrap Cost</th>                                            
										</tr>
									</thead>
									<tbody id="tbodyData"></tbody>
									<tfoot class="bg-light" id="tfootData">
										<tr>
											<th colspan="6" class="text-right">Total Costing</th>
											<th  class="text-center">0</th>
											<th  class="text-center">0</th>
										</tr>
									</tfoot>
								</table>
							</div>
						</div>

						<div class="col-md-12">
							<h5>Final Cost : </h5>
							<div class="table-responsive">
								<table id='finalCostDetail' class="table table-bordered">
									<tr>
										<th style="width:15%" class="bg-light">Material Cost</th>
										<td></td>
									</tr>
									<tr>
										<th class="bg-light">Process Cost</th>
										<td></td>
									</tr>
									<tr>
										<th class="bg-light">Scrap Recovery</th>
										<td></td>
									</tr>
									<tr>
										<th class="bg-light">Final Cost</th>
										<td></td>
									</tr>
								</table>
							</div>
						</div>
                    </div>
				</div>
            </div>
		</div>
    </div>
</div>


<?php $this->load->view('includes/footer'); ?>
<script>
$(document).ready(function(){
	//reportTable();
	$(document).on('click','.loadData',function(){
		$(".error").html("");
		var valid = 1;
		var prc_id = $('#prc_id').val();
        if($("#prc_id").val() == ""){$(".prc_id").html("PRC is required.");valid=0;}
		
		if(valid)
		{
			var is_pdf = $(this).data('pdf');
			var postData = { prc_id:prc_id,is_pdf:is_pdf };
			if(is_pdf == 0){
				$.ajax({
					url: base_url + controller + '/getJobCostingData',
					data: postData,
					type: "POST",
					dataType:'json',
					success:function(data){
						// $("#reportTable").DataTable().clear().destroy();
						$("#tbodyData").html(data.tbody);
						$("#tfootData").html(data.tfoot);
						$("#prcDetail").html(data.prcDetail);
						$("#materialDetail").html(data.materialDetail);
						$("#finalCostDetail").html(data.finalCostDetail);
						// reportTable();
					}
				});
			}else{
				var url = base_url + controller + '/getJobCostingData/' + encodeURIComponent(window.btoa(JSON.stringify(postData)));
                window.open(url);
			}
			
		}
	});
});
</script>