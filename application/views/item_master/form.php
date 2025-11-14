<form class="itemMasterForm" enctype="multipart/form-data">
    <div class="col-md-12">
        <div class="row">
            <input type="hidden" name="id" id="id" value="<?=(!empty($dataRow->id))?$dataRow->id:""?>">
            <input type="hidden" name="item_type" id="item_type" value="<?=(!empty($dataRow->item_type))?$dataRow->item_type:$item_type?>">

             <div class="col-md-4 form-group">
                <label for="item_code">Item Code</label>
                <input type="text" name="item_code" class="form-control" value="<?=(!empty($dataRow->item_code))?$dataRow->item_code:""; ?>" />
            </div>
            <?php $itmtp = (!empty($dataRow->item_type))?$dataRow->item_type:$item_type; ?>
			
            <div class="<?=($itmtp == 3)?'col-md-5':'col-md-8'?> form-group">
                <label for="item_name">Item Name</label>
				    <input type="text" name="item_name" class="form-control req" value="<?=htmlentities((!empty($dataRow->item_name)) ? $dataRow->item_name : "")?>" />      
            </div>
            <?php if($itmtp == 3 OR $itmtp == 1): ?>
				<div class="col-md-3 form-group">
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
            <?php endif; ?>

            <div class="<?=($itmtp == 1)?'col-md-3':'col-md-4'?> form-group">
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
            
			<div class="<?=($itmtp == 1)?'col-md-3':'col-md-4'?> form-group">
                <label for="uom">Unit</label>
                <select name="uom" id="uom" class="form-control select2 req">
                    <option value="0">--</option>
                    <?=getItemUnitListOption($unitData,((!empty($dataRow->uom))?$dataRow->uom:""))?>
                </select>
            </div>
            
			<div class="<?=($itmtp == 1)?'col-md-3':'col-md-4'?> form-group">
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
                <label for="price">Price <small>(Exc. Tax)</small></label>
                <input type="text" name="price" id="price" class="form-control calMRP floatOnly" value="<?=(!empty($dataRow->price))?$dataRow->price:"0"?>">
            </div>  

            <div class="col-md-3 form-group">
                <label for="rate">MRP <small>(Inc. Tax)</small></label>
                <input type="text" name="rate" id="rate" class="form-control calMRP floatOnly" value="<?=(!empty($dataRow->rate))?$dataRow->rate:"0"?>">
            </div>
			<!-- <div class="col-md-3 form-group">
                <label for="scrap_rate">Scrap Rate</label>
                <input type="text" name="scrap_rate" class="form-control floatOnly" value="<?= (!empty($dataRow->scrap_rate)) ? $dataRow->scrap_rate : "" ?>" />
            </div>			 -->
            <div class="col-md-3 form-group">
                <label for="wt_pcs">Weight/Pcs</label>
                <input type="text" name="wt_pcs" id="wt_pcs" class="form-control floatOnly" value="<?=(!empty($dataRow->wt_pcs))?$dataRow->wt_pcs:"0"?>">
            </div>
			<div class="col-md-3 form-group">
                <label for="size">Size(S.F.)</label>
                <input type="text" name="size" class="form-control floatOnly" value="<?= (!empty($dataRow->size)) ? $dataRow->size : "" ?>" />
            </div>
            <div class="col-md-3 form-group">
                <label for="min_qty">Min. Stock Qty</label>
                <input type="text" name="min_qty" class="form-control floatOnly" value="<?= (!empty($dataRow->min_qty)) ? $dataRow->min_qty : "" ?>" />
            </div>

            <!-- <div class="col-md-3 form-group">
                <label for="stock_type">Stock Type</label>
				<select name="stock_type" id="stock_type" class="form-control select2 req">
                    <option value="1" <?=((!empty($dataRow->stock_type) && $dataRow->stock_type == 1) ? "selected" : "")?> >Batch Wise</option>
                    <option value="2" <?=((!empty($dataRow->stock_type) && $dataRow->stock_type == 2) ? "selected" : "")?> >Serial Wise</option>
                </select>
            </div>  -->
            <div class="col-md-3 form-group">
                <label for="com_uom">Commercial Unit</label>
                <select name="com_uom" id="com_uom" class="form-control select2 req">
                    <option value="0">--</option>
                    <?=getItemUnitListOption($unitData,((!empty($dataRow->com_uom))?$dataRow->com_uom:""))?>
                </select>
            </div>
            <div class="col-md-3 form-group">
                <label for="cnv_value">Conversion Value</label>
                <input type="text" name="cnv_value" class="form-control floatOnly" value="<?= (!empty($dataRow->cnv_value)) ? $dataRow->cnv_value : "" ?>" />
            </div>
            <div class="col-md-6 form-group hidden">
                <label for="item_image1">Product Image</label>
                <div class="input-group">
                    <div class="custom-file" style="width:100%;">
                        <input type="file" class="form-control custom-file-input" name="item_image" id="item_image" accept=".jpg, .jpeg, .png" />
                    </div>
                </div>
                <div class="error item_image"></div>
            </div>


            <div class="col-md-12 form-group">
                <label for="description">Product Description</label>
                <textarea name="description" id="description" class="form-control" rows="1"><?=(!empty($dataRow->description))?$dataRow->description:""?></textarea>
            </div>
			
            <?php if(!empty($dataRow->item_image)): ?>
                <div class="col-md-2 form-group text-center m-t-20">
                    <img src="<?=base_url("assets/uploads/item_image/".$dataRow->item_image)?>" class="img-zoom" alt="IMG"><br>
                </div>
            <?php endif; ?>
        </div>

        <?php if(!empty($customFieldList)): ?>
            <h4 class="fs-15 text-primary border-bottom-sm">Custom Fields</h4>
            <div class="row">
                <?php
                    
                    foreach($customFieldList as $field):
                        ?>
                        <div class="col-md-4 form-group">
                            <label for="wt_pcs"><?=$field->field_name?></label>
                            <?php
                            if($field->field_type == 'SELECT'):
                                ?>
                                <select name="customField[f<?=$field->field_idx?>]" id="f<?=$field->field_idx?>" class="form-control select2">
                                    <option value="">Select</option>
                                <?php
                                foreach($customOptionList as $row):
                                    if($row->type == $field->id):
                                        $selected = (!empty($customData) && !empty(htmlentities($customData->{'f'.$field->field_idx}) && htmlentities($customData->{'f'.$field->field_idx}) == htmlentities($row->title)))?'selected':'';
                                        ?>
                                        <option value="<?=htmlentities($row->title)?>" <?=$selected?>><?=$row->title?></option>
                                        <?php
                                    endif;
                                endforeach;
                            elseif($field->field_type == 'TEXT'):
                                ?>
                                <input type="text" name="customField[f<?=$field->field_idx?>]" id="f<?=$field->field_idx?>" class="form-control" value="<?=(!empty($customData) && !empty($customData->{'f'.$field->field_idx}))?$customData->{'f'.$field->field_idx}:''?>">
                                <?php
                            elseif($field->field_type == 'NUM'):
                                ?>
                                <input type="text" name="customField[f<?=$field->field_idx?>]" id="f<?=$field->field_idx?>" class="form-control floatOnly" value="<?=(!empty($customData) && !empty($customData->{'f'.$field->field_idx}))?$customData->{'f'.$field->field_idx}:''?>">
                                <?php
                            endif;
                            ?>
                            </select>
                        </div>
                        <?php
                    endforeach;
                ?>                
            </div>
        <?php endif; ?>
    </div>
