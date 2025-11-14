<form>
    <div class="col-md-12">
        <div class="row">

            <h4 class="fs-15 text-primary border-bottom-sm">Batch Detail : </h4>

            <input type="hidden" name="id" id="id" value="<?=(!empty($dataRow->id) ? $dataRow->id : "")?>">
            <input type="hidden" name="packing_id" id="packing_id" value="<?=(!empty($dataRow->packing_id) ? $dataRow->packing_id : "")?>">
            <input type="hidden" name="item_id" id="item_id" value="<?=(!empty($dataRow->item_id) ? $dataRow->item_id : "")?>">
            <input type="hidden" name="packing_type" id="packing_type" value="<?=(!empty($packing_type) ? $packing_type : "")?>">
            <input type="hidden" name="total_qty" value="<?=(!empty($dataRow->total_qty) ? $dataRow->total_qty : "")?>">
            <input type="hidden" name="trans_number" id="trans_number" value="<?=(!empty($trans_number) ? $trans_number : "")?>">

            <div class="col-md-12 form-group">
                <div class="error batchError"></div>
                <div class="table table-responsive">
                    <table class="table table-bordered">
                        <thead class="thead-info">
                            <?=$theadData?>
                        </thead>
                        <tbody>
                            <?=$tbodyData?>
                        </tbody>
                        <tfoot class="thead-info">
                            <?=$tfootData?>
                        </tfoot>
                    </table>
                </div>
            </div>
            
        </div>
    </div>
</form>
<script>
$(document).ready(function(){
    $(document).on('keyup change','.calculateBoxQty',function(){
        var row_id = $(this).data('srno');
        var box_qty = $(this).val() || 0;
        var stock_qty = $("#batch_stock_"+row_id).val();
        var opt_qty = $("#qty_per_box_"+row_id).val();
        var batch_qty = 0;

        batch_qty = parseFloat((parseFloat(box_qty) * parseFloat(opt_qty))).toFixed(2);
        $("#batch_qty_"+row_id).val(batch_qty); 

        $(".batch_qty_"+row_id).html('');
        if(parseFloat(batch_qty) > parseFloat(stock_qty)){
            $(".batch_qty_"+row_id).html('Invalid Qty.');
            $("#batch_qty_"+row_id).val(0);
            $(this).val("");
        }   
        
        var boxQtyArr = $(".calculateBoxQty").map(function(){return $(this).val();}).get();
        var boxQtySum = 0;
        $.each(boxQtyArr,function(){boxQtySum += parseFloat(this) || 0;});
        $('.total_box').html(boxQtySum); 
        $('#total_box').val(boxQtySum); 

        var batchQtyArr = $(".calculateBatchQty").map(function(){return $(this).val();}).get();
        var batchQtySum = 0;
        $.each(batchQtyArr,function(){batchQtySum += parseFloat(this) || 0;});
        $('.total_qty').html(batchQtySum); 
        $('#total_qty').val(batchQtySum); 
    });

    $(document).on('keyup change','.calculateBatchQty',function(){
        var row_id = $(this).data('srno');
        var batch_qty = $(this).val() || 0;
        var stock_qty = $("#batch_stock_"+row_id).val();
    
        $(".batch_qty_"+row_id).html('');
        if(parseFloat(batch_qty) > parseFloat(stock_qty)){
            $(".batch_qty_"+row_id).html('Invalid Qty.');
            $("#batch_qty_"+row_id).val(0);
            $(this).val("");
        }   
        var batchQtyArr = $(".calculateBatchQty").map(function(){return $(this).val();}).get();
        var batchQtySum = 0;
        $.each(batchQtyArr,function(){batchQtySum += parseFloat(this) || 0;});
        $('#total_qty').val(batchQtySum); 
    });    
});
</script>