<form data-res_function="getPrcLogResponse">
    <div class="card">
        <div class="media align-items-center btn-group process-tags">
            <span class="badge bg-light-peach btn flex-fill">Currunt Process : <?=!empty($dataRow->current_process)?$dataRow->current_process:'Initial Stage'?></span>
            <span class="badge bg-light-cream btn flex-fill" id="pending_log_qty">Pending Qty :  </span>
        </div>                                       
    </div>
    <div class="row">
        <input type="hidden" id="pending_qty" value="">
        <input type="hidden" name="prc_id" id="prc_id" value="<?=$dataRow->prc_id?>">
        <input type="hidden" name="finish_wt" id="finish_wt" value="<?=$dataRow->finish_wt?>">
        <input type="hidden" name="conv_ratio" id="conv_ratio" value="<?=$dataRow->conv_ratio?>">
        <input type="hidden" name="prc_id" id="prc_id" value="<?=$dataRow->prc_id?>">
        <input type="hidden" name="process_id" id="process_id" value="<?=$dataRow->process_id?>">
        <input type="hidden" name="ref_id" id="ref_id" value="<?=!empty($challan_id)?$challan_id:0?>">
        <input type="hidden" name="ref_trans_id" id="ref_trans_id" value="<?=!empty($ref_trans_id)?$ref_trans_id:0?>">
        <input type="hidden" name="trans_type" id="trans_type" value="<?=!empty($trans_type)?$trans_type:1?>">
        <input type="hidden" name="process_from" id="process_from" value="<?=!empty($process_from)?$process_from:0?>">
        <div class="error die_error"></div>
        <div class="col-md-3 form_group">
            <label for="trans_date">Date</label>
            <input type="date" name="trans_date" id="trans_date" class="form-control" value="<?=date("Y-m-d")?>" max="<?=date("Y-m-d")?>">
        </div>
        <div class="col-md-3 form-group" hidden>
            <label for="qty_kg">Qty(kg)</label>
            <input type="text" id="qty_kg" class="form-control floatOnly req " value="">

        </div>
        <div class="col-md-3 form-group">
            <label for="production_qty">Production Qty(pcs)</label>
            <input type="text" id="production_qty" class="form-control numericOnly req qtyLogCal" value="">

        </div>
        <div class="col-md-3 form-group">
            <label for="ok_qty">Ok Qty</label>
            <input type="text" name="ok_qty" id="ok_qty" class="form-control numericOnly req " value="" readonly>
            <div class="error batch_stock_error"></div>
        </div>
        <div class="col-md-3 form-group" >
            <label for="rej_found">Rejection Qty</label>
            <input type="text" name="rej_found" id="rej_found" class="form-control numericOnly qtyLogCal">
        </div>
       
        <?php
        if(!empty($process_by) && $process_by == 3){
            ?>
            <div class="col-md-3 form-group">
               <label for="without_process_qty">Without Process Return</label>
               <input type="text" name="without_process_qty" id="without_process_qty" class="form-control numericOnly qtyLogCal">
            </div>
            <div class="col-md-3 form-group">
               <label for="in_challan_no">In Challan No</label>
               <input type="text" name="in_challan_no" id="in_challan_no" class="form-control">
            </div>
           <input type="hidden" name="process_by" id="process_by" value="<?=$process_by?>">
           <input type="hidden" name="processor_id" id="processor_id" value="<?=$processor_id?>">
        <?php
        }
        else{
        ?>
             <div class="col-md-3 form-group">
                <label for="production_time">Production Time<small>(In Minutes)</small></label>
                <input type="text" name="production_time" id="production_time" class="form-control">
            </div>
            <div class="col-md-3">
                <label for="process_by">Process By</label>
                <select name="process_by" id="process_by" class="form-control select2">
                    <option value="1">On Machine</option>
                    <option value="2">On Department</option>
                </select>
            </div>
            <div class="col-md-3">
                <label for="processor_id">Machine/Dept.</label>
                <select name="processor_id" id="processor_id" class="form-control select2">
                    <option value="0">Select</option>
                </select>
            </div>
            <div class="col-md-3 form-group">
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
            <div class="col-md-3  form-group">
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
            <?php
        }
        if($dataRow->die_required == 1){
        ?>
            <div class="col-md-4 form-group">
                <label for="die_set_no">Die</label>
                <select id="die_set_no" name="die_set_no" class="form-control select2 ">
                    <option value="">Select Die</option>
                    <?php
                        foreach($dieList as $row):
                            echo "<option value='".$row->set_no."' data-item_id='".$row->fg_id."'>".$row->item_name." [Set No : ".$row->set_no."]</option>";
                        endforeach;   
                    ?>
                </select>
            </div>
            <div class="col-md-3 form-group" hidden>
                <label for="qty_kg">Forging Weight</label>
                <input type="text" name="qty_kg" id="qty_kg" class="form-control floatOnly" value="">
            </div>
        <?php
        }
        ?>
        
        <div class="col-md-12 form-group">
            <label for="remark">Remark</label>
            <div class="input-group">
                <input type="text" name="remark" id="remark" class="form-control" value="">
                <div class="input-group-append">
                    <?php
                        $param = "{'formId':'addPrcLog','fnsave':'savePRCLog','res_function':'getPrcLogResponse','controller':'sopDesk'}";
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
            <table id='logTransTable' class="table table-bordered jpExcelTable mb-5">
                <thead class="text-center">
                    <tr>
                        <th style="min-width:20px">#</th>
                        <th style="min-width:100px">Date</th>
                        <th>Production Time</th>
                        <th>Process From</th>
                        <th>Department/Machine/ Vendor</th>
                        <th>OK Qty.</th>
                        <th>Rejection Qty.</th>
						
                        <?php if(!empty($process_by) && $process_by == 3){ ?>
                            <th>Without Process Return</th>
                            <th>In Challan No</th>
                        <?php }else{ ?>
                            <th>Operator</th>
                            <th>Shift</th>
                        <?php } 
                            if($dataRow->die_required == 1){
								echo '<th>Die Set No.</th>';
                                echo '<th>Forging Weight</th>';
                            }
                        ?>
						
                        <th>Remark</th>
                        <th style="min-width:100px">Created By/At</th> 
                        <th style="width:100px;">Action</th>
                    </tr>
                </thead>
                <tbody id="logTbodyData">
                   
                </tbody>
            </table>
        </div>
    </div>
