<form data-res_function="getStockResponse">
    <div class="card">
                <div class="media align-items-center btn-group process-tags">
                    <span class="badge bg-light-peach btn flex-fill">Currunt Process : <?=!empty($dataRow->current_process)?$dataRow->current_process:'Initial Stage'?></span>
                    <span class="badge bg-light-cream btn flex-fill" id="pending_movement_qty">Pending Qty :  </span>
                </div>                                       
    </div>
    <div class="row">
        <input type="hidden" name="prc_id" id="prc_id" value="<?=$dataRow->prc_id?>">
        <input type="hidden" name="prc_process_id" id="prc_process_id" value="<?=$dataRow->id?>">
        <input type="hidden" name="process_id" id="process_id" value="<?=$dataRow->current_process_id?>">
        <input type="hidden" name="next_process_id" id="next_process_id" value="<?=$dataRow->next_process_id?>">
        <input type="hidden" name="send_to" id="send_to" value="4">
        <input type="hidden" name="processor_id" id="processor_id" value="<?=$this->FIR_STORE->id?>">
        <div class="col-md-6 form_group">
            <label for="trans_date">Date</label>
            <input type="date" name="trans_date" id="trans_date" class="form-control" value="<?=date("Y-m-d")?>" max="<?=date("Y-m-d")?>">
        </div>
        
        <div class="col-md-6 form-group">
            <label for="qty"> Qty</label>
            <input type="text" id="qty" name="qty" class="form-control numericOnly req qtyCal" value="">

        </div>
        <!-- <div class="col-md-6 form-group">
            <label for="processor_id"> Location </label>
            <select name="processor_id" id="processor_id" class="form-control select2">
                <option value="">Select Location</option>
                <?php
                    // foreach ($locationList as $row) {
                    //     echo "<option value='".$row->id."'>".$row->location."</option>";
                    // }
                ?>
            </select>
        </div> -->
        <div class="col-md-12 form-group float-end">
            <?php
                $param = "{'formId':'addPrcMovement','fnsave':'savePRCMovement','res_function':'getStockResponse'}";
            ?>
            <button type="button" class="btn waves-effect waves-light btn-success btn-save save-form float-right" onclick="storeSop(<?=$param?>)" style="height:35px"><i class="fa fa-check"></i> Save</button>
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
                        <th style="width:50px;">Action</th>
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
    if(!tbodyData){
        var postData = {'postData':{'prc_id':$("#prc_id").val(),'prc_process_id':$("#prc_process_id").val(),'process_by':$("#process_by").val(),'ref_id':$("#ref_id").val()},'table_id':"movementTransTable",'tbody_id':'movementTbodyData','tfoot_id':'','fnget':'getPRCStockHtml'};
        getPRCMovementHtml(postData);
        tbodyData = true;
    }

    $(document).on("change keyup",".qtyCal", function(){
        var rej_qty = ($("#rej_qty").val() !='')?$("#rej_qty").val():0;
        
		var okQty=parseFloat($("#production_qty").val())-parseFloat(rej_qty);
      
		$("#ok_qty").val(okQty);
    });
});
function getStockResponse(data,formId="addPrcMovement"){ 
    if(data.status==1){
        $('#'+formId)[0].reset();
        var postData = {'postData':{'prc_id':$("#prc_id").val(),'prc_process_id':$("#prc_process_id").val(),'process_by':$("#process_by").val(),'ref_id':$("#ref_id").val()},'table_id':"movementTransTable",'tbody_id':'movementTbodyData','tfoot_id':'','fnget':'getPRCStockHtml'};
        getPRCMovementHtml(postData);
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