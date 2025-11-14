<?php $this->load->view('includes/header'); ?>
<div class="page-content-tab">
    <div class="container-fluid">
        <div class="row">
            <div class="col-sm-12">
                <div class="page-title-box">
					<div class="float-start">
						<ul class="nav nav-pills">    
							<li class="nav-item"> <a href="<?= base_url($headData->controller . "/index/1") ?>" class="btn waves-effect waves-light btn-outline-info <?=($status == 1)?'active':''?>">In Stock</a> </li>
							<li class="nav-item"> <a href="<?= base_url($headData->controller . "/index/0") ?>" class="btn waves-effect waves-light btn-outline-info <?=($status == 0)?'active':''?>">New Inward</a> </li>
							<li class="nav-item"> <a href="<?= base_url($headData->controller . "/indexUsed/2") ?>" class="btn waves-effect waves-light btn-outline-info <?=($status == 2)?'active':''?>">Issued</a> </li>
							<li class="nav-item"> <a href="<?= base_url($headData->controller . "/indexUsed/3") ?>" class="btn waves-effect waves-light btn-outline-info <?=($status == 3)?'active':''?>">In Calibration</a> </li>
							<li class="nav-item"> <a href="<?= base_url($headData->controller . "/index/4") ?>" class="btn waves-effect waves-light btn-outline-info <?=($status == 4)?'active':''?>">Rejected</a> </li>
							<li class="nav-item"> <a href="<?= base_url($headData->controller . "/index/5") ?>" class="btn waves-effect waves-light btn-outline-info <?=($status == 5)?'active':''?>">Due For Calibration</a> </li>
						</ul>
                    </div>
					<div class="float-end">
                        <?php
                            $addParam = "{'postData':{'id' : ''},'modal_id' : 'bs-right-lg-modal', 'call_function':'addInstrument', 'form_id' : 'addInstrument', 'title' : 'Add Instrument'}";
                        ?>
                        <button type="button" class="btn waves-effect waves-light btn-outline-dark float-right press-add-btn" onclick="modalAction(<?=$addParam?>);"><i class="fa fa-plus"></i> Add Instrument</button>
					</div>   				
                </div>
            </div>
        </div>
        <div class="row">
            <div class="col-12">
                <div class="card">
                    <div class="card-body">
                        <div class="table-responsive">
                            <table id='instrumentTable' class="table table-bordered ssTable ssTable-cf" data-url='/getDTRows/<?=$status?>'></table>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
<div class="modal fade" id="rejectGaugeModal" tabindex="-1" role="dialog" aria-labelledby="exampleModalLabel1" data-backdrop="static" data-keyboard="false">
    <div class="modal-dialog" role="document">
        <div class="modal-content animated slideDown">
            <div class="modal-header">
                <h4 class="modal-title" id="exampleModalLabel1">Reject Instrument [Instrument Code: <span id="gauge_code"></span>]</h4>
                <button type="button" class="close" data-bs-dismiss="modal" aria-label="Close"><span aria-hidden="true">&times;</span></button>
            </div>
            <form id="rejectGauge">
                <input type="hidden" name="gauge_id" id="gauge_id" value="">
                <div class="modal-body">
                    <div class="row">
                        <div class="col-md-12 form-group">
                            <label for="reject_reason">Reject Reason</label>
                            <textarea name="reject_reason" id="reject_reason" class="form-control" rows="2"></textarea>
                        </div>
                    </div>
                </div>
            </form>
            <div class="modal-footer">
                <button type="button" class="btn waves-effect waves-light btn-outline-secondary close" data-bs-dismiss="modal"><i class="fa fa-times"></i> Close</button>
                <button type="button" class="btn waves-effect waves-light btn-outline-success" onclick="saveRejectGauge('rejectGauge');"><i class="fa fa-check"></i> Save</button>
            </div>
        </div>
    </div>
