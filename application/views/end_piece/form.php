<table class="table table-bordered">
        <tr>
            <tr>
                <th class="bg-light">Item</th>
                <td colspan="3"><?=$dataRow->item_name?></td>
                <th class="bg-light">Batch No</th>
                <td><?=$dataRow->batch_no?></td>
            </tr>
            <tr>
                <th class="bg-light" style="width:16%"> End Piece</th>
                <td style="width:17%"><?=$dataRow->end_pcs?></td>
                <th class="bg-light" style="width:16%">Weight</th>
                <td style="width:17%"><?=$dataRow->qty?></td>
                <th class="bg-light" style="width:16%">Pending Review</th>
                <td style="width:17%" id="pending_qty">  </td>
            </tr>
    </table>
<form data-res_function="getEndPcsResponse">
    <div class="row">
        <input type="hidden" name="return_id" id="return_id" value="<?=$dataRow->id?>">
        <input type="hidden" name="prc_id" id="prc_id" value="<?=$dataRow->prc_id?>">
        <input type="hidden" name="item_id" id="item_id" value="<?=$dataRow->item_id?>">
        <input type="hidden" name="batch_no" id="batch_no" value="<?=$dataRow->batch_no?>">
        <input type="hidden" name="prc_number" id="prc_number" value="<?=$dataRow->prc_number?>">
        <div class="col-md-4 form-group">
            <label for="return_type">Return Type</label>
            <select name="return_type" id="return_type" class="form-control">
                <option value="1">Add to stock</option>
                <option value="2">Scrap</option>
            </select>
        </div>
        <div class="col-md-4 form-group location">
            <label for="location_id">Location</label>
            <select id="location_id" name="location_id" class="form-control select2 req">
                <?=getLocationListOption($locationList,((!empty($dataRow->location_id))?$dataRow->location_id:0))?>
            </select>  
            
        </div>
        <div class="col-md-4 form-group">
            <label for="qty">Qty.(Kg)</label>
            <input type="text" name="qty" id="qty" class="form-control floatOnly req" placeholder="Enter Quantity" value="0" min="0" />
        </div>
        <div class="col-md-12 from-group remarkDiv">
            <label for="remark">Remark</label>
            <input type="text" name="remark" id="remark" class="form-control" placeholder="Enter Remark" />
        </div>
        <div class="col-md-12 form-group float-end mt-2">
            <?php $param = "{'formId':'addStock','fnsave':'saveStock','res_function':'getEndPcsResponse'}"; ?>
            <button type="button" class="btn waves-effect waves-light btn-success btn-save save-form float-right" onclick="customStore(<?=$param?>)" style="height:35px"><i class="fa fa-check"></i> Save</button>
        </div>
    </div>
</form>
<hr>
<div class="col-md-12">
    <div class="row">
        <h5 > Transaction :</h5>
        <div class="table-responsive  mb-3">
            <table id='returnTable' class="table table-bordered mb-5">
                <thead class="text-center thead-info">
                    <tr>
                        <th style="min-width:20px">#</th>
                        <th style="min-width:50px">Date</th>
                        <th style="min-width:50px">Location</th>
                        <th style="min-width:50px">Batch No</th>
                        <th>Qty.</th>
                        <th>Remark.</th>
                        <th style="width:50px;">Action</th>
                    </tr>
                </thead>
                <tbody id="returnTbodyData">
                   
                </tbody>
            </table>
        </div>
    </div>
</div>
<script>
var tbodyData = false;
$(document).ready(function(){
    if(!tbodyData){
        var postData = {'postData':{'return_id':$("#return_id").val(),'item_id':$("#item_id").val(),'prc_id':$("#prc_id").val()},'table_id':"returnTable",'tbody_id':'returnTbodyData','tfoot_id':'','fnget':'getEndPcsHtml'};
        getEndPcsHtml(postData);
        tbodyData = true;
    }
    setTimeout(function(){  $("#return_type").trigger("change"); }, 30);
    
    $(document).on('change','#return_type',function(){
		var return_type=$(this).val();
		if(return_type == 1){
            $(".location").show();
		}else{
            $(".location").hide();
		}
	});
});
function getEndPcsResponse(data,formId="addStock"){ 
    if(data.status==1){
        $('#'+formId)[0].reset();
        var postData = {'postData':{'return_id':$("#return_id").val()},'table_id':"returnTable",'tbody_id':'returnTbodyData','tfoot_id':'','fnget':'getEndPcsHtml'};
        getEndPcsHtml(postData);initTable();
        setTimeout(function(){  $("#return_type").trigger("change"); }, 30);

    }else{
        if(typeof data.message === "object"){
            $(".error").html("");
            $.each( data.message, function( key, value ) {$("."+key).html(value);});
        }else{
            Swal.fire({ icon: 'error', title: data.message });
        }			
    }
}

function getEndPcsHtml(data){
	var postData = data.postData || {};
	var fnget = data.fnget || "";
	var controllerName = data.controller || controller;

	var table_id = data.table_id || "";
	var thead_id = data.thead_id || "";
	var tbody_id = data.tbody_id || "";
	var tfoot_id = data.tfoot_id || "";	

	if(thead_id != ""){
		$("#"+table_id+" #"+thead_id).html(data.thead);
	}
	
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
		$("#"+table_id+" #"+tbody_id).html('');
		$("#"+table_id+" #"+tbody_id).html(res.tbodyData);
        $("#pending_qty").html(res.pending_qty);
		initSelect2();
		if(tfoot_id != ""){
			$("#"+table_id+" #"+tfoot_id).html('');
			$("#"+table_id+" #"+tfoot_id).html(res.tfootData);
		}
	});
}
</script>