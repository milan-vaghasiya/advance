<html>
    <head>
        <title>Proforma Invoice</title>
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
                        <td style="width:33%;" class="fs-18 text-center">Proforma Invoice</td>
                        <td style="width:33%;" class="fs-18 text-right"></td>
                    </tr>
                </table>
                
                <table class="table item-list-bb fs-22" style="margin-top:5px;">
                    <tr >
                        <td rowspan="4" style="width:36%;vertical-align:top;">
                            <b>M/S. <?=$dataRow->party_name?></b><br>
                            <?=(!empty($partyData->party_address) ? $partyData->party_address ." - ".$partyData->party_pincode : '')?><br>
                            <b>Kind. Attn. : <?=$partyData->contact_person?></b> <br>
                            Contact No. : <?=$partyData->party_mobile?><br>
                            Email : <?=$partyData->party_email?><br><br>
                            GSTIN : <?=$partyData->gstin?>
                        </td>
                        <td rowspan="4" class="text-center" style="width:28%;">
                            <b> Terms of Delivery & Payment</b><br>
                            <?=(!empty($dataRow->terms) ?$dataRow->terms : "-")?>
                        </td>
                        <td style="width:18%">
                            <b>PINV. No.</b>
                        </td>
                        <td style="width:18%">
                            <?=$dataRow->trans_number?>
                        </td>
                    </tr>
                    <tr>
				        <th class="text-left">PINV. Date</th>
                        <td><?=formatDate($dataRow->trans_date)?></td>
                    </tr>
                    <tr>
                        <th class="text-left">Cust. PO. No.</th>
                        <td><?=$dataRow->cust_po_no?></td>
                    </tr>
                    <tr>
                        <th class="text-left">Cust. PO. Date</th>
                        <td><?=(!empty($dataRow->cust_po_date)) ? formatDate($dataRow->cust_po_date) : ""?></td>
                    </tr>
                </table>
                
                <table class="table item-list-bb" style="margin-top:10px;">
                    <tr>
                        <th style="width:4%;">No</th>
                        <th>Item Code</th>
                        <th class="text-left"  style="width:30%;">Item Description</th>
                        <th>Unit</th>
                        <th>Qty</th>
                        <th>Rate</th>
                        <th>Disc.</th>
                        <th>CGST</th>
                        <th>SGST</th>
                        <th>Amount</th>
                    </tr>
                    <tbody>
                        <?php
                            $i=1;$totalQty = 0;
                            if(!empty($dataRow->itemList)):
                                foreach($dataRow->itemList as $row):
                                    $item_remark=(!empty($row->item_remark))?'<br><small>Remark:.'.$row->item_remark.'</small>':'';
                                    echo '<tr>';
                                        echo '<td class="text-center">'.$i++.'</td>';
                                        echo '<td class="text-center">'.$row->item_code.'</td>';
                                        echo '<td>'.$row->item_name.$item_remark.'</td>';
                                        echo '<td class="text-center">'.$row->uom.'</td>';
                                        echo '<td class="text-right">'.sprintf('%0.2f',$row->qty).' </td>';
                                        echo '<td class="text-center">'.$row->price.'</td>';
                                        echo '<td class="text-center">'.$row->disc_amount.'</td>';
                                        echo '<td class="text-center">'.$row->cgst_amount.'</td>';
                                        echo '<td class="text-center">'.$row->sgst_amount.'</td>';
                                        echo '<td class="text-right">'.$row->taxable_amount.'</td>';
                                    echo '</tr>';
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
                            <th colspan="4" class="text-right">Total Qty.</th>
                            <th class="text-right"><?=sprintf('%.3f',$totalQty)?></th>
                            <th class="text-right"></th>
                            <th class="text-right"></th>
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
											$beforExp .= '<th class="text-right" >'.$row->exp_name.'</th>
											<td class="text-right">'.sprintf('%.2f',$expAmt).'</td>';
										else:
											$beforExp .= '<tr>
												<th  class="text-right">'.$row->exp_name.'</th>
												<td class="text-right">'.sprintf('%.2f',$expAmt).'</td>
											</tr>';
										endif;                                
									else:
										$afterExp .= '<tr>
											<th  class="text-right">'.$row->exp_name.'</th>
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
										$taxHtml .= '<th  class="text-right">'.$taxRow->name.'</th>
										<td class="text-right">'.sprintf('%.2f',$taxAmt).'</td>';
									else:
										$taxHtml .= '<tr>
											<th  class="text-right">'.$taxRow->name.'</th>
											<td class="text-right">'.sprintf('%.2f',$taxAmt).'</td>
										</tr>';
									endif;
								
									$rwspan++;
								endif;
							endforeach;

							$fixRwSpan = (!empty($rwspan))?3:0;
						?>
						<tr>

                            <td class="text-left" colspan="8" rowspan="<?=$rwspan?>">
                                <b>Bank Name : </b> <?=$companyData->company_bank_name.", ".$companyData->company_bank_branch?><br>
                                <b>A/c. No. : </b><?=$companyData->company_acc_no?><br>
                                <b>IFSC Code : </b><?=$companyData->company_ifsc_code?>
                                <hr>
                                <b>Note : </b> <?=$dataRow->remark?>
                            </td>

							<?php if(empty($rwspan)): ?>
                                <th  class="text-right">Round Off</th>
								<td class="text-right"><?=sprintf('%.2f',$dataRow->round_off_amount)?></td>
                            <?php endif; ?>
                            
						</tr>
						<?=$beforExp.$taxHtml.$afterExp?>
						<tr>
							<th class="text-left" colspan="8" rowspan="3">
								Amount In Words : <br><?=numToWordEnglish(sprintf('%.2f',$dataRow->net_amount))?>
							</th>

							<?php if(empty($rwspan)): ?>
                                <th  class="text-right">Grand Total</th>
                                <th class="text-right"><?=sprintf('%.2f',$dataRow->net_amount)?></th>
                            <?php endif; ?>
						</tr>

						<?php if(!empty($rwspan)): ?>
						<tr>
							<th  class="text-right">Round Off</th>
							<td class="text-right"><?=sprintf('%.2f',$dataRow->round_off_amount)?></td>
						</tr>
						<tr>
							<th  class="text-right">Grand Total</th>
							<th class="text-right"><?=sprintf('%.2f',$dataRow->net_amount)?></th>
						</tr>	
						<?php endif; ?>		
                    </tbody>
                </table>
		
                <table class="table top-table" style="margin-top:10px;">
                    <tr>
                        <th class="text-left">Terms & Conditions :-</th>
                    </tr>
                    <?php
                        if(!empty($termsData->condition)):
                            echo '<tr>';
                                echo '<td class=" fs-10">'.$termsData->condition.'</td>';
                            echo '</tr>';
                        endif;
                    ?>
                </table>
                
                <htmlpagefooter name="lastpage">
                    <table class="table top-table" style="margin-top:10px;border-top:1px solid #545454;">
                        <tr>
                            <td style="width:50%;" rowspan="3"></td>
                            <th colspan="2" class="text-center">For, <?=$companyData->company_name?></th>
                        </tr>
						<tr>
							<td style="width:25%;" class="text-center"><?=$dataRow->created_name?></td>
							<td style="width:25%;" class="text-center"><?=$dataRow->approved_by?></td>
						</tr>
						<tr>
							<td style="width:25%;" class="text-center"><b>Prepared By</b></td>
							<td style="width:25%;" class="text-center"><b>Authorised By</b></td>
						</tr>
                    </table>
                    <table class="table top-table" style="margin-top:10px;border-top:1px solid #545454;">
						<tr>
							<td style="width:25%;">PINV. No. & Date : <?=$dataRow->trans_number.' ['.formatDate($dataRow->trans_date).']'?></td>
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