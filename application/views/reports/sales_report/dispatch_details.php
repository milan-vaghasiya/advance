<?php $this->load->view('includes/header'); ?>
<div class="page-content-tab">
	<div class="container-fluid">
        <div class="row">
            <div class="col-md-12">
				<div class="page-title-box">
					<div class="float-end" style="width:70%;">
					    <div class="input-group">
							<div class="input-group-append" style="width:20%;">
                                <select id="date_filter" class="form-control select2">
                                    <option value="1">Invoice Wise Date</option>
                                    <option value="2">Dispatch Wise Date</option>
                                </select>
                            </div>
                            <div class="input-group-append" style="width:20%;">
                                <select id="party_id" class="form-control select2">
                                    <option value="">ALL Customer</option>
                                    <?=getPartyListOption($partyList)?>
                                </select>
                            </div>
							<div class="input-group-append" style="width:30%;">
                                <select id="item_id" class="form-control select2">
                                    <option value="">ALL Items</option>
                                    <?php
										if(!empty($itemList)){
											foreach($itemList as $row){
												echo '<option value="'.$row->id.'">'.(!empty($row->item_name) ? '['.$row->item_code.'] '.$row->item_name : $row->item_name).'</option>';
											}
										}
									?>
                                </select>
                            </div>
                            <input type="date" name="from_date" id="from_date" class="form-control" value="<?=getFyDate(date("Y-m-01"));?>"/>                                    
                            <input type="date" name="to_date" id="to_date" class="form-control" value="<?=getFyDate(date("Y-m-t"))?>"/>
                            <div class="input-group-append">
                                <button type="button" class="btn waves-effect waves-light btn-success float-right loadData" title="Load Data">
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
							<table id='dispatchDetailsTable' class="table table-bordered">
								<thead id="theadData" class="thead-dark">
									<tr>
										<th>#</th>
										<th>Inv No.</th>
										<th>Inv Date</th>
										<th>Party Name</th>
										<th>Item Name</th>
										<th>HSN Code</th>
										<th>PRC Number</th>
										<th>Cust. PO. No.</th>
										<th>Delivery Date</th>
										<th>PRC Qty</th>
										<th>Inv Qty</th>
									</tr>
								</thead>
								<tbody id="tbodyData"></tbody>
								<tfoot id="tfootData"> 
									<tr class="thead-dark">
										<th colspan="7"></th>
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


<?php $this->load->view('includes/footer'); ?>
<script>
$(document).ready(function(){
	reportTable();
	loadData();
    $(document).on('click','.loadData',function(){
		loadData();
	}); 
}); 

function loadData(){
    $(".error").html("");
    var valid = 1;
    var party_id = $("#party_id").val();
    var item_id = $('#item_id').val();
    var from_date = $('#from_date').val();
    var to_date = $('#to_date').val();
    var date_filter = $('#date_filter').val();
    if($("#from_date").val() == ""){$(".fromDate").html("From Date is required.");valid=0;}
    if($("#to_date").val() == ""){$(".toDate").html("To Date is required.");valid=0;}
    if($("#to_date").val() < $("#from_date").val()){$(".toDate").html("Invalid Date.");valid=0;}

    var postData = {party_id:party_id,item_id:item_id,from_date:from_date,to_date:to_date,date_filter:date_filter};
    if(valid){
		$.ajax({
			url: base_url + controller + '/getDispatchDetailsData',
			data: postData,
			type: "POST",
			dataType:'json',
			success:function(data){
				$("#dispatchDetailsTable").DataTable().clear().destroy();
				$("#tbodyData").html(data.tbody);
				$("#tfootData").html(data.tfootData);
				reportTable('dispatchDetailsTable');
			}
		});
    }
}
</script>