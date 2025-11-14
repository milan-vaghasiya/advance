$(document).ready(function(){
	$(document).on('change',"#test_type",function(e){
        e.stopImmediatePropagation();e.preventDefault();
		
		var fg_item_id = $("#fg_item_id").val();
		var grade_id = $("#grade_id").val();
		var test_type = $("#test_type").val();
				
		if(fg_item_id){
			$.ajax({
				url : base_url + controller + '/getTestReportParam',
				type : 'post',
				data : {grade_id : grade_id,fg_item_id:fg_item_id,test_type:test_type},
				dataType : 'json'
			}).done(function(response){
				$("#tcParameter").html(response.html);
			});
		}
		var sample_1 = $("#test_type :selected").data('sample_1'); //0 to 10 tonne 
		var sample_2 = $("#test_type :selected").data('sample_2'); //10-25 tonne 
		var sample_3 = $("#test_type :selected").data('sample_3'); //25 and above 

		var grn_qty_ton = parseFloat($("#grn_qty").val()) * 0.001;
		sample_qty = 0;
		if(grn_qty_ton > 0 && grn_qty_ton < 10){ sample_qty = sample_1; } 
		else if(grn_qty_ton >= 10 && grn_qty_ton < 25){ sample_qty = sample_2; } 
		else if(grn_qty_ton >= 25){ sample_qty = sample_3; } 
		
		if(test_type != 0){
			$("#sample_qty").val(sample_qty);
		}else{
			$("#sample_qty").val(1);
		}
	});
});

function editTcReport(data,button){ 
	var row_index = $(button).closest("tr").index();
	$.each(data,function(key, value) {
		if(key != 'tc_file'){
			$("#"+key).val(value);
		}
		if(key == 'agency_id' || key == 'ins_type' || key == 'test_type' || key == 'batch_no' || key == 'sample_qty'){
			$("#"+key).prop("readonly", true);
			$('#'+key+' option:not(:selected)').attr('disabled', true);
		}
	});
	setTimeout(function(){ 
		$("#ins_type").trigger("change");
		$('#agency_id').trigger('change');
	}, 10);

	var grade_id = $('#grade_id').val();	
	if(grade_id){
		setTimeout(function(){ 
			$.ajax({
				url : base_url + controller + '/getTestReportParam',
				type : 'post',
				data : {grade_id : grade_id,test_type:data.test_type,main_id:data.id, fg_item_id:data.fg_item_id},
				dataType : 'json'
			}).done(function(response){
				$("#tcParameter").html("");
				$("#tcParameter").html(response.html);
			});
		}, 100);			
	}	
	initSelect2();
}