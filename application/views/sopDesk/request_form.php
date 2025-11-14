<form>
	<?php $req_from = (!empty($dataRow->req_from)) ? $dataRow->req_from : $req_from; ?>
	<div class="col-md-12">
		<div class="row">
			<input type="hidden" name="id" value="<?= (!empty($dataRow->id)) ? $dataRow->id : ""; ?>" />
			<input type="hidden" name="req_from" value="<?= (!empty($dataRow->req_from)) ? $dataRow->req_from : $req_from ?>" />
			
			<div class="col-md-3 form-group">
				<label for="trans_number">Request No.</label>
				<input type="text" name="trans_number" id="trans_number" class="form-control req" value="<?= (!empty($dataRow->trans_number)) ? $dataRow->trans_number : $trans_number ?>" readonly />
			</div>
			<div class="col-md-3 form-group">
				<label for="trans_date">Request Date</label>
				<input type="date" id="trans_date" name="trans_date" class="form-control req" value="<?= (!empty($dataRow->trans_date)) ? $dataRow->trans_date : date("Y-m-d") ?>" />
			</div>
			<div class="col-md-6 form-group">
				<label for="item_id">Product Name</label>
				<select name="item_id" id="item_id" class="form-control select2 req" autocomplete="off">
					<option value="">Select Product</option>
					<?php
					if (!empty($itemList)) :
						foreach($itemList as $row){
                            $itemName = (!empty($row->item_code)?'['.$row->item_code.']':'').$row->item_name;
							$selected =( !empty($dataRow->item_id) && $dataRow->item_id == $row->id)?'selected':'';
                            ?>
                            <option value="<?=$row->id?>" <?=$selected?>><?=$itemName?></option>
                            <?php
                        }
					endif;
					?>
				</select>
			</div>
			<div class="col-md-6 form-group">
				<label for="qty">Quantity</label>
				<input type="text" name="qty" id="qty" class="form-control numericOnly countWeight req" min="0" placeholder="Enter Qty." value="<?= (!empty($dataRow->qty)) ? floatval($dataRow->qty) : "" ?>" />
			</div>
            <div class="col-md-6 form-group">
				<label for="req_to">Request To</label>
				<select name="req_to" id="req_to" class="form-control select2 req" autocomplete="off">
					<option value="">Select Product</option>
					<?php
					if (!empty($processList)) :
						foreach($processList as $row){
                           if($row->id != $req_from){
							$selected =( !empty($dataRow->req_to) && $dataRow->req_to == $row->id)?'selected':'';
                            ?> <option value="<?=$row->id?>" <?=$selected?>><?=$row->process_name?></option> <?php
                           }
                            
                        }
					endif;
					?>
				</select>
			</div>
			<div class="col-md-12 form-group">
				<label for="remark">Remark</label>
				<textarea name="remark" id="remark" class="form-control" rows="2" ><?= (!empty($dataRow->remark)) ? $dataRow->remark : "" ?></textarea>
			</div>
			
		</div>
	</div>
</form>