<html>
    <head>
        <title>SALES ORDER</title>
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
                        <td style="width:34%;" class="fs-18 text-center">SALES ORDER</td>
                        <td style="width:33%;" class="fs-16 text-right"></td>
                    </tr>
                </table>
                
                <table class="table item-list-bb fs-22" style="margin-top:5px;">
                    <tr >
                        <td rowspan="4" style="width:60%;vertical-align:top;">
                            <b>M/S. <?=$dataRow->party_name?></b><br>
                            <?=(!empty($partyData->party_address) ? $partyData->party_address ." ".$partyData->party_pincode : '')?><br>
                            <b>City : </b><?= $partyData->city_name?> <b>State : </b><?=$partyData->state_name ?> <b>Country : </b><?=$partyData->country_name ?><br><br>
							
                            <b>Kind. Attn. : <?=$partyData->contact_person?></b> <br>
                            Contact No. : <?=$partyData->party_mobile?><br>
                            Email : <?=$partyData->party_email?><br><br>
                            GSTIN : <?=$partyData->gstin?>
                        </td>
                        <td>
                            <b>SO. No. : </b><?=$dataRow->trans_number?>
                        </td>
                        <td>
                            <b>Rev No. : </b><?=sprintf("%02d",$dataRow->so_rev_no)?>  / <?=formatDate($dataRow->doc_date)?>
                        </td>
                    </tr>
                    <tr>
                        <td style="width:40%;" colspan="2">
                            <b>SO Date : </b><?=formatDate($dataRow->trans_date)?><br>
                        </td>
                    </tr>
                    <tr>
                        <td colspan="2">
                            <b>Cust PO No : </b><?=$dataRow->doc_no?><br>
                        </td>
                    </tr>
                    <tr>
                        <td colspan="2">
                            <b>Cust PO Date : </b><?=(!empty($dataRow->doc_date)) ? formatDate($dataRow->doc_date) : ""?><br>
                        </td>
                    </tr>
                </table>
                
                <table class="table item-list-bb" style="margin-top:10px;">
                    <tr>
                        <th style="width:30px;">No</th>
                        <th style="width:50px;">Item Code</th>
                        <th class="text-left" style="min-width:100px;">Item Description</th>
                        <th style="width:50px;">Material</th>
                        <th style="width:50px;" >HSN/SAC</th>
                        <th style="width:50px;" >Net Weight</th>
                        <th style="width:50px;">Qty<small>(NOS)</small></th>
                        <th style="width:50px;">Total Weight</th>
                        <th style="width:80px;">Rate<small>(<?=$partyData->currency?>)</small></th>
                        <th style="width:50px;">Taxable Amount<small>(<?=$partyData->currency?>)</small></th>
                    </tr>
                    <tbody>
                        <?php
                            $i=1;$totalQty = 0;
                            if(!empty($dataRow->itemList)):
                                foreach($dataRow->itemList as $row):
                                    $indent = (!empty($row->ref_id)) ? '<br>Reference No:'.$row->ref_number : '';
                                    $delivery_date = (!empty($row->cod_date)) ? '<br><small><b>Dispatch Date:</b>'.formatDate($row->cod_date).'</small>' : '';
									$mfg_type = (!empty($row->mfgType)) ? '<br><small><b>Mfg. Type: </b>'.$row->mfg_type.'</small>' : '';
									$total_weight = $row->wt_pcs * $row->qty;
                                    $rev_no = ($row->cust_rev_no!="")?'<br><b>Cust. Rev No:</b>.'.$row->cust_rev_no.', <b>Drw No:</b>'.$row->drw_no:'';
                                    
									$rowspan = (!empty($row->item_remark) ? '2': '1');
									
									echo '<tr>';
                                        echo '<td class="text-center" rowspan="'.$rowspan.'">'.$i++.'</td>';
                                        echo '<td class="text-center">'.$row->item_code.'</td>';
                                        echo '<td>'.$row->item_name.$indent.$rev_no.$mfg_type.$delivery_date.'</td>';
                                        echo '<td class="text-center">'.$row->material_grade.'</td>';
                                        echo '<td class="text-center">'.$row->hsn_code.'</td>';
                                        echo '<td class="text-center">'.$row->wt_pcs.'</td>';
                                        echo '<td class="text-right">'.sprintf('%0.2f',$row->qty).'</td>';
                                        echo '<td class="text-center">'.sprintf('%.2f',$total_weight).'</td>';
                                        echo '<td class="text-center">'.moneyFormatIndia($row->price).'</td>';
                                        echo '<td class="text-right" rowspan="'.$rowspan.'">'.moneyFormatIndia($row->taxable_amount).'</td>';
                                    echo '</tr>';
                                    echo (!empty($row->item_remark)) ? '<tr><td colspan="8"><b>Notes : </b>'.$row->item_remark.'</td></tr>' : '';
                                    $totalQty += $row->qty;
                                endforeach;
                            endif;

                            $blankLines = (5 - $i);
                            if($blankLines > 0):
                                for($j=1;$j<=$blankLines;$j++):
                                    echo '<tr>
                                        <td style="border-top:none;border-bottom:none;">&nbsp;</td>
                                        <td style="border-top:none;border-bottom:none;"></td>
                                        <td style="border-top:none;border-bottom:none;"></td>
                                        <td style="border-top:none;border-bottom:none;"></td>
                                        <td style="border-top:none;border-bottom:none;"></td>
                                        <td style="border-top:none;border-bottom:none;"></td>
                                        <td style="border-top:none;border-bottom:none;"></td>
                                        <td style="border-top:none;border-bottom:none;"></td>
                                        <td style="border-top:none;border-bottom:none;"></td>
                                        <td style="border-top:none;border-bottom:none;"></td>
                                    </tr>';
                                endfor;
                            endif;
                        ?>
                        <tr>
                            <th colspan="6" class="text-right">Total Qty.</th>
                            <th class="text-right"><?=sprintf('%.3f',$totalQty)?></th>
                            <th></th>
                            <th class="text-right">Sub Total</th>
                            <th class="text-right"><?=moneyFormatIndia(sprintf('%.2f',$dataRow->taxable_amount))?></th>
                            
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

							$fixRwSpan = (!empty($rwspan))?3:0;
						?>
						<tr>
							<th class="text-left" colspan="8" rowspan="<?=$rwspan?>">
								<b>Note: </b> <?= $dataRow->remark?>
							</th>

							<?php if(empty($rwspan)): ?>
                                <th class="text-right">Round Off</th>
								<td class="text-right"><?=sprintf('%.2f',$dataRow->round_off_amount)?></td>
                            <?php endif; ?>
                            
						</tr>
						<?=$beforExp.$taxHtml.$afterExp?>
						<tr>
							<th class="text-left" colspan="8" rowspan="3">
								Amount In Words (<?=$partyData->currency?>): <br><?=numToWordEnglish(sprintf('%.2f',$dataRow->net_amount))?>
							</th>

							<?php if(empty($rwspan)): ?>
                                <th class="text-right">Grand Total (<?=$partyData->currency?>)</th>
                                <th class="text-right"><?=moneyFormatIndia(sprintf('%.2f',$dataRow->net_amount))?></th>
                            <?php endif; ?>
						</tr>

						<?php if(!empty($rwspan)): ?>
						<tr>
							<th class="text-right">Round Off</th>
							<td class="text-right"><?=sprintf('%.2f',$dataRow->round_off_amount)?></td>
						</tr>
						<tr>
							<th class="text-right">Grand Total (<?=$partyData->currency?>)</th>
							<th class="text-right"><?=moneyFormatIndia(sprintf('%.2f',$dataRow->net_amount))?></th>
						</tr>	
						<?php endif; ?>		
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
							<td style="width:25%;" class="text-center"><?=$dataRow->created_name?></td>
							<td style="width:25%;" class="text-center"><?=$dataRow->approve_by?><br/><?= (!empty($dataRow->approve_date) ? formatDate($dataRow->approve_date) : "");?></td>
						</tr>
						<tr>
							<td style="width:25%;" class="text-center"><b>Prepared By</b></td>
							<td style="width:25%;" class="text-center"><b>Authorised By</b></td>
						</tr>
					</table>
                    <table class="table top-table" style="margin-top:10px;border-top:1px solid #545454;">
						<tr>
							<td style="width:25%;">SO No. & Date : <?=$dataRow->trans_number.' ['.formatDate($dataRow->trans_date).']'?></td>
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