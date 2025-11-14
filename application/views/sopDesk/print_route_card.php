<style>table, .table{width:100% !important;border-collapse:collapse !important;}td,th{border-collapse:collapse !important;}</style>
<?php
	$cutWt = (!empty($cutingData->cut_weight) ? explode('-',$cutingData->cut_weight) : array());
	$cut_weight = (!empty($cutWt[1]) ? $cutWt[1] : (!empty($cutWt[0])?$cutWt[0]:''));
	
	$forgeArr = (!empty($prcProcessData[0]) ? $prcProcessData[0] : array());
	$forge_weight = (!empty($forgeArr->finish_wt) ? $forgeArr->finish_wt : 0);
?>
<div class="row">
	<div class="col-12">
		<table class="table" style="border-bottom:1px solid #000000;">
			<tr>
			    <td style="width:20%;"></td>
				<td class="text-uppercase text-center" style="font-size:1.3rem;font-weight:bold;width:40%;">PROCESS ROUTE CARD</td>
				<td class="text-uppercase text-right" style="font-size:1.3rem;font-weight:bold;width:20%;"></td>
			</tr>
		</table>
		<table class="table item-list-bb">
			<tr class="text-left">
				<th style="width:15%" class="bg-light">PRC No.</th>
				<td style="width:25%"><?= $prcData->prc_number ?></td>
				<th style="width:15%" class="bg-light">PRC Quantity</th>
				<td style="width:15%"><?= floatval($prcData->prc_qty) ?> NOS</td>
				<th style="width:15%" class="bg-light">PRC Date</th>
				<td style="width:15%"><?= formatDate($prcData->prc_date) ?></td>
			</tr>
		    <tr class="text-left">
				<th class="bg-light">Product Name</th>
				<td colspan="3"><?= '['.$prcData->item_code .'] '.$prcData->item_name ?></td>
				<th class="bg-light">Drw/Rev No</th>
				<td><?=$prcData->drw_no.' / '.(!empty($prcData->rev_no)?$prcData->rev_no:'00') ?></td>
			</tr>
			<tr class="text-left">
				<th class="bg-light">Cut Weight</th>
				<td><?= floatval($cut_weight) ?> KGS</td>
				<th class="bg-light">Forge Weight</th>
				<td><?= floatval($forge_weight) ?> KGS</td>
				<th class="bg-light">Final Weight</th>
				<td><?= floatval($prcData->wt_pcs) ?> KGS</td>
			</tr>
			<tr class="text-left">
				<th class="bg-light">SO No </th>
				<td><?=$prcData->so_number?></td>
				<th class="bg-light">Customer PO No.</th>
				<td><?=$prcData->doc_no?></td>
				<th class="bg-light">Created By</th>
				<td><?= $prcData->emp_name ?></td>
			</tr>
            <tr class="text-left">
				<th class="bg-light">Remark</th>
				<td colspan="3"><?= $prcData->remark ?></td>
				<th class="bg-light">Order Type</th>
				<td><?= $prcData->mfg_type ?></td>
			</tr>
		</table>
		<h4 class="row-title" style="margin-top:10px">Material Detail:</h4>
		<table class="table item-list-bb">
			<tr class="thead-gray">
				<th>Item Description</th>
				<th>Grade</th>
				<th style="width:15%;">Batch No.</th>
				<th style="width:15%;">Required Qty</th>
				<th style="width:15%;">Issued Qty</th>
			</tr>
			<?php
			if($prcData->cutting_flow == 2):
				if (!empty($cutingData)) :
					$i = 1;
					// foreach ($cutingData as $row) :
						echo '<tr>';
						echo '<td>' . $cutingData->item_name . '</td>';
						echo '<td>' . $cutingData->material_grade . '</td>';
						echo '<td class="text-center">' .(($prcData->cutting_flow == 2)?((!empty($prcData->batch_no))?'('.$prcData->batch_no.')  ':''):''). $prcMaterialData[0]->batch_no . '</td>';
						echo '<td class="text-center">' . floatVal(abs($prcData->prc_qty * $cutingData->ppc_qty)) . '</td>';
						echo '<td class="text-center">'.(!empty($prcMaterialData[0]->issue_qty)?(($prcMaterialData[0]->issue_qty - $prcMaterialData[0]->return_qty) * $cutingData->avg_wt):'').'</td>';
						echo '</tr>';
					// endforeach;
				else :
					echo '<tr><th class="text-center" colspan="2">Record Not Found !</th></tr>';
				endif;
			else:
				if (!empty($prcMaterialData)) :
					$i = 1;
					foreach ($prcMaterialData as $row) :
						echo '<tr>';
						echo '<td>' . $row->item_name . '</td>';
						echo '<td>' . $row->material_grade . '</td>';
						echo '<td class="text-center">' .(($prcData->cutting_flow == 2)?((!empty($prcData->batch_no))?'('.$prcData->batch_no.')  ':''):''). $row->batch_no . '</td>';
						echo '<td class="text-center">' . floatVal(abs($prcData->prc_qty * $row->ppc_qty)) . '</td>';
						echo '<td class="text-center">' . floatVal(abs($row->issue_qty)) . '</td>';
						echo '</tr>';
					endforeach;
				else :
					echo '<tr><th class="text-center" colspan="2">Record Not Found !</th></tr>';
				endif;
			endif;
			
			?>
		</table>
		<h4 class="row-title" style="margin-top:10px">Process Detail:</h4>
		<table class="table item-list-bb">
			<tr class="text-center thead-gray">
				<th style="width:5%;">No.</th>
				<th class="text-left">Process Detail</th>
				<th style="width:12%;">Inward Qty</th>
				<th style="width:12%;">Production Qty</th>
				<th style="width:12%;">Rej. Found</th>
				<th style="width:12%;">RW. Done</th>
				<th style="width:12%;">OK Qty</th>
				<th style="width:12%;">Pending Qty</th>
			</tr>
			<?php
			if (!empty($prcProcessData)) :
				$i = 1;
                if($prcData->status > 1):
					if($prcData->cutting_flow == 2){
						echo '<tr>
								<td class="text-center">'.$i++.'</td>
								<td>Cutting</td>
								<td class="text-center">'.(!empty($prcMaterialData[0]->issue_qty)?($prcMaterialData[0]->issue_qty - $prcMaterialData[0]->return_qty):'').'</td>
								<td class="text-center">'.(!empty($prcMaterialData[0]->issue_qty)?($prcMaterialData[0]->issue_qty - $prcMaterialData[0]->return_qty):'').'</td>
								<td></td>
								<td></td>
								<td></td>
								<td></td>
							  </tr>';
					}
                    foreach ($prcProcessData as $row) :
                        $currentProcess = (!empty($row->current_process)?$row->current_process : 'Initial Stage');
                        $in_qty = (!empty($row->in_qty)?$row->in_qty:0);
						if($prcData->cutting_flow == 2 && $row->sequence == 1){
							$in_qty = (!empty($prcMaterialData[0]->issue_qty)?($prcMaterialData[0]->issue_qty - $prcMaterialData[0]->return_qty):'');
						}
                        $ok_qty = (!empty($row->ok_qty)?$row->ok_qty:0);
                        $rej_found_qty = (!empty($row->rej_found)?$row->rej_found:0);
                        $rej_qty = (!empty($row->rej_qty)?$row->rej_qty:0);
                        $rw_qty = (!empty($row->rw_qty)?$row->rw_qty:0);
                        $rw_ok_qty = (!empty($row->rw_ok_qty)?$row->rw_ok_qty:0);
                        $pendingReview = $rej_found_qty - $row->review_qty;
                        $pending_production =($in_qty * $row->output_qty) - ($ok_qty+$rej_qty+$rw_qty+$pendingReview);

                        echo '<tr>';
                        echo '<td class="text-center">' . $i++ . '</td>';
                        echo '<td class="text-left">' . $currentProcess . '</td>';
                        echo '<td class="text-center">' . floatVal($in_qty) . '</td>';
                        echo '<td class="text-center">' . floatVal($ok_qty) . '</td>';
                        echo '<td class="text-center">' . floatVal($rej_found_qty) . '</td>';
                        echo '<td class="text-center">' . floatVal($rw_ok_qty) . '</td>';
                        echo '<td class="text-center">' . floatVal(($ok_qty-$rw_ok_qty)) . '</td>';
                        echo '<td class="text-center">' . floatVal($pending_production) . '</td>';
                        echo '</tr>';
                    endforeach;
                else:
                    foreach($prcProcessData as $key=>$row){
                        echo '<tr>';
							echo '<td class="text-center">' . $i++ . '</td>';
							echo '<td class="text-left">' . $row->process_name . '</td>';
							echo '<td class="text-center">0</td>';
							echo '<td class="text-center">0</td>';
							echo '<td class="text-center">0</td>';
							echo '<td class="text-center">0</td>';
                        echo '</tr>';
                    }
                endif;
			else :
				echo '<tr><th class="text-center" colspan="6">Record Not Found !</th></tr>';
			endif;
			?>
		</table>
		<?php
		$prcLogs = array_reduce($logData, function($prcLogs, $log) { $prcLogs[$log->process_name][] = $log; return $prcLogs; }, []);
		foreach ($prcLogs as $process_name=>$logs):
			?>
			<hr>
			<table class="table item-list-bb">
				<thead>
					<tr class="bg-light">
						<th colspan="10" class="text-left"><?=$process_name?> : </th>
					</tr>
					<tr style="background:#f9fafb">
						<th style="width:5%">#</th>
						<th style="width:10%">Date</th>
						<th style="width:10%">Shift</th>
						<th style="width:10%">Challan No</th>
						<th style="width:10%">Operator</th>
						<th style="width:10%">Machine</th>
						<th style="width:10%">Ok</th>
						<th style="width:10%">Rej</th>
						<th style="width:10%">Rw</th>
						<th style="width:10%">Production Time</th>
						<th style="width:15%">Remark</th>
					</tr>
				</thead>
				<tbody>
					<?php
					$i = 1;
					foreach($logs AS $row):
						?>
						<tr class="text-center">
							<td><?=$i++?></td>
							<td><?=formatDate($row->trans_date)?></td>
							<td><?=$row->shift_name?></td>
							<td><?=$row->in_challan_no?></td>
							<td><?=(($row->process_by == 3)? $row->processor_name : $row->emp_name)?></td>
							<td><?=(($row->process_by != 3)? (!empty($row->machine_code) ? $row->machine_code : $row->machine_name) : '' )?></td>
							<td><?=number_format($row->qty)?></td>
							<td><?=number_format($row->rej_found)?></td>
							<td><?=number_format($row->rw_qty)?></td>
							<td><?=$row->production_time?></td>
							<td><?=$row->remark?></td>
						</tr>
						<?php
					endforeach;
					?>
				</tbody>
			</table>
			<?php
		endforeach;
		?>
		
		<h4 class="row-title" style="margin-top:10px">Invoice Detail:</h4>
		<table class="table item-list-bb">
			<tr class="text-center thead-gray">
				<th style="width:5%;">Sr.No.</th>
				<th>Inv No.</th>
				<th>Inv Date</th>
				<th>Inv Qty</th>
			</tr>
			<?php
			if (!empty($invData)) :
				$i = 1;
				$total_qty = 0;
				foreach ($invData as $row) :
					$total_qty += $row->inv_qty;

					echo '<tr>';
					echo '<td class="text-center">' . $i++ . '</td>';
					echo '<td class="text-center">' . $row->inv_number . '</td>';
					echo '<td class="text-center">' . formatDate($row->inv_date) . '</td>';
					echo '<td class="text-center">' . floatVal($row->inv_qty) . '</td>';
					echo '</tr>';
				endforeach;

				echo '<tr><td colspan="2" class="text-center"></td><td class="text-center"><b>Total</b></td><td class="text-center"><b>'.$total_qty.'</b></td></tr>';
			endif;
			?>
		</table>
	</div>
</div>