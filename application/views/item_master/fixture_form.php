<form class="itemMasterForm" enctype="multipart/form-data">
    <div class="col-md-12">
        <div class="row">
            <input type="hidden" name="id" id="id" value="<?=(!empty($dataRow->id))?$dataRow->id:""?>">
            <input type="hidden" name="item_type" id="item_type" value="<?=(!empty($dataRow->item_type))?$dataRow->item_type:$item_type?>">

            <div class="col-md-4 form-group">
                <label for="item_code">Item Code</label>
                <input type="text" name="item_code" class="form-control" value="<?=(!empty($dataRow->item_code))?$dataRow->item_code:""; ?>" />
            </div>
			
            <div class="col-md-8 form-group">
                <label for="item_name">Item Name</label>
				<input type="text" name="item_name" class="form-control req" value="<?=htmlentities((!empty($dataRow->item_name)) ? $dataRow->item_name : "")?>" />      
            </div>

            <div class="col-md-4 form-group">
                <label for="category_id">Category</label>
                <select name="category_id" id="category_id" class="form-control select2 req">
                    <option value="0">Select</option>
                    <?php
                        foreach ($categoryList as $row) :
                            $selected = (!empty($dataRow->category_id) && $dataRow->category_id == $row->id) ? "selected" : "";
                            echo '<option value="' . $row->id . '" ' . $selected . '>' . $row->category_name . '</option>';
                        endforeach;
                    ?>
                </select>
            </div> 

            <div class="col-md-5 form-group">
                <label for="fg_id">Product</label>
                <select name="fg_id[]" id="fg_id" class="form-control select2 req" multiple>
                    <?php
                        foreach ($fgList as $row) :
                            $selected = (!empty($dataRow->fg_id) && in_array($row->id, explode(',', $dataRow->fg_id))) ? "selected" : "";
                            echo '<option value="' . $row->id . '" ' . $selected . '>' . $row->item_name . '</option>';
                        endforeach;
                    ?>
                </select>
            </div> 

            <div class="col-md-3 form-group">
                <label for="process_id">Process</label>
                <select name="process_id[]" id="process_id" class="form-control select2 req" multiple>
                <?=(!empty($processData) ? $processData : '')?>
                </select>
            </div> 

            <div class="col-md-3 form-group">
                <label for="price">Price</label>
                <input type="text" name="price" class="form-control floatOnly" value="<?= (!empty($dataRow->price)) ? $dataRow->price : "0" ?>" />
            </div>
            <div class="col-md-3 form-group">
                <label for="uom">UOM</label>
                <select name="uom" id="uom" class="form-control select2">
                    <option value="0">--</option>
                    <?=getItemUnitListOption($unitData,((!empty($dataRow->uom))?$dataRow->uom:""))?>
                </select>
            </div>
            
            <div class="col-md-3 form-group">
                <label for="hsn_code">HSN Code</label>
                <select name="hsn_code" id="hsn_code" class="form-control select2">
                    <option value="">Select HSN Code</option>
                    <?=getHsnCodeListOption($hsnData,((!empty($dataRow->hsn_code))?$dataRow->hsn_code:""))?>
                </select>
            </div>

            <div class="col-md-3 form-group">
                <label for="gst_per">GST (%)</label>
                <select name="gst_per" id="gst_per" class="form-control calMRP select2">
                    <?php
                        foreach($this->gstPer as $per=>$text):
                            $selected = (!empty($dataRow->gst_per) && floatVal($dataRow->gst_per) == $per)?"selected":"";
                            echo '<option value="'.$per.'" '.$selected.'>'.$text.'</option>';
                        endforeach;
                    ?>
                </select>
            </div>

            <div class="col-md-12 form-group">
                <label for="description">Description</label>
                <textarea name="description" id="description" class="form-control" rows="1"><?=(!empty($dataRow->description))?$dataRow->description:""?></textarea>
            </div>
           
        </div>
    </div>
</form>
<script>
$(document).ready(function(){
	$(document).on('change','#fg_id',function(e){
        e.stopImmediatePropagation();e.preventDefault();
		var fg_id = $('#fg_id').val(); 
		$.ajax({
			url : base_url + controller + '/getProcessList',
			type:'post',
			data:{ fg_id:fg_id},
			dataType:'json',
			success:function(data){
				$("#process_id").html(data.processOption);
				initSelect2();
			}
		});
	});
});
    </script>
