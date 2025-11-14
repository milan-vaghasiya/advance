var itemCount = 0;
$(document).ready(function(){
	setTimeout(function(){$("#party_id").trigger('change');},500);
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
        if (formData.price == "" || parseFloat(formData.price) == 0) {
            $(".price").html("price is required.");
        }
        if (formData.net_weight == "" || parseFloat(formData.net_weight) == 0) {
            $(".net_weight").html("Net Weight is required.");
        }
        if (formData.gross_weight == "" || parseFloat(formData.gross_weight) == 0) {
            $(".gross_weight").html("Net Weight is required.");
        }

        var errorCount = $('#itemForm .error:not(:empty)').length;

		if (errorCount == 0) {
            formData.disc_per = (parseFloat(formData.disc_per) > 0)?formData.disc_per:0;
            var qty = formData.qty;
            var amount = 0; var taxable_amount = 0; var disc_amt = 0; var net_amount = 0; 

            if (formData.disc_per == "" && formData.disc_per == "0") {
                taxable_amount = amount = parseFloat(parseFloat(qty) * parseFloat(formData.price)).toFixed(2);
            } else {
                amount = parseFloat(parseFloat(qty) * parseFloat(formData.price)).toFixed(2);
                disc_amt = parseFloat((amount * parseFloat(formData.disc_per)) / 100).toFixed(2);
                taxable_amount = parseFloat(amount - disc_amt).toFixed(2);
            }

            formData.qty = parseFloat(formData.qty).toFixed(2);
            formData.disc_amount = disc_amt;
            formData.amount = amount;
            formData.taxable_amount = taxable_amount;
            formData.net_amount = taxable_amount;

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

    $(document).on('change','#unit_id',function(){
		$("#unit_name").val("");
		if($(this).val()){ $("#unit_name").val($("#unit_id :selected").data('unit')); }	
	});

	$(document).on('change','#hsn_code',function(){
		$("#gst_per").val(($("#hsn_code :selected").data('gst_per') || 0));
	});
});

function AddRow(data) {
    var tblName = "comInvItems";

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
    var itemTypeInput = $("<input/>", { type: "hidden", name: "itemData["+itemCount+"][item_type]", value: data.item_type });
    var gstPerInput = $("<input/>", { type: "hidden", name: "itemData["+itemCount+"][gst_per]", value: data.gst_per });
    var palletQtyInput = $("<input/>", { type: "hidden", name: "itemData["+itemCount+"][pallet_qty]", class:"palletQty", value: data.pallet_qty });
    var netWeightInput = $("<input/>", { type: "hidden", name: "itemData["+itemCount+"][net_weight]", class:"netWeight", value: data.net_weight });
    var grossWeightInput = $("<input/>", { type: "hidden", name: "itemData["+itemCount+"][gross_weight]", class:"grossWeight", value: data.gross_weight });
    cell = $(row.insertCell(-1));
    cell.html(((data.item_code != "")?"["+data.item_code+"] ":"")+data.item_name);
    cell.append(idInput, formEnteryTypeInput, refIdInput, itemIdInput, itemNameInput, itemCodeInput, itemTypeInput, gstPerInput, palletQtyInput, netWeightInput, grossWeightInput);

    var hsnCodeInput = $("<input/>", { type: "hidden", name: "itemData["+itemCount+"][hsn_code]", value: data.hsn_code });
	cell = $(row.insertCell(-1));
	cell.html(data.hsn_code);
	cell.append(hsnCodeInput);

    var boxQtyInput = $("<input/>", { type: "hidden", name: "itemData["+itemCount+"][total_box]", class:"boxQty", value: data.total_box });
	cell = $(row.insertCell(-1));
	cell.html(data.total_box);
	cell.append(boxQtyInput);

    var packingQtyInput = $("<input/>", { type: "hidden", name: "itemData["+itemCount+"][packing_qty]",  value: data.packing_qty });
	cell = $(row.insertCell(-1));
	cell.html(data.packing_qty);
	cell.append(packingQtyInput);

    var qtyInput = $("<input/>", { type: "hidden", name: "itemData["+itemCount+"][qty]", class:"itemQty", value: data.qty });
	var qtyErrorDiv = $("<div></div>", { class: "error qty" + itemCount });
	cell = $(row.insertCell(-1));
	cell.html(data.qty);
	cell.append(qtyInput, qtyErrorDiv);

	var unitIdInput = $("<input/>", { type: "hidden", name: "itemData["+itemCount+"][unit_id]", value: data.unit_id });
	var unitNameInput = $("<input/>", { type: "hidden", name: "itemData["+itemCount+"][unit_name]", value: data.unit_name });
	cell = $(row.insertCell(-1));
	cell.html(data.unit_name);
	cell.append(unitIdInput, unitNameInput);    

    var priceInput = $("<input/>", { type: "hidden", name: "itemData["+itemCount+"][price]", value: data.price });
	cell = $(row.insertCell(-1));
	cell.html(data.price);
	cell.append(priceInput);

    var discPerInput = $("<input/>", { type: "hidden", name: "itemData["+itemCount+"][disc_per]", value: data.disc_per });
    var discAmtInput = $("<input/>", { type: "hidden", name: "itemData["+itemCount+"][disc_amount]", class:"discAmount", value: data.disc_amount });
	cell = $(row.insertCell(-1));
	cell.html(data.disc_amount + '(' + data.disc_per + '%)');
	cell.append(discPerInput, discAmtInput);

    var amountInput = $("<input/>", { type: "hidden", name: "itemData["+itemCount+"][amount]", class:"amount", value: data.amount });
    var taxableAmountInput = $("<input/>", { type: "hidden", name: "itemData["+itemCount+"][taxable_amount]", class:"taxable_amount",value: data.taxable_amount });
    var netAmountInput = $("<input/>", { type: "hidden", name: "itemData["+itemCount+"][net_amount]", class:"netAmount", value: data.net_amount });
	cell = $(row.insertCell(-1));
	cell.html(data.net_amount);
	cell.append(amountInput, taxableAmountInput, netAmountInput);

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
	claculateColumn();
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
    var tableId = "comInvItems";
	//Determine the reference of the Row using the Button.
	var row = $(button).closest("TR");
	var table = $("#"+tableId)[0];
	table.deleteRow(row[0].rowIndex);
	$('#'+tableId+' tbody tr td:nth-child(1)').each(function (idx, ele) {
		ele.textContent = idx + 1;
	});
	var countTR = $('#'+tableId+' tbody tr:last').index() + 1;
	if (countTR == 0) {
		$("#tempItem").html('<tr id="noData"><td colspan="12" align="center">No data available in table</td></tr>');
	}

	calculateItemSummary();
	claculateColumn();
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

    var discAmountArray = $(".discAmount").map(function () { return $(this).val(); }).get();
	var discAmountSum = 0;
	$.each(discAmountArray, function () { discAmountSum += parseFloat(this) || 0; });
	$("#total_discount").html(discAmountSum.toFixed(2));

    var netAmountArray = $(".netAmount").map(function () { return $(this).val(); }).get();
	var netAmountSum = 0;
	$.each(netAmountArray, function () { netAmountSum += parseFloat(this) || 0; });
	$("#total_net_amount").html(netAmountSum.toFixed(2));
}

function resPartyDetail(response = ""){
    if(response != ""){
        var partyDetail = response.data.partyDetail;
        $("#party_name").val(partyDetail.party_name);
        $("#currency").val(partyDetail.currency);        
    }else{
        $("#party_name").val("");
        $("#currency").val("");
    }
}

function resItemDetail(response = ""){
    if(response != ""){
        var itemDetail = response.data.itemDetail;
        $("#itemForm #item_id").val(itemDetail.id);
        $("#itemForm #item_code").val(itemDetail.item_code);
        $("#itemForm #item_name").val(itemDetail.item_name);
        $("#itemForm #item_type").val(itemDetail.item_type);
		$("#itemForm #packing_qty").val(itemDetail.packing_standard);
        $("#itemForm #unit_id").val(itemDetail.unit_id);
        $("#itemForm #unit_name").val(itemDetail.uom);
        $("#itemForm #hsn_code").val(itemDetail.hsn_code);
        $("#itemForm #gst_per").val(itemDetail.gst_per);
    }else{
		$("#itemForm #item_id").val("");
        $("#itemForm #item_code").val("");
        $("#itemForm #item_name").val("");
        $("#itemForm #item_type").val("");
		$("#itemForm #packing_qty").val("");
        $("#itemForm #unit_id").val("");
        $("#itemForm #unit_name").val("");
        $("#itemForm #hsn_code").val("");
        $("#itemForm #gst_per").val("");
    }
	initSelect2('itemModel');
}

function resComInv(data,formId){
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