<form>
    <div class="col-md-12">
        <div class="row">
            <div class="error general_error"></div>
            <input type="hidden" name="id" id="id" value="<?=(!empty($dataRow->id))?$dataRow->id:""; ?>" />
            <input type="hidden" name="trans_no" id="trans_no" value="<?=(!empty($dataRow->trans_no))?$dataRow->trans_no:$trans_no?>">

            <div class="col-md-6 form-group">
                <label for="trans_no">M.T. No.</label>
                <input type="text" name="trans_number" id="trans_number" class="form-control" value="<?=(!empty($dataRow->trans_number))?$dataRow->trans_number:$trans_number?>" readonly>
            </div>
            <div class="col-md-6 form-group">
                <label for="trans_date">BreakDown Time</label>
                <input type="datetime-local" name="trans_date" class="form-control req" value="<?=(!empty($dataRow->trans_date))?$dataRow->trans_date:date('Y-m-d H:i:s'); ?>" />
            </div>
            <div class="col-md-6 form-group">
				<label for="prc_id">PRC No.</label>
				<select name="prc_id" id="prc_id" class="form-control select2">
                    <option value="">Select PRC</option>
                    <?php 
                        foreach ($prcList as $row) :
                            $selected = (!empty($dataRow->prc_id) && $dataRow->prc_id == $row->id)?"selected":((!empty($prc_id) && $prc_id == $row->id)?"selected":"");

                            echo '<option value="' . $row->id . '" ' . $selected . '>' . $row->prc_number . (!empty($row->item_code) ? ' [ '. $row->item_code .' ] ' : ' '). '</option>'; //22-05-25
                        endforeach;   
                    ?>
                </select>
			</div>
            <div class="col-md-6 form-group">
                <label for="machine_id">Machine</label>
                <select name="machine_id" id="machine_id" class="form-control select2 req">
                <option value="">Select Machine</option>
                    <?php 
                        foreach ($machineList as $row) :
                            $selected = (!empty($dataRow->machine_id) && $dataRow->machine_id == $row->id) ? "selected" : "";
                            echo '<option value="' . $row->id . '" ' . $selected . '>' .(!empty($row->item_code) ? $row->item_code : ''). $row->item_name . '</option>';
                        endforeach;   
                    ?>
                </select>
            </div>  
            <div class="col-md-12 form-group">
                <label for="remark">Problem </label> 
                <textarea type="text" name="remark" class="form-control" rows="2"><?=(!empty($dataRow->remark))?$dataRow->remark:""; ?></textarea>
            </div>

        </div>
    </div>
</form>