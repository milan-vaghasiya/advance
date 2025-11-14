$(document).ready(function(){
	
	$(".ledgerColumn").hide();
	$(".summary_desc").attr('style','width: 60%;');

	setTimeout(function(){
		$(".partyDetails").trigger('change');
	},100);

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
			$("#itemModel").modal('show');
			$("#itemModel .btn-close").show();
			$("#itemModel .btn-save").show();
			
			setTimeout(function(){ $("#itemForm #item_id").focus(); setPlaceHolder(); initSelect2(); },500);
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
            // var itemData = calculateItem(formData);
			formData.disc_per = (parseFloat(formData.disc_per) > 0)?formData.disc_per:0;
            var amount = 0; var taxable_amount = 0; var disc_amt = 0; var igst_amt = 0;
            var gst_amount = 0; var cgst_amt = 0; var sgst_amt = 0; var net_amount = 0; 
            var gst_per = 0; var cgst_per = 0; var sgst_per = 0; var igst_per = 0;

            if (formData.disc_per == "" && formData.disc_per == "0") {
                taxable_amount = amount = parseFloat(parseFloat(formData.qty) * parseFloat(formData.price) + parseFloat(formData.tool_cost)).toFixed(2);
				
            } else {
                amount = parseFloat(parseFloat(formData.qty) * parseFloat(formData.price)).toFixed(2);
                disc_amt = parseFloat((amount * parseFloat(formData.disc_per)) / 100).toFixed(2);
                taxable_amount = parseFloat((amount - disc_amt) + parseFloat(formData.tool_cost)).toFixed(2);
            }

            formData.gst_per = igst_per = parseFloat(formData.gst_per).toFixed(0);
            formData.gst_amount = igst_amt = parseFloat((igst_per * taxable_amount) / 100).toFixed(2);

            cgst_per = parseFloat(parseFloat(igst_per) / 2).toFixed(2);
            sgst_per = parseFloat(parseFloat(igst_per) / 2).toFixed(2);

            cgst_amt = parseFloat((cgst_per * taxable_amount) / 100).toFixed(2);
            sgst_amt = parseFloat((sgst_per * taxable_amount) / 100).toFixed(2);

            net_amount = parseFloat(parseFloat(taxable_amount) + parseFloat(igst_amt)).toFixed(2);

            formData.qty = parseFloat(formData.qty).toFixed(2);
            formData.cgst_per = cgst_per;
            formData.cgst_amount = cgst_amt;
            formData.sgst_per = sgst_per;
            formData.sgst_amount = sgst_amt;
            formData.igst_per = igst_per;
            formData.igst_amount = igst_amt;
            formData.disc_amount = disc_amt;
            formData.amount = amount;
            formData.taxable_amount = taxable_amount;
            formData.net_amount = net_amount;
			
			if(formData.drw_no == '' && formData.drw_no == null){
				var drw_no = $("#rev_no :selected").data('drw_no');	
				formData.drw_no = drw_no;
			}

            AddRow(formData);
            $('#itemForm')[0].reset();
            $("#itemForm input:hidden").val('')
            $('#itemForm #row_index').val("");
            initSelect2();
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
        $('#itemForm .select2').select2();
	});   

	$(document).on('change','#unit_id',function(){
		$("#unit_name").val("");
		if($(this).val()){ $("#unit_name").val($("#unit_id :selected").data('unit')); }	
	});

	$(document).on('change','#hsn_code',function(){
		$("#gst_per").val(($("#hsn_code :selected").data('gst_per') || 0));
		initSelect2();
	});
	
	$(document).on('change','#item_id',function(e){
		e.stopImmediatePropagation();e.preventDefault();
		var item_id  = $("#item_id").val();
		$.ajax({
			url:base_url + controller + "/getRevisionList",
			type:'post',
			data:{item_id:item_id}, 
			dataType:'json',
			success:function(data){
				$("#rev_no").html("");
				$("#rev_no").html(data.revHtml);
				initSelect2();
			}
		});
    });
});

