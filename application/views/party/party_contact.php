<form id="partyContactForm" data-res_function="partyContactHtml">
    <div class="row">
        <input type="hidden" name="id" id="id" class="id" value="" />
        <input type="hidden" name="party_id" id="party_id" class="party_id" value="<?=$party_id?>" />
        <input type="hidden" name="is_default" id="is_default" class="is_default" value="0" />

        <div class="col-md-3 form-group">
			<label for="contact_person">Contact Person</label>
			<input type="text" name="contact_person" id="contact_person" class="form-control req" value="<?=(!empty($dataRow->contact_person))?$dataRow->contact_person:""?>">
		</div>

		<div class="col-md-3 form-group">
			<label for="designation">Contact Designation</label>
			<input type="text" name="designation" id="designation" class="form-control req" value="<?=(!empty($dataRow->designation))?$dataRow->designation:""?>">
		</div>

		<div class="col-md-3 form-group">
			<label for="party_mobile">Mobile No.</label>
			<input type="text" name="party_mobile" id="party_mobile" class="form-control numericOnly req" value="<?=(!empty($dataRow->party_mobile))?$dataRow->party_mobile:""?>">
		</div>

        <div class="col-md-3 form-group">
            <label for="party_email">Email</label>
            <div class="input-group">
                <input type="text" name="party_email" id="party_email" class="form-control req" value="<?=(!empty($dataRow->party_email))?$dataRow->party_email:""?>">
                <div class="input-group-append">                                           
                    <?php
                    $param = "{'formId':'partyContactForm','fnsave':'savePartyContact','controller':'parties','res_function':'partyContactHtml','txt_editor':''}";
                    ?>
                    <button type="button" class="btn btn-success btn-block waves-effect save-form" onclick="customStore(<?=$param?>)"><i class="fa fa-check"></i> Add</button>
                </div>
            </div>
        </div>
    </div>
</form>

<hr>
    <div class="row">
        <div class="table-responsive">
            <table id="partyContactId" class="table table-bordered align-items-center">
                <thead class="thead-info">
                    <tr>
                        <th style="width:5%;">#</th>
                        <th>Contact Person</th>
                        <th>Contact designation</th>
                        <th>Mobile No.</th>
                        <th>Email</th>
                        <th class="text-center" style="width:10%;">Action</th>
                    </tr>
                </thead>
                <tbody id="partyContactBody" class="scroll-tbody scrollable maxvh-60">
                </tbody>
            </table>
        </div>
    </div>
</div>
<script>
var tbodyData = false;
$(document).ready(function(){
    if(!tbodyData){
        var postData = {'postData':{'party_id':$("#party_id").val()},'table_id':"partyContactId",'tbody_id':'partyContactBody','tfoot_id':'','fnget':'partyContactHtml'};
        getTransHtml(postData);
        tbodyData = true;
    }
});

function partyContactHtml(data,formId="partyContactForm"){ 
    if(data.status==1){
        $('#'+formId)[0].reset();
        var postData = {'postData':{'party_id':$("#party_id").val()},'table_id':"partyContactId",'tbody_id':'partyContactBody','tfoot_id':'','fnget':'partyContactHtml'};
        getTransHtml(postData);
    }else{
        if(typeof data.message === "object"){
            $(".error").html("");
            $.each( data.message, function( key, value ) {$("."+key).html(value);});
        }else{
            initTable();
            Swal.fire({ icon: 'error', title: data.message });
        }			
    }
}
</script>