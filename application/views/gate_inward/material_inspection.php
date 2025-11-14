<form>
    <div class="col-md-12">
        <div class="row">
            <div class="table-responsive">
                <table class="table table-bordered">
					<tr>
						<th style="width:25%">Item Name</th>
						<td> <?= (!empty($dataRow->item_code)?$dataRow->item_code:"").$dataRow->item_name.(!empty($dataRow->material_grade) ? ' '.$dataRow->material_grade : '') ?></td>
					</tr>
					<tr>
						<th>Ref./Heat No.</th>
						<td ><?= $dataRow->heat_no ?></td>
					</tr>
					<?php if(!empty($dataRow->is_inspection)): ?>
					<tr>
						<th>Batch No.</th>
						<td ><?= $dataRow->batch_no ?></td>
					</tr>
					<?php endif; ?>
					<tr>
						<th>Grn Qty</th>
						<td > <?= floatVal($dataRow->qty).' ('.$dataRow->uom.')' ?></td>
					</tr>
                </table>
                <hr>
                <div class="row">
                    <input type="hidden" name="id" id="id" value="<?= (!empty($dataRow->id)) ? $dataRow->id : '' ?>" />
                    <input type="hidden" name="grn_id" id="grn_id" value="<?= (!empty($dataRow->grn_id)) ? $dataRow->grn_id : '' ?>" />
                    <input type="hidden" name="grade_id" id="id" value="<?= (!empty($dataRow->grade_id)) ? $dataRow->grade_id : '' ?>" />
                    <input type="hidden" name="fg_item_id" id="fg_item_id" value="<?= (!empty($dataRow->fg_item_id)) ? $dataRow->fg_item_id : '' ?>" />
					<input type="hidden" name="is_inspection" id="is_inspection" value="<?= (!empty($dataRow->is_inspection)) ? $dataRow->is_inspection : 0 ?>" />
                    
					<?php if(!empty($dataRow->item_type) && $dataRow->item_type == 9): ?>
                        <input type="hidden" name="batch_no" id="batch_no" value="General Batch" />
                        <input type="hidden" name="location_id" id="location_id" value="<?=($this->PACKING_STORE->id)?>" />
                    <?php else: ?>
                        <input type="hidden" name="batch_no" id="batch_no" value="<?= (!empty($dataRow->batch_no)) ? $dataRow->batch_no : '' ?>" />

                        <div class="col-md-12 form-group">
                            <label for="location_id">Location</label>
                            <select id="location_id" name="location_id" class="form-control select2 req">
                                <option value="">Select Location</option>

                                <?=getLocationListOption($locationList)?>
                            </select>  
                        </div>
                    <?php endif; ?>
					
                    <div class="table-responsive">
                        <table class="table table-bordered">
                            <tr class="bg-light">
                                <td>Ok Qty</td>
                                <td>
                                    <input type="text" id="ok_qty" name="ok_qty" class="form-control floatOnly" value="<?= (!empty($dataRow->ok_qty)) ? floatval($dataRow->ok_qty) : '' ?>" >
                                </td>
                            </tr>
                            <tr class="bg-light">
                                <td>Reject Qty</td>
                                <td>
                                    <input type="text" name="reject_qty" id="reject_qty" class="form-control floatOnly" value="<?= (!empty($dataRow->reject_qty)) ? floatval($dataRow->reject_qty) : '' ?>">
                                </td>
                            </tr>
                            <tr class="bg-light">
                                <td>Short Qty.</td>
                                <td>
                                    <input type="text" name="short_qty" id="short_qty" class="form-control floatOnly" value="<?= (!empty($dataRow->short_qty)) ? floatval($dataRow->short_qty) : '' ?>">
                                </td>
                            </tr>
                        </table>
                    </div>
                </div>
            </div>
        </div>
    </div>
</form>