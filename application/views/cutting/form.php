<form>
	<div class="col-md-12">
		<div class="row">
			<input type="hidden" name="id" value="<?= (!empty($dataRow->id)) ? $dataRow->id : ""; ?>" />
			<input type="hidden" name="prc_no" value="<?= (!empty($dataRow->prc_no)) ? $dataRow->prc_no : $prc_no ?>" />
			<input type="hidden" name="prc_detail_id" id="prc_detail_id" value="<?= (!empty($dataRow->prc_detail_id)) ? $dataRow->prc_detail_id : ""?>">
			
			<div class="col-md-4 form-group">
				<label for="prc_number">Job No.</label>
				<input type="text" name="prc_number" id="prc_number" class="form-control req" value="<?= (!empty($dataRow->prc_number)) ? $dataRow->prc_number : $prc_prefix.$prc_no ?>" readonly />
			</div>
			<div class="col-md-4 form-group">
				<label for="prc_date">Job Date</label>
				<input type="date" id="prc_date" name="prc_date" class="form-control req" value="<?= (!empty($dataRow->prc_date)) ? $dataRow->prc_date : date("Y-m-d") ?>" />
			</div>
			<div class="col-md-4 form-group">
				<label for="target_date">Target Date</label>
				<input type="date" id="target_date" name="target_date" class="form-control req" value="<?= (!empty($dataRow->target_date)) ? $dataRow->target_date : date("Y-m-d") ?>" />
			</div>
			<div class="col-md-4 form-group">
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
			<div class="col-md-8 form-group">
				<label for="item_id">Product Name</label>
				<select name="item_id" id="item_id" class="form-control select2 req itemDetails" autocomplete="off" data-res_function="resItemDetail">
					<?php
					if (!empty($dataRow->id)) :
						echo $productData;
					endif;
					?>
				</select>
				<input type="hidden" name="so_trans_id" id="so_trans_id" value="<?= (!empty($dataRow->so_trans_id)) ? $dataRow->so_trans_id : ""?>">
			</div>

			<div class="col-md-3 form-group">
				<label for="qty">Plan Quantity</label>
				<input type="text" name="qty" id="qty" class="form-control numericOnly  req" min="0" placeholder="Enter Qty." value="<?= (!empty($dataRow->prc_qty)) ? floatval($dataRow->prc_qty) : "" ?>" />
			</div>
			<div class="col-md-3 form-group">
				<label for="cutting_type">Cutting Type</label>
				<select name="cutting_type" id="cutting_type" class="form-control select2 req">
					<option value="">Select Type</option>
					<option value="Furnace" <?= (!empty($dataRow->cutting_type) && $dataRow->cutting_type == "Furnace") ? "selected" : "" ?> >Furnace</option>
					<option value="Induction" <?= (!empty($dataRow->cutting_type) && $dataRow->cutting_type == "Induction") ? "selected" : "" ?> >Induction</option>
				</select>
			</div>
			<div class="col-md-3 form-group">
				<label for="cutting_length">Cutting Length</label>
				<input type="text" name="cutting_length" id="cutting_length" class="form-control" value="<?= (!empty($dataRow->cutting_length)) ? ($dataRow->cutting_length) : "" ?>" />
			</div>
			<div class="col-md-3 form-group">
				<label for="cutting_dia">Cutting Dia</label>
				<input type="text" name="cutting_dia" id="cutting_dia" class="form-control"  value="<?= (!empty($dataRow->cutting_dia)) ? ($dataRow->cutting_dia) : "" ?>" />
			</div>
			<div class="col-md-3 form-group">
				<label for="cut_weight">Cut Weight <span>(Min-Max)</span></label>
				<div class="d-inline-flex">
					<input type="text" name="min_cut_weight" id="min_cut_weight" class="form-control floatOnly" min="0"  value="<?= (!empty($min_cut_weight) ? $min_cut_weight : 0);?>" />
					<input type="text" name="max_cut_weight" id="max_cut_weight" class="form-control floatOnly" min="0"  value="<?= (!empty($max_cut_weight) ? $max_cut_weight : 0);?>" />
				</div>
				<div class="error minCutWeight"></div>
			</div>
			<div class="col-md-12 form-group">
				<label for="remark">Cutting Instruction</label>
				<textarea name="remark" id="remark" class="form-control" ><?= (!empty($dataRow->remark)) ? $dataRow->remark : "" ?></textarea>
			</div>
		</div>
	</div>
</form>

