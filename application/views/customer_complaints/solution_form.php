<form id="solutionForm">
    <div class="col-md-12">
        <div class="row">
            <input type="hidden" name="id" value="<?=(!empty($dataRow->id))?$dataRow->id:""; ?>" />
            <div class="col-md-12 form-group">
                <label for="report_no">CA Report No.</label>
                <input type="text" name="report_no" id="report_no" class="form-control req" value="<?=(!empty($dataRow->report_no))?$dataRow->report_no:""?>"/>
            </div>
            <div class="col-md-12 form-group">
				<label for="report_8d">8D Report</label>
				<input type="file" name="report_8d" id="report_8d" class="form-control" />                
			</div>
            <div class="col-md-12 form-group">
                <label for="action_taken">Details of  Action Taken</label>
                <textarea name="action_taken" id="action_taken" class="form-control req"><?=(!empty($dataRow->action_taken))?$dataRow->action_taken:""?></textarea>
            </div>
            <div class="col-md-12 form-group">
                <label for="ref_feedback">Reference of feed back to Customer</label>
                <input type="text" name="ref_feedback" class="form-control" value="<?=(!empty($dataRow->ref_feedback))?$dataRow->ref_feedback:""?>" />
            </div>
            <div class="col-md-12 form-group">
                <label for="remark">Remarks</label>
                <textarea name="remark" id="remark" class="form-control"><?=(!empty($dataRow->remark))?$dataRow->remark:""?></textarea>
            </div>
        </div>
    </div>
</form>