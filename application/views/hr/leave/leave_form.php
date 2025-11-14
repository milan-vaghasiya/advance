<form autocomplete="off">
    <div class="col-md-12">
        <div class="row">
            <input type="hidden" name="id" value="<?=(!empty($dataRow->id))?$dataRow->id:""; ?>" />

            <div class="col-md-12 form-group"><div class="error generalError"></div></div>

            <div class="col-md-8 form-group">
                <label for="emp_id">Employee</label>
                <select name="emp_id" id="emp_id" class="form-control select2 req">
                    <option value="">Select Employee</option>
                    <?php
                        foreach($empList as $row):
							$selected = (!empty($dataRow->emp_id) && $row->id == $dataRow->emp_id)?"selected":"";
							$emp_name = ($this->loginId == $row->id) ? "My Self" : $row->emp_name;
							echo '<option value="'.$row->id.'" '.$selected.'>['.$row->emp_code.'] '.$emp_name.'</option>';
                        endforeach;
                    ?>
                </select>
            </div>
            <div class="col-md-4">
                <label for="leave_type_id">Leave Type</label>
                <select name="leave_type_id" id="leave_type_id" class="form-control select2 req">
                    <option value="">Select Leave Type</option>
                    <?php
                        foreach($leaveType as $row):
                            $selected = (!empty($dataRow->leave_type_id) && $row->id == $dataRow->leave_type_id)?"selected":"";
                            echo '<option value="'.$row->id.'" '.$selected.'>'.$row->leave_type.'</option>';
                        endforeach;
                    ?>
                </select>
            </div>

            <div class="col-md-3 form-group">
                <label for="start_date">Start Date</label>
                <input type="date" name="start_date" id="start_date" class="form-control countTotalDays req" value="<?=(!empty($dataRow->start_date))?formatDate($dataRow->start_date,'Y-m-d'):date("Y-m-d")?>" min="<?=(!empty($dataRow->start_date))?formatDate($dataRow->start_date,'Y-m-d'):date("Y-m-d")?>" />
            </div>

            <div class="col-md-3 form-group">
                <label for="start_section">Start Section </label>
                <select name="start_section" id="start_section" class="form-control countTotalDays select2 req" >
                    <option value="">Select Start Section</option>
                    <option value="1" <?=(!empty($dataRow->start_section) && $dataRow->start_section == 1)?"selected":""?>>Half Day(First)</option> 
                    <option value="2" <?=(!empty($dataRow->start_section) && $dataRow->start_section == 2)?"selected":""?>>Half Day(Second)</option>
                    <option value="3" <?=(!empty($dataRow->start_section) && $dataRow->start_section == 3)?"selected":""?>>Full day</option>
                </select>
            </div>
			
            <div class="col-md-3 form-group">
                <label for="end_date">End Date</label>
                <input type="date" name="end_date" id="end_date" class="form-control countTotalDays req" value="<?=(!empty($dataRow->end_date))?formatDate($dataRow->end_date,'Y-m-d'):date("Y-m-d")?>"  />
            </div>
			
            <div class="col-md-3 form-group">
                <label for="end_section">End Section </label>
                <select name="end_section" id="end_section" class="form-control countTotalDays endSection select2 req" <?=(!empty($dataRow->leave_type_id) && $dataRow->leave_type_id == -1)? "disabled":""; ?>>
                    <option value="">Select End Section</option>
                    <option value="1" <?=(!empty($dataRow->end_section) && $dataRow->end_section == 1)?"selected":""?>>First Half</option>
                     <option value="2" <?=(!empty($dataRow->end_section) && $dataRow->end_section == 2)?"selected":""?>>Second Half</option> 
                    <option value="3" <?=(!empty($dataRow->end_section) && $dataRow->end_section == 3)?"selected":""?>>Full day</option>
                </select>
            </div>

            <div class="col-md-3 form-group">
                <label class="totaldays" for="total_days">Total Days</label>
                <input type="text" name="total_days" id="total_days" class="form-control floatOnly req" value="<?=(!empty($dataRow->total_days))?floatval($dataRow->total_days):1; ?>" <?=(!empty($dataRow->leave_type_id) && $dataRow->leave_type_id == -1)? "":"readOnly"; ?> />
            </div>
            <div class="col-md-9 form-group">
                <label for="leave_reason">Reason</label>
                <textarea rows="1" name="leave_reason" class="form-control req" placeholder="Reason" ><?=(!empty($dataRow->leave_reason))?$dataRow->leave_reason:""?></textarea>
            </div>
			<div class="col-md-12 form-group">
				<span class="badge badge-pill bg-primary max-leave font-14 font-medium"><?=!empty($leaveCount)?'Maximum Leave : '.$leaveCount['max_leave']:''?></span>
				<span class="badge badge-pill bg-danger used-leave font-14 font-medium"><?=!empty($leaveCount)?'Taken Leave : '.$leaveCount['used_leaves']:''?></span>
				<span class="badge badge-pill bg-success remain-leave font-14 font-medium"><?=!empty($leaveCount)?'Remain Leave : '.$leaveCount['remain_leaves']:''?></span>
			</div>
        </div>
    </div>
</form>
