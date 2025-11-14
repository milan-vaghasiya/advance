<form>
    <div class="col-md-12">
        <div class="row">
            <input type="hidden" name="id" value="<?=(!empty($dataRow->id))?$dataRow->id:""; ?>" />
            <input type="hidden" name="entry_name" id="entry_name" value="<?=(!empty($dataRow->entry_name))?$dataRow->entry_name:""; ?>" />
			
            <div class="col-md-4 form-group">
                <label for="exp_name">Expense Name</label>
                <input type="text" name="exp_name" class="form-control req" value="<?=(!empty($dataRow->exp_name))?$dataRow->exp_name:""?>" />
            </div>

            <div class="col-md-4 form-group">
                <label for="entry_type">Entry Type</label>
                <select name="entry_type" id="entry_type" class="form-control req">
                    <option value = "">Select Entry Type</option>
                    <option value="1" <?=(!empty($dataRow->entry_type) && $dataRow->entry_type == 1)?"selected":""?>>Purchase</option>
                    <option value="2" <?=(!empty($dataRow->entry_type) && $dataRow->entry_type == 2)?"selected":""?>>Sales</option>
                </select>
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
            
            <div class="col-md-3 form-group">
                <label for="calc_type">Calcu.Type</label>
                <select name="calc_type" id="calc_type" class="form-control req">
                    <option value="">Select Calcu.Type</option>
                    <option value="1" <?=(!empty($dataRow->calc_type) && $dataRow->calc_type == 1)?"selected":""?>>Fixed </option>
                    <option value="2" <?=(!empty($dataRow->calc_type) && $dataRow->calc_type == 2)?"selected":""?>>Percentage</option>
                    <option value="3" <?=(!empty($dataRow->calc_type) && $dataRow->calc_type == 3)?"selected":""?>>Cumulative</option>
                </select>
            </div>

            <div class="col-md-3 form-group">
                <label for="def_per">Def.Per</label>
                <input type="text" name="def_per" class="form-control" value="<?=(!empty($dataRow->def_per))?$dataRow->def_per:""?>" />
            </div>

            <div class="col-md-3 form-group">
                <label for="calc_on">Calcu. ON</label>
                <select name="calc_on" id="calc_on" class="form-control req">
                    <option value="">Select Calc. On</option>
                    <option value="1" <?=(!empty($dataRow->calc_on) && $dataRow->calc_on == 1)?"selected":""?>>Amount </option>
                    <option value="2" <?=(!empty($dataRow->calc_on) && $dataRow->calc_on == 2)?"selected":""?>>Qty</option>
                </select>
            </div>

            <div class="col-md-3 form-group">
                <label for="add_or_deduct">Amount Effect</label>
                <select name="add_or_deduct" id="add_or_deduct" class="form-control req">
                    <option value = "">Select Amount Effect</option>
                    <option value="1" <?=(!empty($dataRow->add_or_deduct) && $dataRow->add_or_deduct == 1)?"selected":""?>>Add </option>
                    <option value="-1" <?=(!empty($dataRow->add_or_deduct) && $dataRow->add_or_deduct == -1)?"selected":""?>>Deduct</option>
                </select>
            </div>

            <div class="col-md-4 form-group">
                <label for="position">Position</label>
                <select name="position" id="position" class="form-control req">
                    <option value = "">Select Position</option>
                    <option value="1" <?=(!empty($dataRow->position) && $dataRow->position == 1)?"selected":""?>>Before Tax</option>
                    <option value="2" <?=(!empty($dataRow->position) && $dataRow->position == 2)?"selected":""?>>After Tax</option>
                </select>
            </div>

            <div class="col-md-4 form-group">
                <label for="seq">Sequence</label>
                <input type="text" name="seq" class="form-control" value="<?=(!empty($dataRow->seq))?$dataRow->seq:""?>" />
            </div>
			
            <div class="col-md-4 form-group">
                <label for="is_active">Is Active</label>
                <select name="is_active" id="is_active" class="form-control">
                    <option value="1" <?=(!empty($dataRow->is_active) && $dataRow->is_active == 1)?"selected":""?>>Active</option>
                    <option value="0" <?=(!empty($dataRow->id) && $dataRow->is_active == 0)?"selected":""?>>In Active </option>
                </select>
            </div>
            
        </div>
    </div>
</form>
<script>
    $(document).ready(function(){
        $(document).on("change","#entry_type",function(){
            var entry_name = $("#entry_type :selected").text();
            $("#entry_name").val(entry_name);
        });
    });
</script>