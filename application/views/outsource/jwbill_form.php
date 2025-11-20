<form id="billForm" data-res_function="challanResponse">
    <div class="col-md-12">
        <div class="row">
            <input type="hidden" name="ids" id="ids" value="<?= (!empty($log_ids)) ? $log_ids: ""; ?>" />

            <div class="col-md-12 form-group">
                <label for="bill_no">Bill No. & Date</label> 
                <input type="text" name="bill_no" id="bill_no" class="form-control req" value="" />
            </div>
		</div>
	</div>
</form>
		