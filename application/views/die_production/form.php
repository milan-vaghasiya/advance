<form>
    <div class="col-md-12">
        <div class="row">
            <input type="hidden" name="id" value="<?=(!empty($dataRow->id))?$dataRow->id:"" ?>" />
            <div class="col-md-6 form-group">
                <label for="trans_no">Job No</label>
                <input type="text" name="trans_number" id="trans_number" class="form-control req" value="<?=(!empty($dataRow->trans_number)) ? $dataRow->trans_number : $trans_number ?>" readonly />
                <input type="hidden" name="trans_no" value="<?=(!empty($dataRow->trans_no)) ? $dataRow->trans_no : $next_no ?>"  />
            </div>
            <div class="col-md-6 form-group">
                <label for="trans_date">Job Date</label>
                <input type="date" name="trans_date" class="form-control req" max="<?=date('Y-m-d')?>"  value="<?=(!empty($dataRow->trans_date))?$dataRow->trans_date:date('Y-m-d')?>" />
            </div>
            <div class="col-md-12 form-group">
                <label for="fg_item_id">Product</label>
                <select name="fg_item_id" id="fg_item_id" class="form-control select2 req">
                    <option value="">Select</option>
                    <?php
                        foreach ($fgItemList as $row) :
                            $selected = (!empty($dataRow->fg_item_id) && $dataRow->fg_item_id == $row->id) ? "selected" : "";
                            echo '<option value="'. $row->id .'" '.$selected.'>['.$row->item_code.'] '.$row->item_name.'</option>';
                        endforeach;
                    ?>
                </select>
            </div>
            <div class="col-md-12 form-group">
                <label for="remark">Remark</label>
                <textarea name="remark" id="remark" class="form-control"><?=(!empty($dataRow->remark))?$dataRow->remark:""?></textarea>
            </div>
        </div>
        <hr>
        <div class="row">
            <div class="table-responsive">
            <table id="dieProductionTable" class="table table-bordered align-items-center">
                <thead class="thead-info">
                    <tr>
                        <th style="width:5%;">#</th>
                        <th>Die Type</th>
                        <th>Qty</th>
                    </tr>
                </thead>
                <tbody id="productItems">
                    <td colspan='3' class='text-center'>No Data</td>
                </tbody>
            </table>
            <div class="error bomErr"></div>
        </div>
    </div>
</form>

<script>
    $(document).ready(function() {
        $(document).on('change', '#fg_item_id', function() {
            var fg_item_id = $(this).val();
            $.ajax({
                url: base_url + controller + '/getDieKitList',
                data: { fg_item_id: fg_item_id },
                type: "POST",
                dataType: 'json',
                success: function(data) {
                    $("#productItems").html('');
                    $("#productItems").html(data.tbodyData);
                }
            });
        });
    });
</script>