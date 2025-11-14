<div class="col-md-12">
    <form data-res_function="resSavePartyGstDetail">
        <div class="row">
            <input type="hidden" name="party_id" id="party_id" value="<?= (!empty($dataRow->party_id)) ? $dataRow->party_id : $party_id; ?>" />
            <div class="col-md-12 form-group">
                <label for="gstin">GST</label>
                <input type="text" name="gstin" id="gstin" class="form-control req" value="" />
            </div>
            <div class="col-md-8 form-group">
                <label for="party_address">Party Address</label>
                <input type="text" name="party_address" id="party_address" class="form-control  req" value="" />
            </div>
            <div class="col-md-4 form-group">
                <label for="dparty_incode">Party Pincode</label>
                <input type="text" name="party_pincode" id="party_pincode" class="form-control req" value="" />
            </div>
            <div class="col-md-8 form-group">
                <label for="delivery_address">Delivery Address</label>
                <input type="text" name="delivery_address" id="delivery_address" class="form-control  req" value="" />
            </div>
            <div class="col-md-4 form-group">
                <label for="delivery_pincode">Delivery Pincode</label>
                <input type="text" name="delivery_pincode" id="delivery_pincode" class="form-control req" value="" />
            </div>
            
        </div>
    </form>
    <hr>
    <div class="row">
        <div class="col-md-12 form-group">
            <button type="button" class="btn waves-effect waves-light btn-outline-success btn-save save-form float-right" onclick="customStore({'formId':'gstDetail','fnsave':'saveGstDetail'});"><i class="fa fa-check"></i> Save</button>
        </div>
    </div>
    <hr>
    <div class="row">
        <div class="table-responsive">
            <table id="gstDetail" class="table table-bordered align-items-center">
                <thead class="thead-dark">
                    <tr>
                        <th style="width:5%;">#</th>
                        <th>GST</th>
                        <th>Party Address</th>
                        <th>Party Pincode</th>
                        <th>Delivery Address</th>
                        <th>Delivery Pincode</th>
                        <th class="text-center" style="width:10%;">Action</th>
                    </tr>
                </thead>
                <tbody id="gstDetailBody">
                    
                </tbody>
            </table>
        </div>
    </div>
</div>
<script>
var gstDetailBody = false;
$(document).ready(function(){
    if(!gstDetailBody){
        var gstTrans = {'postData':{'party_id':$("#gstDetail #party_id").val()},'table_id':"gstDetail",'tbody_id':'gstDetailBody','tfoot_id':'','fnget':'getPartyGSTDetailHtml'};
        getTransHtml(gstTrans);
        gstDetailBody = true;
    }
});
</script>