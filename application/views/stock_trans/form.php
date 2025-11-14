<form>
    <div class="col-md-12">
        <div class="error item_error"></div>
        <div class="row">
            <input type="hidden" name="id" id="id" value="">
            <input type="hidden" name="p_or_m" id="p_or_m" value="1">
            <input type="hidden" name="size" id="size" value="0">
            <input type="hidden" name="batch_no" id="batch_no" value="GB">
            <input type="hidden" name="unique_id" id="unique_id" value="0">            

            <div class="col-md-12 form-group">
                <label for="item_id">Item Name</label>
                <select name="item_id" id="item_id" class="form-control select2 itemDetails" data-res_function="resItemDetail">
                    <option value="">Select Item</option>
                    <?=getItemListOption($itemList)?>
                </select>               
            </div>

            <div class="col-md-6 form-group">
                <label for="ref_date">Date</label>
                <input type="date" name="ref_date" id="ref_date" class="form-control" value="<?=getFyDate()?>">
            </div>

            <div class="col-md-6 form-group">
                <label for="qty">Qty</label>
                <input type="text" name="qty" id="qty" class="form-control floatOnly" value="">
            </div>

            <div class="col-md-12 form-group">
                <label for="remark">Remark</label>
                <input type="text" name="remark" id="remark" class="form-control" value="">
            </div>
        </div>
    </div>
</form>
<script>
$(document).ready(function(){
    $(document).on('change','#unique_id',function(){
        if($(this).find(":selected").val() != ""){
            $("#batch_no").val($(this).find(":selected").text());
        }else{
            $("#batch_no").val("");
        }
    });
});
function resItemDetail(response){
    if(response != ""){
        var itemDetail = response.data.itemDetail;
        $("#size").val(itemDetail.packing_standard);
    }else{
        $("#size").val("");
    }
}
</script>