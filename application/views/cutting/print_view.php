<style>table, .table{width:100% !important;border-collapse:collapse !important;}td,th{border-collapse:collapse !important;}</style>
<div class="row">
	<div class="col-12">
		<table class="table" style="border-bottom:1px solid #000000;margin-top: 25px; !important;">
			<tr>
			    <td style="width:20%;"></td>
				<td class="text-uppercase text-center" style="font-size:1rem;font-weight:bold;width:40%;">Work Order For Raw Material Cutting</td>
				<td class="text-uppercase text-right" style="font-size:0.8rem;font-weight:bold;width:20%;">PRD-F-03 (01/01-01-2025)</td>
			</tr>
		</table>
		<table class="table item-list-bb " style="margin-top: 25px; !important;">
			<tr class="text-left">
				<th style="width:15%" class=" bg-light">Job No.</th>
				<td style="width:25%"><?= $prcData->prc_number ?></td>
				<th style="width:20%" class=" bg-light">Job Quantity</th>
				<td style="width:10%"><?= floatval($prcData->prc_qty) ?></td>
				<th style="width:15%" class=" bg-light">Job Date</th>
				<td style="width:15%"><?= formatDate($prcData->prc_date) ?></td>
			</tr>
            <tr class="text-left" >
				<th style="width:25%" class=" bg-light"> Cut Weight (kg)</th>
				<td><?=$prcData->cut_weight ?></td>
				<th class=" bg-light">Cutting Length (mm)</th>
				<td ><?=floatval($prcData->cutting_length)?></td>
				<th class=" bg-light">Colour Code</th>
				<td><?=(!empty($mtrData->color_code) ? $mtrData->color_code : "");?></td>
			</tr>
            <tr class="text-left ">
                <th class=" bg-light">Product Name</th>
                <td colspan="3"><?= $prcData->item_name ?></td>
				<th class=" bg-light">So No.</th>
                <td><?=$prcData->so_number?></td>
            </tr>
            <tr class="text-left ">
                <th class=" bg-light">Customer</th>
                <td colspan="3"><?=$prcData->party_name?></td>
                <th class=" bg-light">Po No</th>
                <td><?=$prcData->doc_no?></td>
            </tr>
			<tr  class="text-left ">
				<th class=" bg-light">Remark</th>
				<td colspan="5"><?= $prcData->remark ?></td>
			</tr>
           
         
		</table>
		<h2 class="row-title"  style="margin-top: 25px; !important;">Material Detail:</h2>
		<table class="table   item-list-bb">
			<thead>
				<tr>
					<th class="bg-light"> Item</th>
					<th class="bg-light" >Issued Qty</th>
					<th class="bg-light" >Supplier</th>
					<th class="bg-light" >Batch No</th>
					<th class="bg-light" >Heat No</th>
				</tr>
			</thead>
			<tbody>
				<tr>
					<td><?= ((!empty($mtrData->item_name))?$mtrData->item_name:'')?></td>
					<td class="text-center"><?= (!empty($mtrData->issue_qty)?floatVal(abs($mtrData->issue_qty)):'')?></td>
					<td><?= (!empty($mtrData->supplier_name)?$mtrData->supplier_name:'')?></td>
					<td class="text-center"><?=( !empty($mtrData->batch_no)?$mtrData->batch_no:'')?></td>
					<td class="text-center"><?= (!empty($mtrData->heat_no)?$mtrData->heat_no:'')?></td>
				</tr>
			</tbody>
		</table>
		<!--<h2 class="row-title"  style="margin-top: 25px; !important;">Production Log Detail:</h2>
		<table class="table item-list-bb">
			<thead>
				<tr class="bg-light">
					<th style="width:20px">#</th>
					<th style="width:30px">Date</th>
					<th style="width:30px">Machine No.</th>
					<th style="width:30px">Qty</small></th>
					<th style="width:30px">Weight/ Nos.</th>
					<th style="width:80px">Shift</th>
					<th style="width:80px">Operator</th>
					<th style="width:100px">Remark</th>
				</tr>
			</thead>
			
			<tbody>
			<?php
					/*
					$i=1;$totalQty = 0;
					if(!empty($logData)):
						foreach($logData as $row):
						     echo '<tr>
									<td>' . $i . '</td>
									<td class="text-center" >' . formatDate($row->trans_date). '</td>
									<td class="text-center" >' . $row->processor_name . '</td>
									<td class="text-center" >' . floatval($row->qty) . '</td>
									<td class="text-center" >' . floatval($row->wt_nos) . '</td>
									<td class="text-center" >' . $row->shift_name . '</td>
									<td class="text-center" >' . $row->emp_name . '</td>
									<td class="text-center" >' . $row->remark . '</td>
								</tr>';
							$i++;
							$totalQty += $row->qty;
						endforeach;
					endif;
					*/
				?>
			</tbody>
		</table>-->

		
		<table class="table" style="border-top:1px solid #000000;margin-top: 25px; !important;">
		<tr>
			<td style="width:20%;"></td>
			<td class="text-uppercase text-center" style="font-size:1rem;font-weight:bold;width:40%;">Cutting Inspection Report</td>
			<td class="text-uppercase text-right" style="font-size:0.8rem;font-weight:bold;width:20%;">QA-F-15 (01/01-01-2025)</td>
		</tr>
		</table>
		<table>
			<tr>
				<td style="width:50%;">
					<table class="table item-list-bb">
						<thead>
							<tr class="bg-light">
								<th style="width:10%;">Sr.No.</th>
								<th style="width:20%;">Date</th>
								<th style="width:30%;">Size Dia</th>
								<th style="width:20%;">Actual Cut. Wt.</th>
								<th style="width:20%;">Actual Cut. Length</th>
							</tr>
						</thead>
						<tbody>
							<?php
								for($i=1;$i<=20;$i++){
									echo'<tr>
										<td height="30">'.$i.'</td>
										<td></td>
										<td></td>
										<td></td>
										<td></td>
									</tr>';
								}
							?>
						</tbody>
					</table>
				</td>
				<td style="width:50%;">
					<table class="table item-list-bb">
						<thead>
							<tr class="bg-light">
								<th style="width:10%;">Sr.No.</th>
								<th style="width:20%;">Date</th>
								<th style="width:30%;">Size Dia</th>
								<th style="width:20%;">Actual Cut. Wt.</th>
								<th style="width:20%;">Actual Cut. Length</th>
							</tr>
						</thead>
						<tbody>
							<?php
								for($i=21;$i<=40;$i++){
									echo'<tr>
										<td height="30">'.$i.'</td>
										<td></td>
										<td></td>
										<td></td>
										<td></td>
									</tr>';
								}
							?>
						</tbody>
					</table>
				</td>
			</tr>
		</table>
	</div>
</div>