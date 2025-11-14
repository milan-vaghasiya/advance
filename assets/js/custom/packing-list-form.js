var itemCount = 0;

$(document).ready(function(){

    $(document).on('click', '.add-item', function () {
		$('#itemForm')[0].reset();
		$("#itemForm input:hidden").val('');
		$('#itemForm #row_index').val("");
        $("#itemForm .error").html();

		var party_id = $('#party_id').val();
		$(".party_id").html("");
		$("#itemForm #row_index").val("");

		if(party_id){
			setPlaceHolder();
			$("#itemModel").modal("show");
			$("#itemModel .btn-close").show();
			$("#itemModel .btn-save").show();
			setTimeout(function(){ $("#itemForm #item_id").focus();setPlaceHolder();initSelect2('itemModel'); },500);
		}else{
            $(".party_id").html("Party name is required."); $("#itemModel").modal('hide');
        }
	});

    $(document).on('click', '.saveItem', function () {
		var fd = $('#itemForm').serializeArray();
		var formData = {};
		$.each(fd, function (i, v) {
			formData[v.name] = v.value;
		});

		$("#itemForm .error").html("");

        if (formData.item_id == "") {
			$(".item_id").html("Item Name is required.");
		}

        if (formData.qty == "" || parseFloat(formData.qty) == 0) {
            $(".qty").html("Qty is required.");
        }

        if(formData.item_desc == ""){
            $(".item_desc").html("Box name is required.");
        }

        if (formData.net_weight == "" || parseFloat(formData.net_weight) == 0) {
            $(".net_weight").html("Net Weight is required.");
        }

        if (formData.gross_weight == "" || parseFloat(formData.gross_weight) == 0) {
            $(".gross_weight").html("Net Weight is required.");
        }

        var errorCount = $('#itemForm .error:not(:empty)').length;

		if (errorCount == 0) {
            AddRow(formData);
            $('#itemForm')[0].reset();
			
            $("#itemForm input:hidden").val('')
            $('#itemForm #row_index').val("");
            initSelect2('itemModel');

            if ($(this).data('fn') == "save") {
                $("#item_id").focus();
            } else if ($(this).data('fn') == "save_close") {
                $("#itemModel").modal('hide');
            }
        }
	});

    $(document).on('click', '.btn-item-form-close', function () {
		$('#itemForm')[0].reset();
		$("#itemForm input:hidden").val('')
		$('#itemForm #row_index').val("");
		$("#itemForm .error").html("");
        initSelect2('itemModel');
	});

	/*$(document).on('keyup change',"#itemForm #qty",function(){
		var qty = $(this).val() || 0;
		var box_pcs = $("#itemForm #packing_qty").val() || 1;
		var totalBox = parseFloat(parseFloat(qty) / parseFloat(box_pcs)).toFixed(1);
		$("#itemForm #total_box").val(totalBox);
	});

	$(document).on('keyup change',"#itemForm #total_box",function(){
		var totalBox = $(this).val() || 0;
		var box_pcs = $("#itemForm #packing_qty").val() || 1;
		var qty = parseFloat(parseFloat(box_pcs) * parseFloat(totalBox)).toFixed(3);
		$("#itemForm #qty").val(qty);
	});*/
});

function AddRow(data) {
    var tblName = "packingListItems";

    //Remove blank line.
	$('table#'+tblName+' tr#noData').remove();

	//Get the reference of the Table's TBODY element.
	var tBody = $("#" + tblName + " > TBODY")[0];

	//Add Row.
	if (data.row_index != "") {
		var trRow = data.row_index;
		//$("tr").eq(trRow).remove();
		$("#" + tblName + " tbody tr:eq(" + trRow + ")").remove();
	}

	var ind = (data.row_index == "") ? -1 : data.row_index;
	row = tBody.insertRow(ind);
	$(row).attr('id',itemCount);

    //Add index cell
	var countRow = (data.row_index == "") ? ($('#' + tblName + ' tbody tr:last').index() + 1) : (parseInt(data.row_index) + 1);
	var cell = $(row.insertCell(-1));
	cell.html(countRow);
	cell.attr("style", "width:5%;");

    var idInput = $("<input/>", { type: "hidden", name: "itemData["+itemCount+"][id]", value: data.id });
    var formEnteryTypeInput = $("<input/>", { type: "hidden", name: "itemData["+itemCount+"][from_entry_type]", value: data.from_entry_type });
	var refIdInput = $("<input/>", { type: "hidden", name: "itemData["+itemCount+"][ref_id]", value: data.ref_id });
    var itemIdInput = $("<input/>", { type: "hidden", name: "itemData["+itemCount+"][item_id]", class:"item_id", value: data.item_id });
	var itemNameInput = $("<input/>", { type: "hidden", name: "itemData["+itemCount+"][item_name]", value: data.item_name });
    var itemCodeInput = $("<input/>", { type: "hidden", name: "itemData["+itemCount+"][item_code]", value: data.item_code });
	var unitNameInput = $("<input/>", { type: "hidden", name: "itemData["+itemCount+"][unit_name]", value: data.unit_name });
    var itemTypeInput = $("<input/>", { type: "hidden", name: "itemData["+itemCount+"][item_type]", value: data.item_type });
    cell = $(row.insertCell(-1));
    cell.html(((data.item_code != "")?"["+data.item_code+"] ":"")+data.item_name);
    cell.append(idInput, formEnteryTypeInput, refIdInput, itemIdInput, itemNameInput, itemCodeInput, unitNameInput, itemTypeInput);

    var qtyInput = $("<input/>", { type: "hidden", name: "itemData["+itemCount+"][qty]", class:"itemQty", value: data.qty });
    var packingQtyInput = $("<input/>", { type: "hidden", name: "itemData["+itemCount+"][packing_qty]",  value: data.packing_qty });
	var qtyErrorDiv = $("<div></div>", { class: "error qty" + itemCount });
	cell = $(row.insertCell(-1));
	cell.html(data.qty);
	cell.append(qtyInput, packingQtyInput, qtyErrorDiv);

	var boxNameInput = $("<input/>", { type: "hidden", name: "itemData["+itemCount+"][item_desc]", value: data.item_desc });
	cell = $(row.insertCell(-1));
	cell.html(data.item_desc);
	cell.append(boxNameInput);

    var boxQtyInput = $("<input/>", { type: "hidden", name: "itemData["+itemCount+"][total_box]", class:"boxQty", value: data.total_box });
	cell = $(row.insertCell(-1));
	cell.html(data.total_box);
	cell.append(boxQtyInput);

    var palletQtyInput = $("<input/>", { type: "hidden", name: "itemData["+itemCount+"][pallet_qty]", class:"palletQty", value: data.pallet_qty });
	cell = $(row.insertCell(-1));
	cell.html(data.pallet_qty);
	cell.append(palletQtyInput);

    var netWeightInput = $("<input/>", { type: "hidden", name: "itemData["+itemCount+"][net_weight]", class:"netWeight", value: data.net_weight });
	cell = $(row.insertCell(-1));
	cell.html(data.net_weight);
	cell.append(netWeightInput);

    var grossWeightInput = $("<input/>", { type: "hidden", name: "itemData["+itemCount+"][gross_weight]", class:"grossWeight", value: data.gross_weight });
	cell = $(row.insertCell(-1));
	cell.html(data.gross_weight);
	cell.append(grossWeightInput);

    var itemRemarkInput = $("<input/>", { type: "hidden", name: "itemData["+itemCount+"][item_remark]", value: data.item_remark});
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

    calculateItemSummary();

	itemCount++;
}

