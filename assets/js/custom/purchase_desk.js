$(document).ready(function(){
	
	var enqListPageLimit = 15;
    setTimeout(function(){ loadDesk(); }, 50);
    
	$(document).on('click','.stageFilter',function(){
        var postdata = $(this).data('postdata') || {};
		$('#next_page').val('0');
		postdata.start = 0;
		postdata.length = parseFloat(enqListPageLimit);
		postdata.page = 0;
		
		loadHtmlData({'fnget':'getPurchaseEnqList','rescls':'enqList','postdata':postdata});
	});
	
	$('.quicksearch').keyup(delay(function (e) {
		//if(e.which === 13 && !e.shiftKey) {
			e.preventDefault();
			$('#next_page').val('0');
			var postdata = $('.stageFilter.active').data('postdata') || {};
			delete postdata.page;delete postdata.start;delete postdata.length;
			postdata.limit = parseFloat(enqListPageLimit);
			postdata.skey = $(this).val();
			loadHtmlData({'fnget':'getPurchaseEnqList','rescls':'enqList','postdata':postdata});
		//}
	}));

	const scrollEle = $('#purchaseBoard .simplebar-content-wrapper');
	var ScrollDebounce = true;
	$(scrollEle).scroll(function() {
		if($(this).scrollTop() + $(this).innerHeight() >= ($(this)[0].scrollHeight - 10)) {
			if(ScrollDebounce){
				ScrollDebounce = false;
				var postdata = $('.stageFilter.active').data('postdata') || {};
    			var np = parseFloat($('#next_page').val()) || 0;
    			postdata.start = np * parseFloat(enqListPageLimit);
    			postdata.length = enqListPageLimit;
    			postdata.page = np;
    			loadHtmlData({'fnget':'getPurchaseEnqList','rescls':'enqList','postdata':postdata,'scroll_type':1});
				setTimeout(function () { ScrollDebounce = true; }, 500);		
			}
		}
	});

	$(document).on('change','#item_type',function(){
		var item_type = $(this).val();
		if(item_type){
			$.ajax({
				url:base_url + controller + '/getItemList',
				data:{item_type:item_type},
				method:"POST",
				dataType:"json",
				success:function(data){
					$("#item_id").html('');
					$("#item_id").html(data.options);
				}
			});
		}
	}); 

	$(document).on('change','#item_id',function(){
		var item_id = $(this).val();
		var item_type = $('#item_type').val();

		if(item_id == '-1'){
			$('.newItem').show();
		}else{
			$('.newItem').hide();
			$('#item_name').val($('#item_id :selected').text());
			
			$.ajax({
				url: base_url + 'purchaseIndent/getItemWiseFgList',
				type:'post',
				data:{ item_id:item_id ,item_type:item_type},
				dataType:'json',
				success:function(data){
					$("#fg_item_id").html(data.fgoption);
					initSelect2();
				}
			});
		}
	}); 
	
	$(document).on('change','#compare_item',function(){
		var compare_item = $(this).val();
		if(compare_item){
			$.ajax({
				url:base_url + controller + '/getCompareList',
				data:{item_name:compare_item},
				method:"POST",
				dataType:"json",
				success:function(data){
					$("#compareItemList").html('');
					$("#compareItemList").html(data.itemList);
				}
			});
		}
	}); 
	
	$(document).on('click','.compareBtn',function(){
		
		var partyIdArray = $(".partyCheck").map(function () { 
			if(this.checked){
				return $(this).val(); 
			}
		}).get();
		var compare_item = $('#compare_item :selected').val();		
		if(partyIdArray){
			$.ajax({
				url:base_url + controller + '/getPartyComparison',
				data:{party_id:partyIdArray,item_name:compare_item},
				method:"POST",
				dataType:"json",
				success:function(data){
					$("#partyData").html('');
					$("#partyData").html(data.partyData);
				}
			});
		}

	});
	
	/*Created By @Raj 31-12-2024*/
	$(document).on('click', '.add-item', function () {
		$('#itemForm')[0].reset();
		$("#itemForm input:hidden").val('');
		$('#itemForm #row_index').val("");
        $("#itemForm .error").html();
		$("#itemForm #row_index").val("");
		
		$("#itemModel").modal('show');
		$(".btn-close").show();
		$(".btn-save").show();
		
		setTimeout(function(){ 
			$("#itemForm #item_id").focus();
			setPlaceHolder();initSelect2();
		},500);
		
	});

	$('.newItem').hide();
	var item_id = $('#item_id').val();
	if(item_id == '-1'){
		$('.newItem').show();
	}else{
		$('.newItem').hide();
	}
	
	$(document).on('click', '.saveItem', function () {
		var fd = $('#itemForm').serializeArray();
		var formData = {};
		$.each(fd, function (i, v) {
			formData[v.name] = v.value;
		});
        $("#itemForm .error").html("");
		
		if (formData.item_type == "") {
			$(".item_type").html("Item Type is required.");
		}

        if (formData.item_id == "") {
			$(".item_id").html("Item Name is required.");
		}
		
		if (formData.item_id == "-1" && formData.item_name == "") {
			$(".item_name").html("New Item Name is required.");
		}
		
        if (formData.qty == "" || parseFloat(formData.qty) == 0) {
            $(".qty").html("Qty is required.");
        }
        if (formData.uom == "") {
            $(".uom").html("Unit is required.");
        }

        var item_ids = $(".item_id").map(function () { return $(this).val(); }).get();
        if ($.inArray(formData.item_id, item_ids) >= 0 && formData.row_index == "") {
            $(".item_id").html("Item already added.");
        }
		
        var errorCount = $('#itemForm .error:not(:empty)').length;

		if (errorCount == 0) {
			formData.item_id_name = $('#item_id :selected').text();
			formData.item_type_name = $('#item_type :selected').text();

            AddRow(formData);
			 
            $('#itemForm')[0].reset();
            $("#itemForm input:hidden").val('')
            $('#itemForm #row_index').val("");
            initSelect2('itemModel');
            if ($(this).data('fn') == "save") {
                $("#itemForm #item_id").focus();
            } else if ($(this).data('fn') == "save_close") {
                $("#itemModel").modal('hide');
            }

        }
	});
});