</form>
<script>
$(document).ready(function(){
    $(document).on('change','.itemMasterForm #hsn_code',function(){
        $(".itemMasterForm #gst_per").val(($(this).find(':selected').data('gst_per') || 0));
        initSelect2();
    });

    $(document).on('change','.itemMasterForm .calMRP',function(){
        var gst_per = $(".itemMasterForm #gst_per").val() || 0;
        var price = $(".itemMasterForm #price").val() || 0;
        var rate = $(".itemMasterForm #rate").val() || 0;
        if(gst_per > 0){
            if($(this).attr('id') == "price" && price > 0){
                var tax_amt = parseFloat( (parseFloat(price) * parseFloat(gst_per) ) / 100 ).toFixed(2);
                var new_mrp = parseFloat( parseFloat(price) + parseFloat(tax_amt) ).toFixed(2);
                $(".itemMasterForm #rate").val(new_mrp);
                return true;
            }

            if(($(this).attr('id') == "rate" || $(this).attr('id') == "gst_per") && rate > 0){
                var gstReverse = parseFloat(( ( parseFloat(gst_per) + 100 ) / 100 )).toFixed(2);
                var new_price = parseFloat( parseFloat(rate) / parseFloat(gstReverse) ).toFixed(2);
    		    $(".itemMasterForm #price").val(new_price);
                return true;
            }
        }else{
            if($(this).attr('id') == "price" && price > 0){
                $(".itemMasterForm #rate").val(price);
                return true;
            }

            if(rate > 0){
                $(".itemMasterForm #price").val(rate);
                return true;
            }
        }
    });

    $(document).on('change','#uom',function(){ 
        var com_uom = $('#uom').val();
        $('#com_uom').val(com_uom);
        initSelect2();
    });
});
</script>