</div>
<?php $this->load->view('includes/footer'); ?>
<script>
$(document).ready(function(){
    initBulkChallanButton();
    $(document).on('click','.rejectGauge',function(){
        var id = $(this).data('id');
        var gauge_code = $(this).data('gauge_code');
        $(".error").html("");
		$("#rejectGaugeModal").modal('show');
		$("#gauge_code").html(gauge_code);
		$("#gauge_id").val(id);
    });
    
	$(document).on('click', '.BulkInstChallan', function() {
		if ($(this).attr('id') == "masterInstSelect") {
			if ($(this).prop('checked') == true) {
				$(".bulkChallan").show();
				$("input[name='ref_id[]']").prop('checked', true);
			} else {
				$(".bulkChallan").hide();
				$("input[name='ref_id[]']").prop('checked', false);
			}
		} else {
			if ($("input[name='ref_id[]']").not(':checked').length != $("input[name='ref_id[]']").length) {
				$(".bulkChallan").show();
				$("#masterInstSelect").prop('checked', false);
			} else {
				$(".bulkChallan").hide();
			}

			if ($("input[name='ref_id[]']:checked").length == $("input[name='ref_id[]']").length) {
				$("#masterInstSelect").prop('checked', true);
				$(".bulkChallan").show();
			}
			else{$("#masterInstSelect").prop('checked', false);}
		}
	});
	
	$(document).on('click', '.bulkChallan', function() {
		var ref_id = [];
		$("input[name='ref_id[]']:checked").each(function() {
			ref_id.push(this.value);
		});
		var ids = ref_id.join("~");
		var send_data = {
			ids
		};
		Swal.fire({
			title: 'Are you sure?',
			text: 'Are you sure want to generate Challan?',
			icon: 'warning',
			showCancelButton: true,
			confirmButtonColor: '#3085d6',
			cancelButtonColor: '#d33',
			confirmButtonText: 'Yes, Do it!',
		}).then(function(result) {
			if (result.isConfirmed){				
				window.open(base_url + 'qcChallan/createChallan/' + ids, '_self');
			}
		});
	});

});

function inwardGauge(data){
	var button = data.button;if(button == "" || button == null){button="both";};
	var fnedit = data.fnedit;if(fnedit == "" || fnedit == null){fnedit="edit";}
	var fnsave = data.fnsave;if(fnsave == "" || fnsave == null){fnsave="save";}
	var savebtn_text = data.savebtn_text;if(savebtn_text == "" || savebtn_text == null){savebtn_text="Save";}
	var sendData = {id:data.id,status:data.status};
	$.ajax({ 
		type: "POST",   
		url: base_url + controller + '/' + fnedit,
		data: sendData,
	}).done(function(response){
		$("#"+data.modal_id).modal('show');
		$("#"+data.modal_id+' .modal-body').html('');
		$("#"+data.modal_id).addClass(data.form_id+"Modal");
		$("#"+data.modal_id+' .modal-title').html(data.title);
		$("#"+data.modal_id+' .modal-body').html(response);
		$("#"+data.modal_id+" .modal-body form").attr('id',data.form_id);
		$("#"+data.modal_id+" .modal-footer .btn-save").attr('onclick',"store({formId : '"+data.form_id+"', fnsave : '"+fnsave+"'});");
		$("#"+data.modal_id+" .modal-footer .btn-save-close").attr('onclick',"store({formId : '"+data.form_id+"', fnsave : '"+fnsave+"', save_close : 'save_close'});");
		$("#"+data.modal_id+" .modal-footer .btn-close").attr('data-modal_id',data.form_id);
		if(button == "close"){
			$("#"+data.modal_id+" .modal-footer .btn-close").show();
			$("#"+data.modal_id+" .modal-footer .btn-save").hide();
			$("#"+data.modalId+" .modal-footer .btn-save-close").hide();
		}else if(button == "save"){
			$("#"+data.modal_id+" .modal-footer .btn-close").hide();
			$("#"+data.modal_id+" .modal-footer .btn-save").show();
            $("#"+data.modalId+" .modal-footer .btn-save-close").show();
		}else{
			$("#"+data.modal_id+" .modal-footer .btn-close").show();
			$("#"+data.modal_id+" .modal-footer .btn-save").show();
            $("#"+data.modalId+" .modal-footer .btn-save-close").show();
		}
		initModalSelect();
		$(".single-select").select2();
		$('.model-select2').select2({ dropdownParent: $('.model-select2').parent() });});
		initMultiSelect();setPlaceHolder();
	}

function saveRejectGauge(formId){
	var fd = $('#'+formId).serialize();
	$.ajax({
		url: base_url + controller + '/saveRejectGauge',
		data:fd,
		type: "POST",
		dataType:"json",
	}).done(function(data){
		if(data.status==1){
			$("#gauge_id").val(""); $("#gauge_code").html(""); $(".modal").modal('hide');
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
	});
}

function initBulkChallanButton() {
	var bulkChallanBtn = '<button class="btn btn-outline-dark bulkChallan" tabindex="0" aria-controls="instrumentTable" type="button"><span>Bulk Challan</span></button>';
	$("#instrumentTable_wrapper .dt-buttons").append(bulkChallanBtn);
	$(".bulkChallan").hide();
}
</script>