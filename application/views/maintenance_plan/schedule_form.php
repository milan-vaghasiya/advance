<form id="scheduleForm">
	<div class="col-md-12">
		<div class="row form-group">
			<div class="col-md-2 form-group">
				<label for="schedule_date">Schedule Date</label>
				<input type="date" id="schedule_date" name="schedule_date" class="form-control req" value="<?=(!empty($dataRow->schedule_date))?$dataRow->schedule_date:date("Y-m-d")?>" />	
			</div>								
		</div>
	</div>
	<hr>
	<div class="col-md-12 mt-3">
		<div class="error general_error"></div>
		<div class="row form-group">
			<div class="table-responsive">
				<table id="scheduleItems" class="table table-striped table-borderless">
					<thead class="thead-info">
						<tr>
							<th style="width:5%;">#</th>
							<th>Machine</th>
							<th>Activity</th>
							<th>Checking Frequency</th>
							<th>Last Maintenance Date</th>
							<th>Due Date</th>
							<th class="text-center" style="width:10%;">Action</th>
						</tr>
					</thead>
					<tbody id="tempItem" class="temp_item">
						<?php
						if(!empty($dataRow)){
							$i=1;
							foreach($dataRow as $row){
								$last_maintenance_date = date("d-m-Y",strtotime($row->last_maintence_date));
								$due_date = '';
								if($row->checking_frequancy == 'Monthly'){ $due_date = date("d-m-Y",strtotime($last_maintenance_date.' +1 months')); }
								if($row->checking_frequancy == 'Quarterly'){ $due_date = date("d-m-Y",strtotime($last_maintenance_date.' +3 months')); }
								elseif($row->checking_frequancy == 'Half Yearly'){ $due_date = date("d-m-Y",strtotime($last_maintenance_date.' +6 months')); }
								elseif($row->checking_frequancy == 'Yearly'){ $due_date = date("d-m-Y",strtotime($last_maintenance_date.' +12 months')); }

								echo '<tr>
									<td>'.$i++.'</td>
									<td>
										'.(!empty($row->item_code) ? '['.$row->item_code.'] ' : '').$row->item_name.'
										<input type="hidden" name="machine_id[]" id="machine_id" value="'.$row->machine_id.'">
										<input type="hidden" name="id[]" id="id" value="">
									</td>
									<td>
										'.$row->activities.'
										<input type="hidden" name="activity_id[]" id="activity_id" value="'.$row->activity_id.'">
										<input type="hidden" name="main_id[]" id="main_id" value="'.$row->main_id.'">
									</td>
									<td>
										'.$row->checking_frequancy.'
										<input type="hidden" name="checking_frequancy[]" id="checking_frequancy" value="'.$row->checking_frequancy.'">
									</td>
									<td>
										'.$last_maintenance_date.'
										<input type="hidden" name="last_maintenance_date[]" id="last_maintenance_date" value="'.$last_maintenance_date.'">
									</td>
									<td>
										'.$due_date.'
										<input type="hidden" name="due_date[]" id="due_date" value="'.$due_date.'">
									</td>
									<td class="text-center">
                            			<a class="btn btn-outline-danger btn-sm permission-remove" href="javascript:void(0)" onclick="Remove(this);" datatip="Remove" flow="down"><i class="mdi mdi-trash-can-outline"></i></a>
									</td>
								</tr>';
							}
						}
						?>
					</tbody>
				</table>
			</div>
		</div>
	</div>
</form>
<script>
function Remove(button) {
    var tableId = "scheduleItems";
	var row = $(button).closest("TR");
	var table = $("#"+tableId)[0];
	table.deleteRow(row[0].rowIndex);
	$('#'+tableId+' tbody tr td:nth-child(1)').each(function (idx, ele) {
		ele.textContent = idx + 1;
	});
	var countTR = $('#'+tableId+' tbody tr:last').index() + 1;
	if (countTR == 0) {
		$("#tempItem").html('<tr id="noData"><td colspan="7" align="center">No data available in table</td></tr>');
	}
}
</script>    