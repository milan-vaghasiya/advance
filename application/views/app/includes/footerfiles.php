<?php //$this->load->view('hr/employee/change_password');?>
<script>
	var base_url = '<?=base_url();?>'; 
	var page_url ='<?=(isset($headData->pageUrl)) ? $headData->pageUrl : ''?>'; 
	var controller = '<?=(isset($headData->controller)) ? $headData->controller : ''?>'; 
	var popupTitle = '<?=POPUP_TITLE;?>';
	var theads = '<?=(isset($tableHeader)) ? $tableHeader[0] : ''?>';
	var textAlign = '<?=(isset($tableHeader[1])) ? $tableHeader[1] : ''?>';
	var srnoPosition = '<?=(isset($tableHeader[2])) ? $tableHeader[2] : 1?>';
	var tableHeaders = {'theads':theads,'textAlign':textAlign,'srnoPosition':srnoPosition};
	var menu_id = '<?=(isset($headData->menu_id)) ? $headData->menu_id : 0?>';
	var zindex = "9999";
</script>
<div class="chat-windows"></div>
<!-- Permission Checking -->
<?php
	$script= "";
	if($permission = $this->session->userdata('emp_app_permission')):
		if(!empty($headData->pageUrl)):
    		$empPermission = $permission[$headData->pageUrl];
			
    		$script .= '
    			<script>
    				var permissionRead = "'.$empPermission['is_read'].'";
    				var permissionWrite = "'.$empPermission['is_write'].'";
    				var permissionModify = "'.$empPermission['is_modify'].'";
    				var permissionRemove = "'.$empPermission['is_remove'].'";
    				var permissionApprove = "'.$empPermission['is_approve'].'";
    			</script>
    		';
    		echo $script;
		else:
			$script .= '
			<script>
				var permissionRead = "1";
				var permissionWrite = "1";
				var permissionModify = "1";
				var permissionRemove = "1";
				var permissionApprove = "1";
			</script>
		';
		echo $script;
		endif;
	else:
		$script .= '
			<script>
				var permissionRead = "";
				var permissionWrite = "";
				var permissionModify = "";
				var permissionRemove = "";
				var permissionApprove = "";
			</script>
		';
		echo $script;
	endif;
?>
<!-- * DialogIconedDanger -->
<!-- * toast top auto close in 2 seconds -->
<!-- ============================================================== -->
<!--**********************************
    Scripts
***********************************-->
<script src="<?=base_url('assets/app/js/jquery.js')?>"></script>
<script src="<?=base_url('assets/app/vendor/bootstrap/js/bootstrap.bundle.min.js')?>"></script>
<script src="<?=base_url('assets/app/vendor/swiper/swiper-bundle.min.js')?>"></script><!-- Swiper -->
<script src="<?=base_url('assets/app/vendor/bootstrap-touchspin/dist/jquery.bootstrap-touchspin.min.js')?>"></script><!-- Swiper -->
<script src="<?=base_url('assets/app/js/dz.carousel.js')?>"></script><!-- Swiper -->
<script src="<?=base_url()?>assets/app/js/settings.js?v=<?=time()?>"></script>
<script src="<?=base_url('assets/app/js/custom.js')?>"></script>
<!-- Select2 js -->
<script src="<?=base_url()?>assets/plugins/select2/js/select2.full.min.js"></script>
<script src="<?=base_url()?>assets/js/pages/multiselect/js/bootstrap-multiselect.js"></script>
<script src="<?=base_url()?>assets/plugins/sweet-alert2/sweetalert2.min.js"></script><!-- Select2 js -->
<script src="<?=base_url()?>assets/js/custom/typehead.js?v=<?=time()?>"></script>
<div class="ajaxModal"></div>
<div class="centerImg">
	<img src="<?=base_url()?>assets/images/logo.png" style="width:50%;height:auto;"><br>
	<img src="<?=base_url()?>assets/images/ajaxLoading.gif" style="margin-top:-25px;">
</div>
<script>
	$(document).ready(function(){
		$(document).ajaxStart(function(){
			$('.ajaxModal').show();$('.centerImg').show();$(".error").html("");
			$('.save-form').attr('disabled','disabled');
			$('.btn-save').attr('disabled','disabled');
		});
	
		$(document).ajaxComplete(function(){
			$('.ajaxModal').hide();$('.centerImg').hide();
			$('.save-form').removeAttr('disabled');
			$('.btn-save').removeAttr('disabled');
			checkPermission();
		});
	});
