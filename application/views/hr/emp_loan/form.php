<form autocomplete="off">
    <div class="col-md-12">
        <div class="row">
            <input type="hidden" name="id" value="<?=(!empty($dataRow->id))?$dataRow->id:""; ?>" />
            <input type="hidden" name="vou_acc_id" value="<?=(!empty($dataRow->vou_acc_id))?$dataRow->vou_acc_id:""; ?>" />
            <div class="col-md-4">
                <label for="entry_date"> Date</label>
                <input type="date" name="entry_date" id="entry_date" class="form-control" value="<?=(!empty($dataRow->trans_date))?$dataRow->trans_date:date("Y-m-d")?>" max="<?=(!empty($dataRow->entry_date))?$dataRow->entry_date:date("Y-m-d")?>" />
            </div>

            <div class="col-md-8 form-group">
                <label for="emp_id">Employee</label>
                <select name="emp_id" id="emp_id" class="form-control select2 req">
                    <option value="">Select Employee</option>
                    <?php
                        foreach($empData as $row):
                            $selected = (!empty($dataRow->emp_id) && $row->id == $dataRow->emp_id)?"selected":"";
                            echo '<option value="'.$row->id.'" '.$selected.'>['.$row->emp_code.'] '.$row->emp_name.'</option>';
                        endforeach;
                    ?>
                </select>
                <div class="error emp_id"></div>
            </div>

            <div class="col-md-3 form-group">
                <label for="payment_mode">Payment Mode</label>
                <select name="payment_mode" id="payment_mode" class="form-control select2">
                    <option value="CS" <?=(!empty($dataRow->payment_mode) && $dataRow->payment_mode == 'CS')?"selected":"";?>>CASH</option>
                    <option value="BA" <?=(!empty($dataRow->payment_mode) && $dataRow->payment_mode == 'BA')?"selected":"";?>>BANK</option>
                </select>
            </div>

            <?php
            if(empty($approve_type)){
                ?>
                <div class="col-md-3 form-group">
                    <label for="demand_amount">Loan Demand</label>
                    <input type="text" name="demand_amount" id="demand_amount" class="form-control numericOnly req calEMI" value="<?=(!empty($dataRow->demand_amount))?$dataRow->demand_amount:""?>" />
                </div>
                <?php
            }else{
                ?>
                <div class="col-md-3 form-group">
                    <label for="approved_amount">Approved Amount</label>
                    <input type="text" name="approved_amount" id="approved_amount" class="form-control numericOnly req calEMI" value="<?=(!empty($dataRow->demand_amount))?$dataRow->demand_amount:""?>" />
                </div>
                <?php
            }
            ?>
            
            <div class="col-md-3 form-group">
                <label for="total_emi">Total EMI</label>
                <input type="text" name="total_emi" id="total_emi" class="form-control numericOnly req calEMI" value="<?=(!empty($dataRow->total_emi))?$dataRow->total_emi:""?>" />
            </div>

            <div class="col-md-3 form-group">
                <label for="emi_amount">EMI Amount</label>
                <input type="text" name="emi_amount" id="emi_amount" class="form-control req calEMI floatOnly" value="<?=(!empty($dataRow->emi_amount))?$dataRow->emi_amount:""?>" />
            </div>
            
            <div class="col-md-12 form-group">
                <label for="reason">Reason</label>
                <textarea name="reason" class="form-control req" style="resize:none;" ><?=(!empty($dataRow->reason))?$dataRow->reason:""?></textarea>
            </div>
    
        </div>
    </div>
</form>
<script>
$(document).ready(function(){
    $(document).on('change',".calEMI",function(){   
        
        if($(this).prop('id') == 'emi_amount'){$("#total_emi").val('');}
        if($(this).prop('id') == 'total_emi'){$("#emi_amount").val('');}
        var amount = 0;
        
        <?php if(empty($approve_type)){ ?>
            amount = parseFloat($("#demand_amount").val());
        <?php }else{ ?>
            amount = parseFloat($("#approved_amount").val());
        <?php } ?>
        
		var total_emi = parseFloat($("#total_emi").val());if(!total_emi){total_emi=0;}
		var emi_amount = parseFloat($("#emi_amount").val());if(!emi_amount){emi_amount=0;}
        var emiAmt = 0;var totalEmi = 0;
        if((total_emi > 0 || emi_amount > 0) && amount > 0)
        {
            if(emi_amount > 0){total_emi = parseFloat((parseFloat(amount) / parseFloat(emi_amount)).toFixed());}
            if(total_emi > 0){emi_amount = parseFloat((parseFloat(amount) / parseFloat(total_emi))).toFixed(2); }
        }
        $("#total_emi").val(total_emi);
        $("#emi_amount").val(emi_amount);
    });

});

</script>