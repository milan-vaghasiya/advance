<form data-res_function="getSparPartRequestHtml" >
    <div class="col-md-12">
        <div class="row">
            <input type="hidden" name="id" id="id" value="">
            <input type="hidden" name="ref_id" id="ref_id" value="<?=$ref_id?>" />
            <input type="hidden" name="req_type" id="req_type" value="2" />
            
            <input type="hidden" name="trans_no" id="trans_no" value="<?=$trans_no?>">
            <input type="hidden" name="trans_number" id="trans_number" value="<?=$trans_number?>">
            <input type="hidden" name="trans_date" id="trans_date" value="<?= date("Y-m-d") ?>">

            <div class="col-md-6 form-group">
                <label for="item_id">Items</label>
                <select name="item_id" id="item_id" class="form-control select2 req">
                <option value="">Select item</option>
                    <?php 
                        foreach ($itemList as $row) :
                            echo '<option value="' . $row->id . '" >' . $row->item_name . '</option>';
                        endforeach;   
                    ?>
                </select>
            </div>

            <div class="col-md-6">
                <label for="req_qty">Qty</label>
                <div class="input-group">
                    <input type="text" id="req_qty" name="req_qty" class="form-control floatOnly req" value="" min="0" />
                    <div class="input-group-append">
                        <?php
                            $param = "{'formId':'addSparPartRequest','fnsave':'saveSparPartRequest','controller':'machineBreakdown','res_function':'getSparPartRequestHtml'}";
                        ?>
                        <button type="button" class="btn waves-effect waves-light btn-outline-success btn-save save-form float-right" onclick="customStore(<?=$param?>)" style="height:36px"><i class="fa fa-check"></i> Save</button>
                    </div>
                </div>                
            </div>

        </div>
        <hr>
        <div class="row">
            <div class="table-responsive">
            <table id="reqTbl" class="table table-bordered align-items-center">
                <thead class="thead-info">
                    <tr class="text-center">
                        <th style="width:5%;">#</th>
                        <th style="width:15%;">Req. Date</th>
                        <th style="width:15%;">Req. No.</th>
                        <th class="text-left">Item Name</th>
                        <th>Qty</th>
                        <th class="text-center" style="width:10%;">Action</th>
                    </tr>
                </thead>
                <tbody id="requestData">
                </tbody>
            </table>
        </div>
        </div>
    </div>
</form>
<script>
var tbodyData = false;
$(document).ready(function(){
    setPlaceHolder();
    if(!tbodyData){
        var postData = {'postData':{'ref_id':$("#ref_id").val()},'table_id':"reqTbl",'tbody_id':'requestData','tfoot_id':'','fnget':'getSparPartRequestHtml'};
        getTransHtml(postData);
        tbodyData = true;
    }
});



function getSparPartRequestHtml(data,formId="addSparPartRequest"){ 
    if(data.status==1){
        $('#'+formId)[0].reset();initSelect2();
        var postData = {'postData':{'ref_id':$("#ref_id").val()},'table_id':"reqTbl",'tbody_id':'requestData','tfoot_id':'','fnget':'getSparPartRequestHtml'};
        getTransHtml(postData);
    }else{
        if(typeof data.message === "object"){
            $(".error").html("");
            $.each( data.message, function( key, value ) {$("."+key).html(value);});
        }else{
            initTable();
            Swal.fire({ icon: 'error', title: data.message });	
        }
    }   
}
</script>