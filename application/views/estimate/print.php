<html>
    <head>
        <title>Estimate</title>
        <!-- Favicon icon -->
        <link rel="icon" type="image/png" sizes="16x16" href="<?=base_url();?>assets/images/favicon.png">
    </head>
    <body>
        <div class="row">
            <div class="col-12">
            <table>
                <tr>
                    <td>
                        <?php if(!empty($companyData->print_header)): ?>
                            <img src="<?=base_url($companyData->print_header)?>" class="img">
                        <?php endif;?>
                    </td>
                </tr>
            </table>

                <table class="table bg-light-grey">
                    <tr class="" style="letter-spacing: 2px;font-weight:bold;padding:2px !important; border-bottom:1px solid #000000;">
                        <td style="width:33%;" class="fs-18 text-left"></td>
                        <th style="width:33%;" class="fs-18 text-center">ESTIMATE</th>
                        <td style="width:33%;" class="fs-18 text-right"></td>
                    </tr>
                </table>
                
                <table class="table item-list-bb fs-22" style="margin-top:5px;">
                    <tr >
                        <td rowspan="4" style="width:67%;vertical-align:top;">
                            <b>M/S. <?=$dataRow->party_name?></b><br>
                            <?=(!empty($dataRow->ship_address) ? $dataRow->ship_address ." - ".$dataRow->ship_pincode : '')?><br>
                            Contact No. : <?=$dataRow->contact_no?><br>
                            Email : <?=$partyData->contact_email?>
                        </td>
                        <td>
                            <b>ES. No.</b>
                        </td>
                        <td>
                            <?=$dataRow->trans_number?>
                        </td>
                    </tr>
                    <tr>
				        <th class="text-left">ES Date</th>
                        <td><?=formatDate($dataRow->trans_date)?></td>
                    </tr>
                </table>
                
                <table class="table item-list-bb" style="margin-top:10px;">
                    <tr>
                        <th style="width:40px;">No.</th>
                        <th class="text-left">Item Description</th>
                        <th style="width:100px;">Qty</th>
                        <th style="width:100px;">Price</th>
                        <th style="width:120px;">Amount</th>
                    </tr>
                    <?php
                        $i=1;$totalBoxQty=$totalQty = 0;
                        if(!empty($dataRow->itemList)):
                            foreach($dataRow->itemList as $row):
                                
                                echo '<tr>';
                                    echo '<td class="text-center">'.$i++.'</td>';
                                    echo '<td>'.$row->item_name.'</td>';
                                    echo '<td class="text-right">'.floatVal($row->qty).'</td>';
                                    echo '<td class="text-right">'.floatVal($row->price).'</td>';
                                    echo '<td class="text-right">'.floatVal($row->taxable_amount).'</td>';
                                echo '</tr>';
                                $totalBoxQty += $row->total_box;
                                $totalQty += $row->qty;
                            endforeach;
                        endif;

                        $blankLines = (20 - $i);
                        if($blankLines > 0):
                            for($j=0;$j<=$blankLines;$j++):
                                echo '<tr>
                                    <td style="border-top:none;border-bottom:none;">&nbsp;</td>
                                    <td style="border-top:none;border-bottom:none;"></td>
                                    <td style="border-top:none;border-bottom:none;"></td>
                                    <td style="border-top:none;border-bottom:none;"></td>
                                    <td style="border-top:none;border-bottom:none;"></td>
                                </tr>';
                            endfor;
                        endif;
                    ?>
                    <tr>
                        <th colspan="2" class="text-right">Total Qty.</th>
                        <th class="text-right"><?=floatval($totalQty)?></th>
                        <th class="text-right"></th>
                        <th class="text-right"><?=sprintf('%.2f',$dataRow->taxable_amount)?></th>
                    </tr>
                    <tr>
                        <td colspan="3" class="text-left" rowspan="2">
                            <b>Amount (In Words) : </b> <?=numToWordEnglish(sprintf('%.2f',$dataRow->net_amount))?>
                        </td>
                        <th class="text-right">Round Off</th>
                        <td class="text-right"><?=sprintf('%.2f',$dataRow->round_off_amount)?></td>
                    </tr> 
                    <tr>
                        <th class="text-right">Grand Total</th>
                        <th class="text-right"><?=sprintf('%.2f',$dataRow->net_amount)?></th>
                    </tr>                   
                </table>
                
                <htmlpagefooter name="lastpage">
                    <table class="table top-table" style="margin-top:10px;border-top:1px solid #545454;">
						<tr>
							<td style="width:25%;">ES No. & Date : <?=$dataRow->trans_number.' ['.formatDate($dataRow->trans_date).']'?></td>
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
