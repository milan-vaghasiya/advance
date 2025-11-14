<div class="card">
    <div class="media align-items-center btn-group process-tags">
        <span class="badge bg-light-peach btn flex-fill" style="padding:5px">CP : <?=!empty($dataRow->current_process)?$dataRow->current_process:'Initial Stage'?></span>
        <span class="badge bg-light-teal btn flex-fill" style="padding:5px">NP : <?=$dataRow->next_process?></span>
    </div>                                       
</div>

<input type="hidden" name="prc_process_id" id="prc_process_id" value="<?=$dataRow->id?>">
<div class="movementData">

</div>
<script>
var tbodyData = false;
$(document).ready(function(){
    if(!tbodyData){
        var postData = {'postData':{'prc_id':$("#prc_id").val(),'prc_process_id':$("#prc_process_id").val(),'process_by':$("#process_by").val(),'ref_id':$("#ref_id").val(),'ref_trans_id':$("#ref_trans_id").val()},'table_id':"logTransTable",'div_id':'movementData','tfoot_id':'','fnget':'getPRCMovementHtml','controller':'app/sop'};
        getPRCMovementHtml(postData);
        tbodyData = true;
    }
});
function getPrcMovementResponse(data,formId="addPrcLog"){ 
    if(data.status==1){
        var postData = {'postData':{'prc_id':$("#prc_id").val(),'prc_process_id':$("#prc_process_id").val(),'process_by':$("#process_by").val(),'ref_id':$("#ref_id").val(),'ref_trans_id':$("#ref_trans_id").val()},'div_id':'movementData','fnget':'getPRCMovementHtml','controller':'sopDesk'};
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