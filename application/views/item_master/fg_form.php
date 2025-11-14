<form class="itemMasterForm" enctype="multipart/form-data">
    <div class="col-md-12">
        <div class="row">
            <input type="hidden" name="id" id="id" value="<?=(!empty($dataRow->id))?$dataRow->id:""?>">
            <input type="hidden" name="item_type" id="item_type" value="<?=(!empty($dataRow->item_type))?$dataRow->item_type:$item_type?>">
			<input type="hidden" name="is_active" id="is_active" value="<?=(!empty($dataRow->is_active))?$dataRow->is_active:$is_active?>">
          
            <div class="col-md-4 form-group">
                <label for="item_code">Item Code</label>
                <input type="text" name="item_code" class="form-control" value="<?=(!empty($dataRow->item_code))?$dataRow->item_code:""; ?>" />
            </div>
			
            <div class="col-md-8 form-group">
                <label for="item_name">Item Name</label>
                <input type="text" name="item_name" class="form-control req" value="<?=htmlentities((!empty($dataRow->item_name)) ? $dataRow->item_name : "")?>" />      
            </div>
			<div class="col-md-4 form-group">
                <label for="party_id">Customer Name</label>
                <select name="party_id" id="party_id" class="form-control select2">
                    <option value="">Select Party</option>
                    <?=getPartyListOption($partyList,(!empty($dataRow->party_id))?$dataRow->party_id:(!empty($party_id)?$party_id:0))?>
                </select>
            </div>
			<div class="col-md-4 form-group">
                <label for="grade_id">Material Grade</label>
                <select name="grade_id" id="grade_id" class="form-control select2">
                    <option value="">Select Material Grade</option>
                    <?php
                        foreach($materialGrade as $row):
                            $selected = (!empty($dataRow->grade_id) && $dataRow->grade_id == $row->id)?"selected":"";
                            echo '<option value="'.$row->id.'" '.$selected.'>'.$row->material_grade.'</option>';
                        endforeach;
                    ?>
                </select>
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

            <div class="col-md-3 form-group">
                <label for="price">Price</label>
                <input type="text" name="price" id="price" class="form-control calMRP floatOnly" value="<?=(!empty($dataRow->price))?$dataRow->price:"0"?>">
            </div>  

            <div class="col-md-3 form-group">
                <label for="wt_pcs">Weight (Kg)</label>
                <input type="text" name="wt_pcs" id="wt_pcs" class="form-control floatOnly" value="<?=(!empty($dataRow->wt_pcs))?$dataRow->wt_pcs:"0"?>">
            </div>
			
			<div class="col-md-3 form-group">
                <label for="cut_rate">Cutting Rate</label>
                <input type="text" name="cut_rate" id="cut_rate" class="form-control floatOnly" value="<?=(!empty($dataRow->cut_rate))?$dataRow->cut_rate:"0"?>">
            </div>

			<div class="col-md-3 form-group">
                <label for="mfg_status">MFG Status</label>
                <select name="mfg_status" id="mfg_status" class="form-control select2">
					<option value="">Select MFG Status</option>
					<option value="Prototype" <?= ((!empty($dataRow->mfg_status) && $dataRow->mfg_status == "Prototype")?"selected":"");?>>Prototype</option>
					<option value="Pre Launch" <?= ((!empty($dataRow->mfg_status) && $dataRow->mfg_status == "Pre Launch")?"selected":"");?>>Pre Launch</option>
					<option value="Reguler" <?= ((!empty($dataRow->mfg_status) && $dataRow->mfg_status == "Reguler")?"selected":"");?>>Reguler</option>
				</select>
            </div>
			
			<div class="col-md-3 form-group">
				<label for="mfg_type">Mfg. Type</label>
				<select name="mfg_type" id="mfg_type" class="form-control select2">
					<?php
						foreach($this->MFG_TYPES as $mt):
							$selected = ((!empty($dataRow->mfg_type) && $dataRow->mfg_type == $mt)?"selected":"");
							echo '<option value="'.$mt.'" '.$selected.'>'.$mt.'</option>';
						endforeach;
					?>
				</select>
			</div> 
			
			<div class="col-md-3 form-group">
                <label for="is_packing">Packing Type</label>
                <select name="is_packing" id="is_packing" class="form-control select2">
                    <option value="0" >No</option>
					<option value="1" <?= ((!empty($dataRow) && $dataRow->is_packing == "1")?"selected":"");?>>Primary + Final Packing </option>
					<option value="2" <?= ((!empty($dataRow) && $dataRow->is_packing == "2")?"selected":"");?>>Only Final Packing</option>
				</select>
            </div>
			
			<div class="col-md-12 form-group">
                <label for="tc_head">TC Head</label>
                <select name="tc_head[]" id="tc_head" class="form-control select2" multiple>
                    <?php
                    if(!empty($tcHeadList)):
                        foreach ($tcHeadList as $row) :
                            $selected = (!empty($dataRow->tc_head) && (in_array($row->id,  explode(',', $dataRow->tc_head)))) ? "selected" : "";
                            echo '<option value="' . $row->id . '" ' . $selected . '>' . $row->test_name . '</option>';
                        endforeach;
                    endif;
                    ?>
                </select>
            </div>
			
            <div class="col-md-12 form-group">
                <label for="description">Description</label>
                <textarea name="description" id="description" class="form-control" rows="1"><?=(!empty($dataRow->description))?$dataRow->description:""?></textarea>
            </div>
            <div class="col-md-12 form-group">
                <label for="item_image">Product Image</label>
                <div class="input-group">
                    <div class="custom-file" style="width:100%;">
                        <input type="file" class="form-control custom-file-input" name="item_image" id="item_image" accept=".jpg, .jpeg, .png" />
                    </div>
                </div>
                <div class="error item_image"></div>
            </div>
			
            <?php if(!empty($dataRow->item_image)): ?>
                
			    <input type="hidden" name="old_image" id="old_image" value="<?=(!empty($dataRow->item_image))?$dataRow->item_image:''?>">
                <div class="col-md-3 form-group text-center m-t-20">
                    <img src="<?=base_url("assets/uploads/item_image/".$dataRow->item_image)?>" class="img-zoom" alt="IMG"><br>
                </div>
            <?php endif; ?>

        </div>    
    </div>
</form>