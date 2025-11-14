<form data-res_function="getChallanRequestResponse">
    <div class="row">
        <input type="hidden" name="id" id="id" value="">
        <input type="hidden" name="prc_id" id="prc_id" value="<?=$dataRow->prc_id?>">
        <input type="hidden" name="prc_process_id" id="prc_process_id" value="<?=$dataRow->id?>">
        <input type="hidden" name="process_id" id="process_id" value="<?=$dataRow->current_process_id?>">
        <div class="col-6 form-group" >
            <div class="mb-3">
                <label for="trans_date">Request Date</label>
                <input type="text" name="trans_date" id="trans_date" class="form-control" value="<?=date("Y-m-d")?>">
            </div>
        </div>
        <div class="col-6 form-group" >
            <div class="mb-3">
                <label for="qty">Request Qty</label>
                <input type="text" name="qty" id="qty" class="form-control numericOnly">
            </div>
        </div>
        <div class="mb-3 form-group float-end">
            <?php $param = "{'formId':'addChallanRequest','fnsave':'saveChallanRequest','res_function':'getChallanRequestResponse','controller':'sopDesk'}";  ?>
            <button type="button" class="btn btn-sm btn-primary btn-save save-form float-right" onclick="storeData(<?=$param?>)" style="height:35px"><i class="fa fa-check"></i> Save</button>
        </div>
    </div>
</form>

<div class="row">
    <h5 style="width:100%;margin:0 auto;vertical-align:middle;border-top:1px solid #ccc;padding:5px 0px;">Process Transaction :</h5>
    <div class="table-responsive  mb-3">
        <table id='requestTable' class="table table-bordered mb-5">
            <thead class="text-center">
                <tr>
                    <th style="min-width:20px">#</th>
                    <th style="min-width:100px">Date</th>
                    <th>Request Qty</th>
                    <th style="width:50px;">Action</th>
                </tr>
            </thead>
            <tbody id="requestTabodyData">
                
            </tbody>
        </table>
    </div>
</div>

<script>
var tbodyData = false;
$(document).ready(function(){
    if(!tbodyData){
        var postData = {'postData':{'prc_process_id':$("#prc_process_id").val(),'prc_id':$("#prc_id").val()},'table_id':"requestTable",'tbody_id':'requestTabodyData','tfoot_id':'','fnget':'getChallanRequestHtml'};
        getPRCAcceptHtml(postData);
        tbodyData = true;
    }
});
function getChallanRequestResponse(data,formId="addChallanRequest"){ 
    if(data.status==1){
        $('#'+formId)[0].reset();
        var postData = {'postData':{'prc_process_id':$("#prc_process_id").val(),'prc_id':$("#prc_id").val()},'table_id':"requestTable",'tbody_id':'requestTabodyData','tfoot_id':'','fnget':'getChallanRequestHtml'};
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