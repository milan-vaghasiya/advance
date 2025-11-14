<?php $this->load->view('includes/header'); ?>
<div class="page-content-tab">
    <div class="container-fluid bg-container">
        <div class="row">
            <div class="col-sm-12">
                <div class="page-title-box">
                    <div class="row">
                        <div class="col-md-3">
                            <select name="party_id" id="party_id" class="form-control select2 req">
                                <option value="">Select ALL Party</option>
                                <?=getPartyListOption($partyList)?>
                            </select>
                        </div>
                        <div class="col-md-3">
                            <select name="item_id" id="item_id" class="form-control select2 req">
                                <option value="">Select ALL Item</option>
                                <?=getItemListOption($itemList)?>
                            </select>
                        </div>
                        <div class="col-md-2">
                            <select name="type" id="type" class="form-control select2 req">
                                <option value="">Select ALL Type</option>
                                <option value="REJECT">Reject</option>
                                <option value="SHORT">Short</option>
                            </select>
                        </div>
                        <div class="col-md-2">   
                            <input type="date" name="from_date" id="from_date" class="form-control" max="<?=date('Y-m-d')?>" value="<?=date('Y-m-01')?>" />
                            <div class="error fromDate"></div>
                        </div>     
                        <div class="col-md-2">  
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
                                        <thead class="thead-dark">
                                            <tr class="text-center">
                                                <th>#</th>
                                                <th>GRN No.</th>
                                                <th>GRN Date</th>
                                                <th>Party</th>
                                                <th>Item</th>
                                                <th>Qty</th>
                                                <th>Reject Qty</th>
                                                <th>Short Qty</th>
                                            </tr>
                                        </thead>
                                        <tbody id="tbodyData"></tbody>
										
                                        <tfoot id="tfootData"> 
                                            <tr class="thead-dark">
                                            <th colspan="5" class="text-right">Total</th>
                                            <th class="text-center">0</th> 
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
</div>

<?php $this->load->view('includes/footer'); ?>
<script>
$(document).ready(function(){
	reportTable();
    setTimeout(function(){$(".loaddata").trigger('click');},100);

    $(document).on('click','.loaddata',function(e){
		$(".error").html("");
		var valid = 1;
		var item_id = $('#item_id').val();
		var party_id = $('#party_id').val();
		var type = $('#type').val();
		var from_date = $('#from_date').val();
		var to_date = $('#to_date').val();
		if($("#from_date").val() == ""){$(".fromDate").html("From Date is required.");valid=0;}
		if($("#to_date").val() == ""){$(".toDate").html("To Date is required.");valid=0;}
		if($("#to_date").val() < $("#from_date").val()){$(".toDate").html("Invalid Date.");valid=0;}
        
		if(valid)
		{
            $.ajax({
                url: base_url + controller + '/getIncomingRejection',
                data: { item_id:item_id, party_id:party_id, type:type, from_date:from_date, to_date:to_date },
				type: "POST",
				dataType:'json',
				success:function(data){
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