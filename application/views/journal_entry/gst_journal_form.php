<?php $this->load->view('includes/header'); ?>
<div class="page-content-tab">
	<div class="container-fluid">
        <div class="row">
            <div class="col-12">
				<div class="col-12">
                    <div class="card">
                        <div class="card-body">
							<form autocomplete="off" data-res_function="resGstJournalEntry" id="saveGstJournalEntry">
								<div class="col-md-12">

									<div class="hiddenInput">
										<input type="hidden" name="id" id="id" value="">
										<input type="hidden" name="entry_type" id="entry_type" value="<?=$entry_type?>">
									</div>

									<div class="row form-group">

										<div class="col-md-2 form-group <?=($this->cm_id_count == 1)?"hidden":""?>">
                                            <label for="cm_id">Select Unit</label>
                                            <select name="cm_id" id="cm_id" class="form-control" data-selected_cm_id="<?=(!empty($dataRow->cm_id))?$dataRow->cm_id:""?>">
                                                <?=getCompanyListOptions($companyList,((!empty($dataRow->cm_id))?$dataRow->cm_id:""))?>
                                            </select>
                                        </div>

										<div class="col-md-3">
											<label for="trans_date">Journal Date</label>
                                            <div class="input-group">
											    <input type="date" id="trans_date" name="trans_date" class="form-control fyDates req" value="<?=getFyDate() ?>" />
                                                <button type="button" class="btn waves-effect waves-light btn-success" onclick="loadData()"><i class="fas fa-sync-alt"></i> Load</button>
                                            </div>
										</div>

									</div>
								</div>

								<hr>

								<div class="col-md-12 row">
									<div class="col-md-6">
										<h4>GST Journal Details : </h4>
									</div>
								</div>
								<div class="col-md-12 mt-3">
									<div class="error item_name_error"></div>
									<div class="row form-group">
										<div class="table-responsive ">
											<table id="journalEntryData" class="table table-striped table-borderless" >
												<thead class="thead-dark">
													<tr>
														<th style="width:5%;">#</th>
														<th>Ledger</th>
														<th>CR</th>
														<th>DR</th>
														<th>Remark</th>
													</tr>
												</thead>
												<tbody id="tempItem" class="temp_item">
													<tr id="noData">
														<td colspan="5" class="text-center">No data available in table</td>
													</tr>
												</tbody>
											</table>
										</div>
									</div>
								</div>
							
							</form>
						</div>
                        <div class="card-footer bg-facebook">
                            <div class="col-md-12"> 
                                <button type="button" class="btn waves-effect waves-light btn-success float-right save-form" onclick="customStore({'formId':'saveGstJournalEntry','fnsave':'saveGstJournalEntry'});" ><i class="fa fa-check"></i> Save </button>

                                <button type="button" class="btn btn-secondary press-close-btn btn-close-modal save-form float-right m-r-10" onclick="window.location.href='<?=base_url($headData->controller)?>'"><i class="fa fa-times"></i> Cancel</button>
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
function loadData(){
    var trans_date = $("#trans_date").val();
    var cm_id = $("#cm_id").val();
    var system_code = "'CGSTOPACC','SGSTOPACC','IGSTOPACC','UTGSTOPACC','CESSOPACC','CGSTIPACC','SGSTIPACC','IGSTIPACC','UTGSTIPACC','CESSIPACC','CGSTIPRCMACC','SGSTIPRCMACC','IGSTIPRCMACC'";
    var balance_condition = "<>";

    $(".trans_date").html("");
    if(trans_date == ""){
        $(".trans_date").html("Date is required.");
    }else{
        $.ajax({
            url : base_url + controller + '/getGstLedgers',
            type : 'post',
            data : {to_date:trans_date, cm_id:cm_id, system_code:system_code, balance_condition:balance_condition},
            dataType : 'json',
            async : true
        }).done(function(response){
            $("#tempItem").html('<tr id="noData"><td colspan="5" align="center">No data available in table</td></tr>');
            if(response.data != ""){
                $.each(response.data,function(key,row){
                    row.id = row.row_index = "";
                    row.price = row.cl_bal;
                    row.cr_dr = row.cl_balance_type;
                    row.credit_amount = row.cr_amount;
                    row.debit_amount = row.dr_amount;
                    row.item_remark = "GST JOURNAL";

                    AddRow(row);
                });
            }
        });
    }
}

