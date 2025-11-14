$(document).ready(function(){
	
	$(document).on("click",".btn-leaveAction",function(){
		$('#id').val($(this).data('id'));
		var approve_status = $(this).data('approve_status');
		$('#approve_type').val(approve_status);
		$("#approveLeaveModal").modal('show');
		
		$('#approve_status').html('');
	    $('#approve_status').html('<option value="2">Approve</option><option value="4">Decline</option>');
	    //$('#approve_status').comboSelect();
		initSelect2('approveLeaveModal');
		
		var leave_type = $(this).data('type_leave');
        var start_date = $(this).data('min_date'); 
        var emp_id = $(this).data('emp_id');
		if(start_date != '' && leave_type == 'SL'){
    	    $.ajax({
    			url:base_url + controller + '/getLeaveQuota',
    			type:'post',
    			data:{emp_id:emp_id,start_date:start_date},
    			dataType:'json',
    			success:function(data){
    			    $('.max-leave').html('Max Leave: '+data.max_leave);
    			    $('.used-leave').html('Used Leave: '+data.used_leave);
    			    var remain = parseFloat(data.max_leave) - parseFloat(data.used_leave);
    			    $('.remain-leave').html('Remain Leave: '+remain);
    			    if(parseFloat(remain) > 0){
    			        $('#is_penalty').val(2);
    			        //$('#is_penalty').comboSelect();
						initSelect2('approveLeaveModal');
    			    }else{
    			        $('#is_penalty').val(1);
    			        //$('#is_penalty').comboSelect();
						initSelect2('approveLeaveModal');
    			    }
    			}
    		});	
    		
    		$('.is_penalty').show();
            $(".approve_date").addClass("col-md-6 form-group approve_date"); 
        }else{
            $('.is_penalty').hide();
            $(".approve_date").addClass("col-md-12 form-group approve_date"); 
        }
        var dt = $(this).data('created_at').split('-');
        var date = dt[0] +"-"+ dt[1] +"-"+ dt[2];
        $("input[type=date]").val(date);
        $('#approved_date').attr('value',$(this).data('created_at'));
	});
	
	$(document).on("click",".btn-approveLeave",function(){
		var fd = $('#approveLeaveForm').serialize();
		$.ajax({
			url: base_url + controller + '/approveLeave',
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
				initTable(); $(".modal").modal('hide');
				Swal.fire({ icon: 'success', title: data.message});
				//toastr.success(data.message, 'Success', { "showMethod": "slideDown", "hideMethod": "slideUp", "closeButton": true, positionClass: 'toastr toast-bottom-center', containerId: 'toast-bottom-center', "progressBar": true });
			}else{
				initTable(); $(".modal").modal('hide');
				Swal.fire({ icon: 'error', title: data.message });
				//toastr.error(data.message, 'Error', { "showMethod": "slideDown", "hideMethod": "slideUp", "closeButton": true, positionClass: 'toastr toast-bottom-center', containerId: 'toast-bottom-center', "progressBar": true });
			}
					
		});
	});
});