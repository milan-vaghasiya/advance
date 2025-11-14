<form autocomplete="off" id="rev_check_point">
	<div class="col-md-12">
		<div class="row form-group">
			
			<?php $status = (!empty($dataRow->status))?$dataRow->status:""?>
			
			<input type="hidden" name="id" id="id" value="<?=(!empty($dataRow->id) ? $dataRow->id : '')?>" />
			<div class="col-md-2 form-group">
				<label for="ecn_no">ECN Type</label>
				<select name="ecn_type" id="ecn_type" class="form-control">
					<?php
					foreach($this->ECN_TYPES AS $key=>$type){
						$selected = (!empty($dataRow->ecn_type) && $dataRow->ecn_type == $key)?'selected':'';
						?> <option value="<?=$key?>" <?= $selected?>> <?=$type?> </option> <?php
					}
					?>
				</select>
			</div>
			<div class="col-md-2 form-group">
				<label for="ecn_no">ECN No.</label>
				<input type="text" id="ecn_no" name="ecn_no" class="form-control req" value="<?=(!empty($dataRow->ecn_no ) ? $dataRow->ecn_no  : '' )?>" />
			</div>
            <div class="col-md-2 form-group">
				<label for="ecn_date">ECN Date</label>
				<input type="date" id="ecn_date" name="ecn_date" class="form-control req" value="<?=(!empty($dataRow->ecn_date) ? $dataRow->ecn_date : date("Y-m-d"))?>" />
			</div>
			
            <div class="col-md-6 form-group">
				<label for="item_id">Item Name</label>
				<select name="item_id" id="item_id" class="form-control select2 req">
					<option value="">Select Item Name</option>
					<?php
					foreach ($itemData as $row) :
						$selected = (!empty($dataRow->item_id) && ($dataRow->item_id == $row->id) ? "selected" : "");
						$disabled = ($status == 1 && !$selected) ? 'disabled' : '';
						echo '<option value="' . $row->id . '" '.$selected.' '.$disabled.'>' . $row->item_code.' '.$row->item_name . '</option>';
					endforeach;
					?>
				</select>
				<div class="error item_id"></div>
			</div>

            <div class="col-md-2 form-group">
				<label for="drw_no">Drawing No.</label>
				<input type="text" id="drw_no" name="drw_no" class="form-control req" value="<?=(!empty($dataRow->drw_no) ? $dataRow->drw_no : '')?>" />
			</div>
            <div class="col-md-3 form-group">
				<label for="cust_rev_no">Cust. Rev. No.</label>
				<input type="text" id="cust_rev_no " name="cust_rev_no" class="form-control req" value="<?=(!empty($dataRow->cust_rev_no ) ? $dataRow->cust_rev_no  : '')?>" />
			</div>

			<div class="col-md-3 form-group">
				<label for="cust_rev_date">Cust. Rev. Date</label>
				<input type="date" id="cust_rev_date" name="cust_rev_date" class="form-control req" value="<?=(!empty($dataRow->cust_rev_date ) ? $dataRow->cust_rev_date  : date("Y-m-d"))?>" />	
			</div>

			<div class="col-md-2 form-group">
				<label for="rev_no">Revision No.</label>
				<input type="text" id="rev_no" name="rev_no" class="form-control req" value="<?=(!empty($dataRow->rev_no) ? $dataRow->rev_no : '')?>" <?=($status == 1) ? 'readOnly' : ''?> />
			</div>

			<div class="col-md-2 form-group">
				<label for="rev_date">Revision Date</label>
				<input type="date" id="rev_date" name="rev_date" class="form-control req" value="<?=(!empty($dataRow->rev_date) ? $dataRow->rev_date : date("Y-m-d"))?>" />	
			</div>			
			
			<div class="col-md-6 form-group">
				<label for="customer_drg"> Customer drg. </label>
				<input type="file" name="customer_drg" id="customer_drg" class="form-control" />                
			</div>
			<div class="col-md-6 form-group">
				<label for="company_drg">Company drg.</label>
				<input type="file" name="company_drg" id="company_drg" class="form-control" />                
			</div>	

			<div class="col-md-12 form-group">
				<label for="remark">Remark</label>
				<textarea name="remark" id="remark" class="form-control" placeholder="Enter Remark" value=""><?=(!empty($dataRow->remark))?$dataRow->remark:""?></textarea>
			</div>

			<div class="col-md-12 form-group">
				<label for="change_reason">Reason For Changes</label>
				<textarea name="change_reason" id="change_reason" class="form-control" rows="4"><?=(!empty($dataRow->change_reason))?$dataRow->change_reason:""?></textarea>
			</div>
            <div class="col-md-12 form-group">
				<label for="change_detail">Details of Changes</label>
				<textarea name="change_detail" id="change_detail" class="form-control" rows="4"><?=(!empty($dataRow->change_detail))?$dataRow->change_detail:""?></textarea>
			</div>
			
			<div class="col-md-6 form-group">
                <label for="key_contact">Key Contact</label>
                <select class="form-control select2" name="key_contact" id="key_contact">
                    <option value="">Select Key Contact</option>
                    <?php
						if(!empty($empList)){
							foreach($empList as $row){
								$selected = (!empty($dataRow->key_contact) && $dataRow->key_contact == $row->id)?'selected':'';
								echo '<option value="'.$row->id.'" '.$selected.'>'.$row->emp_name.'</option>';
							}
						}
                    ?>
                </select>
            </div>
			<div class="col-md-6 form-group">
                <label for="core_team">Core Team</label>
                <select class="form-control select2" name="core_team[]" id="core_team" multiple>
                    <?php
						if(!empty($empList)){
							foreach($empList as $row){
								$selected = (!empty($dataRow->core_team) && (in_array($row->id,  explode(',', $dataRow->core_team)))) ? "selected" : "";
								echo '<option value="'.$row->id.'" '.$selected.'>'.$row->emp_name.'</option>';
							}
						}
                    ?>
                </select>
            </div>
		</div>
	</div>
</form>
