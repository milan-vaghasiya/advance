<div class="row">
	<div class="col-md-12">
		<table class="table item-list-bb" style="margin-top:2px;">
			<tr>
				<th class="text-left" style="width:20%;">Code</th>
				<td><?=$insData->item_code?></td>
				<th class="text-left" style="width:20%;">Make</th>
				<td><?=$insData->make_brand?></td>
			</tr>
			<tr>
				<th class="text-left">Discription</th>
				<td><?=$insData->item_name?></td>
				<th class="text-left"><b>Cali. Frequency</b></th>
                <td><?=$insData->cal_freq?>(Month)</td>
			</tr>
			
			<?php 
			    if($insData->status == 4):
                    echo '<tr>';
                    echo '<th class="text-left"> Reject Date</th>';
                    echo '<td class="text-left">'.$insData->rejected_at.'</td>';
                    echo '<th class="text-left"> Reject Reason </th>';
                    echo '<td class="text-left">'.$insData->reject_reason.'</td>';
                    echo '</tr>';
                endif; 
            ?>
		</table>

		<table class="table item-list-bb" style="margin-top:10px;">
			<tr>
				<th style="width:20px;">#</th>
				<th style="width:40px;">Ch. No.</th>
				<th style="width:40px;">Ch. Date</th>
				<th style="width:40px;">Ch. Type</th>
				<th style="width:40px;">Issue From</th>
				<th style="width:40px;">Return Date</th>
				<th style="width:100px;">Return Remark</th>
			</tr>
			<?php
                if(!empty($dataRow)):
					$i=1;
					foreach($dataRow as $row):
						$row->party_name = (!empty($row->party_name))?$row->party_name:'IN-HOUSE';
						$row->challan_type = (($row->challan_type==1)? 'IN-House Issue': (($row->challan_type==2) ? 'Vendor Issue':'Calibration'));
						
						echo '<tr class="text-center" height="32">';
							echo '<td>'.$i++.'</td>';
							echo '<td>'.$row->trans_number.'</td>';
							echo '<td>'.formatDate($row->trans_date).'</td>';
							echo '<td>'.$row->challan_type.'</td>';
							echo '<td>'.$row->party_name.'</td>';
							echo '<td>'.formatDate($row->receive_at).'</td>';
							echo '<td>'.$row->return_remark.'</td>';
						echo '</tr>';
					endforeach;
				endif;
			?>
		</table>
	</div>
</div>