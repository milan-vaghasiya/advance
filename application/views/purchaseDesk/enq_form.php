<form>
    <div class="col-md-12">
        <div class="row">
            <input type="hidden" name="id" id="id" value="<?= (!empty($dataRow->id)) ? $dataRow->id : ""; ?>" />
            <input type="hidden" name="req_id" id="req_id" value="<?= (!empty($dataRow->req_id)) ? $dataRow->req_id : ""; ?>" />
            <input type="hidden" name="entry_type" id="entry_type" value="<?= !empty($dataRow->entry_type)?$dataRow->entry_type:((!empty($entry_type)) ? $entry_type : '') ?>" />
            <input type="hidden" name="trans_no" id="trans_no" value="<?=(!empty($dataRow->trans_no))?$dataRow->trans_no:$trans_no?>" />
            <input type="hidden" name="trans_prefix" id="trans_prefix" value="<?=(!empty($dataRow->trans_prefix))?$dataRow->trans_prefix:$trans_prefix?>" />

            <div class="col-md-6 form-group">
                <label for="trans_number">Enquiry No.</label>
				<input type="text" name="trans_number" id="trans_number" class="form-control req" value="<?= (!empty($dataRow->trans_number)) ? $dataRow->trans_number : $trans_prefix.$trans_no ?>" readonly />
            </div>

			<div class="col-md-6 form-group">
				<label for="trans_date">Enquiry Date</label>
				<input type="date" id="trans_date" name="trans_date" class="form-control req" value="<?= (!empty($dataRow->trans_date)) ? $dataRow->trans_date : date("Y-m-d") ?>" />
			</div>

            <div class="col-md-12 form-group">
                <label for="party_id">Supplier Name</label>
                <select name="party_id[]" id="party_id" class="form-control select2 req" multiple="multiple"> 
                    <option value="">Select Supplier</option>
                    <?=getPartyListOption($partyList,((!empty($dataRow->party_id))?$dataRow->party_id:0))?>
                </select>
                <div class="error party_id"></div>
            </div>

            <div class="col-md-5 form-group">
                <label for="item_type">Item Type</label>
                <select name="item_type" id="item_type" class="form-control select2 req">
                    <option value="">Select Item Type</option>
                    <?php
                        foreach($categoryList as $row):
                            $selected = (!empty($dataRow->item_type) && $dataRow->item_type == $row->id) ? 'selected' : '';
                            echo '<option value="'.$row->id.'" '.$selected.'>'.$row->category_name.'</option>';
                        endforeach;
                    ?>
                </select>
            </div>

            <div class="col-md-7 form-group">
                <label for="item_id">Item Name</label>
                <select name="item_id" id="item_id" class="form-control select2 req">
                    <option value="">Select Item</option>
                    <option value="-1">New Item</option>
                    <?= (!empty($dataRow)) ? $itemData : '' ?>
                </select>
            </div>

            <div class="col-md-12 form-group newItem">
                <label for="item_name">New Item Name</label>
                <input type="text" name="item_name" id="item_name" class="form-control req" value="<?= (!empty($dataRow->item_name)) ? $dataRow->item_name : "" ?>" />
            </div>

            <div class="col-md-6 form-group">
                <label for="uom">Unit</label>
                <select name="uom" id="uom" class="form-control select2 req">
                    <option value="0">--</option>
                    <?php
                        foreach($unitData as $row):
                            $selected = (!empty($dataRow->uom) && $dataRow->uom == $row->unit_name) ? 'selected' : '';
                            $disabled = (!empty($dataRow->uom) && $dataRow->uom != $row->unit_name) ? 'disabled' : '';
                            echo '<option value="'.$row->unit_name.'" '.$selected.' '.$disabled.'>['.$row->unit_name.'] '.$row->description.'</option>';
                        endforeach;
                    ?>
                </select>
                <div class="error uom"></div>
            </div>

            <div class="col-md-6 form-group">
                <label for="qty">Quantity</label>
                <input type="text" name="qty" id="qty" class="form-control floatOnly req" value="<?= (!empty($dataRow->qty)) ? floatval($dataRow->qty) : 0 ?>">
            </div>

            <div class="col-md-12 form-group">
                <label for="item_remark">Remark</label>
                <textarea name="item_remark" id="item_remark" class="form-control" rows="2"><?= (!empty($dataRow->item_remark)) ? $dataRow->item_remark : "" ?></textarea>
            </div>

        </div>
    </div>
</form>
<script src="<?=base_url()?>assets/plugins/tinymce/tinymce.min.js?v=<?=time()?>"></script>
<script>
    $(document).ready(function(){
        initEditor({
            selector: '#item_remark',
            height: 400
        });

        $('.newItem').hide();
        var item_id = $('#item_id').val();
        if(item_id == '-1'){
            $('.newItem').show();
        }else{
            $('.newItem').hide();
        }
    });    
</script>