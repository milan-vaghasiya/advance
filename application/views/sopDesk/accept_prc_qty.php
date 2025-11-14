<form data-res_function="getPrcAcceptResponse">
    <div class="row">
        <input type="hidden" name="id" id="id" value="">
        <input type="hidden" name="accepted_process_id" id="accepted_process_id" value="<?=$accepted_process_id?>">
        <input type="hidden" name="prc_id" id="prc_id" value="<?=$prc_id?>">
        <input type="hidden" name="trans_type" id="trans_type" value="<?=!empty($trans_type)?$trans_type:1?>">
        <input type="hidden" name="process_from" id="process_from" value="<?=!empty($process_from)?$process_from:0?>">
        <div class="col-md-12 form-group">
            <label for="accepted_qty">Accept Qty</label>
            <div class="input-group">
            <input type="text" name="accepted_qty" id="accepted_qty" class="form-control numericOnly">
                <div class="input-group-append">
                    <?php $param = "{'formId':'addPrcAccept','fnsave':'saveAcceptedQty','res_function':'getPrcAcceptResponse'}";  ?>
                    <button type="button" class="btn waves-effect waves-light btn-success btn-save save-form float-right" onclick="storeSop(<?=$param?>)" style="height:35px"><i class="fa fa-check"></i> Save</button>
                </div>
            </div>
        </div>
        <div class="col-md-6 form-group" hidden>
            <label for="short_qty">Short Qty</label>
            <input type="text" name="short_qty" id="short_qty" class="form-control numericOnly">
        </div>
        
    </div>
</form>
<div class="col-md-12">
    <div class="row">
        <h5 style="width:100%;margin:0 auto;vertical-align:middle;border-top:1px solid #ccc;padding:5px 0px;">Process Transaction :</h5>
        <div class="table-responsive  mb-3">
            <table id='acceptedTransTable' class="table table-bordered jpExcelTable mb-5">
                <thead class="text-center">
                    <tr>
                        <th style="min-width:20px">#</th>
                        <th style="min-width:100px">Date</th>
                        <th style="min-width:100px">Process From</th>
                        <th>Accepted Qty</th>
                        <th hidden>Short Qty</th>
                        <th style="width:50px;">Action</th>
                    </tr>
                </thead>
                <tbody id="acceptedTbodyData">
                   
                </tbody>
            </table>
        </div>
    </div>
</div>
<script>
var tbodyData = false;
$(document).ready(function(){
    if(!tbodyData){
        var postData = {'postData':{'accepted_process_id':$("#accepted_process_id").val(),'prc_id':$("#prc_id").val(),'process_from':$("#process_from").val(),'trans_type':$("#trans_type").val()},'table_id':"acceptedTransTable",'tbody_id':'acceptedTbodyData','tfoot_id':'','fnget':'getPRCAcceptHtml'};
        getPRCAcceptHtml(postData);
        tbodyData = true;
    }
});
function getPrcAcceptResponse(data,formId="addPrcAccept"){ 
    if(data.status==1){
        $('#'+formId)[0].reset();
        var postData = {'postData':{'accepted_process_id':$("#accepted_process_id").val(),'prc_id':$("#prc_id").val(),'process_from':$("#process_from").val(),'trans_type':$("#trans_type").val()},'table_id':"acceptedTransTable",'tbody_id':'acceptedTbodyData','tfoot_id':'','fnget':'getPRCAcceptHtml'};
        getPRCAcceptHtml(postData);
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