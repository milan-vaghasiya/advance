<?php $this->load->view('includes/header'); ?>
<div class="page-content-tab">
	<div class="container-fluid">
        <div class="row">
            <div class="col-12">
                <div class="card">
                    <div class="card-header">
                        <h4 class="card-title">Dispatch Plan For : <?=(!empty($orderItemList[0]->party_name) ? $orderItemList[0]->party_name : '')?></h4>
                    </div>
                    <div class="card-body">
						<form autocomplete="off" id="saveDispatchPlan" data-res_function="resDispatchPlan">
                            <div class="row">
                                <div class="col-md-3 form-group">
                                    <label for="plan_number">Plan No.</label>
                                    <input type="text" name="plan_number" id="plan_number" value="<?=$plan_number?>" class="form-control" readonly>
                                </div>
                                <div class="col-md-3 form-group">
                                    <label for="plan_date">Plan Date</label>
                                    <input type="date" name="plan_date" id="plan_date" value="<?=date("Y-m-d")?>" class="form-control">
                                </div>
                            </div>
                            <div class="col-md-12">
                                <div class="row">

                                    <input type="hidden" name="party_id" id="party_id" value="<?=(!empty($party_id) ? $party_id : '')?>">

                                    <div class="error general_error"></div>
                                    <div class="table table-responsive">
                                        <table class="table table-bordered">
                                            <thead class="thead-dark">
                                                <tr class="text-center">
                                                    <th style="width:2%">#</th>
                                                    <th style="width:8%"><input type="checkbox" id="masterSelect" class="filled-in chk-col-success BulkDispatchPlan" value=""><label for="masterSelect">Select ALL</label></th>
                                                    <th style="width:10%">SO No.</th>
                                                    <th style="width:30%">Product</th>
                                                    <th style="width:10%">Order Qty</th>
                                                    <th style="width:10%">Dispatch Qty</th>
                                                    <th style="width:10%">Planned Qty</th>
                                                    <th style="width:10%">Pending Qty</th>
                                                    <th style="width:10%">Plan Qty</th>
                                                </tr>
                                            </thead>
                                            <tbody id="tbodyData">
                                                <?php      
                                                if(!empty($orderItemList)):
                                                    $i = 1; 
                                                    foreach($orderItemList as $row):
                                                        echo '<tr class="text-center">
                                                            <td>
                                                                '.$i.'
                                                            </td>
                                                            <td>
                                                                <input type="checkbox" name="so_trans_id[]" id="so_trans_id_'.$row->id.'" class="filled-in chk-col-success BulkDispatchPlan" value="'.$row->id.'"><label for="so_trans_id_'.$row->id.'"></label>
                                                                <input type="hidden" name="id['.$row->id.']" id="id_'.$row->id.'" value="">   
                                                            </td>
                                                            <td>
                                                                '.$row->trans_number.'
                                                            </td>
                                                            <td>
                                                                '.$row->item_name.'
                                                                <input type="hidden" name="item_id['.$row->id.']" id="item_id_'.$row->id.'" value="'.$row->item_id.'">     
                                                            </td>
                                                            <td>
                                                                '.floatval($row->qty).'
                                                            </td>
                                                            <td>
                                                                '.floatval($row->dispatch_qty).'
                                                            </td>
                                                            <td>
                                                                '.floatval($row->plan_qty).'
                                                            </td>
                                                            <td>
                                                                '.floatval($row->pending_qty).'
                                                                <input type="hidden" name="pending_qty['.$row->id.']" id="pending_qty_'.$row->id.'" value="'.floatval($row->pending_qty).'">    
                                                            </td>
                                                            <td>
                                                                <input type="text" name="qty['.$row->id.']" id="qty_'.$row->id.'" class="form-control text-center floatOnly" value="">
                                                            </td>
                                                        </tr>';
                                                        $i++;
                                                    endforeach;
                                                else:
                                                    echo '<tr><td colspan="9" class="text-center">No data found.</td></tr>';
                                                endif;
                                                ?>
                                            </tbody>
                                        </table>
                                    </div>
								</div>
							</div>
                        </form>
                    </div>
                    <div class="card-footer">
                        <div class="col-md-12">
							<button type="button" class="btn waves-effect waves-light btn-success float-right save-form" onclick="customStore({'formId':'saveDispatchPlan'});" ><i class="fa fa-check"></i> Save</button>
                            <button type="button" class="btn btn-secondary press-close-btn btn-close-modal save-form float-right m-r-10" onclick="window.location.href='<?=base_url($headData->controller)?>'"><i class="fa fa-times"></i> Cancel</button>
                        </div>
                    </div>
                </div>
            </div>
        </div>        
    </div>
</div>
<?php $this->load->view('includes/footer'); ?>
<script>
$(document).ready(function() {
    
    $(document).on('click', '.BulkDispatchPlan', function() {
        if ($(this).attr('id') == "masterSelect") {
            if ($(this).prop('checked') == true) {
                $("input[name='so_trans_id[]']").prop('checked', true);
            } else {
                $("input[name='so_trans_id[]']").prop('checked', false);
            }
        } else {
            if ($("input[name='so_trans_id[]']").not(':checked').length != $("input[name='so_trans_id[]']").length) {
                $("#masterSelect").prop('checked', false);
            } else {                
            }
            if ($("input[name='so_trans_id[]']:checked").length == $("input[name='so_trans_id[]']").length) {
                $("#masterSelect").prop('checked', true);
            }
            else{$("#masterSelect").prop('checked', false);}
        }
    });    

});
</script>

<script>
function resDispatchPlan(data,formId){
    if(data.status==1){
        $('#'+formId)[0].reset();
		Swal.fire({ icon: 'success', title: data.message});
        window.location = base_url + controller;
    }else{
        if(typeof data.message === "object"){
            $(".error").html("");
            $.each( data.message, function( key, value ) {$("."+key).html(value);});
        }else{
			Swal.fire({ icon: 'error', title: data.message });
        }			
    }	
}
</script>