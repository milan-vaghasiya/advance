var zindex = "9999";
$(document).ready(function(){
	localStorage.setItem('tabReloads', 'false');

	var lastActivityTime = new Date();;

	// Update last activity time on user interaction events //mousemove
	$(document).on('click change keydown', function() {
		var idleTime = 7200; //Session Time
		var currentDateTime = new Date();

		// Calculate the time difference in milliseconds
		var idleThreshold = currentDateTime - lastActivityTime;

		// Convert the time difference to seconds
        var secondsDifference = Math.floor(idleThreshold / 1000);

		if (secondsDifference > idleTime) {
			// Idle time exceeded threshold, perform actions or redirect user
			//console.log('User is idle');
			window.location.reload();
			// Perform any necessary actions or redirect the user
		} else {
			// User is active, perform any necessary actions
			lastActivityTime = new Date();
		}		
	});

	// Check last activity time every second
	setInterval(function() {
		var idleTime = 7200; //Session Time
		var currentDateTime = new Date();

		// Calculate the time difference in milliseconds
		var idleThreshold = currentDateTime - lastActivityTime;

		// Convert the time difference to seconds
        var secondsDifference = Math.floor(idleThreshold / 1000);

		if (secondsDifference > idleTime) {
			// Idle time exceeded threshold, perform actions or redirect user
			//console.log('User is idle');
			window.location.reload();
			// Perform any necessary actions or redirect the user
		} else {
			// User is active, perform any necessary actions
			//console.log('User is active, Seconds : '+ secondsDifference);
		}
	}, 1000); // Check every second (adjust interval as needed)
	    
	// initSpeechRecognitationMenu();
	$('[data-tooltip="tooltip"]').tooltip();
	if (typeof ssDatatable !== 'undefined'){ssTableInit();}
	checkPermission();
	initMultiSelect();
	setMinMaxDate();
	initSelect2();
	setPlaceHolder();
	
	$(document).on("keypress",".numericOnly",function (e) {
		if (String.fromCharCode(e.keyCode).match(/[^0-9]/g)) return false;
	});	

	$(document).on("keypress",'.floatOnly',function(event) {
		if ((event.which != 46 || $(this).val().indexOf('.') != -1) && (event.which < 48 || event.which > 57)) {event.preventDefault();}
	});
	
	// Prevent Arrow & Keyboard Event in Number input
	$("input[type=number]").on("focus", function() { $(this).on("keydown", function(event) {if (event.keyCode === 38 || event.keyCode === 40) {event.preventDefault();}}); });

	/*** Keep Selected Tab after page loading ***/
	var selectedTab = localStorage.getItem('selected_tab');
	if (selectedTab != null) { $("#"+selectedTab).trigger('click'); }
	$(document).on('click','.nav-tab',function(){
		var id = $(this).attr('id');
    	localStorage.setItem('selected_tab', id);
    });
	
	$(document).on('click',".show_terms",function(){$("#termModel").modal('show');});
	
	$(document).on('change','.country_list',function(){
		var id = $(this).val();
		var state_id = $(this).data('state_id');
		var selected_state_id = $(this).data('selected_state_id') || "";
		var city_id = $("#"+state_id).data('city_id');
		if(id == ""){
			$("#"+state_id).html('<option value="">Select State</option>');
			$("#"+city_id).html('<option value="">Select City</option>');
			initSelect2();
		}else{
			$.ajax({
				url: base_url + controller +'/getStatesOptions',
				type:'post',
				data:{country_id:id},
				dataType:'json',
				success:function(data){
					$("#"+state_id).html(data.result);
					if(selected_state_id != ""){
						$("#"+state_id).val(selected_state_id);
						$(".state_list").trigger('change');
						initSelect2();
					}
					$(this).focus();
				}
			});
		}
	});

	$(document).on('change',".state_list",function(){
		var id = $(this).val();
		var city_id = $(this).data('city_id');
		var selected_city_id = $(this).data('selected_city_id') || "";
		if(id == ""){
			$("#"+city_id).html('<option value="">Select City</option>');
			initSelect2();
		}else{
			$.ajax({
				url: base_url + controller + '/getCitiesOptions',
				type:'post',
				data:{state_id:id},
				dataType:'json',
				success:function(data){
					$("#"+city_id).html(data.result);
					if(selected_city_id != ""){
						$("#"+city_id).val(selected_city_id);
						initSelect2();
					}
					$(this).focus();
				}
			});
		}
		initSelect2();
	});	

	$(document).on('click','.pswHideShow',function(){
		var type = $('.pswType').attr('type');
		if(type == "password"){
			$(".pswType").attr('type','text');
			$(this).html('<i class="fa fa-eye-slash"></i>');
		}else{
			$(".pswType").attr('type','password');
			$(this).html('<i class="fa fa-eye"></i>');
		}
	});

	$(document).on('mouseenter', '.mainButton', function(e){
		e.preventDefault();
		$(this).addClass('open');
		$(this).addClass('showAction');
		$(this).children('.fa').removeClass('fa-cog');
		$(this).children('.fa').addClass('fa-times');
		$(this).parent().children('.btnDiv').css('z-index','9');
	});

	$(document).on('mouseleave', '.actionButtons', function(e){
		e.preventDefault();
		$('.mainButton').removeClass('open');
		$('.mainButton').removeClass('showAction');
		$('.mainButton').children('.fa').removeClass('fa-times');
		$('.mainButton').children('.fa').addClass('fa-cog');
		$('.mainButton').parent().children('.btnDiv').css('z-index','-1');
	});
	
	$(document).ajaxStart(function(){
		$('.ajaxModal').show();$('.centerImg').show();$(".error").html("");
		$('.btn-save').attr('disabled','disabled');
	});
	
	$(document).ajaxComplete(function(){
		$('.ajaxModal').hide();$('.centerImg').hide();
		$('.btn-save').removeAttr('disabled');
		checkPermission();
	});
	
	$(document).on('change','#financialYearSelection',function(e){
		var fy = $(this).val();
		var send_data = { year:fy };

		Swal.fire({
			title: 'Confirm!',
			text: "Are you sure want to change this Financial Year ?",
			icon: 'warning',
			showCancelButton: true,
			confirmButtonColor: '#3085d6',
			cancelButtonColor: '#d33',
			confirmButtonText: 'Yes, delete it!',
		}).then(function(result) {
			if (result.isConfirmed){
				$.ajax({
					url: base_url + 'login/setFinancialYear',
					data: send_data,
					type: "POST",
					dataType:"json",
					success:function(data)
					{
						if(data.status==0){
							toastr.error(data.message, 'Sorry...!', { "showMethod": "slideDown", "hideMethod": "slideUp", "closeButton": true, positionClass: 'toastr toast-bottom-center', containerId: 'toast-bottom-center', "progressBar": true });
						}
						else{
							Swal.fire({ icon: 'success', title: data.message});
							// Trigger refresh in all open tabs    						
    						localStorage.setItem('tabReloads', 'true');
							window.location.reload();
						}
					}
				});
			}
		});
	});	

	$(document).on('blur','.fyDates',function(){
		setMinMaxDate();
		var inputName = $(this).attr('name');
		var date = $(this).val();
		var minAttr = $(this).attr('min');
		var maxAttr = $(this).attr('max');			

		fDate = Date.parse(minAttr);
		lDate = Date.parse(maxAttr);
		cDate = Date.parse(date);

		$("."+inputName).html("");
		if((cDate < fDate || cDate > lDate)) {
			$("."+inputName).html("Please select valid Date.");
			$(this).val("");
		}
	});	

	$(document).on('change',".partyDetails",function(){
		var party_id = $(this).val();
		var resFunctionName = $(this).data('res_function') || "";

		if(party_id){
			$.ajax({
				url : base_url + '/parties/getPartyDetails',
				type:'post',
				data: {id:party_id},
				dataType : 'json',
			}).done(function(response){
				window[resFunctionName](response);
			});
		}else{
			window[resFunctionName]();
		}
	});

	$(document).on('change click',".itemDetails",function(){
		var item_id = $(this).val();
		var resFunctionName = $(this).data('res_function') || "";
		var price_structure_id = $(this).attr('data-price_structure_id') || "";
		var party_id = $("#party_id").val() || "";
		var party_name = $("#party_name").val() || "";

		if($(this).hasClass("partyReq")){			
			if(party_id == "" && party_name == ""){  
				$(".party_id").html("Party name is required."); $(this).val(""); initSelect2();  
				return false; 
			} 
		}

		if(item_id){
			$.ajax({
				url : base_url + '/items/getItemDetails',
				type:'post',
				data: {id : item_id, price_structure_id : price_structure_id, party_id : party_id},
				dataType : 'json',
			}).done(function(response){
				window[resFunctionName](response);
			});
		}else{
			window[resFunctionName]();
		}
	});

	$(document).on('click','.btn-close,.btn-close-modal',function(){
		zindex--;
		var modal_id = $(this).data('modal_id');
		var modal_class = $(this).data('modal_class');
		$("#"+modal_id).removeClass(modal_class);
		$("#"+modal_id+' .modal-body').html("");
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
	});

	$(document).on('change','.custom-file-input',function(){
		var inputId = $(this).attr('id');
		if($('#'+inputId).hasClass("multifiles")){
			var files = $('#'+inputId).prop("files")
			var fileNames = $.map(files, function(val) { return val.name; });
			var fileName = fileNames.join(", ") || "Choose file";
		}else{
			var fileName = $('#'+inputId).val().split('\\').pop() || "Choose file";
		}
        $('label[for="' + inputId + '"]').html(fileName);
	});

	//$("#print_dialog").modal();
	$(document).on("click",".printDialog",function(){
		$("#printModel").attr('action',base_url + controller + "/" + ($(this).data('fn_name') || ""));
		$("#printModel #print_format").val($(this).data('print_format'));
		$("#printModel #id").val($(this).data('id'));
		$("#print_dialog").modal("show");
	});

	// on first focus (bubbles up to document), open the menu
	$(document).on('focus', '.select2-selection.select2-selection--single', function (e) {
		$(this).closest(".select2-container").siblings('select:enabled').select2('open');
	});
	
	// steal focus during close - only capture once and stop propogation
	$('select.select2').on('select2:closing', function (e) {
		$(e.target).data("select2").$selection.one('focus focusin', function (e) {
			e.stopPropagation();
		});
	});

	// Prevent Bootstrap dialog from blocking focusin
    document.addEventListener('focusin', (e) => {
		if (e.target.closest(".tox-tinymce, .tox-tinymce-aux, .moxman-window, .tam-assetmanager-root") !== null) {
		  e.stopImmediatePropagation();
		}
	});

	// fix select2 bootstrap modal scroll bug
	$(document).on('select2:close', '.select2', function (e) {
		var evt = "scroll.select2";
		$(e.target).parents().off(evt);
		$(window).off(evt);
	});
});

