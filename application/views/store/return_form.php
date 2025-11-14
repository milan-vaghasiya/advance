<form>
    <div class="col-md-12">
        <div class="row">

            <input type="hidden" name="ref_id" value="<?=(isset($mtData->id) && !empty($mtData->id)) ? $mtData->id : "" ?>">
            <input type="hidden" name="issue_id" value="<?=(isset($dataRow->id) && !empty($dataRow->id) ? $dataRow->id : "") ?>">
            <input type="hidden" name="issue_number" value="<?=(isset($dataRow->issue_number) && !empty($dataRow->issue_number) ? $dataRow->issue_number : "") ?>">
            <input type="hidden" name="batch_no" value="<?=(isset($dataRow->batch_no) && !empty($dataRow->batch_no) ? $dataRow->batch_no : "") ?>">
            <input type="hidden" name="heat_no" value="<?=(isset($dataRow->heat_no) && !empty($dataRow->heat_no) ? $dataRow->heat_no : "") ?>">
			<input type="hidden" name="issue_qty" value="<?=(isset($dataRow->issue_qty) && !empty($dataRow->issue_qty) ? abs($dataRow->issue_qty) : "") ?>">
            <input type="hidden" name="return_qty" value="<?=(isset($dataRow->return_qty) && !empty($dataRow->return_qty) ? abs($dataRow->return_qty) : "") ?>">
            <input type="hidden" name="item_id" value="<?=(isset($dataRow->item_id) && !empty($dataRow->item_id) ? $dataRow->item_id : "") ?>">
            <input type="hidden" name="total_qty" value="<?=(isset($mtData->total_qty) && !empty($mtData->total_qty)) ? $mtData->total_qty : "" ?>">

            <div class="col-md-4 form-group">
                <label for="trans_date">Trans Date</label>
                <input type="date" name="trans_date" class="form-control req" value="<?= date("Y-m-d")?>" />
            </div>

            <div class="col-md-4 form-group">
                <label for="usable_qty">For Reuse Qty</label>
                <input type="text" name="usable_qty" class="form-control floatOnly req" value="<?=isset($mtData->usable_qty) ? abs($mtData->usable_qty) : ""?>" />
            </div>

            <div class="col-md-4 form-group">
                <label for="missed_qty">Missed Qty</label>
                <input type="text" name="missed_qty" class="form-control floatOnly req" value="<?=isset($mtData->missed_qty) ? abs($mtData->missed_qty) : ""?>" />
            </div>

            <div class="col-md-4 form-group">
                <label for="broken_qty">Broken Qty</label>
                <input type="text" name="broken_qty" class="form-control floatOnly req" value="<?=isset($mtData->broken_qty) ? abs($mtData->broken_qty) : ""?>" />
            </div>

            <div class="col-md-4 form-group">
                <label for="scrap_qty">Scrap Qty</label>
                <input type="text" name="scrap_qty" class="form-control floatOnly req" value="<?=isset($mtData->scrap_qty) ? abs($mtData->scrap_qty) : ""?>" />
            </div>

            <?php if(isset($mtData)) { ?>
                <div class="col-md-12 form-group">
                    <label for="location_id">Store Location</label>
                    <select id="location_id" name="location_id" class="form-control select2 req">
                        <option value="">Select Location</option>
                        <?=getLocationListOption($locationData)?>
                    </select>
					<div class="error location_id"></div>
                </div>
            <?php } ?>

            <div class="col-md-12 form-group">
                <label for="remark">Remark</label>
                <input type="text" name="remark" class="form-control" value="<?=isset($mtData->remark) ? $mtData->remark : ""?>" />
				<div class="error genral_error"></div>
            </div>
        </div>
    </div>
</form>
<script>
    $(document).ready(function(){
        initModalSelect();
    });
</script>