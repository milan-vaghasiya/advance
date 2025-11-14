<form>
    <div class="col-md-12">
        <div class="row">
            <input type="hidden" name="id" value="<?=(!empty($dataRow->id))?$dataRow->id:""; ?>" />
            <input type="hidden" name="category_level" id="maincate_level" value="" />
            <input type="hidden" name="category_type" id="category_type" value="<?=(!empty($dataRow->category_type))?$dataRow->category_type:""; ?>" />
			<div class="col-md-8 form-group">
                <label for="category_name">Category Name</label>
                <input type="text" name="category_name" class="form-control req" value="<?=(!empty($dataRow->category_name))?$dataRow->category_name:""?>" />
            </div>
			<div class="col-md-4 form-group">
                <label for="final_category">Final Category</label>
                <select name="final_category" id="final_category" class="form-control">
                    <option value="0" <?=(!empty($dataRow) && $dataRow->final_category == 0) ? "selected" : "";?>>No</option>
                    <option value="1" <?=(!empty($dataRow) && $dataRow->final_category == 1) ? "selected" : "";?>>Yes</option>
                </select>
            </div>
			
			<div class="col-md-8 form-group">
                <label for="ref_id">Parent Category</label>
                <select name="ref_id" id="ref_id" class="form-control select2 req">
                    <option value="0">Select</option>
                    <?php
                        foreach ($mainCategory as $row) :
                            if($row->id != $dataRow->id):
                                $selected = (!empty($dataRow->ref_id) && $dataRow->ref_id == $row->id) ? "selected" : ((!empty($ref_id) && $ref_id == $row->id)?"selected":"");
                                echo '<option value="' . $row->id . '" class="level_'.$row->category_level.'" data-level="'.$row->category_level.'" data-category_type="'.$row->category_type.'" ' . $selected . '>' . $row->category_name . '</option>';
                            endif;
                        endforeach;
                    ?>
                </select>
            </div>
            <div class="col-md-4 form-group ">
                <label for="category_code">Code</label>
                <input type="text" name="category_code" class="form-control req" value="<?=(!empty($dataRow->category_code))?$dataRow->category_code:""?>" />
            </div>
            <div class="col-md-6 form-group returnable">
                <label for="is_return">Is Returnable?</label>
                <select name="is_return" id="is_return" class="form-control modal-select2">
                    <option value="0" <?=(!empty($dataRow) && $dataRow->is_return == 0) ? "selected" : "";?>>No</option>
                    <option value="1" <?=(!empty($dataRow) && $dataRow->is_return == 1) ? "selected" : "";?>>Yes</option>
                </select>
            </div>
            
			<div class="col-md-6 form-group">
                <label for="is_inspection">Dimensional Inspection</label>
                <select name="is_inspection" id="is_inspection" class="form-control modal-select2">
                    <option value="0" <?=(!empty($dataRow) && $dataRow->is_inspection == 0) ? "selected" : "";?>>No</option>
                    <option value="1" <?=(!empty($dataRow) && $dataRow->is_inspection == 1) ? "selected" : "";?>>Yes</option>
                </select>
            </div>

            

			<div class="col-md-4 form-group hidden">
                <label for="batch_stock">Stock Type</label>
                <select name="batch_stock" id="batch_stock" class="form-control">
                    <?php
                        foreach($this->stockTypes as $key => $name):
                            $selected = (!empty($dataRow->batch_stock) && $dataRow->batch_stock == $key)?"selected":"";
                            echo '<option value="'.$key.'" '.$selected.' >'.$name.'</option>';
                        endforeach;
                    ?>
                </select>
            </div>
            
            <div class="col-md-12 form-group">
                <label for="remark">Remark</label>
                <textarea name="remark" class="form-control" rows="3" ><?=(!empty($dataRow->remark))?$dataRow->remark:"";?></textarea>
            </div>
        </div>
    </div>
</form>
<script type="text/javascript">
$(document).ready(function(){
    setTimeout(function(){$("#ref_id").trigger('change')},1000);

    var ctype = $('#category_type').val();
    if(ctype == 6  || ctype == 11){$('.toolType').show();$('.returnable').hide();}
	else{$('.returnable').show();$('.toolType').hide();}

    $(document).on('change','#ref_id',function(){
		var ref_id = $(this).val();
		var level = $('#ref_id :selected').data('level'); 
		var category_type = $('#ref_id :selected').data('category_type'); 
        $('#maincate_level').val(level);
		$('#category_type').val(category_type);
		
		if(category_type == 6  || ctype == 11){$('.toolType').show();$('.returnable').hide();}
		else{$('.returnable').show();$('.toolType').hide();}
	});
});
</script>