$(window).on('pageshow', function() {
	$('form').off();
	checkPermission();setMinMaxDate();
});

function initSimplebar(){new SimpleBar($('.simpleBar'), { autoHide: true });}

function initSelect2(){
	$(".select2").each(function () {
		$(this).select2();
	});	

	$(".modal .select2").each(function () {
		$(this).select2({
			dropdownParent: $('#'+$(this).closest('.modal form').attr('id')),
		});
	});	
}

function getPartyList(postData){
	$.ajax({
		url : base_url + '/parties/getPartyList',
		type : 'post',
		data : postData,
		dataType : 'json',
	}).done(function(response){
		var partyList = response.data.partyList;
		var html = '<option value="">Select Party Name</option>';
		if(!$.isEmptyObject(partyList)){
			$.each(partyList,function(index,row){  
				html += '<option value="'+row.id+'">'+row.party_name+'</option>';
			});
			$(".partyOptions").html(html);
		}
	});
}

function getItemList(postData){
	$.ajax({
		url : base_url + '/items/getItemList',
		type : 'post',
		data : postData,
		dataType : 'json',
	}).done(function(response){
		var itemList = response.data.itemList;
		var html = '<option value="">Select Item Name</option>';
		if(!$.isEmptyObject(itemList)){
			$.each(itemList,function(index,row){
				var itemFullName = (row.item_code != "")?"[ "+row.item_code+" ] "+row.item_name:row.item_name  
				html += '<option value="'+row.id+'">'+itemFullName+'</option>';
			});
			$(".itemOptions").html(html);
		}
	});
}

