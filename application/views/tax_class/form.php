<form>
    <div class="col-md-12">
        <div class="row">
            <input type="hidden" name="id" id="id" class="form-control" value="<?=(!empty($dataRow->id))?$dataRow->id:""?>">

            <div class="col-md-2 form-group">
                <label for="sp_type">Class Type</label>
                <select name="sp_type" id="sp_type" class="form-control">
                    <option value="1" <?=(!empty($dataRow->sp_type) && $dataRow->sp_type == 1)?"selected":""?>>Purchase</option>
                    <option value="2"  <?=(!empty($dataRow->sp_type) && $dataRow->sp_type == 2)?"selected":""?>>Sales</option>
                </select>
            </div>

            <div class="col-md-2 form-group">
                <label for="tax_class">Class Code</label>
                <select name="tax_class" id="tax_class" class="form-control select2 req" data-selected="<?=(!empty($dataRow->tax_class))?$dataRow->tax_class:""?>">
                    <option value="">Select</option>
                </select>
            </div>

            <div class="col-md-4 form-group">
                <label for="tax_class_name">Class Name</label>
                <input type="text" name="tax_class_name" id="tax_class_name" class="form-control req" value="<?=(!empty($dataRow->tax_class_name))?$dataRow->tax_class_name:""?>">
            </div>

            <div class="col-md-4 form-group">
                <label for="sp_acc_id">Ledger Name</label>
                <select name="sp_acc_id" id="sp_acc_id" class="form-control select2 req">
                    <option value="">Select Ledger</option>
                    <?=getPartyListOption($ledgerList,((!empty($dataRow->sp_acc_id))?$dataRow->sp_acc_id:""))?>
                </select>
            </div>           

            <div class="col-md-4 form-group">
                <label for="tax_ids">Tax Name</label>
                <select id="tax_ids_selection" data-input_id="tax_ids" class="form-control jp_multiselect req" multiple="multiple"></select>
                <input type="hidden" name="tax_ids" id="tax_ids" value="<?= (!empty($dataRow->tax_ids)) ? $dataRow->tax_ids : "" ?>" />
                <div class="error tax_ids"></div>
            </div>

            <div class="col-md-4 form-group">
                <label for="expense_ids">Expense Name</label>
                <select id="expense_ids_selection" data-input_id="expense_ids" class="form-control jp_multiselect req" multiple="multiple"></select>
                <input type="hidden" name="expense_ids" id="expense_ids" value="<?= (!empty($dataRow->expense_ids)) ? $dataRow->expense_ids : "" ?>" />
                <div class="error expense_ids"></div>
            </div>

            <div class="col-md-2 form-group">
                <label for="is_defualt">Is Defualt</label>
                <select name="is_defualt" id="is_defualt" class="form-control req">
                    <option value="0" <?=(!empty($dataRow->id) && $dataRow->is_defualt == 0)?"selected":""?>>NO</option>
                    <option value="1" <?=(!empty($dataRow->is_defualt) && $dataRow->is_defualt == 1)?"selected":""?>>YES</option>
                </select>   
            </div>

            <div class="col-md-2 form-group">
                <label for="is_active">Is Active</label>
                <select name="is_active" id="is_active" class="form-control req">
                    <option value="1" <?=(!empty($dataRow->is_active) && $dataRow->is_active == 1)?"selected":""?>>Active</option>
                    <option value="0" <?=(!empty($dataRow->id) && $dataRow->is_active == 0)?"selected":""?>>In Active </option>
                </select>   
            </div>
        </div>
    </div>
</form>
<script>
var taxClassCodes = <?=json_encode($this->taxClassCodes)?>;

$(document).ready(function(){
    setTimeout(function(){
        $("#sp_type").trigger('change');
    },500);

    $(document).on('change','#sp_type',function(){
        var type = $(this).val();

        $("#tax_class").html("");
        var tax_class_options = '<option value="">Select</option>';
        $.each(taxClassCodes[type],function(value,text){
            tax_class_options += '<option value="'+value+'">'+text+'</option>';
        });
        $("#tax_class").html(tax_class_options);

        $("#tax_class").val($("#tax_class").data('selected'));
        initSelect2();

        $.ajax({
            url : base_url + controller + '/getTaxClassAccountList',
            type : 'post',
            data : {type : type},
            dataType : 'json'
        }).done(function(response){
            var taxList = response.data.taxList;
            var expenseList = response.data.expenseList;

            $("#tax_ids_selection").html("");
            var taxListoptions = '';
            $.each(taxList,function(key,row){
                taxListoptions += '<option value="'+row.id+'">'+row.name+'</option>';
            });
            $("#tax_ids_selection").html(taxListoptions);

            $("#expense_ids_selection").html("");
            var expenseListoptions = '';
            $.each(expenseList,function(key,row){
                expenseListoptions += '<option value="'+row.id+'">'+row.exp_name+'</option>';
            });
            $("#expense_ids_selection").html(expenseListoptions);

            var tax_ids = $("#tax_ids").val();
            var expense_ids = $("#expense_ids").val();

            if(tax_ids != ""){
                tax_ids = tax_ids.split(','); 
                $("#tax_ids_selection").val(tax_ids);
            }

            if(expense_ids != ""){
                expense_ids = expense_ids.split(','); 
                $("#expense_ids_selection").val(expense_ids);
            }

            reInitMultiSelect();
        });
    });
});
</script>