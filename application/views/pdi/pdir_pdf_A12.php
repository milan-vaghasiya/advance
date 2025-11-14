<div class="row">
	<div class="col-12">
		<table class="table item-list-bb">
		<?php $sample_size= (!empty($firData->sampling_qty))?floatval($firData->sampling_qty):5 ?>
			<thead>
				<tr style="text-align:center;" class="bg-light">
					<th colspan="<?= (($sample_size*2)+10)?>">Inspected Characteristics</th>
				</tr>
				<tr style="text-align:center;" class="bg-light">
					<th rowspan="2" width="3%;">#</th>
					<th rowspan="2" width="10%;">Specification</th>
					<th rowspan="2" width="7%">Nominal Value</th>
					<th colspan="2" width="10%">Tolerance</th>
					<th colspan="2" width="10%">Specification Limit</th>
					<th rowspan="2" width="10%">Inspection Method</th>
					<th colspan="<?= $sample_size?>" width="20%">Supplier's Inspection</th>
					<th rowspan="2" width="5%">OK/<br/>Not Ok</th>
					<th colspan="<?= $sample_size?>" width="20%">Customer's Inspection</th>
					<th rowspan="2" width="5%">OK/<br/>Not Ok</th>
				</tr>
				<tr style="text-align:center;" class="bg-light">
					<th>Min</th>
					<th>Max</th>
					<th>LSL</th>
					<th>USL</th>
					<?php for($j=1;$j<=$sample_size;$j++):?> 
						<th><?= $j ?></th>
					<?php endfor;?>
					
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
										<td style="text-align:center;">'.$row->min.'</td>
										<td style="text-align:center;">'.$row->max.'</td>
										<td style="text-align:center;">'.$lsl.'</td>
										<td style="text-align:center;">'.$usl.'</td>
										<td style="text-align:center;">'.$row->instrument.'</td>';
							for($c=0;$c<$sample_size;$c++):
								if(!empty($obj->{$row->id})):
									$paramItems .= '<td style="text-align:center;">'.$obj->{$row->id}[$c].'</td>';
								endif;
								if(!empty($obj->{$row->id}[$c])){$flag=true;}
							endfor;
							
							if(!empty($obj->{$row->id})):
								$paramItems .= '<td class="text-center" style="width:60px;">'.$obj->{$row->id}[$c].'</td>';
							endif;
							
							for($c=0;$c<$sample_size;$c++):
								if(!empty($obj->{$row->id})):
									$paramItems .= '<td style="text-align:center;" style="width:45px;"></td>';
								endif;
								if(!empty($obj->{$row->id}[$c])){$flag=true;}
							endfor;
							$paramItems .= '<td style="text-align:center;"></td></tr>';
							
							$tbodyData .= $paramItems;$i++;
							
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