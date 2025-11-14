<html>
    <head>
        <title>QUOTATION</title>
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
                        <td style="width:34%;" class="fs-18 text-center">QUOTATION</td>
                        <td style="width:33%;" class="fs-16 text-right"></td>
                    </tr>
                </table>
                
                <table class="table item-list-bb fs-22" style="margin-top:5px;">
                    <tr>
                        <td style="width:60%; vertical-align:top;" rowspan="4">
                            <b>M/S. <?=$dataRow->party_name?></b><br>
                            <?=(!empty($partyData->party_address) ? $partyData->party_address ." ".$partyData->party_pincode : '')?><br>
                            <b>City : </b><?= $partyData->city_name?> <b>State : </b><?=$partyData->state_name ?> <b>Country : </b><?=$partyData->country_name ?><br><br><br>
							
							<b>Kind. Attn.: <?=$partyData->contact_person?></b><br>
							Contact No.: <?=$partyData->party_mobile?><br>
							Email: <?=$partyData->party_email?>
                        </td>
                        <td>
                            <b>Qtn. No. : <?=$dataRow->trans_number?></b>
                        </td>
                        <td>
                            Rev No. : <?=sprintf("%02d",$dataRow->quote_rev_no)?>  / <?=formatDate($dataRow->doc_date)?>
                        </td>
                    </tr>
                    <tr>
                        <td style="width:40%;" colspan="2">
                            <b>Qtn. Date</b> : <?=formatDate($dataRow->trans_date)?><br>
                        </td>
                    </tr>
					<tr>
                        <td style="width:40%;" colspan="2">
                            <b>Your Reference</b> : <?=$dataRow->ref_by.(!empty($dataRow->ref_id)?' ['.$dataRow->ref_number.']': '')?><br>
                        </td>
                    </tr>
                    <tr>
                        <td style="width:40%;" colspan="2">
                            <b>Reference Date</b> : <?=(!empty($dataRow->delivery_date) ? formatDate($dataRow->delivery_date) : '')?><br>
                        </td>
                    </tr>
                </table>
                
                <table class="table item-list-bb" style="margin-top:10px;">
                    <thead>
                        <tr>
                            <th style="width:40px;">No.</th>
                            <th style="width:40px;">Part/Drg No.</th>
                            <th class="text-left" style="width:100px;">Part Description</th>
                            <th style="width:80px;">Annual Vol</th>
                            <th style="width:60px;">MOQ/Lot</th>
                            <th style="width:90px;">Material Grade </th>
                            <th style="width:60px;">Quote Rate/Pcs</th>
                            <th style="width:60px;">Tool Cost</th>
                            <th style="width:80px;">Remark<br></th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php
                            $i=1;$totalQty = 0;
                            if(!empty($dataRow->itemList)):
                                foreach($dataRow->itemList as $row):	
                                    echo '<tr>';
                                        echo '<td class="text-center">'.$i++.'</td>';
                                        echo '<td class="text-center">'.$row->drw_no.'</td>';
                                        echo '<td>'.$row->item_name.'</td>';
                                        echo '<td class="text-center">'.sprintf('%.2f',$row->annual_vol).'</td>';
                                        echo '<td class="text-center">'.sprintf('%.2f',$row->qty).' ('.$row->uom.')</td>';
                                        echo '<td class="text-center">'.$row->material_grade.'</td>';
                                        echo '<td class="text-right">'.sprintf('%.2f',$row->price,2).' ('.$partyData->currency.')</td>';
                                        echo '<td class="text-right">'.sprintf('%.2f',$row->tool_cost).' ('.$partyData->currency.')</td>';
                                        echo '<td class="text-right">'.$row->item_remark.'</td>';
                                    echo '</tr>';
                                    
                                    $totalQty += $row->qty;
                                endforeach;
                            endif;
                        ?>
                        <tr>
                            <td class="text-left" colspan="9" rowspan="<?=$rwspan?>">
                                <b>Note : </b> <?=$dataRow->remark?>
                            </td>
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
                    <table class="table top-table" style="margin-top:0px;border-top:1px solid #545454;">
                        <tr>
                            <td style="width:30%;"></td>
                            <td style="width:20%;"></td>
                            <td style="width:20%;"></td>
                            <th class="text-center">For, <?=$companyData->company_name?></th>
                        </tr>
                        <tr>
                            <td colspan="3" height="40"></td>
                        </tr>
                        <tr>
                            <td><br>This is a computer-generated quotation.</td>
                            <td class="text-center"><?=$dataRow->created_name?><br>Prepared By</td>
                            <td class="text-center"><?=$dataRow->internal_aprv_name?><br>Approved By</td>
                            <td class="text-center"><br>Authorised By</td>
                        </tr>
                    </table>
                    <table class="table top-table" style="margin-top:0px;border-top:1px solid #545454;">
						<tr>
							<td style="width:25%;">Qtn. No. & Date : <?=$dataRow->trans_number.' ['.formatDate($dataRow->trans_date).']'?></td>
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