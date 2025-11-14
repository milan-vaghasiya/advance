<div class="row">
	<div class="col-12">
		<table class="table bg-light-grey"><tr><td class="fs-18 text-center" style="letter-spacing: 2px;font-weight:bold;padding:0px !important;">QC CHALLAN</td></tr></table>
		
		<table class="table item-list-bb fs-22" style="margin-top:5px;">
			<tr>
				<td rowspan="4" style="width:55%;vertical-align:top;">
					<b>M/S. <?=((!empty($challanData->party_name)) ? $challanData->party_name : 'IN-HOUSE')?></b><br>
					<?=((!empty($partyData->party_address) && !empty($partyData->party_pincode)) ? $partyData->party_address ." - ".$partyData->party_pincode : '')?><br>
					<b>Kind. Attn. : <?=(!empty($partyData->contact_person) ? $partyData->contact_person : '')?></b> <br>
					Contact No. : <?=(!empty($partyData->party_mobile) ? $partyData->party_mobile : '')?><br>
					Email : <?=(!empty($partyData->party_email) ? $partyData->party_email : '')?><br><br>
					GSTIN : <?=(!empty($partyData->gstin) ? $partyData->gstin : '')?>
				</td>
				<td>
					<b>Challan No.</b>
				</td>
				<td>
					<?=(!empty($challanData->trans_no)) ? $challanData->trans_prefix.$challanData->trans_no : ""?>
				</td>
			</tr>
			<tr>
				<th class="text-left">Challan Date</th>
				<td><?=(!empty($challanData->trans_date)) ? formatDate($challanData->trans_date) : ""?></td>
			</tr>
			<tr>
				<th class="text-left">Issue To</th>
				<td><?=((!empty($challanData->party_name)) ? $challanData->party_name : 'IN-HOUSE')?></td>
			</tr>
			<tr>
				<th class="text-left">Handover To</th>
				<td><?=(!empty($challanData->emp_name)) ? $challanData->emp_name : ""?></td>
			</tr>
			<tr>
				<td colspan="3"><b>Remark :</b> <?=(!empty($challanData->remark) ? $challanData->remark : "")?></td>
			</tr>
		</table>
		
		<table class="table item-list-bb" style="margin-top:10px;">
			<tr>
				<th style="width:40px;">No.</th>
				<th class="text-center" >Item Code</th>
				<th class="text-center">Item Name</th>
			</tr>
			<?php
				$i=1;
				if(!empty($challanData->itemData)):
					foreach($challanData->itemData as $row):
						echo '<tr>';
							echo '<td class="text-center">'.$i++.'</td>';
							echo '<td class="text-center">'.$row->item_code.'</td>';
							echo '<td class="text-center">'.$row->item_name.'</td>';
						echo '</tr>';
						
					endforeach;
				endif;
			?>
		</table>
	</div>
</div>  