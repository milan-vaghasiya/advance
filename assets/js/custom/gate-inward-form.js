$(document).ready(function(){
    $("#party_id").trigger('change');
    $(document).on('change',"#party_id",function(){
        var party_id = $(this).val();
        getPoList(party_id);
    });

    $(document).on('change',"#po_id",function(){
        var po_id = $(this).val();
        getItemList(po_id);
    });

	$(document).on('click','.addBatch',function(e){
        e.stopImmediatePropagation();
        e.preventDefault();
        
        var formData = {};

        formData.grn_id = "";
        formData.id = "";

        formData.po_number = $("#po_id :selected").data('po_no');
        formData.item_name = $("#item_id :selected").text();
        formData.location_name = $("#location_id :selected").text();
        formData.fg_item_name = "";
        if($("#fg_item_id").val() != ""){
            formData.fg_item_name = $("#fg_item_id :selected").text();
        }
        formData.heat_no = $("#heat_no").val();
        formData.qty = $("#qty").val();
        formData.price = $("#price").val();
        formData.po_trans_id = $("#po_trans_id").val();
        formData.po_id = $("#po_id").val();
        formData.item_id = $("#item_id").val();
        formData.fg_item_id = $("#fg_item_id").val();
		formData.unit_id = $("#unit_id").val();
		formData.com_unit = $("#com_unit").val();
		formData.com_qty = $("#com_qty").val();
        formData.item_type = $("#item_id :selected").data('item_type');//26-02-25
        formData.so_trans_id = $("#so_trans_id").val();
        formData.location_id = $("#location_id").val();
        formData.item_remark = $("#item_remark").val();

        formData.trans_status = 1;        
       
        $(".error").html("");

        if(formData.item_id == ""){ 
            $('.item_id').html("Item Name is required.");
        }
		if(formData.location_id == ""){ 
            $('.location_id').html("Location is required.");
        }
        if(formData.qty == "" || parseFloat(formData.qty) == 0){ 
            $('.qty').html("Qty is required.");
        }
        if(formData.price == "" || parseFloat(formData.price) == 0){ 
            $('.price').html("Price is required.");
        }
        if(formData.item_type == 3 && formData.fg_item_id == ""){
            $('.fg_item_id').html("Finish Goods is required.");
        }
        var errorCount = $('.error:not(:empty)').length;

		if(errorCount == 0){
            AddBatchRow(formData);
            $("#heat_no").val("");
            $("#qty").val("");
            $("#item_id").val("");$("#item_id").select2();
            $("#fg_item_id").val("");$("#fg_item_id").select2();
            $("#po_trans_id").val("");
            $("#po_id").val("");$("#po_id").select2();   
            $("#so_trans_id").val("");         
            $("#price").val("");  
            $("#com_unit").val();//$("#com_unit").select2();  
            $("#com_qty").val();
            $("#uom_span").text("");
            $("#location_id").val("");
            $("#item_remark").val("");
            $(".error").html("");
            initSelect2();
        }
    });

    $(document).on('change keyup','.calcComQty',function(){ 
        var qty = $('#qty').val();
		var con_val = $('#com_unit :selected').data('conversion_value');
		var com_qty = 0;
		if(parseFloat(qty) > 0 && parseFloat(con_val) > 0){
			com_qty = (parseFloat(qty) * parseFloat(con_val)).toFixed(2);
		}
        $('#com_qty').val(com_qty);
    });

	$(document).on("change keyup", '#com_qty',function(e){
		e.stopImmediatePropagation();e.preventDefault();
		var com_qty = $('#com_qty').val();
		var con_val = $('#com_unit :selected').data('conversion_value');
		var qty = 0;
		if(parseFloat(com_qty) > 0 && parseFloat(con_val) > 0){
			qty = ((parseFloat(com_qty) / parseFloat(con_val)).toFixed(2));
		}
        $('#qty').val(qty);
	});

	$(document).on('change','#item_id',function(e){
        e.stopImmediatePropagation();e.preventDefault();
		var item_id = $(this).val();
        var item_type = $("#item_id :selected").data('item_type');
        var fg_item_id = $("#item_id :selected").data('fg_item_id');
		$.ajax({
			url: base_url + 'purchaseIndent/getItemWiseFgList',
			type:'post',
			data:{ item_id:item_id ,item_type:item_type, fg_item_id:fg_item_id},
			dataType:'json',
			success:function(data){
				$("#fg_item_id").html(data.fgoption);
				initSelect2();
			}
		});
	});
});

function resItemDetail(response = ""){
    if(response != ""){
        var itemDetail = response.data.itemDetail;
        if($("#po_id").find(":selected").val() == ""){
            $("#price").val((itemDetail.price || 0));
            $("#po_trans_id").val("");
            $("#so_trans_id").val("");
        }else{
            $("#disc_per").val(($("#item_id").find(":selected").data('disc_per') || 0));
            $("#price").val(($("#item_id").find(":selected").data('price') || 0));
            $("#po_trans_id").val(($("#item_id").find(":selected").data('po_trans_id') || 0));
            $("#so_trans_id").val(($("#item_id").find(":selected").data('so_trans_id') || 0));
        }  
        $("#com_unit").val(itemDetail.uom);   
        $("#uom_span").text(itemDetail.uom);  
    }else{
        $("#price").val("");
        $("#po_trans_id").val("");
        $("#uom_span").text("");
        $("#so_trans_id").val("");
    }
}

