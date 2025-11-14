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

            <div class="col-md-3 form-group">
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
            
			<div class="col-md-3 form-group">
                <label for="uom">UOM</label>
                <select name="uom" id="uom" class="form-control select2 ">
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

            <div class="col-md-3 form-group">
                <label for="price">Price</label>
                <input type="text" name="price" id="price" class="form-control calMRP floatOnly" value="<?=(!empty($dataRow->price))?$dataRow->price:"0"?>">
            </div> 
            
			<div class="col-md-3 form-group">
                <label for="size">Size/Range</label>
                <input type="text" name="size" class="form-control floatOnly" value="<?= (!empty($dataRow->size)) ? $dataRow->size : "" ?>" />
            </div>

            <div class="col-md-3 form-group">
                <label for="make_brand">Make/Brand</label>
                <input type="text" name="make_brand" class="form-control" value="<?=(!empty($dataRow->make_brand))?$dataRow->make_brand:""?>" />
            </div>
            
            <div class="col-md-3 form-group">
                <label for="cal_required">Calibration Req?</label>
                <select name="cal_required" id="cal_required" class="form-control select2">
                    <option value="YES" <?=((!empty($dataRow->cal_required) && $dataRow->cal_required == 'YES') ? 'selected' : '')?>>YES</option>
                    <option value="NO" <?=((!empty($dataRow->cal_required) && $dataRow->cal_required == 'NO') ? 'selected' : '')?>>NO</option>
                </select>
            </div>
            
            <div class="col-md-4 form-group">
                <label for="cal_freq">Cali. Frequency (Month)</label>
                <input type="text" name="cal_freq" class="form-control numericOnly" value="<?=(!empty($dataRow->cal_freq))?$dataRow->cal_freq:""?>" />
            </div>
            
            <div class="col-md-4 form-group">
                <label for="cal_reminder">Cali.Reminder (Days Before)</label>
                <input type="text" name="cal_reminder" class="form-control numericOnly" value="<?=(!empty($dataRow->cal_reminder))?$dataRow->cal_reminder:""?>" />
            </div>
            
            <div class="col-md-4 form-group">
                <label for="permissible_error">Permissible Error</label>
                <input type="text" name="permissible_error" class="form-control" value="<?=(!empty($dataRow->permissible_error))?$dataRow->permissible_error:""?>" />
            </div>
            
            <div class="col-md-12 form-group">
                <label for="description">Description</label>
                <textarea name="description" id="description" class="form-control" rows="1"><?=(!empty($dataRow->description))?$dataRow->description:""?></textarea>
            </div>
            
        </div>
    </div>
</form>