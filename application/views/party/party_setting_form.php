<form>
    <div class="col-md-12">
        <div class="row">
            <input type="hidden" name="id" id="id" value="<?=(!empty($dataRow->id))?$dataRow->id:""?>">
            <input type="hidden" name="form_type" value="settings">

            <div class="col-md-4 form-group">
                <label for="group_id">Group Name</label>
                <select name="group_id" id="group_id" class="form-control select2 req">
                    <option value="">Select Group</option>
                    <?php
                        foreach($groupList as $row):
                            $selected = (!empty($dataRow->group_id) && $row->id == $dataRow->group_id)?"selected":"";
                            echo "<option value='".$row->id."' data-group_code='".$row->group_code."' ".$selected.">".$row->name."</option>";
                        endforeach;
                    ?>
                </select>
            </div>

            <div class="col-md-4 form-group">
                <label for="tds_applicable">TDS</label>
                <select name="tds_applicable" id="tds_applicable" class="form-control select2">
                    <option value="NO" <?=(!empty($dataRow->tds_applicable) && $dataRow->tds_applicable == "NO")?"selected":""?>>NO</option>
                    <option value="YES-FROM-START" <?=(!empty($dataRow->tds_applicable) && $dataRow->tds_applicable == "YES-FROM-START")?"selected":""?>>YES-FROM-START</option>
                    <option value="YES-FROM-LIMIT" <?=(!empty($dataRow->tds_applicable) && $dataRow->tds_applicable == "YES-FROM-LIMIT")?"selected":""?>>YES-FROM-LIMIT</option>
                </select>
            </div>

            <div class="col-md-4 form-group tdsInputs">
                <label for="tds_per">TDS Per. (%)</label>
                <input type="text" name="tds_per" id="tds_per" class="form-control floatOnly" value="<?=(!empty($dataRow->tds_per))?$dataRow->tds_per:""?>">
            </div>

            <div class="col-md-6 form-group tdsInputs">
                <label for="tds_acc_id">TDS Account</label>
                <select name="tds_acc_id" id="tds_acc_id" class="form-control select2">
                    <option value="">Select Account</option>
                    <?=getPartyListOption($ledgerList,((!empty($dataRow->tds_acc_id))?$dataRow->tds_acc_id:""))?>
                </select>
            </div>

            <div class="col-md-6 form-group tdsInputs">
                <label for="tds_class_id">TDS Class</label>
                <select name="tds_class_id" id="tds_class_id" class="form-control select2">
                    <option value="">Select Class</option>
                    <?=getTDSClassListOptions($tdsClassList,((!empty($dataRow->tds_class_id))?$dataRow->tds_class_id:""))?>
                </select>
            </div>

            <div class="col-md-4 form-group">
                <label for="tcs_applicable">TCS</label>
                <select name="tcs_applicable" id="tcs_applicable" class="form-control select2">
                    <option value="NO" <?=(!empty($dataRow->tcs_applicable) && $dataRow->tcs_applicable == "NO")?"selected":""?>>NO</option>
                    <option value="YES-SALES" <?=(!empty($dataRow->tcs_applicable) && $dataRow->tcs_applicable == "YES-SALES")?"selected":""?>>YES-SALES</option>
                </select>
            </div>        
         
            <div class="col-md-4 form-group <?=($dataRow->party_category != 3)?"hidden":""?>">
                <label for="mhr">Machine Hourly Cost</label>
                    <input type="text" name="mhr" id="mhr" class="form-control" value="<?=(!empty($dataRow->mhr))?$dataRow->mhr:""?>" />
            </div>
        </div>
    </div>
</form>

<script>
    setTimeout(function(){
        $("#tds_applicable").trigger('change');
    },500);

    $(document).on('change','#tds_applicable',function(){
        var tds = $(this).val();
        if(tds == "NO"){
            $("#tds_per").val("");
            $("#tds_acc_id").val("");
            $("#tds_class_id").val("");
            
            $(".tdsInputs").hide();
            initSelect2();
        }else{
            $(".tdsInputs").show();

            $("#tds_class_id option[data-class_type='N']").hide();
            initSelect2();
        }
    });

    
</script>