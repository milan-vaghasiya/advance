<div class="row">
	<div class="col-12">
		<table class="table item-list-bb" style="margin-top:2px;">
			<tr class="text-left">
				<th width="15%">Part Name</th>
				<td width="25%"><?=(!empty($inInspectData->item_code)) ? '['.$inInspectData->item_code.'] '.$inInspectData->item_name:$inInspectData->item_name?></td>
				<th width="15%">Date</th>
				<td width="15%"><?=(!empty($inInspectData->trans_date)) ? formatDate($inInspectData->trans_date):""?></td>
				<th width="10%">Report No</th>
				<td width="20%"><?=(!empty($inInspectData->trans_number)) ? $inInspectData->trans_number:""?></td>
			</tr>
			<tr class="text-left">
				<th>Cust Part No</th>
				<td><?=(!empty($inInspectData->fg_item_code)) ? '['.$inInspectData->fg_item_code.'] '.$inInspectData->fg_item_name:$inInspectData->fg_item_name?></td>
				<th>Receive Qty</th>
				<td><?=(!empty($inInspectData->qty)) ?$inInspectData->qty:""?></td>
				<th>Challan No.</th>
				<td><?=(!empty($inInspectData->doc_no)) ? $inInspectData->doc_no:""?></td>
			</tr>
			<tr class="text-left">
				<th>Material Grade</th>
				<td><?=(!empty($inInspectData->material_grade)) ? $inInspectData->material_grade : ""?></td>
				<th>Batch No</th>
				<td><?=(!empty($inInspectData->batch_no)) ? $inInspectData->batch_no : ""?></td>
				<th>Process</th>
				<td>GRN</td>
			</tr>
			<tr class="text-left">
				<th>Drg. No.</th>
				<td><?=(!empty($inInspectData->drw_no)) ? $inInspectData->drw_no:""?></td>
				<th>Supplier Name</th>
				<td colspan="3"><?=(!empty($inInspectData->party_name)) ? $inInspectData->party_name:""?></td>
			</tr>
		</table>

		<table class="table item-list-bb" style="margin-top:10px;">
		<?php $sample_size= (!empty($observation->sampling_qty))?floatval($observation->sampling_qty):5 ?>
		<tr style="text-align:center;">
			<th rowspan="2">#</th>
			<th rowspan="2">Parameter</th>
			<th rowspan="2">Specification</th>
			<th rowspan="2">Instrument</th>
			<th colspan="2">Tolerance</th>
			<th colspan="2">Specification Limit</th>
			<th colspan="<?= $sample_size?>">Observation on Samples</th>
			<th rowspan="2">Result</th>
		</tr>
		<tr style="text-align:center;">
			<th>Min</th>
			<th>Max</th>
			<th>LSL</th>
			<th>USL</th>
			<?php for($j=1;$j<=$sample_size;$j++):?> 
				<th><?= $j ?></th>
			<?php endfor;?>    
		</tr>
			<?php
				$tbodyData="";$i=1; 
				if(!empty($paramData)):
					
					foreach($paramData as $row):
						$obj = New StdClass;
						if(!empty($observation)):
							$obj = json_decode($observation->observation_sample);
						endif;
						$lsl = floatVal($row->specification) - $row->min;
						$usl = floatVal($row->specification) + $row->max;
						
						$flag=false;$paramItems = '';
							$paramItems.= '<tr>
										<td style="text-align:center;">'.$i.'</td>
										<td style="text-align:center;">'.$row->parameter.'</td>
										<td style="text-align:center;">'.$row->specification.'</td>   
										<td style="text-align:center;">'.$row->instrument.'</td>
										<td style="text-align:center;">'.$row->min.'</td>
										<td style="text-align:center;">'.$row->max.'</td>
										<td style="text-align:center;">'.$lsl.'</td>
										<td style="text-align:center;">'.$usl.'</td>';
							for($c=0;$c<$sample_size;$c++):
								if(!empty($obj->{$row->id})):
									$paramItems .= '<td style="text-align:center;">'.$obj->{$row->id}[$c].'</td>';
								endif;
								if(!empty($obj->{$row->id}[$c])){$flag=true;}
							endfor;
							if(!empty($obj->{$row->id})):
								$paramItems .= '<td style="text-align:center;">'.$obj->{$row->id}[$sample_size].'</td></tr>';
							endif;
							
							if($flag):
								$tbodyData .= $paramItems;$i++;
							endif;
					endforeach;
				else:
					$tbodyData.= '<tr><td colspan="14" style="text-align:center;">No Data Found</td></tr>';
				endif;
				echo $tbodyData;
			?>
		</table>
		
	</div>
</div>