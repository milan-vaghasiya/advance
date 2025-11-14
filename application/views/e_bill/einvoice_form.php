<form autocomplete="off">
    <div class="col-md-12 form-group">
        <div class="row">
            <div class="error general_error"></div>
                        
            <input type="hidden" name="ref_id" id="ref_id" value="<?=$ref_id?>">
            <input type="hidden" name="party_id" id="party_id" value="<?=(!empty($partyData->id))?$partyData->id:""?>">
            
            <div class="col-md-3 form-group">
                <label for="doc_type">Document Type</label>
                <select name="doc_type" id="doc_type" class="form-control req">
                    <option value="">Select Doc. Type</option>
                    <option value="INV" <?=($doc_type == "INV")?"selected":""?>>TAX Invoice</option>
                    <option value="CRN" <?=($doc_type == "CRN")?"selected":""?>>Credit Note</option>
                    <option value="DBN" <?=($doc_type == "DBN")?"selected":""?>>Debit Note</option>
                </select>
            </div>

            <div class="col-md-3 form-group">
                <label for="type_of_transaction">Transaction Type</label>
                <select name="type_of_transaction" id="type_of_transaction" class="form-control req">
                    <option value="">Select Type</option>
                    <option value="B2B" selected>Business to Business</option>
                    <!-- <option value="SEZWP">SEZ with payment</option>
                    <option value="SEZWOP">SEZ without payment</option> -->
                    <option value="EXPWP">Export with Payment</option>
                    <option value="EXPWOP">Export without Payment</option>
                    <!-- <option value="DEXP">Deemed Export</option> -->
                </select>
            </div>
            
            <div class="col-md-3 form-group">                
                <label for="ewb_status">Generate Eway Bill</label>
                <select name="ewb_status" id="ewb_status" class="form-control req">
                    <option value="0" selected>No</option>
                    <option value="1">Yes</option>
                </select>
            </div>

            <div class="col-md-3 form-group">
                <label for="trans_mode">Transport Mode</label>
                <select name="trans_mode" id="trans_mode" class="form-control req">
                    <option value="">Select</option>
                    <option value="1">Road</option>
                    <option value="2">Rail</option>
                    <option value="3">Air</option>
                    <option value="4">Ship</option>
                </select>
            </div>

            <div class="col-md-3 form-group">
                <label for="transport_name">Transport Name</label>
                <select name="transport_name" id="transport_name" class="form-control select2 transport">
                    <option value="">Select Transport</option>
                    <?php
                        foreach($transportData as $row):
                            $selected = ((!empty($invoiceData->transport_id)) && $invoiceData->transport_id == $row->id)?"selected":"";
                            echo '<option value="'.$row->transport_name.'" data-val="'.$row->transport_id.'" '.$selected.'>'.$row->transport_name.'</option>';
                        endforeach;
                    ?>
                </select>
            </div>

            <div class="col-md-3 form-group">
                <label for="transport_id">Transport Id</label>
                <input type="text" name="transport_id" id="transport_id" class="form-control" placeholder="Transport Id" maxlength="15" value="" />
            </div>            
            
            <div class="col-md-3 form-group">
                <label for="transport_doc_no">Trans. Doc. No.</label>
                <input type="text" name="transport_doc_no" id="transport_doc_no" class="form-control" placeholder="Trans. Doc. No." maxlength="16" value="" />
                <div class="error transport_doc_no"></div>
            </div>
            
            <div class="col-md-3 form-group">
                <label for="transport_doc_date">Transport Doc. Date</label>
                <input type="date" class="form-control" name="transport_doc_date" id="transport_doc_date" value="" >
            </div>

            <div class="col-md-4 form-group">
                <label for="trans_distance">Distance (Km.)</label>
                <input type="text" name="trans_distance" id="trans_distance" class="form-control req numericOnly" placeholder="Distance (In km)" min="0" maxlength="4" value="<?=(!empty(floatVal($partyData->distance)))?floatVal($partyData->distance):""?>" />
            </div>

            <div class="col-md-4 form-group">
                <label for="vehicle_no">Vehicle No.</label>
                <input type="text" name="vehicle_no" id="vehicle_no" class="form-control" placeholder="Vehicle No." maxlength="15" value="" />
            </div>

            <div class="col-md-4 form-group">
                <label for="vehicle_type">Vehicle Type</label>
                <select name="vehicle_type" id="vehicle_type" class="form-control req">
                    <option value="R">Regular</option>
                    <option value="O">ODC</option>
                </select>
            </div>

        </div>

        <hr>
        <div class="row">
            <div class="col-md-12 form-group">
                <label for="dispatchDetails">Dispatch Details : </label>
            </div>

            <div class="col-md-3 form-group">
                <label for="dispatch_country">Dispatch Country</label>
                <select name="dispatch_country" id="dispatch_country" class="form-control country_list select2 req" data-state_id="dispatch_state" data-selected_state_id="<?=(!empty($companyInfo->company_state_id))?$companyInfo->company_state_id:4030?>">
                    <option value="">Select Country</option>
                    <?php
                        foreach($countryList as $row):
                            $selected = (!empty($companyInfo->company_country_id) && $companyInfo->company_country_id == $row->id)?"selected":"";
                            echo '<option value="'.$row->id.'" '.$selected.'>'.$row->name.'</option>';
                        endforeach;
                    ?>
                </select>
            </div>

            <div class="col-md-3 form-group">
                <label for="dispatch_state">Dispatch State</label>
                <select name="dispatch_state" id="dispatch_state" class="form-control select2 req">
                    <option value="">Select State</option>
                </select>
            </div>

            <div class="col-md-3 form-group">
                <label for="dispatch_city">Dispatch City</label>
                <input type="text" name="dispatch_city" id="dispatch_city" class="form-control req" value="<?=(!empty($companyInfo->company_city_name))?$companyInfo->company_city_name:""?>">
                <!-- <select name="dispatch_city" id="dispatch_city" class="form-control select2 req" >
                    <option value="">Select City</option>
                </select> -->
            </div>

            <div class="col-md-3 form-group">
                <label for="dispatch_pincode">Dispatch Pincode</label>
                <input type="text" name="dispatch_pincode" id="dispatch_pincode" class="form-control numericOnly req" value="<?=(!empty($companyInfo->company_pincode))?$companyInfo->company_pincode:""?>">
            </div>

            <div class="col-md-12 form-group">
                <label for="dispatch_address">Dispatch Address</label>
                <input type="text" name="dispatch_address" id="dispatch_address" class="form-control req" value="<?=(!empty($companyInfo->company_address))?$companyInfo->company_address:""?>" />
            </div>
        </div>

        <hr>
        <div class="row">
            <div class="col-md-12 form-group">
                <label for="partyDetails">Party Details : </label>
            </div>

            <div class="col-md-3 form-group">
                <label for="billing_country">Billing Country</label>
                <select name="billing_country" id="billing_country" class="form-control country_list select2 req" data-state_id="billing_state" data-selected_state_id="<?=(!empty($partyData->state_id))?$partyData->state_id:4030?>">
                    <option value="">Select Country</option>
                    <?php
                        foreach($countryList as $row):
                            $selected = (!empty($partyData->country_id) && $partyData->country_id == $row->id)?"selected":"";
                            echo '<option value="'.$row->id.'" '.$selected.'>'.$row->name.'</option>';
                        endforeach;
                    ?>
                </select>
            </div>

            <div class="col-md-3 form-group">
                <label for="billing_state">Billing State</label>
                <select name="billing_state" id="billing_state" class="form-control select2 req">
                    <option value="">Select State</option>
                </select>
            </div>

            <div class="col-md-3 form-group">
                <label for="billing_city">Billing City</label>
                <input type="text" name="billing_city" id="billing_city" class="form-control req" value="<?=(!empty($partyData->city_name))?$partyData->city_name:""?>">
                <!-- <select name="billing_city" id="billing_city" class="form-control select2 req">
                    <option value="">Select City</option>
                </select> -->
            </div>

            <div class="col-md-3 form-group">
                <label for="billing_pincode">Billing Pincode</label>
                <input type="text" name="billing_pincode" id="billing_pincode" class="form-control req" placeholder="Billing Pincode" value="<?=(!empty($partyData->party_pincode))?$partyData->party_pincode:""?>" />
            </div>
            <div class="col-md-12 form-group">
                <label for="billing_address">Billing Address</label>
                <input type="text" name="billing_address" id="billing_address" class="form-control req" placeholder="Billing Address" value="<?=(!empty($partyData->party_address))?$partyData->party_address:""?>" />
            </div>

            <div class="col-md-3 form-group">
                <label for="ship_country">Shipping Country</label>
                <select name="ship_country" id="ship_country" class="form-control country_list select2 req" data-state_id="ship_state" data-selected_state_id="<?=(!empty($invoiceData->delivery_state_id))?$invoiceData->delivery_state_id:4030?>">
                    <option value="">Select Country</option>
                    <?php
                        foreach($countryList as $row):
                            $selected = (!empty($invoiceData->delivery_country_id) && $invoiceData->delivery_country_id == $row->id)?"selected":"";
                            echo '<option value="'.$row->id.'" '.$selected.'>'.$row->name.'</option>';
                        endforeach;
                    ?>
                </select>
            </div>

            <div class="col-md-3 form-group">
                <label for="ship_state">Shipping State</label>
                <select name="ship_state" id="ship_state" class="form-control select2 req">
                    <option value="">Select State</option>
                </select>
            </div>

            <div class="col-md-3 form-group">
                <label for="ship_city">Shipping City</label>
                <input type="text" name="ship_city" id="ship_city" class="form-control req" value="<?=(!empty($invoiceData->delivery_city_name))?$invoiceData->delivery_city_name:""?>">
                <!-- <select name="ship_city" id="ship_city" class="form-control select2 req">
                    <option value="">Select City</option>
                </select> -->
            </div>

            <div class="col-md-3 form-group">
                <label for="ship_pincode">Shipping Pincode</label>
                <input type="text" name="ship_pincode" id="ship_pincode" class="form-control req" placeholder="Shipping Pincode" value="<?=((!empty($invoiceData->delivery_pincode))?$invoiceData->delivery_pincode:"")?>" />
            </div>            
            
            <div class="col-md-12 form-group">
                <label for="ship_address">Shipping Address</label>
                <input type="text" name="ship_address" id="ship_address" class="form-control req" placeholder="Shipping Address" value="<?=(!empty($invoiceData->delivery_address))?$invoiceData->delivery_address:""?>" />
            </div>            
        </div>
    </div>
</form>
<script>
$(document).ready(function(){
    setTimeout(function(){
        $("#transport_name").trigger('change');
    },500);
});
</script>