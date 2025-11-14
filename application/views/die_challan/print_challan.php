<div class="row">
	<div class="col-12">
		<table class="table bg-light-grey"><tr><td class="fs-18 text-center" style="letter-spacing: 2px;font-weight:bold;padding:0px !important;">DIE CHALLAN</td></tr></table>
		
		<table class="table top-table-border" style="margin-top:5px;">
			<tr class="text-left">
			    <th>Challan No</th>
				<td><?=(!empty($challanData->trans_number) ? $challanData->trans_number : "")?></td>
				<th>Challan Date</th>
				<td><?=(!empty($challanData->trans_date) ? formatDate($challanData->trans_date) : "")?></td>
			</tr>
			<tr class="text-left">
				<th>Issue To</th>
				<td colspan="3"><?=(!empty($challanData->issue_to) ? $challanData->issue_to : "")?></td>
			</tr>
			<tr class="text-left">
				<th>Remark</th>
				<td colspan="3"><?=(!empty($challanData->remark) ? $challanData->remark : "")?></td>
			</tr>
		</table>
		
		<table class="table item-list-bb" style="margin-top:10px;">
			<tr>
				<th style="width:40px;">No.</th>
				<th class="text-center" >Item Code</th>
				<th class="text-center">Item Name</th>
				<th class="text-center">PRC Number</th>
				<th class="text-center">Set No.</th>
			</tr>
			<?php
				$i=1;
				if(!empty($challanData->itemList)):
					foreach($challanData->itemList as $row):
						echo '<tr>';
							echo '<td class="text-center">'.$i++.'</td>';
							echo '<td class="text-center">'.(!empty($row->item_code) ? $row->item_code : '').'</td>';
							echo '<td class="text-center">'.(!empty($row->item_name) ? $row->item_name : '').'</td>';
							echo '<td class="text-center">'.(!empty($row->prc_number) ? $row->prc_number : '').'</td>';
							echo '<td class="text-center">'.(!empty($row->die_set_no) ? $row->die_set_no : '').'</td>';
						echo '</tr>';						
					endforeach;
				else:
					echo '<tr><td class="text-center" colspan="5">No data available.</td></tr>';
				endif;
			?>
		</table>
	</div>
</div>  