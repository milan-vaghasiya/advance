<form>
    <div class="col-md-12">
        <div class="row">

        <input type="hidden" name="prev_main_plan_id" id="prev_main_plan_id" value="<?=(!empty($dataRow->prev_main_plan_id) ? $dataRow->prev_main_plan_id : '')?>">

        <div class="col-md-12 form-group">
            <label for="schedule_date">Schedule Date</label>
            <input type="date" name="schedule_date" id="schedule_date" class="form-control req" value="<?=(!empty($dataRow->schedule_date) ? $dataRow->schedule_date : date('Y-m-d'))?>">
        </div>

        </div>
    </div>
</form>