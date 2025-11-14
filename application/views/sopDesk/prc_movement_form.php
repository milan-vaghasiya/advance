<form data-res_function="getPrcMovementResponse">
    <div class="card">
        <div class="media align-items-center btn-group process-tags">
            <span class="badge bg-light-peach btn flex-fill">Currunt Process : <?=!empty($dataRow->current_process)?$dataRow->current_process:(!empty($semiFinish)?'Semi Finished':'')?></span>
            <span class="badge bg-light-cream btn flex-fill" id="pending_movement_qty">Pending Qty :  </span>
        </div>                                       
    </div>
    <div class="row">
        <input type="hidden" id="pending_qty" value="">
        <input type="hidden" name="prc_id" id="prc_id" value="<?=$dataRow->prc_id?>">
        <input type="hidden" name="process_id" id="process_id" value="<?=!empty($dataRow->process_id)?$dataRow->process_id:(!empty($semiFinish)?1:'')?>">
        <input type="hidden" name="finish_wt" id="finish_wt" value="<?=!empty($dataRow->finish_wt)?$dataRow->finish_wt:''?>">
        <input type="hidden" name="move_from" id="move_from" value="<?=!empty($move_type)?$move_type:1?>"> 
        <input type="hidden" name="process_from" id="process_from" value="<?=!empty($process_from)?$process_from:0?>"> 
        <div class="col-md-4 form_group">
            <label for="trans_date">Date</label>
            <input type="date" name="trans_date" id="trans_date" class="form-control" value="<?=date("Y-m-d")?>" max="<?=date("Y-m-d")?>">
        </div>
        <div class="col-md-4 form-group" hidden>
            <label for="qty_kg">Qty(kg)</label>
            <input type="text" id="qty_kg" class="form-control floatOnly req calKg2Pc" value="">

        </div>
        <div class="col-md-4 form-group">
            <label for="qty"> Qty(pcs)</label>
            <input type="text" id="qty" name="qty" class="form-control numericOnly req qtyCal" value="">
        </div>
        
        <div class="col-md-4 form-group">
            <label for="next_process_id">Next Process</label>
            <select name="next_process_id" id="next_process_id" class="form-control select2">
                <?php
                if($dataRow->production_type == 1){ ?> <option value="" >Select Next Process</option> <?php } 
                
                if(!empty($processList)){
                    $i =0;
                    $process = explode(",",$this->data['dataRow']->process_ids);
                    $currentProcessKey = array_search($dataRow->process_id,$process);
                    foreach($processList as $row){
                        if($dataRow->production_type == 1)
                        {
                            if(!in_array($row->process_id,[$dataRow->process_id,$dataRow->first_process])){
                            ?> <option value="<?=$row->process_id?>"><?=$row->current_process?></option> <?php
                            }
                        }
                        if($i > $currentProcessKey){
                            if($i == $currentProcessKey+1){
                                ?> <option value="<?=$row->process_id?>"><?=$row->current_process?></option> <?php
                                
                            }
                            
                        }
                        $i++;
                    }
                }
				/*
                //if($dataRow->is_packing == 0){
                    ?><option value="0">Move Ready To Dispatch Area</option><?php
                //}else{
                    ?><option value="0">Move to Packing Area</option><?php
                //}
				*/
                ?>
                <option value="0">Move to Stock</option>
                <option value="-1">Semi-Finished</option>
            </select>
        </div>
        
        <?php
        if(!empty($move_type) && $move_type == 2){
            ?>
            <div class="col-md-3 form-group">
                <label for="move_type">Movement Type</label>
                <select name="move_type" id="move_type" class="form-control select2">
                    <option value="1" <?=(!empty($move_type) && $move_type == 1)?'selected':''?>>Regular</option>
                    <option value="2" <?=(!empty($move_type) && $move_type == 2)?'selected':''?>>Rework</option>
                </select>
            </div>
            <?php
        }else{
            ?> <input type="hidden" name="move_type" id="move_type" value="<?=!empty($move_type)?$move_type:1?>"> <?php
        }
        ?>
        
        <div class="<?=(!empty($move_type) && $move_type == 2)?'col-md-9':'col-md-12'?> form-group remarkDiv">
            <label for="remark">Remark</label>
            <div class="input-group">
                <input type="text" name="remark" id="remark" class="form-control" value="">
                <div class="input-group-append">
                    <?php
                        $param = "{'formId':'addPrcMovement','fnsave':'savePRCMovement','res_function':'getPrcMovementResponse'}";
                    ?>
                    <button type="button" class="btn waves-effect waves-light btn-success btn-save save-form float-right btn-block" onclick="storeSop(<?=$param?>)" style="height:35px"><i class="fa fa-check"></i> Save</button>
                </div>
            </div>
        </div>
    </div>