function setMinMaxDate(){
	$.each($('.fyDates'),function(){
		var minAttr = $(this).attr('min');
		var maxAttr = $(this).attr('max');	
		if(typeof minAttr === 'undefined' || minAttr === false){ $(this).attr('min',startYearDate); }
		if(typeof maxAttr === 'undefined' || maxAttr === false){ $(this).attr('max',endYearDate); }	
	});	
}

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

function initMultiSelect(){
    //$(".jp_multiselect option:selected").prependTo(".jp_multiselect");
	$('.jp_multiselect').multiselect({
		includeSelectAllOption:false,
		enableFiltering:true,
        enableCaseInsensitiveFiltering: true,
		buttonWidth: '100%',
		onChange: function() {
			var inputId = this.$select.data('input_id');
			var selected = this.$select.val();$('#' + inputId).val(selected);
			//$(".jp_multiselect option:selected").prependTo(".jp_multiselect");
		    //reInitMultiSelect();
		}
	});
	$('.form-check-input').addClass('filled-in');
	$('.multiselect-filter i').removeClass('fas');
	$('.multiselect-filter i').removeClass('fa-sm');
	$('.multiselect-filter i').addClass('fa');
	$('.multiselect-container.dropdown-menu').addClass('scrollable');
	$('.multiselect-container.dropdown-menu').css('max-height','200px');
}

