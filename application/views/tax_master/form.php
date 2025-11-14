<form>
    <div class="col-md-12">
        <div class="row">
            <input type="hidden" name="id" id="id" value="<?=(!empty($dataRow->id))?$dataRow->id:""?>">

            <div class="col-md-4 form-group">
                <label for="name">Tax Name</label>
                <input type="text" name="name" id="name" class="form-control req" value="<?=(!empty($dataRow->name))?$dataRow->name:""?>">
            </div>
            <div class="col-md-4 form-group">
                <label for="tax_type">Tax Type</label>
                <select name="tax_type" id="tax_type" class="form-control req">
                    <option value = "">Select Tax Type</option>
                    <option value="1" <?=(!empty($dataRow->tax_type) && $dataRow->tax_type == 1)?"selected":""?>>Purchase</option>
                    <option value="2" <?=(!empty($dataRow->tax_type) && $dataRow->tax_type == 2)?"selected":""?>>Sales</option>
                </select>
            </div>
            <div class="col-md-2 form-group">
                <label for="calculation_type">Calcu. Type</label>
                <select name="calculation_type" id="calculation_type" class="form-control req">
                    <option value="">Select Calcu.Type</option>
                    <option value="1" <?=(!empty($dataRow->calculation_type) && $dataRow->calculation_type == 1)?"selected":""?>>Basic Amount</option>
                    <option value="2" <?=(!empty($dataRow->calculation_type) && $dataRow->calculation_type == 2)?"selected":""?>>Net Amount</option>
                    <option value="0" <?=(!empty($dataRow->id) && $dataRow->calculation_type == 0)?"selected":""?>>Qty</option>
                </select>
            </div>
            <div class="col-md-2 form-group">
                <label for="def_per">Def. Per.</label>
                <input type="text" name="def_per" id="def_per" class="form-control floatOnly" value="<?=(!empty($dataRow->def_per))?$dataRow->def_per:""?>">
            </div>
            <div class="col-md-4 form-group">
                <label for="acc_id">Ledger Name</label>
                <select name="acc_id" id="acc_id" class="form-control select2 req">
                    <option value="">Select Ledger Name</option>
                    <?php
                        foreach ($ledgerData as $row) :
                            $selected = (!empty($dataRow->acc_id) && $dataRow->acc_id == $row->id) ? "selected" : "";
                            echo '<option value="' . $row->id . '" ' . $selected . '>' . $row->party_name . '</option>';
                        endforeach;
                    ?>
                </select>
            </div>
            <div class="col-md-4 form-group">
                <label for="add_or_deduct">Amount Effect</label>
                <select name="add_or_deduct" id="add_or_deduct" class="form-control req">
                    <option value = "">Select Amount Effect</option>
                    <option value="1" <?=(!empty($dataRow->add_or_deduct) && $dataRow->add_or_deduct == "1")?"selected":""?>>Add </option>
                    <option value="-1" <?=(!empty($dataRow->add_or_deduct) && $dataRow->add_or_deduct == "-1")?"selected":""?>>Deduct</option>
                </select>
            </div>
            <div class="col-md-4 form-group">
                <label for="is_active">Is Active</label>
                <select name="is_active" id="is_active" class="form-control req">
                    <option value="1" <?=(!empty($dataRow->is_active) && $dataRow->is_active == 1)?"selected":""?>>Active</option>
                    <option value="0" <?=(!empty($dataRow->id) && $dataRow->is_active == 0)?"selected":""?>>In Active </option>
                </select>
            </div>
        </div>
    </div>
</form>