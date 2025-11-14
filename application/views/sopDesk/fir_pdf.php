<div class="row">
	<div class="col-12">
		<table class="table item-list-bb" style="margin-top:2px;">
			<tr class="text-left">
				<th class="bg-light text-center" width="10%">Date</th>
				<td width="30%"><?=(!empty($firData->insp_date)) ? formatDate($firData->insp_date) : ""?></td>
				<th class="bg-light text-center" width="15%">FIR No</th>
				<td width="15%"><?=(!empty($firData->trans_number)) ? $firData->trans_number : ""?></td>
				<th class="bg-light text-center" width="10%">PRC No</th>
				<td width="20%"><?=(!empty($firData->prc_number)) ? $firData->prc_number : ""?></td>
			</tr>
			<tr class="text-left">
				<th class="bg-light text-center">Ok Qty</th>
				<?php
					if($type == 1){
						echo '<td colspan="3">'.((!empty($firData->pdi_qty)) ? floatval($firData->pdi_qty) : 0).'</td>';
					}else{
						echo '<td>'.((!empty($firData->ok_qty)) ? floatval($firData->ok_qty) : "").'</td>
						<th class="bg-light text-center">Rej Qty</th>
						<td>'.((!empty($firData->rej_qty)) ? floatval($firData->rej_qty) : "").'</td>';
					}
				?>
				<th class="bg-light text-center">Rev No</th>
				<td><?=(!empty($firData->rev_no)) ? ($firData->rev_no) : ""?></td>
			</tr>
			<tr class="text-left">
				<th class="bg-light text-center">Part Name</th>
				<td><?=(!empty($firData->item_name)) ? $firData->item_name : ""?></td>
				<th class="bg-light text-center">Cus. Part No.</th>
				<td><?=(!empty($firData->item_code)) ? $firData->item_code : ""?></td>
				<th class="bg-light text-center">Drg. No.</th>
				<td><?= (!empty($firData->drw_no) ? $firData->drw_no : "")?></td>
			</tr>
			<tr class="text-left">
				<th class="bg-light text-center">Supplier</th>
				<td><?=(!empty($companyData->company_name)) ? $companyData->company_name : ""?></td>
				<th class="bg-light text-center">Material Grade</th>
				<td colspan="3"><?=(!empty($firData->material_grade)) ? $firData->material_grade : ""?></td>
			</tr>
		</table>

		<table class="table item-list-bb">
		<?php $sample_size= (!empty($firData->sampling_qty))?floatval($firData->sampling_qty):5 ?>
			<thead>
				<tr style="text-align:center;" class="bg-light">
					<th rowspan="2">#</th>
					<th rowspan="2">Parameter</th>
					<th rowspan="2">Specification</th>
					<th rowspan="2">Instrument</th>
					<th colspan="2">Tolerance</th>
					<th colspan="2">Specification Limit</th>
					<th colspan="<?= $sample_size?>">Observation on Samples</th>
					<th rowspan="2">Result</th>
				</tr>
				<tr style="text-align:center;" class="bg-light">
					<th>Min</th>
					<th>Max</th>
					<th>LSL</th>
					<th style=>USL</th>
					<?php for($j=1;$j<=$sample_size;$j++):?> 
						<th><?= $j ?></th>
					<?php endfor;?>    
				</tr>
			</thead>
			<tbody>
			<?php
				$tbodyData="";$i=1; 
				if(!empty($paramData)):
					foreach($paramData as $row):
						$obj = New StdClass;
						if(!empty($firData)):
							$obj = json_decode($firData->observation_sample); 
						endif;
						$lsl = floatVal($row->specification) - $row->min;
						$usl = floatVal($row->specification) + $row->max;
						$flag=false;$paramItems = '';
							$paramItems.= '<tr>
										<td style="text-align:center;">'.$i.'</td>
										<td style="text-align:left;">'.$row->parameter.'</td>
										<td style="text-align:left;">'.$row->specification.'</td>   
										<td style="text-align:left;">'.$row->instrument.'</td>
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
								$paramItems .= '<td style="text-align:center;">'.$obj->{$row->id}[$c].'</td></tr>';
							endif;
							// if($flag):
								$tbodyData .= $paramItems;$i++;
							// endif;
					endforeach;
				else:
					$tbodyData.= '<tr><td colspan="11" style="text-align:center;">No Data Found</td></tr>';
				endif;
				echo $tbodyData;
			?>
			</tbody>
			
		</table>
	</div>
</div>