<form data-res_function="getOutChallanHtml" >
    <div class="col-md-12">
        <div class="row">

            <input type="hidden" name="id" id="id" value="">
            <input type="hidden" name="ref_id" id="ref_id" value="<?=$ref_id?>" />
            <input type="hidden" name="challan_type" id="challan_type" value="1" />
            
            <div class="col-md-2 form-group">
                <label for="receive_date">Recieve Date</label>
                <input type="date" name="receive_date" id="receive_date" class="form-control req" value="<?=date("Y-m-d")?>">
            </div>

            <div class="col-md-6 form-group">
                <label for="challan_trans_id">Item Name</label>
                <select name="challan_trans_id" id="challan_trans_id" class="form-control select2 req">
                    <?=((!empty($options)) ? $options : '')?>
                </select>
            </div>

            <div class="col-md-4">
                <label for="receive_qty">Qty</label>
                <div class="input-group">
                    <input type="text" id="receive_qty" name="receive_qty" class="form-control floatOnly req" value="" min="0" />
                    <div class="input-group-append">
                        <?php
                            $param = "{'formId':'receiveItem','fnsave':'saveReceiveItem','controller':'outChallan','res_function':'getOutChallanHtml'}";
                        ?>
                        <button type="button" class="btn waves-effect waves-light btn-outline-success btn-save save-form float-right" onclick="customStore(<?=$param?>)" style="height:36px"><i class="fa fa-check"></i> Save</button>
                    </div>
                </div>                
            </div>

        </div>
        <hr>
        <div class="row">
            <div class="table-responsive">
            <table id="outChallanTbl" class="table table-bordered align-items-center">
                <thead class="thead-info">
                    <tr class="text-center">
                        <th style="width:5%;">#</th>
                        <th>Receive Date</th>
                        <th class="text-left">Item Name</th>
                        <th>Qty</th>
                        <th class="text-center" style="width:10%;">Action</th>
                    </tr>
                </thead>
                <tbody id="outChallanItems">
                </tbody>
            </table>
        </div>
        </div>
    </div>
</form>
<script>
var tbodyData = false;
$(document).ready(function(){
    setPlaceHolder();
    
    if(!tbodyData){
        var postData = {'postData':{'ref_id':$("#ref_id").val()},'table_id':"outChallanTbl",'tbody_id':'outChallanItems','tfoot_id':'','fnget':'outChallanHtml'};
        getReceiveTransHtml(postData);
        tbodyData = true;
    }
});

function getReceiveTransHtml(data){
	var postData = data.postData || {};
	var fnget = data.fnget || "";
	var controllerName = data.controller || controller;
	var resFunctionName = data.res_function || "";

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
		if(resFunctionName != ""){
			window[resFunctionName](response);
		}else{
			$("#"+table_id+" #"+tbody_id).html('');
			$("#"+table_id+" #"+tbody_id).html(res.tbodyData);
            $("#challan_trans_id").html(res.options);
            initTable();

			if(tfoot_id != ""){
				$("#"+table_id+" #"+tfoot_id).html('');
				$("#"+table_id+" #"+tfoot_id).html(res.tfootData);
			}
		}
	});
}

function getOutChallanHtml(data,formId="receiveItem"){ 
    if(data.status==1){
        $('#'+formId)[0].reset();
        var postData = {'postData':{'ref_id':$("#ref_id").val()},'table_id':"outChallanTbl",'tbody_id':'outChallanItems','tfoot_id':'','fnget':'outChallanHtml'};
        getReceiveTransHtml(postData);
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