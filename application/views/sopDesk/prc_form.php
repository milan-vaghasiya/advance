<form>
	<div class="col-md-12">
		<div class="row">
			<input type="hidden" name="id" value="<?= (!empty($dataRow->id)) ? $dataRow->id : ""; ?>" />
			<input type="hidden" name="prc_no" value="<?= (!empty($dataRow->prc_no)) ? $dataRow->prc_no : $prc_no ?>" />
			<input type="hidden" name="prc_detail_id" id="prc_detail_id" value="<?= (!empty($dataRow->prc_detail_id)) ? $dataRow->prc_detail_id : ""?>">
			
			<div class="col-md-4 form-group">
				<label for="prc_number">PRC No.</label>
				<input type="text" name="prc_number" id="prc_number" class="form-control req" value="<?= (!empty($dataRow->prc_number)) ? $dataRow->prc_number : $prc_prefix.$prc_no ?>" readonly />
			</div>
			<div class="col-md-4 form-group">
				<label for="prc_date">PRC Date</label>
				<input type="date" id="prc_date" name="prc_date" class="form-control fyDates req" value="<?= (!empty($dataRow->prc_date)) ? $dataRow->prc_date : date("Y-m-d") ?>" />
			</div>
			<div class="col-md-4 form-group">
				<label for="target_date">Target Date</label>
				<input type="date" id="target_date" name="target_date" class="form-control req" value="<?= (!empty($dataRow->target_date)) ? $dataRow->target_date : date("Y-m-d") ?>" />
			</div>
			<div class="col-md-6 form-group">
				<label for="party_id">Customer</label>
				<select name="party_id" id="party_id" class="form-control select2 req" autocomplete="off">
					<option value="">Select Customer</option>
					<option value="0" <?= (isset($dataRow->party_id) && $dataRow->party_id == 0) ? "selected" : "" ?>>Self Stock</option>
					<?php
						foreach ($customerData as $row) :
							$selected = (!empty($dataRow->party_id) && $dataRow->party_id == $row->party_id) ? "selected" : "";
							echo '<option value="' . $row->party_id . '" ' . $selected . '>[' . $row->party_code.'] '.$row->party_name . '</option>';
						endforeach;
					?>
				</select>
			</div>
			
			<div class="col-md-6 form-group">
				<label for="item_id">Product Name</label>
				<select name="item_id" id="item_id" class="form-control select2 req" autocomplete="off">
					<?php
					if (!empty($productData)) :
						echo $productData;
					endif;
					?>
				</select>
				<input type="hidden" name="so_trans_id" id="so_trans_id" value="<?=(!empty($dataRow->so_trans_id)?$dataRow->so_trans_id:'')?>">
			</div>
			<div class="col-md-4 form-group">
				<label for="rev_no">Revision No</label>
				<select name="rev_no" id="rev_no" class="select2">
					<?php
					if(!empty($revisionData)){
						echo $revisionData;
					}
					?>
				</select>
			</div>
			<div class="col-md-4 form-group">
				<label for="qty">Quantity</label>
				<input type="text" name="qty" id="qty" class="form-control numericOnly countWeight req" min="0" placeholder="Enter Qty." value="<?= (!empty($dataRow->prc_qty)) ? floatval($dataRow->prc_qty) : "" ?>" />
			</div>
			<div class="col-md-4 form-group">
				<label for="mfg_route">Mfg. Route</label>
				<select name="mfg_route" id="mfg_route" class="form-control">
					<option value="Forging" <?=(!empty($dataRow->mfg_route) && $dataRow->mfg_route == 'REGULAR')?'selected':''?>>REGULAR</option>
					<option value="Machining" <?=(!empty($dataRow->mfg_route) && $dataRow->mfg_route == 'NPD')?'selected':''?>>NPD</option>
				</select>
			</div>
			<div class="col-md-12 form-group">
				<label for="remark">Production Instruction</label>
				<textarea name="remark" id="remark" class="form-control" rows="2" ><?= (!empty($dataRow->remark)) ? $dataRow->remark : "" ?></textarea>
			</div>
			
		</div>
	</div>
</form>