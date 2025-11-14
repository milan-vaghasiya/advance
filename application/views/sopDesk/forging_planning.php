<?php $this->load->view('includes/header'); ?>
<div class="page-content-tab">
	<div class="container-fluid">
        <div class="row">
            <div class="col-sm-12">
				<div class="page-title-box">
                    <div class="float-end" style="width:90%;">
					    <div class="input-group">
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
                        <div class="card-body">
                            <div class="table-responsive">
                                <table id='reportTable' class="table table-bordered" >
                                    <thead class="thead-info">
                                        <tr>
                                            <th>Product</th>
                                            <th>PO No.</th>
                                            <th>Raw Material</th>
                                            <th>CUT Wt/ Length</th>
                                            <th>Pending Forging Qty</th>
                                            <th>Total Pro. WT (KG)</th>
                                            <th>Batch No</th>
                                            <th>Cutting Qty</th>
                                            <th>Forging Done</th>
                                            <th>SO Delivery Date</th>
                                        </tr>
                                    </thead>
                                    <tbody id="tbodyData">
                                    </tbody>
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
    reportTable();
    setTimeout(function(){$(".loadData").trigger('click');},500);
    
    $(document).on('click','.loadData',function(e){
		$(".error").html("");
		var valid = 1;
        var party_id = $("#party_id").val();
        var item_id = $("#item_id").val();
        var from_date = $('#from_date').val();
	    var to_date = $('#to_date').val();
        if($("#from_date").val() == ""){$(".fromDate").html("From Date is required.");valid=0;}
	    if($("#to_date").val() == ""){$(".toDate").html("To Date is required.");valid=0;}
	    if($("#to_date").val() < $("#from_date").val()){$(".toDate").html("Invalid Date.");valid=0;}

		if(valid){
            $.ajax({
                url: base_url + controller + '/getSoPlanningData',
                data: {party_id:party_id,item_id:item_id,from_date:from_date,to_date:to_date},
				type: "POST",
				dataType:'json',
				success:function(data){
                    $("#reportTable").DataTable().clear().destroy();
					$("#tbodyData").html(data.tbodyData);
					reportTable();
                }
            });
        }
    });   
</script>