var itemCount = 0;
function AddRow(data) { 
    var tblName = "salesQuotationItems";

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

	var revNoInput = $("<input/>", { type: "hidden", name: "itemData["+itemCount+"][rev_no]", value: data.rev_no });
	var drgNoInput = $("<input/>", { type: "hidden", name: "itemData["+itemCount+"][drw_no]", value: data.drw_no });
	cell = $(row.insertCell(-1));
	cell.html(data.drw_no);
    cell.append(revNoInput);
    cell.append(drgNoInput);

    var idInput = $("<input/>", { type: "hidden", name: "itemData["+itemCount+"][id]", value: data.id });
    var itemIdInput = $("<input/>", { type: "hidden", name: "itemData["+itemCount+"][item_id]", class:"item_id", value: data.item_id });
    var formEnteryTypeInput = $("<input/>", { type: "hidden", name: "itemData["+itemCount+"][from_entry_type]", value: data.from_entry_type });
	var refIdInput = $("<input/>", { type: "hidden", name: "itemData["+itemCount+"][ref_id]", value: data.ref_id });
	// var revNoInput = $("<input/>", { type: "hidden", name: "itemData["+itemCount+"][rev_no]", value: data.rev_no });
	cell = $(row.insertCell(-1));
    cell.html(data.item_name);
    cell.append(idInput);
    cell.append(itemIdInput);
    cell.append(formEnteryTypeInput);
    cell.append(refIdInput);
    // cell.append(revNoInput);

	// cell = $(row.insertCell(-1));
	// cell.html(data.material_grade); //11-12-24

    var annualInput = $("<input/>", { type: "hidden", name: "itemData["+itemCount+"][annual_vol]", value: data.annual_vol });
	cell = $(row.insertCell(-1));
	cell.html(data.annual_vol);
	cell.append(annualInput);

	// cell = $(row.insertCell(-1));
	// cell.html(data.unit_name);

	var qtyInput = $("<input/>", { type: "hidden", name: "itemData["+itemCount+"][qty]", class:"item_qty", value: data.qty });
	var qtyErrorDiv = $("<div></div>", { class: "error qty" + itemCount });
	cell = $(row.insertCell(-1));
	cell.html(data.qty);
	cell.append(qtyInput);
	cell.append(qtyErrorDiv);


	var gradeInput = $("<input/>", { type: "hidden", name: "itemData["+itemCount+"][material_grade]", value: data.material_grade });
	cell = $(row.insertCell(-1));
	cell.html(data.material_grade);
	// cell.append(gradeInput);

	var priceInput = $("<input/>", { type: "hidden", name: "itemData["+itemCount+"][price]", value: data.price});
	var priceErrorDiv = $("<div></div>", { class: "error price" + itemCount });
	cell = $(row.insertCell(-1));
	cell.html(data.price);
	cell.append(priceInput);
	cell.append(priceErrorDiv);
	
	var toolCostInput = $("<input/>", { type: "hidden", name: "itemData["+itemCount+"][tool_cost]", value: data.tool_cost});
	cell = $(row.insertCell(-1));
	cell.html(data.tool_cost);
	cell.append(toolCostInput);

	var itemRemarkInput = $("<input/>", { type: "hidden", name: "itemData["+itemCount+"][item_remark]", value: data.item_remark});
	cell = $(row.insertCell(-1));
	cell.html(data.item_remark);
	cell.append(itemRemarkInput);

    var discPerInput = $("<input/>", { type: "hidden", name: "itemData["+itemCount+"][disc_per]", value: data.disc_per});
	var discAmtInput = $("<input/>", { type: "hidden", name: "itemData["+itemCount+"][disc_amount]", value: data.disc_amount });
	// cell = $(row.insertCell(-1));
	// cell.html(data.disc_amount + '(' + data.disc_per + '%)');
	cell.append(discPerInput);
	cell.append(discAmtInput);

    var cgstPerInput = $("<input/>", { type: "hidden", name: "itemData["+itemCount+"][cgst_per]", value: data.cgst_per });
	var cgstAmtInput = $("<input/>", { type: "hidden", name: "itemData["+itemCount+"][cgst_amount]", class:'cgst_amount', value: data.cgst_amount });
	// cell = $(row.insertCell(-1));
	// cell.html(data.cgst_amount + '(' + data.cgst_per + '%)');
	cell.append(cgstPerInput);
	cell.append(cgstAmtInput);
	cell.attr("class", "cgstCol");

	var sgstPerInput = $("<input/>", { type: "hidden", name: "itemData["+itemCount+"][sgst_per]", value: data.sgst_per });
	var sgstAmtInput = $("<input/>", { type: "hidden", name: "itemData["+itemCount+"][sgst_amount]", class:"sgst_amount", value: data.sgst_amount });
	// cell = $(row.insertCell(-1));
	// cell.html(data.sgst_amount + '(' + data.sgst_per + '%)');
	cell.append(sgstPerInput);
	cell.append(sgstAmtInput);
	cell.attr("class", "sgstCol");

	var gstPerInput = $("<input/>", { type: "hidden", name: "itemData["+itemCount+"][gst_per]", class:"gst_per", value: data.gst_per });
	var igstPerInput = $("<input/>", { type: "hidden", name: "itemData["+itemCount+"][igst_per]", value: data.igst_per });
	var gstAmtInput = $("<input/>", { type: "hidden", name: "itemData["+itemCount+"][gst_amount]", class:"gst_amount", value: data.gst_amount });
	var igstAmtInput = $("<input/>", { type: "hidden", name: "itemData["+itemCount+"][igst_amount]", class:"igst_amount", value: data.igst_amount });
	// cell = $(row.insertCell(-1));
	// cell.html(data.igst_amount + '(' + data.igst_per + '%)');
	cell.append(gstPerInput);
	cell.append(igstPerInput);
	cell.append(gstAmtInput);
	cell.append(igstAmtInput);
	cell.attr("class", "igstCol");

    var amountInput = $("<input/>", { type: "hidden", name: "itemData["+itemCount+"][amount]", class:"amount", value: data.amount });
    var taxableAmountInput = $("<input/>", { type: "hidden", name: "itemData["+itemCount+"][taxable_amount]", class:"taxable_amount", value: data.taxable_amount });
	// cell = $(row.insertCell(-1));
	// cell.html(data.taxable_amount);
	cell.append(amountInput);
	cell.append(taxableAmountInput);
	cell.attr("class", "amountCol");

	var netAmtInput = $("<input/>", { type: "hidden", name: "itemData["+itemCount+"][net_amount]", value: data.net_amount });
	// cell = $(row.insertCell(-1));
	// cell.html(data.net_amount);
	cell.append(netAmtInput);
	cell.attr("class", "netAmtCol");


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

	if(data.is_approve == 0){
		cell.append(btnEdit);
		cell.append(btnRemove);
	}
	
	cell.attr("class", "text-center");
	cell.attr("style", "width:10%;");

	var gst_type = $("#gst_type").val();
	if (gst_type == 1) {
		$(".cgstCol").show(); $(".sgstCol").show(); $(".igstCol").hide();
		$(".amountCol").hide(); $(".netAmtCol").show();
	} else if (gst_type == 2) {
		$(".cgstCol").hide(); $(".sgstCol").hide(); $(".igstCol").show();
		$(".amountCol").hide(); $(".netAmtCol").show();
	} else {
		$(".cgstCol").hide(); $(".sgstCol").hide(); $(".igstCol").hide();
		$(".amountCol").show(); $(".netAmtCol").hide();
	}

    claculateColumn();
	itemCount++;
}

