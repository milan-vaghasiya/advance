<form>
	<div class="col-md-12">
        <div class="row">
            <span><b><?='['.$dataRow->item_code.'] '.$dataRow->item_name; ?></b></span>
        </div>
    </div>
    
	<hr>
	
    <div class="col-md-12">
        <div class="row"> 
            <input type="hidden" name="id" value="<?= (!empty($dataRow->id)) ? $dataRow->id : ''; ?>" />
            <input type="hidden" name="item_id" id="item_id" value="<?= (!empty($dataRow->item_id)) ? $dataRow->item_id : ''; ?>" />
            <input type="hidden" name="challan_id" id="challan_id" value="<?= (!empty($dataRow->challan_id)) ? $dataRow->challan_id : ''; ?>" />
            <input type="hidden" name="batch_no" id="batch_no" value="<?= (!empty($dataRow->item_code)) ? $dataRow->item_code : ''; ?>" />
            <input type="hidden" name="party_id" id="party_id" value="<?= (!empty($dataRow->party_id)) ? $dataRow->party_id : 'IN-HOUSE'; ?>" />
            <input type="hidden" name="challan_type" value="<?=(!empty($dataRow->challan_type))?$dataRow->challan_type:''?>" />

            <div class="col-md-6 form-group">
                <label for="receive_at">Calibration Date</label>
                <input type="date" name="receive_at" id="receive_at" class="form-control floatOnly req" value="<?= (!empty($dataRow->receive_at)) ? $dataRow->receive_at :date("Y-m-d") ?>">
            </div>
            <div class="col-md-6 form-group">
                <label for="in_ch_no">Certificate No.</label>
                <input type="text" name="in_ch_no" id="in_ch_no" class="form-control" value="<?= (!empty($dataRow->in_ch_no)) ? $dataRow->in_ch_no : ''; ?>">
            </div>
            <div class="col-md-6 form-group">
                <label for="certificate_file">Certificate File</label>
                <input type="file" name="certificate_file" id="certificate_file" class="form-control"/>
            </div>

            <div class="col-md-6 form-group">
                <label for="to_location">Receive Location</label>
                <select name="to_location" id="to_location" class="form-control select2">
                    <option value="">Select Location</option>
                    <?php
                        foreach ($locationList as $row) :
                            $selected = (!empty($dataRow->to_location) && $dataRow->to_location == $row->id) ? 'selected' : '';
                            echo '<option value="' . $row->id . '" '.$selected.'>[' .$row->store_name. '] '.$row->location.'</option>';
                        endforeach;
                    ?>
                </select>
            </div>

        </div>
    </div>
</form>


