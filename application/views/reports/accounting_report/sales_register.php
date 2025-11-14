<?php $this->load->view('includes/header'); ?>
<div class="page-content-tab">
	<div class="container-fluid">
        <div class="row">
            <div class="col-sm-2">
				<h4 class="card-title pageHeader"><?=$pageHeader?></h4>
            </div>
            <div class="col-sm-3">
				<select id="party_id" name="party_id" class="form-control bill_wise select2">
					<option value="">ALL Party</option>
					<?php
						if(!empty($partyList)){
							foreach($partyList as $row){
								echo '<option value="'.$row->id.'">'.(!empty($row->party_code) ? '['.$row->party_code.'] '.$row->party_name : $row->party_name).'</option>';
							}
						}
					?>
				</select>
            </div>
			<div class="col-sm-3">
				<select id="item_id" name="item_id" class="form-control item_wise select2" disabled="disabled">
					<option value="">ALL Item</option>
					<?php
						if(!empty($itemList)){
							foreach($itemList as $row){
								echo '<option value="'.$row->id.'">'.(!empty($row->item_code) ? '['.$row->item_code.'] '.$row->item_name : $row->item_name).'</option>';
							}
						}
					?>
				</select>
            </div>
			<div class="col-sm-4">
				<div class="float-end">
					<div class="input-group">
						<div class="input-group-append" style="width:20%;">
							<select id="report_type" name="report_type" class="form-control select2">
								<option value="1">Bill Wise</option>
								<option value="2">Item Wise</option>
							</select>
						</div>
						<div class="input-group-append" style="width:20%;">
							<select id="state_code" name="state_code" class="form-control select2">
								<option value="">All States</option>
								<option value="1">IntraState</option>
								<option value="2">InterState</option>
							</select>
						</div>
						<input type="date" name="from_date" id="from_date" class="form-control" value="<?=$startDate?>" />
						<input type="date" name="to_date" id="to_date" class="form-control" value="<?=$endDate?>" />
						<div class="input-group-append">
							<button type="button" class="btn waves-effect waves-light btn-success float-right refreshReportData loadData" title="Load Data">
								<i class="fas fa-sync-alt"></i> Load
							</button>
						</div>
					</div>
					<div class="error fromDate"></div>
					<div class="error toDate"></div>
				</div>
			</div>
        </div>
		<div class="row">
			<div class="col-12">
				<div class="col-12">
					<div class="card">
						<div class="card-body reportDiv" style="min-height:75vh">
							<div class="table-responsive">
								<table id='reportTable' class="table table-bordered">
									<thead id="theadData" class="thead-dark">
										<tr>
											<th>#</th>
											<th>Inv Date</th>
											<th>Inv No.</th>
											<th>Party Name</th>
											<th>Gst No.</th>
											<th>Total Amount</th>
											<th>Disc. Amount</th>
											<th>Taxable Amount</th>
											<th>CGST Amount</th>
											<th>SGST Amount</th>
											<th>IGST Amount</th>
											<th>Other Amount</th>
											<th>Net Amount</th>
										</tr>
									</thead>
									<tbody id="tbodyData"></tbody>
									<tfoot id="tfootData" class="thead-dark">
										<tr>
											<th colspan="5" class="text-right">Total</th>
											<th>0</th>
											<th>0</th>
											<th>0</th>
											<th>0</th>
											<th>0</th>
											<th>0</th>
											<th>0</th>
											<th>0</th>
										</tr>
									</tfoot>
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
	reportTable();
    setTimeout(function(){$(".loadData").trigger('click');},500);
    
    $(document).on('click','.loadData',function(e){
		$(".error").html("");
		var valid = 1;
        var report_type = $("#report_type").val();
		var state_code = $('#state_code').val();
        var from_date = $('#from_date').val();
	    var to_date = $('#to_date').val();
	    var party_id = $('#party_id').val();
	    var to_date = $('#to_date').val();
	    var item_id = $('#item_id').val();
        if($("#from_date").val() == ""){$(".fromDate").html("From Date is required.");valid=0;}
	    if($("#to_date").val() == ""){$(".toDate").html("To Date is required.");valid=0;}
	    if($("#to_date").val() < $("#from_date").val()){$(".toDate").html("Invalid Date.");valid=0;}

		if(valid){
            $.ajax({
                url: base_url + controller + '/getSalesRegisterData',
                data: {report_type:report_type,state_code:state_code,from_date:from_date,to_date:to_date,vou_name_s:["'Sale'","'GInc'"],party_id:party_id,item_id:item_id},
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
		
	$(document).on("change","#report_type",function(){
		var value = $(this).val();
		if(value == 1){
			$(".item_wise").attr("disabled",true);
		}else{
			$(".item_wise").attr("disabled",false);
		}
	});
});
</script>