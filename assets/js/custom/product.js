$(document).ready(function(){        
    $(document).on('change','.itemMasterForm #hsn_code',function(){
        $(".itemMasterForm #gst_per").val(($(this).find(':selected').data('gst_per') || 0));
        initSelect2();
    });

    $(document).on('change','.itemMasterForm .calMRP',function(){
        var gst_per = $(".itemMasterForm #gst_per").val() || 0;
        var price = $(".itemMasterForm #price").val() || 0;
        var rate = $(".itemMasterForm #rate").val() || 0;
        if(gst_per > 0){
            if($(this).attr('id') == "price" && price > 0){
                var tax_amt = parseFloat( (parseFloat(price) * parseFloat(gst_per) ) / 100 ).toFixed(2);
                var new_mrp = parseFloat( parseFloat(price) + parseFloat(tax_amt) ).toFixed(2);
                $(".itemMasterForm #rate").val(new_mrp);
                return true;
            }

            if(($(this).attr('id') == "rate" || $(this).attr('id') == "gst_per") && rate > 0){
                var gstReverse = parseFloat(( ( parseFloat(gst_per) + 100 ) / 100 )).toFixed(2);
                var new_price = parseFloat( parseFloat(rate) / parseFloat(gstReverse) ).toFixed(2);
    		    $(".itemMasterForm #price").val(new_price);
                return true;
            }
        }else{
            if($(this).attr('id') == "price" && price > 0){
                $(".itemMasterForm #rate").val(price);
                return true;
            }

            if(rate > 0){
                $(".itemMasterForm #price").val(rate);
                return true;
            }
        }
    });

    $(document).on('change','.productionSetting', function(e){
        e.stopImmediatePropagation();e.preventDefault();
        var production_type = $('#production_type').val();
        var cutting_flow = $('#cutting_flow').val();
        var item_id = $("#item_id").val();
        $.ajax({
            url:base_url + 'items/setProductionType',
            method:"POST",
            data:{production_type:production_type,cutting_flow:cutting_flow,item_id:item_id},
            dataType:"json",
            success:function(data){
                
            }
        });
    });

    $("#itemProcess tbody").sortable({
        items: 'tr',
        cursor: 'pointer',
        axis: 'y',
        dropOnEmpty: false,
        helper: fixWidthHelper,
        start: function (e, ui) {
            ui.item.addClass("selected");
        },
        stop: function (e, ui) {
            ui.item.removeClass("selected");
            $(this).find("tr").each(function (index) {
                $(this).find("td").eq(0).html(index+1);
            });
        },
        update: function () 
        {
            var ids='';
            $(this).find("tr").each(function (index) {ids += $(this).attr("id")+",";});
            var lastChar = ids.slice(-1);
            if (lastChar == ',') {ids = ids.slice(0, -1);}
            
            $.ajax({
                url: base_url + 'items/updateProductProcessSequance',
                type:'post',
                data:{id:ids},
                dataType:'json',
                global:false,
                success:function(data){}
            });
        }
    });    

    $(document).on('keyup','.validateOutQty',function(){
        var output_qty = $(this).val() || "";
        if(parseFloat(output_qty) <= 0){
            $(this).val("1");
        }else{
            return true;
        }
    });

    $(document).on('click', '.importProductExcel', function(e) {
        e.stopImmediatePropagation();e.preventDefault();
        $(this).attr("disabled", "disabled");
        var fd = new FormData();
        fd.append("insp_excel", $("#insp_excel")[0].files[0]);
        fd.append("item_id", $("#item_id").val());
        $.ajax({
            url: base_url + controller + '/importProductExcel',
            data: fd,
            type: "POST",
            processData: false,
            contentType: false,
            dataType: "json",
        }).done(function(data) { 
            $(".msg").html(data.message);
            $(this).removeAttr("disabled");
            $("#insp_excel").val(null);
            if (data.status == 1) {
                inspectionHtml(data);   
            }
        });
    });
	
	$(document).on('click', '.importPopExcel', function(e) {
        e.stopImmediatePropagation();e.preventDefault();
        $(this).attr("disabled", "disabled");
        var fd = new FormData();  
        fd.append("insp_excel", $("#addPopInsp #insp_excel")[0].files[0]);
        fd.append("item_id", $("#addPopInsp #item_id").val());
        fd.append("control_method", $("#addPopInsp #control_method").val());
        $.ajax({
            url: base_url + controller + '/importPopExcel',
            data: fd,
            type: "POST",
            processData: false,
            contentType: false,
            dataType: "json",
        }).done(function(data) {
            $(".msg").html(data.message);
            $(this).removeAttr("disabled");
            $("#insp_excel").val(null);
            if (data.status == 1) {
                popInspectionHtml(data);   
            }
        });
    });
	
    var bomData = {'postData':{'item_id':$("#addProductKitItems #item_id").val()},'table_id':"productKit",'tbody_id':'kitItems','tfoot_id':'','fnget':'productKitHtml'};
    getTransHtml(bomData);

    var processData = {'postData':{'item_id':$("#viewProductProcess #item_id").val()},'table_id':"itemProcess",'tbody_id':'itemProcessData','tfoot_id':'','fnget':'productProcessHtml'};
    getTransHtml(processData);

    var ctData = {'postData':{'item_id':$("#addCycleTime #item_id").val()},'table_id':"ctTable",'tbody_id':'ctBody','tfoot_id':'','fnget':'cycleTimeHtml'};
    getTransHtml(ctData);

    var dieBomData = {'postData':{'item_id':$("#addDieBom #item_id").val()},'table_id':"dieBomTbl",'tbody_id':'bomItems','tfoot_id':'','fnget':'dieBomHtml'};
    getTransHtml(dieBomData);
        
    var packStandardData = {'postData':{'item_id':$("#addPackingStandard #item_id").val()},'table_id':"packingTbl",'tbody_id':'packingBody','tfoot_id':'','fnget':'packingStandardHtml'};
    getTransHtml(packStandardData);
    
    var toolData = {'postData':{'item_id':$("#addToolBom #item_id").val()},'table_id':"toolBomTbl",'tbody_id':'toolBomBody','tfoot_id':'','fnget':'toolBomHtml'};
    getTransHtml(toolData);
	
    var parameterData = {'postData':{'item_id':$("#addParameter #item_id").val()},'table_id':"inspectionId",'tbody_id':'inspectionBody','tfoot_id':'','fnget':'inspectionHtml'}; 
    getTransHtml(parameterData);
	
	var popParaData = {'postData':{'item_id':$("#addPopInsp #item_id").val()},'table_id':"popInspectionId",'tbody_id':'popInspectionBody','tfoot_id':'','fnget':'popInspectionHtml'};
    getTransHtml(popParaData);

    var revData = {'postData':{'item_id':$("#addRevision #item_id").val()},'table_id':"revisionTbl",'tbody_id':'revisionBody','tfoot_id':'','fnget':'revisionHtml'};
    getTransHtml(revData);

	$(document).on('click','.excel',function(e){ 
        e.stopImmediatePropagation();e.preventDefault();
        var item_id = $(this).data('item_id');
        var rev_no = $(this).data('rev_no');
        var send_data = {'item_id':item_id,'rev_no':rev_no};  
        window.open(base_url + controller + '/getRevisionExcel/'+encodeURIComponent(window.btoa(JSON.stringify(send_data))),'_blank').focus();
    });

    $(document).on('change','#item_type',function(){
		var item_type = $(this).val();
		if(item_type){
			$.ajax({
				url:base_url + controller + '/getCategoryList',
				data:{item_type:item_type},
				method:"POST",
				dataType:"json",
				success:function(data){
					$("#category_id").html('');
					$("#category_id").html(data.options);
				}
			});
		}
	}); 
});

