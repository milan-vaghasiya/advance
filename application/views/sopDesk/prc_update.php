<div class="col-md-12">
    <form id="updatePrcQty" data-res_function="updatePrcQtyHtml">
        <div class="row">
            <div class="col-md-3 form-group">
                <label for="qty">Add/Reduce</label>
                <select name="log_type" id="log_type" class="form-control select2" style="mix-width:10%;">
                    <option value="1">(+) Add</option>
                    <option value="-1">(-) Reduce</option>
                </select>
                <input type="hidden" name="id" id="id" value="" />
                <input type="hidden" name="prc_id" id="prc_id" value="<?=$prc_id?>" />
                <input type="hidden" name="log_date" id="log_date" value="<?=date("Y-m-d")?>" />        
            </div>
            <div class="col-md-6 form-group">
                <label for="qty">Quantity</label>
                <input type="text" id="qty" name="qty" class="form-control numericOnly req" />
            </div>
            <div class="col-md-3 form-group">
                    <?php
                    $param = "{'formId':'updatePrcQty','fnsave':'savePrcQty','controller':'sopDesk','res_function':'updatePrcQtyHtml'}";
                ?>
                <button type="button" class="btn btn-block waves-effect waves-light btn-outline-success btn-save save-form float-right mt-20" onclick="customStore(<?=$param?>)" style="height:35px"><i class="fa fa-check"></i> Save</button>
            </div>
        </div>
    </form>
    
    <hr>
    <div class="table-responsive">
        <table id="prcLogTable" class="table table-bordered align-items-center">
            <thead class="thead-info">
                <tr>
                    <th style="width:5%;">#</th>
                    <th>Date</th>
                    <th>Type</th>
                    <th>Qty</th>
                    <th class="text-center" style="width:10%;">Action</th>
                </tr>
            </thead>
            <tbody id="prclogData">
               
            </tbody>
        </table>
    </div>
</div>


<script>

var tbodyData = false;
$(document).ready(function(){
    if(!tbodyData){
        var postData = {'postData':{'prc_id':$("#prc_id").val()},'table_id':"prcLogTable",'tbody_id':'prclogData','tfoot_id':'','fnget':'getUpdatePrcQtyHtml','controller':'sopDesk'};
        getTransHtml(postData);
        tbodyData = true;
    }
});
function updatePrcQtyHtml(data,formId="updatePrcQty"){ 
    if(data.status==1){
        $('#'+formId)[0].reset();

        var postData = {'postData':{'prc_id':$("#prc_id").val()},'table_id':"prcLogTable",'tbody_id':'prclogData','tfoot_id':'','fnget':'getUpdatePrcQtyHtml','controller':'sopDesk'};
        getTransHtml(postData);
        initTable();
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