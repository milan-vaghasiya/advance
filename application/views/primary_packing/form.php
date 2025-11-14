<form data-res_function="getPackingResponse">
    <div class="col-md-12">
        <input type="hidden" name="id" id="pack_id" value="<?=(!empty($dataRow->id))?$dataRow->id:""?>">
        <input type="hidden" name="trans_no" class="form-control" value="<?=(!empty($dataRow->trans_no))?$dataRow->trans_no:$trans_no?>" readonly>
        <div class="row">
            <div class="col-md-2 form-group">
                <label for="trans_number">Packing No.</label>
                <div class="input-group">
                    <input type="text" name="trans_number" class="form-control req" value="<?=(!empty($dataRow->trans_number))?$dataRow->trans_number:$trans_number?>" readonly>
                </div>
            </div>
            <div class="col-md-2 form-group">
                <label for="trans_date">Packing Date</label>
                <input type="date" name="trans_date" id="trans_date" class="form-control req" value="<?=(!empty($dataRow->trans_date))?$dataRow->trans_date:date("Y-m-d")?>">
            </div>
            <div class="col-md-2 form-group">
                <label for="box_type">Box Type</label>
                <select name="box_type" id="box_type" class="form-control">
                    <option value="1">Single Batch</option>
                    <option value="2">Multi Batch</option>
                </select>
            </div>
            <div class="col-md-6 form-group ">
                <label for="item_id">Product</label>
                <!-- <div class="float-right"><span id="packing_type" class="text-primary fw-bold"></span></div> -->
                <div class="input-group">
                    <div class="input-group-append" style="width:90%">
                        <select name="item_id" id="item_id" class="form-control select2 req">
                            <option value="">Select Product</option>
                            <?php
                                foreach($productData as $row):
                                    $selected = (!empty($dataRow->item_id) && $dataRow->item_id == $row->id) ? 'selected' : '';
                                    $item_name = (!empty($row->item_code)) ? "[".$row->item_code."] ".$row->item_name : $row->item_name;
                                    echo '<option value="'.$row->id.'" '.$selected.' data-packing_type="'.$row->is_packing.'">'.$item_name.'</option>';
                                endforeach;
                            ?>
                        </select>
                    </div>
                    <div class="input-group-append" style="width:10%">
                        <button type="button" class="btn btn-info float-right loadBach">Load</button>
                    </div>
                </div>
            </div>
           
        </div> 
        <hr>
        <div class="row materialDetails">
            <div class="col-md-6 form-group">
                <div class="error batchDetails"></div>
                <div class="table-responsive">
                    <table id="batchDetail" class="table jpExcelTable">
                        <thead class="thead-info  text-center">
                            <tr>
                                <th colspan="4">Stock Detail</th>
                            </tr>
                            <tr>
                                <th></th>
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
                                        echo '<td colspan="4" class="text-center"> No data available in table </td>';
                                    }
                                ?>
                            </tr>
                        </tbody>
                        <tfoot class="thead-info">
                            <tr>
                                <th colspan="3" class="text-right">Total Qty</th>
                                <th>
                                    <input type="text" name="total_qty" id="total_qty" class="form-control" value="0" readonly>
                                </th>
                            </tr>
                        </tfoot>
                    </table>
                </div>
            </div> 
            <div class="col-md-6 form-group">
                <div class="error packStdDetail"></div>
                <div class="table-responsive">
                    <table id="packingDetail" class="table jpExcelTable">
                        <thead class="thead-info text-center">
                            <tr>
                                <th colspan="5" >Packing Standard</th>
                            </tr>
                            <tr>
                                <th></th>
                                <th>Standard</th>
                                <th>Packing Material</th>
                                <th>Qty/Box</th>
                                <th>Packing Wt.(KGS)</th>
                            </tr>
                        </thead>
                        <tbody id="packStandardTbody">
                            
                        </tbody>
                    </table>
                </div>
            </div> 
            <div class="col-md-4 form-group regular">
                <label for="qty_per_box">Qty Per Box (Nos)</label>
                <input type="text" id="qty_per_box" name="qty_per_box" class="form-control numericOnly req netwt totalQtyNos" value="<?=(!empty($dataRow->qty_per_box))?$dataRow->qty_per_box:""?>">
            </div>
            <div class="col-md-4 form-group">
                <label for="total_box">Total Box (Nos)</label>
                <input type="text" id="total_box" name="total_box" class="form-control numericOnly netwt req totalQtyNos" value="<?=(!empty($dataRow->total_box))?$dataRow->total_box:""?>">
            </div>
            <div class="col-md-4 form-group">
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
        
        $(document).on("click",".loadBach",function(e){
            e.stopImmediatePropagation();e.preventDefault();
            var fg_id = $("#item_id").val();
            $("#packing_type").html("");
            if(fg_id){
                // var packing_type = $("#item_id :selected").data('packing_type'); 
                // if(packing_type == 1){
                //     $("#packing_type").html("This Packing Moved To Ready To Dispatch");
                // }else if(packing_type == 2){
                //     $("#packing_type").html("This Packing Moved To Final Packing Area");
                // }
                var batchDetail = {'postData':{'item_id': fg_id, 'id' : '','location_ids': <?=$this->PACKING_STORE->id?>},'table_id':"batchDetail",'tbody_id':'batchTrans','tfoot_id':'','fnget':'getBatchWiseItemStockForPack'};
                getBatchTransHtml(batchDetail);
                $(".calculateBatchQty").trigger('change');
            }
           
        });

        $(document).on("change","#box_type",function(){
            var box_type = $("#box_type").val();
            $(".batchNoIp").attr("disabled","disabled");
            $(".batchQtyIp").val("");$('#total_qty').val(""); 
            $(".batchNoCheck").prop('checked', false).attr('checked', 'disabled');
            if(box_type == 2){
                $("#total_box").val(1);
                $("#total_box").attr('readonly',true);
            }else{
                $("#total_box").removeAttr('readonly');
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

        $(document).on("click", ".batchNoCheck", function() {
            var box_type = $("#box_type").val();
            var rowid = $(this).data('rowid');
            if(box_type == 1){
                $('.batchNoCheck').not(this).prop('checked', false).attr('checked', 'disabled');
                $(".batchNoIp").attr('disabled', 'disabled');
                $(".batchQtyIp").val("");
                $('#total_qty').val("0"); 
            }
            $(".error").html("");
            if (this.checked) {
                $(".checkRow" + rowid).removeAttr('disabled');
            } else{
                $(".checkRow" + rowid).attr('disabled', 'disabled');
            }
        });

        $(document).on("click", ".standardCheck", function() {
            $('.standardCheck').not(this).prop('checked', false).attr('checked', 'disabled');
        });
    }); 

    function editBatch(batchDetail,pack_standard){
        var item_id = $("#item_id").val();
        var id = $("#id").val();
        var batchDetail = {'postData':{'item_id': item_id, 'id' : id ,'batchDetail': JSON.stringify(batchDetail),'location_ids':<?=$this->PACKING_STORE->id?>,'pack_standard':pack_standard},'table_id':"batchDetail",'tbody_id':'batchTrans','tfoot_id':'','fnget':'getBatchWiseItemStockForPack'};
        getBatchTransHtml(batchDetail);
        setTimeout(function(){ $(".calculateBatchQty").trigger('change'); },500);
    }

    function getPackingResponse(data,formId="addPacking"){
        if(data.status==1){
            Swal.fire({ icon: 'success', title: data.message});
            $('#'+formId)[0].reset();
            $('#batchTrans').html("");
            $('#packStandardTbody').html("");
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

    function getBatchTransHtml(data){
        var postData = data.postData || {};
        var fnget = data.fnget || "";
        var controllerName = data.controller || controller;
        var resFunctionName = data.res_function || "";

        var table_id = data.table_id || "";
        var thead_id = data.thead_id || "";
        var tbody_id = data.tbody_id || "";
        var tfoot_id = data.tfoot_id || "";	

        if(thead_id != ""){
            $("#"+table_id+" #"+thead_id).html(data.thead);
        }
        
        $.ajax({
            url: base_url + controllerName + '/' + fnget,
            data:postData,
            type: "POST",
            dataType:"json",
            beforeSend: function() {
                if(table_id != ""){
                    var columnCount = $('#'+table_id+' thead tr').first().children().length;
                    $("#"+table_id+" #"+tbody_id).html('<tr><td colspan="'+columnCount+'" class="text-center">Loading...</td></tr>');
                }
            },
        }).done(function(res){
            if(resFunctionName != ""){
                window[resFunctionName](response);
            }else{
                $("#"+table_id+" #"+tbody_id).html('');
                $("#"+table_id+" #"+tbody_id).html(res.tbodyData);
                $("#packStandardTbody").html(res.packStandardTbody);
                if(tfoot_id != ""){
                    $("#"+table_id+" #"+tfoot_id).html('');
                    $("#"+table_id+" #"+tfoot_id).html(res.tfootData);
                    
                }
            }
        });
    }

</script>
<?php
if(!empty($dataRow->batchDetail)){
    $bactchdetail = json_decode($dataRow->batchDetail);
    if(count($bactchdetail) > 1){
        echo '<script> setTimeout(function(){  $("#box_type").val("2");   },50);</script>';
    }
    echo '<script> setTimeout(function(){  editBatch('.$dataRow->batchDetail.',"'.$dataRow->pack_standard.'"); },500); </script>';
}
?>