var itemCount = 0;
$(document).ready(function(){
	setTimeout(function(){$("#party_id").trigger('change');},500);
	$(".ledgerColumn").hide();
	$(".summary_desc").attr('style','width: 60%;');

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
            $(".price").html("Price is required.");
        }

        var errorCount = $('#itemForm .error:not(:empty)').length;

		if (errorCount == 0) {
            var itemData = calculateItem(formData);

            AddRow(itemData);
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
		$("#gst_per").select2();
	});

	//on change Currency Rate.
	$(document).on('keyup change','#inrrate',function(){
		var inrrate = $(this).val();
		inrrate = (parseFloat(inrrate) > 0)?inrrate:1;		
		itemCount = 0;
		var i = 0;
		$.each($("#customInvoiceItems tbody tr"),function(){
			var countRow = $(this).attr('id');
			
			if($("#customInvoiceItems tbody tr:eq("+i+") input[name='itemData["+countRow+"][org_price]']").val()){
				formData = {};				

				formData.id = $("#customInvoiceItems tbody tr:eq("+i+") input[name='itemData["+countRow+"][id]']").val();
				formData.item_id = $("#customInvoiceItems tbody tr:eq("+i+") input[name='itemData["+countRow+"][item_id]']").val();
				formData.item_name = $("#customInvoiceItems tbody tr:eq("+i+") input[name='itemData["+countRow+"][item_name]']").val();
				formData.from_entry_type = $("#customInvoiceItems tbody tr:eq("+i+") input[name='itemData["+countRow+"][from_entry_type]']").val();
				formData.ref_id = $("#customInvoiceItems tbody tr:eq("+i+") input[name='itemData["+countRow+"][ref_id]']").val();
				formData.item_code = $("#customInvoiceItems tbody tr:eq("+i+") input[name='itemData["+countRow+"][item_code]']").val();
				formData.item_type = $("#customInvoiceItems tbody tr:eq("+i+") input[name='itemData["+countRow+"][item_type]']").val();
				formData.pallet_qty = $("#customInvoiceItems tbody tr:eq("+i+") input[name='itemData["+countRow+"][pallet_qty]']").val();
    			formData.net_weight = $("#customInvoiceItems tbody tr:eq("+i+") input[name='itemData["+countRow+"][net_weight]']").val();
    			formData.gross_weight = $("#customInvoiceItems tbody tr:eq("+i+") input[name='itemData["+countRow+"][gross_weight]']").val();
				formData.hsn_code = $("#customInvoiceItems tbody tr:eq("+i+") input[name='itemData["+countRow+"][hsn_code]']").val();
				formData.qty = $("#customInvoiceItems tbody tr:eq("+i+") input[name='itemData["+countRow+"][qty]']").val();
				formData.packing_qty = $("#customInvoiceItems tbody tr:eq("+i+") input[name='itemData["+countRow+"][packing_qty]']").val();
				formData.total_box = $("#customInvoiceItems tbody tr:eq("+i+") input[name='itemData["+countRow+"][total_box]']").val();
				formData.unit_id = $("#customInvoiceItems tbody tr:eq("+i+") input[name='itemData["+countRow+"][unit_id]']").val();
				formData.unit_name = $("#customInvoiceItems tbody tr:eq("+i+") input[name='itemData["+countRow+"][unit_name]']").val();

				var org_price = $("#customInvoiceItems tbody tr:eq("+i+") input[name='itemData["+countRow+"][org_price]']").val();
				formData.org_price = (org_price != "" || parseFloat(org_price) > 0)?org_price:0;
				formData.price = parseFloat((parseFloat(org_price) * parseFloat(inrrate))).toFixed(2);
				
				formData.disc_per = $("#customInvoiceItems tbody tr:eq("+i+") input[name='itemData["+countRow+"][disc_per]']").val();
				formData.gst_per = $("#customInvoiceItems tbody tr:eq("+i+") input[name='itemData["+countRow+"][gst_per]']").val();
				formData.item_remark = $("#customInvoiceItems tbody tr:eq("+i+") input[name='itemData["+countRow+"][item_remark]']").val();
				
				formData.row_index = ""+i+"";

                var itemData = calculateItem(formData);
				AddRow(itemData); 	
				i++;			
			}
		});
	});
});

