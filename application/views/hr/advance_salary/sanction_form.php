<form autocomplete="off">
    <div class="col-md-12">
        <div class="row">
            <input type="hidden" name="id" value="<?=(!empty($dataRow->id))?$dataRow->id:""; ?>" />
            <input type="hidden" name="form_type" value="sanction">
            
            <div class="col-md-3">
                <label for="entry_date">Date</label>
                <input type="date" name="entry_date" id="entry_date" class="form-control" value="<?=(!empty($dataRow->entry_date))?$dataRow->entry_date:date("Y-m-d")?>" max="<?=(!empty($dataRow->entry_date))?$dataRow->entry_date:date("Y-m-d")?>" readonly/>
            </div>
            
            <div class="col-md-5 form-group">
                <label for="emp_id">Employee</label>
                <input type="text" class="form-control" value="<?=(!empty($dataRow->emp_name))?$dataRow->emp_name:""?>" readonly>
                <input type="hidden" name="emp_id" id="emp_id" value="<?=(!empty($dataRow->emp_id))?$dataRow->emp_id:""?>">
            </div>
            
            <div class="col-md-2 form-group">
                <label>Payment Mode</label>
                <input type="text" class="form-control" value="<?=(!empty($dataRow->payment_mode) && $dataRow->payment_mode == 'CS')?"CASH":"BANK";?>" readonly>
            </div>
            
            <div class="col-md-2 form-group">
                <label for="amount">Amount</label>
                <input type="text" name="amount" class="form-control numericOnly req" value="<?=(!empty($dataRow->amount))?floatVal($dataRow->amount):""?>" readonly/>
            </div>
            
            <div class="col-md-12 form-group">
                <label for="reason">Demand Reason</label>
                <textarea name="reason" class="form-control req" style="resize:none;" readonly><?=(!empty($dataRow->reason))?$dataRow->reason:""?></textarea>
            </div>

            <hr>

            <div class="col-md-4 form-group">
                <label for="sanctioned_at">Sanction AT</label>
                <input type="datetime-local" name="sanctioned_at" id="sanctioned_at" class="form-control req" value="<?=date('Y-m-d H:i:s')?>" min="<?=date("Y-m-d\TH:i:s",strtotime($dataRow->entry_date." 00:00:00"))?>" max="<?=date("Y-m-d\TH:i:s")?>">
            </div>

            <div class="col-md-4 form-group">
                <label for="sanctioned_amount">Sanction Amount</label>
                <input type="text" name="sanctioned_amount" id="sanctioned_amount" class="form-control numericOnly req" value="<?=(!empty($dataRow->amount))?floatVal($dataRow->amount):""?>">
            </div>
        </div>
    </div>
</form>