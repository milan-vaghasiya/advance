<form>
    <div class="col-md-12">
        <div class="row">
            <input type="hidden" name="id" id="id" value="<?=(!empty($dataRow->id))?$dataRow->id:""; ?>" />
           <input type="hidden" name="party_category" id="party_category" value="<?=(!empty($dataRow->party_category))?$dataRow->party_category:$party_category?>" />

            <div class="col-md-12 form-group">
                <label for="party_name">Ladger Name</label>
                <input type="text" name="party_name" class="form-control text-capitalize req" value="<?=(!empty($dataRow->party_name))?$dataRow->party_name:""; ?>" />
            </div>

            <div class="col-md-8 form-group">
                <label for="group_id">Group Name</label>
                <select name="group_id" id="group_id" class="form-control select2 req" data-selected_group_id="<?=!empty($dataRow->group_id)?$dataRow->group_id:""?>">
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
                <label for="is_gst_applicable">Gst Applicable</label>
                <select name="is_gst_applicable" id="is_gst_applicable" class="form-control req" >
                    <option value="0" <?=(!empty($dataRow->is_gst_applicable) && $dataRow->is_gst_applicable == 0)?"selected":""?>>No</option>
                    <option value="1" <?=(!empty($dataRow->is_gst_applicable) && $dataRow->is_gst_applicable == 1)?"selected":""?>>Yes</option>
                </select>
            </div>

            <div class="col-md-4 form-group applicable <?=(empty($dataRow->is_gst_applicable))?'hidden':'';?>">
                <label for="hsn_code">Hsn Code</label>
                <select name="hsn_code" id="hsn_code" class="form-control select2">
                    <option value="">Select HSN Code</option>
                    <?=getHsnCodeListOption($hsnList)?>
                </select>
            </div>

            <div class="col-md-4 form-group applicable <?=(empty($dataRow->is_gst_applicable))?'hidden':'';?>" >
                <label for="gst_per">GST Per.</label>
                <select name="gst_per" id="gst_per" class="form-control select2">
                    <?php
                        foreach($this->gstPer as $per=>$text):
                            $selected = (!empty($dataRow->gst_per) && floatVal($dataRow->gst_per) == $per)?"selected":"";
                            echo '<option value="'.$per.'" '.$selected.'>'.$text.'</option>';
                        endforeach;
                    ?>
                </select>
            </div>

            <div class="col-md-4 form-group applicable <?=(empty($dataRow->is_gst_applicable))?'hidden':'';?>" >
                <label for="cess_per">Cess Per.</label>
                <input type="text" name="cess_per" class="form-control numericOnly" value="<?=(!empty($dataRow->cess_per))?$dataRow->cess_per:""?>" />
            </div>           

            <div class="col-md-4 form-group taxType">
                <label for="tax_type">Tax Type</label>
                <select name="tax_type" id="tax_type" class="form-control">
                    <option value="">Select TAX Type</option>
                    <option value="GST" <?=(!empty($dataRow->tax_type) && $dataRow->tax_type == "GST")?"selected":""?>>GST</option>
                    <option value="TDS" <?=(!empty($dataRow->tax_type) && $dataRow->tax_type == "TDS")?"selected":""?>>TDS</option>
                    <option value="CST" <?=(!empty($dataRow->tax_type) && $dataRow->tax_type == "CST")?"selected":""?>>CST</option>
                    <option value="OTHER" <?=(!empty($dataRow->tax_type) && $dataRow->tax_type == "OTHER")?"selected":""?>>OTHER</option>
                </select>
            </div>

            <div class="col-md-4 form-group tcsApplicable">
                <label for="tcs_applicable">TCS</label>
                <select name="tcs_applicable" id="tcs_applicable" class="form-control">
                    <option value="NO" <?=(!empty($dataRow->tcs_applicable) && $dataRow->tcs_applicable == "NO")?"selected":""?>>NO</option>
                    <option value="YES-SALES" <?=(!empty($dataRow->tcs_applicable) && $dataRow->tcs_applicable == "YES-SALES")?"selected":""?>>YES-SALES</option>
                </select>
            </div>

            <div class="col-md-4 form-group tdsApplicable">
                <label for="tds_applicable">TDS</label>
                <select name="tds_applicable" id="tds_applicable" class="form-control">
                    <option value="NO" <?=(!empty($dataRow->tds_applicable) && $dataRow->tds_applicable == "NO")?"selected":""?>>NO</option>
                    <option value="YES-FROM-START" <?=(!empty($dataRow->tds_applicable) && $dataRow->tds_applicable == "YES-FROM-START")?"selected":""?>>YES-FROM-START</option>
                    <option value="YES-FROM-LIMIT" <?=(!empty($dataRow->tds_applicable) && $dataRow->tds_applicable == "YES-FROM-LIMIT")?"selected":""?>>YES-FROM-LIMIT</option>
                </select>
            </div>

            <div class="col-md-4 form-group tdsInputs">
                <label for="tds_per">TDS Per. (%)</label>
                <input type="text" name="tds_per" id="tds_per" class="form-control floatOnly" value="<?=(!empty($dataRow->tds_per))?$dataRow->tds_per:""?>">
            </div>

            <div class="col-md-12 form-group tdsInputs tdsAccount">
                <label for="tds_acc_id">TDS Account</label>
                <select name="tds_acc_id" id="tds_acc_id" class="form-control select2">
                    <option value="">Select Account</option>
                    <?=getPartyListOption($ledgerList,((!empty($dataRow->tds_acc_id))?$dataRow->tds_acc_id:""))?>
                </select>
            </div>

            <div class="col-md-12 form-group tdsInputs">
                <label for="tds_class_id">TDS Class</label>
                <select name="tds_class_id" id="tds_class_id" class="form-control">
                    <option value="">Select</option>
                    <?=getTDSClassListOptions($tdsClassList,((!empty($dataRow->tds_class_id))?$dataRow->tds_class_id:""))?>
                </select>
            </div>
        </div>
    </div>
