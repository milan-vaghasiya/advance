function prcResponse(data,formId=""){
	if(data.status==1){	
		closeModal(formId); 
		Swal.fire( 'Success', data.message, 'success' );
		loadPrcDetail();
    }else{
        if(typeof data.message === "object"){
            $(".error").html("");
            $.each( data.message, function( key, value ) {$("."+key).html(value);});
        }else{
            Swal.fire({ icon: 'error', title: data.message });
        }			
    }	
}

function getPRCAcceptHtml(data){
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
		url: base_url  + 'sopDesk/' + fnget,
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
			loadPrcDetail();
			initSelect2();
			if(tfoot_id != ""){
				$("#"+table_id+" #"+tfoot_id).html('');
				$("#"+table_id+" #"+tfoot_id).html(res.tfootData);
			}
	});
}

// 28-05-2024
function getPRCMovementHtml(data){
    var pending_qty = data.pending_qty || "";
	var postData = data.postData || {};
	var fnget = data.fnget || "";
	var controllerName = data.controller || controller;
	
	$.ajax({
		url: base_url  + 'app/sop/' + fnget,
		data:postData,
		type: "POST",
		dataType:"json",
		beforeSend: function() {
			$("."+data.div_id).html('Loading...');
		},
	}).done(function(res){
		$("."+data.div_id).html('');
		$("."+data.div_id).html(res.html);
		if(pending_qty){
			$("#"+pending_qty).html(res.pendingQty);
		}
		loadPrcDetail();
	});
}

function trashSop(data){
	var controllerName = data.controller || controller;
	var fnName = data.fndelete || "delete";
	var msg = data.message || "Record";
	var send_data = data.postData;
	var resFunctionName = data.res_function || "";
	
	Swal.fire({
		title: 'Are you sure?',
		text: "You won't be able to revert this!",
		icon: 'warning',
		showCancelButton: true,
		confirmButtonColor: '#3085d6',
		cancelButtonColor: '#d33',
		confirmButtonText: 'Yes, delete it!',
	}).then(function(result) {
		if (result.isConfirmed){
			$.ajax({
				url: base_url + controllerName + '/' + fnName,
				data: send_data,
				type: "POST",
				dataType:"json",
			}).done(function(response){
				if(resFunctionName != ""){
					window[resFunctionName](response);
				}else{
					if(response.status==0){
						Swal.fire( 'Sorry...!', response.message, 'error' );
					}else{
						Swal.fire( 'Deleted!', response.message, 'success' );
					}	
				}
			});
		}
	});
	
}