function AddRow(data) {
    var tblName = "customInvoiceItems";

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
    var itemIdInput = $("<input/>", { type: "hidden", name: "itemData["+itemCount+"][item_id]", class:"item_id", value: data.item_id });
	var itemNameInput = $("<input/>", { type: "hidden", name: "itemData["+itemCount+"][item_name]", value: data.item_name });
    var formEnteryTypeInput = $("<input/>", { type: "hidden", name: "itemData["+itemCount+"][from_entry_type]", value: data.from_entry_type });
	var refIdInput = $("<input/>", { type: "hidden", name: "itemData["+itemCount+"][ref_id]", value: data.ref_id });
    var itemCodeInput = $("<input/>", { type: "hidden", name: "itemData["+itemCount+"][item_code]", value: data.item_code });
    var itemTypeInput = $("<input/>", { type: "hidden", name: "itemData["+itemCount+"][item_type]", value: data.item_type });
    cell = $(row.insertCell(-1));
    cell.html(data.item_name);
    cell.append(idInput, itemIdInput, itemNameInput, formEnteryTypeInput, refIdInput, itemCodeInput, itemTypeInput);

    var hsnCodeInput = $("<input/>", { type: "hidden", name: "itemData["+itemCount+"][hsn_code]", value: data.hsn_code });
	cell = $(row.insertCell(-1));
	cell.html(data.hsn_code);
	cell.append(hsnCodeInput);

    var qtyInput = $("<input/>", { type: "hidden", name: "itemData["+itemCount+"][qty]", class:"item_qty", value: data.qty });
    var psInput = $("<input/>", { type: "hidden", name: "itemData["+itemCount+"][packing_qty]", value: data.packing_qty });
    var tbInput = $("<input/>", { type: "hidden", name: "itemData["+itemCount+"][total_box]", value: data.total_box });console.log(data.total_box);
    var palletQtyInput = $("<input/>", { type: "hidden", name: "itemData["+itemCount+"][pallet_qty]", class:"palletQty", value: data.pallet_qty });
    var netWeightInput = $("<input/>", { type: "hidden", name: "itemData["+itemCount+"][net_weight]", class:"netWeight", value: data.net_weight });
    var grossWeightInput = $("<input/>", { type: "hidden", name: "itemData["+itemCount+"][gross_weight]", class:"grossWeight", value: data.gross_weight });
	var qtyErrorDiv = $("<div></div>", { class: "error qty" + itemCount });
	cell = $(row.insertCell(-1));
	cell.html(data.qty);
	cell.append(qtyInput, psInput, tbInput, palletQtyInput, netWeightInput, grossWeightInput, qtyErrorDiv);

    var unitIdInput = $("<input/>", { type: "hidden", name: "itemData["+itemCount+"][unit_id]", value: data.unit_id });
	var unitNameInput = $("<input/>", { type: "hidden", name: "itemData["+itemCount+"][unit_name]", value: data.unit_name });
	cell = $(row.insertCell(-1));
	cell.html(data.unit_name);
	cell.append(unitIdInput, unitNameInput);

    var priceInput = $("<input/>", { type: "hidden", name: "itemData["+itemCount+"][price]", value: data.price});
    var orgPriceInput = $("<input/>", { type: "hidden", name: "itemData["+itemCount+"][org_price]", value: data.org_price});
	var priceErrorDiv = $("<div></div>", { class: "error price" + itemCount });
	cell = $(row.insertCell(-1));
	cell.html(data.price);
	cell.append(priceInput, orgPriceInput, priceErrorDiv);

    var discPerInput = $("<input/>", { type: "hidden", name: "itemData["+itemCount+"][disc_per]", value: data.disc_per});
	var discAmtInput = $("<input/>", { type: "hidden", name: "itemData["+itemCount+"][disc_amount]", value: data.disc_amount });
	cell = $(row.insertCell(-1));
	cell.html(data.disc_amount + '(' + data.disc_per + '%)');
	cell.append(discPerInput);
	cell.append(discAmtInput);

    var cgstPerInput = $("<input/>", { type: "hidden", name: "itemData["+itemCount+"][cgst_per]", value: data.cgst_per });
	var cgstAmtInput = $("<input/>", { type: "hidden", name: "itemData["+itemCount+"][cgst_amount]", class:'cgst_amount', value: data.cgst_amount });
	cell = $(row.insertCell(-1));
	cell.html(data.cgst_amount + '(' + data.cgst_per + '%)');
	cell.append(cgstPerInput);
	cell.append(cgstAmtInput);
	cell.attr("class", "cgstCol");

	var sgstPerInput = $("<input/>", { type: "hidden", name: "itemData["+itemCount+"][sgst_per]", value: data.sgst_per });
	var sgstAmtInput = $("<input/>", { type: "hidden", name: "itemData["+itemCount+"][sgst_amount]", class:"sgst_amount", value: data.sgst_amount });
	cell = $(row.insertCell(-1));
	cell.html(data.sgst_amount + '(' + data.sgst_per + '%)');
	cell.append(sgstPerInput);
	cell.append(sgstAmtInput);
	cell.attr("class", "sgstCol");

	var gstPerInput = $("<input/>", { type: "hidden", name: "itemData["+itemCount+"][gst_per]", class:"gst_per", value: data.gst_per });
	var igstPerInput = $("<input/>", { type: "hidden", name: "itemData["+itemCount+"][igst_per]", value: data.igst_per });
	var gstAmtInput = $("<input/>", { type: "hidden", name: "itemData["+itemCount+"][gst_amount]", class:"gst_amount", value: data.gst_amount });
	var igstAmtInput = $("<input/>", { type: "hidden", name: "itemData["+itemCount+"][igst_amount]", class:"igst_amount", value: data.igst_amount });
	cell = $(row.insertCell(-1));
	cell.html(data.igst_amount + '(' + data.igst_per + '%)');
	cell.append(gstPerInput);
	cell.append(igstPerInput);
	cell.append(gstAmtInput);
	cell.append(igstAmtInput);
	cell.attr("class", "igstCol");

    var amountInput = $("<input/>", { type: "hidden", name: "itemData["+itemCount+"][amount]", class:"amount", value: data.amount });
    var taxableAmountInput = $("<input/>", { type: "hidden", name: "itemData["+itemCount+"][taxable_amount]", class:"taxable_amount", value: data.taxable_amount });
	cell = $(row.insertCell(-1));
	cell.html(data.taxable_amount);
	cell.append(amountInput);
	cell.append(taxableAmountInput);
	cell.attr("class", "amountCol");

	var netAmtInput = $("<input/>", { type: "hidden", name: "itemData["+itemCount+"][net_amount]", value: data.net_amount });
	cell = $(row.insertCell(-1));
	cell.html(data.net_amount);
	cell.append(netAmtInput);
	cell.attr("class", "netAmtCol");

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
	//$("#itemForm #qty").trigger('change');
}

