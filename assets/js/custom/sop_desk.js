$(document).ready(function(){
	var prcListPageLimit = 15;
    setTimeout(function(){ loadDesk(); }, 50);
    
	$('.quicksearch').keyup(delay(function (e) {
		//if(e.which === 13 && !e.shiftKey) {
			e.preventDefault();
			$('#next_page').val('0');
			var postdata = $('.stageFilter.active').data('postdata') || {};
			delete postdata.page;delete postdata.start;delete postdata.length;
			postdata.limit = parseFloat(prcListPageLimit);
			postdata.skey = $(this).val();
			loadHtmlData({'fnget':'getPRCList','rescls':'prcList','postdata':postdata});
		//}
	}));
	
	const scrollEle = $('#sopBoard .simplebar-content-wrapper');
	var ScrollDebounce = true;
	$(scrollEle).scroll(function() {
		if($(this).scrollTop() + $(this).innerHeight() >= ($(this)[0].scrollHeight - 10)) {
			if(ScrollDebounce){
				ScrollDebounce = false;
				var postdata = $('.stageFilter.active').data('postdata') || {};
    			var np = parseFloat($('#next_page').val()) || 0;
    			postdata.start = np * parseFloat(prcListPageLimit);
    			postdata.length = prcListPageLimit;
    			postdata.page = np;
    			loadHtmlData({'fnget':'getPRCList','rescls':'prcList','postdata':postdata,'scroll_type':1});
				setTimeout(function () { ScrollDebounce = true; }, 500);		
			}
		}
	});

    $(document).on('change','#party_id',function(){
        var party_id = $(this).val();
		
		$.ajax({
			url:base_url + controller + "/getProductList",
			type:'post',
			data:{party_id:party_id},
			dataType:'json',
			success:function(data){
                $("#item_id").html("");
				$("#item_id").html(data.options);
			}
		});
    });
	
	$(document).on('change','#item_id',function(){
		var so_trans_id  = $(this).find(":selected").data("so_trans_id");
        $('#so_trans_id').val(so_trans_id);
		var item_id  =$("#item_id").val();
		
		$.ajax({
			url:base_url + controller + "/getItemRevList",
			type:'post',
			data:{item_id:item_id,so_trans_id:so_trans_id}, 
			dataType:'json',
			success:function(data){
                $("#first_process").html("");
				$("#first_process").html(data.html);
				$("#first_process").select2();

				$("#rev_no").html("");
				$("#rev_no").html(data.revHtml);
				$("#rev_no").select2();
			}
		});
    });
	
	$(document).on("change", ".rev_no", function() {
		var rev_no = $(this).val();
		var prc_id = $(this).find("option:selected").attr("data-prc_id");
		
		$.ajax({
			url: base_url + 'sopDesk/updateRevNo',
			data:{prc_id,prc_id,rev_no:rev_no},
			type: "POST",
			dataType:"json",
		}).done(function(data){			
			if(data.status==1){
				Swal.fire({ icon: 'success', title: data.message});			
			}else{
				if(typeof data.message === "object"){
					$(".error").html("");
					$.each( data.message, function( key, value ) {$("."+key).html(value);});
				}else{
					Swal.fire({ icon: 'error', title: data.message });
				}			
			}
		});
	});
});

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
	var jsStoreFn = postData.js_store_fn || 'storeSop';
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
		/*beforeSend: function() {
			if(rescls != ""){
				$("."+rescls).html('<h4 class="text-center">Loading...</h4>');
			}
		},*/
	}).done(function(res){
		$("#next_page").val(res.next_page);
		if(!scrollType){$("."+rescls).html(res.prcList);}else{$("."+rescls).append(res.prcList);}
		loading = true;
	});
}

