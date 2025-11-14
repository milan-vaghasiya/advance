<?php $this->load->view('includes/header'); ?>
<div class="page-content-tab">
	<div class="container-fluid">
        <div class="row">
            <div class="col-12">
                <div class="card">
                    <div class="card-header">
                        <div class="row"> 
							<div class="col-md-2">
                                <select name="vendor_id" id="vendor_id" class="form-control select2 float-right">
                                    <option value="">Select ALL Vendor</option>
                                    <?=getPartyListOption($vendorList);?>
                                </select>
                                <div class="error vendor_id"></div>
							</div>
							<div class="col-md-2">
                                <select name="item_id" id="item_id" class="form-control select2 float-right">
                                    <option value="">Select ALL Product</option>
									<?=getItemListOption($itemList)?>
                                </select>
                                <div class="error item_id"></div>
							</div>
							<div class="col-md-2">
								<select id="process_id" name="item_iprocess_idd" class="form-control select2">
									<option value="">Select ALL Process</option>
									<?php
										foreach($processList as $row):
											echo '<option value="'.$row->id.'">'.$row->process_name.'</option>';
										endforeach;
									?>
								</select>
                                <div class="error process_id"></div>
							</div>
							<div class="col-md-2">
								<select id="date_filter" name="date_filter" class="form-control select2">
									<option value="Challan">Challan Wise Date</option>
									<option value="Inward">Inward Wise Date</option>
								</select>
							</div>
							<div class="col-md-2">   
                                <input type="date" name="from_date" id="from_date" class="form-control"  value="<?=date('Y-m-01')?>" />
                                
                                <div class="error fromDate"></div>
                            </div>     
                            <div class="col-md-2">  
                                <div class="input-group">
                                    <input type="date" name="to_date" id="to_date" class="form-control" value="<?=date('Y-m-d')?>" />
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
                    <div class="card-body reportDiv" style="min-height:75vh">
                        <div class="table-responsive">
                            <table id='reportTable' class="table table-bordered" >
								<thead id="theadData" class="thead-dark">
									<tr>
										<th colspan="11" style="text-align: center;">Outward Details</th>
										<th colspan="10" style="text-align: center;">Inward Details</th>
									</tr>
									<tr class="text-center">
										<th style="min-width:50px;">#</th>
										<th style="min-width:50px;">Challan No.</th>
										<th style="min-width:100px;">Challan Date</th>
										<th style="min-width:50px;">PRC No.</th>
										<th style="min-width:100px;">Vendor</th>
										<th style="min-width:100px;">Product</th>
										<th style="min-width:180px;">Process</th>
										<th style="min-width:80px;">Qty.</th>
										<th style="min-width:80px;">Pending Qty.</th>
										<th style="min-width:80px;">Rate</th>
										<th style="min-width:80px;">Approx Rate</th>
										
										<th style="min-width:100px;">Date</th>
										<th style="min-width:100px;">Vendor</th>
										<th style="min-width:100px;">Bill No.</th>
										<th style="min-width:50px;">Vendor Challan No.</th>
										<th style="min-width:80px;">Ok Qty.</th>
										<th style="min-width:80px;">Rej. Qty.</th>
										<th style="min-width:80px;">W.P. Return</th>
										<th style="min-width:80px;">Scrap Wt.</th>
										<th style="min-width:80px;">Rate</th> 
										<th style="min-width:80px;">Amount</th>
									</tr>

								</thead>
								<tbody id="tbodyData"></tbody>
								<tfoot id="tfootData" class="thead-dark">
								    <th colspan="7" class="text-right">Total</th>
                                    <th></th>                                    
									<th></th>
                                    <th></th>
                                    <th></th>
                                    <th></th>
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
		var vendor_id = $('#vendor_id').val();
		var item_id = $('#item_id').val();
		var process_id = $('#process_id').val();
		var from_date = $('#from_date').val();
		var to_date = $('#to_date').val();
		var date_filter = $('#date_filter').val();
		var valid = 1;
		if($("#from_date").val() == ""){$(".fromDate").html("From Date is required.");valid=0;}
		if($("#to_date").val() == ""){$(".toDate").html("To Date is required.");valid=0;}
		if($("#to_date").val() < $("#from_date").val()){$(".toDate").html("Invalid Date.");valid=0;}
		if(valid)
		{
			$.ajax({
				url: base_url + controller + '/getOutSourceRegister',
				data: { vendor_id:vendor_id, item_id:item_id, from_date:from_date, to_date:to_date, process_id:process_id, date_filter:date_filter },
				type: "POST",
				dataType:'json',
				success:function(data){
                    $("#reportTable").DataTable().clear().destroy();
					$("#tbodyData").html(data.tblData);
					$("#tfootData").html(data.tfoot);
					reportTable();
				}
			});
		}
	});
});

</script>