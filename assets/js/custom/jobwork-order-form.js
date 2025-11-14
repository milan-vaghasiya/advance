var itemCount = 0;var inv_print = 0;
$(document).ready(function(){
	$(document).on('change','.calculatePrice',function(e){
		$(".error").html("");
		var rate_per = $("#rate_per_unit").val();
		var rate = $('#rate').val();
		var qty = $('#qty').val();
		var qty_kg = $('#qty_kg').val();

        if(rate_per == 'PCS'){

            if(qty != 0 && qty != "")
            {
                var perpcs = parseFloat(rate) * parseFloat(qty);
                $("#amount").val(perpcs); 
            } else { $(".qty_pcs").html("Qty Pcs is required."); $("#amount").val(0); } 

        } else if(rate_per == 'KGS') {

            if(qty_kg != 0 && qty_kg != "")
            {
                var perkg = parseFloat(rate) * parseFloat(qty_kg);
                $("#amount").val(perkg);
            } else { $(".qty_kg").html("Qty kg is required."); $("#amount").val(0); } 

        } else {
            $("#amount").val(0); 
        }
    });

	$(document).on('change','#item_id',function(e){
        e.stopImmediatePropagation();e.preventDefault();
		var item_id = $(this).val();
		$.ajax({
			url: base_url + controller+'/getProductProcessList',
			type:'post',
			data:{ item_id:item_id },
			dataType:'json',
			success:function(data){
				$("#process_id").html(data.processOption);
				initSelect2();
			}
		});
	});
    $(document).on('click', '.saveItem', function () {
		

		var formData = {};
        $.each($(".itemFormInput"),function() {
            formData[$(this).attr("id")] = $(this).val();
        });
		
        $("#itemForm .error").html("");

        if (formData.item_id == "") {
			$(".item_id").html("Item Name is required.");
		}
		if (formData.process_id == "") {
			$(".process_id").html("Process is required.");
		}
		
		if (formData.rate_per_unit == "") {
			$(".rate_per_unit").html("Required.");
		}
       
        if (parseFloat(formData.rate) == 0 || formData.rate == '') {
            $(".rate").html("Rate is required.");
        }
		
		var isDuplicate = false;
		$(".item_row").each(function () {
			var existingItemId = $(this).find(".item_id").val();
			var existingProcessId = $(this).find(".process_id").val();
			
			if (formData.row_index === "" && formData.item_id === existingItemId && formData.process_id === existingProcessId) {
				isDuplicate = true;
				return false;
			}
		});

		if(isDuplicate){
			$(".item_id").html("Item already added.");
			return;
		}

        var errorCount = $('#itemForm .error:not(:empty)').length;

		if (errorCount == 0) {
			
			formData.process_name = $("#process_id :selected").text();
 			formData.item_name = $("#item_id :selected").text();
            AddRow(formData);
			$.each($('.itemFormInput'),function(){ $(this).val(""); });

            $("#itemForm input:hidden").val('')
            $('#itemForm #row_index').val("");
            initSelect2('itemModel');
			setTimeout(function(){
				initSelect2();
				setTimeout(function(){
					$("#itemForm #item_id").focus();
				},150);
			},100);	
        }
	});
});

function AddRow(data) {
    var tblName = "salesInvoiceItems";

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
	$(row).attr('id',((data.row_index == "") ? ($('#' + tblName + ' tbody tr:last').index()) : parseInt(data.row_index))).addClass('item_row');

    //Add index cell
	var countRow = (data.row_index == "") ? ($('#' + tblName + ' tbody tr:last').index() + 1) : (parseInt(data.row_index) + 1);
	var cell = $(row.insertCell(-1));
	cell.html(countRow);
	cell.attr("style", "width:5%;");

    if(data.is_revision == 1){
		var idInput = $("<input/>", { type: "hidden", name: "itemData["+itemCount+"][id]", value: "" });
		var refIdInput = $("<input/>", { type: "hidden", name: "itemData["+itemCount+"][ref_id]", value: data.ref_id });
	}else{
		var idInput = $("<input/>", { type: "hidden", name: "itemData["+itemCount+"][id]", value: data.id });
		var refIdInput = $("<input/>", { type: "hidden", name: "itemData["+itemCount+"][ref_id]", value: "" });
	}
    var itemIdInput = $("<input/>", { type: "hidden", name: "itemData["+itemCount+"][item_id]", class:"item_id", value: data.item_id });
	var itemIdErrorDiv = $("<div></div>", {class: "error item_id_"+data.item_id+"_"+data.process_id});
	
    cell = $(row.insertCell(-1));
    cell.html(data.item_name);
    cell.append(idInput);
    cell.append(itemIdInput);
	cell.append(refIdInput);
    cell.append(itemIdErrorDiv);

    var processInput = $("<input/>", { type: "hidden", name: "itemData["+itemCount+"][process_id]", class:"process_id", value: data.process_id });
	cell = $(row.insertCell(-1));
	cell.html(data.process_name);
	cell.append(processInput);

    var unitIdInput = $("<input/>", { type: "hidden", name: "itemData["+itemCount+"][rate_per_unit]", value: data.rate_per_unit });
	cell = $(row.insertCell(-1));
	cell.html(data.rate_per_unit);
	cell.append(unitIdInput);

    var priceInput = $("<input/>", { type: "hidden", name: "itemData["+itemCount+"][rate]", value: data.rate});
	cell = $(row.insertCell(-1));
	cell.html(data.rate);
	cell.append(priceInput);

    var itemRemarkInput = $("<input/>", { type: "hidden", name: "itemData["+itemCount+"][remark]", value: data.remark});
	cell = $(row.insertCell(-1));
	cell.html(data.remark);
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

	$(row).attr('data-item_data',JSON.stringify(data));

	itemCount++;
}

function Edit(data, button) {
	var row_index = $(button).closest("tr").index();
	$.each(data, function (key, value) {
		$("#itemForm #" + key).val(value);
	});

	setTimeout(function(){
		$("#itemForm #item_id").trigger('change');
		setTimeout(function(){ 
			$('#process_id').val(data.process_id);
			initSelect2();
		},500);
	},500);

	initSelect2('itemModel');
	$("#itemForm #row_index").val(row_index);
}

function Remove(button) {
    var tableId = "salesInvoiceItems";
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

function resSaveJwo(data,formId){
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