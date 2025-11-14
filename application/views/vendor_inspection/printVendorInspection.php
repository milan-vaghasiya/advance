<div class="row">
	<div class="col-12">
		<table class="table  item-list-bb" style="margin-top:2px;">
			<tr class="text-left">
				<th width="10%">Item Name</th>
				<td width="30%"><?=(!empty($vendorInspectData->item_name)) ? $vendorInspectData->item_name : ""?></td>
				<th width="10%">Prc Date </th>
				<td width="10%"><?=((!empty($vendorInspectData->prc_date)) ? $vendorInspectData->prc_date : "")?></td>
				<th width="10%">Prc No.</th>
				<td width="10%"><?=((!empty($vendorInspectData->prc_number)) ? $vendorInspectData->prc_number : "")?></td>
				<th width="10%">Process</th>
				<td width="10%"><?=(!empty($prcLog->process_name)) ? $prcLog->process_name : ""?></td>
			</tr>
			<tr class="text-left">
				<th>Log Date</th>
				<td><?=(!empty($prcLog->trans_date)) ? formatDate($prcLog->trans_date) : ""?></td>
				<th>In Challan No.</th>
				<td><?=(!empty($prcLog->in_challan_no)) ? $prcLog->in_challan_no : ""?></td>
				<th>Qty</th>
				<td><?=(!empty($prcLog->qty)) ? $prcLog->qty : ""?></td>
				<th>Challan No.</th>
				<td><?=((!empty($vendorInspectData->challan_no)) ? $vendorInspectData->challan_no : "")?></td>
			</tr>
		</table>
		
		<table class="table item-list-bb" style="margin-top:10px;">
			<thead>
				<tr style="text-align:center;" class="bg-light">
					<th rowspan="2" style="width:3%;">#</th>
					<th rowspan="2" style="width:10%;">Parameter</th>
					<th rowspan="2" style="width:10%;">Specification</th>
					<th colspan="2" style="width:10%">Tolerance</th>
					<th colspan="2" style="width:10%">Specification Limit</th>
					<th rowspan="2" style="width:5%;">Char Class</th>
					<th rowspan="2" style="width:10%;">Instrument</th>
					<th rowspan="2" style="width:5%;">Size</th>
					<th rowspan="2" style="width:7%;">Frequency</th>
					<?= $theadData; ?>
					<th rowspan="2" style="width:7%">Result</th>
				</tr>
				<tr class="bg-light">
					<th style="width:5%">Min</th>
					<th style="width:5%">Max</th>
					<th style="width:5%">LSL</th>
					<th style="width:5%">USL</th>
					<?=$theadData2?>
				</tr>
			</thead>
			<tbody>
				<?= $tbodyData; ?>
			</tbody>
		</table>
	</div>
</div>