function getProductKitHtml(data,formId="addProductKitItems"){ 
    if(data.status==1){
        $('#'+formId)[0].reset();
        var postData = {'postData':{'item_id':$("#item_id").val()},'table_id':"productKit",'tbody_id':'kitItems','tfoot_id':'','fnget':'productKitHtml'};
        getTransHtml(postData);
        $("#ref_id").html(data.mbOptions);
        initSelect2();
    }else{
        if(typeof data.message === "object"){
            $(".error").html("");
            $.each( data.message, function( key, value ) {$("."+key).html(value);});
        }else{
            initTable();
            Swal.fire({ icon: 'error', title: data.message });	
        }
    }   
}

function getProductProcessHtml(data,formId="viewProductProcess"){ 
    if(data.status==1){
        $('#'+formId)[0].reset();
        var postData = {'postData':{'item_id':$("#item_id").val()},'table_id':"itemProcess",'tbody_id':'itemProcessData','tfoot_id':'','fnget':'productProcessHtml'};
        getTransHtml(postData);
    }else{
        if(typeof data.message === "object"){
            $(".error").html("");
            $.each( data.message, function( key, value ) {$("."+key).html(value);});
        }else{
            initTable();
            Swal.fire({ icon: 'error', title: data.message });	
        }
    }  
}