function quoteConfirm(data){
	var call_function = data.call_function;
	if(call_function == "" || call_function == null){call_function="edit";}

	var fnsave = data.fnsave;
	if(fnsave == "" || fnsave == null){fnsave="save";}

	var controllerName = data.controller;
	if(controllerName == "" || controllerName == null){controllerName=controller;}
	
	var modal_id = data.modal_id || "";
	var init_action = data.init_action || "";

	var enq_id = data.postData.id;
	var partyName = data.postData.party_name;
	var enquiry_no = data.postData.trans_number;
	var enquiry_date = data.postData.trans_date;
	
	var ajaxParam = {
		type: "POST",   
		url: base_url + controllerName +'/' + call_function,   
		data: data.postData
	}; 

	if(modal_id == ""){ 
		ajaxParam = {
			url: base_url + controllerName +'/' + call_function,   
			type: "POST",   
			data: data.postData,
			dataType : "JSON"
		}; 
	}
	
	$.ajax(ajaxParam).done(function(response){
		if(modal_id != ""){
			initModal(data,response);
		}else{
			window[init_action](response);
		}
		
		$("#party_name").html(partyName);
		$("#enquiry_no").html(enquiry_no);
		$("#enquiry_date").html(enquiry_date);
		$("#enq_id").val(enq_id);

		$('.floatOnly').keypress(function(event) {
			if ((event.which != 46 || $(this).val().indexOf('.') != -1) && (event.which < 48 || event.which > 57)) {
				event.preventDefault();
			}
		});
	});
}

