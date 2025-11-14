var itemCount = 0;
$(document).ready(function(){
	$(document).on('click', '.saveItem', function () {
		var formData = {};
        $.each($(".itemFormInput"),function() {
            formData[$(this).attr("id")] = $(this).val();
        });

		$("#itemForm .error").html("");
        if (formData.item_id == "") {
			$(".so_trans_id").html("Item Name is required.");
		}
        if (formData.package_no == "") {
            $(".package_no").html("Carton No is required.");
        }
		if (formData.total_qty == "" || parseFloat(formData.total_qty) <= 0) {
			$(".total_qty").html("Qty is required.");
		}
		if (formData.total_box == "" || parseFloat(formData.total_box) <= 0) {
			$(".total_box").html("Box is required.");
		}

        var errorCount = $('#itemForm .error:not(:empty)').length;

		if (errorCount == 0) {
			formData.item_name = $("#so_trans_id :selected").data('item_name');

			var fd = $('#itemForm #packStandardDiv').find('input').serializeArray();var packingDetail = [];
			$.each(fd, function (i, v) {
				if (v.name.startsWith('packingDetail')) {
					var match = v.name.match(/packingDetail\[(\d+)\]\[(.+)\]/);
					
					if(match){
						var index = match[1];
						var key = match[2];
						if(!packingDetail[index]){
							packingDetail[index] = {};
						}
						packingDetail[index][key] = v.value;
					}
				}
			});

			formData.packingDetail = [];
			for (var key in packingDetail) {
				if (packingDetail.hasOwnProperty(key)) {
					formData.packingDetail.push(packingDetail[key]);
				}
			}

			formData.packing_detail = JSON.stringify(formData.packingDetail); 
			if(formData.packingDetail == ""){
				$(".packDtl").html("Box detail required");
			}else{
				var package_no = formData.package_no;
				for (var i = 0; i < formData.total_box; i++) {
					formData.package_no = String(package_no).padStart(2, '0');
					AddRow(formData);
					package_no++;
				}
				$('#itemForm #row_index').val("");
				$("#itemForm #total_qty").val('');
				$("#itemForm #total_box").val('');
				$("#itemForm #item_id").val('');				
				$("#itemForm #so_trans_id").val('');				
				setTimeout(function(){
					setTimeout(function(){
						$("#itemForm #so_trans_id").focus();
					},150);
				},100);	
			}
        }
	});
	
	$(document).on('change', '#party_id', function () {
		var party_id = $("#party_id").val();
		if (party_id) {
			$.ajax({
				url: base_url + 'finalPacking/getPendingOrders',
				type: 'post',
				dataType:'json',
				data: { party_id: party_id },
				success: function (response) {
					$("#so_trans_id").html(response.options);
					$("#so_trans_id").select2();
				}
			});
		}else{
			$("#so_trans_id").html("");
			$("#so_trans_id").select2();
		} 
	});

	$(document).on('change', '#so_trans_id', function () {
		var so_trans_id = $("#so_trans_id").val();
		if (so_trans_id) {
			var item_id = $("#so_trans_id :selected").data('item_id');
			var packing_type = $("#so_trans_id :selected").data('packing_type');
			$("#item_id").val(item_id);
			$("#packing_type").val(packing_type);
		}
	});

	$(document).on('change', '#pack_mt_id', function () {
		var wt_pcs = $("#pack_mt_id :selected").data('wt_pcs');
		$('#pack_wt').val(wt_pcs);
	});
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
    var soInput = $("<input/>", { type: "hidden", name: "itemData["+itemCount+"][so_trans_id]", value: data.so_trans_id });
	var packDetailInput = $("<input/>", { type: "hidden", name: "itemData["+itemCount+"][packing_detail]", value: data.packing_detail });
	var packTypeInput = $("<input/>", { type: "hidden", name: "itemData["+itemCount+"][packing_type]", value: data.packing_type });
	
    cell = $(row.insertCell(-1));
    cell.html(data.item_name);
    cell.append(idInput, itemIdInput, soInput,packDetailInput,packTypeInput);

	var packageNoInput = $("<input/>", { type: "hidden", name: "itemData["+itemCount+"][package_no]", class:"packageNo", value: data.package_no });
	cell = $(row.insertCell(-1));
	cell.html(data.package_no);
	cell.append(packageNoInput);

    var qtyInput = $("<input/>", { type: "hidden", name: "itemData["+itemCount+"][total_qty]", class:"itemQty", value: data.total_qty });
	var qtyErrorDiv = $("<div></div>", { class: "error qty" + itemCount });
	cell = $(row.insertCell(-1));
	cell.html(data.total_qty);
	cell.append(qtyInput);   

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

	if(parseFloat(data.total_qty) != parseFloat(data.stock_qty)){
		cell.append(btnEdit);
		cell.append(btnRemove);
	}

	cell.attr("class", "text-center");
	cell.attr("style", "width:10%;");
	itemCount++;
}

function Edit(data, button) {
	var row_index = $(button).closest("tr").index();
	$.each(data, function (key, value) {
		$("#itemForm #" + key).val(value);
	});	
	
	setTimeout(function(){
		$("#packStandardDiv").html("");
		if(data.packing_detail){
			var packingDetail = JSON.parse(data.packing_detail)
			$.each(packingDetail, function (key, value) {
				standardHtml(value);
			});
		}
	},500);

	$("#itemForm #row_index").val(row_index);
	initSelect2();
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
}

function resFinalPacking(data,formId){
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

var sCount = 0;
function addStandard(){
	var pack_mt_id = $("#pack_mt_id").val();        
	var pack_wt = $("#pack_wt").val();        
	var std_qty = $("#std_qty").val();        
	$(".error").html("");
	var valid = 1;
	if(pack_mt_id == ""){ $('.pack_mt_id').html("Required."); valid=0; }             
	if(pack_wt == ""){ $('.pack_wt').html("Required."); valid=0; }             
	if(std_qty == ""){ $('.std_qty').html("Required."); valid=0; }             
	if(valid){
		var mt_name = $("#pack_mt_id :selected").text();
		standardHtml({pack_mt_id:pack_mt_id,pack_wt:pack_wt,std_qty:std_qty,item_name:mt_name});
		$("#pack_mt_id").val(""); 
		$("#pack_wt").val("");    
		$("#std_qty").val("");    
		$("#pack_mt_id").select2();    
	}        
}

function standardHtml(data){
var html = '<div class="col-md-4 form-group">';
	html += '<table class="table jpExcelTable">';
	html += '<tr>';
	html += 	'<td>'+data.item_name+'<br>(Qty : '+data.std_qty+' , Weight : '+data.pack_wt+')	</td>';
	html += 	'<td><button type="button" class="btn btn-outline-danger" onclick="removeStandard(this)"><i class="fa fa-trash"></i></button></td>';
	html += 	'<input type="hidden" name="packingDetail['+sCount+'][pack_mt_id]" class="size form-control" value="'+data.pack_mt_id+'">';
	html += 	'<input type="hidden" name="packingDetail['+sCount+'][pack_wt]" class="size form-control" value="'+data.pack_wt+'">';
	html += 	'<input type="hidden" name="packingDetail['+sCount+'][std_qty]" class="size form-control" value="'+data.std_qty+'">';
	html += 	'<input type="hidden" name="packingDetail['+sCount+'][item_name]" class="size form-control" value="'+data.item_name+'">';
	html += '</tr>';
	$("#packStandardDiv").append(html);
	html += '</table>';
	html += '</div>';
	sCount++;
}

function removeStandard(button){
	$(button).closest('.col-md-6.form-group').remove();
}