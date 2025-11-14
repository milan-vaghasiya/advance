<form autocomplete="off">
    <div class="col-md-12">
        <div class="row">
            <input type="hidden" name="id" value="<?=(!empty($dataRow->id))?$dataRow->id:""; ?>" />
            <input type="hidden" id="type" name="type" value="3" />

            <div class="col-md-4 form-group">
                <label for="entry_date">Issue Date</label>
                <input type="date" name="entry_date" id="entry_date" class="form-control" value="<?=(!empty($dataRow->entry_date))?$dataRow->entry_date:date('Y-m-d'); ?>">
            </div>
            <div class="col-md-4 form-group">
                <label for="emp_id">Employee</label>
                <select name="emp_id" id="emp_id" class="form-control single-select req">
                    <option value="">Select Employee</option>
                    <?php
                        foreach($empData as $row):
                            $selected = (!empty($dataRow->emp_id) && $row->id == $dataRow->emp_id)?"selected":"";
                            echo '<option value="'.$row->id.'" '.$selected.'>['.$row->emp_code.'] '.$row->emp_name.'</option>';
                        endforeach;
                    ?>
                </select>
            </div>
            <div class="col-md-4 form-group">
                <label for="facility_id">Facility Type</label>
                <select name="facility_id" id="facility_id" class="form-control single-select req">
                    <option value="">Select Type</option>
                    <?php
                        foreach($typeData as $row):
							if($row->id != 10):
								$selected = (!empty($dataRow->facility_id) && $row->id == $dataRow->facility_id)?"selected":"";
								echo '<option value="'.$row->id.'" '.$selected.'>'.$row->remark.'</option>';
							endif;
                        endforeach;
                    ?>
                </select>
            </div>
            <div class="col-md-4 form-group">
                <label for="amount">Qty</label>
                <input type="text" name="amount" class="form-control numericOnly" value="<?=(!empty($dataRow->amount))?$dataRow->amount:""?>" />
            </div>
            <div class="col-md-8 form-group">
                <label for="reason">Specification/Size</label>
                <input type="text" id="reason" name="reason"  class="form-control req" value="<?=(!empty($dataRow->reason))?$dataRow->reason:""?>" />
            </div>
        </div>
    </div>
</form>