<form data-res_function="getPrcAcceptResponse">
    <div class="row">
        <input type="hidden" name="id" id="id" value="">
        <input type="hidden" name="accepted_process_id" id="accepted_process_id" value="<?=$accepted_process_id?>">
        <input type="hidden" name="prc_id" id="prc_id" value="<?=$prc_id?>">
        <input type="hidden" name="prc_process_id" id="prc_process_id" value="<?=$prev_prc_process_id?>">
        <div class="col-6 form-group" >
            <div class="mb-3">
                <label for="accepted_qty">Accept Qty</label>
                <input type="number" name="accepted_qty" id="accepted_qty" class="form-control numericOnly">
            </div>
        </div>
        <div class="col-6 form-group" >
            <div class="mb-3">
                <label for="short_qty">Short Qty</label>
                <input type="number" name="short_qty" id="short_qty" class="form-control numericOnly">
            </div>
        </div>
        <div class="col-12 float-end mb-3">
            <?php $param = "{'formId':'addPrcAccept','fnsave':'saveAcceptedQty','res_function':'getPrcAcceptResponse','controller':'sopDesk'}";  ?>
            <button type="button" class="btn btn-sm btn-primary save-form float-end" onclick="storeData(<?=$param?>)" style="height:35px"><i class="fa fa-check"></i> Save</button>
        </div>
    </div>
</form>
<div class="col-md-12">
    <div class="row">
        <h5 style="width:100%;margin:0 auto;vertical-align:middle;border-top:1px solid #ccc;padding:5px 0px;">Process Transaction :</h5>
        <div class="table-responsive  mb-3">
            <table id='acceptedTransTable' class="table table-bordered mb-5">
                <thead class="text-center">
                    <tr>
                        <th style="min-width:20px">#</th>
                        <th style="min-width:100px">Date</th>
                        <th>Accepted</th>
                        <th>Short</th>
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
        var postData = {'postData':{'accepted_process_id':$("#accepted_process_id").val(),'prc_id':$("#prc_id").val()},'table_id':"acceptedTransTable",'tbody_id':'acceptedTbodyData','tfoot_id':'','fnget':'getPRCAcceptHtml'};
        getPRCAcceptHtml(postData);
        tbodyData = true;
    }
});
function getPrcAcceptResponse(data,formId="addPrcAccept"){ 
    if(data.status==1){
        $('#'+formId)[0].reset();
        var postData = {'postData':{'accepted_process_id':$("#accepted_process_id").val(),'prc_id':$("#prc_id").val()},'table_id':"acceptedTransTable",'tbody_id':'acceptedTbodyData','tfoot_id':'','fnget':'getPRCAcceptHtml'};
        getPRCAcceptHtml(postData);
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