function reInitMultiSelect(){
	$('.jp_multiselect').multiselect('rebuild');
	$('.form-check-input').addClass('filled-in');
	$('.multiselect-filter i').removeClass('fas');
	$('.multiselect-filter i').removeClass('fa-sm');
	$('.multiselect-filter i').addClass('fa');
	$('.multiselect-container.dropdown-menu').addClass('scrollable');
	$('.multiselect-container.dropdown-menu').css('height','200px');
}

function statusTab(tableId,status,hp_fn_name="",page=""){

    $("#"+tableId).attr("data-url",$("#"+tableId).data('url')+'/'+status);

	$("#"+tableId).data("hp_fn_name","");
    $("#"+tableId).data("page","");
    $("#"+tableId).data("hp_fn_name",hp_fn_name);
    $("#"+tableId).data("page",page);

    ssTable.state.clear();
	initTable();
}

function ssTableInit(){
	var tableId = $('.ssTable').attr('id');
	$("#"+tableId).data("hp_fn_name","");
    $("#"+tableId).data("page","");
	var tableOptions = {pageLength: 25,'stateSave':false};
    ssDatatable($('.ssTable'),tableHeaders,tableOptions);
}

function initTable(postData = {}){
	$('.ssTable').DataTable().clear().destroy();
	var tableId = $('.ssTable').attr('id');
	
	var hp_fn_name = $("#"+tableId).data("hp_fn_name") || "";
	var page = $("#"+tableId).data("page") || "";

	if(hp_fn_name != "" && page != ""){
		$.ajax({
			url : base_url + controller + '/getTableHeader',
			type : 'POST',
			data : {'hp_fn_name':hp_fn_name,'page':page},
			dataType: 'json',
			success: function(response) {
				var tableOptions = {pageLength: 25,'stateSave':false};
				var dataSet = postData;
				var tableHeaders = response.data;
				tableHeaders.reInit = 1;
				
				$('.ssTable').html("");
				ssDatatable($('.ssTable'),tableHeaders,tableOptions,dataSet);
			},
			error: function() {
				console.log('Error occurred while fetching table headers.');
			}
		});
	}else{
		var tableOptions = {pageLength: 25,'stateSave':false};
		var tableHeaders = {'theads':'','textAlign':textAlign,'sortable':sortable,'reInit':'1'};
		//var tableHeaders = {'theads':'','textAlign':textAlign,'sortable':sortable};
		var dataSet = postData;
		ssDatatable($('.ssTable'),tableHeaders,tableOptions,dataSet);
	}	
}

