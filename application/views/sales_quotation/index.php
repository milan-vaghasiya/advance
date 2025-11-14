<?php $this->load->view('includes/header'); ?>
<div class="page-content-tab">
	<div class="container-fluid">
        <div class="row">
            <div class="col-sm-12">
				<div class="page-title-box">
                    <div class="float-start">
                        <ul class="nav nav-pills">
                            <li class="nav-item"> 
                                <button onclick="statusTab('salesQuotationTable',0);" id="pending_sq" class="nav-tab btn waves-effect waves-light btn-outline-danger active" style="outline:0px" data-toggle="tab" aria-expanded="false">Pending</button> 
                            </li>
                            <li class="nav-item"> 
                                <button onclick="statusTab('salesQuotationTable',3);" id="approve_sq" class="nav-tab btn waves-effect waves-light btn-outline-warning" style="outline:0px" data-toggle="tab" aria-expanded="false">Approved</button> 
                            </li>
                            <li class="nav-item"> 
                                <button onclick="statusTab('salesQuotationTable',1);" id="complete_sq" class="nav-tab btn waves-effect waves-light btn-outline-success" style="outline:0px" data-toggle="tab" aria-expanded="false">Completed</button> 
                            </li>
							<li class="nav-item"> 
                                <button onclick="statusTab('salesQuotationTable',2);" id="cancel_sq" class="nav-tab btn waves-effect waves-light btn-outline-primary" style="outline:0px" data-toggle="tab" aria-expanded="false">Cancel</button>  
                            </li>
                        </ul>
                    </div>
					<div class="float-end">
					    <a href="javascript:void(0)" class="btn waves-effect waves-light btn-outline-dark float-right permission-write press-add-btn" onclick="window.location.href='<?=base_url($headData->controller.'/addQuotation')?>'"><i class="fa fa-plus"></i> Add Quotation</a>
					</div>
				</div>
            </div>
		</div>
        <div class="row">
            <div class="col-12">
				<div class="col-12">
					<div class="card">
                        <div class="card-body">
                            <div class="table-responsive">
                                <table id='salesQuotationTable' class="table table-bordered ssTable ssTable-cf" data-url='/getDTRows'></table>
                            </div>
                        </div>
					</div>
				</div>
            </div>
        </div>
    </div>
</div>
<?php $this->load->view('includes/footer'); ?>

<script>
$(document).ready(function(){

	$(document).on('click','.confirmQuotation',function(){
        var functionName = $(this).data("function");
        var modalId = $(this).data('modal_id');
        var button = $(this).data('button');
		var title = $(this).data('form_title');
		var formId = functionName;

        var quote_id = $(this).data('id');
		var customerId = $(this).data('customer_id');
		var partyName = $(this).data('party');
		var quote_no = $(this).data('quote_no');
		var quotation_date = $(this).data('quotation_date');
		
        $.ajax({ 
            type: "POST",   
            url: base_url + 'salesQuotation/' + functionName,   
            data: {quote_id:quote_id}
        }).done(function(response){
            $("#"+modalId).modal('show');
			$("#"+modalId+' .modal-title').html(title);
            $("#"+modalId+' .modal-body').html(response);
            $("#"+modalId+" .modal-body form").attr('id',formId);
            $("#"+modalId+" .modal-dialog").css('max-width','40%');
            $("#"+modalId+" .modal-footer .btn-save").attr('onclick',"saveConfirmQuotation('"+formId+"');");
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

            $("#customer_id").val(customerId);
            $("#party_name").html(partyName);
            $("#quote_no").html(quote_no);
            $("#quotation_date").html(quotation_date);
            $("#id").val(quote_id);$("#quote_id").val(quote_id);
            $(".modal-lg").attr("style","max-width: 70% !important;");
            $('.floatOnly').keypress(function(event) {
                if ((event.which != 46 || $(this).val().indexOf('.') != -1) && (event.which < 48 || event.which > 57)) {
                    event.preventDefault();
                }
            });
        });
    });

	$(document).on("click",".itemCheckCQ",function(){
        var id = $(this).data('rowid');
        if($("#md_checkbox"+id).attr('check') == "checked"){
            $("#md_checkbox"+id).attr('check','');
            $("#md_checkbox"+id).removeAttr('checked');
            $("#qty"+id).attr('disabled','disabled');
            $("#unit_id"+id).attr('disabled','disabled');
            $("#price"+id).attr('disabled','disabled');
            $("#item_id"+id).attr('disabled','disabled');
            $("#automotive"+id).attr('disabled','disabled');
            $("#inq_trans_id"+id).attr('disabled','disabled');
            $("#confirm_price"+id).attr('disabled','disabled');
            $("#trans_id"+id).attr('disabled','disabled');
            $("#drg_rev_no"+id).attr('disabled','disabled');
            $("#rev_no"+id).attr('disabled','disabled');

        }else{
            $("#md_checkbox"+id).attr('check','checked');
            $("#qty"+id).removeAttr('disabled');
            $("#price"+id).removeAttr('disabled');
            $("#unit_id"+id).removeAttr('disabled');
            $("#automotive"+id).removeAttr('disabled');
            $("#item_id"+id).removeAttr('disabled');
            $("#inq_trans_id"+id).removeAttr('disabled');
            $("#confirm_price"+id).removeAttr('disabled');
            $("#trans_id"+id).removeAttr('disabled');
            $("#drg_rev_no"+id).removeAttr('disabled');
            $("#rev_no"+id).removeAttr('disabled');
        }
    });

});

function saveConfirmQuotation(formId){
	var fd = $('#'+formId).serialize();
	$.ajax({
		url: base_url + 'salesQuotation/saveConfirmQuotation',
		data:fd,
		type: "POST",
		dataType:"json",
    }).done(function(data){
        if(data.status===0){
			if(typeof data.message === "object"){
				$(".error").html("");
				$.each( data.message, function( key, value ) {$("."+key).html(value);});
			}else{
				Swal.fire({ icon: 'error', title: data.message });
			}	
        }else{
			$('#'+formId)[0].reset();
			Swal.fire({ icon: 'success', title: data.message});
			window.location = base_url + controller;
        }		
	});
}
</script>