$(document).ready(function(){
    $(document).on('click','.getPendingOrders',function(){
		var party_id = $('#party_id').val();
		var party_name = $('#party_id :selected').text();
		$('.party_id').html("");

		if (party_id != "" || party_id != 0) {
			$.ajax({
				url: base_url + 'salesOrders/getPartyOrders',
				type: 'post',
				data: { party_id: party_id },
				success: function (response) {
					$("#modal-xl").modal("show");
					$('#modal-xl .modal-body').html('');
					$('#modal-xl .modal-title').html("Carete Invoice [ Party Name : "+party_name+" ]");
					$('#modal-xl .modal-body').html(response);
					$('#modal-xl .modal-body form').attr('id',"createInvoiceForm");
					$('#modal-xl .modal-footer .btn-save').html('<i class="fa fa-check"></i> Create Invoice');
					$("#modal-xl .modal-footer .btn-save").attr('onclick',"createInvoice();");
				}
			});
		} else {
			$('.party_id').html("Party is required.");
		}
	});

	$(document).on('click', '.add-item', function () {
		$('#itemForm')[0].reset();
		$("#itemForm input:hidden").val('');
		$('#itemForm #row_index').val("");
        $('#itemForm #stock_eff').val("1");
        $("#itemForm .error").html();

		var party_id = $('#party_id').val();
		$(".party_id").html("");
		$("#itemForm #row_index").val("");
		if(party_id){
			$("#itemModel").modal('show');
			$(".btn-close").show();
			$(".btn-save").show();			
			
			setTimeout(function(){ $("#itemForm #item_id").focus();setPlaceHolder();initSelect2(); },500);
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

		/* var item_ids = $(".item_id").map(function () { return $(this).val(); }).get();
        if ($.inArray(formData.item_id, item_ids) >= 0 && formData.row_index == "") {
            $(".item_name").html("Item already added.");
        } */

        var errorCount = $('#itemForm .error:not(:empty)').length;

		if (errorCount == 0) {  
			var fd = $('#batchDetail').find('input').serializeArray();var batchDetail = [];
			$.each(fd, function (i, v) {
				if (v.name.startsWith('batchDetail')) {
					var match = v.name.match(/batchDetail\[(\d+)\]\[(.+)\]/);
					
					if(match){
						var index = match[1];
						var key = match[2];
						
						if(!batchDetail[index]){
							batchDetail[index] = {};
						}
						batchDetail[index][key] = v.value;
					}
				}
			});

			formData.batch_detail = [];
			for (var key in batchDetail) {
				if (batchDetail.hasOwnProperty(key)) {
					formData.batch_detail.push(batchDetail[key]);
				}
			}

			formData.batch_detail = JSON.stringify(formData.batch_detail);     
			

			formData.disc_amount = 0;
            formData.org_price = calculatePrice({price:formData.price,gst_per:formData.gst_per,disc_per:formData.disc_per,disc_amount:formData.disc_amount},"mrp");

            var itemData = calculateItem(formData);
			AddRow(itemData);
            
            $('#itemForm')[0].reset();
            $("#itemForm input:hidden").val('')
            $('#itemForm #row_index').val("");
            $('#itemForm #stock_eff').val("1");
			$("#itemForm #batchTrans").html(`<tr><td colspan="4" class="text-center">No data available in table</td></tr>`);
            initSelect2('itemModel');
            if ($(this).data('fn') == "save") {
                $("#itemForm #item_id").focus();
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
		initSelect2();
	});   

	$(document).on('change','#unit_id',function(){
		$("#unit_name").val("");
		if($(this).val()){ $("#unit_name").val($("#unit_id :selected").data('unit')); }
	});

	$(document).on('keyup change','#itemForm .calculateBatchQty',function(){
        var row_id = $(this).data('srno');
        var batch_qty = $(this).val() || 0;
        var stock_qty = $("#batch_stock_"+row_id).val();
        $(".batch_qty_"+row_id).html('');
        if(parseFloat(batch_qty) > parseFloat(stock_qty)){
            $(".batch_qty_"+row_id).html('Invalid Qty.');
            $("#batch_qty_"+row_id).val(0);
            $(this).val("");
        }   
        
        var batchQtyArr = $("#itemForm .calculateBatchQty").map(function(){return $(this).val();}).get();
        var batchQtySum = 0;
        $.each(batchQtyArr,function(){batchQtySum += parseFloat(this) || 0;});
        $('#itemForm #qty').val(batchQtySum); 
        $('#itemForm #total_qty').html(batchQtySum); 
    });
});

function createInvoice(){
	var fromEntryTypes = $("#saveDeliveryChallan #from_entry_type").val();
	var refIds = $("#saveDeliveryChallan #ref_id").val();
	var ship_to_id = [];
	var mainRefIds = []; var mainFromEntryType = [];

	if(refIds != ""){ mainRefIds = refIds.split(","); }
	if(fromEntryTypes != ""){ mainFromEntryType = fromEntryTypes.split(","); }
	
	$(".orderItem:checked").map(function() {
		row = $(this).data('row');
		
		mainRefIds.push(row.trans_main_id);
		mainFromEntryType.push(row.main_entry_type);

		row.qty = row.pending_qty;
		row.unit_id = row.uom;
		row.unit_name = row.uom;
		row.gst_per = parseFloat(row.gst_per);
		row.org_price = (parseFloat(row.org_price) > 0)?row.org_price:row.price;
		row.stock_eff = (row.stock_eff == 0)?1:0;
		AddRow(row);		
	}).get();

	mainRefIds = $.unique(mainRefIds);
	mainFromEntryType = $.unique(mainFromEntryType);

	mainRefIds = mainRefIds.join(",");
	mainFromEntryType = mainFromEntryType.join(",");

	$("#saveDeliveryChallan #ref_id").val("");
	$("#saveDeliveryChallan #ref_id").val(mainRefIds);
	$("#saveDeliveryChallan #from_entry_type").val("");
	$("#saveDeliveryChallan #from_entry_type").val(mainFromEntryType);
	initSelect2();

	$("#modal-xl").modal('hide');
	$('#modal-xl .modal-body').html('');
}

var itemCount = 0;
function AddRow(data) {
    var tblName = "deliveryChallanItems";

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
    var itemtypeInput = $("<input/>", { type: "hidden", name: "itemData["+itemCount+"][item_type]", value: data.item_type });
    var stockEffInput = $("<input/>", { type: "hidden", name: "itemData["+itemCount+"][stock_eff]", value: data.stock_eff });
    var hsnCodeInput = $("<input/>", { type: "hidden", name: "itemData["+itemCount+"][hsn_code]", value: data.hsn_code });
    var pormInput = $("<input/>", { type: "hidden", name: "itemData["+itemCount+"][p_or_m]", value: -1 });
	var batchDetailInput = $("<input/>", { type: "hidden", name: "itemData["+itemCount+"][batch_detail]", value: data.batch_detail });
    cell = $(row.insertCell(-1));
    cell.html(data.item_name);
    cell.append(idInput);
    cell.append(itemIdInput);
    cell.append(itemNameInput);
    cell.append(formEnteryTypeInput);
    cell.append(refIdInput);
    cell.append(itemCodeInput);
    cell.append(itemtypeInput);
    cell.append(stockEffInput);
    cell.append(hsnCodeInput);
    cell.append(pormInput);
    cell.append(batchDetailInput);

    var qtyInput = $("<input/>", { type: "hidden", name: "itemData["+itemCount+"][qty]", class:"item_qty", value: data.qty });
	var qtyErrorDiv = $("<div></div>", { class: "error qty" + itemCount });
	cell = $(row.insertCell(-1));
	cell.html(data.qty);
	cell.append(qtyInput);
	cell.append(qtyErrorDiv);

    var unitIdInput = $("<input/>", { type: "hidden", name: "itemData["+itemCount+"][unit_id]", value: data.unit_id });
	var unitNameInput = $("<input/>", { type: "hidden", name: "itemData["+itemCount+"][unit_name]", value: data.unit_id });
	cell = $(row.insertCell(-1));
	cell.html(data.unit_name);
	cell.append(unitIdInput);
	cell.append(unitNameInput);    

    var itemRemarkInput = $("<input/>", { type: "hidden", name: "itemData["+itemCount+"][item_remark]", value: data.item_remark});
	cell = $(row.insertCell(-1));
	cell.html(data.item_remark);
	cell.append(itemRemarkInput);

    var priceInput = $("<input/>", { type: "hidden", name: "itemData["+itemCount+"][price]", value: data.price});
    var orgPriceInput = $("<input/>", { type: "hidden", name: "itemData["+itemCount+"][org_price]", value: data.org_price});	
	cell.append(priceInput);
	cell.append(orgPriceInput);

    var discPerInput = $("<input/>", { type: "hidden", name: "itemData["+itemCount+"][disc_per]", value: data.disc_per});
	var discAmtInput = $("<input/>", { type: "hidden", name: "itemData["+itemCount+"][disc_amount]", value: data.disc_amount });	
	cell.append(discPerInput);
	cell.append(discAmtInput);

    var cgstPerInput = $("<input/>", { type: "hidden", name: "itemData["+itemCount+"][cgst_per]", value: data.cgst_per });
	var cgstAmtInput = $("<input/>", { type: "hidden", name: "itemData["+itemCount+"][cgst_amount]", class:'cgst_amount', value: data.cgst_amount });
    cell.append(cgstPerInput);
	cell.append(cgstAmtInput);

    var sgstPerInput = $("<input/>", { type: "hidden", name: "itemData["+itemCount+"][sgst_per]", value: data.sgst_per });
	var sgstAmtInput = $("<input/>", { type: "hidden", name: "itemData["+itemCount+"][sgst_amount]", class:"sgst_amount", value: data.sgst_amount });
    cell.append(sgstPerInput);
	cell.append(sgstAmtInput);

    var gstPerInput = $("<input/>", { type: "hidden", name: "itemData["+itemCount+"][gst_per]", class:"gst_per", value: data.gst_per });
	var igstPerInput = $("<input/>", { type: "hidden", name: "itemData["+itemCount+"][igst_per]", value: data.igst_per });
	var gstAmtInput = $("<input/>", { type: "hidden", name: "itemData["+itemCount+"][gst_amount]", class:"gst_amount", value: data.gst_amount });
	var igstAmtInput = $("<input/>", { type: "hidden", name: "itemData["+itemCount+"][igst_amount]", class:"igst_amount", value: data.igst_amount });
    cell.append(gstPerInput);
	cell.append(igstPerInput);
	cell.append(gstAmtInput);
	cell.append(igstAmtInput);

    var amountInput = $("<input/>", { type: "hidden", name: "itemData["+itemCount+"][amount]", class:"amount", value: data.amount });
    var taxableAmountInput = $("<input/>", { type: "hidden", name: "itemData["+itemCount+"][taxable_amount]", class:"taxable_amount", value: data.taxable_amount });
    cell.append(amountInput);
	cell.append(taxableAmountInput);

    var netAmtInput = $("<input/>", { type: "hidden", name: "itemData["+itemCount+"][net_amount]", value: data.net_amount });
    cell.append(netAmtInput);

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

	itemCount++;
}

function Edit(data, button) {
	var row_index = $(button).closest("tr").index();
	$("#itemModel").modal('show');
	$(".btn-close").hide();
	$(".btn-save").hide();
	$.each(data, function (key, value) {
		$("#itemForm #" + key).val(value);
	});
	$("#itemForm #unit_id").val(data.unit_name);

	setTimeout(function(){
		if(data.stock_eff == 1){
			$('#itemForm #qty').prop('readonly',true);
			$("#batchTransactions").removeClass('hidden');
			var location_ids = "ALL"; var qty_readonly = '';
			
			var batchDetail = {'postData':{'item_id': data.item_id, 'id' : data.id, 'batchDetail': data.batch_detail,'location_ids':location_ids,'qty_readonly':qty_readonly,'with_opt_qty':0},'table_id':"batchDetail",'tbody_id':'batchTrans','tfoot_id':'','fnget':'getBatchWiseItemStock'};
			getTransHtml(batchDetail);
			setTimeout(function(){ $("#itemForm .calculateBatchQty").trigger('change'); },500);
		}else{
			$("#batchTransactions").addClass('hidden');
			$('#itemForm #qty').val(data.qty).prop('readonly',false);
			$("#itemForm #batchTrans").html('<tr><td colspan="4" class="text-center">No data available in table</td></tr>');
			$("#itemForm #total_box").html('0');
		}
	},500);
	
	$("#itemForm #row_index").val(row_index);
	initSelect2();
}

function Remove(button) {
    var tableId = "deliveryChallanItems";
	//Determine the reference of the Row using the Button.
	var row = $(button).closest("TR");
	var table = $("#"+tableId)[0];
	table.deleteRow(row[0].rowIndex);
	$('#'+tableId+' tbody tr td:nth-child(1)').each(function (idx, ele) {
		ele.textContent = idx + 1;
	});
	var countTR = $('#'+tableId+' tbody tr:last').index() + 1;
	if (countTR == 0) {
		$("#tempItem").html('<tr id="noData"><td colspan="14" align="center">No data available in table</td></tr>');
	}

}

function resPartyDetail(response = ""){
    var html = '<option value="">Select GST No.</option>';
    if(response != ""){
        var partyDetail = response.data.partyDetail;
        $("#party_name").val(partyDetail.party_name);
		$("#party_state_code").val(partyDetail.state_code);
        $("#itemForm #item_id").attr('data-price_structure_id',partyDetail.price_structure_id);
		$("#master_t_col_3").val(partyDetail.delivery_address);

        var gstDetails = response.data.gstDetails; var i = 1;
        $.each(gstDetails,function(index,row){  
			if(row.gstin !=""){
				html += '<option value="'+row.gstin+'" '+((i==1)?"selected":"")+'>'+row.gstin+'</option>';
				i++;
			}            
        });        
    }else{
        $("#party_name").val("");
		$("#party_state_code").val("");
		$("#itemForm #item_id").attr('data-price_stracture_id',"");
		$("#master_t_col_3").val("");
    }
    $("#gstin").html(html);initSelect2();
}

function resItemDetail(response = ""){
    if(response != ""){
        var itemDetail = response.data.itemDetail;
        $("#itemForm #item_code").val(itemDetail.item_code);
        $("#itemForm #item_name").val(itemDetail.item_name);
        $("#itemForm #item_type").val(itemDetail.item_type);
        $("#itemForm #unit_id").val(itemDetail.unit_id);
        $("#itemForm #unit_name").val(itemDetail.unit_name);
		$("#itemForm #price").val(itemDetail.price);
		$("#itemForm #org_price").val(itemDetail.mrp);
		$("#itemForm #gst_per").val(parseFloat(itemDetail.gst_per).toFixed(0));
		$("#itemForm #hsn_code").val(itemDetail.hsn_code);

		$("#itemForm #stock_eff").val(1); 

		$("#batchTransactions").removeClass('hidden');
		$("#batchTrans").html(`<tr><td colspan="4" class="text-center">No data available in table</td></tr>`);
		$('#itemForm #total_box').html(0);
		$('#itemForm #qty').val(0).prop('readonly',true);
		var batchDetail = {'postData':{'item_id': itemDetail.id, 'party_id' : $("#party_id").val(), 'id' : '','location_ids': 'ALL','with_opt_qty':0},'table_id':"batchDetail",'tbody_id':'batchTrans','tfoot_id':'','fnget':'getBatchWiseItemStock'};
		getTransHtml(batchDetail);
    }else{
        $("#itemForm #item_code").val("");
        $("#itemForm #item_name").val("");
        $("#itemForm #item_type").val("");
        $("#itemForm #unit_id").val("");
        $("#itemForm #unit_name").val("");
		$("#itemForm #disc_per").val("");
		$("#itemForm #price").val("");
		$("#itemForm #org_price").val("");
        $("#itemForm #hsn_code").val("");
        $("#itemForm #gst_per").val(0);
    }
	initSelect2();
}

function resSaveChallan(data,formId){
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