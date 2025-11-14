<div class="col-md-12">
	<div class="accordion" id="accordionExample">
		<div class="accordion-item">
			<h6 class="accordion-header m-0" id="heading">
				<button class="accordion-button collapsed fw-semibold" type="button" data-bs-toggle="collapse" data-bs-target="#flush-collapse" aria-expanded="false" aria-controls="flush-collapse">
					Edit
				</button>
			</h6>
			<div id="flush-collapse" class="accordion-collapse collapse" aria-labelledby="heading" data-bs-parent="#accordionExample">
				<div class="accordion-body">
					<form id="viewDieHistory" data-res_function="dieHistoryHtml">
						<div class="row">

						<input type="hidden" name="id" id="id" value="" />
						<input type="hidden" name="die_id" id="die_id" value="<?=(!empty($dieData->id) ? $dieData->id : 0)?>" />
						<input type="hidden" name="die_job_id" id="die_job_id" value="<?=(!empty($dieData->die_job_id) ? $dieData->die_job_id : 0)?>" />

							<div class="col-md-3 form-group">
								<label for="height">Height <small>(MM)</small></label>
								<input type="text" name="height" id="height" class="form-control floatOnly req" value="" />
							</div>
							<div class="col-md-3 form-group">
								<label for="weight">Weight <small>(KGS)</small></label>
								<input type="text" name="weight" id="weight" class="form-control floatOnly req" value="" />                
							</div>
							<div class="col-md-3 form-group">
								<label for="length">Length</label>
								<input type="text" name="length" id="length" class="form-control floatOnly" value="" />
							</div>
							<div class="col-md-3 form-group">
								<label for="width">Width</label>
								<input type="text" name="width" id="width" class="form-control floatOnly" value="" />                
							</div>
							<div class="col-md-3 form-group">
								<label for="material_value">Die Value <small>(INR)</small></label>
								<input type="text" name="material_value" class="form-control floatOnly" value="" />                
							</div>
							<div class="col-md-3 form-group">
								<label for="attach_file">Attachment</label>
								<input type="file" name="attach_file" id="attach_file" class="form-control" />                
							</div>
							<div class="col-md-2 form-group">
								<?php $param = "{'formId':'viewDieHistory','fnsave':'updateDieHistory','controller':'dieMaster','res_function':'dieHistoryHtml'}"; ?>
								<button type="button" class="btn waves-effect waves-light btn-outline-success float-right mt-20 save-form btn-block" onclick="customStore(<?=$param?>)" style="height:36px;"><i class="fa fa-check"></i> Save</button>
							</div>
						</div>
					</form>
				</div>
			</div>
		</div>
	</div>
</div>

<hr>
<div class="row">
	<div class="col-md-12">
		<?php
		if(!empty($dieData->bom_item_name)){
			$itemName = (!empty($dieData->bom_item_code) ? $dieData->bom_item_code.' - ' : '').$dieData->bom_item_name;
		}else{
			$itemName = $dieData->item_name; 
		}
		?>
		<p class="text-primary font-bold">Material Name : <?=((!empty($itemName)) ? $itemName : (''))?></p>
		<div class="table-responsive">
			<table id="dieHistoryTbl" class="table table-bordered">
				<thead>
					<tr class="thead-info text-center">
						<th>#</th>
						<th>Prod./Recut Date</th>
						<th>Recut No</th>
						<th>POP No. & Date</th>
						<th>Die Run (min-max) <small>(NOS)</small></th>
						<th>Actual Die Run <br><span class="badge rounded-pill bg-primary" id="totalDieRun"></span></th>
						<th>Weight <small>(KGS)</small></th>
						<th>Height <small>(MM)</small></th>
						<th>Die Value <small>(INR)</small></th>
						<th>Length</th>
						<th>Width</th>
						<th>Value</th>
						<th>Attachment</th>
						<th>Action</th>
					</tr>
				</thead>
				<tbody id="dieHistoryBody"></tbody>
			</table>
		</div>
	</div>
</div>

<script>
var tbodyData = false;
$(document).ready(function(){
	if(!tbodyData){
        var postData = {'postData':{'die_id':$("#die_id").val(), 'die_job_id':$("#die_job_id").val()},'table_id':"dieHistoryTbl",'tbody_id':'dieHistoryBody','tfoot_id':'','fnget':'dieHistoryHtml','controller':'dieMaster'};
        getHistoryTransHtml(postData);
        tbodyData = true;
    }
});

function getHistoryTransHtml(data){
	var postData = data.postData || {};
	var fnget = data.fnget || "";
	var controllerName = data.controller || controller;
	var resFunctionName = data.res_function || "";

	var table_id = data.table_id || "";
	var tbody_id = data.tbody_id || "";
	var tfoot_id = data.tfoot_id || "";	
	
	$.ajax({
		url: base_url + controllerName + '/' + fnget,
		data:postData,
		type: "POST",
		dataType:"json",
		beforeSend: function() {
			if(table_id != ""){
				var columnCount = $('#'+table_id+' thead tr').first().children().length;
				$("#"+table_id+" #"+tbody_id).html('<tr><td colspan="'+columnCount+'" class="text-center">Loading...</td></tr>');
			}
		},
	}).done(function(res){
		if(resFunctionName != ""){
			window[resFunctionName](response);
		}else{
			$("#"+table_id+" #"+tbody_id).html('');
			$("#"+table_id+" #"+tbody_id).html(res.tbodyData);

			$('#totalDieRun').html('');
			$('#totalDieRun').html(res.totalDieRun);
			
			$('#flush-collapse').removeClass('show');
		}
	});
}

function dieHistoryHtml(data,formId="viewDieHistory"){ 
    if(data.status==1){
        $('#'+formId)[0].reset();
        Swal.fire({ icon: 'success', title: data.message});
        var postData = {'postData':{'die_id':$("#die_id").val(), 'die_job_id':$("#die_job_id").val()},'table_id':"dieHistoryTbl",'tbody_id':'dieHistoryBody','tfoot_id':'','fnget':'dieHistoryHtml','controller':'dieMaster'};
        getHistoryTransHtml(postData);
    }else{
        if(typeof data.message === "object"){
            $(".error").html("");
            $.each( data.message, function( key, value ) {$("."+key).html(value);});
        }else{
            Swal.fire({ icon: 'error', title: data.message });
        }			
    }
}

function editDie(data, button) {
	$.each(data, function (key, value) { 
		$("#viewDieHistory #" + key).val(value);
		$('#flush-collapse').addClass('show');
	});
}
</script>