function getCycleTimeHtml(data,formId="addCycleTime"){ 
    if(data.status==1){
        $('#'+formId)[0].reset();
        var postData = {'postData':{'item_id':$("#item_id").val()},'table_id':"ctTable",'tbody_id':'ctBody','tfoot_id':'','fnget':'cycleTimeHtml'};
        getTransHtml(postData);
    }else{
        if(typeof data.message === "object"){
            $(".error").html("");
            $.each( data.message, function( key, value ) {$("."+key).html(value);});
        }else{
            initTable();
            Swal.fire({ icon: 'error', title: data.message });	
        }
    }   
}

function getDieBomHtml(data,formId="addDieBom"){ 
    if(data.status==1){
        $('#'+formId)[0].reset();
        var postData = {'postData':{'item_id':$("#item_id").val()},'table_id':"dieBomTbl",'tbody_id':'bomItems','tfoot_id':'','fnget':'dieBomHtml'};
        getTransHtml(postData);
    }else{
        if(typeof data.message === "object"){
            $(".error").html("");
            $.each( data.message, function( key, value ) {$("."+key).html(value);});
        }else{
            initTable();
            Swal.fire({ icon: 'error', title: data.message });	
        }
    }   
}

function getPackingStandardHtml(data,formId="addPackingStandard"){ 
    if(data.status==1){
        $('#'+formId)[0].reset();
        var postData = {'postData':{'item_id':$("#item_id").val()},'table_id':"packingTbl",'tbody_id':'packingBody','tfoot_id':'','fnget':'packingStandardHtml'};
        getTransHtml(postData);
    }else{
        if(typeof data.message === "object"){
            $(".error").html("");
            $.each( data.message, function( key, value ) {$("."+key).html(value);});
        }else{
            initTable();
            Swal.fire({ icon: 'error', title: data.message });	
        }
    }   
}

function toolBomHtml(data,formId="addToolBom"){ 
    if(data.status==1){
        $("#processIds").val("");
        $("#tool_id").val("");
        $("#id").val("");
        $('#'+formId)[0].reset();
        var postData = {'postData':{'item_id':$("#addToolBom #item_id").val()},'table_id':"toolBomTbl",'tbody_id':'toolBomBody','tfoot_id':'','fnget':'toolBomHtml'};
        getTransHtml(postData);
    }else{
        if(typeof data.message === "object"){
            $(".error").html("");
            $.each( data.message, function( key, value ) {$("."+key).html(value);});
        }else{
            initTable();
            Swal.fire({ icon: 'error', title: data.message });	
        }
    }   
}

function editToolBom(data) { 
    $.each(data, function (key, value) {
        $("#addToolBom #" + key).val(value);
	});
    $('#processIds').select2();
    $("#tool_id").select2();
}