function getPoList(party_id){
    if(party_id){
        $.ajax({
            url : base_url + controller + '/getPoNumberList',
            type : 'post',
            data : { party_id:party_id },
            dataType : 'json'
        }).done(function(response){
            $("#po_id").html(response.poOptions);
        });
    }else{
        $("#po_id").html('<option value="">Select Purchase Order</option>');
    }
    initSelect2();
}

function getItemList(po_id){
    $.ajax({
        url : base_url + controller + '/getItemList',
        type : 'post',
        data : { po_id:po_id},
        dataType : 'json'
    }).done(function(response){
        $("#item_id").html(response.itemOptions);
		
        $("#fg_item_id").html(response.fgItemOptions);
    });
    initSelect2();
}

var itemCount = 0;
function AddBatchRow(data){
    $('table#batchTable tr#noData').remove();
    //Get the reference of the Table's TBODY element.
	var tblName = "batchTable";
	
	var tBody = $("#"+tblName+" > TBODY")[0];
	
	//Add Row.
	row = tBody.insertRow(-1);
    //Add index cell
	var countRow = $('#'+tblName+' tbody tr:last').index() + 1;
	var cell = $(row.insertCell(-1));
	cell.html(countRow);
	cell.attr("style","width:5%;");	

    var poIdInput = $("<input/>",{type:"hidden",name:"batchData["+itemCount+"][po_id]",value:data.po_id});
    var poTransIdInput = $("<input/>",{type:"hidden",name:"batchData["+itemCount+"][po_trans_id]",value:data.po_trans_id});
    var itemIdInput = $("<input/>",{type:"hidden",name:"batchData["+itemCount+"][item_id]",value:data.item_id});
    var comUnitInput = $("<input/>",{type:"hidden",name:"batchData["+itemCount+"][com_unit]",value:data.com_unit});
    var comQTyInput = $("<input/>",{type:"hidden",name:"batchData["+itemCount+"][com_qty]",value:data.com_qty});
    var soTransIdInput = $("<input/>",{type:"hidden",name:"batchData["+itemCount+"][so_trans_id]",value:data.so_trans_id});
    
    var cell = $(row.insertCell(-1));
	cell.html(data.po_number);
    cell.append(poIdInput);
	cell.append(poTransIdInput);
	cell.append(itemIdInput);
	cell.append(comUnitInput);
	cell.append(comQTyInput);
	cell.append(soTransIdInput);

    var cell = $(row.insertCell(-1));
	cell.html(data.item_name);
	
	var fgItemIdInput = $("<input/>",{type:"hidden",name:"batchData["+itemCount+"][fg_item_id]",value:data.fg_item_id});
    cell.append(fgItemIdInput);
    var cell = $(row.insertCell(-1));
	cell.html(data.fg_item_name);

    var mirIdInput = $("<input/>",{type:"hidden",name:"batchData["+itemCount+"][grn_id]",value:data.grn_id});
    var mirTransIdInput = $("<input/>",{type:"hidden",name:"batchData["+itemCount+"][id]",value:data.id});
    cell.append(mirIdInput);
    cell.append(mirTransIdInput);

    var heatNoInput = $("<input/>",{type:"hidden",name:"batchData["+itemCount+"][heat_no]",value:data.heat_no});
    cell = $(row.insertCell(-1));
	cell.html(data.heat_no);
    cell.append(heatNoInput);
    

    var batchQtyInput = $("<input/>",{type:"hidden",name:"batchData["+itemCount+"][qty]",value:data.qty});   
    cell = $(row.insertCell(-1));
	cell.html(data.qty);
    cell.append(batchQtyInput);

    var priceInput = $("<input/>",{type:"hidden",name:"batchData["+itemCount+"][price]",value:data.price});   
    cell = $(row.insertCell(-1));
	cell.html(data.price);
    cell.append(priceInput);

    var locationInput = $("<input/>",{type:"hidden",name:"batchData["+itemCount+"][location_id]",value:data.location_id});
    cell = $(row.insertCell(-1));
	cell.html(data.location_name);
    cell.append(locationInput);

    var itemRemarkInput = $("<input/>",{type:"hidden",name:"batchData["+itemCount+"][item_remark]",value:data.item_remark});
    cell = $(row.insertCell(-1));
	cell.html(data.item_remark);
    cell.append(itemRemarkInput);
    
    //Add Button cell.	
    var btnRemove = $('<button><i class="mdi mdi-trash-can-outline"></i></button>');
	btnRemove.attr("type", "button");
	btnRemove.attr("onclick", "batchRemove(this);");
	btnRemove.attr("style", "margin-left:4px;");
	btnRemove.attr("class", "btn btn-outline-danger btn-sm waves-effect waves-light");
    
    cell = $(row.insertCell(-1));
    if(data.trans_status == 1){
    	cell.append(btnRemove);
    }
    else{
    	cell.append('');
    }
    cell.attr("class","text-center");
    cell.attr("style","width:10%;");

    itemCount++;
}

function batchRemove(button){
    var row = $(button).closest("TR");
	var table = $("#batchTable")[0];
	table.deleteRow(row[0].rowIndex);

	$('#batchTable tbody tr td:nth-child(1)').each(function(idx, ele) {
        ele.textContent = idx + 1;
    });
	var countTR = $('#batchTable tbody tr:last').index() + 1;

    if (countTR == 0) {
        $("#batchTable tbody").html('<tr id="noData"><td colspan="9" align="center">No data available in table</td></tr>');
    }
}