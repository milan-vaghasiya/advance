<?php $this->load->view('includes/header'); ?>
<div class="page-content-tab">
	<div class="container-fluid">
        <div class="row">
            <div class="col-sm-12">
				<div class="page-title-box">
					<div class="row">						
						<div class="col-md-4 form-group">
							<label for="operator_id">Operator</label>
							<select id="operator_id" class="form-control select2" multiple>
								<?php
									foreach($employeeList as $row):
										echo '<option value="'.$row->id.'">'.$row->emp_name.'</option>';
									endforeach;
								?>
							</select>
						</div>
						
						<div class="col-md-4 form-group">
							<label for="item_id">Product</label>
							<select id="item_id" class="form-control select2" multiple>
								<?=getItemListOption($itemList)?>
							</select>
						</div>
						
						<div class="col-md-4 form-group">
							<label for="machine_id">Machine</label>
							<select id="machine_id" class="form-control select2" multiple>
								<?=getItemListOption($machineList)?>
							</select>
						</div>

						<div class="col-md-3 form-group">
							<label for="process_id">Process</label>
							<select id="process_id" class="form-control select2" multiple>
								<?php
									foreach($processList as $row):
										echo '<option value="'.$row->id.'">'.$row->process_name.'</option>';
									endforeach;
								?>
							</select>
						</div>
						
						<div class="col-md-2 form-group">
							<label for="report_type">Report Type</label>
							<select id="report_type" class="form-control select2">
								<option value="1">Summary</option>
								<option value="2">Date Wise</option>
							</select>
						</div>

						<div class="col-md-2 form-group">
							<label for="process_by">Process By</label>
							<select id="process_by" class="form-control select2">
								<option value="All">Select ALL</option>
								<option value="1">In House</option>
								<option value="3">Outsource</option>
							</select>
						</div>

						<div class="col-md-2 form-group"> 
							<label for="from_date">From Date</label>
							<input type="date" name="from_date" id="from_date" class="form-control"  value="<?=date('Y-m-01')?>" />							
							<div class="error fromDate"></div>
						</div> 
						 
						<div class="col-md-3 form-group"> 
							<label for="to_date">To Date</label> 
							<div class="input-group">
								<input type="date" name="to_date" id="to_date" class="form-control" value="<?=$endDate?>"  style="width:20%;"/>
								<div class="input-group-append">
									<button type="button" class="btn waves-effect waves-light btn-success float-right loadData" title="Load Data">
										<i class="fas fa-sync-alt"></i> Load
									</button>
								</div>
							</div>
							<div class="error toDate"></div>
						</div>     
					</div>  
				</div>
			</div>
		</div>
        <div class="row">
            <div class="col-12">
                <div class="card">
                    <div class="card-body reportDiv" style="min-height:75vh">
                        <div class="table-responsive">
                            <table id='reportTable' class="table table-bordered" >
								<thead id="theadData" class="thead-dark">
									<tr class="text-center">
                                        <th>#</th>
                                        <th>Operator Name</th>
                                        <th>Machine/Department/Vendor</th>
                                        <th>PRC No.</th>
                                        <th>Shift</th>
                                        <th>Production Time<br>(Min.)</th>
                                        <th>Product Name</th>
                                        <th>Material Grade</th>
                                        <th>Batch No</th>
                                        <th>Batch Qty</th>
                                        <th>Total Prod Qty</th>
                                        <th>Set up</th>
                                        <th>After Wt/PC</th>
                                        <th>Before Wt/PC</th>
                                        <th>Total Prod. Wt.</th>
                                        <th>Rejection<br/>(NOS)</th>
                                        <th>Rejection<br/>(KGS)</th>
                                        <th>Total OK<br/>Prod Qty<br/>NOS</th>
                                        <th>Scrap<br/>Wt/Pc</th>
                                        <th>Total Scrap</th>
                                        <th>Rejection (%)</th>
									</tr>
								</thead>
								<tbody id="tbodyData"></tbody>
								<tfoot id="tfootData" class="thead-dark">
								    <th colspan="9" class="text-right">Total</th>
                                    <th></th>
                                    <th></th>
									<th colspan="4"></th>
									<th></th>
									<th></th>
									<th></th>
									<th></th>
									<th></th>
									<th></th>
								</tfoot>
							</table>
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
	reportTable();

	$(document).on('click','.loadData',function(e){
		var operator_id = $('#operator_id').val();
		var item_id = $('#item_id').val();
		var machine_id = $('#machine_id').val();
		var process_by = $('#process_by').val();
		var process_id = $('#process_id').val();
		var report_type = $('#report_type').val();
		var to_date = $('#to_date').val();
		var from_date = $('#from_date').val();
		var valid = 1;
		if($("#from_date").val() == ""){$(".fromDate").html("From Date is required.");valid=0;}
	    if($("#to_date").val() == ""){$(".toDate").html("To Date is required.");valid=0;}
	    if($("#to_date").val() < $("#from_date").val()){$(".toDate").html("Invalid Date.");valid=0;}
		if(valid)
		{
			$.ajax({
				url: base_url + controller + '/getProductionLogSheet',
				data: { operator_id:operator_id, item_id:item_id, machine_id:machine_id, process_id:process_id, to_date:to_date, from_date:from_date, process_by:process_by,report_type:report_type},
				type: "POST",
				dataType:'json',
				success:function(data){
                    $("#reportTable").DataTable().clear().destroy();
					$("#theadData").html(data.thead);
					$("#tbodyData").html(data.tbody);
					$("#tfootData").html(data.tfoot);
					reportTable();
				}
			});
		}
	});
});
</script>