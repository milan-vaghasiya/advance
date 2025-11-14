<?php $this->load->view('includes/header'); ?>
<div class="page-content-tab">
    <div class="container-fluid">
        <div class="row">
            <div class="col-sm-12">
                <div class="page-title-box">
                    <div class="float-start">
                        <ul class="nav nav-pills">
                            <li class="nav-item"> <button onclick="statusTab('dieProductionTable',1);" class="btn waves-effect waves-light btn-outline-info active" data-toggle="tab" aria-expanded="false">Pending</button> </li>
                            <li class="nav-item"> <button onclick="statusTab('dieProductionTable',2);" class="btn waves-effect waves-light btn-outline-info" data-toggle="tab" aria-expanded="false">In Progress</button> </li>
                            <li class="nav-item"> <button onclick="statusTab('dieProductionTable',5);" class="btn waves-effect waves-light btn-outline-info" data-toggle="tab" aria-expanded="false">M/C Done</button> </li>
                            <li class="nav-item"> <button onclick="statusTab('dieProductionTable',6);" class="btn waves-effect waves-light btn-outline-info" data-toggle="tab" aria-expanded="false">POP Done</button> </li>
                            <li class="nav-item"> <button onclick="statusTab('dieProductionTable',9);" class="btn waves-effect waves-light btn-outline-info" data-toggle="tab" aria-expanded="false">Approve</button> </li>
                            <li class="nav-item"> <a href="<?=base_url($headData->controller.'/scrapIndex')?>" target="_blank" class="btn waves-effect waves-light btn-outline-info">Scrap</a></li>
                        </ul>
                    </div>
                    <div class="float-end">
                        <?php
                            $addParam = "{'modal_id' : 'bs-right-md-modal', 'call_function':'addDieProduction', 'form_id' : 'addDieProduction', 'title' : 'Add Die Production'}";
                        ?>
                        <button type="button" class="btn waves-effect waves-light btn-outline-dark float-right press-add-btn" onclick="modalAction(<?=$addParam?>);"><i class="fa fa-plus"></i> Add Die Production</button>
                    </div>
                </div>
            </div>
        </div>
        <div class="row">
            <div class="col-12">
                <div class="card">
                    <div class="card-body">
                        <div class="table-responsive">
                            <table id='dieProductionTable' class="table table-bordered ssTable ssTable-cf" data-url='/getDTRows'></table>
                        </div>
                    </div>
                </div>
            </div>
        </div>       
    </div>
</div>
<div class="modal modal-right fade" id="bs_approval_modal" role="dialog" data-bs-backdrop="static" data-bs-keyboard="false" >
	<div class="modal-dialog modal-md" role="document">
		<div class="modal-content">
			<div class="modal-header">
				<h6 class="modal-title m-0"></h6>
				<button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
			</div>
			<div class="modal-body"></div>
			<div class="modal-footer modal-footer-fixed">

				<button type="button" class="btn btn-secondary press-close-btn btn-close-modal save-form" data-bs-dismiss="modal"><i class="fa fa-times"></i> Close</button>

                <button type="button" class="btn btn-success approveBtn" onclick="approve()"><i class="fa fa-check"></i> Approve</button>

                <button type="button" class="btn btn-danger  rejectBtn" onclick="reject()"><i class="fa fa-close"></i> Reject</button>

                <button type="button" class="btn btn-warning  recutBtn"  onclick="recut()"><i class="fas fa-cogs"></i> Recut</button>


			</div>
		</div>
	</div>
</div>
<?php $this->load->view('includes/footer'); ?>
<script src="<?=base_url()?>assets/plugins/imask/imask.js"></script>

<script>
    $(document).ready(function() {

        $(document).on('click', '.diekitCheck', function() {
            var id = $(this).data('rowid');
            if($("#dk_ch_"+id).attr('check') == "checked"){
                $("#dk_ch_"+id).attr('check','');
                $("#dk_ch_"+id).removeAttr('checked');
                $("#diekit_qty_"+id).attr('disabled','disabled');
                $("#rowid_"+id).attr('disabled','disabled');
            }else{
                $("#dk_ch_"+id).attr('check','checked');
                $("#diekit_qty_"+id).removeAttr('disabled');
                $("#rowid_"+id).removeAttr('disabled');
            }
        });

        $(document).on('click','.changeStatus',function(){
            var id = $(this).data('id');
            var status = $(this).data('val');
            var msg = $(this).data('msg');

            Swal.fire({
                title: 'Confirm!',
                text: 'Are you sure want to '+msg+' this Die Product?',
                icon: 'warning',
                showCancelButton: true,
                confirmButtonColor: '#3085d6',
                cancelButtonColor: '#d33',
                confirmButtonText: 'Yes',
            }).then(function(result) {
                if (result.isConfirmed){
                    $.ajax({
                        url: base_url + controller + '/changeStatus',
                        data: {id:id,status:status,msg:msg},
                        type: "POST",
                        dataType:"json",
                    }).done(function(response){
                        if(response.status==0) {
                            Swal.fire( 'Sorry...!', data.message, 'error' );
                        } else {
                            initTable();
                            Swal.fire({ icon: 'success', title: response.message});
                        }
                    });
                }
            });
        });

    });

    function modalApproveAction(data){
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
            initApprovalModal(data,response);
        });
    }
    function initApprovalModal(postData,response){

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
        var message = postData.message || '';

        var fnJson = "{'formId':'"+postData.form_id+"','fnsave':'"+fnsave+"','controller':'"+controllerName+"','txt_editor':'"+txt_editor+"','form_close':'"+form_close+"','message':'"+message+"'}";

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

        $("#"+postData.modal_id+" .modal-footer .approveBtn").attr('onclick',"approve("+fnJson+");");
        $("#"+postData.modal_id+" .modal-footer .rejectBtn").attr('onclick',"reject("+fnJson+");");
        $("#"+postData.modal_id+" .modal-footer .recutBtn").attr('onclick',"recut("+fnJson+");");
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

    function approve(postData){
        var input = $("<input>").attr("type", "hidden").attr("name", "status").val(9);
        $('#'+postData.formId).append($(input));
        confirmStore(postData);
    }
    function reject(postData){
        var input = $("<input>").attr("type", "hidden").attr("name", "status").val(8);
        $('#'+postData.formId).append($(input));
        confirmStore(postData);
    }
    function recut(postData){
        var input = $("<input>").attr("type", "hidden").attr("name", "status").val(7);
        $('#'+postData.formId).append($(input));
        confirmStore(postData);
    }
</script>