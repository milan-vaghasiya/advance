<?php $this->load->view('includes/header'); ?>
<div class="page-content-tab">
	<div class="container-fluid">
        <div class="row">
            <div class="col-sm-12">
				<div class="page-title-box">
                    <div class="float-start">
					    <ul class="nav nav-pills">
                            <li class="nav-item"> 
                                <a href="<?=base_url($headData->controller.'/materialForecast/1')?>"  class="nav-tab btn waves-effect waves-light btn-outline-dark active" style="outline:0px" aria-expanded="false">Forecast</a> 
                            </li>
                            <li class="nav-item"> 
                                 <a href="<?=base_url($headData->controller.'/index/1')?>" id="pending_pi" class="nav-tab btn waves-effect waves-light btn-outline-danger " style="outline:0px" aria-expanded="false">Pending</a> 
                            </li>
                            <li class="nav-item"> 
                            <a href="<?=base_url($headData->controller.'/index/2')?>" id="complete_pi" class="nav-tab btn waves-effect waves-light btn-outline-success" style="outline:0px" aria-expanded="false">Completed</a> 
                            </li>
                            <li class="nav-item"> 
                                <a href="<?=base_url($headData->controller.'/index/3')?>" id="close_pi" class="nav-tab btn waves-effect waves-light btn-outline-primary" style="outline:0px" aria-expanded="false"> Closed</a> 
                            </li>
                        </ul>
					</div>
					
                    <div class="float-end">
						<ul class="nav nav-pills">
							<li class="nav-item"> 
								<a href="<?=base_url($headData->controller.'/materialForecast/1')?>" id="pending_short" class="nav-tab btn waves-effect waves-light btn-outline-danger <?=(($status == 1)? 'active':'')?>" style="outline:0px" aria-expanded="false">Shortage</a> 
							</li>
							<li class="nav-item"> 
								 <a href="<?=base_url($headData->controller.'/materialForecast/2')?>" id="complet_short" class="nav-tab btn waves-effect waves-light btn-outline-success <?=(($status == 2)? 'active':'')?>" style="outline:0px" aria-expanded="false">Available</a> 
							</li>
						</ul>
					</div>
				</div>
            </div>
		</div>
        <div class="row">
            <div class="col-12">
				<div class="col-12">
					<div class="card">
                        <div class="card-body">
                            <div class="table-responsive">
                                <table id='forecastTable' class="table table-bordered ssTable ssTable-cf" data-url='/getForecastDtRows/<?=$status?>'></table>
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
	initbulkPOButton();
	$(document).on('click', '.BulkRequest', function() {
		if ($(this).attr('id') == "masterSelect") {
			if ($(this).prop('checked') == true) {
				$(".bulkPO").show();
				$(".bulkEnq").show();
				$("input[name='ref_id[]']").prop('checked', true);
			} else {
				$(".bulkPO").hide();
				$(".bulkEnq").hide();
				$("input[name='ref_id[]']").prop('checked', false);
			}
		} else {
			if ($("input[name='ref_id[]']").not(':checked').length != $("input[name='ref_id[]']").length) {
				$(".bulkPO").show();
				$(".bulkEnq").show();
				$("#masterSelect").prop('checked', false);
			} else {
				$(".bulkPO").hide();
				$(".bulkEnq").hide();
			}

			if ($("input[name='ref_id[]']:checked").length == $("input[name='ref_id[]']").length) {
				$("#masterSelect").prop('checked', true);
				$(".bulkPO").show();
				$(".bulkEnq").show();
			}
			else{$("#masterSelect").prop('checked', false);}
		}
	});
	
	$(document).on('click', '.bulkPO', function() {
		var ref_id = []; var qty = []; var sendData = [];
		$("input[name='ref_id[]']:checked").each(function() {
			ref_id = $(this).val();
			sendData.push({ id:ref_id});//08-04-25
		});
		var postData = encodeURIComponent(JSON.stringify(sendData));

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
				window.open(base_url + 'purchaseOrders/addPOFromForecast/' + postData, '_self');
			}
		});
	});

	$(document).on('click', '.bulkEnq', function() {
		var ref_id = []; var qty = []; var sendData = [];
		$("input[name='ref_id[]']:checked").each(function() {
			ref_id = $(this).val();
			// qty = $(this).data('qty');//08-04-25
			sendData.push({ id:ref_id});
		});
		var postData = encodeURIComponent(JSON.stringify(sendData));

		Swal.fire({
			title: 'Are you sure?',
			text: 'Are you sure want to generate RFQ?',
			icon: 'warning',
			showCancelButton: true,
			confirmButtonColor: '#3085d6',
			cancelButtonColor: '#d33',
			confirmButtonText: 'Yes, Do it!',
		}).then(function(result) {
			if (result.isConfirmed){				
				window.open(base_url + 'purchaseDesk/addEnqFromForecast/' + postData, '_self');
			}
		});
	});
});

function initbulkPOButton() {
	var bulkPOBtn = '<button class="btn btn-outline-dark bulkPO" tabindex="0" aria-controls="forecastTable" type="button"><span>Bulk PO</span></button>';
	var bulkEnqBtn = '<button class="btn btn-outline-dark bulkEnq" tabindex="0" aria-controls="forecastTable" type="button"><span>Bulk RFQ</span></button>';
	$("#forecastTable_wrapper .dt-buttons").append(bulkEnqBtn);
	$("#forecastTable_wrapper .dt-buttons").append(bulkPOBtn);
	$(".bulkPO").hide();
	$(".bulkEnq").hide();
}
</script>