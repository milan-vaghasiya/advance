<form data-res_function="getPackingResponse">
    <div class="col-md-12">
        <input type="hidden" name="id" id="pack_id" value="<?=(!empty($dataRow->id))?$dataRow->id:""?>">
        <input type="hidden" name="trans_no" class="form-control" value="<?=(!empty($dataRow->trans_no))?$dataRow->trans_no:$trans_no?>" readonly>
        <div class="row">
            <div class="col-md-3 form-group">
                <label for="trans_number">Packing No.</label>
                <div class="input-group">
                    <input type="text" name="trans_number" class="form-control req" value="<?=(!empty($dataRow->trans_number))?$dataRow->trans_number:$trans_number?>" readonly>
                </div>
            </div>
            <div class="col-md-3 form-group">
                <label for="trans_date">Packing Date</label>
                <input type="date" name="trans_date" id="trans_date" class="form-control req" value="<?=(!empty($dataRow->trans_date))?$dataRow->trans_date:date("Y-m-d")?>">
            </div>
            <div class="col-md-6 form-group">
                <label for="item_id">Product</label>
                <select name="item_id" id="item_id" class="form-control select2 req">
                    <option value="">Select Product</option>
                    <?php
                        foreach($productData as $row):
                            $selected = (!empty($dataRow->item_id) && $dataRow->item_id == $row->id) ? 'selected' : '';
                            $item_name = (!empty($row->item_code)) ? "[".$row->item_code."] ".$row->item_name : $row->item_name;
                            echo '<option value="'.$row->id.'" '.$selected.'>'.$item_name.'</option>';
                        endforeach;
                    ?>
                </select>
            </div>
        </div> 
        <hr>
        <div class="row materialDetails">
            <div class="col-md-12 form-group">
                <div class="error batchDetails"></div>
                <div class="table-responsive">
                    <table id="batchDetail" class="table table-bordered">
                        <thead class="thead-info">
                            <tr>
                                <th></th>
                                <th>Location</th>
                                <th>Batch No.</th>
                                <th>Current Stock</th>
                                <th style="width:180px;">Packing Qty.</th>
                            </tr>
                        </thead>
                        <tbody id="batchTrans">
                            <tr id="batchNoData">
                                <?php 
                                    if(!empty($productBatchTableData)){
                                        echo $productBatchTableData;
                                    } else {
                                        echo '<td colspan="5" class="text-center"> No data available in table </td>';
                                    }
                                ?>
                            </tr>
                        </tbody>
                        <tfoot class="thead-info">
                            <tr>
                                <th colspan="4" class="text-right">Total Qty</th>
                                <th>
                                    <input type="text" name="total_qty" id="total_qty" class="form-control" value="0" readonly>
                                </th>
                            </tr>
                        </tfoot>
                    </table>
                </div>
            </div>
			<div class="col-md-4 form-group">
                <label for="box_item_id">Packing Material</label>
                <select id="box_item_id" name="box_item_id" class="form-control select2 req">
                    <option value="">Loose Packing</option>                    
                    <?=(!empty($packingMaterial) ? $packingMaterial : '')?>
                </select>                
                <input type="hidden" name="pack_wt" id="pack_wt" value="<?=(!empty($dataRow->pack_wt) ? $dataRow->pack_wt : 0)?>">
            </div>    
            <div class="col-md-3 form-group regular">
                <label for="qty_per_box">Qty Per Box (Nos)</label>
                <input type="text" id="qty_per_box" name="qty_per_box" class="form-control numericOnly req netwt totalQtyNos" value="<?=(!empty($dataRow->qty_per_box))?$dataRow->qty_per_box:""?>">
            </div>
            <div class="col-md-3 form-group">
                <label for="total_box">Total Box (Nos)</label>
                <input type="text" id="total_box" name="total_box" class="form-control numericOnly netwt req totalQtyNos" value="<?=(!empty($dataRow->total_box))?$dataRow->total_box:""?>">
            </div>
            <div class="col-md-2 form-group">
                <label for="total_box_qty">Total Qty (Nos)</label>
                <input type="text" id="total_box_qty" name="total_box_qty" class="form-control numericOnly req" value="<?=(!empty($dataRow->total_qty))?$dataRow->total_qty:""?>" readonly>
            </div>      
            <div class="col-md-12 form-group">
                <label for="remark">Remark</label>
                <input type="text" id="remark" name="remark" class="form-control" value="<?=(!empty($dataRow->remark))?$dataRow->remark:""?>">
            </div>
        </div>
    </div>                          
</form>

<script>
    $(document).ready(function(){
        setTimeout(function(){ $('.calculateBatchQty').trigger('change'); }, 5);
        
        $(document).on("change","#item_id",function(){
            var fg_id = $(this).val();
            var batchDetail = {'postData':{'item_id': fg_id, 'id' : '','location_ids': <?=$this->PACKING_STORE->id?>},'table_id':"batchDetail",'tbody_id':'batchTrans','tfoot_id':'','fnget':'getBatchWiseItemStockForPack'};
		    getTransHtml(batchDetail);
            $(".calculateBatchQty").trigger('change');
			
			if(fg_id){
                $.ajax({
                    url: base_url + controller + '/getPackingMaterial',
                    type:'post',
                    data:{ item_id:fg_id },
                    dataType:'json',
                    success:function(data){
                        $("#box_item_id").html("");
                        $("#box_item_id").html(data.options);
                    }
                });
            }
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

        $(document).on('keyup change','.totalQtyNos',function(){
            var qty_per_box = $("#qty_per_box").val();
            var total_box = $("#total_box").val();
            
            qty_per_box = (qty_per_box != 0 || qty_per_box != "")?qty_per_box:0;
            total_box = (total_box != 0 || total_box != "")?total_box:0;
            
            var total_qty = parseFloat((parseFloat(qty_per_box) * parseFloat(total_box))).toFixed(3);
            $("#total_box_qty").val(total_qty);
        });

		$(document).on("change","#box_item_id",function(){
            var qty = $("#box_item_id :selected").data('qty'); 
            var pack_wt = $("#box_item_id :selected").data('pack_wt'); 
            $('#qty_per_box').val(qty);
            $('#pack_wt').val(pack_wt);
        });

        $(document).on("click", ".batchNoCheck", function() {
            $('.batchNoCheck').not(this).prop('checked', false).attr('checked', 'disabled');
            $(".batchNoIp").attr('disabled', 'disabled');
            $(".batchQtyIp").val("");
            var rowid = $(this).data('rowid');
            $('#total_qty').val("0"); 
            $(".error").html("");
            if (this.checked) {
                $(".checkRow" + rowid).removeAttr('disabled');
            } 
        });
    }); 

    function editBatch(batchDetail){
        var item_id = $("#item_id").val();
        var id = $("#id").val();
        var batchDetail = {'postData':{'item_id': item_id, 'id' : id ,'batchDetail': JSON.stringify(batchDetail),'location_ids':<?=$this->PACKING_STORE->id?>},'table_id':"batchDetail",'tbody_id':'batchTrans','tfoot_id':'','fnget':'getBatchWiseItemStockForPack'};
        getTransHtml(batchDetail);
        setTimeout(function(){ $(".calculateBatchQty").trigger('change'); },500);
    }

    function getPackingResponse(data,formId="addPacking"){
        if(data.status==1){
            $('#'+formId)[0].reset();
            $('#batchTrans').html("");

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
<?php
if(!empty($dataRow->batchDetail)){
    echo '<script> setTimeout(function(){  editBatch('.$dataRow->batchDetail.'); },500); </script>';
}
?>