function storeSop(postData){
	setPlaceHolder();
	postData.txt_editor = postData.txt_editor || "";	
	if(postData.txt_editor !== "")
	{
    	var myContent = tinymce.get(postData.txt_editor).getContent();
    	$("#" + postData.txt_editor).val(myContent);
	}

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
		$(".stageFilter.active").trigger("click");
		if(resFunctionName != ""){
			window[resFunctionName](data,formId);
		}else{
			if(data.status==1){
				$('#'+formId)[0].reset(); closeModal(formId); 
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

function confirmSOPStore(data){
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
				if(resFunctionName != ""){
					window[resFunctionName](response,formId);
				}else{
					if(response.status==1){
						initTable();

						if(formId != ""){$('#'+formId)[0].reset(); closeModal(formId);}
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

function loadProcessDetail(data){
	var prc_id = data.prc_id;
	var move_type = data.move_type;
	$.ajax({
		url: base_url + controller + '/getPRCDetail',
		type:'post',
		data:{id:prc_id,move_type:move_type},
		dataType:'json',
		success:function(data){
			$(".prcDetail").html(data.prcDetail);
			$(".prcMaterial").html(data.prcMaterial);
			$(".processDetail").html(data.processDetail);
			initSelect2();
		}
	});
}

function getPRCLogHtml(data){
	var postData = data.postData || {};
	var fnget = data.fnget || "";
	var controllerName = data.controller || controller;

	var table_id = data.table_id || "";
	var thead_id = data.thead_id || "";
	var tbody_id = data.tbody_id || "";
	var tfoot_id = data.tfoot_id || "";	

	if(thead_id != ""){
		$("#"+table_id+" #"+thead_id).html(data.thead);
	}
	
	$.ajax({
		url: base_url + controllerName + '/' + fnget,
		data:postData,
		type: "POST",
		dataType:"json",
		beforeSend: function() {
			if(table_id != ""){
				var columnCount = $('#'+table_id+' thead tr').first().children().length;
				$("#"+table_id+" #"+tbody_id).html('<tr><td colspan="'+columnCount+'" class="text-center">Loading...</td></tr>');
			}
		},
	}).done(function(res){
		$("#"+table_id+" #"+tbody_id).html('');
			$("#"+table_id+" #"+tbody_id).html(res.tbodyData);
			$("#pending_log_qty").html("Pending Qty : "+res.pendingQty);
			$("#pending_qty").val(res.pendingQty);
			$("#rev_no").html(res.revOptions);
			if(postData.process_by == 3){
				initTable();
			}else{
				// loadProcessDetail(postData);
				loadDesk();
			}
			
			initSelect2();
			if(tfoot_id != ""){
				$("#"+table_id+" #"+tfoot_id).html('');
				$("#"+table_id+" #"+tfoot_id).html(res.tfootData);
			}
	});
}

function getPRCMovementHtml(data){
	var postData = data.postData || {};
	var fnget = data.fnget || "";
	var controllerName = data.controller || controller;

	var table_id = data.table_id || "";
	var thead_id = data.thead_id || "";
	var tbody_id = data.tbody_id || "";
	var tfoot_id = data.tfoot_id || "";	

	if(thead_id != ""){
		$("#"+table_id+" #"+thead_id).html(data.thead);
	}
	
	$.ajax({
		url: base_url + controllerName + '/' + fnget,
		data:postData,
		type: "POST",
		dataType:"json",
		beforeSend: function() {
			if(table_id != ""){
				var columnCount = $('#'+table_id+' thead tr').first().children().length;
				$("#"+table_id+" #"+tbody_id).html('<tr><td colspan="'+columnCount+'" class="text-center">Loading...</td></tr>');
			}
		},
	}).done(function(res){
		$("#"+table_id+" #"+tbody_id).html('');
			$("#"+table_id+" #"+tbody_id).html(res.tbodyData);
			$("#pending_movement_qty").html("Pending Qty : "+res.pendingQty);
			$("#pending_qty").val(res.pendingQty);
			loadDesk();
			
			initSelect2();
			if(tfoot_id != ""){
				$("#"+table_id+" #"+tfoot_id).html('');
				$("#"+table_id+" #"+tfoot_id).html(res.tfootData);
			}
	});
}

function openAlertBox(data){
	var call_function = data.call_function;
	if(call_function == "" || call_function == null){call_function="edit";}
	var fnsave = data.fnsave;
	if(fnsave == "" || fnsave == null){fnsave="save";}
	var formId = data.form_id;
	var controllerName = data.controller;
	if(controllerName == "" || controllerName == null){controllerName=controller;}	
	var resFunctionName =data.res_function || "";
	$.ajax({ 
		type: "POST",   
		url: base_url + controllerName + '/' + call_function,   
		data: data.postData,
	}).done(function(response){
		Swal.fire({
			title: data.title,
			html:response,
			showCancelButton: true,
			confirmButtonText: "Submit",
			showLoaderOnConfirm: true,
			preConfirm: async () => {
			  try {
				$(".swal2-html-container form").attr("id",formId);
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
					if(data.status==1){
						Swal.fire( 'Success', data.message, 'success' );
						if(resFunctionName != ""){
							window[resFunctionName](data,formId);
						}
					}else{
						return Swal.showValidationMessage( data.message);
					}	
				});
			  } catch (error) {
				Swal.showValidationMessage(`
				  Request failed: ${error}
				`);
			  }
			},
			allowOutsideClick: () => !Swal.isLoading()
		  }).then((result) => {
			if (result.isConfirmed) {
			  Swal.fire({
				title: `${result.value.login}'s avatar`,
				imageUrl: result.value.avatar_url
			  });
			}
		});
	});
	
}

function getPRCAcceptHtml(data){
	var postData = data.postData || {};
	var fnget = data.fnget || "";
	var controllerName = data.controller || controller;

	var table_id = data.table_id || "";
	var thead_id = data.thead_id || "";
	var tbody_id = data.tbody_id || "";
	var tfoot_id = data.tfoot_id || "";	

	if(thead_id != ""){
		$("#"+table_id+" #"+thead_id).html(data.thead);
	}
	
	$.ajax({
		url: base_url + controllerName + '/' + fnget,
		data:postData,
		type: "POST",
		dataType:"json",
		beforeSend: function() {
			if(table_id != ""){
				var columnCount = $('#'+table_id+' thead tr').first().children().length;
				$("#"+table_id+" #"+tbody_id).html('<tr><td colspan="'+columnCount+'" class="text-center">Loading...</td></tr>');
			}
		},
	}).done(function(res){
		$("#"+table_id+" #"+tbody_id).html('');
			$("#"+table_id+" #"+tbody_id).html(res.tbodyData);
			if(res.pending_ch_qty){
			    $("#pending_ch_qty").html("Pending Qty : "+res.pending_ch_qty)
			}
			loadDesk();
			initSelect2();
			if(tfoot_id != ""){
				$("#"+table_id+" #"+tfoot_id).html('');
				$("#"+table_id+" #"+tfoot_id).html(res.tfootData);
			}
	});
}


function trashSop(data){
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

function getPrcResponse(data,formId=""){ 
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

function getReturnHtml(data){
	var postData = data.postData || {};
	var fnget = data.fnget || "";
	var controllerName = data.controller || controller;

	var table_id = data.table_id || "";
	var thead_id = data.thead_id || "";
	var tbody_id = data.tbody_id || "";
	var tfoot_id = data.tfoot_id || "";	

	if(thead_id != ""){
		$("#"+table_id+" #"+thead_id).html(data.thead);
	}
	
	$.ajax({
		url: base_url + controllerName + '/' + fnget,
		data:postData,
		type: "POST",
		dataType:"json",
		beforeSend: function() {
			if(table_id != ""){
				var columnCount = $('#'+table_id+' thead tr').first().children().length;
				$("#"+table_id+" #"+tbody_id).html('<tr><td colspan="'+columnCount+'" class="text-center">Loading...</td></tr>');
			}
		},
	}).done(function(res){
		$("#"+table_id+" #"+tbody_id).html('');
		$("#"+table_id+" #"+tbody_id).html(res.tbodyData);
		$("#pending_stock_qty").html("Available Qty : "+res.pending_qty);

		loadProcessDetail(postData)
		initSelect2();
		if(tfoot_id != ""){
			$("#"+table_id+" #"+tfoot_id).html('');
			$("#"+table_id+" #"+tfoot_id).html(res.tfootData);
		}
	});
}