function AddRow(data){
	var tblName = "purchaseOrderItems";
	
	$('table#'+tblName+' tr#noData').remove();

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

    var idInput = $("<input/>", { type: "hidden", name: "itemData["+countRow+"][id]", value: data.id });
    var itemTypeInput = $("<input/>", { type: "hidden", name: "itemData["+countRow+"][item_type]", class:"item_type", value: data.item_type });
	var reqIdInput = $("<input/>", { type: "hidden", name: "itemData["+countRow+"][req_id]", value: data.req_id });
    var fromEntryTypeInput = $("<input/>", { type: "hidden", name: "itemData["+countRow+"][from_entry_type]", value: data.from_entry_type });
	var fgItemIdInput = $("<input/>", { type: "hidden", name: "itemData["+countRow+"][fg_item_id]", value: data.fg_item_id });
	var soTransIdInput = $("<input/>", { type: "hidden", name: "itemData["+countRow+"][so_trans_id]", value: data.so_trans_id }); 
	cell = $(row.insertCell(-1));
    cell.html(data.item_type_name);
    cell.append(idInput);
    cell.append(itemTypeInput);
	cell.append(fgItemIdInput);
    cell.append(reqIdInput);
    cell.append(fromEntryTypeInput);
	cell.append(soTransIdInput);
	
    var itemIdInput = $("<input/>", { type: "hidden", name: "itemData["+countRow+"][item_id]", class:"item_id", value: data.item_id });
    var itemNameInput = $("<input/>", { type: "hidden", name: "itemData["+countRow+"][item_name]", class:"item_name", value: data.item_name });
	cell = $(row.insertCell(-1));
	if(data.item_id == "-1"){
		cell.html(data.item_name);
	}else{
		cell.html(data.item_id_name);		
	}
    cell.append(itemIdInput);
    cell.append(itemNameInput);

    var unitIdInput = $("<input/>", { type: "hidden", name: "itemData["+countRow+"][uom]", value: data.uom });
	cell = $(row.insertCell(-1));
	cell.html(data.uom);
	cell.append(unitIdInput);
	
    var qtyInput = $("<input/>", { type: "hidden", name: "itemData["+countRow+"][qty]", class:"qty", value: data.qty });
	var qtyErrorDiv = $("<div></div>", { class: "error qty" + countRow });
	cell = $(row.insertCell(-1));
	cell.html(data.qty);
	cell.append(qtyInput);
	cell.append(qtyErrorDiv);

    var remarkInput = $("<input/>", { type: "hidden", name: "itemData["+countRow+"][item_remark]", value: data.item_remark});
	cell = $(row.insertCell(-1));
	cell.html(data.item_remark);
	cell.append(remarkInput);

    //Add Button cell.
	cell = $(row.insertCell(-1));
	var btnRemove = $('<button><i class="mdi mdi-trash-can-outline"></i></button>');
	btnRemove.attr("type", "button");
	btnRemove.attr("onclick", "Remove(this);");
	btnRemove.attr("style", "margin-left:4px;");
	btnRemove.attr("class", "btn btn-sm btn-outline-danger waves-effect waves-light");

	var btnEdit = $('<button><i class="mdi mdi-square-edit-outline"></i></button>');
	btnEdit.attr("type", "button");
	btnEdit.attr("onclick", "Edit(" + JSON.stringify(data) + ",this);");
	btnEdit.attr("class", "btn btn-sm btn-outline-warning waves-effect waves-light");

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
	var itemId = null; 
	var fg_item_id="";
	$.each(data, function (key, value) {
		if (key === "item_id") {
            itemId = value;
		}
		if (key == "fg_item_id") { fg_item_id = value; }
		if(key === "item_id" && value === "-1"){
			$('.newItem').show();
		}else{
			$('.newItem').hide();
		}
		$("#itemForm #" + key).val(value);
	});

	var item_id  = $("#item_id").val();
	var item_type  = $("#item_type").val();
	$.ajax({
		url:base_url + "purchaseIndent/getItemWiseFgList",
		type:'post',
		data:{item_id:item_id,fg_item_id:fg_item_id,item_type:item_type}, 
		dataType:'json',
		success:function(data){
			$("#fg_item_id").html("");
			$("#fg_item_id").html(data.fgoption);
		}
	});

	initSelect2('itemModel');
	$("#itemForm #row_index").val(row_index);
	
	/* Get Item Option selected Item Type Wise */
	$("#itemForm #item_type").on('change', function () {
        var itemType = $(this).val();
        updateItemNameDropdown(itemType,itemId);
    });

    var itemType = $("#itemForm #item_type").val();
    updateItemNameDropdown(itemType,itemId);


}

function updateItemNameDropdown(itemType,itemId) {
    $.ajax({
		type: "POST",   
		url: base_url  + 'purchaseDesk/getItemList',   
		data: {item_type:itemType,item_id:itemId},
	}).done(function(response){
		var data = JSON.parse(response);
		if(data.status == 1){			
			$("#item_id").html(data.options);
		}
	});
}



