<div class="row">
    <div class="col-12">
        <table>
			<tr>
				<td>
					<img src="<?=$letter_head?>" class="img">
				</td>
			</tr>
		</table>
		
		<table class="table bg-light-grey">
			<tr class="" style="letter-spacing: 2px;font-weight:bold;padding:2px !important; border-bottom:1px solid #000000;">
				<td style="width:33%;" class="fs-16 text-left"></td>
				<td style="width:34%;" class="fs-18 text-center">JOB WORK ORDER</td>
				<td style="width:33%;" class="fs-16 text-right"></td>
			</tr>
		</table>

        <table class="table item-list-bb fs-22" style="margin-top:5px;">
            <tr>
                <td width="70%" rowspan="2"><b>TO : <?= $dataRow->party_name;?></b><br/><?= $dataRow->party_address;?><br/><br/><b>GSTIN No. : </b> <?= $dataRow->gstin;?></td>
				<td width="30%"><b>Order No. : </b><?= $dataRow->trans_number;?></td>
            </tr>
            <tr>
                <td><b>Order Date : </b><?= formatDate($dataRow->order_date);?></td>
            </tr>
        </table>
        
        <table class="table item-list-bb" style="margin-top:10px;">
			<thead>
				<tr style="background-color:#D2D8E0;">
					<th style="width:40px;">No.</th>
					<th class="text-left">Material Description</th>
					<th class="text-left">Process</th>
					<th style="width:80px;">Pcs.</th>
					<th style="width:80px;">Rate</th>
				</tr>
			</thead>
            <tbody>
				<?php
					if(!empty($dataRow->itemData)){
						$i = 1;$totalRate = 0;
						foreach($dataRow->itemData as $row){
							$remark = (!empty($row->remark) ? "<b>Remark : </b>".$row->remark : "");
							echo '<tr>
								<td>'.$i++.'</td>
								<td>'.$row->item_name.$remark.'</td>
								<td>'.$row->process_name.'</td>
								<td class="text-center">'.$row->rate_per_unit.'</td>
								<td class="text-center">'.$row->rate.'</td>
							</tr>';
							$totalRate += $row->rate;
						}
					}
				?>
				<tr style="background-color:#D2D8E0;">
					<th class="text-right" colspan="4">Total</th>
					<th class="text-center"><?= $totalRate;?></th>
				</tr>
            </tbody>
        </table>
		
		<table class="table item-list-bb" border="0" style="margin-top:10px;">
			<tr>
				<th class="text-left">Terms & Conditions :-</th>
			</tr>
			<tr>
				<td><?= $dataRow->terms_conditions;?></td>
			</tr>
		</table>
    </div>
</div>        