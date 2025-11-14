$(document).ready(function(){
    $(document).on('click','.enquiryConfirmed',function(){
        var functionName = $(this).data("function");
        var modalId = $(this).data('modal_id');
        var button = $(this).data('button');
		var title = $(this).data('form_title');
		var formId = functionName;

        var enq_id = $(this).data('id');
		var partyName = $(this).data('party');
		var enquiry_no = $(this).data('enqno');
		var enquiry_date = $(this).data('enqdate');	

        $.ajax({ 
            type: "POST",   
            url: base_url + 'purchaseEnquiry/' + functionName,   
            data: {enq_id:enq_id}
        }).done(function(response){
            $("#"+modalId).modal('show');
			$("#"+modalId+' .modal-title').html(title);
            $("#"+modalId+' .modal-body').html(response);
            $("#"+modalId+" .modal-body form").attr('id',formId);
            $("#"+modalId+" .modal-footer .btn-save").attr('onclick',"enquiryConfirmedSave('"+formId+"');");
            if(button == "close"){
                $("#"+modalId+" .modal-footer .btn-close").show();
                $("#"+modalId+" .modal-footer .btn-save").hide();
            }else if(button == "save"){
                $("#"+modalId+" .modal-footer .btn-close").hide();
                $("#"+modalId+" .modal-footer .btn-save").show();
            }else{
                $("#"+modalId+" .modal-footer .btn-close").show();
                $("#"+modalId+" .modal-footer .btn-save").show();
            }
			initSelect2();

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
    });

    $(document).on("click",".itemCheck",function(){
        var id = $(this).data('rowid');
        if($("#md_checkbox"+id).attr('check') == "checked"){
            $("#md_checkbox"+id).attr('check','');
            $("#md_checkbox"+id).removeAttr('checked');
            $("#item_name"+id).attr('disabled','disabled');
            $("#qty"+id).attr('disabled','disabled');
            $("#rate"+id).attr('disabled','disabled');
            $("#quote_date"+id).attr('disabled','disabled');
            $("#quote_no"+id).attr('disabled','disabled');
            $("#quote_remark"+id).attr('disabled','disabled');
            $("#feasible"+id).attr('disabled','disabled');
            $("#trans_id"+id).attr('disabled','disabled');
        }else{
            $("#md_checkbox"+id).attr('check','checked');
            $("#item_name"+id).removeAttr('disabled');
            $("#qty"+id).removeAttr('disabled');
            $("#rate"+id).removeAttr('disabled');
            $("#quote_date"+id).removeAttr('disabled');
            $("#quote_no"+id).removeAttr('disabled');
            $("#quote_remark"+id).removeAttr('disabled');
            $("#feasible"+id).removeAttr('disabled');
            $("#trans_id"+id).removeAttr('disabled');
        }
    });
    
	$(document).on('click',".approvePEnquiry",function(){
		var id = $(this).data('id');
		var val = $(this).data('val');
		var msg = $(this).data('msg');

		var ajaxParam = {
			url: base_url + controller + '/approvePEnquiry',
			data: {id:id, val:val, msg:msg},
			type: "POST",
			dataType:"json"
		};
		Swal.fire({
			title: 'Confirm!',
			text: 'Are you sure want to '+ msg +' this Enquiry?',
			icon: 'warning',
			showCancelButton: true,
			confirmButtonColor: '#3085d6',
			cancelButtonColor: '#d33',
			confirmButtonText: 'Yes, Do it!',
		}).then(function(result) {
			if (result.isConfirmed){
				$.ajax(ajaxParam).done(function(response){
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
				});
			}
		});
	});
});

function enquiryConfirmedSave(formId){
	var fd = $('#'+formId).serialize();
	$.ajax({
		url: base_url + 'purchaseEnquiry/enquiryConfirmed',
		data:fd,
		type: "POST",
		dataType:"json",
    }).done(function(data){
        if(data.status===0){
            $(".error").html("");
            $.each( data.message, function( key, value ) {
                $("."+key).html(value);
            });
        }else{
            initTable(); $(".modal").modal('hide');
			Swal.fire( 'Success', data.message, 'success' );
        }		
	});
}