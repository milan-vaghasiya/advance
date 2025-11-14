<form data-res_function="prcResponse">
    <div class="card">
        <div class="media align-items-center btn-group process-tags" style=" word-wrap: break-word !important;">
            <span class="badge bg-light-peach btn flex-fill" style="padding:5px;">CP : <?=!empty($dataRow->current_process)?$dataRow->current_process:'Initial Stage'?></span>
            <span class="badge bg-light-teal btn  flex-fill" style="padding:5px; ">NP : <?=$dataRow->next_process?></span>
            <span class="badge bg-light-cream btn flex-fill" style="padding:5px;" id="pending_movement_qty">PQ : <?=(!empty($pending_movement)?$pending_movement:0)?></span>
        </div>                                       
    </div>
    <input type="hidden" id="pending_qty" value="<?=(!empty($pending_movement)?$pending_movement:0)?>">
    <input type="hidden" name="finish_wt" id="finish_wt" value="<?=$dataRow->finish_wt?>">
    <input type="hidden" name="conv_ratio" id="conv_ratio" value="<?=$dataRow->conv_ratio?>">
    <input type="hidden" name="prc_id" id="prc_id" value="<?=$dataRow->prc_id?>">
    <input type="hidden" name="prc_process_id" id="prc_process_id" value="<?=$dataRow->id?>">
    <input type="hidden" name="process_id" id="process_id" value="<?=$dataRow->current_process_id?>">
    <input type="hidden" name="next_process_id" id="next_process_id" value="<?=$dataRow->next_process_id?>">
    <div class="row">
        <div class="col">
            <div class="mb-3">
                <label class="form-label" for="trans_date">Date</label>
                <input type="date" name="trans_date" id="trans_date" class="form-control" value="<?=date("Y-m-d")?>" max="<?=date("Y-m-d")?>">
            </div>
        </div>
        
    </div>
    <div class="row">
        <div class="col" <?=empty($masterSetting->prod_kgpcs_conversion)?'hidden':''?>>
            <div class="mb-3"  >
                <label for="qty_kg">Qty(kg)</label>
                <input type="number" id="qty_kg" class="form-control floatOnly req calKg2Pc" value="">
            </div>
        </div>
        <div class="col">
            <div class="mb-3">
                <label class="form-label" for="qty"> Qty</label>
                <input type="number" id="qty" name="qty" class="form-control numericOnly req qtyCal" value="">
            </div>
        </div>
       
    </div>
    <div class="row">
        <div class="mb-3">
            <div class="col">
                <label class="form-label" for="send_to">Send To</label>
                <select name="send_to" id="send_to" class="form-control select2">
                    <option value="1">Move To Next</option>
                    <option value="4">Store</option>
                </select>
            </div>
        </div>
    </div>
    <div class="row">
        <div class="mb-3">
            <div class="col storeList">
                <label class="form-label" for="processor_id">Location</label>
                <select name="processor_id" id="processor_id" class="form-control select2">
                    <option value="0">Select</option>
                    <?=getLocationListOption($locationList)?>
                </select>
            </div>
        </div>
    </div>
    
    <div class="row">
        <div class="mb-3">
            <div class="col form-group remarkDiv">
                <label class="form-label" for="remark">Remark</label>
                <input type="text" name="remark" id="remark" class="form-control" value="">
            </div>
        </div>
    </div>
 
</form>
<script>
var tbodyData = false;
$(document).ready(function(){
    $('.storeList').hide();

    $(document).on('change','#send_to',function(){
        var send_to = $(this).val();
        if(send_to == 4){
            $(".remarkDiv").removeClass("col-md-12");
            $(".remarkDiv").addClass("col-md-9");
            $('.storeList').show();
        }else{
            $(".remarkDiv").removeClass("col-md-9");
            $(".remarkDiv").addClass("col-md-12");
            $('.storeList').hide();
        }
    });

    $(document).on("change input",".qtyCal", function(){
        $(".qty").html("");
        var production_qty = parseFloat($("#qty").val() )|| 0
        var finish_wt = parseFloat($("#finish_wt").val()) || 0;
       var qty_kg = 0;
       if(finish_wt > 0){
            qty_kg = production_qty*finish_wt;
       }
       $("#qty_kg").val(qty_kg);
    });

    $(document).on("input",".calKg2Pc", function(){
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
});

</script>