function inspectionHtml(data,formId="addParameter"){
    if(data.status==1){
        $("#parameter").val("");
        $("#specification").val("");
        $("#instrument").val("");
        $("#id").val("");
        var process_id = data.process_id || "";
        $('#'+formId)[0].reset();
      
        var postData = {'postData':{'item_id':$("#addParameter #item_id").val()},'table_id':"inspectionId",'tbody_id':'inspectionBody','tfoot_id':'','fnget':'inspectionHtml'};
        getTransHtml(postData);
       
        setTimeout(function(){ 
            $(".process_tab").removeClass("active");
            $("#prid_"+process_id).addClass("active"); 
            processDetail($("#item_id").val(),process_id);		
		}, 10);
        
    }else{
        if(typeof data.message === "object"){
            $(".error").html("");
            $.each( data.message, function( key, value ) {$("."+key).html(value);});
        }else{
            initTable();
            Swal.fire({ icon: 'error', title: data.message });	
        }
    }   
}

function popInspectionHtml(data,formId="addPopInsp"){ 
    if(data.status==1){
        $('#'+formId)[0].reset();
        var postData = {'postData':{'item_id':$("#addPopInsp #item_id").val()},'table_id':"popInspectionId",'tbody_id':'popInspectionBody','tfoot_id':'','fnget':'popInspectionHtml'};
        getTransHtml(postData);
    }else{
        if(typeof data.message === "object"){
            $(".error").html("");
            $.each( data.message, function( key, value ) {$("."+key).html(value);});
        }else{
            initTable();
            Swal.fire({ icon: 'error', title: data.message });	
        }
    }   
}

function editInspParam(data) { 
	$.each(data, function (key, value) { 
    $("#addParameter #" + key).val(value); });
    $.each(data.control_method.split(","), function(i,e){ $("#control_method option[value='" + e + "']").prop("selected", true); });
    $('#char_class').select2();
    $("#rev_no").select2();
    $("#process_id").select2();
    $("#freq_unit").select2();
    $("#control_method").select2();
    $("#machine_tool").select2();
}

function processDetail(item_id,process_id){ 
	if(item_id,process_id){
		setTimeout(function(){ 
			$.ajax({
				url : base_url + controller + '/getParameterData',
				type : 'post',
				data : {item_id:item_id,process_id:process_id},
				dataType : 'json'
			}).done(function(response){
				$("#theadData").html(response.theadData);
				$("#tbodyData").html(response.tbodyData);
			});
		}, 100);			
	}	
    initSelect2();
}

function fixWidthHelper(e, ui) {
    ui.children().each(function() {
        $(this).width($(this).width());
    });
    return ui;
}

function customPrcStore(postData){
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
			$("#process_id").html(data.prcOption);
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
			}else{
				if(typeof data.message === "object"){
					$(".error").html("");
					$.each( data.message, function( key, value ) {$("."+key).html(value);});
				}else{
					initTable();
					Swal.fire({ icon: 'error', title: data.message });
				}			
			}	
		}			
	});
}

function getTcParamResponse(data){
    if(data.status==1){
        Swal.fire({ 
            icon: 'success', title: data.message
        }).then(function(result) {
            if (result.isConfirmed){
                window.location.reload();
            }
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

function getRevisionHtml(data,formId="addRevision"){ 
    if(data.status==1){
        $('#'+formId)[0].reset();
        closeModal(formId);
        var postData = {'postData':{'item_id':$("#item_id").val()},'table_id':"revisionTbl",'tbody_id':'revisionBody','tfoot_id':'','fnget':'revisionHtml'};
        getTransHtml(postData);
    }else{
        if(typeof data.message === "object"){
            $(".error").html("");
            $.each( data.message, function( key, value ) {$("."+key).html(value);});
        }else{
            initTable();
            Swal.fire({ icon: 'error', title: data.message });	
        }
    }   
}

function getTcSpecificationResponse(data){
    if(data.status==1){
        Swal.fire({ 
            icon: 'success', title: data.message
        }).then(function(result) {
            if (result.isConfirmed){
                window.location.reload();
            }
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