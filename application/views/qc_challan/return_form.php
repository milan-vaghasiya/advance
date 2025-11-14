<form>
	<div class="col-md-12">
        <div class="row">
            <span><b><?='['.$dataRow->item_code.'] '.$dataRow->item_name; ?></b></span>
        </div>
    </div>
    
	<hr>
	
    <div class="col-md-12 row">
        <input type="hidden" name="id" value="<?=(!empty($dataRow->id))?$dataRow->id:''?>" />
        <input type="hidden" name="challan_id" value="<?=(!empty($dataRow->challan_id))?$dataRow->challan_id:''?>" />
        <input type="hidden" name="item_id" value="<?=(!empty($dataRow->item_id))?$dataRow->item_id:''?>" />
        <input type="hidden" name="from_location" value="<?=(!empty($dataRow->from_location))?$dataRow->from_location:''?>" />
        <input type="hidden" name="challan_type" value="<?=(!empty($dataRow->challan_type))?$dataRow->challan_type:''?>" />

        <div class="col-md-4 form-group">
            <label for="receive_at">Receive Date</label>
            <input type="date" name="receive_at" id="receive_at" class="form-control req" value="<?=date("Y-m-d")?>">
        </div> 
        <div class="col-md-4 form-group">
            <label for="in_ch_no">In Challan No</label>
            <input type="text" name="in_ch_no" id="in_ch_no" class="form-control" value="">
        </div>

        <div class="col-md-4 form-group">
            <label for="to_location">Receive Location</label>
            <select name="to_location" id="to_location" class="form-control select2">
                <option value="">Select Location</option>
                <?php
                    foreach ($locationList as $row) :
                        echo '<option value="' . $row->id . '">[' .$row->store_name. '] '.$row->location.'</option>';
                    endforeach;
                ?>
            </select>
        </div>
        
        <div class="col-md-12 form-group">
            <label for="return_remark">Remark</label>
            <input type="text" name="return_remark" id="return_remark" class="form-control" value="">
        </div>
            
    </div>
</form>