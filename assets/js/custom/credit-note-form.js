var itemCount = 0;
$(document).ready(function(){
	$(".ledgerColumn").hide();
	$(".summary_desc").attr('style','width: 60%;');

	$("#invItemLink").hide();
	if($('#doc_no').val() != ""){ $("#invItemLink").show(); }

	$(document).on('click','.getInvoiceItem',function(){
		var ref_id = $('#ref_id').val();
		var party_name = $('#party_id :selected').text();
		$('.doc_no').html("");

		if (ref_id != "" || ref_id != 0) {
			$.ajax({
				url: base_url + 'salesInvoice/getPartyInvoiceItems',
				type: 'post',
				data: { id : ref_id },
				success: function (response) {
					$("#modal-xl").modal("show");
					$('#modal-xl .modal-body').html('');
					$('#modal-xl .modal-title').html("Carete Invoice [ Party Name : "+party_name+" ]");
					$('#modal-xl .modal-body').html(response);
					$('#modal-xl .modal-body form').attr('id',"createCreditNoteForm");
					$('#modal-xl .modal-footer .btn-save').html('<i class="fa fa-check"></i> Create Invoice');
					$("#modal-xl .modal-footer .btn-save").attr('onclick',"createInvoice();");
				}
			});
		} else {
			$('.doc_no').html("Inv. No. is required.");
		}	
	});


	$(document).on("change",'#order_type',function(){
        var order_type = $(this).val();
		$.ajax({ 
            type: "post",   
            url: base_url + controller + '/getCreditNoteTypes',   
            data: {order_type:order_type},
			dataType: 'json',
        }).done(function(response){
			$("#inv_type").val("");
			$("#inv_type").val(response.inv_type);
			$("#tax_class_id").html("");
			$("#tax_class_id").html(response.accountOptions);
			$("#tax_class_id").select2();
			$("#tax_class_id").trigger('change');
			gstin();
        });

        if(order_type == "Sales Return"){
            $('#itemForm #stock_eff').val("1");
            $('#tempItem .itemStockEff').val("1");
        }else{
            $('#itemForm #stock_eff').val("0");
			$('#tempItem .itemStockEff').val("0");
        }
    });

    $(document).on('click', '.add-item', function () {
		$('#itemForm')[0].reset();
		$("#itemForm input:hidden").val('');
		$('#itemForm #row_index').val("");
		
        $("#itemForm .error").html();

        if($('#order_type').val() == "Sales Return"){
            $('#itemForm #stock_eff').val("1");
        }else{
            $('#itemForm #stock_eff').val("0");
        }

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
			if($('#order_type').val() == "Sales Return"){
				formData.stock_eff = 1;
			}else{
				formData.stock_eff = 0;
			}
            var itemData = calculateItem(formData);

            AddRow(itemData);
            $('#itemForm')[0].reset();
            $("#itemForm input:hidden").val('')
            $('#itemForm #row_index').val("");
			if($('#order_type').val() == "Sales Return"){
				$('#itemForm #stock_eff').val("1");
			}else{
				$('#itemForm #stock_eff').val("0");
			}
            initSelect2('itemModel');
            if ($(this).data('fn') == "save") {
                $("#item_idc").focus();
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

	$(document).on('change','#unit_id',function(){
		$("#unit_name").val("");
		if($(this).val()){ $("#unit_name").val($("#unit_id :selected").data('unit')); }
	});

	$(document).on('change','#hsn_code',function(){
		$("#gst_per").val(($("#hsn_code :selected").data('gst_per') || 0));
		initSelect2('itemModel');
	});

	$('#doc_no').typeahead({
		source: function(query, result){
			$.ajax({
				url:base_url + controller + '/getPartyInvoiceList',
				method:"POST",
				global:false,
				data:{doc_no:query,party_id:$("#party_id :selected").val(),order_type:$("#order_type :selected").val()},
				dataType:"json",
				success:function(data){
					result($.map(data, function(row){return {name:row.trans_number,id:row.id,doc_date:row.trans_date,entry_type:row.entry_type};}));
					$("#saveCreditNote #doc_date").val("");
					$("#saveCreditNote #ref_id").val("");
					$("#saveCreditNote #from_entry_type").val("");
					$("#invItemLink").hide();
				}
			});
		},
		updater: function(item) {
			$("#saveCreditNote #doc_date").val(item.doc_date || "");
			$("#saveCreditNote #ref_id").val(item.id || "");
			$("#saveCreditNote #from_entry_type").val(item.entry_type || "");
			$("#invItemLink").show();
			return item;
        }
	});
});

function createInvoice(){	
	$(".orderItem:checked").map(function() {
		row = $(this).data('row');
		row.qty = row.pending_qty;
		row.gst_per = parseFloat(row.gst_per);
		if($('#order_type').val() == "Sales Return"){
			row.stock_eff = 1;
        }else{
			row.stock_eff = 0;
        }
		AddRow(row);
	}).get();

	$("#modal-xl").modal('hide');
	$('#modal-xl .modal-body').html('');
}

function AddRow(data) {
    var tblName = "creditNoteItems";

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
	var stockEffInput = $("<input/>", { type: "hidden", name: "itemData["+itemCount+"][stock_eff]", class:'itemStockEff', value: data.stock_eff });
    var pormInput = $("<input/>", { type: "hidden", name: "itemData["+itemCount+"][p_or_m]", value: -1 });
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
    cell.append(pormInput);

    var hsnCodeInput = $("<input/>", { type: "hidden", name: "itemData["+itemCount+"][hsn_code]", value: data.hsn_code });
	cell = $(row.insertCell(-1));
	cell.html(data.hsn_code);
	cell.append(hsnCodeInput);

    var qtyInput = $("<input/>", { type: "hidden", name: "itemData["+itemCount+"][qty]", class:"item_qty", value: data.qty });
	var qtyErrorDiv = $("<div></div>", { class: "error qty" + itemCount });
	cell = $(row.insertCell(-1));
	cell.html(data.qty);
	cell.append(qtyInput);
	cell.append(qtyErrorDiv);

    var unitIdInput = $("<input/>", { type: "hidden", name: "itemData["+itemCount+"][unit_id]", value: data.unit_id });
	var unitNameInput = $("<input/>", { type: "hidden", name: "itemData["+itemCount+"][unit_name]", value: data.unit_name });
	cell = $(row.insertCell(-1));
	cell.html(data.unit_name);
	cell.append(unitIdInput);
	cell.append(unitNameInput);

    var priceInput = $("<input/>", { type: "hidden", name: "itemData["+itemCount+"][price]", value: data.price});
    var orgPriceInput = $("<input/>", { type: "hidden", name: "itemData["+itemCount+"][org_price]", value: data.org_price});
	var priceErrorDiv = $("<div></div>", { class: "error price" + itemCount });
	cell = $(row.insertCell(-1));
	cell.html(data.price);
	cell.append(priceInput);
	cell.append(orgPriceInput);
	cell.append(priceErrorDiv);

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
	if($('#order_type').val() == "Sales Return"){
		$("#itemForm #stock_eff").val(1);
	}else{
		$("#itemForm #stock_eff").val(0);
	}
}

function Remove(button) {
    var tableId = "creditNoteItems";
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
    var html = '<option value="">Select GST No.</option>';
    if(response != ""){
        var partyDetail = response.data.partyDetail;
        $("#party_name").val(partyDetail.party_name);
		$("#party_state_code").val(partyDetail.state_code);
		$("#itemForm #item_id").attr('data-price_structure_id',partyDetail.price_structure_id);

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
    }
    
    $("#gstin").html(html);initSelect2();gstin();
}

function resItemDetail(response = ""){
    if(response != ""){
        var itemDetail = response.data.itemDetail;
        $("#itemForm #item_code").val(itemDetail.item_code);
        $("#itemForm #item_name").val(itemDetail.item_name);
        $("#itemForm #item_type").val(itemDetail.item_type);
        $("#itemForm #unit_id").val(itemDetail.unit_id);
        $("#itemForm #unit_name").val(itemDetail.unit_name);
		$("#itemForm #disc_per").val(itemDetail.defualt_disc);
		$("#itemForm #price").val(itemDetail.price);
		$("#itemForm #org_price").val(itemDetail.price);
        $("#itemForm #hsn_code").val(itemDetail.hsn_code);
        $("#itemForm #gst_per").val(parseFloat(itemDetail.gst_per).toFixed(0));

		if(itemDetail.item_type == 8){ 
			$("#itemForm #stock_eff").val(0); 
		}else{ 
			if($('#order_type').val() == "Sales Return"){
				$('#itemForm #stock_eff').val("1");
			}else{
				$('#itemForm #stock_eff').val("0");
			}
		}
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

		if($('#order_type').val() == "Sales Return"){
			$('#itemForm #stock_eff').val("1");
		}else{
			$('#itemForm #stock_eff').val("0");
		}
    }
	initSelect2('itemModel');
}

function resCreditNote(data,formId){
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