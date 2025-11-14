$(document).ready(function(){
    
    $(document).on('change','#item_id',function(){
       $('#batch_no').val($('#item_id :selected').data('code'));
       $('#item_name').val($('#item_id :selected').data('name'));
    });
    
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

    $(document).on('click','.saveItem',function(){
        var fd = $('#challanItemForm').serializeArray();
        var formData = {};
        $.each(fd,function(i, v) {
            formData[v.name] = v.value;
        });

        $(".error").html("");
        if(formData.item_id == ""){
			$(".item_id").html("Item Name is required.");
		}else{
		    var item_ids = $("input[name='item_id[]']").map(function(){return $(this).val();}).get();
			if ($.inArray(formData.item_id,item_ids) >= 0 && formData.row_index == "") {
				$(".item_id").html("Item already added.");
			}else {
    			AddRow(formData);
                $('#challanItemForm')[0].reset();
                if($(this).data('fn') == "save"){
                    $("#item_idc").focus();
                    $("#item_id").select2();             
    				$("#row_index").val($('#qcChallanItems tbody').find('tr').length);        
                }else if($(this).data('fn') == "save_close"){
                    $("#itemModel").modal('hide');
    				$("#item_id").select2();
                } 
			}
		}
    });  
    
	$(document).on('click','.add-item',function(){
        $('#itemModel').modal('show');
		$(".btn-close").show();
    	$(".btn-save").show();
    	$("#row_index").val("");
	});
    
    $(document).on('click','.btn-efclose',function(){
        $('#challanItemForm')[0].reset();
		$("#item_id").select2();
		$('#challanItemForm .error').html('');	
    });		
});

function AddRow(data) {
	$('table#qcChallanItems tr#noData').remove();
	//Get the reference of the Table's TBODY element.
	var tblName = "qcChallanItems";
	
	var tBody = $("#"+tblName+" > TBODY")[0];
	
	//Add Row.
	if(data.row_index != ""){
		var trRow = data.row_index;
		//$("tr").eq(trRow).remove();
		$("#"+tblName+" tbody tr:eq("+trRow+")").remove();
	}
	var ind = (data.row_index == "")?-1:data.row_index; 
	row = tBody.insertRow(ind);
	
	//Add index cell
	var countRow = (data.row_index == "")?($('#'+tblName+' tbody tr:last').index() + 1):(parseInt(data.row_index) + 1);
	var cell = $(row.insertCell(-1));
	cell.html(countRow);
	cell.attr("style","width:5%;");	
	
	var itemIdInput = $("<input/>",{type:"hidden",name:"item_id[]",value:data.item_id});
	var transIdInput = $("<input/>",{type:"hidden",name:"trans_id[]",value:data.trans_id});
	cell = $(row.insertCell(-1));
	cell.html(data.item_name);
	cell.append(itemIdInput);
	cell.append(transIdInput);
	
	var batchNoInput = $("<input/>",{type:"hidden",name:"batch_no[]",value:data.batch_no});	
	cell = $(row.insertCell(-1));
	cell.html(data.batch_no);
	cell.append(batchNoInput);

	var itemRemarkInput = $("<input/>",{type:"hidden",name:"item_remark[]",value:data.item_remark});
	cell = $(row.insertCell(-1));
	cell.html(data.item_remark);
	cell.append(itemRemarkInput);
	
	//Add Button cell.
	cell = $(row.insertCell(-1));
	var btnRemove = $('<button><i class="mdi mdi-trash-can-outline"></i></button>');
	btnRemove.attr("type", "button");
	btnRemove.attr("onclick", "Remove(this);");
    btnRemove.attr("style","margin-left:4px;");
	btnRemove.attr("class", "btn btn-outline-danger waves-effect waves-light");

	var btnEdit = $('<button><i class="mdi mdi-square-edit-outline"></i></button>');
    btnEdit.attr("type", "button");
    btnEdit.attr("onclick", "Edit("+JSON.stringify(data)+",this);");
    btnEdit.attr("class", "btn btn-outline-warning waves-effect waves-light");

	cell.append(btnEdit);
	cell.append(btnRemove);
	cell.attr("class","text-center");
	cell.attr("style","width:10%;");
};

function Edit(data,button){
	var row_index = $(button).closest("tr").index();
    $("#itemModel").modal('show');
    $(".btn-save").hide();
    
    $.each(data,function(key, value) {
		$("#"+key).val(value);
	}); 	
	$("#item_id").select2();
	$("#row_index").val(row_index);	
    //Remove(button);
}

function Remove(button) {
	//Determine the reference of the Row using the Button.
	var row = $(button).closest("TR");
	var table = $("#qcChallanItems")[0];
	table.deleteRow(row[0].rowIndex);
	$('#qcChallanItems tbody tr td:nth-child(1)').each(function(idx, ele) {
        ele.textContent = idx + 1;
    });
	var countTR = $('#qcChallanItems tbody tr:last').index() + 1;
	if(countTR == 0){
		$("#tempItem").html('<tr id="noData"><td colspan="5" align="center">No data available in table</td></tr>');
	}	
};

function saveQcChallan(formId){
	var fd = $('#'+formId).serialize();
	$.ajax({
		url: base_url + controller + '/save',
		data:fd,
		type: "POST",
		dataType:"json",
	}).done(function(data){
		if(data.status===0){
			$(".error").html("");
			$.each( data.message, function( key, value ) {
				$("."+key).html(value);
			});
		}else if(data.status==1){
            Swal.fire( 'Success', data.message, 'success' );
            window.location = data.url;
		}else{
            Swal.fire( 'Sorry...!', data.message, 'error' );
		}				
	});
}