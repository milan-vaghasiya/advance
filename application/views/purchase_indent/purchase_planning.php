<?php $this->load->view('includes/header'); ?>
<div class="page-content-tab">
	<div class="container-fluid">
        <div class="row">
            <div class="col-12">
				<div class="col-12">
					<div class="card">
                        <div class="card-body">
                            <div class="table-responsive">
								<button type="button" class="refreshReportData" style="display:none"></button>
								<table id='reportTable' class="table table-bordered">
									<thead class="thead-info">
										<tr >
											<th>Action</th>
											<th>
												<input type="checkbox" id="masterSelect" class="filled-in chk-col-success BulkRequest" value=""><label for="masterSelect">ALL</label>
											</th>
											<th>#</th>
											<th>Raw Material</th>
											<th>Requirement</th>
											<th>RM Stock</th>
											<th>Pending GRN</th>
											<th>Pending QC</th>
											<th>Shortage</th>
										</tr>
									</thead>
									<tbody id="tbodyData">

									</tbody>
								</table>
                            </div>
                        </div>
					</div>
				</div>
            </div>
        </div>
    </div>
</div>
<?php $this->load->view('includes/footer'); ?>
<script>
$(document).ready(function() {
	reportTable();
	setTimeout(function(){$(".refreshReportData").trigger('click');},500);
	$(document).on('click', '.refreshReportData', function() {
		$.ajax({
			url: base_url + controller + '/getPurchasePlanDTRows',
			data: {},
			type: "POST",
			dataType:'json',
			success:function(data){
				$("#reportTable").DataTable().clear().destroy();
				$("#tbodyData").html(data.tbodyData);
				reportTable();
				initbulkPOButton();
			}
		});
	});
	$(document).on('click', '.BulkRequest', function() {
		if ($(this).attr('id') == "masterSelect") {
			if ($(this).prop('checked') == true) {
				$(".bulkPO").show();
				$("input[name='ref_id[]']").prop('checked', true);
			} else {
				$(".bulkPO").hide();
				$("input[name='ref_id[]']").prop('checked', false);
			}
		} else {
			if ($("input[name='ref_id[]']").not(':checked').length != $("input[name='ref_id[]']").length) {
				$(".bulkPO").show();
				$("#masterSelect").prop('checked', false);
			} else {
				$(".bulkPO").hide();
			}

			if ($("input[name='ref_id[]']:checked").length == $("input[name='ref_id[]']").length) {
				$("#masterSelect").prop('checked', true);
				$(".bulkPO").show();
			}
			else{$("#masterSelect").prop('checked', false);}
		}
	});
	$(document).on('click', '.bulkPO', function() {
		var sendData = [];
		$("input[name='ref_id[]']:checked").each(function() {
			item_id = $(this).val();
			qty = $(this).data('qty');
			sendData.push({ item_id:item_id,qty:qty});
		});
		var postData = JSON.stringify(sendData);

		Swal.fire({
			title: 'Are you sure?',
			text: 'Are you sure want to generate PO?',
			icon: 'warning',
			showCancelButton: true,
			confirmButtonColor: '#3085d6',
			cancelButtonColor: '#d33',
			confirmButtonText: 'Yes, Do it!',
		}).then(function(result) {
			if (result.isConfirmed){	
				// Create a form
				var form = document.createElement('form');
				form.method = 'POST';
				form.action = base_url + 'purchaseOrders/addPOFromPPC';
				form.style.display = 'none';

				// Create hidden input
				let input = document.createElement('input');
				input.type = 'hidden';
				input.name = 'data';
				input.value = postData; 
				form.appendChild(input);
				document.body.appendChild(form);

				// Submit the form
				form.submit();
			}
		});
		
	});
});
function initbulkPOButton() {
	var bulkPOBtn = '<button class="btn btn-outline-dark bulkPO" tabindex="0" aria-controls="reportTable" type="button"><span>Bulk PO</span></button>';
	$("#reportTable_wrapper .dt-buttons").append(bulkPOBtn);
	$(".bulkPO").hide();
}


</script>