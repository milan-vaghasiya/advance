<?php
	$param = "{'formId':'Item_revision','fnsave':'saveItemRevision','controller':'items','res_function':'itemRevisionHtml'}";
?>
<form  data-res_function="itemRevisionHtml" >
    <div class="col-md-12">
		<div class="row">
            <input type="hidden" name="item_id" id="item_id" value="<?=$item_id?>" />
            <input type="hidden" name="id" id="id" value="" />
			<div class="col-md-4 form-group">
				<label for="drw_no">Drawing No.</label>
				<input type="text" id="drw_no" name ="drw_no" class="form-control req" placeholder="Drawing No." value=""  />
			</div>
			<div class="col-md-4 form-group">
				<label for="cust_rev_no">Cust. Rev. No.</label>
				<input type="text" id="cust_rev_no" name ="cust_rev_no" class="form-control req" placeholder="Cust. Rev. No." value=""  />
			</div>
			<div class="col-md-4 form-group">
				<label for="cust_rev_date">Cust. Rev. Date</label>
				<input type="date" id="cust_rev_date" name ="cust_rev_date" class="form-control req" value="<?=date('Y-m-d')?>"  />
			</div>
			<div class="col-md-4 form-group">
				<label for="rev_no">ATL Rev. No.</label>
				<input type="text" id="rev_no" name ="rev_no" class="form-control req" placeholder="Revision No." value=""  />
			</div>
			<div class="col-md-4 form-group">
				<label for="rev_date">ATL Rev. Date</label>
				<input type="date" id="rev_date" name ="rev_date" class="form-control req" value="<?=date('Y-m-d')?>"  />
			</div>
			<div class="col-md-4 form-group">
				<label for="">&nbsp;</label>
				<button type="button" class="btn btn-block btn-success save-form float-right " onclick="customStore(<?=$param?>)" style="height:35px"><i class="fa fa-check"></i> Save</button>
			</div>
        </div>
    </div>
</form>
<hr>
    <div class="row">
        <div class="col-md-12">
            <div class="table-responsive">
            <table id ="revItemId" class="table table-bordered">
                <thead class="thead-info">
                    <tr>
                        <th>#</th>
                        <th>Drawing No.</th>
                        <th>Cust. Rev. No.</th>
                        <th>Cust. Rev. Date</th>
                        <th>ATL Rev. No.</th>
                        <th>ATL Rev. Date</th>
                        <th class="text-center" style="width:10%;">Action</th>
                    </tr>
                </thead>
                <tbody id="tbodydata"></tbody>
            </table>
        </div>
    </div> 

<script>
var tbodyData = false;
$(document).ready(function(){
    if(!tbodyData){
        var postData = {'postData':{'item_id':$("#item_id").val()},'table_id':"revItemId",'tbody_id':'tbodydata','tfoot_id':'','fnget':'itemRevisionHtml'};
        getTransHtml(postData);
        tbodyData = true;
    }
});
function itemRevisionHtml(data,formId="Item_revision"){ 
    console.log(data);
    if(data.status==1){
        $('#'+formId)[0].reset();
        var postData = {'postData':{'item_id':$("#item_id").val()},'table_id':"revItemId",'tbody_id':'tbodydata','tfoot_id':'','fnget':'itemRevisionHtml'};
        getTransHtml(postData);
    }else{
        if(typeof data.message === "object"){
            $(".error").html("");
            $.each( data.message, function( key, value ) {$("."+key).html(value);});
        }else{
            Swal.fire({ icon: 'error', title: data.message });
        }			
    }		
}
</script>