var itemCount = 0;
function AddRow(data) {
	var tblName = "journalEntryData";

	//Remove blank line.
	$('table#'+tblName+' tr#noData').remove();

	//Get the reference of the Table's TBODY element.
	var tBody = $("#" + tblName + " > TBODY")[0];

	//Add Row.
	if (data.row_index != "") {
		var trRow = data.row_index;
		$("#" + tblName + " tbody tr:eq(" + trRow + ")").remove();
	}
	var ind = (data.row_index == "") ? -1 : data.row_index;
	row = tBody.insertRow(ind);

	//Add index cell
	var countRow = (data.row_index == "") ? ($('#' + tblName + ' tbody tr:last').index() + 1) : (parseInt(data.row_index) + 1);
	var cell = $(row.insertCell(-1));
	cell.html(countRow);
	cell.attr("style", "width:5%;");

	var accIdInput = $("<input/>", { type: "hidden", name: "itemData["+itemCount+"][acc_id]", value: data.acc_id, class:'accIds' });
	var ledgerNameInput = $("<input/>", { type: "hidden", name: "itemData["+itemCount+"][ledger_name]", value: data.ledger_name });
	var priceInput = $("<input/>", { type: "hidden", name: "itemData["+itemCount+"][price]", value: data.price });
	var systemCodeInput = $("<input/>", { type: "hidden", name: "itemData["+itemCount+"][system_code]", value: data.system_code });
	var transIdInput = $("<input/>", { type: "hidden", name: "itemData["+itemCount+"][id]", value: data.id });
	cell = $(row.insertCell(-1));
	cell.html(data.ledger_name);
	cell.append(accIdInput);
	cell.append(ledgerNameInput);
	cell.append(priceInput);
	cell.append(systemCodeInput);
	cell.append(transIdInput);

	var crDrInput = $("<input/>", { type: "hidden", name: "itemData["+itemCount+"][cr_dr]", value: data.cr_dr });
	var creditInput = $("<input/>", { type: "hidden", name: "itemData["+itemCount+"][credit_amount]", value: data.credit_amount, class:'credit_amount' });
	var priceErrorDiv = $("<div></div>", { class: "error price" + itemCount });
	cell = $(row.insertCell(-1));
	cell.html(data.credit_amount);
	cell.append(creditInput);
	cell.append(crDrInput);
	cell.append(priceErrorDiv);

	var debitInput = $("<input/>", { type: "hidden", name: "itemData["+itemCount+"][debit_amount]", value: data.debit_amount, class:'debit_amount' });
	cell = $(row.insertCell(-1));
	cell.html(data.debit_amount);
	cell.append(debitInput);

	var itemRemarkInput = $("<input/>", { type: "hidden", name: "itemData["+itemCount+"][item_remark]", value: data.item_remark });
	cell = $(row.insertCell(-1));
	cell.html(data.item_remark);
	cell.append(itemRemarkInput);
	itemCount++;
}

function resGstJournalEntry(data,formId){
    if(data.status==1){
        $('#'+formId)[0].reset();
        Swal.fire({ icon: 'success', title: data.message});

        window.location = base_url + controller;
    }else{
        if(typeof data.message === "object"){
            $(".error").html("");
            $.each( data.message, function( key, value ) {$("."+key).html(value);});
        }else{
			Swal.fire({ icon: 'error', title: data.message });
        }			
    }	
}
</script>