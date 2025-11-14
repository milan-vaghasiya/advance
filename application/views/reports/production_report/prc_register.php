<?php $this->load->view('includes/header'); ?>
<div class="page-content-tab">
	<div class="container-fluid">
        <div class="row">
            <div class="col-sm-12">
				<div class="page-title-box">
					<div class="row"> 
						<div class="col-md-3">
							<select name="party_id" id="party_id" class="form-control select2 float-right">
								<option value="">Select ALL Customer</option>
								<?=getPartyListOption($partyList);?>
							</select>
						</div>
						<div class="col-md-4">
							<select name="item_id" id="item_id" class="form-control select2 float-right">
								<option value="">Select ALL Product</option>
								<?=getItemListOption($itemList)?>
							</select>
						</div>
						<div class="col-md-2">   
							<input type="date" name="from_date" id="from_date" class="form-control"  value="<?=date('Y-m-01')?>" />
							
							<div class="error fromDate"></div>
						</div>     
						<div class="col-md-3">  
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
											<th style="min-width:25px;">#</th>
											<th style="min-width:80px;">PRC No.</th>
											<th style="min-width:80px;">PRC Date</th>
											<th style="min-width:100px;">Customer</th>
											<th style="min-width:100px;">Sales Order</th>
											<th style="min-width:100px;">Item Name</th>
											<th style="min-width:50px;">Job Qty</th>
											<th style="min-width:50px;">Ok Qty</th>
											<th style="min-width:50px;">Rej. Qty</th>
											<th style="min-width:80px;">Created By</th>
											<th style="min-width:100px;">Remark</th>
										</tr>
                                    </thead>
                                    <tbody id="tbodyData"></tbody>
									<tfoot id="tfootData" class="thead-dark">
                                        <tr>
                                            <th colspan="6" class="text-right">Total</th>
                                            <th>0</th>
                                            <th>0</th>
                                            <th>0</th>
                                            <th></th>
                                            <th></th>
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
		var party_id = $('#vendor_id').val();
		var item_id = $('#item_id').val();
        var from_date = $('#from_date').val();
	    var to_date = $('#to_date').val();
        if($("#from_date").val() == ""){$(".fromDate").html("From Date is required.");valid=0;}
	    if($("#to_date").val() == ""){$(".toDate").html("To Date is required.");valid=0;}
	    if($("#to_date").val() < $("#from_date").val()){$(".toDate").html("Invalid Date.");valid=0;}

		if(valid){
            $.ajax({
                url: base_url + controller + '/getPrcRegisterData',
                data: {party_id:party_id,item_id:item_id,from_date:from_date,to_date:to_date},
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