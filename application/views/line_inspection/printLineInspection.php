<div class="row">
	<div class="col-12">
		<table class="table  item-list-bb" style="margin-top:2px;">
			<tr  class="text-left">
				<th class="bg-light" style="width:10%;font-size:0.8rem;">Report Date</th>
				<td style="font-size:0.8rem;"><?=(!empty($lineInspectData->insp_date)) ? formatDate($lineInspectData->insp_date) : ""?></td>
				<th class="bg-light" style="width:10%;font-size:0.8rem;">PRC No</th>
				<td style="font-size:0.8rem;"><?=(!empty($lineInspectData->prc_number)) ? $lineInspectData->prc_number : ""?></td>
				<th class="bg-light" style="width:10%;font-size:0.8rem;">PRC Date</th>
				<td style="font-size:0.8rem;"><?=(!empty($lineInspectData->prc_date)) ? formatDate($lineInspectData->prc_date) : ""?></td>
			</tr>
			<tr class="text-left">
				<th class="bg-light" style="width:10%;font-size:0.8rem;">Part</th>
				<td style="font-size:0.8rem;"><?=(!empty($lineInspectData->item_name)) ? $lineInspectData->item_name : ""?></td>
				<th class="bg-light" style="width:10%;font-size:0.8rem;">Revision</th>
				<td style="font-size:0.8rem;"><?=(!empty($lineInspectData->rev_no)) ? $lineInspectData->rev_no : ""?></td>
				<th class="bg-light" style="width:10%;font-size:0.8rem;">Setup</th>
				<td style="font-size:0.8rem;"><?=(!empty($lineInspectData->process_name)) ?$lineInspectData->process_name:""?></td>
			</tr>
			<tr class="text-left">
				<th class="bg-light" style="width:10%;font-size:0.8rem;">Drg. No.</th>
				<td style="font-size:0.8rem;"><?=(!empty($lineInspectData->drw_no)) ? $lineInspectData->drw_no : ""?></td>
				<th class="bg-light" style="width:10%;font-size:0.8rem;">Operator</th>
				<td style="font-size:0.8rem;"><?=(!empty($lineInspectData->emp_name)) ? $lineInspectData->emp_name : ""?></td>
				<th class="bg-light" style="width:10%;font-size:0.8rem;">Machine</th>
				<td style="font-size:0.8rem;"><?=(!empty($lineInspectData->machine_name)) ? $lineInspectData->machine_name : ""?></td>
			</tr>
		</table>
		
		<table class="table item-list-bb">
			<thead>
				<tr style="text-align:center;" class="bg-light">
					<th rowspan="2">#</th>
					<th rowspan="2">Parameter</th>
					<th rowspan="2">Specification</th>
					<th colspan="2">Tolerance</th>
					<th colspan="2" style="width:10%">Specification Limit</th>
					<th rowspan="2">Instrument</th>
					<?= $theadData; ?>
				</tr>
				<tr class="bg-light">
					<th>Min</th>
					<th>Max</th>
					<th >LSL</th>
					<th >USL</th>
					<?=$theadData2?>
				</tr>
			</thead>
			<tbody>
				<?= $tbodyData; ?>
			</tbody>
		</table>
	</div>
</div>