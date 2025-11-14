<form autocomplete="off">
    <div class="col-md-12">
        <input type="hidden" name="id" value="<?=(!empty($dataRow->id))?$dataRow->id:""; ?>" />
        <div class="row">
			<div class="col-md-12 form-group">
                <label for="leave_type">Leave Type</label>
                <input type="text" name="leave_type" class="form-control text-capitalize req" placeholder="Leave Type" value="<?=(!empty($dataRow->leave_type))?$dataRow->leave_type:""; ?>" />
            </div>
            <div class="col-md-12 form-group">
                <label for="remark">Remark</label>
                <input type="text" name="remark" class="form-control text-capitalize" placeholder="Remark" value="<?=(!empty($dataRow->remark))?$dataRow->remark:""; ?>" />
            </div>
        </div>
	</div>
</form>