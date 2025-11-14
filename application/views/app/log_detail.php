<div class="card">
    <div class="media align-items-center btn-group process-tags">
        <span class="badge bg-light-peach btn flex-fill" style="padding:5px">CP : <?=!empty($dataRow->current_process)?$dataRow->current_process:'Initial Stage'?></span>
        <span class="badge bg-light-teal btn flex-fill" style="padding:5px">NP : <?=$dataRow->next_process?></span>
    </div>                                       
</div>
<input type="hidden" name="log_type" id="log_type" value="<?=!empty($log_type)?$log_type:1?>">
<input type="hidden" name="prc_id" id="prc_id" value="<?=$dataRow->prc_id?>">
<input type="hidden" name="prc_process_id" id="prc_process_id" value="<?=$dataRow->id?>">
<input type="hidden" name="process_id" id="process_id" value="<?=$dataRow->current_process_id?>">
<input type="hidden" name="ref_id" id="ref_id" value="<?=!empty($challan_id)?$challan_id:0?>">
<input type="hidden" name="ref_trans_id" id="ref_trans_id" value="<?=!empty($ref_trans_id)?$ref_trans_id:0?>">
<input type="hidden" id="processor_id" name="processor_id" value="<?=!empty($dataRow->machine_id)?$dataRow->machine_id:0?>">
<input type="hidden" name="process_by" id="process_by" value="<?=!empty($process_by)?$process_by:1?>">
<div class="logData">

</div>
<script>
var tbodyData = false;
$(document).ready(function(){
    if(!tbodyData){
        var postData = {'postData':{'prc_id':$("#prc_id").val(),'prc_process_id':$("#prc_process_id").val(),'process_by':$("#process_by").val(),'ref_id':$("#ref_id").val(),'ref_trans_id':$("#ref_trans_id").val()},'table_id':"logTransTable",'div_id':'logData','tfoot_id':'','fnget':'getPRCLogHtml','controller':'app/sop'};
        getPRCMovementHtml(postData);
        tbodyData = true;
    }
});
function getPrcLogResponse(data,formId="addPrcLog"){ 
    if(data.status==1){
        var postData = {'postData':{'prc_id':$("#prc_id").val(),'prc_process_id':$("#prc_process_id").val(),'process_by':$("#process_by").val(),'ref_id':$("#ref_id").val(),'ref_trans_id':$("#ref_trans_id").val()},'div_id':'logData','fnget':'getPRCLogHtml','controller':'sopDesk'};
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