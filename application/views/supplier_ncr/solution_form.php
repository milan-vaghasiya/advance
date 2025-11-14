<form id="solutionForm">
    <div class="col-md-12">
        <div class="row">
            <input type="hidden" name="id" value="<?=(!empty($dataRow->id))?$dataRow->id:""; ?>" />
            <div class="col-md-6 form-group">
                <label for="report_no">CA Report No.</label>
                <input type="text" name="report_no" id="report_no" class="form-control req" value="<?=(!empty($dataRow->report_no))?$dataRow->report_no:""?>"/>
            </div>
            <div class="col-md-6 form-group">
                <label for="product_returned">CAPA Request</label>
                <select name="product_returned" id="product_returned" class="form-control select2">
                    <option value="">Select Option</option>
                    <option value="No" <?= (!empty($dataRow) && $dataRow->product_returned == 'No') ? "selected" : "" ?>>No</option>
                    <option value="Yes" <?= (!empty($dataRow) && $dataRow->product_returned == 'Yes') ? "selected" : "" ?>>Yes</option>
                </select>
            </div>
            <div class="col-md-12 form-group">
				<label for="report_8d">8D Report</label>
				<input type="file" name="report_8d" id="report_8d" class="form-control" />                
			</div>
            <div class="col-md-12 form-group">
                <label for="ref_feedback">Effectiveness</label>
                <textarea name="ref_feedback" id="ref_feedback" class="form-control req"><?=(!empty($dataRow->ref_feedback))?$dataRow->ref_feedback:""?></textarea>
            </div>
            <div class="col-md-12 form-group">
                <label for="remark">Remarks</label>
                <textarea name="remark" id="remark" class="form-control"><?=(!empty($dataRow->remark))?$dataRow->remark:""?></textarea>
            </div>
        </div>
    </div>
</form>