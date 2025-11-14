<form autocomplete="off" id="saveGateEntry">
    <div class="col-md-12">
        <div class="row">
            <input type="hidden" name="id" id="id" value="<?=(!empty($dataRow->id))?$dataRow->id:''?>">
            <input type="hidden" name="trans_prefix" id="trans_prefix" value="<?=(!empty($dataRow->trans_prefix))?$dataRow->trans_prefix:$trans_prefix?>">
            <input type="hidden" name="trans_no" id="trans_no" value="<?=(!empty($dataRow->trans_no))?$dataRow->trans_no:$trans_no?>">
            <input type="hidden" name="trans_type" id="trans_type" value="<?=(!empty($dataRow->trans_type))?$dataRow->trans_type:1?>">

            <div class="col-md-2 form-group">
                <label for="trans_no">GE No.</label>
                <input type="text" name="trans_number" id="trans_number" class="form-control" value="<?=(!empty($dataRow->trans_number))?$dataRow->trans_number:$trans_number?>" readonly>
            </div>

            <div class="col-md-4 form-group">
                <label for="trans_date">GE Date</label>
                <input type="datetime-local" name="trans_date" id="trans_date" class="form-control" value="<?=(!empty($dataRow->trans_date))?$dataRow->trans_date:getFyDate("Y-m-d H:i:s")?>">
            </div>

            <!-- <div class="col-md-3 form-group">
                <label for="driver_name">Driver Name</label>
                <input type="text" name="driver_name" id="driver_name" class="form-control req" value="<?=(!empty($dataRow->driver_name))?$dataRow->driver_name:""?>">
            </div>

            <div class="col-md-3 form-group">
                <label for="driver_name">Driver Contact No.</label>
                <input type="text" name="driver_contact" id="driver_contact" class="form-control numericOnly req" value="<?=(!empty($dataRow->driver_contact))?$dataRow->driver_contact:""?>">
            </div> -->

            <div class="col-md-4 form-group">
                <label for="transporter">Transport Name</label>
                <select name="transporter" id="transporter" class="form-control select2">
                    <option value="">Select Transport Name</option>
                    <?php
                        foreach($transportList as $row):
                            $selected = (!empty($dataRow->transporter) && $dataRow->transporter == $row->id)?"selected":"";
                            echo '<option value="'.$row->id.'" '.$selected.'>'.$row->transport_name.'</option>';
                        endforeach;
                    ?>
                </select>
            </div>

            <div class="col-md-2 form-group">
                <label for="lr">LR. NO.</label>
                <input type="text" name="lr" id="lr" class="form-control req" value="<?=(!empty($dataRow->lr))?$dataRow->lr:""?>">
            </div>

            <div class="col-md-4 form-group">
                <label for="vehicle_type">Vehicle Type</label>
                <select name="vehicle_type" id="vehicle_type" class="form-control select2 req">
                    <option value="">Select Transport Name</option>
                    <?php
                        foreach($vehicleTypeList as $row):
                            $selected = (!empty($dataRow->vehicle_type) && $dataRow->vehicle_type == $row->id)?"selected":"";
                            echo '<option value="'.$row->id.'" '.$selected.'>'.$row->vehicle_type.'</option>';
                        endforeach;
                    ?>
                </select>
            </div>

            <div class="col-md-4 form-group">
                <label for="vehicle_no">Vehicle No.</label>
                <input type="text" name="vehicle_no" id="vehicle_no" class="form-control text-uppercase" value="<?=(!empty($dataRow->vehicle_no))?$dataRow->vehicle_no:""?>">
            </div>

            <div class="col-md-4 form-group">
                <label for="party_id">Party Name</label>
                <select name="party_id" id="party_id" class="form-control select2 req">
                    <option value="">Select Party Name</option>
                    <?=getPartyListOption($partyList,((!empty($dataRow->party_id))?$dataRow->party_id:0))?>
                </select>
            </div>

            <div class="col-md-3 form-group">
                <label for="inv_no">Invoice No.</label>
                <input type="text" name="inv_no" id="inv_no" class="form-control req text-uppercase" value="<?=(!empty($dataRow->inv_no))?$dataRow->inv_no:""?>">
            </div>

            <div class="col-md-3 form-group">
                <label for="inv_date">Invoice Date</label>
                <input type="date" name="inv_date" id="inv_date" class="form-control req" value="<?=(!empty($dataRow->inv_date))?$dataRow->inv_date:""?>" >
            </div>

            <div class="col-md-3 form-group">
                <label for="doc_no">Challan No.</label>
                <input type="text" name="doc_no" id="doc_no" class="form-control req text-uppercase" value="<?=(!empty($dataRow->doc_no))?$dataRow->doc_no:""?>">
            </div>

            <div class="col-md-3 form-group">
                <label for="doc_date">Challan Date</label>
                <input type="date" name="doc_date" id="doc_date" class="form-control req" value="<?=(!empty($dataRow->doc_date))?$dataRow->doc_date:""?>">
            </div>

            <!-- <div class="col-md-4 form-group">
                <label for="qty">No Of Items</label>
                <input type="text" name="qty" id="qty" class="form-control floatOnly" value="<?=(!empty($dataRow->qty))?floatVal($dataRow->qty):""?>">
            </div> -->
        </div>
        
    </div>
</form>