function Remove(button) {
    var tableId = "customInvoiceItems";
	//Determine the reference of the Row using the Button.
	var row = $(button).closest("TR");
	var table = $("#"+tableId)[0];
	table.deleteRow(row[0].rowIndex);
	$('#'+tableId+' tbody tr td:nth-child(1)').each(function (idx, ele) {
		ele.textContent = idx + 1;
	});
	var countTR = $('#'+tableId+' tbody tr:last').index() + 1;
	if (countTR == 0) {
		$("#tempItem").html('<tr id="noData"><td colspan="15" align="center">No data available in table</td></tr>');
	}

	claculateColumn();
}

function resPartyDetail(response = ""){    
    if(response != ""){
        var partyDetail = response.data.partyDetail;
        $("#party_name").val(partyDetail.party_name);  
        $("#currency").val(partyDetail.currency);        
        $("#inrrate").val(partyDetail.inrrate);
    }else{
        $("#party_name").val("");
        $("#currency").val("");  
        $("#inrrate").val(1);
    }
	$("#inrrate").trigger('change');
    gstin();
}

function resItemDetail(response = ""){
    if(response != ""){
        var itemDetail = response.data.itemDetail;
        $("#itemForm #item_id").val(itemDetail.id);
        $("#itemForm #item_code").val(itemDetail.item_code);
        $("#itemForm #item_name").val(itemDetail.item_name);
        $("#itemForm #item_type").val(itemDetail.item_type);
        $("#itemForm #unit_id").val(itemDetail.unit_id);
        $("#itemForm #unit_name").val(itemDetail.uom);
		$("#itemForm #disc_per").val(itemDetail.defualt_disc);
		$("#itemForm #price").val(itemDetail.price);
		$("#itemForm #org_price").val(itemDetail.price);
		$("#itemForm #packing_qty").val(itemDetail.packing_standard);
        $("#itemForm #hsn_code").val(itemDetail.hsn_code);
        $("#itemForm #gst_per").val(parseFloat(itemDetail.gst_per).toFixed(0));
    }else{
		$("#itemForm #item_id").val("");
        $("#itemForm #item_code").val("");
        $("#itemForm #item_name").val("");
        $("#itemForm #item_type").val("");
        $("#itemForm #unit_id").val("");
        $("#itemForm #unit_name").val("");
		$("#itemForm #disc_per").val("");
		$("#itemForm #price").val("");
		$("#itemForm #org_price").val("");
		$("#itemForm #packing_qty").val("");
        $("#itemForm #hsn_code").val("");
        $("#itemForm #gst_per").val(0);
    }
	initSelect2('itemModel');
}

function resCustomInvoice(data,formId){
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