function initDataTable(){
	var table = $('#commanTable').DataTable( {
		lengthChange: false,
		responsive: true,
		'stateSave':true,
		retrieve: true,
		buttons: [ 'pageLength','copy', 'excel' ]
	});
	table.buttons().container().appendTo( '#commanTable_wrapper .col-md-6:eq(0)' );
	return table;
};

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

function store(postData){
	setPlaceHolder();
		
	if(postData.txt_editor !== "")
	{
    	var myContent = tinymce.get(postData.txt_editor).getContent();
    	$("#" + postData.txt_editor).val(myContent);
	}

	var formId = postData.formId;
	var fnsave = postData.fnsave || "save";
	var controllerName = postData.controller || controller;

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
			initTable(); $('#'+formId)[0].reset(); closeModal(formId);
			Swal.fire({ icon: 'success', title: data.message});
			/*toastr.success(data.message, 'Success', { "showMethod": "slideDown", "hideMethod": "slideUp", "closeButton": true, positionClass: 'toastr toast-bottom-center', containerId: 'toast-bottom-center', "progressBar": true });*/
		}else{
			if(typeof data.message === "object"){
				$(".error").html("");
				$.each( data.message, function( key, value ) {$("."+key).html(value);});
			}else{
				Swal.fire({ icon: 'error', title: data.message });
			}			
		}				
	});
}

