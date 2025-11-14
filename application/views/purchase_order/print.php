<html>
    <head>
        <title>PURCHASE ORDER</title>
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
						<td style="width:33%;" class="fs-16 text-left">GSTIN: <?=$companyData->company_gst_no?></td>
						<td style="width:34%;" class="fs-18 text-center">PURCHASE ORDER</td>
						<td style="width:33%;" class="fs-16 text-right"></td>
					</tr>
				</table>               
                
                <table class="table item-list-bb fs-22" style="margin-top:5px;">
                    <tr >
                        <td rowspan="4" style="width:60%;vertical-align:top;">
                            <b>M/S. <?=$dataRow->party_name?></b><br>
							<?= $partyData->party_address ." ".$partyData->party_pincode ?><br>
							<b>City : </b><?= $partyData->city_name?> <b>State : </b><?=$partyData->state_name ?> <b>Country : </b><?=$partyData->country_name ?><br><br>
							
                            <b>Kind. Attn. : <?=$dataRow->contact_person?></b> <br>
                            Contact No. : <?=$dataRow->party_mobile?><br>
                            Email : <?=$partyData->party_email?><br><br>
                            GSTIN : <?=$dataRow->gstin?>
                        </td>
                        <td>
                            <b>PO. No.</b>
                        </td>
                        <td>
                            <?=$dataRow->trans_number?>
                        </td>
                    </tr>
                    <tr>
				        <th class="text-left">PO Date</th>
                        <td><?=formatDate($dataRow->trans_date)?></td>
                    </tr>
                    <tr>
                        <th class="text-left">Ref. No.</th>
                        <td>
							<?php
								$mainData = $dataRow->itemList[0];
								$enqNo = $mainData->enq_number;

								if(!empty($mainData->req_id)){
									$enqNo = implode(', ',array_column($dataRow->itemList,'enq_number'));
								}
							?>
							<?=$dataRow->doc_no.(!empty($enqNo) ? ' ('.$enqNo.')' : '')?>
						</td>
                    </tr>
                    <tr>
                        <th class="text-left">Ref. Date</th>
                        <td><?=(!empty($dataRow->doc_date)) ? formatDate($dataRow->doc_date) : ""?></td>
                    </tr>
					<tr>
                        <td colspan="3"><b>Delivery Address:</b> <?=(!empty($dataRow->delivery_address) ? $dataRow->delivery_address:$companyData->delivery_address).'-'.(!empty($dataRow->delivery_pincode) ? $dataRow->delivery_pincode:$companyData->delivery_pincode)?></td>
                    </tr>
					<tr>
						<td colspan="3">
							Dear sir, <br>
							We are pleased to place an order for the following materials on the below terms and conditions of supply.
						</td>
					</tr>
                </table>
                
                <table class="table item-list-bb" style="margin-top:10px;">
					<thead>
						<tr>
							<th style="min-width:15px;">No.</th>
							<th style="min-width:170px;">Item Description</th>
							<th style="min-width:60px;">HSN</th>
							<th style="min-width:60px;">Grade</th>
							<th style="min-width:80px;">Make</th>
							<th style="min-width:60px;">Delivery Date</th>
							<th style="min-width:50px;">Qty</th>
							<th style="min-width:60px;">Rate</th>
                            <th style="min-width:60px;">GST <small>(%)</small></th>
							<th style="min-width:80px;">Taxable Amount</th>
						</tr>
					</thead>
					<tbody>
						<?=$tbody?>
						<tr>
							<th colspan="6" class="text-right">Total Qty.</th>
							<th class="text-right"><?=sprintf('%.2f',$totalQty)?></th>
							<th class="text-right"></th>
							<th class="text-right">Sub Total</th>
							<th class="text-right"><?=sprintf('%.2f',$dataRow->taxable_amount)?></th>
						</tr>
						<?php
							$rwspan= 0; $srwspan = '';
							$beforExp = "";
							$afterExp = "";
							$invExpenseData = (!empty($dataRow->expenseData)) ? $dataRow->expenseData : array();
							foreach ($expenseList as $row) :
								$expAmt = 0;
								$amtFiledName = $row->map_code . "_amount";
								if (!empty($invExpenseData) && $row->map_code != "roff") :
									$expAmt = floatVal($invExpenseData->{$amtFiledName});
								endif;

								if(!empty($expAmt)):
									if ($row->position == 1) :
										if($rwspan == 0):
											$beforExp .= '<th class="text-right">'.$row->exp_name.'</th>
											<td class="text-right">'.sprintf('%.2f',$expAmt).'</td>';
										else:
											$beforExp .= '<tr>
												<th class="text-right">'.$row->exp_name.'</th>
												<td class="text-right">'.sprintf('%.2f',$expAmt).'</td>
											</tr>';
										endif;                                
									else:
										$afterExp .= '<tr>
											<th class="text-right">'.$row->exp_name.'</th>
											<td class="text-right">'.sprintf('%.2f',$expAmt).'</td>
										</tr>';
									endif;
									$rwspan++;
								endif;
							endforeach;

							$taxHtml = '';
							foreach ($taxList as $taxRow) :
								$taxAmt = 0;
								$taxAmt = floatVal($dataRow->{$taxRow->map_code.'_amount'});
								if(!empty($taxAmt)):
									if($rwspan == 0):
										$taxHtml .= '<th class="text-right">'.$taxRow->name.'</th>
										<td class="text-right">'.sprintf('%.2f',$taxAmt).'</td>';
									else:
										$taxHtml .= '<tr>
											<th class="text-right">'.$taxRow->name.'</th>
											<td class="text-right">'.sprintf('%.2f',$taxAmt).'</td>
										</tr>';
									endif;
								
									$rwspan++;
								endif;
							endforeach;

							foreach ($expenseList as $row) :
								$expAmt = 0;
								$amtFiledName = $row->map_code . "_amount";
								if (!empty($invExpenseData) && $row->map_code != "roff") :
									$expAmt = floatVal($invExpenseData->{$amtFiledName});
								endif;

								if(!empty($expAmt)):
									if ($row->position == 2) :
										if($rwspan == 0):
											$afterExp .= '<th class="text-right">'.$row->exp_name.'</th>
											<td class="text-right">'.sprintf('%.2f',$expAmt).'</td>';
										else:
											$afterExp .= '<tr>
												<th class="text-right">'.$row->exp_name.'</th>
												<td class="text-right">'.sprintf('%.2f',$expAmt).'</td>
											</tr>';
										endif;
										$rwspan++;
									endif;
								endif;
							endforeach;

							$fixRwSpan = (!empty($rwspan))?3:0;
						?>
						<tr>
							<th class="text-left" colspan="8" rowspan="<?=$rwspan?>">
								<b>Note: </b> <?=$dataRow->remark?>
							</th>

							<?php if(empty($rwspan)): ?>
                                <th  class="text-right">Round Off</th>
								<td class="text-right"><?=sprintf('%.2f',$dataRow->round_off_amount)?></td>
                            <?php endif; ?>
						</tr>
						<?=$beforExp.$taxHtml.$afterExp?>
						<tr>
							<th class="text-left" colspan="8" rowspan="<?=$fixRwSpan?>">
								Amount In Words : <br><?=numToWordEnglish(round($dataRow->net_amount,2))?>
							</th>

							<?php if(empty($rwspan)): ?>
                                <th  class="text-right">Grand Total</th>
                                <th class="text-right"><?=sprintf('%.2f',$dataRow->net_amount)?></th>
                            <?php else: ?>
                                <th  class="text-right">Round Off</th>
                                <td class="text-right"><?=sprintf('%.2f',$dataRow->round_off_amount)?></td>
                            <?php endif; ?>
						</tr>

						<?php if(!empty($rwspan)): ?>
						<tr>
							<th  class="text-right">Grand Total</th>
							<th class="text-right"><?=sprintf('%.2f',$dataRow->net_amount)?></th>
						</tr>	
						<?php endif; ?>		
					</tbody>
                </table>

                <div style="font-size:12px;padding-left:10px;padding-top:10px;">
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
							<td style="width:25%;" class="text-center"><?=$dataRow->prepareBy.' <br>('.formatDate($dataRow->created_at).')'?></td>
							<td style="width:25%;" class="text-center"><?=(!empty($dataRow->approveBy) ? $dataRow->approveBy : '').(!empty($dataRow->approve_date) ? ' <br>('.formatDate($dataRow->approve_date).')' : '')?>'</td>
						</tr>
						<tr>
							<td style="width:25%;" class="text-center"><b>Prepared By</b></td>
							<td style="width:25%;" class="text-center"><b>Authorised By</b></td>
						</tr>
					</table>
					<table class="table top-table" style="margin-top:10px;border-top:1px solid #545454;">
						<tr>
							<td style="width:25%;">PO No. & Date : <?=$dataRow->trans_number.' ['.formatDate($dataRow->trans_date).']'?></td>
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