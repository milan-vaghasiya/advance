<div class="row">
	<div class="col-12">
		<table class="table item-list-bb">
		<?php $sample_size= (!empty($firData->sampling_qty))?floatval($firData->sampling_qty):5 ?>
			<thead>
				<tr style="text-align:center;" class="bg-light">
					<th rowspan="2" width="4%">#</th>
					<th rowspan="2" width="13%">Parameter</th>
					<th rowspan="2" width="15%">Specification</th>
					<th colspan="2" width="13%">Tolerance</th>
					<th rowspan="2" width="6%">Spl.<br/>Char.</th>
					<th rowspan="2" width="17%">Inspection<br/>Method</th>
					<th colspan="<?= $sample_size?>" width="27%">Measurement Results</th>
					<th rowspan="2" width="5%">OK/<br/>Not Ok</th>
				</tr>
				<tr style="text-align:center;" class="bg-light">
					<th>LTL</th>
					<th>UTL</th>
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
										<td style="text-align:center;">'.((!empty($row->char_class))?'<img src="'.base_url("/assets/images/symbols/".$row->char_class.'.png').'" style="width:20px;">':'').'</td>
										<td style="text-align:center;">'.$row->instrument.'</td>';
							for($c=0;$c<$sample_size;$c++):
								if(!empty($obj->{$row->id})):
									$paramItems .= '<td style="text-align:center;">'.$obj->{$row->id}[$c].'</td>';
								else:
									$paramItems .= '<td></td>';
								endif;
								if(!empty($obj->{$row->id}[$c])){$flag=true;}
							endfor;
							
							if(!empty($obj->{$row->id})):
								$paramItems .= '<td style="text-align:center;">'.$obj->{$row->id}[$c].'</td></tr>';
							else:
								$paramItems .= '<td></td>';
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