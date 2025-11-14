<html>
    <head>
        <title>TAX INVOICE</title>
        <!-- Favicon icon -->
        <link rel="icon" type="image/png" sizes="16x16" href="<?=base_url();?>assets/images/favicon.png">
    </head>
    <body>
        <table class="table table-bordered">
            <tr>
                <th colspan="4" class="text-center" style="width:100%;"><h1>TAX INVOICE</h1></th>
            </tr>
            <tr>
                <td colspan="2" rowspan="3" class="text-left" style="width:50%;">
                    <b>Exporter</b><hr style="margin:5px 0px;">
                    <b><?=$companyData->company_name?></b><br>
                    <?=$companyData->company_address."<br>".$companyData->company_city_name.", ".$companyData->company_state_name." - ".$companyData->company_pincode.", ".$companyData->company_country_name?><br>
                    Mobile No. : <?=$companyData->company_phone?><br>
                    Contact Person : <?=$companyData->company_contact_person?>
                </td>
                <td style="width:25%;">Invoice No. <br> <b><?=$dataRow->trans_number?></b></td>
                <td style="width:25%;">Date <br> <b><?=formatDate($dataRow->trans_date,"d F Y")?></b></td>
            </tr>
            <tr>
                <td>IEC <br> <b><?=$companyData->company_pan_no?></b></td>
                <td>GST <br> <b><?=$companyData->company_gst_no?></b></td>
            </tr>
            <tr>
                <td style="width:25%;">Po. No. <br> <b><?=$dataRow->doc_no?></b></td>
                <td style="width:25%;">Date <br> <b><?=(!empty($dataRow->doc_date))?formatDate($dataRow->doc_date,"d F Y"):""?></b></td>
            </tr>
            <tr>
                <td colspan="2" class="text-left" style="width:50%;">Consignee</td>
                <td colspan="2" class="text-left" style="width:50%;">Buyer (If not Consignee)</td>
            </tr>
            <tr>
                <td colspan="2" class="text-left" style="width:50%;vertical-align: top;">
                    <b><?=$dataRow->consignee?></b>
                </td>
                <td colspan="2" class="text-left" style="width:50%;vertical-align: top;height:80px;">
                    <b><?=$dataRow->buyer_name?></b><br>
                    <?=$dataRow->buyer_address?>
                </td>
            </tr>
            <tr>
                <td class="text-left">
                    Metdod of Dispatch<br>
                    <b><?=$dataRow->method_of_dispatch?></b>
                </td>
                <td class="text-left">
                    Type of Shipment<br>
                    <b><?=$dataRow->type_of_shipment?></b>
                </td>
                <td class="text-left">
                    Country Of Origin<br>
                    <b><?=$dataRow->country_of_origin?></b>
                </td>
                <td class="text-left">
                    Country of Final Destination<br>
                    <b><?=$dataRow->country_of_fd?></b>
                </td>
            </tr>
            <tr>
                <td class="text-left">
                    Port of Loading<br>
                    <b><?=$dataRow->port_of_loading?></b>
                </td>
                <td class="text-left">
                    Date of Departure<br>
                    <b><?=(!empty($dataRow->date_of_departure))?formatDate($dataRow->date_of_departure):""?></b>
                </td>
                <td colspan="2" rowspan="2" class="text-left" style="vertical-align: top;">
                    Terms / Method of Payment<br>
                    <b><?=$dataRow->terms_and_method_of_payment?></b>
                </td>
            </tr>
            <tr>
                <td class="text-left">
                    Port of Discharge<br>
                    <b><?=$dataRow->port_of_discharge?></b>
                </td>
                <td class="text-left">
                    Final Destination<br>
                    <b><?=$dataRow->final_destination?></b>
                </td>
            </tr>
        </table>

        <table class="table table-bordered">
            <thead>
                <tr>
                    <th class="text-center">Product Code</th>
                    <th class="text-center">Description of Goods</th>
                    <th class="text-center">HSN Code</th>
                    <th class="text-center">Total Box</th>
                    <th class="text-center">Qty. In Box</th>
                    <th class="text-center">Unit Qty.</th>
                    <th class="text-center">Unit Type</th>
                    <th class="text-center">Price</th>
                    <th class="text-center">Disc. Amt.</th>
                    <th class="text-center">Amount</th>
                </tr>
            </thead>
            <tbody>
                <?php
                    $i=1;$totalQty = $totalBox = $totalDiscAmt = $totalNetAmount = $totalPallets = $totalNetWeight = $totalGrossWeight = 0;
                    $maxGst = 0;
                    if(!empty($dataRow->itemList)):
                        foreach($dataRow->itemList as $row):
                            echo '<tr>
                                <td class="text-center">'.$row->item_code.'</td>
                                <td class="text-center">'.$row->item_name.'</td>
                                <td class="text-center">'.$row->hsn_code.'</td>
                                <td class="text-center">'.floatval($row->total_box).'</td>
                                <td class="text-center">'.floatval($row->packing_qty).'</td>
                                <td class="text-center">'.floatval($row->qty).'</td>
                                <td class="text-center">'.$row->unit_name.'</td>
                                <td class="text-center">'.floatval($row->price).'</td>
                                <td class="text-center">'.floatval($row->disc_amount).'</td>
                                <td class="text-center">'.floatval($row->taxable_amount).'</td>
                            </tr>';
                            $i++;
                            $totalQty += floatval($row->qty);
                            $totalBox += floatval($row->total_box);
                            $totalDiscAmt += $row->disc_amount;
                            $totalNetAmount += $row->net_amount;
                            $totalPallets += floatval($row->pallet_qty);
                            $totalNetWeight += $row->net_weight;
                            $totalGrossWeight += $row->gross_weight;                            
                        endforeach;

                        $maxGst = MAX(array_column($dataRow->itemList,'gst_per'));
                    endif;

                    $blankLines = (15 - $i);
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
                            </tr>';
                        endfor;
                    endif;

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
                                $rwspan++;
                            endif;
                        endif;
                    endforeach;

                    $taxHtml = '';$totalTaxAmount = 0;
                    foreach ($taxList as $taxRow) :
                        $taxAmt = 0;
                        $taxAmt = floatVal($dataRow->{$taxRow->map_code.'_amount'});
                        if(!empty($taxAmt)):
                            if($rwspan == 0):
                                $taxHtml .= '<th class="text-right">'.$taxRow->name.' @'.(($dataRow->gst_type == 1)?floatVal($maxGst/2):floatVal($maxGst)).'%</th>
                                <td class="text-right">'.sprintf('%.2f',$taxAmt).'</td>';
                            else:
                                $taxHtml .= '<tr>
                                    <th class="text-right">'.$taxRow->name.' @'.(($dataRow->gst_type == 1)?floatVal($maxGst/2):floatVal($maxGst)).'%</th>
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
                    <th colspan="3" class="text-center">Consignment Total</th>
                    <th class="text-center"><?=$totalBox?></th>
                    <th></th>
                    <th class="text-center"><?=$totalQty?></th>
                    <th></th>
                    <th></th>
                    <th class="text-right">Sub Total</th>
                    <!-- <th class="text-center"><?=sprintf("%.02f",$totalDiscAmt)?></th> -->                    
                    <th class="text-right"><?=sprintf("%.02f",$dataRow->taxable_amount)?></th>
                </tr>
                <tr>
                    <td class="text-left" colspan="8" rowspan="<?=$rwspan?>">
                        <b>Note : <?=($totalTaxAmount > 0)?"WITH PAYMENT OF GST":"LUT(ARN) NUMBER, "?></b>
                    </td>
                    <?php if(empty($rwspan)): ?>
                        <th class="text-right">Round Off</th>
                        <td class="text-right"><?=sprintf('%.2f',$dataRow->round_off_amount)?></td>
                    <?php endif; ?>
                </tr>
                <?=$beforExp.$taxHtml.$afterExp?>
                <tr>
                    <td class="text-left" colspan="8" rowspan="<?=$fixRwSpan?>">
                        <b>Bill Amount (In Words)</b> : <?=numToWordEnglish(sprintf('%.2f',$dataRow->net_amount))?>
                    </td>	
                    
                    <?php if(empty($rwspan)): ?>
                        <th class="text-right">Grand Total</th>
                        <th class="text-right"><?=sprintf('%.2f',$dataRow->net_amount)?></th>
                    <?php else: ?>
                        <th class="text-right">Round Off</th>
                        <td class="text-right"><?=sprintf('%.2f',$dataRow->round_off_amount)?></td>
                    <?php endif; ?>
                </tr>
                <?php if(!empty($rwspan)): ?>
                <tr>
                    <th class="text-right">Grand Total</th>
                    <th class="text-right"><?=sprintf('%.2f',$dataRow->net_amount)?></th>
                </tr>
                <?php endif; ?>
            </tbody>
        </table>

        <table class="table table-bordered" style="width:100%;">
            <tr>
                <td colspan="3" class="text-left" style="width:50%;">
                    Additional Info
                </td>
                <td colspan="2" class="text-left" style="width:25%;">
                    Incoterms&#174; 2020
                </td>
                <td style="width:25%;" class="text-center">
                    Date of Issue
                </td>
            </tr>
            <tr>
                <th colspan="3" style="width:50%;">
                    "I/We Shell Claim Rewards Under Merchandise Export From India Scheme. (RoDTEP)"
                </th>
                <th style="width:10%;">
                    <?=$dataRow->delivery_type?>
                </th>
                <th style="width:20%;">
                    <?=$dataRow->delivery_location?>
                </th>
                <th style="width:20%;">
                    <?=formatDate($dataRow->trans_date)?>
                </th>
            </tr>
            <tr>
                <th class="text-left" style="width:30%;">Total Pallets</th>
                <th class="text-left" style="width:10%;"><?=$totalPallets?></th>
                <th class="text-left" style="width:10%;">Nos</th>

                <td colspan="3" style="width:50%;">
                    Signatory Company<br>
                    <b><?=$companyData->company_name?></b>
                </td>
            </tr>
            <tr>
                <th class="text-left" style="width:30%;">Total Gross Weight</th>
                <th class="text-left" style="width:10%;"><?=sprintf("%.03f",$totalGrossWeight)?></th>
                <th class="text-left" style="width:10%;">Kgs</th>

                <td colspan="3" rowspan="2" style="width:50%;">
                    Name of Authorized Signatory<br>
                    <b></b>
                </td>
            </tr>
            <tr>
                <th class="text-left" style="width:30%;">Total Net Weight</th>
                <th class="text-left" style="width:10%;"><?=sprintf("%.03f",$totalNetWeight)?></th>
                <th class="text-left" style="width:10%;">Kgs</th>
            </tr>
            <tr>
                <td colspan="3">
                    Bank Details:<br>
                    <b>Account Name : <?=$companyData->company_acc_name?></b><br>
                    <b>Account Number : <?=$companyData->company_acc_no?></b><br>
                    <b>IFSC : <?=$companyData->company_ifsc_code?></b><br>
                    <b>Swift Code : <?=$companyData->swift_code?></b><br>
                    <b>Bank Name : <?=$companyData->company_bank_name?></b>
                </td>
                <td class="text-center" colspan="3" style="width:50%;height:90px">
                    
                </td>
            </tr>
        </table>

        <htmlpagefooter name="lastpage">
            <table class="table top-table" style="margin-top:10px;border-top:1px solid #545454;">
                <tr>
                    <td style="width:25%;"></td>
                    <td style="width:25%;"></td>
                    <td style="width:25%;text-align:right;">Page No. {PAGENO}/{nbpg}</td>
                </tr>
            </table>
        </htmlpagefooter>
        <sethtmlpagefooter name="lastpage" value="on" />
    </body>
</html>