function Edit(data, button) {
	var row_index = $(button).closest("tr").index();
	$("#itemModel").modal('show');
	//$("#itemModel .btn-close").hide();
	$("#itemModel .btn-save").hide();
	var revNo="";
	$.each(data, function (key, value) {
		// $("#itemForm #" + key).val(value);
		if (key == "rev_no") { revNo = value; }
		else { $("#itemForm #" + key).val(value); }
	});

	var item_id  = $("#item_id").val();
		$.ajax({
			url:base_url + controller + "/getRevisionList",
			type:'post',
			data:{item_id:item_id,rev_no:revNo}, 
			dataType:'json',
			success:function(data){
				$("#rev_no").html("");
				$("#rev_no").html(data.revHtml);
			}
		});

	initSelect2();
	$("#itemForm #row_index").val(row_index);
}

function Remove(button) {
    var tableId = "salesQuotationItems";
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

/* UPDATED BY : AVT DATE:18-12-2024 */
function resPartyDetail(response = ""){
    var html = '<option value="">Select GST No.</option>';
    if(response != ""){
        var partyDetail = response.data.partyDetail;
		$("#party_state_code").val(partyDetail.state_code);
		$("#currency").val(partyDetail.currency);
		$("#addnewitem").attr("onclick","modalAction({'postData':{'item_type':1,'is_active':2,'party_id':"+partyDetail.id+"},'modal_id' : 'bs-left-lg-modal','controller':'items', 'call_function':'addItem', 'form_id' : 'addItem', 'title' : 'Add Product','res_function':'resItemMaster','js_store_fn':'customStore'})");
		
        var gstDetails = response.data.gstDetails; var i = 1;
        $.each(gstDetails,function(index,row){  
			if(row.gstin !=""){
				html += '<option value="'+row.gstin+'" '+((i==1)?"selected":"")+'>'+row.gstin+'</option>';
				i++;
			}            
        });        
    }else{
		$("#party_state_code").val("");
		$("#currency").val("");
    }
    $("#gstin").html(html);$("#gstin").select2();gstin();
	initSelect2();
}

function resItemDetail(response = ""){
    if(response != ""){
        var itemDetail = response.data.itemDetail;

        $("#itemForm #item_name").val(itemDetail.item_name);
        $("#itemForm #unit_id").val(itemDetail.unit_id);
		$("#itemForm #disc_per").val(itemDetail.defualt_disc);
		$("#itemForm #price").val(itemDetail.price);
        $("#itemForm #hsn_code").val(itemDetail.hsn_code);
        $("#itemForm #gst_per").val(parseFloat(itemDetail.gst_per).toFixed(0));$("#itemForm #gst_per").select2();
        $("#itemForm #material_grade").val(itemDetail.material_grade);
    }else{
        $("#itemForm #item_name").val("");
        $("#itemForm #unit_id").val("");
		$("#itemForm #disc_per").val("");
		$("#itemForm #price").val("");
        $("#itemForm #hsn_code").val("");
        $("#itemForm #gst_per").val(0);
        $("#itemForm #material_grade").val("");
    }
	initSelect2();
}

function resSaveQuotation(data,formId){
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

