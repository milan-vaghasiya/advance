<div class="col-md-12">
    <form data-res_function="resSavePartyContactDetail">
        <div class="row">
            <input type="hidden" name="party_id" id="party_id" value="<?= (!empty($dataRow->party_id)) ? $dataRow->party_id : $party_id; ?>" />
            <div class="col-md-4 form-group">
                <label for="person">Contact Person</label>
                <input type="text" name="person" id="person" class="form-control req" value="" />
            </div>
            <div class="col-md-4 form-group">
                <label for="mobile">Contact Mobile</label>
                <input type="text" name="mobile" id="mobile" class="form-control req" value="" />
            </div>
            <div class="col-md-4 form-group">
                <label for="email">Contact Email</label>
                <input type="text" name="email" id="email" class="form-control req" value="" />
            </div>
        </div>
    </form>
    <hr>
    <div class="row">
        <div class="col-md-12 form-group">
            <button type="button" class="btn waves-effect waves-light btn-outline-success btn-save save-form float-right" onclick="customStore({'formId':'contactDetail','fnsave':'saveContactDetail'});"><i class="fa fa-check"></i> Save</button>
        </div>
    </div>
    <hr>
    <div class="row">
        <div class="table-responsive">
            <table id="contactDetail" class="table table-bordered align-items-center">
                <thead class="thead-dark">
                    <tr>
                        <th style="width:5%;">#</th>
                        <th>Person</th>
                        <th>Mobile</th>
                        <th>Email</th>
                        <th class="text-center" style="width:10%;">Action</th>
                    </tr>
                </thead>
                <tbody id="contactDetailBody">
                </tbody>
            </table>
        </div>
    </div>
</div>
<script>
var contactDetailBody = false;
$(document).ready(function(){
    if(!contactDetailBody){
        var contactTrans = {'postData':{'party_id':$("#contactDetail #party_id").val()},'table_id':"contactDetail",'tbody_id':'contactDetailBody','tfoot_id':'','fnget':'getPartyContactDetailHtml'};
        getTransHtml(contactTrans);
        contactDetailBody = true;
    }
});
</script>