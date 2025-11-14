$(document).ready(function () {
    calculateCRDR();

    $(document).on('click', '.add-item', function () {
		$('#itemForm')[0].reset();
		$("#itemForm input:hidden").val('');
		$('#itemForm #row_index').val("");
        $("#itemForm .error").html();

		setPlaceHolder();
        $("#itemModel").modal("show");
        $("#itemModel .btn-close").show();
        $("#itemModel .btn-save").show();	
        setTimeout(function(){ $("#itemForm #item_id").focus();setPlaceHolder();initSelect2('itemModel'); },500);
	});

    $(document).on('click', '.btn-item-form-close', function () {
		$('#itemForm')[0].reset();
		$("#itemForm input:hidden").val('')
		$('#itemForm #row_index').val("");
		$("#itemForm .error").html("");
        initSelect2('itemModel');
	});   

    $(document).on('click', '.saveItem', function () {

		var fd = $('#itemForm').serializeArray();
		var formData = {};
		$.each(fd, function (i, v) { formData[v.name] = v.value; });
		$("#itemForm .error").html("");

        if (formData.acc_id == "") {
			$(".acc_id").html("Ledger is required.");
		}
        if (formData.cr_dr == "") {
            $(".cr_dr").html("CR DR is required.");
        }
        if (formData.price == "" || formData.price == "0") {
            $(".price").html("Amount is required.");
        }

        var accIds = $(".accIds").map(function () { return $(this).val(); }).get();
        if ($.inArray(formData.acc_id, accIds) >= 0 && formData.row_index == "") {
            $(".acc_id").html("Ledger already added.");
        }

        var errorCount = $('#itemForm .error:not(:empty)').length;
        if (errorCount == 0) {
            var amount = formData.price;
            formData.credit_amount = (formData.cr_dr == 'CR') ? amount : 0;
            formData.debit_amount = (formData.cr_dr == 'DR') ? amount : 0;
            AddRow(formData);
            $("#itemForm .error").html('');
            $('#itemForm')[0].reset();
			initSelect2('itemModel');
            if ($(this).data('fn') == "save") {
                $("#acc_id").focus();
            } else if ($(this).data('fn') == "save_close") {
                $("#itemModel").modal('hide');
            }
        }
	});

});

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
	var transIdInput = $("<input/>", { type: "hidden", name: "itemData["+itemCount+"][id]", value: data.id });
	cell = $(row.insertCell(-1));
	cell.html(data.ledger_name);
	cell.append(accIdInput);
	cell.append(ledgerNameInput);
	cell.append(priceInput);
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

	//Add Button cell.
	cell = $(row.insertCell(-1));
	var btnRemove = $('<button><i class="mdi mdi-trash-can-outline"></i></button>');
	btnRemove.attr("type", "button");
	btnRemove.attr("onclick", "Remove(this);");
	btnRemove.attr("style", "margin-left:4px;");
	btnRemove.attr("class", "btn btn-outline-danger btn-sm waves-effect waves-light");

	var btnEdit = $('<button><i class="mdi mdi-square-edit-outline"></i></button>');
	btnEdit.attr("type", "button");
	btnEdit.attr("onclick", "Edit(" + JSON.stringify(data) + ",this);");
	btnEdit.attr("class", "btn btn-outline-warning btn-sm waves-effect waves-light");

	cell.append(btnEdit);
	cell.append(btnRemove);
	cell.attr("class", "text-center");
	cell.attr("style", "width:10%;");

	calculateCRDR();
	itemCount++;
}


function Edit(data, button) {
	var row_index = $(button).closest("tr").index();
	$("#itemModel").modal("show");
	$("#itemModel .btn-save").hide();
	$.each(data, function (key, value) { $("#itemForm #" + key).val(value); });
	$("#itemForm #row_index").val(row_index);
	$('#itemForm .select2').select2();
}

function Remove(button) {
    var tableId = "journalEntryData";
	//Determine the reference of the Row using the Button.
	var row = $(button).closest("TR");
	var table = $("#"+tableId)[0];
	table.deleteRow(row[0].rowIndex);
	$('#'+tableId+' tbody tr td:nth-child(1)').each(function (idx, ele) {
		ele.textContent = idx + 1;
	});
	var countTR = $('#'+tableId+' tbody tr:last').index() + 1;
	if (countTR == 0) {
		$("#tempItem").html('<tr id="noData"><td colspan="6" align="center">No data available in table</td></tr>');
	}

	calculateCRDR();
};

function calculateCRDR() {
	var creditAmountArray = $(".credit_amount").map(function () { return $(this).val(); }).get();
	var total_cr_amount = 0;
	$.each(creditAmountArray, function () { total_cr_amount += parseFloat(this) || 0; });

	var debitAmountArray = $(".debit_amount").map(function () { return $(this).val(); }).get();
	var total_dr_amount = 0;
	$.each(debitAmountArray, function () { total_dr_amount += parseFloat(this) || 0; });


	$("#total_cr_amount").html(total_cr_amount.toFixed(2));
	$("#total_dr_amount").html(total_dr_amount.toFixed(2));

	var difference = 0;
	difference = parseFloat(parseFloat(total_cr_amount) - parseFloat(total_dr_amount)).toFixed(2);
	difference = Math.abs(difference);
	$("#difference").html(difference);
}

function resPartyDetail(response = ""){
    if(response != ""){
        var partyDetail = response.data.partyDetail;
        $("#ledger_name").val(partyDetail.party_name);        
    }else{
        $("#ledger_name").val("");
    }
}

function resJournalEntry(data,formId){
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