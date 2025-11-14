<form>
    <div class="col-md-12">
        <div class="row">
            <input type="hidden" name="id" id="id" value="<?=(!empty($dataRow->id))?$dataRow->id:""; ?>" />
            
            <div class="col-md-4 form-group">
                <label for="end_date">End Time</label>
                <input type="datetime-local" name="end_date" class="form-control req" value="<?=(!empty($dataRow->end_date))?$dataRow->end_date:date('Y-m-d H:i:s') ?>" />
            </div>
            
            <div class="col-md-8 form-group">
                <label for="idle_reason">Idle Reason</label>
                <select name="idle_reason" id="idle_reason" class="form-control select2 req">
                <option value="">Select Idle Reason</option>
                    <?php 
                        foreach ($reasonList as $row) :
                            $selected = (!empty($dataRow->idle_reason) && $dataRow->idle_reason == $row->id) ? "selected" : "";
                            echo '<option value="' . $row->id . '" ' . $selected . '>['.$row->code.']' . $row->remark . '</option>';
                        endforeach;   
                    ?>
                </select>
            </div> 
            
            <div class="col-md-12 form-group">
                <label for="solution">Solution</label>
                <textarea type="text" name="solution" class="form-control" rows="2"><?=(!empty($dataRow->solution))?$dataRow->solution:""; ?></textarea>
            </div>

        </div> 
    </div>
</form>