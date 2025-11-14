<form>
	<div class="col-md-12">
		<div class="row">
			<input type="hidden" name="id" value="<?= (!empty($dataRow->id)) ? $dataRow->id : ""; ?>" />
			<input type="hidden" name="prc_no" value="<?= (!empty($dataRow->prc_no)) ? $dataRow->prc_no : $prc_no ?>" />
			<input type="hidden" name="prc_detail_id" id="prc_detail_id" value="<?= (!empty($dataRow->prc_detail_id)) ? $dataRow->prc_detail_id : ""?>">
			
			<div class="col-md-3 form-group">
				<label for="prc_number">PRC No.</label>
				<input type="text" name="prc_number" id="prc_number" class="form-control req" value="<?= (!empty($dataRow->prc_number)) ? $dataRow->prc_number : $prc_prefix.$prc_no ?>" readonly />
			</div>
			<div class="col-md-3 form-group">
				<label for="prc_date">PRC Date</label>
				<input type="date" id="prc_date" name="prc_date" class="form-control req" value="<?= (!empty($dataRow->prc_date)) ? $dataRow->prc_date : date("Y-m-d") ?>" />
			</div>
			<div class="col-md-6 form-group">
				<label for="party_id">Customer</label>
				<select name="party_id" id="party_id" class="form-control select2 req" autocomplete="off">
					<option value="">Select Customer</option>
					<option value="0" <?= (!empty($dataRow->id) && $dataRow->party_id == 0) ? "selected" : "" ?>>Self Stock</option>
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
					<option value="">Select Product</option>
					<?php
					if (!empty($dataRow->id)) :
						echo $productData['options'];
					endif;
					?>
				</select>
				<input type="hidden" name="so_trans_id" id="so_trans_id" value="">
			</div>

			<div class="col-md-3 form-group">
				<label for="qty">Quatity</label>
				<input type="text" name="qty" id="qty" class="form-control numericOnly countWeight req" min="0" placeholder="Enter Qty." value="<?= (!empty($dataRow->qty)) ? $dataRow->qty : "" ?>" />
			</div>
			<div class="col-md-3 form-group">
				<label for="target_date">Target Date</label>
				<input type="date" id="target_date" name="target_date" class="form-control req" value="<?= (!empty($dataRow->target_date)) ? $dataRow->target_date : date("Y-m-d") ?>" />
			</div>
			<div class="col-md-12 mt-3 form-group">
				<label for="remark">Remark</label>
				<input type="text" name="remark" id="remark" class="form-control" value="<?= (!empty($dataRow->remark)) ? $dataRow->remark : "" ?>" />
			</div>
			<div class="col-md-12 form-group mb-5" id="processData"></div>
			<div class="error process"></div>
		</div>
	</div>
</form>
