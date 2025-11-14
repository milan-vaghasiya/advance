<div class="row">
	<div class="col-12">
		<table class="table item-list-bb">
		<?php $sample_size= (!empty($firData->sampling_qty))?floatval($firData->sampling_qty):5 ?>
			<thead>
				<tr style="text-align:center;" class="bg-light">
					<th rowspan="5" width="4%">#</th>
					<th rowspan="5" width="10%">Required Characteristic</th>
					<th rowspan="5" width="10%">Specifications</th>
					<th rowspan="5" width="9%">Instrument</th>
					<th rowspan="5" width="5%">Gauge Serial No</th>
					<th rowspan="5" width="8%">Inspection<br/>Frequency</th>
					<th colspan="2" width="9%">Tolerance</th>
					<th colspan="2" width="10%">Specification Limit</th>
					<th colspan="<?= $sample_size+1?>" width="30%">Observation</th>
					<th width="5%">Remarks</th>
				</tr>
				<tr style="text-align:center;">
					<th class="bg-light" rowspan="4">Max</th>
					<th class="bg-light" rowspan="4">Min</th>
					<th class="bg-light" rowspan="4">USL</th>
					<th class="bg-light" rowspan="4">LSL</th>
					<th class="bg-light">Heat No.</th>
					<td colspan="<?= $sample_size?>"><?= $firData->rm_batch?></td>
					<td></td>
				</tr>
				<tr>
					<th class="bg-light">H. Lot No.</th>
					<td colspan="<?= $sample_size?>"></td>
					<td></td>
				</tr>
				<tr>
					<th class="bg-light">TN No.</th>
					<td colspan="<?= $sample_size?>"></td>
					<td></td>
				</tr>
				<tr>
					<th class="bg-light">SR.No.</th>
					<?php for($j=1;$j<=$sample_size;$j++):?> 
						<th class="bg-light"><?= $j ?></th>
					<?php endfor;?>
					<td></td>
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
										<td style="text-align:left;"></td>
										<td style="text-align:left;">'.$row->size.'/'.$row->frequency.' '.$row->freq_unit.'</td>
										<td style="text-align:center;">'.$row->max.'</td>
										<td style="text-align:center;">'.$row->min.'</td>
										<td style="text-align:center;">'.$usl.'</td>
										<td style="text-align:center;">'.$lsl.'</td>
										<td style="text-align:center;"></td>';
							
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