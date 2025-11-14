<form data-res_function="prcResponse">
    <div class="card">
        <div class="media align-items-center btn-group process-tags">
            <span class="badge bg-light-peach btn flex-fill" style="padding:5px">CP : <?=!empty($dataRow->current_process)?$dataRow->current_process:'Initial Stage'?></span>
            <span class="badge bg-light-teal btn flex-fill" style="padding:5px">NP : <?=$dataRow->next_process?></span>
            <span class="badge bg-light-cream btn flex-fill" style="padding:5px" id="pending_log_qty">PQ : <?=(!empty($pending_log)?$pending_log:0)?></span>
        </div>                                       
    </div>
    <input type="hidden" id="pending_qty" value="<?=(!empty($pending_log)?$pending_log:0)?>">
    <input type="hidden" name="finish_wt" id="finish_wt" value="<?=$dataRow->finish_wt?>">
    <input type="hidden" name="conv_ratio" id="conv_ratio" value="<?=$dataRow->conv_ratio?>">
    <input type="hidden" name="prc_id" id="prc_id" value="<?=$dataRow->prc_id?>">
    <input type="hidden" name="prc_process_id" id="prc_process_id" value="<?=$dataRow->id?>">
    <input type="hidden" name="process_id" id="process_id" value="<?=$dataRow->current_process_id?>">
    <input type="hidden" name="ref_id" id="ref_id" value="<?=!empty($challan_id)?$challan_id:0?>">
    <input type="hidden" name="ref_trans_id" id="ref_trans_id" value="<?=!empty($ref_trans_id)?$ref_trans_id:0?>">
    <div class="row">
        <div class="col">
            <div class="mb-3">
                <label for="trans_date">Date</label>
                <input type="date" name="trans_date" id="trans_date" class="form-control" value="<?=date("Y-m-d")?>" max="<?=date("Y-m-d")?>">
            </div> 
        </div>
        <div class="col" <?=empty($masterSetting->prod_kgpcs_conversion)?'hidden':''?>>
            <div class="mb-3">
                <label for="qty_kg">Qty(kg)</label>
                <input type="number" id="qty_kg" class="form-control floatOnly req  calKg2Pc" value="">
            </div>
        </div>
    </div>
    <div class="row">
        <div class="col">
            <div class="mb-3">
                <label for="production_qty">Production Qty</label>
                <input type="text" id="production_qty" class="form-control numericOnly req qtyCal" value="">
            </div>
        </div>
        <div class="col">
            <div class="mb-3">
                <label for="ok_qty">Ok Qty</label>
                <input type="text" name="ok_qty" id="ok_qty" class="form-control numericOnly req " value="" readonly>
                <div class="error batch_stock_error"></div>
            </div>
        </div>
    </div>
    <div class="row">
        <div class="col">
            <div class="mb-3">
                <label for="rej_found">Rejection Qty</label>
                <input type="text" name="rej_found" id="rej_found" class="form-control numericOnly qtyCal">
            </div>
        </div>
        <div class="col">
            <div class="mb-3">
                <label for="production_time">Production Time</label>
                <input type="text" name="production_time" id="production_time" class="form-control">
            </div>
        </div>

    </div>
    <div class="row">
        <div class="col">
            <div class="mb-3">
                <label for="process_by">Process By</label>
                <select name="process_by" id="process_by" class="form-control select2">
                    <option value="1">Inhouse Machining</option>
                    <option value="2">Department Process</option>
                </select>
            </div>
        </div>
        <div class="col">
            <div class="mb-3">
                <label for="processor_id">Machine/Dept.</label>
                <select name="processor_id" id="processor_id" class="form-control select2">
                    <option value="0">Select</option>
                </select>
            </div>
        </div>
    </div>
    <div class="row">
        <div class="mb-3">
            <label for="shift_id">Shift</label>
            <select name="shift_id" id="shift_id" class="form-control select2">
                <option value="">Select Shift</option>
                <?php
                if(!empty($shiftData)){
                    foreach ($shiftData as $row) :
                        echo '<option value="' . $row->id . '" >' . $row->shift_name . '</option>';
                    endforeach;
                }
                ?>
            </select>
            <div class="error shift_id"></div>
        </div>
    </div>
    <div class="row">
        <div class="mb-3">
            <label for="operator_id">Operator</label>
            <select name="operator_id" id="operator_id" class="form-control select2">
                <option value="0">Select</option>
                <?php
                if(!empty($operatorList)){
                    foreach($operatorList as $row){
                        ?><option value="<?=$row->id?>"><?=$row->emp_name?></option><?php
                    }
                }
                ?>
            </select>
        </div>
    </div>
    <div class="row">
        <div class="mb-3">
            <label for="remark">Remark</label>
            <input type="text" name="remark" id="remark" class="form-control" value="">
        </div>
    </div>
    
</form>

<script>
var tbodyData = false;
$(document).ready(function(){
    setTimeout(function(){ $('#process_by').trigger('change'); }, 50);

    $(document).on("change input",".qtyCal", function(){
        $(".production_qty").html("");
        var rej_qty = ($("#rej_found").val() !='')?$("#rej_found").val():0;
        var production_qty = parseFloat($("#production_qty").val() )|| 0
		var okQty=production_qty-parseFloat(rej_qty);
		$("#ok_qty").val(okQty);
        var finish_wt = parseFloat($("#finish_wt").val()) || 0;
        var qty_kg = 0;
        if(finish_wt > 0){
            qty_kg = production_qty*finish_wt;
        }
        $("#qty_kg").val(qty_kg);
    });

    $(document).on("input",".calKg2Pc", function(){
        $(".production_qty").html("");
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
            if(ratioQty >= qty_pc){
                qty_pc = pending_qty;
            }else{
                $(".production_qty").html("Invalid Pcs");
                qty_pc = 0;
            }
       }
       $("#production_qty").val(qty_pc);
       $("#ok_qty").val(qty_pc);
    });


    $(document).on('change','#process_by',function(){
		var process_by = $(this).val();
        if(process_by)
        {		
            $.ajax({
                url:base_url  + "sopDesk/getProcessorList",
                type:'post',
                data:{process_by:process_by}, 
                dataType:'json',
                success:function(data){
                    $("#processor_id").html("");
                    $("#processor_id").html(data.options);
                }
            });
        }
    });
});
</script>