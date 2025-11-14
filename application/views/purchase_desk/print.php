<html>
    <head>
        <title>Purchase RFQ</title>
        <!-- Favicon icon -->
        <link rel="icon" type="image/png" sizes="16x16" href="<?=base_url();?>assets/images/favicon.png">
    </head>
    <body>
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
						<td style="width:33%;" class="fs-18 text-left">
							GSTIN: <?=$companyData->company_gst_no?>
						</td>
						<td style="width:33%;" class="fs-18 text-center">Purchase RFQ</td>
						<td style="width:33%;" class="fs-18 text-right"></td>
					</tr>
				</table>               
                
                <?php $masterData = $dataRow[0]; ?>
                <table class="table item-list-bb fs-22" style="margin-top:5px;">
                    <tr>
                        <td rowspan="2" style="width:55%;vertical-align:top;">
                            <b>M/S. <?=$partyData->party_name?></b><br>
							<?= $partyData->party_address ." - ".$partyData->party_pincode ?><br>
                            <b>Kind. Attn. : <?=$partyData->contact_person?></b> <br>
                            Contact No. : <?=$partyData->party_mobile?><br>
                            Email : <?=$partyData->party_email?><br><br>
                            GSTIN : <?=$partyData->gstin?>
                        </td>
                        <td>
                            <b>Enquiry No.</b>
                        </td>
                        <td>
                            <?=$masterData->trans_number?>
                        </td>
                    </tr>
                    <tr>
				        <th class="text-left">Enquiry Date</th>
                        <td><?=formatDate($masterData->trans_date)?></td>
                    </tr>
                </table>
                
                <table class="table item-list-bb" style="margin-top:10px;">
					<thead>
						<tr>
							<th style="width:30px;">No.</th>
							<th style="min-width:130px;" class="text-left">Item Description</th>
							<th style="width:60px;">HSN</th>
							<th style="width:60px;">Grade</th>
							<th style="width:80px;">Qty</th>
						</tr>
					</thead>
					<tbody>
						<?php
							$i = 1; $totalQty = 0;
							if(!empty($dataRow)):
								foreach($dataRow as $row):
									$rowspan = (!empty($row->item_remark) ? '2': '1');
									$fg_item_name = (!empty($row->fg_item_code)) ? '<b>Notes : </b>['.$row->fg_item_code.'] '.$row->fg_item_name : $row->fg_item_name;

									echo '<tr>';
										echo '<td class="text-center" rowspan='.$rowspan.'>'.$i++.'</td>';
										echo '<td>'.$row->item_name.'<br>'.$fg_item_name.'</td>';
										echo '<td class="text-center">'.$row->hsn_code.'</td>';
										echo '<td class="text-center">'.(!empty($row->material_grade) ? $row->material_grade : '').'</td>';
										echo '<td class="text-right">'.sprintf('%.2f',$row->qty).(!empty($row->uom) ? ' <small>('.$row->uom.')</small>' : '').'</td>';
									echo '</tr>';
									echo (!empty($row->item_remark)) ? '<tr><td colspan="4"><b>Notes : </b>'.$row->item_remark.'</td></tr>' : '';
									$totalQty += $row->qty;
								endforeach;
							endif;
						?>
						<tr>
							<th colspan="4" class="text-right">Total Qty.</th>
							<th class="text-right"><?=sprintf('%.2f',$totalQty)?></th>
						</tr>
						<tr>
							<th class="text-left" colspan="5">
								<b>Note: </b> <?=$masterData->remark?>
							</th>
						</tr>	
					</tbody>
                </table>

                <div style="font-size:12px;padding-left:10px;">
                    <strong class="text-left">Terms & Conditions :-</strong><br>
                    <?php
                        if(!empty($termsData->condition)):
							echo $termsData->condition;
                        endif;
                    ?>
                </div>
                
				<htmlpagefooter name="lastpage">
					<table class="table top-table" style="margin-top:10px;border-top:1px solid #545454;">
						<tr>
							<td style="width:50%;" rowspan="4"></td>
							<th colspan="2">For, <?=$companyData->company_name?></th>
						</tr>
						<tr>
							<td style="width:25%;" class="text-center"><?=$prepareBy?></td>
							<td style="width:25%;" class="text-center"><?=$approveBy?></td>
						</tr>
						<tr>
							<td style="width:25%;" class="text-center"><b>Prepared By</b></td>
							<td style="width:25%;" class="text-center"><b>Authorised By</b></td>
						</tr>
					</table>
					<table class="table top-table" style="margin-top:10px;border-top:1px solid #545454;">
						<tr>
							<td style="width:25%;">Enquiry No. & Date : <?=$masterData->trans_number.' ['.formatDate($masterData->trans_date).']'?></td>
							<td style="width:25%;"></td>
							<td style="width:25%;text-align:right;">Page No. {PAGENO}/{nbpg}</td>
						</tr>
					</table>
                </htmlpagefooter>
				<sethtmlpagefooter name="lastpage" value="on" />
            </div>
        </div>        
    </body>
</html>