$(document).ready(function(){
	$(document).on("change",".leave_type_id",function(){
		var leave_type_id = $(this).val();
		var emp_id = $("#emp_id").val();
		if(leave_type_id)
		{
			$.ajax({
				url: base_url + controller + '/getEmpLeaves',
				type:'post',
				data:{leave_type_id:leave_type_id,emp_id:emp_id},
				dataType:'json',
				success:function(data){
					$(".max-leave").html('Maximum Leave : ' + data.max_leave);
					$(".used-leave").html('Taken Leave : ' + data.used_leaves);
					$(".remain-leave").html('Remain Leave : ' + data.remain_leaves);
				}
			});
		}
		else{$(".max-leave").html('');$(".used-leave").html('');$(".remain-leave").html('');}
	});
	
	$(document).on("change",".countTotalDays",function(){
	    var startDate  = $('#start_date').val();
		var endDate    = $('#end_date').val();
	    var start_section = $('#start_section').val();
		var end_section = $('#end_section').val();
		
		//$('#end_date').attr('min',startDate);
		
		if(start_section == 1){endDate = startDate; $('#end_date').val(startDate); }
		
		if(startDate == endDate){
		    $('#end_section').html("");
		    $('#end_section').html('<option value="0">NA</option>');
		} else {
		    if(!($(this).hasClass('endSection')))
		    {
		        $('#end_section').html("");
		        $('#end_section').html('<option value="">Select End Section</option><option value="1">Half Day</option><option value="3">Full Day</option>');
		    }
		}
		
		const diffInMs   = new Date(endDate) - new Date(startDate)
		const diffInDays = diffInMs / (1000 * 60 * 60 * 24);
		
		var totalDay = parseFloat(diffInDays) + 1;
		if(start_section == 1 || start_section == 2){totalDay =  totalDay -  0.5;}
		if(end_section == 1 || end_section == 2){totalDay = totalDay -  0.5;}
		$("#total_days").val(totalDay);
	});
	
	/*$(document).on("change keyup",".countTotalDays",function(){
		var startDate  = $('#start_date').val();
		var endDate    = $('#end_date').val();
		var startSection    = $('#start_section').val();
		var endSection   = $('#end_section').val();
		
		$(".end_date").html("");
		if(startDate != "" && endDate != ""){

			var diffInMs   = new Date(endDate) - new Date(startDate)
			var diffInDays = diffInMs / (1000 * 60 * 60 * 24) + 1;		

			
			if(startSection != "" || endSection != ""){
				if(startDate == endDate && endSection != ""){
					$(".end_date").html("Start & End Date is same.");
					$('#end_section').val("");
					$(".leave-days").css('padding','5px');
					$(".leave-days").html('You are applying ' + diffInDays + ' Days Leave');
					$("#total_days").val(diffInDays);
				}else{
					if(startSection == 2){
						diffInDays=parseFloat(diffInDays)-0.5;
					}
					if(endSection == 1){
						diffInDays=parseFloat(diffInDays)-0.5;
					}
					if(endSection == 2){
						diffInDays=parseFloat(diffInDays)+0.5;
					}
				}
			}

			$(".leave-days").css('padding','5px');
			$(".leave-days").html('You are applying ' + diffInDays + ' Days Leave');
			$("#total_days").val(diffInDays);
		}else{
			$("#total_days").val(0);
		}
	});*/

	/* $(document).on("change","#start_date",function(){
		$('#end_date').val($(this).val());
		$('#end_date').attr('min',$(this).val());
		
		const startDate  = $('#start_date').val();
		const endDate    = $('#end_date').val();

		const diffInMs   = new Date(endDate) - new Date(startDate)
		const diffInDays = diffInMs / (1000 * 60 * 60 * 24) + 1;
		$(".leave-days").css('padding','5px');
		$(".leave-days").html('You are applying ' + diffInDays + ' Days Leave');
		$("#total_days").val(diffInDays);
	});
	
	$(document).on("change","#end_date",function(){
		const startDate  = $('#start_date').val();
		const endDate    = $('#end_date').val();

		const diffInMs   = new Date(endDate) - new Date(startDate)
		const diffInDays = diffInMs / (1000 * 60 * 60 * 24) + 1;
		$(".leave-days").css('padding','5px');
		$(".leave-days").html('You are applying ' + diffInDays + ' Days Leave');
		$("#total_days").val(diffInDays);
	});
	
	$(document).on("change","#start_section",function(){
		const startDate  = $('#start_date').val();
		const endDate    = $('#end_date').val();
		var startSection    = $('#start_section').val();
		var endSection   = $('#end_section').val();
 
		const  diffInMs   = new Date(endDate) - new Date(startDate)
		var diffInDays = diffInMs / (1000 * 60 * 60 * 24);
		if(startSection == 2){
			diffInDays=parseFloat(diffInDays)-0.5;
		}
		if(endSection == 2){
			diffInDays=parseFloat(diffInDays)+0.5;
		}
		if(endSection == 3){
			diffInDays=parseFloat(diffInDays)+1;
		}
		$(".leave-days").css('padding','5px');
		$(".leave-days").html('You are applying ' + diffInDays + ' Days Leave');
		$("#total_days").val(diffInDays);
	});

	$(document).on("change","#end_section",function(){
		const startDate  = $('#start_date').val();
		const endDate    = $('#end_date').val();
		var startSection    = $('#start_section').val();
		var endSection   = $('#end_section').val();
 
		const  diffInMs   = new Date(endDate) - new Date(startDate)
		var diffInDays = diffInMs / (1000 * 60 * 60 * 24);
		if(startSection == 2){
			diffInDays=parseFloat(diffInDays)-0.5;
		}
		if(endSection == 2){
			diffInDays=parseFloat(diffInDays)+0.5;
		}
		if(endSection == 3){
			diffInDays=parseFloat(diffInDays)+1;
		}
		$(".leave-days").css('padding','5px');
		$(".leave-days").html('You are applying ' + diffInDays + ' Days Leave');
		$("#total_days").val(diffInDays);
	}); */
});