</form>

<script type="text/javascript">
$(document).ready(function(){
    $("label[for='tds_class_id']").html("TDS Class");
    $("#tds_class_id option[data-class_type='N']").show();
    $("#tds_class_id option[data-class_type='D']").hide();
    $(".taxType,.tdsInputs,.tdsAccount").hide();
    
    var selected_group_id = $("#group_id").val();
    setTimeout(function(){
        $("#group_id").trigger('change');
    },500);

    $(document).on('change','#is_gst_applicable',function(){
		var is_gst_applicable = $(this).val();
		if(is_gst_applicable == 1){
			$('.applicable').removeClass('hidden');
		} else {
            $('.applicable').addClass('hidden');
        }
	});

    $(document).on('change','#hsn_code',function(){
		$("#gst_per").val(($("#hsn_code :selected").data('gst_per') || 0));
		$("#gst_per").select2();
	});

    $(document).on('change','#group_id',function(){
        var groupCode = $(this).find(':selected').data('group_code');
        var selected_group_id = $(this).data('selected_group_id');

        if(selected_group_id != $(this).val()){
            $("#tds_per,#tds_acc_id,#tds_class_id,#tax_type").val("");
            $("#tds_applicable,#tcs_applicable").val("NO");
        }

        if(groupCode == "DT"){
            $(".taxType,.tdsInputs").show();
            $(".tdsApplicable,.tcsApplicable,.tdsAccount").hide();

            $("label[for='tds_class_id']").html("Nature of Pymt.");
            $("#tds_class_id option[data-class_type='N']").show();
            $("#tds_class_id option[data-class_type='D']").hide();
        }else{
            if($(this).val() != ""){
                $(".taxType").hide();
                $(".tdsApplicable,.tcsApplicable").show();
                
                $("label[for='tds_class_id']").html("TDS Class");
                $("#tds_class_id option[data-class_type='N']").hide();
                $("#tds_class_id option[data-class_type='D']").show();
                $("#tds_applicable").trigger('change');
            }
        }
        
        initSelect2();
    });

    $(document).on('change','#tds_applicable',function(){
        var tds = $(this).val();
        if(tds == "NO"){
            $("#tds_per").val("");
            $("#tds_acc_id").val("");
            $("#tds_class_id").val("");
            
            $(".tdsInputs").hide();
            initSelect2();
        }else{
            $(".tdsInputs,.tdsAccount").show();
            initSelect2();
        }        
    });
});
</script>