function Remove(button) {
    var tableId = "purchaseOrderItems";
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

function resSavePoDesk(data,formId){
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

function initForm(postData,response){
	var button = postData.button;if(button == "" || button == null){button="both";};
	var fnedit = postData.fnedit;if(fnedit == "" || fnedit == null){fnedit="edit";}
	var fnsave = postData.fnsave;if(fnsave == "" || fnsave == null){fnsave="save";}
	var controllerName = postData.controller;if(controllerName == "" || controllerName == null){controllerName=controller;}
	var savebtn_text = postData.savebtn_text;
	var savebtn_icon = postData.savebtn_icon || "";
	if(savebtn_text == "" || savebtn_text == null){savebtn_text='<i class="fa fa-check"></i> Save';}
	else{ savebtn_text = ((savebtn_icon != "")?'<i class="'+savebtn_icon+'"></i> ':'')+savebtn_text; }

	var resFunction = postData.res_function || "";
	var jsStoreFn = postData.js_store_fn || 'storeEnquiry';
	var txt_editor = postData.txt_editor || '';

	var fnJson = "{'formId':'"+postData.form_id+"','fnsave':'"+fnsave+"','controller':'"+controllerName+"','txt_editor':'"+txt_editor+"'}";

	$("#"+postData.modal_id).modal('show');
	$("#"+postData.modal_id).addClass('modal-i-'+zindex);
	$('.modal-i-'+(zindex - 1)).removeClass('show');
	$("#"+postData.modal_id).css({'z-index':zindex,'overflow':'auto'});
	$("#"+postData.modal_id).addClass(postData.form_id+"Modal");
	$("#"+postData.modal_id+' .modal-title').html(postData.title);
	$("#"+postData.modal_id+' .modal-body').html('');
	$("#"+postData.modal_id+' .modal-body').html(response);
	$("#"+postData.modal_id+" .modal-body form").attr('id',postData.form_id);
	if(resFunction != ""){
		$("#"+postData.modal_id+" .modal-body form").attr('data-res_function',resFunction);
	}
	$("#"+postData.modal_id+" .modal-footer .btn-save").html(savebtn_text);
	$("#"+postData.modal_id+" .modal-footer .btn-save").attr('onclick',jsStoreFn+"("+fnJson+");");
	$("#"+postData.modal_id+" .btn-custom-save").attr('onclick',jsStoreFn+"("+fnJson+");");

	$("#"+postData.modal_id+" .modal-header .btn-close").attr('data-modal_id',postData.modal_id);
	$("#"+postData.modal_id+" .modal-header .btn-close").attr('data-modal_class',postData.form_id+"Modal");
	$("#"+postData.modal_id+" .modal-footer .btn-close-modal").attr('data-modal_id',postData.modal_id);
	$("#"+postData.modal_id+" .modal-footer .btn-close-modal").attr('data-modal_class',postData.form_id+"Modal");

	if(button == "close"){
		$("#"+postData.modal_id+" .modal-footer .btn-close-modal").show();
		$("#"+postData.modal_id+" .modal-footer .btn-save").hide();
	}else if(button == "save"){
		$("#"+postData.modal_id+" .modal-footer .btn-close-modal").hide();
		$("#"+postData.modal_id+" .modal-footer .btn-save").show();
	}else{
		$("#"+postData.modal_id+" .modal-footer .btn-close-modal").show();
		$("#"+postData.modal_id+" .modal-footer .btn-save").show();
	}
	
	setTimeout(function(){ 
		initMultiSelect();setPlaceHolder();setMinMaxDate();initSelect2();		
	}, 5);
	setTimeout(function(){
		$('#'+postData.modal_id+'  :input:enabled:visible:first, select:first').focus();
	},500);
	zindex++;
}

function loadform(data){
	var call_function = data.call_function;
	if(call_function == "" || call_function == null){call_function="edit";}

	var fnsave = data.fnsave;
	if(fnsave == "" || fnsave == null){fnsave="save";}

	var controllerName = data.controller;
	if(controllerName == "" || controllerName == null){controllerName=controller;}	

	$.ajax({ 
		type: "POST",   
		url: base_url + controllerName + '/' + call_function,   
		data: data.postData,
	}).done(function(response){
		initForm(data,response);
	});
}

/***** GET DYNAMIC DATA *****/
function loadHtmlData(data){
	
	var postData = data.postdata || {};
	var fnget = data.fnget || "";
	var controllerName = data.controller || controller;
	var rescls = data.rescls || "dynamicData";
	var scrollType = data.scroll_type || "";
	
	$.ajax({
		url: base_url + controllerName + '/' + fnget,
		data:postData,
		type: "POST",
		dataType:"json",
		global:false,
	}).done(function(res){
		$("#next_page").val(res.next_page);
		if(!scrollType){$("."+rescls).html(res.enqList);}else{$("."+rescls).append(res.enqList);}
		loading = true;
		var img = base_url + 'assets/images/background/dnf_1.png';
		var img2 = base_url + 'assets/images/background/dnf_2.png';
	});
}

function storeEnquiry(postData){
	setPlaceHolder();

	var formId = postData.formId;
	var fnsave = postData.fnsave || "save";
	var controllerName = postData.controller || controller;
	var resFunctionName =$("#"+formId).data('res_function') || "";
	var form = $('#'+formId)[0];
	var fd = new FormData(form);
	$.ajax({
		url: base_url + controllerName + '/' + fnsave,
		data:fd,
		type: "POST",
		processData:false,
		contentType:false,
		dataType:"json",
	}).done(function(data){
		if(resFunctionName != ""){
			console.log(resFunctionName);
			window[resFunctionName](data,formId);
		}else{
			if(data.status==1){
				$('#'+formId)[0].reset(); closeModal(formId); $(".stageFilter.active").trigger("click");
				Swal.fire({ icon: 'success', title: data.message});	
			}else{
				if(typeof data.message === "object"){
					$(".error").html("");
					$.each( data.message, function( key, value ) {$("."+key).html(value);});
				}else{
					Swal.fire({ icon: 'error', title: data.message });
				}			
			}
		}				
	});
}

function delay(callback, ms=500) {
	var timer = 0;
	return function() {
		var context = this, args = arguments;
		clearTimeout(timer);
		timer = setTimeout(function () { callback.apply(context, args); }, ms || 0);
	};
}

function initSelect2(){
	//$(".select2").select2({with:null});
	$(".select2").each(function () {
		$(this).select2();
	});	

	$(".modal .select2").each(function () {
		$(this).select2({
			dropdownParent: $('#'+$(this).closest('.modal').attr('id')),
		});
	});	
}

function confirmPurchaseStore(data){
	setPlaceHolder();

	var formId = data.formId || "";
	var fnsave = data.fnsave || "save";
	var controllerName = data.controller || controller;

	if(formId != ""){
		var form = $('#'+formId)[0];
		var fd = new FormData(form);
		var resFunctionName = $("#"+formId).data('res_function') || "";
		var msg = "Are you sure want to save this record ?";
		var ajaxParam = {
			url: base_url + controllerName + '/' + fnsave,
			data:fd,
			type: "POST",
			processData:false,
			contentType:false,
			dataType:"json"
		};
	}else{
		var fd = data.postData;
		var resFunctionName = data.res_function || "";
		var msg = data.message || "Are you sure want to save this change ?";
		var ajaxParam = {
			url: base_url + controllerName + '/' + fnsave,
			data:fd,
			type: "POST",
			dataType:"json"
		};
	}
	Swal.fire({
		title: 'Are you sure?',
		text: msg,
		icon: 'warning',
		showCancelButton: true,
		confirmButtonColor: '#3085d6',
		cancelButtonColor: '#d33',
		confirmButtonText: 'Yes, Do it!',
	}).then(function(result) {
		if (result.isConfirmed){
			$.ajax(ajaxParam).done(function(response){
				if(formId != ""){$('#'+formId)[0].reset(); closeModal(formId);}
				if(resFunctionName != ""){
					window[resFunctionName](response,formId);
				}else{
					if(response.status==1){
						initTable();
						Swal.fire( 'Success', response.message, 'success' );
					}else{
						if(typeof response.message === "object"){
							$(".error").html("");
							$.each( response.message, function( key, value ) {$("."+key).html(value);});
						}else{
							initTable();
							Swal.fire( 'Sorry...!', response.message, 'error' );
						}			
					}	
				}			
			});
		}
	});
}

function loadDesk(){
	$(".stageFilter.active").trigger("click");
}

function loadItemDetail(data,form_id=""){
	var enq_id = data.enq_id;
	$.ajax({
		url: base_url + controller + '/getEnqDetail',
		type:'post',
		data:{id:enq_id},
		dataType:'json',
		success:function(data){
			$(".enqDetail").html(data.enqDetail);
			$(".itemDetail").html(data.itemDetail);
			$(".quoteDetail").html(data.quoteDetail);
		}
	});
}

function trashEnquiry(data){
	var controllerName = data.controller || controller;
	var fnName = data.fndelete || "delete";
	var msg = data.message || "Record";
	var send_data = data.postData;
	var resFunctionName = data.res_function || "";
	
	Swal.fire({
		title: 'Are you sure?',
		text: "You won't be able to revert this!",
		icon: 'warning',
		showCancelButton: true,
		confirmButtonColor: '#3085d6',
		cancelButtonColor: '#d33',
		confirmButtonText: 'Yes, delete it!',
	}).then(function(result) {
		if (result.isConfirmed){
			$.ajax({
				url: base_url + controllerName + '/' + fnName,
				data: send_data,
				type: "POST",
				dataType:"json",
			}).done(function(response){
				if(resFunctionName != ""){
					window[resFunctionName](response);
				}else{
					if(response.status==0){
						Swal.fire( 'Sorry...!', response.message, 'error' );
					}else{
						initTable();
						Swal.fire( 'Deleted!', response.message, 'success' );
					}	
				}
			});
		}
	});
	
}

function getPurchaseResponse(data,formId=""){ 
	if(data.status==1){
		if(formId){
			$('#'+formId)[0].reset();
			closeModal(formId);
		}
		Swal.fire({
			title: "Success",
			text: data.message,
			icon: "success",
			showCancelButton: false,
			confirmButtonColor: "#3085d6",
			cancelButtonColor: "#d33",
			confirmButtonText: "Ok!"
		}).then((result) => {
			loadItemDetail(data);
			loadDesk();
		});
		
	}else{
		if(typeof data.message === "object"){
			$(".error").html("");
			$.each( data.message, function( key, value ) {$("."+key).html(value);});
		}else{
			Swal.fire({ icon: 'error', title: data.message });
		}			
	}
}

function compareResponse(data,formId=""){ 
	if(data.status==1){
		
		Swal.fire({
			title: "Success",
			text: data.message,
			icon: "success",
			showCancelButton: false,
			confirmButtonColor: "#3085d6",
			cancelButtonColor: "#d33",
			confirmButtonText: "Ok!"
		}).then((result) => {
			$(".compareBtn").trigger("click");
			loadItemDetail(data);
			loadDesk();
		});
		
	}else{
		if(typeof data.message === "object"){
			$(".error").html("");
			$.each( data.message, function( key, value ) {$("."+key).html(value);});
		}else{
			Swal.fire({ icon: 'error', title: data.message });
		}			
	}
}

function storeQuotation(data){
	setPlaceHolder();

	var formId = data.formId || "";
	var fnsave = data.fnsave || "save";
	var controllerName = data.controller || controller;
	var form = $('#'+formId)[0];
	var fd = new FormData(form);
	var resFunctionName = $("#"+formId).data('res_function') || "";
	
	$.ajax({
		url: base_url + controllerName + '/' + fnsave,
		data:fd,
		type: "POST",
		processData:false,
		contentType:false,
		dataType:"json",
	}).done(function(response){ 
		if(resFunctionName != ""){
			window[resFunctionName](response,formId);
		}else{
			if(response.status==1){
				$('#'+formId)[0].reset(); closeModal(formId);
				Swal.fire( 'Success', response.message, 'success' );
			}else{
				if(typeof response.message === "object"){
					$(".error").html("");
					$.each( response.message, function( key, value ) {$("."+key).html(value);});
				}else{
					Swal.fire( 'Sorry...!', response.message, 'error' );
				}			
			}
		}	
	});
}