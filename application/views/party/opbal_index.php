<?php $this->load->view('includes/header'); ?>
<style>
.text-success {
    color: #109118 !important;
}
</style>
<div class="page-content-tab">
	<div class="container-fluid">
		<div class="row">
			<div class="col-sm-12">
				<div class="page-title-box">
					<div class="float-end"  style="width:40%;">
                        <div class="input-group">
                            <div class="input-group-append" style="width:70%;">
                                <select name="group_id" id="group_id" class="form-control select2 req">
                                    <option value="">Select Group</option>
                                    <?php
                                        foreach($grpData as $row):
                                            $selected = (!empty($dataRow->group_id) && $row->id == $dataRow->group_id)?"selected":"";
                                            echo "<option value='".$row->id."' data-row='".json_encode($row)."' ".$selected.">".$row->name."</option>";
                                        endforeach;
                                    ?>
                                </select>
                            </div>
                            <div class="input-group-append">
                                <button type="button" class="btn waves-effect waves-light btn-success float-right loadData refreshReportData" title="Load Data">
                                    <i class="fas fa-sync-alt"></i> Load
                                </button>
                            </div>
                        </div>
					</div>
					<h4 class="page-title">Update Ledger Opening</h4>
				</div>
			</div>
		</div>
        <div class="row">
            <div class="col-12">
                <div class="card">
                    <div class="card-body">
                        <form autocomplete="off" id="saveLedgerOp">					
							<div class="col-md-12 mt-3">
								<div class="error op_data_error"></div>
								<div class="row form-group">
									<div class="table-responsive ">
										<table id="ledgerOpening" class="table table-bordered">
											<thead class="thead-dark">
												<tr>
													<th style="width:5%;">#</th>
													<th>Party Name</th>
													<th>Op. Balance</th>
													<th>New Op. Balance</th>
                                                    <th>Action</th>
												</tr>
											</thead>
                                            <tbody id="ledgerOpeningData">
                                                <!-- <tr>
                                                    <td class="text-center" colspan="5">No data available in table</td>
                                                </tr> -->
                                            </tbody>
										</table>
									</div>
								</div>
							</div>
                        </form>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<?php $this->load->view('includes/footer'); ?>
<script>
$(document).ready(function(){
    reportTable("ledgerOpening");

    $(document).on('click','.loadData',function(e){
		$(".error").html("");
		var group_id = $('#group_id').val();
        $.ajax({
            url: base_url + controller + '/getGroupWiseLedger',
            data: {group_id:group_id},
            type: "POST",
            dataType:'json',
            success:function(data){
                $('#ledgerOpening').DataTable().clear().destroy();
                $("#ledgerOpeningData").html(data.tbody);
                reportTable("ledgerOpening");
            }
        });
    }); 

    $(document).on('click','.saveOp',function(){
        var id = $(this).data('id');
        var balance_type = $("#balance_type_"+id).val();
        var opening_balance = $("#op_balance_"+id).val();

        var fd = {id:id,balance_type:balance_type,opening_balance:opening_balance};
        
        Swal.fire({
            title: 'Confirm!',
            text: 'Are you sure to update ledger opening balance?',
            icon: 'warning',
            showCancelButton: true,
            confirmButtonColor: '#3085d6',
            cancelButtonColor: '#d33',
            confirmButtonText: 'Yes, Do it!',
        }).then(function(result) {
            if (result.isConfirmed){
                $.ajax({
                    url: base_url + controller + '/saveOpeningBalance',
                    data:fd,
                    type: "POST",
                    dataType:"json",
                }).done(function(data){
                    if(data.status===0){
                        $(".error").html("");
                        $.each( data.message, function( key, value ) {$("."+key).html(value);});
                    }else if(data.status==1){

                        var cur_op = parseFloat(parseFloat(opening_balance) * parseFloat(balance_type)).toFixed(2);
                        var cur_op_text = '';
                        if(parseFloat(cur_op) > 0){
                            cur_op_text = '<span class="text-success">'+Math.abs(cur_op)+' CR</span>';
                        }else if(parseFloat(cur_op) < 0){
                            cur_op_text = '<span class="text-danger">'+Math.abs(cur_op)+' DR</span>';
                        }else{
                            cur_op_text = cur_op;
                        }
                        $("#cur_op_"+id).html(cur_op_text);

                        //toastr.success(data.message, 'Success', { "showMethod": "slideDown", "hideMethod": "slideUp", "closeButton": true, positionClass: 'toastr toast-bottom-center', containerId: 'toast-bottom-center', "progressBar": true });

                        Swal.fire( 'Success', data.message, 'success' );
                    }else{
                        //toastr.error(data.message, 'Error', { "showMethod": "slideDown", "hideMethod": "slideUp", "closeButton": true, positionClass: 'toastr toast-bottom-center', containerId: 'toast-bottom-center', "progressBar": true });

                        Swal.fire( 'Sorry...!', data.message, 'error' );
                    }
                            
                });
            }
	    });

        /* $.confirm({
            title: 'Confirm!',
            content: 'Are you sure to update ledger opening balance?',
            type: 'orange',
            buttons: {   
                ok: {
                    text: "ok!",
                    btnClass: 'btn waves-effect waves-light btn-outline-success',
                    keys: ['enter'],
                    action: function(){
                        $.ajax({
                            url: base_url + controller + '/saveOpeningBalance',
                            data:fd,
                            type: "POST",
                            dataType:"json",
                        }).done(function(data){
                            if(data.status===0){
                                $(".error").html("");
                                $.each( data.message, function( key, value ) {$("."+key).html(value);});
                            }else if(data.status==1){

                                var cur_op = parseFloat(parseFloat(op_balance) * parseFloat(balance_type)).toFixed(2);
                                var cur_op_text = '';
                                if(parseFloat(cur_op) > 0){
                                    cur_op_text = '<span class="text-success">'+cur_op+' CR</span>';
                                }else if(parseFloat(cur_op) < 0){
                                    cur_op_text = '<span class="text-danger">'+Math.abs(cur_op)+' DR</span>';
                                }else{
                                    cur_op_text = cur_op;
                                }
                                $("#cur_op_"+id).html(cur_op_text);

                                //toastr.success(data.message, 'Success', { "showMethod": "slideDown", "hideMethod": "slideUp", "closeButton": true, positionClass: 'toastr toast-bottom-center', containerId: 'toast-bottom-center', "progressBar": true });

                                Swal.fire( 'Success', response.message, 'success' );
                            }else{
                                //toastr.error(data.message, 'Error', { "showMethod": "slideDown", "hideMethod": "slideUp", "closeButton": true, positionClass: 'toastr toast-bottom-center', containerId: 'toast-bottom-center', "progressBar": true });

                                Swal.fire( 'Sorry...!', response.message, 'error' );
                            }
                                    
                        });
                    }
                },
                cancel: {
                    btnClass: 'btn waves-effect waves-light btn-outline-secondary',
                    action: function(){

                    }
                }
            }
        }); */
    });
});
</script>