<div class="row">
	<div class="col-12">
		<table class="table  item-list-bb" style="margin-top:2px;">
			<tr  class="text-left">
				<th class="bg-light" style="width:10%;font-size:0.8rem;">Report Date</th>
				<td style="font-size:0.8rem;width:30%;"><?=(!empty($lineInspectData->insp_date)) ? formatDate($lineInspectData->insp_date) : ""?></td>
				<th class="bg-light" style="width:10%;font-size:0.8rem;">PRC No</th>
				<td style="font-size:0.8rem;width:20%;"><?=(!empty($lineInspectData->prc_number)) ? $lineInspectData->prc_number : ""?></td>
				<th class="bg-light" style="width:10%;font-size:0.8rem;">PRC Date</th>
				<td style="font-size:0.8rem;width:20%;"><?=(!empty($lineInspectData->prc_date)) ? formatDate($lineInspectData->prc_date) : ""?></td>
			</tr>
			<tr class="text-left">
				<th class="bg-light" style="font-size:0.8rem;">Part</th>
				<td style="font-size:0.8rem;"><?=(!empty($lineInspectData->item_name)) ? $lineInspectData->item_name : ""?></td>
				<th class="bg-light" style="font-size:0.8rem;">Revision</th>
				<td style="font-size:0.8rem;"><?=(!empty($lineInspectData->rev_no)) ? $lineInspectData->rev_no : ""?></td>
				<th class="bg-light" style="font-size:0.8rem;">Setup</th>
				<td style="font-size:0.8rem;"><?=(!empty($lineInspectData->process_name)) ?$lineInspectData->process_name:""?></td>
			</tr>
			<tr class="text-left">
				<th class="bg-light" style="font-size:0.8rem;">Drg. No.</th>
				<td style="font-size:0.8rem;"><?=(!empty($lineInspectData->drw_no)) ? $lineInspectData->drw_no : ""?></td>
				<th class="bg-light" style="font-size:0.8rem;">Operator</th>
				<td style="font-size:0.8rem;"><?=(!empty($lineInspectData->emp_name)) ? $lineInspectData->emp_name : ""?></td>
				<th class="bg-light" style="font-size:0.8rem;">Machine</th>
				<td style="font-size:0.8rem;"><?=(!empty($lineInspectData->machine_name)) ? $lineInspectData->machine_name : ""?></td>
			</tr>
		</table>
		
		<table class="table item-list-bb">
			<thead>
				<tr style="text-align:center;" class="bg-light">
					<th rowspan="2" style="width:20px;">#</th>
					<th rowspan="2" style="width:70px;">Parameter</th>
					<th rowspan="2" style="width:250px;">Specification</th>
					<th colspan="2">Tolerance</th>
					<th colspan="2">Specification Limit</th>
					<th rowspan="2" style="width:90px;">Instrument</th>
                    <th colspan="3" style="width:150px;">First Piece Inspection</th>
                    <th colspan="3" style="width:150px;">Last Piece Inspection</th>
				</tr>
				<tr class="bg-light">
					<th style="width:50px;">Min</th>
					<th style="width:50px;">Max</th>
					<th>LSL</th>
					<th>USL</th>
					<th style="width:70px;">Reading</th>
					<th style="width:50px;">Result</th>
					<th style="width:80px;">Remark</th>
					<th style="width:70px;">Reading</th>
					<th style="width:50px;">Result</th>
					<th style="width:80px;">Remark</th>
				</tr>
			</thead>
			<tbody>
				<?php
                $i=1;
                if (!empty($paramData)):
                    foreach ($paramData as $row):   
						
						$obj1 = New StdClass; $obj2 = New StdClass;
						if ($lineInspectData->report_type == 4):
							$obj1 = json_decode($lineInspectData->observation_sample);
							$obj2 = json_decode($lineInspectData->last_piece_sample);
						elseif ($lineInspectData->report_type == 5):
							$obj1 = json_decode($lineInspectData->last_piece_sample);
							$obj2 = json_decode($lineInspectData->observation_sample);
						endif;
						$lsl = floatVal($row->specification) - $row->min;
						$usl = floatVal($row->specification) + $row->max;
                        echo '<tr>
                            <td class="text-center" height="30">'.$i.'</td>
                            <td>'.$row->parameter.'</td>
                            <td>'.$row->specification.'</td>
                            <td class="text-center">'.$row->min.'</td>
                            <td class="text-center">'.$row->max.'</td>
							<td style="text-align:center;">'.$lsl.'</td>
							<td style="text-align:center;">'.$usl.'</td>  
                            <td>'.$row->instrument.'</td>

							<td class="text-center">'.(!empty($obj1->{$row->id}[0]) ? $obj1->{$row->id}[0] : '').'</td>
							<td class="text-center">'.(!empty($obj1->{$row->id}[1]) ? $obj1->{$row->id}[1] : '').'</td>
							<td class="text-center">'.(!empty($obj1->{$row->id}[2]) ? $obj1->{$row->id}[2] : '').'</td>

							<td class="text-center">'.(!empty($obj2->{$row->id}[0]) ? $obj2->{$row->id}[0] : '').'</td>
							<td class="text-center">'.(!empty($obj2->{$row->id}[1]) ? $obj2->{$row->id}[1] : '').'</td>
							<td class="text-center">'.(!empty($obj2->{$row->id}[2]) ? $obj2->{$row->id}[2] : '').'</td>
                        </tr>';
                        $i++;
                    endforeach;
                else:
                    echo '<tr><td colspan="12" class="text-center">No Data Found</td></tr>';
                endif;
                ?>
			</tbody>
		</table>
	</div>
</div>