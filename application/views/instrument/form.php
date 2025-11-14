<form>
    <div class="col-md-12">
        <div class="error item_name"></div>
        <div class="row">

            <input type="hidden" name="id" value="<?=(!empty($dataRow->id))?$dataRow->id:""?>" />
            <input type="hidden" name="status" value="<?=(!empty($dataRow->status))?$dataRow->status:$status?>" />
            <input type="hidden" name="cat_code" id="cat_code" value="<?= (!empty($dataRow->cat_code)) ? $dataRow->cat_code : ""; ?>" />
            <input type="hidden" name="cat_name" id="cat_name" value="<?= (!empty($dataRow->category_name)) ?$dataRow->category_name : ""; ?>" />
			
			<div class="col-md-3 form-group">
                <label for="item_code">Item Code</label>
                <input type="text" name="item_code" id="item_code" class="form-control req" value="<?=(!empty($dataRow->item_code))?$dataRow->item_code:""?>" />
            </div>
			
            <?php if(!empty($dataRow->id)): ?>
                <div class="col-md-3 form-group">
                    <label for="category_id">Category</label>
                    <input type="text" id="category_name" value="<?=(!empty($dataRow->category_name))?'['.$dataRow->cat_code.'] '.$dataRow->category_name:""?>" class="form-control req" readOnly>
                    <input type="hidden" name="category_id" id="category_id" value="<?=(!empty($dataRow->category_id))?$dataRow->category_id:""?>" />
                </div>
            <?php else: ?>    
                <div class="col-md-3 form-group">
                    <label for="category_id">Category</label>
                    <select name="category_id" id="category_id" class="form-control select2 req">
                        <option value="">Select Category</option>
                        <?php
                            foreach ($categoryList as $row) :
                                $selected = (!empty($dataRow->category_id) && $dataRow->category_id == $row->id) ? "selected" : "";
                                echo '<option value="'. $row->id .'" '.$selected.' data-cat_code="'.$row->category_code.'" data-cat_name="'.$row->category_name.'">'.((!empty($row->category_code))?'['.$row->category_code.'] '.$row->category_name:$row->category_name).'</option>';
                            endforeach;
                        ?>
                    </select>
                </div>
            <?php endif; ?>
            
            <div class="col-md-3 form-group">
                <label for="size">Size</label>
                <input type="text" name="size" id="size" class="form-control req" value="<?=(!empty($dataRow->size))?$dataRow->size:""?>" />
            </div>

            <div class="col-md-3 form-group">
                <label for="make_brand">Make</label>
                <input type="text" name="make_brand" class="form-control" value="<?=(!empty($dataRow->make_brand))?$dataRow->make_brand:""?>" />
            </div>

            <div class="col-md-3 form-group">
                <label for="mfg_sr">Mfg Serial No.</label>
                <input type="text" name="mfg_sr" class="form-control" value="<?=(!empty($dataRow->mfg_sr))?$dataRow->mfg_sr:""?>" />
            </div>
            
            <div class="col-md-3 form-group">
                <label for="least_count">Least Count</label>
                <input type="text" name="least_count" id="least_count" class="form-control" value="<?=(!empty($dataRow->least_count))?$dataRow->least_count:""?>" />
            </div>
           
            <div class="col-md-3 form-group">
                <label for="permissible_error">Permissible Error</label>
                <input type="text" name="permissible_error" class="form-control" value="<?=(!empty($dataRow->permissible_error))?$dataRow->permissible_error:""?>" />
            </div>

            <div class="col-md-3 form-group">
                <label for="cal_required">Cal. Required</label>
                <select name="cal_required" id="cal_required" class="form-control select2 req" >
                    <option value="YES" <?=(!empty($dataRow->cal_required) && $dataRow->cal_required == "YES")?"selected":""?>>YES</option>
                    <option value="NO" <?=(!empty($dataRow->cal_required) && $dataRow->cal_required == "NO")?"selected":""?>>NO</option>
                </select>
            </div>
            
            <div class="col-md-6 form-group">
                <div class="input-group">
                    <label for="cal_freq" style="width: 50%;">Freq. <small>(Months)</small></label>
                    <label for="cal_reminder">Reminder <small>(Days)</small></label>
                </div>
                <div class="input-group">
                    <input type="text" name="cal_freq" class="form-control floatOnly"  value="<?=(!empty($dataRow->cal_freq))?$dataRow->cal_freq:""?>" />
                    <input type="text" name="cal_reminder" class="form-control floatOnly" value="<?=(!empty($dataRow->cal_reminder))?$dataRow->cal_reminder:""?>" />
                </div>
            </div>
            
            <div class="col-md-3 form-group">
                <label for="price">Price</label>
                <input type="text" name="price" class="form-control floatOnly" value="<?=(!empty($dataRow->price))?$dataRow->price:""?>" />
            </div>
            
            <div class="col-md-3 form-group">
                <label for="location_id">Location</label>
                <select name="location_id" id="location_id" class="form-control select2 req">
					<option value="">Select Location</option>
                    <?php
						foreach ($locationList as $row) :
							$selected = (!empty($dataRow->location_id) && $dataRow->location_id == $row->id) ? "selected" : "";
							echo '<option value="' . $row->id . '" ' . $selected . '>[' .$row->store_name. '] '.$row->location.'</option>';
						endforeach;
                    ?>
                </select>
            </div>  

            <div class="col-md-12 form-group">
                <label for="description">Remark</label>
                <textarea name="description" id="description" class="form-control"><?=(!empty($dataRow->description))?$dataRow->description:""?></textarea>
            </div>       

            <h4 class="fs-15 text-primary border-bottom-sm">Certificate Details</h4>
            <div class="col-md-3 form-group">
                <label for="last_cal_date">Certificate Date</label>
                <input type="date" name="last_cal_date" class="form-control" value="<?=(!empty($dataRow->last_cal_date))?$dataRow->last_cal_date:date('Y-m-d')?>" />
            </div>

            <div class="col-md-3 form-group">
                <label for="certi_no">Certificate No.</label>
                <input type="text" name="certi_no" class="form-control" value="<?=(!empty($dataRow->certi_no))?$dataRow->certi_no:""?>" />
            </div>

            <div class="col-md-6 form-group">
                <label for="certi_file">Certificate File</label>
                <div class="input-group">
                    <input type="file" name="certi_file" class="form-control" value="" />
                    <?php if(!empty($dataRow->certi_file)): ?>
                        <div class="input-group-append">
                            <a href="<?=base_url('assets/uploads/instrument/'.$dataRow->certi_file)?>" class="btn btn-outline-primary" target="_blank"><i class="fas fa-download"></i></a>
                        </div>
                    <?php endif; ?>
                </div>
            </div>

        </div>
    </div>
</form>
<script>
$(document).ready(function(){    
    $("#category_id").on('change',function(){
        var cat_code = $(this).find(":selected").data('cat_code');
        var cat_name = $(this).find(":selected").data('cat_name');
        $('#cat_code').val(cat_code);
        $('#cat_name').val(cat_name);
    });
});
</script>