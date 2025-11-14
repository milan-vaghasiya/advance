<html>
    <head>
        <title>Tool Invoice</title>
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
                        <td style="width:33%;" class="fs-18 text-center">Tool Invoice</td>
                        <td style="width:33%;" class="fs-18 text-right"></td>
                    </tr>
                </table>
                
                <table class="table item-list-bb fs-22" style="margin-top:5px;">
                    <tr >
                        <td rowspan="3" colspan="2" style="width:50%;vertical-align:top;">
                            <b>Exporter :</b><br>
                            <b>M/S. <?=$companyData->company_name?></b><br>
                            <?=(!empty($companyData->company_address) ? $companyData->company_address ." - ".$companyData->company_pincode : '')?><br>
                            Tel. : <?=$companyData->company_phone?><br>
                            IEC Code : <?=$companyData->iec_no?><br>
                        </td>
                        <td><b>PINV. No.</b></td>
                        <td><?=$dataRow->trans_number?></td>
                        <td><b>PINV. Date</b></td>
                        <td><?=formatDate($dataRow->trans_date)?></td>
                    </tr>
                    
                    <tr>
				        <th class="text-left">Buyers Order No.& Date</th>
                        <td colspan="3"><?=$dataRow->cust_po_no ?><br><?= formatDate($dataRow->cust_po_date)?></td>
                    </tr>
                    <tr>
                        <th class="text-left">Other references : </th>
                        <td colspan="3">NIL</td>
                    </tr>
                  
                    <tr>
                        <td rowspan="2" colspan="2" style="width:50%;vertical-align:top;">
                            <b>Consignee :</b><br>
                            <b>M/S. <?=$dataRow->party_name?></b><br>
                            <?=(!empty($partyData->party_address) ? $partyData->party_address ." - ".$partyData->party_pincode : '')?><br>
                            <b>Kind. Attn. : <?=$partyData->contact_person?></b> <br>
                            Contact No. : <?=$partyData->party_mobile?><br>
                            Email : <?=$partyData->party_email?><br><br>
                            GSTIN : <?=$partyData->gstin?>
                        </td>
                        <td colspan="4" class="text-center"> <b> Buyer (if other than consignee)</b><br>
                            SAME AS BUYER
                        </td>
                    </tr>
                    <tr>
                        <td colspan="2" class="text-center"> <b> Country of origin of goods</b><br>
							India
                        </td>
                        <td colspan="2" class="text-center"> <b> Country of Final Destination</b><br>
                            <?= $partyData->city_name.','.$partyData->country_name?>
                        </td>
                    </tr>


                    <tr>
                        <td class="text-center">
                            <b>Pre-Carriage By</b><br>
                            <?=(!empty($dataRow->carriage_by) ?$dataRow->carriage_by : "-")?>
                        </td>
                        <td class="text-center"> <b> Rcpt at by Pre-Carrier</b><br>
                            <?=(!empty($dataRow->rcpt_by) ?$dataRow->rcpt_by : "-")?>

                        </td>

                        <td colspan="4" rowspan="3" class="text-center" style="width:50%;"> <b> Terms of Delivery & Payment</b><br>
                        <?=(!empty($dataRow->terms) ?$dataRow->terms : "-")?>
                        </td>
                    </tr>

                    <tr>
                        <td class="text-center">
                            <b>Vessel/Flight No</b><br>
                            <?=(!empty($dataRow->flight_no) ?$dataRow->flight_no : "-")?>

                        </td>
                        <td class="text-center"> <b> Port of Loading</b><br>
                            <?=(!empty($dataRow->port_loading) ?$dataRow->port_loading : "-")?>

                        </td>
                    </tr>

                    <tr>
                        <td class="text-center">
                            <b>Final Destination</b><br>
                            <?=(!empty($dataRow->destination) ?$dataRow->destination : "-")?>

                        </td>
                        <td class="text-center"> <b> Port of Discharge</b><br>
                            <?=(!empty($dataRow->port_discharge) ?$dataRow->port_discharge : "-")?>

                        </td>
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
                                       
                                    </tr>';
                                endfor;
                            endif;
                        ?>
                        <tr>
                            <th colspan="2">Net wt :<br> -</th>
                            <th>Gross wt : <br> -</th>
                            <th style="width:15%;">No. of Cases/Boxes:<br> -</th>
                            <th class="text-right"><?=sprintf('%.3f',$totalQty)?></th>
                            <th class="text-right"> Total</th>
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

                            <td class="text-left" colspan="5" rowspan="<?=$rwspan?>">
                                <b>Note : </b> <?=$dataRow->remark?>
                            </td>

							<?php if(empty($rwspan)): ?>
                                <th  class="text-right">Round Off</th>
								<td class="text-right"><?=sprintf('%.2f',$dataRow->round_off_amount)?></td>
                            <?php endif; ?>
                            
						</tr>
						<?=$beforExp.$taxHtml.$afterExp?>
						<tr>
							<th class="text-left" colspan="5" rowspan="3">
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
		
              
                
                <htmlpagefooter name="lastpage">
                    <table class="table top-table" style="margin-top:10px;border-top:1px solid #545454;">
                        <tr>
                            <td style="width:50%;"></td>
                            <td style="width:20%;"></td>
                            <th class="text-center">For, <?=$companyData->company_name?></th>
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