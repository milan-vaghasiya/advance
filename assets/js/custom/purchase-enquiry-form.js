$(document).ready(function(){

	// $("#item_type_name").val($('#item_type :selected').text());
    $(document).on('change keyup','#item_type',function(){$("#item_type_name").val($('#item_type :selected').text());});
    $(document).on('change','#unit_id',function(){ $("#unit_name").val($('#unit_id :selected').text());});

    $(document).on('click','.saveItem',function(){
        var fd = $('.enquiryItemForm').find('input,select,textarea').serializeArray();

        var formObject = {};
        $.each(fd,function(i, v) {
            formObject[v.name] = v.value;
        });
        $(".item_name").html("");
		$(".qty").html("");
        $(".unit_id").html("");

        if(formObject.item_name == ""){
			$(".item_name").html("Item Name is required.");
		}else{
            if(formObject.qty == "" || formObject.qty == "0" || isNaN(formObject.qty)){
                $(".qty").html("Qty is required.");
            }else if(formObject.unit_id == "" || formObject.unit_id == "0" || isNaN(formObject.unit_id)){
                $(".unit_id").html("Unit is required.");
            }else{								
                AddRow(formObject);					
                resetFormByClass('enquiryItemForm');
                $("#item_name").focus();
                initSelect2();            
            }
		}
    });  

    $(document).on('click','.add-item',function(){
		$(".btn-close").show();
    	$(".btn-save").show();
	});    
    
    $(document).on('click','.btn-close',function(){
        $('#enquiryItemForm')[0].reset();
        $("#unit_id").select2();
        $('#enquiryItemForm .error').html("");
    });

	$('#item_name').typeahead({
		source: function(query, result)
		{
			$.ajax({
				url:base_url + 'purchaseEnquiry/itemSearch',
				method:"POST",
				global:false,
				data:{query:query},
				dataType:"json",
				success:function(data){
                    result($.map(data, function(item){return item;}));                    
                }
			});
		}
	});	 

	$(document).on('change','#item_name',function(){
		var item_name = $(this).val();
		$.ajax({
			url:base_url + controller + '/getItemData',
			data:{item_name:item_name},
			method:"POST",
			dataType:"json",
			success:function(data){
				if(data.item_id != ''){
					$("#item_id").val(data.item_id);
					$("#item_type").html(data.item_type);
					$("#unit_id").html(data.unit_id);
					$("#unit_id").trigger('change');
					initSelect2();
					$("#item_type_name").val($('#item_type :selected').text());
				}
			}
		});
	});

});

function AddRow(data) {
    var tblName = "purchaseEnqItems";

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

    var itemNameInput = $("<input/>",{type:"hidden",name:"itemData["+countRow+"][item_name]", class:"item_name",value:data.item_name});
    var itemIdInput = $("<input/>",{type:"hidden",name:"itemData["+countRow+"][item_id]",value:data.item_id});
	var transIdInput = $("<input/>",{type:"hidden",name:"itemData["+countRow+"][id]",value:data.id});
	cell = $(row.insertCell(-1));
	cell.html(data.item_name);
	cell.append(itemNameInput);
	cell.append(itemIdInput);
	cell.append(transIdInput);
	
    var itemTypeInput = $("<input/>",{type:"hidden",name:"itemData["+countRow+"][item_type]",value:data.item_type});
	cell = $(row.insertCell(-1));
	cell.html(data.item_type_name);
	cell.append(itemTypeInput);

	var qtyInput = $("<input/>",{type:"hidden",name:"itemData["+countRow+"][qty]",value:data.qty});
	cell = $(row.insertCell(-1));
	cell.html(data.qty);
	cell.append(qtyInput);

    var unitIdInput = $("<input/>",{type:"hidden",name:"itemData["+countRow+"][unit_id]",value:data.unit_id});
	cell = $(row.insertCell(-1));
	cell.html(data.unit_name);
	cell.append(unitIdInput);

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
	$.each(data, function (key, value) {
		$(".enquiryItemForm #" + key).val(value);
	});
	
	initSelect2();
	$(".enquiryItemForm #row_index").val(row_index);
}

function Remove(button) {
    //Determine the reference of the Row using the Button.
    var row = $(button).closest("TR");
    var table = $("#purchaseEnqItems")[0];
    table.deleteRow(row[0].rowIndex);
    $('#purchaseEnqItems tbody tr td:nth-child(1)').each(function(idx, ele) {
        ele.textContent = idx + 1;
    });
    var countTR = $('#purchaseEnqItems tbody tr:last').index() + 1;
    if(countTR == 0){
        $("#tempItem").html('<tr id="noData"><td colspan="7" align="center">No data available in table</td></tr>');
    }
}

function resetFormByClass(cls) {
    $('.' + cls + " input").each(function(){
		if($(this).data('resetval')){$(this).val($(this).data('resetval'));}else{$(this).val('');}
	});
    $('.' + cls).find('select').val('');
    $('.' + cls).find('textarea').val('');
    $('.' + cls+" .select2").select2();
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