<form>
    <div class="col-md-12">
        <div class="row">

            <div class="error general_error"></div>

            <input type="hidden" name="id" id="id" value="<?=(!empty($dataRow->id))?$dataRow->id:""; ?>" />
                
            <div class="col-md-12 form-group">
                <label for="shift_name">Shift Name</label>
                <input type="text" name="shift_name" class="form-control req" value="<?=(!empty($dataRow->shift_name))?$dataRow->shift_name:""; ?>" />
            </div>
            <div class="col-md-6 form-group">
                <label for="shift_start">Shift Start Time</label>
                <input type="time" name="shift_start" class="form-control req" value="<?=(!empty($dataRow->shift_start))?$dataRow->shift_start:""; ?>" />
            </div>
            <div class="col-md-6 form-group">
                <label for="shift_end">Shift End Time</label>
                <input type="time" name="shift_end" class="form-control req" value="<?=(!empty($dataRow->shift_end))?$dataRow->shift_end:""; ?>" />
            </div>
            <div class="col-md-6 form-group">
                <label for="lunch_start">Lunch Start Time</label>
                <input type="time" name="lunch_start" class="form-control req" value="<?=(!empty($dataRow->lunch_start))?$dataRow->lunch_start:""; ?>" />
            </div>
            <div class="col-md-6 form-group">
                <label for="lunch_end">Lunch End Time</label>
                <input type="time" name="lunch_end" class="form-control req" value="<?=(!empty($dataRow->lunch_end))?$dataRow->lunch_end:""; ?>" />
            </div>
        </div>
    </div>
</form>