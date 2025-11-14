<?php $this->load->view('includes/header'); ?>
<div class="page-content-tab">
	<div class="container-fluid">
        <div class="row">
            <div class="col-sm-12">
				<div class="page-title-box">
					<div class="card-header">
						<div class="row">     
							<div class="col-md-2 form-group">
								<select id="type" class="form-control select2">
									<option value="1">Purchase</option>
									<option value="2">JobWork</option>
								</select>
							</div>
                            <div class="col-md-2 form-group">
								<select id="party_id" class="form-control select2">
                                    <option value="">Select Supplier</option>
                                    <?=getPartyListOption($partyList)?>
                                </select>
							</div>
							<div class="col-md-2 form-group">
								<select id="item_id" class="form-control select2">
                                    <option value="">Select Item</option>
                                    <?=getItemListOption($itemList)?>
                                </select>
							</div>
							<div class="col-md-2 form-group">
								<select id="process_id" class="form-control select2">
                                    <option value="">Select Process</option>
									<?php
									if(!empty($processList)){
										foreach($processList as $row){
											echo '<option value="'.$row->id.'">'.$row->process_name.'</option>';
										}
									}
									?>
                                </select>
							</div>
							<div class="col-md-4 form-group">
								<div class="input-group">
									<div class="input-group-append">
										<select id="order_by" class="form-control select2">
											<option value="ASC">LOW TO HIGH</option>
											<option value="DESC">HIGH TO LOW</option>
										</select>
									</div>

									<input type="date" id="from_date" class="form-control" max="<?=date('Y-m-d')?>" value="<?=date('Y-m-01')?>" />

									<input type="date" id="to_date" class="form-control" value="<?=date('Y-m-d')?>" />

									<div class="input-group-append">
										<button type="button" class="btn waves-effect waves-light btn-success loadData" title="Load Data">
											<i class="fas fa-sync-alt"></i>
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
		</div>
        <div class="row">
			<div class="col-12">
				<div class="card">
					<div class="card-body reportDiv" style="min-height:75vh">
						<div class="table-responsive">
							<table id='reportTable' class="table table-bordered">
								<thead id="theadData" class="thead-dark">
									<tr class="text-center">
										<th colspan="7">PRICE REVISION HISTORY REPORT</th>
									</tr>									
									<tr class="text-center">
										<th>#</th>
										<th>PO/Ch. Date</th>
										<th>PO/Ch. No.</th>
										<th>Supplier</th>
										<th>Item</th>
										<th>Process</th>
										<th>Rate</th>
									</tr>								
								</thead>
								<tbody id="tbodyData"></tbody>
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
		e.stopImmediatePropagation();e.preventDefault();
		$(".error").html("");
		var valid = 1;
        var type = $("#type").val();
        var party_id = $("#party_id").val();
        var item_id = $("#item_id").val();
        var process_id = $("#process_id").val();
        var order_by = $("#order_by").val();
        var from_date = $('#from_date').val();
	    var to_date = $('#to_date').val();

        if (item_id == "") { $(".item_id").html("Item is required."); valid=0; }
        if (from_date == "") { $(".fromDate").html("From Date is required."); valid=0; }
        if (to_date == "") { $(".toDate").html("To Date is required."); valid=0; }
        if (to_date < from_date) { $(".toDate").html("Invalid Date."); valid=0; }

		if (valid) {
			$.ajax({
				url: base_url + controller + '/getPriceRevisionHistory',
				data: { type:type, party_id:party_id, item_id:item_id, process_id:process_id, order_by:order_by, from_date:from_date, to_date:to_date },
				type: "POST",
				dataType:'json',
				success:function(data){
					$("#reportTable").DataTable().clear().destroy();
					$("#tbodyData").html(data.tbody);
					reportTable();
				}
			});
        }
    });   
});
</script>