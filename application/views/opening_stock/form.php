<form>
    <div class="col-md-12">
        <div class="error item_error"></div>
        <div class="row">
            <input type="hidden" name="id" id="id" value="">
            <input type="hidden" name="p_or_m" id="p_or_m" value="1">
            <input type="hidden" name="ref_no" id="ref_no" value="OPENING STOCK">
            <input type="hidden" name="trans_type" id="trans_type" value="OPS">
			

            <div class="col-md-12 form-group">
                <label for="item_id">Item Name</label>
                <select name="item_id" id="item_id" class="form-control select2 req">
                    <option value="">Select Item</option>
                    <?=getItemListOption($itemList)?>
                </select>               
            </div> 
			
			<div class="col-md-6 form-group">
				<label for="qty">Date</label>
                <input type="date" name="trans_date" id="trans_date" class="form-control req" value="<?=date("Y-m-d")?>" >
			</div>
			
            <div class="col-md-6 form-group">
                <label for="location_id">Location</label>
                <select id="location_id" name="location_id" class="form-control select2 req">
                    <option value="">Select Location</option>
                    <?=getLocationListOption($locationList)?>
					<option value="<?=$this->RTD_STORE->id?>">Ready To Dispatch</option>
                </select>  
            </div>
    
            <div class="col-md-6 form-group">
				<label for="qty">Batch No.</label>
                <input type="text" name="batch_no" id="batch_no" class="form-control req" value="" >
			</div>
			
            <div class="col-md-6 form-group">
                <label for="qty">Qty</label>
                <input type="text" name="qty" id="qty" class="form-control floatOnly req" value="" >
            </div>

        </div>
    </div>
</form>