function setPlaceHolder(){
	var label="";
	$('input').each(function () {
		if(!$(this).hasClass('combo-input') && $(this).attr("type")!="hidden" )
		{
			label="";
			inputElement = $(this).parent();
			if($(this).parent().hasClass('input-group')){inputElement = $(this).parent().parent();}else{inputElement = $(this).parent();}
			label = inputElement.children("label").text();
			label = label.replace('*','');
			label = $.trim(label);
			if($(this).hasClass('req')){inputElement.children("label").html(label + ' <strong class="text-danger">*</strong>');}
			if(!$(this).attr("placeholder")){if(label){$(this).attr("placeholder", label);}}
			$(this).attr("autocomplete", 'off');
			var errorClass="";
			var nm = $(this).attr('name');
			if($(this).attr('id')){errorClass=$(this).attr('id');}else{errorClass=$(this).attr('name');if(errorClass){errorClass = errorClass.replace("[]", "");}}
			if(inputElement.find('.'+errorClass).length <= 0){inputElement.append('<div class="error '+ errorClass +'"></div>');}
		}
		else{$(this).attr("autocomplete", 'off');}
	});
	$('textarea').each(function () {
		label="";
		label = $(this).parent().children("label").text();
		label = label.replace('*','');
		label = $.trim(label);
		if($(this).hasClass('req')){$(this).parent().children("label").html(label + ' <strong class="text-danger">*</strong>');}
		if(label){$(this).attr("placeholder", label);}
		$(this).attr("autocomplete", 'off');
		var errorClass="";
		var nm = $(this).attr('name');
		if($(this).attr('name')){errorClass=$(this).attr('name');}else{errorClass=$(this).attr('id');}
		if($(this).parent().find('.'+errorClass).length <= 0){$(this).parent().append('<div class="error '+ errorClass +'"></div>');}
	});
	$('select').each(function () {
		let string =String($(this).attr('name'));
		if(string.indexOf('[]') === -1)
		{
			label="";
			var selectElement = $(this).parent();
			if($(this).hasClass('single-select')){selectElement = $(this).parent().parent();}
			label = selectElement.children("label").text();
			label = label.replace('*','');
			label = $.trim(label);
			if($(this).hasClass('req')){selectElement.children("label").html(label + ' <strong class="text-danger">*</strong>');}
			var errorClass="";
			var nm = $(this).attr('name');
			
			if($(this).attr('name')){errorClass=$(this).attr('name');}else{errorClass=$(this).attr('id');}
			if(selectElement.find('.'+errorClass).length <= 0){selectElement.append('<div class="error '+ errorClass +'"></div>');}
		}
	});
}
$(window).on('pageshow', function() {
	$('form').off();
	checkPermission();
	$(".menubar-nav .nav-link").removeClass("active");
	$("[data-page_url ='"+page_url+"']").addClass("active");
});

function checkPermission(){
	$('.permission-read').show();
	$('.permission-write').show();
	$('.permission-modify').show();
	$('.permission-remove').show();
	$('.permission-approve').show();

	//view permission
	if(permissionRead == "1"){ 
		$('.permission-read').prop('disabled', false);
		$('.permission-read').show(); 
	}else{ 
		$('.permission-read').prop('disabled', true);
		$('.permission-read').hide(); 
		//window.location.href = base_url + 'error_403';
	}

	//write permission
	if(permissionWrite == "1"){ 
		$('.permission-write').prop('disabled', false);
		$('.permission-write').show(); 
	}else{ 
		$('.permission-write').prop('disabled', true);
		$('.permission-write').hide(); 
	}

	//update permission
	if(permissionModify == "1"){ 
		$('.permission-modify').prop('disabled', false);
		$('.permission-modify').show(); 
	}else{ 
		$('.permission-modify').prop('disabled', true);
		$('.permission-modify').hide(); 
	}

	//delete permission
	if(permissionRemove == "1"){ 
		$('.permission-remove').prop('disabled', false);
		$('.permission-remove').show(); 
	}else{ 
		$('.permission-remove').prop('disabled', true);
		$('.permission-remove').hide(); 
	}

	//Approve permission
	if(permissionApprove == "1"){ 
		$('.permission-approve').prop('disabled', false);
		$('.permission-approve').show(); 
	}else{ 
		$('.permission-approve').prop('disabled', true);
		$('.permission-approve').hide(); 
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
	var jsStoreFn = postData.js_store_fn || 'storeData';
	var txt_editor = postData.txt_editor || '';
	var save_controller = postData.save_controller ||controllerName;

	var fnJson = "{'formId':'"+postData.form_id+"','fnsave':'"+fnsave+"','controller':'"+save_controller+"','txt_editor':'"+txt_editor+"'}";

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
		// initMultiSelect();
		setPlaceHolder();
		// setMinMaxDate();
		// initSelect2();		
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

function storeData(postData){
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
		
		if(resFunctionName != ""){
			window[resFunctionName](data,formId);
		}else{
			if(data.status==1){
				$('#'+formId)[0].reset(); 
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
function closeModal(formId){
	zindex--;
	
	var modal_id = $("."+formId+"Modal").attr('id');
	$("#"+modal_id).removeClass(formId+"Modal");
	$("#"+modal_id+' .modal-body').html("");
	$("#"+modal_id).modal('hide');	
	$(".modal").css({'overflow':'auto'});
	$("#"+modal_id).removeClass('modal-i-'+zindex);	
	$('.modal-i-'+(zindex-1)).addClass('show');

	$("#"+modal_id+" .modal-header .btn-close").attr('data-modal_id',"");
	$("#"+modal_id+" .modal-header .btn-close").attr('data-modal_class',"");
	$("#"+modal_id+" .modal-footer .btn-close-modal").attr('data-modal_id',"");
	$("#"+modal_id+" .modal-footer .btn-close-modal").attr('data-modal_class',"");
	setTimeout(function(){ 
		initSelect2();		
	}, 500);
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

function confirmStore(data){
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

						if(formId != ""){$('#'+formId)[0].reset(); closeModal(formId);}
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
	});
}

function trashData(data){
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

</script>