function Edit(data, button) {
	var row_index = $(button).closest("tr").index();
	$("#itemModel").modal("show");
	$("#itemModel .btn-save").hide();
	
	$.each(data, function (key, value) {
		$("#itemForm #" + key).val(value);
	});

	initSelect2('itemModel');
	$("#itemForm #row_index").val(row_index);
	$("#itemForm #qty").trigger('change');
}

function Remove(button) {
    var tableId = "packingListItems";

	//Determine the reference of the Row using the Button.
	var row = $(button).closest("TR");
	var table = $("#"+tableId)[0];
	table.deleteRow(row[0].rowIndex);

	$('#'+tableId+' tbody tr td:nth-child(1)').each(function (idx, ele) {
		ele.textContent = idx + 1;
	});

	var countTR = $('#'+tableId+' tbody tr:last').index() + 1;
	if (countTR == 0) {
		$("#tempItem").html('<tr id="noData"><td colspan="10" align="center">No data available in table</td></tr>');
	}

	calculateItemSummary();
}

function calculateItemSummary(){
    var itemQtyArray = $(".itemQty").map(function () { return $(this).val(); }).get();
	var itemQtySum = 0;
	$.each(itemQtyArray, function () { itemQtySum += parseFloat(this) || 0; });
	$("#total_item_qty").html(itemQtySum.toFixed(2));
	
    var boxQtyArray = $(".boxQty").map(function () { return $(this).val(); }).get();
	var boxQtySum = 0;
	$.each(boxQtyArray, function () { boxQtySum += parseFloat(this) || 0; });
	$("#total_box_qty").html(boxQtySum.toFixed(2));

    var palletQtyArray = $(".palletQty").map(function () { return $(this).val(); }).get();
	var palletQtySum = 0;
	$.each(palletQtyArray, function () { palletQtySum += parseFloat(this) || 0; });
	$("#total_pallet_qty").html(palletQtySum.toFixed(2));

    var netWeightArray = $(".netWeight").map(function () { return $(this).val(); }).get();
	var netWeightSum = 0;
	$.each(netWeightArray, function () { netWeightSum += parseFloat(this) || 0; });
	$("#total_net_weight").html(netWeightSum.toFixed(3));

    var grossWeightArray = $(".grossWeight").map(function () { return $(this).val(); }).get();
	var grossWeightSum = 0;
	$.each(grossWeightArray, function () { grossWeightSum += parseFloat(this) || 0; });
	$("#total_gross_weight").html(grossWeightSum.toFixed(3));
}

function resPartyDetail(response = ""){
    if(response != ""){
        var partyDetail = response.data.partyDetail;
        $("#party_name").val(partyDetail.party_name);
    }else{
        $("#party_name").val("");
    }
}

function resItemDetail(response = ""){
    if(response != ""){
        var itemDetail = response.data.itemDetail;
        $("#itemForm #item_id").val(itemDetail.id);
        $("#itemForm #item_code").val(itemDetail.item_code);
        $("#itemForm #item_name").val(itemDetail.item_name);
        $("#itemForm #item_type").val(itemDetail.item_type);
        $("#itemForm #unit_name").val(itemDetail.uom);
		$("#itemForm #packing_qty").val(itemDetail.packing_standard);
    }else{
		$("#itemForm #item_id").val("");
        $("#itemForm #item_code").val("");
        $("#itemForm #item_name").val("");
        $("#itemForm #item_type").val("");
		$("#itemForm #unit_name").val("");
		$("#itemForm #packing_qty").val("");
    }
	initSelect2('itemModel');
}

function resPackingList(data,formId){
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