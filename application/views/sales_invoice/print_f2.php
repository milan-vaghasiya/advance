<?php
    $msme = '';$toprs = 4;$notes= '';$regDetail = '';$refNo = '';
	
    if(!empty($companyData->udyam_no)){$regDetail .= '<b>Udyam Reg No. : </b> '.$companyData->udyam_no;}
    if(!empty($companyData->company_reg_no))
    {
        $regDetail .= ' • <b>MSME No. : </b> '.$companyData->company_reg_no;
    }
	if(!empty($companyData->cin_no)){$regDetail .= ' • <b>CIN No. : </b> '.$companyData->cin_no;}
    if(!empty($companyData->iec_no)){$regDetail .= ' • <b>IEC No. : </b> '.$companyData->iec_no;}
	
    if(!empty($invData->remark)){$notes = '<hr>'.$invData->remark;}
    if(!empty($invData->doc_no)){$refNo = $invData->doc_no;};
    if(!empty($invData->doc_no) AND !empty($invData->doc_date)){$refNo = $invData->doc_no." / ".formatDate($invData->doc_date,"d F Y");}
    
?>

<div class="row">
    <div class="col-12">
        <?php if(!empty($header_footer)): ?>
        <table>
            <tr>
                <td>
                    <?php if(!empty($letter_head)): ?>
                        <img src="<?=$letter_head?>" class="img">
                    <?php endif;?>
                </td>
            </tr>
        </table>
        <?php endif; ?>
        
        <table class="table bg-light-grey">
            <tr class="" style="letter-spacing: 2px;font-weight:bold;padding:2px !important; border-bottom:1px solid #000000;">
                <th style="width:25%;" class="fs-14 text-left">
                    GSTIN : <?=$companyData->company_gst_no?>
                </th>
				<td style="width:50%;" class="text-center">
                    <span class="fs-18"><b>TAX INVOICE</b></span><br>
                    <span class="fs-12">For Removal Taxable Goods From Factory Under Rule31 OF GST Act.2021 Goods Cleared Against LUT ARN No.AD240325049124F Dt: 22.03.2025</span>
                </td>
                <th style="width:25%;" class="fs-14 text-right">
                    <?=$printType?>
                </th>
            </tr>
        </table>
        
        <table class="table table-bordered" style="margin-top:5px;">
			<tr>
				<td colspan="3" class="fs-12 text-center"><small><?=$regDetail?></small></td>
			</tr>
            <tr>
				<td class="text-left" rowspan="6" style="width:35%;vertical-align: top;">
                    Ship To<br>
					<b>GSTIN: </b><?=((!empty($shipToData->gstin) && $shipToData->gstin != "URP") ? $shipToData->gstin : "")?><br><br>
                    <b><?=(!empty($shipToData->party_name) ? $shipToData->party_name : '')?></b><br>
                    <?=(!empty($shipToData->delivery_address) ? $shipToData->delivery_address : '')?><br>
					<b>Country: </b><?=(!empty($shipToData->delivery_country_name) ? $shipToData->delivery_country_name : '')?> 
                    <b>State: </b><?=((!empty($shipToData->delivery_state_name) && !empty($shipToData->delivery_state_code)) ? $shipToData->delivery_state_code." - ".$shipToData->delivery_state_name : '')?> 
                    <b>City: </b><?=(!empty($shipToData->delivery_city_name) ? $shipToData->delivery_city_name : '').(!empty($shipToData->delivery_pincode) ? "-".$shipToData->delivery_pincode : '')?> <br><br>
					
					<b>Contact Person: </b><?=(!empty($shipToData->contact_person) ? $shipToData->contact_person : '')?><br>
					<b>Mobile No.: </b><?=(!empty($shipToData->party_mobile) ? $shipToData->party_mobile : '')?><br>
                </td>   
                <td class="text-left" rowspan="6" style="width:35%;vertical-align: top;">
                    Buyer (Bill To)<br>
					<b>GSTIN: </b><?=($invData->gstin != "URP")?$invData->gstin:""?><br><br>
                    <b><?=$invData->party_name?></b><br>
                    <?=(!empty($partyData->party_address) ? $partyData->party_address : '')?><br>
					<b>Country: </b><?=$partyData->country_name?> <b>State: </b><?=$partyData->state_code ." - ".$partyData->state_name?> <b>City: </b><?=$partyData->city_name."-".$partyData->party_pincode?> <br><br>
					
					<b>Contact Person: </b><?=$partyData->contact_person?><br>
					<b>Mobile No.: </b><?=$partyData->party_mobile?><br>
                </td>
                <td style="width:30%;">Invoice No. <br> <b><?=$invData->trans_number?></b></td>
			</tr>
            <tr>
                <td>Date <br> <b><?=formatDate($invData->trans_date,"d F Y")?></b></td>
            </tr>
            <!--<tr>
                <td>Payment <br> <b><?=(!empty($partyData->credit_days))?$partyData->credit_days." Days Credit":""?></b></td>
			</tr>-->
            <tr>
                <td>Memo Type <br> <b><?=$invData->memo_type?></b></td>
            </tr>
            <tr>
                <td>Transport <br> <b><?=(!empty($invData->transporter_name))?$invData->transporter_name." - ".$invData->transporter_gst_no:" - "?></b></td>
            </tr>
            <tr>
                <td>Vehicle No. <br> <b><?=(!empty($invData->vehicle_no))?$invData->vehicle_no:""?></b></td>
            </tr>
            <tr>
                <td>LR No.& Date: <br> <b><?=(!empty($invData->lr_no))?$invData->lr_no:""?></b> <b><?=(!empty($invData->lr_date))? ' & '.formatDate($invData->lr_date):""?></b></td>
            </tr>
        </table>
		<?php if(!empty($invData->e_inv_no)): ?>
			<table class="table item-list-bb" style="page-break-inside: avoid;">
				<tr>
					<th colspan="7" class="text-center">E-Invoice Details</th>
				</tr>
				<tr>
					<th>ACK NO.</th>
					<td><?=$invData->e_inv_no?></td>

					<th>ACK Date</th>
					<td><?=date("d-m-Y H:i:s",strtotime($invData->e_inv_date))?></td>

					<th>EWB No.</th>
					<td><?=(!empty($invData->eway_bill_no))?$invData->eway_bill_no:'-'?></td>
					
					<td rowspan="2" class="text-center" style="padding:0px;width:20%;">
						<img width="100" src="data:image/png;base64,'<?=$invData->e_inv_qr_code?>'">
					</td>
				</tr>
				<tr>
					<th>IRN</th>
					<td colspan="5">
						<?=$invData->e_inv_irn?>
					</td>
				</tr>
			</table>
        <?php endif; ?>
        <table class="table item-list-bb" style="margin-top:5px;">
            <?php 
				$discCol = ' <th style="width:50px;">Disc<br><small>(%)</small></th>';
				$footCol = 8;
				if($invData->discStatus == 0 ) { $discCol = ''; $footCol = 7; }
			
				$thead = '<thead>
                    <tr>
                        <th style="width:20px;">No.</th>
                        <th class="text-left">Description of Goods</th>
                        <th style="width:10%;">PO. No.</th>
                        <th style="width:10%;">HSN/SAC</th>
                        <th>No. & Desc.of pkg.</th>
                        <th style="width:50px;">Qty</th>
                        <th style="width:50px;">Unit</th>
                        <th style="width:50px;">Rate</th>
                        '.$discCol.'
                        <th style="width:50px;">GST<br><small>(%)</small></th>
                        <th style="width:90px;">Amount</th>
                    </tr>
                </thead>';
                echo $thead;
            ?>
            <tbody>
                <?php
                    $i=1;$totalBoxQty = $totalQty = 0;$migst=0;$mcgst=0;$msgst=0;
                    $rowCount = 1;$pageCount = 1;
                    if(!empty($invData->itemList)):
                        foreach($invData->itemList as $row):
                            $item_name = $row->item_name;
                            $item_name .= (!empty($row->drw_no)) ? '<br><b>Drg No.</b><small>'.$row->drw_no.'</small>' : '';
                            $item_name .= (!empty($row->rev_no)) ? '&nbsp;&nbsp;<b>Rev.</b><small>'.$row->rev_no.'</small>' : '';
							$item_name .= (!empty($row->item_remark)) ? '<br><b>Remark.</b><small>'.$row->item_remark.'</small>' : '';
                            $cust_po_no = (!empty($row->doc_no)) ? '<small>'.$row->doc_no.'<br>'.$row->doc_date.'</small>' : '';
                            
                            echo '<tr>';
                                echo '<td class="text-center" style="border-top:none;border-bottom:none;">'.$i++.'</td>';
                                echo '<td style="border-top:none;border-bottom:none;">'.$item_name.'</td>';
                                echo '<td class="text-center" style="border-top:none;border-bottom:none;">'.(!empty($cust_po_no) ? $cust_po_no : "").'</td>';
                                echo '<td class="text-center" style="border-top:none;border-bottom:none;">'.$row->hsn_code.'</td>';
                                echo '<td class="text-center" style="border-top:none;border-bottom:none;">'.(($row->from_entry_type == 241 && !empty($row->make))?$row->box_detail .'<br> '.$invData->p_type.' ':'LOOSE' ).'</td>';
                                echo '<td class="text-right" style="border-top:none;border-bottom:none;">'.sprintf('%.2f',$row->qty).'</td>';
                                echo '<td class="text-right" style="border-top:none;border-bottom:none;">'.$row->unit_name.'</td>';
                                echo '<td class="text-right" style="border-top:none;border-bottom:none;">'.sprintf('%.2f',$row->price).'</td>';
                                if($invData->discStatus > 0){ 
									echo '<td class="text-right" style="border-top:none;border-bottom:none;">' . floatval($row->disc_per) . '%</td>';
								}
                                echo '<td class="text-center" style="border-top:none;border-bottom:none;">'.sprintf('%.2f',$row->gst_per).'</td>';
                                echo '<td class="text-right" style="border-top:none;border-bottom:none;">'.sprintf('%.2f',$row->taxable_amount).'</td>';
                            echo '</tr>';
                            
                            $totalQty += $row->qty;
                            if($row->gst_per > $migst){$migst=$row->gst_per;$mcgst=$row->cgst_per;$msgst=$row->sgst_per;}
                        endforeach;
                    endif;

                    $blankLines = ($maxLinePP - $i);
					
					if(in_array($invData->tax_class,["EXPORTGSTACC","EXPORTTFACC"])):
						echo '<tr>
							<td style="border-top:none;border-bottom:none;"></td>
							<td style="border-top:none;border-bottom:none;">
								Export Invoice No: '.$invData->trans_number.' Dt.: '.formatDate($invData->trans_date,"d F Y").'
								Port of loading : '.$invData->port_code.'
							</td>
							<td style="border-top:none;border-bottom:none;"></td>
							<td style="border-top:none;border-bottom:none;"></td>
							<td style="border-top:none;border-bottom:none;"></td>
							<td style="border-top:none;border-bottom:none;"></td>
							<td style="border-top:none;border-bottom:none;"></td>
							<td style="border-top:none;border-bottom:none;"></td>
							<td style="border-top:none;border-bottom:none;"></td>
							<td style="border-top:none;border-bottom:none;"></td>
							'.(($invData->discStatus > 0) ? '<td style="border-top:none;border-bottom:none;"></td>' : '').'
						</tr>';
					endif;
							
                    if($blankLines > 0):
                        for($j=0;$j<=$blankLines;$j++):
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
                                '.(($invData->discStatus > 0) ? '<td style="border-top:none;border-bottom:none;"></td>' : '').'
                            </tr>';
                        endfor;
                    endif;
                    
                    $rwspan= 0; $srwspan = '';
                    $beforExp = "";
                    $afterExp = "";
                    $invExpenseData = (!empty($invData->expenseData)) ? $invData->expenseData : array();
                    foreach ($expenseList as $row) :
                        $expAmt = 0;
                        $amtFiledName = $row->map_code . "_amount";
                        if (!empty($invExpenseData) && $row->map_code != "roff") :
                            $expAmt = floatVal($invExpenseData->{$amtFiledName});
                        endif;

                        if(!empty($expAmt)):
                            if ($row->position == 1) :
                                if($rwspan == 0):
                                    $beforExp .= '<th colspan="2" class="text-right">'.$row->exp_name.'</th>
                                    <td class="text-right">'.sprintf('%.2f',$expAmt).'</td>';
                                else:
                                    $beforExp .= '<tr>
                                        <th colspan="2" class="text-right">'.$row->exp_name.'</th>
                                        <td class="text-right">'.sprintf('%.2f',$expAmt).'</td>
                                    </tr>';
                                endif;
                                $rwspan++;
                            endif;
                        endif;
                    endforeach;

                    $taxHtml = '';$totalTaxAmount = 0;
                    foreach ($taxList as $taxRow) :
                        $taxAmt = 0;
                        $taxAmt = floatVal($invData->{$taxRow->map_code.'_amount'});
                        if(!empty($taxAmt)):
                            if($rwspan == 0):
                                $taxHtml .= '<th colspan="2" class="text-right">'.$taxRow->name.' @'.(($invData->gst_type == 1)?floatVal($migst/2):$migst).'%</th>
                                <td class="text-right">'.sprintf('%.2f',$taxAmt).'</td>';
                            else:
                                $taxHtml .= '<tr>
                                    <th colspan="2" class="text-right">'.$taxRow->name.' @'.(($invData->gst_type == 1)?floatVal($migst/2):$migst).'%</th>
                                    <td class="text-right">'.sprintf('%.2f',$taxAmt).'</td>
                                </tr>';
                            endif;
                            $totalTaxAmount += $taxAmt;
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
                                    $afterExp .= '<th colspan="2" class="text-right">'.$row->exp_name.'</th>
                                    <td class="text-right">'.sprintf('%.2f',$expAmt).'</td>';
                                else:
                                    $afterExp .= '<tr>
                                        <th colspan="2" class="text-right">'.$row->exp_name.'</th>
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
                    <th colspan="5" class="text-right">Total Qty.</th>
                    <th class="text-right"><?=sprintf('%.2f',$totalQty)?></th>
                    <?=($invData->discStatus > 0 ? '<th></th>' : '')?>
                    <th></th>
                    <th colspan="2" class="text-right">Sub Total</th>
                    <th class="text-right"><?=numberFormatIndia(sprintf('%.2f',$invData->taxable_amount))?></th>
                </tr>
                <tr>
                    <td class="text-left" colspan="<?=$footCol?>" rowspan="<?=$rwspan?>">
                        <b>Bank Name : </b> <?=$companyData->company_bank_name.", ".$companyData->company_bank_branch?><br>
                        <b>A/c. No. : </b><?=$companyData->company_acc_no?><br>
                        <b>IFSC Code : </b><?=$companyData->company_ifsc_code?>
                        <?=$notes?>
                    </td>
                    <?php if(empty($rwspan)): ?>
                        <th colspan="2" class="text-right">Round Off</th>
                        <td class="text-right"><?=sprintf('%.2f',$invData->round_off_amount)?></td>
                    <?php endif; ?>
                </tr>
                <?=$beforExp.$taxHtml.$afterExp?>
                <tr>
                    <td class="text-left" colspan="<?=$footCol?>" rowspan="<?=$fixRwSpan?>">
						<b>GST Amount (In Words)</b> : <?=($totalTaxAmount > 0)?numToWordEnglish(sprintf('%.2f',$totalTaxAmount)):""?>
                        <hr>
                        <b>Bill Amount (In Words)</b> : <?=numToWordEnglish(sprintf('%.2f',$invData->net_amount))?>
                    </td>	
                    
                    <?php if(empty($rwspan)): ?>
						<th colspan="2" class="text-right">Grand Total</th>
                        <th class="text-right"><?=sprintf('%.2f',$invData->net_amount)?></th>
                    <?php else: ?>
                        <th colspan="2" class="text-right">Round Off</th>
                        <td class="text-right"><?=sprintf('%.2f',$invData->round_off_amount)?></td>
                    <?php endif; ?>
                </tr>
                
                <?php if(!empty($rwspan)): ?>
                <tr>
					<th colspan="2" class="text-right">Grand Total</th>
                    <th class="text-right"><?=numberFormatIndia(sprintf('%.2f',$invData->net_amount))?></th>
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
        
		<!--<htmlpagefooter name="lastpage">-->
            <table class="table top-table" style="margin-top:10px;border-top:1px solid #545454;border-bottom:1px solid #545454;">
                <tr>
                    <td></td>
                    <th>For, <?=$companyData->company_name?></th>
                </tr>
                <tr class="text-center">
                    <td><?=$invData->created_name?></td>
                    <td></td>
                </tr>
                <tr class="text-center">
                    <td style="width:60%;"><b>Prepared By</b></td>
                    <td style="width:40%;"><b>Authorised By</b></td>
                </tr>
            </table>
        <!--</htmlpagefooter>
		<sethtmlpagefooter name="lastpage" value="on" />-->
    </div>
</div>