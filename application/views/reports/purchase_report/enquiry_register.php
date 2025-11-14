<?php $this->load->view('includes/header'); ?>
<div class="page-content-tab">
    <div class="container-fluid bg-container">
        <div class="row">
            <div class="col-sm-12">
                <div class="page-title-box">
                    <div class="row">
                        <div class="col-md-3">
                            <select name="item_id" id="item_id" class="form-control select2 req">
                                <option value="">Select Item</option>
                                <?=getItemListOption($itemList)?>
                            </select>
                        </div>
                        <div class="col-md-3">
                            <select name="party_id" id="party_id" class="form-control select2 req">
                                <option value="">Select Party</option>
                                    <?=getPartyListOption($partyList)?>
                                </select>
                        </div>
                        <div class="col-md-3">   
                            <input type="date" name="from_date" id="from_date" class="form-control" max="<?=date('Y-m-d')?>" value="<?=date('Y-m-01')?>" />
                            <div class="error fromDate"></div>
                        </div>     
                        <div class="col-md-3">  
                            <div class="input-group">
                                <input type="date" name="to_date" id="to_date" class="form-control" value="<?=date('Y-m-d')?>" />
                                <div class="input-group-append ml-2">
                                    <button type="button" class="btn waves-effect waves-light btn-success float-right loaddata" title="Load Data">
                                        <i class="fas fa-sync-alt"></i> Load
                                    </button>
                                </div>
                            </div>
                            <div class="error toDate"></div>
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
                                            <tr class="text-center">
                                                <th colspan="14">Enquiry Register</th>
                                            </tr>
                                            <tr class="text-center">
                                                <th rowspan="2">#</th>
                                                <th rowspan="2">Enq. No.</th>
                                                <th rowspan="2">Enq. Date</th>
                                                <th rowspan="2">Supplier Name</th>
                                                <th rowspan="2">Item Description</th>
                                                <th rowspan="2">Qty.</th>
                                                <th colspan="7">Quotation Detail</th>
                                            </tr>
                                            <tr class="text-center">
                                                <th>Qtn. No.</th>
                                                <th>Qtn. Date</th>
                                                <th>MOQ</th>
                                                <th>Price</th>
                                                <th>Feasible</th>
                                                <th>Lead Time</th>
                                                <th>Approve By</th>
                                            </tr>
                                        </thead>
                                        <tbody id="tbodyData"> </tbody>
                                        <tfoot id="tfootData"> 
                                            <tr class="thead-dark">
                                            <th colspan="5" class="text-right">Total</th>
                                            <th class="text-center">0</th> 
                                            <th colspan="2"></th>
                                            <th class="text-center">0</th> 
                                            <th class="text-center">0</th>
                                            <th colspan="3"></th>
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
</div>

<?php $this->load->view('includes/footer'); ?>
<script>
$(document).ready(function(){
    initModalSelect();
	reportTable();
    $(document).on('click','.loaddata',function(e){
		$(".error").html("");
		var valid = 1;
		var item_id = $('#item_id').val();
		var party_id = $('#party_id').val();
		var from_date = $('#from_date').val();
		var to_date = $('#to_date').val();
		if($("#from_date").val() == ""){$(".fromDate").html("From Date is required.");valid=0;}
		if($("#to_date").val() == ""){$(".toDate").html("To Date is required.");valid=0;}
		if($("#to_date").val() < $("#from_date").val()){$(".toDate").html("Invalid Date.");valid=0;}
		if(valid)
		{
            $.ajax({
                url: base_url + controller + '/getEnquiryRegister',
                data: {item_id:item_id,party_id:party_id,from_date:from_date, to_date:to_date},
				type: "POST",
				dataType:'json',
				success:function(data){
                    console.log(data);
                    $("#reportTable").dataTable().fnDestroy();
					$("#tbodyData").html(data.tbody);
					$("#tfootData").html(data.tfoot);
					reportTable();
                }
            });
        }
    });   
});
</script>