$(document).ready(function(){        

    $(document).on('change','#challan_type',function(){
        var challan_type = $(this).val();
        if(challan_type){
            $.ajax({
				url: base_url + controller + '/getPartyList',
				data: {challan_type:challan_type},
				type: "POST",
				dataType: 'json',
				success: function (data) {
					$("#party_id").html("");
					$("#party_id").html(data.options);
					$("#party_id").select2();
				}
			});
        }
    });

	$(document).on('change','#die_id',function(){
        var item_id = $('#die_id :selected').data('item_id');
        $('#item_id').val(item_id);
		var die_set_no = $('#die_id :selected').data('die_set_no');
        $('#die_set_no').val(die_set_no);
    });

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
		formData.item_name = $('#die_id :selected').text();
		formData.prc_number = $('#prc_id :selected').text();
        $("#itemForm .error").html("");
        if (formData.die_id == "") {
			$("#itemForm .die_id").html("Die is required.");
		}

		// var item_ids = $(".item_id").map(function () { return $(this).val(); }).get();
        // if ($.inArray(formData.item_id, item_ids) >= 0 && formData.row_index == "") {
        //     $(".item_name").html("Item already added.");
        // }

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
    var tblName = "dieChallanItems";

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
	cell.attr("class", "text-center");

    var itemIdInput = $("<input/>",{type:"hidden",name:"itemData["+countRow+"][item_id]", class:"item_id",value:data.item_id});
	var transIdInput = $("<input/>",{type:"hidden",name:"itemData["+countRow+"][id]",value:data.id});
	var dieIdInput = $("<input/>",{type:"hidden",name:"itemData["+countRow+"][die_set_no]",class:"die_set_no", value:data.die_set_no});
	var dieMasterIdInput = $("<input/>",{type:"hidden",name:"itemData["+countRow+"][die_id]",value:data.die_id});
	cell = $(row.insertCell(-1));
	cell.html(data.item_name);
	cell.append(itemIdInput);
	cell.append(transIdInput);
	cell.append(dieIdInput);
	cell.append(dieMasterIdInput);
	cell.attr("style", "width:40%;");
	cell.attr("class", "text-center");
	
	var prcIdInput = $("<input/>",{type:"hidden",name:"itemData["+countRow+"][prc_id]", class:"prc_id",value:''});
	// cell = $(row.insertCell(-1));
	// cell.html(data.prc_number);
	cell.append(prcIdInput);
	// cell.attr("style", "width:20%;");
	// cell.attr("class", "text-center");

	var itemRemarkInput = $("<input/>",{type:"hidden",name:"itemData["+countRow+"][item_remark]",value:data.item_remark});
	cell = $(row.insertCell(-1));
	cell.html(data.item_remark);
	cell.append(itemRemarkInput);
	cell.attr("style", "width:25%;");
	cell.attr("class", "text-center");

    //Add Button cell.
	cell = $(row.insertCell(-1));
	var btnRemove = $('<button><i class="mdi mdi-trash-can-outline"></i></button>');
	btnRemove.attr("type", "button");
	btnRemove.attr("onclick", "Remove(this);");
	btnRemove.attr("style", "margin-left:4px;");
	btnRemove.attr("class", "btn btn-outline-danger waves-effect waves-light");

	// var btnEdit = $('<button><i class="mdi mdi-square-edit-outline"></i></button>');
	// btnEdit.attr("type", "button");
	// btnEdit.attr("onclick", "Edit(" + JSON.stringify(data) + ",this);");
	// btnEdit.attr("class", "btn btn-outline-warning waves-effect waves-light");

	// cell.append(btnEdit);
	var trans_status = data.trans_status || 0;
	if(trans_status == 0){
		cell.append(btnRemove);
		cell.attr("class", "text-center");
		cell.attr("style", "width:10%;");
	}
	
}

function Edit(data, button) {
	var row_index = $(button).closest("tr").index();
	$("#itemModel").modal('show');
	$(".btn-close").hide();
	$(".btn-save").hide();
	$.each(data, function (key, value) {
		$("#itemForm #" + key).val(value);
	});
	
	initSelect2();
	$("#itemForm #row_index").val(row_index);
}

function Remove(button) {
    var tableId = "dieChallanItems";
	//Determine the reference of the Row using the Button.
	var row = $(button).closest("TR");
	var table = $("#"+tableId)[0];
	table.deleteRow(row[0].rowIndex);
	$('#'+tableId+' tbody tr td:nth-child(1)').each(function (idx, ele) {
		ele.textContent = idx + 1;
	});
	var countTR = $('#'+tableId+' tbody tr:last').index() + 1;
	if (countTR == 0) {
		$("#tempItem").html('<tr id="noData"><td colspan="4" align="center">No data available in table</td></tr>');
	}

}

function resSaveDieChallan(data,formId){
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