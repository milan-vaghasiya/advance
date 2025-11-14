<form>
    <div class="col-md-12">
        <div class="row">
            <input type="hidden" name="id" value="<?= (!empty($dataRow->id)) ? $dataRow->id : ""; ?>" />
            <input type="hidden" name="trans_no" value="<?= (!empty($dataRow->trans_no)) ? $dataRow->trans_no : $trans_no; ?>" />
            <input type="hidden" name="trans_prefix" value="<?= (!empty($dataRow->trans_prefix)) ? $dataRow->trans_prefix : $trans_prefix; ?>" />
            <input type="hidden" name="entry_type" value="<?= (!empty($dataRow->entry_type)) ? $dataRow->entry_type : $entry_type; ?>" />
			<input type="hidden" name="so_trans_id" value="<?= ((!empty($dataRow->so_trans_id)) ? $dataRow->so_trans_id : (!empty($so_trans_id) ? $so_trans_id : 0)) ?>" />

            <div class="col-md-6 form-group">
                <label for="trans_number">Indent No.</label>
                <input type="text" name="trans_number" id="trans_number" class="form-control req" value="<?= (!empty($dataRow->trans_number)) ? $dataRow->trans_number : $trans_number ?>" readonly>
            </div>
           
            <div class="col-md-6 form-group">
                <label for="trans_date">Indent Date</label>
                <input type="date" name="trans_date" id="trans_date" class="form-control" value="<?= (!empty($dataRow->trans_date)) ? $dataRow->trans_date : getFyDate() ?>" />
            </div>

			<div class="col-md-12 form-group">
                <label for="item_id">Item </label>
                <select name="item_id" id="item_id" class="form-control select2 req">
                    <option value="">Select Item</option>
                    <?php 
                        foreach ($itemList as $row) :
                            $selected = (!empty($dataRow->item_id) && $dataRow->item_id == $row->id) ? "selected" : ((!empty($item_id) && $item_id == $row->id)? "selected":'');
                            $disabled = (!empty($dataRow->item_id) && $dataRow->item_id != $row->id && !empty($dataRow->so_trans_id)) ? "disabled" : ((!empty($item_id) && $item_id != $row->id && !empty($so_trans_id))? "disabled":'');
							
							$row->item_name = (!empty($row->item_code)) ? '['.$row->item_code.'] '.$row->item_name : $row->item_name;
                            echo '<option value="'. $row->id .'" data-item_type="'.$row->item_type.'" '.$selected.' '.$disabled.'>'.$row->item_name.'</option>';
                        endforeach;
                    ?>
                </select>
            </div>

            <div class="col-md-12 form-group">
                <label for="fg_item_id">Finish Goods</label>
                <select name="fg_item_id" id="fg_item_id" class="form-control select2">
					<?=(!empty($fgoption) ? $fgoption : '<option value="">Select Finish Goods</option>')?>
                </select>
            </div>
            
            <div class="col-md-6 form-group">
                <label for="qty">Qty</label>
                <input type="text" name="qty" id="qty" class="form-control req" value="<?= (!empty($dataRow->qty)) ? $dataRow->qty : ((!empty($qty))?$qty:'') ?>">
            </div>
            <div class="col-md-6 form-group">
                <label for="delivery_date">Delivery Date</label>
                <input type="date" name="delivery_date" id="delivery_date" class="form-control" value="<?= (!empty($dataRow->delivery_date)) ? $dataRow->delivery_date : getFyDate() ?>" />
            </div>

            <div class="col-md-12 form-group">
                <label for="remark">Remark</label>
                <input type="text" name="remark" id="remark" class="form-control" rows="1" value="<?= (!empty($dataRow->remark)) ? $dataRow->remark : "" ?>">
            </div>
        </div>
    </div>
</form>
<script>
$(document).ready(function() {
    $(document).on('change','#item_id',function(e){
        e.stopImmediatePropagation();e.preventDefault();
		var item_id = $(this).val();
		var item_type = $('#item_id :selected').data('item_type');
		$.ajax({
			url: base_url + controller + '/getItemWiseFgList',
			type:'post',
			data:{ item_id:item_id,item_type:item_type },
			dataType:'json',
			success:function(data){
				$("#fg_item_id").html(data.fgoption);
				initSelect2();
			}
		});
	});
});
</script>