</div>
<script>
var tbodyData = false;
$(document).ready(function(){
    setTimeout(function(){ $('#process_by').trigger('change'); }, 50);

    if(!tbodyData){
        var postData = {'postData':{'prc_id':$("#prc_id").val(),'process_id':$("#process_id").val(),'process_by':$("#process_by").val(),'ref_id':$("#ref_id").val(),'ref_trans_id':$("#ref_trans_id").val(),'process_from':$("#process_from").val(),'trans_type':$("#trans_type").val()},'table_id':"logTransTable",'tbody_id':'logTbodyData','tfoot_id':'','fnget':'getPRCLogHtml','controller':'sopDesk'};
        getPRCLogHtml(postData);
        tbodyData = true;
    }

    $(document).on("keyup",".qtyLogCal", function(e){
        e.stopImmediatePropagation();e.preventDefault();
        $(".production_qty").html("");
        var rej_qty = $("#rej_found").val()|| 0;
        // var without_process_qty = $("#without_process_qty").val()|| 0;
        var production_qty = parseFloat($("#production_qty").val() )|| 0
		// var okQty=production_qty-(parseFloat(rej_qty) + parseFloat(without_process_qty));
        var okQty=((production_qty > 0)?(production_qty-parseFloat(rej_qty)) : 0);
		$("#ok_qty").val(okQty);
        var finish_wt = parseFloat($("#finish_wt").val()) || 0;
       var qty_kg = 0;
       if(finish_wt > 0){
            qty_kg = production_qty*finish_wt;
       }
       $("#qty_kg").val(qty_kg);
    });

    $(document).on("keyup",".calKg2Pc", function(e){
        e.stopImmediatePropagation();e.preventDefault();
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


    $(document).on('change','#process_by',function(e){
        e.stopImmediatePropagation();e.preventDefault();
		var process_by = $(this).val();
        if(process_by != 3) {		
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
function getPrcLogResponse(data,formId="addPrcLog"){ 
    if(data.status==1){
        $('#'+formId)[0].reset();
        var postData = {'postData':{'prc_id':$("#prc_id").val(),'process_id':$("#process_id").val(),'process_by':$("#process_by").val(),'ref_id':$("#ref_id").val(),'ref_trans_id':$("#ref_trans_id").val(),'process_from':$("#process_from").val(),'trans_type':$("#trans_type").val()},'table_id':"logTransTable",'tbody_id':'logTbodyData','tfoot_id':'','fnget':'getPRCLogHtml','controller':'sopDesk'};
        getPRCLogHtml(postData);
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