$(document).ready(function(){
	$(".ledgerColumn").hide();
	$(".summary_desc").attr('style','width: 60%;');
	setTimeout(function(){$(".partyDetails").trigger('change');},100);

	$(document).on('click', '.add-item', function () {
		$('#itemForm')[0].reset();
		$("#itemForm input:hidden").val('');
		$('#itemForm #row_index').val("");
        $("#itemForm .error").html();

		var party_id = $('#party_id').val();
		$(".party_id").html("");
		$("#itemForm #row_index").val("");
		if(party_id){
			$("#itemModel").modal('show');
			$(".btn-close").show();
			$(".btn-save").show();			
			
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

		var item_ids = $(".item_id").map(function () { return $(this).val(); }).get();
        if ($.inArray(formData.item_id, item_ids) >= 0 && formData.row_index == "") {
            $(".item_name").html("Item already added.");
        }

        var errorCount = $('#itemForm .error:not(:empty)').length;

		if (errorCount == 0) {
            
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

    $(document).on('click', '.btn-close', function () {
		$('#itemForm')[0].reset();
		$("#itemForm input:hidden").val('')
		$('#itemForm #row_index').val("");
		$("#itemForm .error").html("");
		initSelect2('itemModel');
	});   
});

function AddRow(data) {
    var tblName = "salesEnqItems";

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

	var partNoInput = $("<input/>",{type:"hidden",name:"itemData["+countRow+"][drw_no]",value:data.drw_no});
	cell = $(row.insertCell(-1));
	cell.html(data.drw_no);
	cell.append(partNoInput);

    var itemIdInput = $("<input/>",{type:"hidden",name:"itemData["+countRow+"][item_id]", class:"item_id",value:data.item_id});
	var transIdInput = $("<input/>",{type:"hidden",name:"itemData["+countRow+"][id]",value:data.id});
	var drgReceiveInput = $("<input/>",{type:"hidden",name:"itemData["+countRow+"][drg_receive]",value:data.drg_receive});
	cell = $(row.insertCell(-1));
	cell.html(((data.item_code != "")?"[" + data.item_code + "] " : "" ) + data.item_name);
	cell.append(itemIdInput);
	cell.append(transIdInput);
	cell.append(drgReceiveInput);
	
	var annualInput = $("<input/>", { type: "hidden", name: "itemData["+countRow+"][annual_vol]", value: data.annual_vol });
	cell = $(row.insertCell(-1));
	cell.html(data.annual_vol);
	cell.append(annualInput);
	
	var qtyInput = $("<input/>",{type:"hidden",name:"itemData["+countRow+"][qty]",value:data.qty});
	cell = $(row.insertCell(-1));
	cell.html(data.qty);
	cell.append(qtyInput);
	
	var gradeInput = $("<input/>", { type: "hidden", name: "itemData["+countRow+"][material_grade]", value: data.material_grade });
	var fgWeightInput = $("<input/>",{type:"hidden",name:"itemData["+countRow+"][fg_weight]",value:data.fg_weight});
	cell = $(row.insertCell(-1));
	cell.html(data.material_grade);
	cell.append(fgWeightInput);

	var itemRemarkInput = $("<input/>",{type:"hidden",name:"itemData["+countRow+"][item_remark]",value:data.item_remark});
	cell = $(row.insertCell(-1));
	cell.html(data.item_remark);
	cell.append(itemRemarkInput);

    //Add Button cell.
	cell = $(row.insertCell(-1));
	var btnRemove = $('<button><i class="mdi mdi-trash-can-outline"></i></button>');
	btnRemove.attr("type", "button");
	btnRemove.attr("onclick", "Remove(this);");
	btnRemove.attr("style", "margin-left:4px;");
	btnRemove.attr("class", "btn btn-outline-danger waves-effect waves-light");

	var btnEdit = $('<button><i class="mdi mdi-square-edit-outline"></i></button>');
	btnEdit.attr("type", "button");
	btnEdit.attr("onclick", "Edit(" + JSON.stringify(data) + ",this);");
	btnEdit.attr("class", "btn btn-outline-warning waves-effect waves-light");

	cell.append(btnEdit);
	cell.append(btnRemove);
	cell.attr("class", "text-center");
	cell.attr("style", "width:10%;");
}

function Edit(data, button) {
	var row_index = $(button).closest("tr").index();
	$("#itemModel").modal('show');
	$(".btn-close").hide();
	$(".btn-save").hide();
	$.each(data, function (key, value) {
		$("#itemForm #" + key).val(value);
	});
	
	$("#item_id").select2();
	$("#drg_receive").select2();
	$("#itemForm #row_index").val(row_index);
}

function Remove(button) {
    var tableId = "salesEnqItems";
	//Determine the reference of the Row using the Button.
	var row = $(button).closest("TR");
	var table = $("#"+tableId)[0];
	table.deleteRow(row[0].rowIndex);
	$('#'+tableId+' tbody tr td:nth-child(1)').each(function (idx, ele) {
		ele.textContent = idx + 1;
	});
	var countTR = $('#'+tableId+' tbody tr:last').index() + 1;
	if (countTR == 0) {
		$("#tempItem").html('<tr id="noData"><td colspan="8" align="center">No data available in table</td></tr>');
	}

}

function resItemDetail(response = ""){
    if(response != ""){
        var itemDetail = response.data.itemDetail;
        $("#itemForm #item_name").val(itemDetail.item_name);
        $("#itemForm #item_code").val(itemDetail.item_code);
        $("#itemForm #material_grade").val(itemDetail.material_grade);
    }else{
        $("#itemForm #item_name").val("");
        $("#itemForm #item_code").val("");
        $("#itemForm #material_grade").val("");
    }
}

function resSaveEnquiry(data,formId){
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

/* UPDATED BY : AVT DATE:18-12-2024 */
function resPartyDetail(response = ""){
    if(response != ""){
        var partyDetail = response.data.partyDetail;
		$("#currency").val(partyDetail.currency);
		$("#addnewitem").attr("onclick","modalAction({'postData':{'item_type':1,'is_active':2,'party_id':"+partyDetail.id+"},'modal_id' : 'bs-left-lg-modal','controller':'items', 'call_function':'addItem', 'form_id' : 'addItem', 'title' : 'Add Product','res_function':'resItemMaster','js_store_fn':'customStore'})");
    }else{
		$("#currency").val("");
    }
	initSelect2();
}