<?php $this->load->view('includes/header'); ?>
<div class="page-content-tab">
	<div class="container-fluid">
        <div class="row">
            <div class="col-sm-12">
				<div class="page-title-box">
					<div class="float-end" style="width:90%;">
					    <div class="input-group">
                                <div class="input-group-append" style="width:15%;">
                                <select name="status" id="status" class="form-control select2 req">
									<option value="">ALL Status</option>
									<option value="0,3">Pending</option>
									<option value="1">Completed</option>
									<option value="2">Short Close</option>
								</select>
                                </div>
                                <div class="input-group-append" style="width:25%;">
                                    <select id="party_id" class="form-control select2">
                                        <option value="">ALL Customer</option>
                                        <?=getPartyListOption($partyList)?>
                                    </select>
                                </div>
                                <div class="input-group-append" style="width:30%;">
                                    <select id="item_id" class="form-control select2">
                                        <option value="">ALL Product</option>
                                        <?=getItemListOption($itemList)?>
                                    </select>
                                </div>
                                <input type="date" name="from_date" id="from_date" class="form-control" value="<?=$startDate?>" style="width:10%;"/>                                    
                                <input type="date" name="to_date" id="to_date" class="form-control" value="<?=$endDate?>" style="width:10%;"/>
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
                                            <th>Order Date</th>
                                            <th>Order No.</th>
                                            <th>Cust. PO. No.</th>
                                            <th>Customer Name</th>
                                            <th>Item Name</th>
                                            <th>Order Qty.</th>
                                            <th>Order Value</th>
                                            <th>Dispatch Date</th>
                                            <th>Inv. Date</th>
                                            <th>Inv. No.</th>
                                            <th>Inv. Qty.</th>
                                            <th>Deviation Days</th>
                                            <th>Pending Qty.</th>
                                            <th>Pending Value</th>
                                        </tr>
                                    </thead>
                                    <tbody id="tbodyData"></tbody>
									
                                    <tfoot class="thead-dark" id="tfootData">
                                        <tr>
                                            <th colspan="6">Total</th>
                                            <th>0</th>
                                            <th>0</th>
                                            <th colspan="3"></th>
                                            <th class="text-center">0</th> 
                                            <th></th>
                                            <th class="text-center">0</th>
                                            <th class="text-center">0</th>
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
        var status = $("#status").val();
        var party_id = $("#party_id").val();
        var item_id = $("#item_id").val();
        var from_date = $('#from_date').val();
	    var to_date = $('#to_date').val();
        if($("#from_date").val() == ""){$(".fromDate").html("From Date is required.");valid=0;}
	    if($("#to_date").val() == ""){$(".toDate").html("To Date is required.");valid=0;}
	    if($("#to_date").val() < $("#from_date").val()){$(".toDate").html("Invalid Date.");valid=0;}

		if(valid){
            $.ajax({
                url: base_url + controller + '/getOrderMonitoringData',
                data: {party_id:party_id,item_id:item_id,from_date:from_date,to_date:to_date,status:status},
				type: "POST",
				dataType:'json',
				success:function(data){
                    $("#reportTable").DataTable().clear().destroy();
					$("#tbodyData").html(data.tbody);
					$("#tfootData").html(data.tfoot);
					reportTable();
                }
            });
        }
    });   
});
</script>