</form>
<div class="col-md-12">
    <div class="row">
        <h5 style="width:100%;margin:0 auto;vertical-align:middle;border-top:1px solid #ccc;padding:5px 0px;">Process Transaction :</h5>
        <div class="table-responsive  mb-3">
            <table id='movementTransTable' class="table table-bordered jpExcelTable mb-5">
                <thead class="text-center">
                    <tr>
                        <th style="min-width:20px">#</th>
                        <th style="min-width:100px">Date</th>
                        <th>Qty.</th>
                        <th>Next Process</th>
                        <th>Remark</th>
                        <th style="min-width:100px">Created By/At</th>
                        <th style="width:100px;">Action</th>
                    </tr>
                </thead>
                <tbody id="movementTbodyData">
                   
                </tbody>
            </table>
        </div>
    </div>
</div>
<script>
var tbodyData = false;
$(document).ready(function(){
    $('.storeList').hide();
    if(!tbodyData){
        var postData = {'postData':{'prc_id':$("#prc_id").val(),'process_id':$("#process_id").val(),'process_by':$("#process_by").val(),'ref_id':$("#ref_id").val(),'trans_type':$("#move_from").val(),'process_from':$("#process_from").val()},'table_id':"movementTransTable",'tbody_id':'movementTbodyData','tfoot_id':'','fnget':'getPRCMovementHtml'};
        getPRCMovementHtml(postData);
        tbodyData = true;
    }

    $(document).on("change keyup",".qtyCal", function(){
        $(".qty").html("");
        var rej_qty = ($("#rej_found").val() !='')?$("#rej_found").val():0;
        var production_qty = parseFloat($("#qty").val() )|| 0
        var finish_wt = parseFloat($("#finish_wt").val()) || 0;
       var qty_kg = 0;
       if(finish_wt > 0){
            qty_kg = production_qty*finish_wt;
       }
       $("#qty_kg").val(qty_kg);
    });

    $(document).on("keyup",".calKg2Pc", function(){
        $(".qty").html("");
       var qty_kg = $("#qty_kg").val() || 0;
       var finish_wt = parseFloat($("#finish_wt").val()) || 0;
       var pending_qty = parseFloat($("#pending_qty").val()) || 0;
       var qty_pc = 0;
       if(finish_wt > 0){
            qty_pc = parseInt(qty_kg/finish_wt);
       }
       if(qty_pc > pending_qty){
            var conv_ratio  = parseFloat($("#conv_ratio").val()) || 0;
            var ratioQty = pending_qty + ((pending_qty*conv_ratio)/100);
            console.log(conv_ratio+"##"+ratioQty+"<<"+pending_qty);
            if(ratioQty >= qty_pc){
                qty_pc = pending_qty;
            }else{
                $(".qty").html("Invalid Pcs");
                qty_pc = 0;
            }
       }
       $("#qty").val(qty_pc);
    });


    $(document).on('change','#send_to',function(e){
        e.stopImmediatePropagation();e.preventDefault();

        var send_to = $(this).val();
        if(send_to == 4){
            // $(".remarkDiv").removeClass("col-md-12");
            // $(".remarkDiv").addClass("col-md-9");
            $('.storeList').show();
        }else{
            // $(".remarkDiv").removeClass("col-md-9");
            // $(".remarkDiv").addClass("col-md-12");
            $('.storeList').hide();
        }
    });
});
function getPrcMovementResponse(data,formId="addPrcMovement"){ 
    if(data.status==1){
        $('#'+formId)[0].reset();
        var postData = {'postData':{'prc_id':$("#prc_id").val(),'process_id':$("#process_id").val(),'process_by':$("#process_by").val(),'ref_id':$("#ref_id").val(),'trans_type':$("#move_from").val(),'process_from':$("#process_from").val()},'table_id':"movementTransTable",'tbody_id':'movementTbodyData','tfoot_id':'','fnget':'getPRCMovementHtml'};
        $("#send_to").trigger('change');
        getPRCMovementHtml(postData);
        currLoc = $(location).prop('href');
        if (currLoc.indexOf('/sopDesk/productionLog/') > 0) { 
			initTable();
		}
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