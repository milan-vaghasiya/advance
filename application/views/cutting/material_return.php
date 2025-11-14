<div class="prcMaterial">
    <h6><?=$mtData->item_name?></h6>
    <?php
    $requiredQty  = $mtData->prc_qty * $mtData->ppc_qty;
    ?>
    <table class="table table-bordered">
        <tr>
            <tr>
                <th class="bg-light">Supplier</th>
                <td colspan="3"><?=$mtData->supplier_name?></td>
                <th class="bg-light">Batch No</th>
                <td><?=$mtData->batch_no?></td>
            </tr>
            <tr>
                <th class="bg-light"> Required Qty</th>
                <td><?=$requiredQty?></td>
                <th class="bg-light">Issue Qty</th>
                <td id="issue_qty"></td>
                <th class="bg-light">Used Qty</th>
                <td id="used_qty">  </td>
            </tr>
            <tr>
                <th class="bg-light">Returned Qty</th>
                <td  id="return_qty"></td>
                <th class="bg-light">Stock Qty</th>
                <td  id="stock_qty"></td>
                <th class="bg-light"> UOM</th>
                <td><?=$mtData->uom ?></td>
            </tr>
    </table>
</div>
<hr>
<h5>Material return : </h5>
<form data-res_function="getReturnResponse">
    <div class="row">
        <input type="hidden" name="prc_id" id="prc_id" value="<?=$mtData->prc_id?>">
        <input type="hidden" name="prc_bom_id" id="prc_bom_id" value="<?=$mtData->id?>">
        <input type="hidden" name="prc_number" id="prc_number" value="<?=$mtData->prc_number?>">
        <input type="hidden" name="item_id" id="item_id" value="<?=$mtData->item_id?>">
        <input type="hidden" name="process_id" id="process_id" value="<?=$mtData->process_id?>">
        <div class="col-md-4 form-group">
            <label for="return_type">Return Type</label>
            <select name="return_type" id="return_type" class="form-control">
                <option value="1">Return AS Usable</option>
                <option value="2">End Piece Return</option>
                <option value="3">Scrap</option>
            </select>
        </div>
        <div class="col-md-4 form-group location">
            <label for="location_id">Location</label>
            <?php
            $locations = !empty($mtData->location_id)?explode(",",$mtData->location_id):[];
            ?>
            <select id="location_id" name="location_id" class="form-control select2 req">
                <option value="">Select Location</option>
                <?=getLocationListOption($locationList,((!empty($locations[0]))?$locations[0]:0))?>
            </select>  
            
        </div>
        <div class="col-md-4 form-group batchNo">
            <label for="batch_no">Batch No.</label>
            <select id="batch_no" class="form-control select2 req" name="batch_no">
                <?php
                $batchList = !empty($mtData->batch_no)?explode(",",$mtData->batch_no):[];
                if(!empty($batchList)){
                    foreach($batchList as $batch_no){
                        ?>
                        <option value="<?=$batch_no?>"><?=$batch_no?></option>
                        <?php
                    }
                }
                ?>
            </select>
        </div>
        <div class="col-md-4 form-group">
            <label for="qty">Qty.(Kg)</label>
            <input type="text" name="qty" id="qty" class="form-control floatOnly req" placeholder="Enter Quantity" value="0" min="0" />
        </div>
        <div class="col-md-4 form-group endPcs">
            <label for="end_pcs">Qty (pcs)</label>
            <input type="text" name="end_pcs" id="end_pcs" class="form-control floatOnly" placeholder="Enter Quantity" value="0" min="0" />
        </div>
        <div class="col-md-8 from-group remarkDiv">
            <label for="remark">Remark</label>
            <input type="text" name="remark" id="remark" class="form-control" placeholder="Enter Remark" />
        </div>
        <div class="col-md-12 form-group float-end mt-2">
            <?php $param = "{'formId':'prcMaterial','fnsave':'storeReturnedMaterial','res_function':'getReturnResponse'}"; ?>
            <button type="button" class="btn waves-effect waves-light btn-success btn-save save-form float-right" onclick="customStore(<?=$param?>)" style="height:35px"><i class="fa fa-check"></i> Save</button>
        </div>
    </div>
</form>
<hr>
<div class="col-md-12">
    <div class="row">
        <h5 >Return Transaction :</h5>
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
        var postData = {'postData':{'prc_id':$("#prc_id").val(),'item_id':$("#item_id").val(),'process_id':$("#process_id").val()},'table_id':"returnTable",'tbody_id':'returnTbodyData','tfoot_id':'','fnget':'getReturnHtml'};
        getReturnHtml(postData);
        tbodyData = true;
    }
    setTimeout(function(){  $("#return_type").trigger("change"); }, 30);
    
    $(document).on('change','#return_type',function(){
		var return_type=$(this).val();
		if(return_type == 1){
            $(".location").show();
            $(".endPcs").hide();
            $(".remarkDiv").removeClass("col-md-12");
            $(".remarkDiv").addClass("col-md-8");
		}else if(return_type == 2){
            $(".location").hide();
            $(".endPcs").show();
            $(".remarkDiv").removeClass("col-md-12");
            $(".remarkDiv").addClass("col-md-8");
		}else if(return_type == 3){
            $(".location").hide();
            $(".endPcs").hide();
            $(".remarkDiv").removeClass("col-md-8");
            $(".remarkDiv").addClass("col-md-12");
		}
	});
});
function getReturnResponse(data,formId="prcMaterial"){ 
    if(data.status==1){
        $('#'+formId)[0].reset();
        var postData = {'postData':{'prc_id':$("#prc_id").val(),'item_id':$("#item_id").val(),'process_id':$("#process_id").val()},'table_id':"returnTable",'tbody_id':'returnTbodyData','tfoot_id':'','fnget':'getReturnHtml'};
        getReturnHtml(postData);
    }else{
        if(typeof data.message === "object"){
            $(".error").html("");
            $.each( data.message, function( key, value ) {$("."+key).html(value);});
        }else{
            Swal.fire({ icon: 'error', title: data.message });
        }			
    }
}

function getReturnHtml(data){
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
        $("#issue_qty").html(res.issue_qty);
        $("#used_qty").html(res.used_qty);
        $("#return_qty").html(res.return_qty);
        $("#stock_qty").html(res.stock_qty);
		initSelect2();
		if(tfoot_id != ""){
			$("#"+table_id+" #"+tfoot_id).html('');
			$("#"+table_id+" #"+tfoot_id).html(res.tfootData);
		}
	});
}
</script>