<form autocomplete="off">
    <div class="col-md-12">
        <div class="row">
            <input type="hidden" name="id" value="<?=(!empty($dataRow->id))?$dataRow->id:""; ?>" />
            <input type="hidden" id="type" name="type" value="2" />

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
                <label for="amount">Amount</label>
                <input type="text" name="amount" class="form-control numericOnly req" value="<?=(!empty($dataRow->amount))?$dataRow->amount:""?>" />
            </div>

            <div class="col-md-4">
                <label for="entry_date">Date</label>
                <input type="date" name="entry_date" id="entry_date" class="form-control" value="<?=(!empty($dataRow->entry_date))?$dataRow->date:date("Y-m-d")?>" max="<?=(!empty($dataRow->entry_date))?$dataRow->entry_date:date("Y-m-d")?>" />
            </div>
            
            <div class="col-md-12 form-group">
                <label for="reason">Reason</label>
                <textarea name="reason" class="form-control req" style="resize:none;" ><?=(!empty($dataRow->reason))?$dataRow->reason:""?></textarea>
            </div>
    
        </div>
    </div>
</form>