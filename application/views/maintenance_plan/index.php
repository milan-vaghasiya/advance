<?php $this->load->view('includes/header'); ?>
<div class="page-content-tab">
	<div class="container-fluid">
        <div class="row">
			<div class="col-sm-12">
				<div class="page-title-box">
					<div class="float-end" style="width:15%">
						<select id="frequency_filter" class="form-control select2">
							<option value="">All Frequency</option>
							<option value="Monthly">Monthly</option>
							<option value="Quarterly">Quarterly</option>
							<option value="Half Yearly">Half Yearly</option>
							<option value="Yearly">Yearly</option>
						</select>
                    </div>
                    <div class="float-start">
						<ul class="nav nav-pills">
							<li class="nav-item"> <a href="<?= base_url($headData->controller . "/index/1") ?>" class="nav-tab btn btn-outline-info <?=($status == 1)?'active':''?>">Due Maintenance</a> </li>
							<li class="nav-item"> <a href="<?= base_url($headData->controller . "/index/2") ?>" class="nav-tab btn btn-outline-info <?=($status == 2)?'active':''?>">Planned</a> </li>
							<li class="nav-item"> <a href="<?= base_url($headData->controller . "/index/3") ?>" class="nav-tab btn btn-outline-info <?=($status == 3)?'active':''?>">Completed</a> </li>
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
                                <table id='mPlanTable' class="table table-bordered ssTable ssTable-cf" data-url='/getDTRows/<?=$status?>'></table>
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
$(document).ready(function(){

    initSchedulePlanButton();
	$(document).on("change","#frequency_filter",function(){
	    var maintence_frequancy = $(this).val();
	    var dataSet = {maintence_frequancy:maintence_frequancy};
	    initTable(dataSet);
        initSchedulePlanButton();
	});

	$(document).on('click', '.BulkPlanSchedule', function() {
		if ($(this).attr('id') == "masterSelect") {
			if ($(this).prop('checked') == true) {
				$(".bulkSchedule").show();
				$("input[name='ref_id[]']").prop('checked', true);
			} else {
				$(".bulkSchedule").hide();
				$("input[name='ref_id[]']").prop('checked', false);
			}
		} else {
			if ($("input[name='ref_id[]']").not(':checked').length != $("input[name='ref_id[]']").length) {
				$(".bulkSchedule").show();
				$("#masterSelect").prop('checked', false);
			} else {
				$(".bulkSchedule").hide();
			}

			if ($("input[name='ref_id[]']:checked").length == $("input[name='ref_id[]']").length) {
				$("#masterSelect").prop('checked', true);
				$(".bulkSchedule").show();
			}
			else{$("#masterSelect").prop('checked', false);}
		}
	});
	
	$(document).on('click', '.bulkSchedule', function() {
		var ref_id = [];
		$("input[name='ref_id[]']:checked").each(function() {
			ref_id.push(this.value);
		});
		var ids = ref_id.join(",");
		var send_data = { ids };
        var modalId = 'bs-right-xl-modal';
        var formId = 'scheduleForm';	
		var fnsave = 'saveSchedule';
		var zindex = "9999";
		var fnJson = "{'formId':'"+formId+"','fnsave':'"+fnsave+"','controller':'"+controller+"','txt_editor':''}";

		$.ajax({ 
            url: base_url + controller + '/schedulePlan',
            data: send_data,
            type: "POST"
        }).done(function(response){
			$('#'+modalId).modal('show');
			$("#"+modalId).addClass('modal-i-'+zindex);
			$('.modal-i-'+(zindex - 1)).removeClass('show');
			$("#"+modalId).css({'z-index':zindex,'overflow':'auto'});
			$("#"+modalId).addClass(formId+"Modal");
			$('#'+modalId+' .modal-title').html('Schedule Maintenance Plan');
			$('#'+modalId+' .modal-body').html(response);
			$("#"+modalId+" .modal-body form").attr('id',formId);
			$("#"+modalId+" .modal-footer .btn-save").attr('onclick',"store("+fnJson+");");
			initSelect2(); setPlaceHolder();     
			zindex++;
		});	
	});	
});

function initSchedulePlanButton() {
	var bulkScheduleBtn = '<button class="btn btn-outline-dark bulkSchedule" tabindex="0" aria-controls="mPlanTable" type="button"><span>Bulk Schedule</span></button>';	
	$("#mPlanTable_wrapper .dt-buttons").append(bulkScheduleBtn);
	$(".bulkSchedule").hide();
}
</script>