<form>
    <div class="col-md-12">
        <div class="row">

            <input type="hidden" name="log_id" value="<?=(isset($dataRow->id) && !empty($dataRow->id) ? $dataRow->id : "") ?>">
            <input type="hidden" name="is_return" value="<?=(isset($dataRow->is_return) && !empty($dataRow->is_return) ? $dataRow->is_return : "") ?>">
            <?php 
            if(!empty($itemData)) 
            {
                ?>
                <div class="row">
                    <div class="col-md-3 form-group">
                        <label for="challan_no">Issue No.</label>
                        <div class="input-group">
                            <input type="text" name="log_number" class="form-control" value="<?=(isset($dataRow->issue_prefix) && !empty($dataRow->issue_no) ? $dataRow->issue_no : $issue_prefix) ?>" readOnly />
                            <input type="hidden" name="log_no" value="<?=$issue_no?>" readOnly />
                        </div>
                    </div>
                    <div class="col-md-4 form-group">
                        <label for="item_id">Items</label>
                        <select name="item_id" id="item_id" class="form-control select2 req">
                            <option value="">Select Item</option>
                            <?php
                                if(isset($itemData) && !empty(isset($itemData))){
                                    foreach ($itemData as $value) {
                                        $selected = "";
                                        echo "<option value='".$value->id."' ".$selected.">".$value->item_name."</option>";
                                    }
                                }
                            ?>
                        </select>
                        <div class="error item_err"></div>
                    </div>
                </div>
                <?php 
            } else {  ?>
                <div class="row">
                    <div class="col-md-4">
                        <input type="hidden" name="log_number" value="<?=(isset($dataRow->log_number) && !empty($dataRow->log_number) ? $dataRow->log_number : "") ?>">
                        <?= "<b>Req No. : </b>".$dataRow->log_number; ?>
                    </div>
    
                    <div class="col-md-4">
                        <input type="hidden" name="item_id" value="<?=(isset($dataRow->item_id) && !empty($dataRow->item_id) ? $dataRow->item_id : "") ?>">
                        <?= "<b>Item Name : </b>".$dataRow->item_name; ?>
                    </div>
    
                    <div class="col-md-4">
                        <input type="hidden" name="req_qty" value="<?=(isset($dataRow->req_qty) && !empty($dataRow->req_qty) ? ($dataRow->req_qty - $dataRow->issue_qty) : "") ?>">
                        <?= "<b>Qty : </b>".($dataRow->req_qty - $dataRow->issue_qty); ?>
                    </div>
                </div>
        <?php }   ?>

            <div class="col-md-12 form-group mt-4">
                <div class="error general_batch_no"></div>
                <div class="table-responsive ">
                    <table class="table table-striped table-borderless">
                        <thead class="thead-info">
                            <th>Location</th>
                            <th>Batch No</th>
                            <th>Stock</th>
                            <th>Type</th>
                            <th>Qty</th>
                        </thead>
                        <tbody id="tbodyData">
                            <?php 
                            if(isset($batchData) && !empty($batchData)){
                                $i = 1;
                                foreach ($batchData as $value) {
                                    echo "<tr>";
                                    echo '<td>'.$value->location.'</td>';
                                    echo '<td>'.$value->batch_no.'</td>';
                                    echo '<td>'.floatVal($value->qty).'</td>';
                                    echo '<td>'.$value->stock_type.'</td>';
                                    echo '<td>
                                            <input type="text" name="batch_qty[]" class="form-control batchQty floatOnly" min="0" value="" />
                                            <div class="error batch_qty_' . $i . '"></div>
                                            <input type="hidden" name="batch_no[]" id="batch_number_' . $i . '" value="' . $value->batch_no . '" />
                                            <input type="hidden" name="location_id[]" id="location_' . $i . '" value="' . $value->location_id . '" />
                                            <input type="hidden" name="stock_type[]" id="stock_type_' . $i . '" value="' . $value->stock_type . '" />
                                        </td>';
                                    echo "</tr>";
                                    $i++;
                                }
                            }
                            ?>
                        </tbody>
                    </table>
                    <div class="error table_err"></div>
                </div>
            </div>

        </div>
    </div>
</form>
<script>
    $(document).ready(function(){
        initModalSelect();

        $(document).on('change', '#item_id', function () {
            var item_id = $(this).val();
            $.ajax({
                url:base_url + controller + "/getBatchWiseStock",
                type:'post',
                data:{item_id:item_id},
                dataType:'json',
                success:function(data){
                    if(data.status == 1){
                        $('#tbodyData').html('');
                        $('#tbodyData').html(data.tbodyData);
                        $('#is_return').val(data.is_return)
                    }
                }
            });
        });
    });
</script>