<form id="addInspection">
    <div class="col-md-12">
        <div class="row">

            <input type="hidden" name="ref_id" value="<?=(isset($mtData->id) && !empty($mtData->id)) ? $mtData->id : "" ?>">
            <input type="hidden" name="issue_id" value="<?=(isset($dataRow->id) && !empty($dataRow->id) ? $dataRow->id : "") ?>">
            <input type="hidden" name="issue_number" value="<?=(isset($dataRow->issue_number) && !empty($dataRow->issue_number) ? $dataRow->issue_number : "") ?>">
            <input type="hidden" name="batch_no" value="<?=(isset($dataRow->batch_no) && !empty($dataRow->batch_no) ? $dataRow->batch_no : "") ?>">
            <input type="hidden" name="issue_qty" value="<?=(isset($dataRow->issue_qty) && !empty($dataRow->issue_qty) ? abs($dataRow->issue_qty) : "") ?>">
            <input type="hidden" name="return_qty" value="<?=(isset($dataRow->return_qty) && !empty($dataRow->return_qty) ? abs($dataRow->return_qty) : "") ?>">
            <input type="hidden" name="item_id" value="<?=(isset($dataRow->item_id) && !empty($dataRow->item_id) ? $dataRow->item_id : "") ?>">

            <input type="hidden" name="total_qty" value="<?=(isset($mtData->total_qty) && !empty($mtData->total_qty)) ? $mtData->total_qty : "" ?>">

            <div class="col-md-4 form-group">
                <label for="trans_date">Trans Date</label>
                <input type="date" name="trans_date" class="form-control" value="<?= date("Y-m-d")?>" />
            </div>

            <div class="col-md-4 form-group">
                <label for="used_qty">For Reuse Qty</label>
                <input type="number" name="used_qty" class="form-control req" value="<?=isset($mtData->used_qty) ? abs($mtData->used_qty) : ""?>" />
            </div>

            <div class="col-md-4 form-group">
                <label for="fresh_qty">Fresh Qty</label>
                <input type="number" name="fresh_qty" class="form-control req" value="<?=isset($mtData->fresh_qty) ? abs($mtData->fresh_qty) : ""?>" />
            </div>

            <div class="col-md-4 form-group">
                <label for="missed_qty">Missed Qty</label>
                <input type="number" name="missed_qty" class="form-control req" value="<?=isset($mtData->missed_qty) ? abs($mtData->missed_qty) : ""?>" />
            </div>

            <div class="col-md-4 form-group">
                <label for="broken_qty">Broken Qty</label>
                <input type="number" name="broken_qty" class="form-control req" value="<?=isset($mtData->broken_qty) ? abs($mtData->broken_qty) : ""?>" />
            </div>

            <div class="col-md-4 form-group">
                <label for="scrap_qty">Scrap Qty</label>
                <input type="number" name="scrap_qty" class="form-control req" value="<?=isset($mtData->scrap_qty) ? abs($mtData->scrap_qty) : ""?>" />
            </div>

            <?php if(isset($mtData)) { ?>
                <div class="col-md-12 form-group lc">
                    <label for="location_id">Store Location</label>
                    <select id="location_id" name="location_id" class="form-control single-select select2 req">
                        <option value="">Select Location</option>
                        <?php
                            if(!empty($locationData)):
                                foreach($locationData as $value):
                                    echo '<option value="'.$value->location_id.'">'.$value->location.'</option>';
                                endforeach; 
                            endif;
                        ?>
                    </select>
                </div>
                <div class="error location_err"></div>
            <?php } ?>

            <div class="col-md-12 form-group">
                <label for="remark">Remark</label>
                <input type="text" name="remark" class="form-control" value="<?=isset($mtData->remark) ? $mtData->remark : ""?>" />
            </div>

            <div class="error genral_error"></div>

        </div>
    </div>
</form>
<script>
    $(document).ready(function(){
        initModalSelect();
    });
</script>