function customStore(postData){
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
	var formClose = postData.form_close || "";

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
	}).done(function(data){
		
		if(resFunctionName != ""){
			if(formClose){ 
				$('#'+formId)[0].reset(); closeModal(formId);
				Swal.fire({ icon: 'success', title: data.message}); 
			}
			window[resFunctionName](data,formId);
			
		}else{
			if(data.status==1){
				initTable(); $('#'+formId)[0].reset(); closeModal(formId);
				Swal.fire({ icon: 'success', title: data.message});
				$(".modal-select2").select2();
				// toastr.success(data.message, 'Success', { "showMethod": "slideDown", "hideMethod": "slideUp", "closeButton": true, positionClass: 'toastr toast-bottom-center', containerId: 'toast-bottom-center', "progressBar": true });
			}else{
				if(typeof data.message === "object"){
					$(".error").html("");
					$.each( data.message, function( key, value ) {$("."+key).html(value);});
				}else{
					initTable();
					Swal.fire({ icon: 'error', title: data.message });
					// toastr.error(data.message, 'Error', { "showMethod": "slideDown", "hideMethod": "slideUp", "closeButton": true, positionClass: 'toastr toast-bottom-center', containerId: 'toast-bottom-center', "progressBar": true });
				}			
			}	
		}			
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

function initModal(postData,response){
	var button = postData.button;if(button == "" || button == null){button="both";};
	var fnedit = postData.fnedit;if(fnedit == "" || fnedit == null){fnedit="edit";}
	var fnsave = postData.fnsave;if(fnsave == "" || fnsave == null){fnsave="save";}
	var controllerName = postData.controller;if(controllerName == "" || controllerName == null){controllerName=controller;}
	var savebtn_text = postData.savebtn_text;
	var savebtn_icon = postData.savebtn_icon || "";
	if(savebtn_text == "" || savebtn_text == null){savebtn_text='<i class="fa fa-check"></i> Save';}
	else{ savebtn_text = ((savebtn_icon != "")?'<i class="'+savebtn_icon+'"></i> ':'')+savebtn_text; }

	var resFunction = postData.res_function || "";
	var jsStoreFn = postData.js_store_fn || 'store';
	var txt_editor = postData.txt_editor || '';
	var form_close = postData.form_close || '';

	var fnJson = "{'formId':'"+postData.form_id+"','fnsave':'"+fnsave+"','controller':'"+controllerName+"','txt_editor':'"+txt_editor+"','form_close':'"+form_close+"'}";

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

function modalAction(data){
	var call_function = data.call_function;
	if(call_function == "" || call_function == null){call_function="edit";}

	var fnsave = data.fnsave;
	if(fnsave == "" || fnsave == null){fnsave="save";}

	var controllerName = data.controller;
	if(controllerName == "" || controllerName == null){controllerName=controller;}	

	var modal_id = data.modal_id || "";
	var init_action = data.init_action || "";
	
	var ajaxParam = {
		url: base_url + controllerName + '/' + call_function,   
		type: "POST",   
		data: data.postData
	}; 

	if(modal_id == ""){ 
		ajaxParam = {
			url: base_url + controllerName + '/' + call_function,   
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
	});
}

function trash(data){
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

function getTransHtml(data){
	var postData = data.postData || {};
	var fnget = data.fnget || "";
	var controllerName = data.controller || controller;
	var resFunctionName = data.res_function || "";

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
		if(resFunctionName != ""){
			window[resFunctionName](response);
		}else{
			$("#"+table_id+" #"+tbody_id).html('');
			$("#"+table_id+" #"+tbody_id).html(res.tbodyData);

			if(tfoot_id != ""){
				$("#"+table_id+" #"+tfoot_id).html('');
				$("#"+table_id+" #"+tfoot_id).html(res.tfootData);
			}
		}
	});
}

function changePsw(formId){
	var fd = $('#'+formId).serialize();
	$.ajax({
		url: base_url + 'hr/employees/changePassword',
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
			initTable(); $("#change-psw").modal('hide');
			Swal.fire({ icon: 'success', title: data.message});
		}else{
			initTable(); $("#change-psw").modal('hide');
			Swal.fire({ icon: 'error', title: data.message });
		}		
	});
}

/***** Get Select2 Data *****/
function getDynamicItemList(dataSet = {},eleClass = "large-select2"){   
	var eleID = $('.' + eleClass).attr('id');
	var url = base_url + $('.' + eleClass).data('url');
	var pholder = $('.' + eleClass).data('pholder');
	
	var base_element = $('.' + eleClass);
	
	$(base_element).select2({
		placeholder: pholder,
		closeOnSelect: true,
		ajax: {
			url: url,
			type: "post",
			dataType: 'json',
			//delay: 250,
			global:false,
			data: function (params) {var dataObj = {searchTerm: params.term,item_type:$(this).attr('data-item_type'),category_id:$(this).attr('data-category_id'),family_id:$(this).attr('data-family_id'),default_val:$(this).attr('data-default_val')};return $.extend(dataObj, dataSet);},
			processResults: function (response) {return {results: response};},
			templateSelection: function (item) { return item.name; },
			cache: true
		},
		dropdownParent: $(base_element).parent()
	});
	
	if(dataSet.id)
	{
    	setTimeout(function()
    	{
    	    if(dataSet.id != "" && dataSet.row != "" && dataSet.text != "")
    	    {
    		    var $option = "<option value='"+dataSet.id+"' data-row='"+dataSet.row+"' selected>"+dataSet.text+"</option>";
                $('.' + eleClass).append($option).trigger('change');
    	    }
    	}, 200);
    }
}

function isInteger(x) { return typeof x === "number" && isFinite(x) && Math.floor(x) === x; }

function isFloat(x) { return !!(x % 1); }

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

function toFixTableHeader(){
    var scroll = $(window).scrollTop();
    $('.ssTable body').css("visibility", "hidden");

    if (scroll >= $('.table-responsive').offset().top) {$(".ssTable thead tr th").css({ top: scroll - $('.table-responsive').offset().top+10 });} else {$(".ssTable thead tr th").css({top: 0 });}
	$(".ssTable thead tr th").css('z-index','99');
    $('.ssTable body').css("visibility", "visible");
	checkPermission();
}

function formatResult(node) {
    var level = "1";
    if(node.element !== undefined){
      level = (node.element.className);
      if(level.trim() !== ''){var l = level.split("_");level = l[1];}
    }
	
	var lArr = level.split(".");
	level = lArr.length-1;
    var $result = $('<span style="padding-left:' + (20 * level) + 'px;">' + node.text + '</span>');
    return $result;
};

function formatDate(date,format='Y-m-d') {
	var convertedDate = "";
	if(date != ""){
		var d = new Date(date),
			month = '' + (d.getMonth() + 1),
			day = '' + d.getDate(),
			year = d.getFullYear();

		if (month.length < 2) 
			month = '0' + month;
		if (day.length < 2) 
			day = '0' + day;
			
		convertedDate = date;
		if(format == "Y-m-d"){return [year, month, day].join('-');}
		if(format == "y-m-d"){year = year.toString().substr(-2); convertedDate = [year, month, day].join('-');}
		if(format == "d-m-Y"){return [day, month, year].join('-');}
		if(format == "d-m-y"){year = year.toString().substr(-2); convertedDate = [day, month, year].join('-');}
	}
    
    return convertedDate;
}

function formatSymbol(selection) {
    var img_path = $(selection.element).data('img_path');
    
    if(!img_path){return selection.text;}
    else {
        var $selection = $('<img src="' + img_path + '" style="width:20px;"><span class="img-changer-text">' + $(selection.element).text() + '</span>');
        return $selection;
    }
}

function decodeQrCode(){
    var qrValue = $('#decodeQr').val();
	var sendData = {qrValue:qrValue};
	$.ajax({ 
		type: "POST",   
		url: base_url + 'dashboard/decodeQRCode',   
		data: sendData,
	}).done(function(response){
	    $('.decodeData').html(response.decodeData);
	});
}

$(document).on('keypress','#decodeQr',function(e){ 
	if(e.which == 13) {
		var qrValue = $(this).val();
		$.ajax({
			type: "POST",
			url: base_url + 'dashboard/decodeQRCode',
			data:{qrValue:qrValue},
			dataType:'json'
		}).done(function (response) {
	    $('.decodeData').html(response.decodeData);});
	}
});

function calcTimeDiffInHrs(start_time,end_time,type="H"){
    var time1 = start_time.split(':'), time2 = end_time.split(':');
    var hours1 = parseInt(time1[0], 10), 
    hours2 = parseInt(time2[0], 10),
    mins1 = parseInt(time1[1], 10),
    mins2 = parseInt(time2[1], 10);
    var hours = hours2 - hours1, mins = 0;
    
    if(hours < 0) hours = 24 + hours;
    
    if(mins2 >= mins1) {mins = mins2 - mins1;}
    else {mins = (mins2 + 60) - mins1;hours--;}
    
    var minute = (hours * 60) + mins;
    mins = mins / 60; 
    
    hours += mins;
    hours = hours.toFixed(2);
    if(type=="H"){return hours;}
    else{return minute;}
}

function inrFormat(no){
    if(no){
        no=no.toString();
        var afterPoint = '';
        if(no.indexOf('.') > 0)
           afterPoint = no.substring(no.indexOf('.'),no.length);
        no = Math.floor(no);
        no=no.toString();
        var lastThree = no.substring(no.length-3);
        var otherNumbers = no.substring(0,no.length-3);
        if(otherNumbers != ''){lastThree = ',' + lastThree;}
            
        var res = otherNumbers.replace(/\B(?=(\d{2})+(?!\d))/g, ",") + lastThree + afterPoint;
    	return res;
    }else{return no;}        
}

function resPartyMaster(response,formId){
	if(response.status==1){
        $('#'+formId)[0].reset();closeModal(formId);
        Swal.fire({ icon: 'success', title: response.message});

		//toastr.success(response.message, 'Success', { "showMethod": "slideDown", "hideMethod": "slideUp", "closeButton": true, positionClass: 'toastr toast-bottom-center', containerId: 'toast-bottom-center', "progressBar": true });

		getPartyList({"party_category":$("#party_id").data('party_category'),"party_type":($("#party_id").data('party_type') || 1)});
		setTimeout(function(){
			$("#party_id").val(response.id);
			//$("#party_id").select2();			
			initSelect2();
			$(".partyDetails").trigger('change');
		},1000);
    }else{
        if(typeof response.message === "object"){
            $(".error").html("");
            $.each( response.message, function( key, value ) {$("."+key).html(value);});
        }else{
            //toastr.error(response.message, 'Error', { "showMethod": "slideDown", "hideMethod": "slideUp", "closeButton": true, positionClass: 'toastr toast-bottom-center', containerId: 'toast-bottom-center', "progressBar": true });

			Swal.fire( 'Sorry...!', response.message, 'error' );
        }			
    }	
}

function resItemMaster(response,formId){
	if(response.status==1){
        $('#'+formId)[0].reset();closeModal(formId);
        //toastr.success(response.message, 'Success', { "showMethod": "slideDown", "hideMethod": "slideUp", "closeButton": true, positionClass: 'toastr toast-bottom-center', containerId: 'toast-bottom-center', "progressBar": true });

		Swal.fire({ icon: 'success', title: response.message});

		getItemList({"item_type" : $("#item_id").data('item_type'), "active_item" : $("#item_id").data('is_active')});
		
		setTimeout(function(){
			$("#item_id").val(response.id);
			//$("#item_id").select2();
			initSelect2();
			$(".itemDetails").trigger('change');
		},1000);
    }else{
        if(typeof response.message === "object"){
            $(".error").html("");
            $.each( response.message, function( key, value ) {$("."+key).html(value);});
        }else{
            //toastr.error(response.message, 'Error', { "showMethod": "slideDown", "hideMethod": "slideUp", "closeButton": true, positionClass: 'toastr toast-bottom-center', containerId: 'toast-bottom-center', "progressBar": true });

			Swal.fire( 'Sorry...!', response.message, 'error' );
        }			
    }	
}

function resetFormByClass(cls) {
    $('.' + cls + " input").each(function(){
		if($(this).data('resetval')){$(this).val($(this).data('resetval'));}else{$(this).val('');}
	});
    $('.' + cls).find('select').val('');
    $('.' + cls).find('textarea').val('');
}

// Check if the dropdown has changed in other tabs
window.addEventListener('storage', function(event){
    if (event.key === 'tabReloads') {
		localStorage.setItem('tabReloads', 'false');
		// Reload the page if the dropdown has changed in another tab
		location.reload();
    }
});

function initEditor(setting={}){
    
    var options = {
                    selector: '#txt_editor',
                    height: 400,
                    plugins: [ 'lists advlist paste link' ],
                    toolbar: 'undo redo | fontselect fontsizeselect | bullist numlist | link',
                    toolbar_sticky: true,
                    menu: {},
                    menubar: '',
                    content_style: 'body { font-family:Helvetica,Arial,sans-serif; font-size:14px }'
                };
                
    $.extend( options, setting );
    
    tinymce.init(options);
    
}

function initModalSelect(){$('.select2').select2({ width: null});}

function formatSymbol(selection) {
	console.log(selection.element);
    var img_path = $(selection.element).data('img_path');
    if(!img_path){return selection.text;}
    else {
        var $selection = $('<img src="' + img_path + '" style="width:20px;"><span class="img-changer-text">' + $(selection.element).text() + '</span>');
        return $selection;
    }
}

function delay(callback, ms) {
  var timer = 0;
  return function() {
    var context = this, args = arguments;
    clearTimeout(timer);
    timer = setTimeout(function () {
      callback.apply(context, args);
    }, ms || 0);
  };
}