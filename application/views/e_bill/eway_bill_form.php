<form>
    <div class="col-md-12">
        <div class="row">
            <div class="error general_error"></div>
            <input type="hidden" name="ref_id" id="ref_id" value="<?=(!empty($ref_id))?$ref_id:""?>">
            <input type="hidden" name="party_id" id="party_id" value="<?=(!empty($party_id))?$party_id:""?>">
            <input type="hidden" name="document_type" id="document_type" value="<?=(!empty($document_type))?$document_type:""?>">

            <div class="col-md-4 form-group">
                <label for="doc_type">Document Type</label>
                <select name="doc_type" id="doc_type" class="form-control req">
                    <option value="">Select Doc. Type</option>
                    <option value="INV" <?=($document_type == "INV")?"selected":""?>>TAX Invoice</option>
                    <!-- <option value="BIL">Bill Of Supply</option>
                    <option value="BOE">Bill of Entry</option> -->
                    <option value="CHL" <?=($document_type == "JOBWORK")?"selected":""?>>Delivery Challan</option>
                    <option value="OTH">Others</option>
                </select>
            </div>

            <div class="col-md-4 form-group">
                <label for="supply_type">Supply Type</label>
                <select name="supply_type" id="supply_type" class="form-control req">
                    <option value="">Select Supply Type</option>
                    <option value="O" selected>Outward</option>
                    <option value="I">Inward</option>
                </select>
            </div>

            <div class="col-md-4 form-group">
                <label for="sub_supply_type">Sub Supply Type</label>
                <select name="sub_supply_type" id="sub_supply_type" class="form-control req">
                    <option value="">Select Sub Supply Type</option>
                    <option value="1" <?=($document_type == "INV")?"selected":""?>>Supply</option>
                    <option value="2">Import</option>
                    <option value="3">Export</option>
                    <option value="4" <?=($document_type == "JOBWORK")?"selected":""?>>Job Work</option>
                    <option value="5">For Own Use</option>
                    <option value="6">Job Work Return</option>
                    <option value="7">Sales Return</option>
                    <option value="8">Others</option>
                    <option value="9">SKD/CKD</option>
                    <option value="10">Line Sales</option>
                    <option value="11">Recipient Not Known</option>
                    <option value="12">Exhibition or Fairs</option>
                </select>                
            </div>

            <div class="col-md-12 form-group subSupDesc hidden">
                <label for="sub_supply_desc">Sub Supply Description</label>
                <input type="text" name="sub_supply_desc" id="sub_supply_desc" class="form-control" value="">
            </div>

            <div class="col-md-4 form-group">
                <label for="trans_mode">Transport Mode</label>
                <select name="trans_mode" id="trans_mode" class="form-control">
                    <option value="">Select</option>
                    <option value="1">Road</option>
                    <option value="2">Rail</option>
                    <option value="3">Air</option>
                    <option value="4">Ship</option>
                </select>                
            </div>
            
            <div class="col-md-4 form-group">
                <label for="trans_distance">Distance (Km.)</label>
                <input type="text" name="trans_distance" id="trans_distance" class="form-control numericOnly req" min="0" maxlength="4" value="<?=(!empty($partyData->distance))?floatval($partyData->distance):""?>" />
            </div>

            <div class="col-md-4 form-group">
                <label>Transporter Name</label>
                <select name="transport_name" id="transport_name" class="form-control select2 transport_name req">
                    <option value="">Select Transporter</option>
                    <?php
                    foreach ($transportData as $row) :
                        $selected = ((!empty($invoiceData->transport_id)) && $invoiceData->transport_id == $row->id)?"selected":"";
                        echo '<option value="' . $row->transport_name . '" data-val="' . $row->transport_id . '" '.$selected.'>' . $row->transport_name . '</option>';
                    endforeach;
                    ?>
                </select>
            </div>

            <div class="col-md-4 form-group">
                <label>Transporter Id</label>
                <input type="text" name="transport_id" id="transport_id" value="" class="form-control req text-uppercase" />
            </div>

            <div class="col-md-4 form-group">
                <label for="transport_doc_no">Transport Doc. No.</label>
                <input type="text" name="transport_doc_no" id="transport_doc_no" class="form-control req" placeholder="Enter Trans. Doc. No." maxlength="16" value="" />                
            </div>

            <div class="col-md-4 form-group">
                <label for="transport_doc_date">Transport Doc. Date</label>
                <input type="date" name="transport_doc_date" id="transport_doc_date" class="form-control" value="">
            </div>

            <div class="col-md-4 form-group">
                <label>Vehicle No.</label>
                <input type="text" name="vehicle_no" id="vehicle_no" value="<?=$invoiceData->vehicle_no??''?>" class="form-control text-uppercase req" />
                <div class="error vehicle_no"></div>
            </div>

            <div class="col-md-4 form-group">
                <label for="vehicle_type">Vehicle Type</label>
                <select name="vehicle_type" id="vehicle_type" class="form-control">
                    <option value="R">Regular</option>
                    <option value="O">ODC</option>
                </select>
                <div class="error vehicle_type"></div>
            </div>

            <div class="col-md-4 form-group">
                <label for="ship_pincode">Transaction Type</label>
                <select name="transaction_type" id="transaction_type" class="form-control">
                    <option value="">Select Transaction</option>
                    <option value="1" selected>Regular</option>
                    <option value="2">Bill To - Ship To</option>
                    <option value="3">Bill From - Dispatch From</option>
                    <option value="4">Combination of 2 and 3</option>
                </select>
                <div class="error transaction_type"></div>
            </div>

            <div class="col-md-2 form-group">											
                <label for="from_state">Dispatch State</label>
                <select name="from_state" id="from_state" class="form-control select2 req">                
                    <?php                    
                        $html= '<option value="">Select State</option>';
                        foreach($stateList as $row):
                            $html .= '<option value="'.$row->id.'" >'.$row->name.'</option>';
                        endforeach;
                        echo $html;
                    ?>
                </select>
                <div class="error from_state"></div>
            </div>

            <div class="col-md-2 form-group">
                <label for="from_city">Dispatch City</label>
                <input type="text" name="from_city" id="from_city" class="form-control req" value="" />
                <div class="error from_city"></div>
            </div>

            <div class="col-md-2 form-group">
                <label for="from_pincode">Dispatch Pincode</label>
                <input type="text" name="from_pincode" max="5" id="from_pincode" class="form-control" placeholder="Dispatch Pincode" maxlength="16" value="" />
                <div class="error from_pincode"></div>
            </div>

            <div class="col-md-6 form-group">
                <label for="from_address">Dispatch Address</label>
                <input type="text" name="from_address" id="from_address" class="form-control" placeholder="Dispatch Address" value="" />
                <div class="error from_address"></div>
            </div>

            <div class="col-md-2 form-group">                
                <label for="ship_state">Shipping State</label>
                <select name="ship_state" id="ship_state" class="form-control select2">                
                    <?php                    
                        $html= '<option value="">Select State</option>';
                        foreach($stateList as $row):
                            $html .= '<option value="'.$row->id.'" >'.$row->name.'</option>';
                        endforeach;
                        echo $html;               
                    ?>
                </select>
                <div class="error ship_state"></div>
            </div>

            <div class="col-md-2 form-group">
                <label for="ship_state">Shipping City</label>
                <input type="text" name="ship_city" id="ship_city" class="form-control" value="" />
                <div class="error ship_city"></div>
            </div>

            <div class="col-md-2 form-group">
                <label for="ship_pincode">Shipping Pincode</label>
                <input type="text" name="ship_pincode" id="ship_pincode" class="form-control" placeholder="Shipping Pincode" maxlength="16" value="" />
                <div class="error ship_pincode"></div>
            </div>

            <div class="col-md-6 form-group">
                <label for="ship_address">Shipping Address</label>
                <input type="text" name="ship_address" id="ship_address" class="form-control" placeholder="Shipping Address" value="" />
                <div class="error ship_address"></div>
            </div>

        </div>
    </div>
</form>
<script>
$(document).ready(function(){
    setTimeout(function(){
        $("#transport_name").trigger('change');
    },500);

    $(document).on('change',"#sub_supply_type",function(){
        $(".subSupDesc").addClass('hidden');
        if($(this).find(":selected").val() == 8){
            $(".subSupDesc").removeClass('hidden');
        }
    });
});
</script>
