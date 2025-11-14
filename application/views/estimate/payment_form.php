<form>
    <div class="col-md-12">
        <div class="row">
            <input type="hidden" name="id" id="id" value="<?=(!empty($dataRow->id))?$dataRow->id:""?>">
            <input type="hidden" name="main_ref_id" id="main_ref_id" value="<?=(!empty($dataRow->main_ref_id))?$dataRow->main_ref_id:""?>">
            <div class="col-md-6 form-group">
                <label for="party_id">Customer Name</label>
                <select name="party_id" id="party_id" class="form-control select2 req">
                    <option value="">Select Party</option>
                    <?=getPartyListOption($partyList,((!empty($dataRow->party_id))?$dataRow->party_id:0))?>
                </select>
            </div>
            <div class="col-md-3 form-group">
                <label for="entry_date">Date</label>
                <input type="date" name="entry_date" id="entry_date" class="form-control fyDates" max="<?=getFyDate()?>" value="<?=(!empty($dataRow->entry_date))?$dataRow->entry_date:getFyDate()?>">
            </div>
            <div class="col-md-3 form-group">
                <label for="received_by">Received By</label>
                <input type="text" name="received_by" id="received_by" class="form-control" value="<?=(!empty($dataRow->received_by))?$dataRow->received_by:""?>">
            </div>
            <div class="col-md-3 form-group">
                <label for="reference_no">Ref. No.</label>
                <input type="text" name="reference_no" id="reference_no" class="form-control" value="<?=(!empty($dataRow->reference_no))?$dataRow->reference_no:""?>">
            </div>
            <div class="col-md-3 form-group">
                <label for="amount">Amount</label>
                <input type="text" name="amount" id="amount" class="form-control floatOnly" value="<?=(!empty($dataRow->amount))?$dataRow->amount:""?>">
            </div>
            <div class="col-md-6 form-group">
                <label for="remark">Remark</label>
                <input type="text" name="remark" id="remark" class="form-control" value="<?=(!empty($dataRow->remark))?$dataRow->remark:""?>">
            </div>
        </div>
    </div>
</form>