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
                <th class="fs-14 text-right">
                    <?=$printType?>
                </th>
            </tr>
        </table>

        <table class="table item-list-bb fs-22" style="margin-top:5px;">
            <tr>
                <td rowspan="3" style="text-align:center;width:50%;">
                    <h1>Job Work Challan </h1>                            
                    <p style="font-size:11px;">Challan for movement of inputs or partially processed goods under GST section 143 From one factory to another factory for further processing/operation.</p>
                </td>
                <td>Challan No.</td>
                <td style="text-align:center; background-color:#D2D8E0;"><b><?=$outSourceData->ch_number?></b></td>
            </tr>
            <tr>
                <td>Challan Date</td>
                <td style="text-align:center; background-color:#D2D8E0;"><b><?=formatDate($outSourceData->ch_date)?></b></td>
            </tr>
            <tr>
                <td>Expected Delivery Date</td>
                <td style="text-align:center; background-color:#D2D8E0;"><b><?=(!empty($outSourceData->delivery_date) ? formatDate($outSourceData->delivery_date) : '-')?></b></td>
            </tr>
            <tr>
                <td class="text-left">
                    <b>Ship From </b>
                </td>
                <td colspan="2" class="text-left">
                    <b>Ship To </b>
                </td>
            </tr>
            <tr style="background-color:#D2D8E0;">
                <td><?=$companyData->company_name?></td>
                <td colspan ="2"><?= $outSourceData->party_name?></td>
            </tr>
            <tr>
                <td>
                    <?=$companyData->company_address?>
                </td>
                <td colspan ="2"><?= $outSourceData->party_address?></td>
            </tr>
            <tr>
                <td>
                    <b>Contact No : </b><?=$companyData->company_phone?>
                </td>
                <td colspan ="2"><b>Contact No : </b><?=$outSourceData->party_mobile?></td>
            </tr>
            <tr>
                <td><b>GSTIN : </b><?=$companyData->company_gst_no?></td>
                <td colspan ="2"><b>GSTIN : </b><?=$outSourceData->gstin?></td>
            </tr>                    
        </table>
        
        <table class="table item-list-bb" style="margin-top:10px;">
			<?php $price_uom = (!empty($reqData->rm_uom) ? '<br><small>(As Per '.$reqData->rm_uom.')</small>' : '<br><small>(As Per NOS)</small>'); ?>
            <tr style="background-color:#D2D8E0;">
                <th style="width:40px;">No.</th>
                <th class="text-left">Item Description</th>
                <th style="width:80px;">HSN Code</th>
                <th style="width:80px;">Nos.</th>
                <th style="width:80px;">Kgs.</th>
                <th style="width:80px;">Price <?=$price_uom?></th>
                <th style="width:80px;">Amount <?=$price_uom?></th>
            </tr>
            <tbody>
                <?php
                    $i=1; $totalQty=0; $totalAmt=0; $totalKgs=0;$vendorHtml = '';
                    if(!empty($reqData)):
                            $nos = ((!empty($reqData->rm_uom) && $reqData->rm_uom == 'NOS') ? ($reqData->qty) : 0);
                            $kgs = ((!empty($reqData->rm_uom) && $reqData->rm_uom == 'KGS') ? ($reqData->qty) : 0);
                            $amount =  ($reqData->qty * $reqData->price);
                            $prc_no = (!empty($reqData->grn_number) ? '<br><b>GRN No. : </b>'.$reqData->grn_number : '');
                            $batch_no = (!empty($reqData->grn_batch) ? '<br><b>Batch No. : </b>'.$reqData->grn_batch : '');
                            $grade = (!empty($reqData->rm_grade) ? '<br><b>Material Grade : </b>'.$reqData->rm_grade : '');

                            $pData = '';
                            if (!empty($tcData)) {
                                foreach($tcData as $row) {
                                    $parameter = json_decode($row->parameter);

                                    if ($parameter) {
                                        foreach($parameter as $key => $value) {
											$minText = (!empty($value->min) && $value->min !== '-') ? 'Min:'.$value->min : '';
											$maxText = (!empty($value->max) && $value->max !== '-') ? ' Max:'.$value->max : '';
											$pData .= (!empty($minText) || !empty($maxText) ? $value->param . ' (' . $minText . $maxText . '), ' : '');
                                        }
                                    }
                                }
                                $pData = rtrim($pData, ', ');
                            }
							$paramData = (!empty($pData) ? '<br><b>Parameter : </b>'.$pData : '');


                            echo '<tr>';
                                echo '<td class="text-center">'.$i++.'</td>';
                                echo '<td style="line-height:20px;">'.(!empty($reqData->rm_item_code) ? '['.$reqData->rm_item_code.'] ' : '').$reqData->rm_item.$prc_no.$batch_no. $grade.$paramData.'</td>';
                                echo '<td class="text-center">'.$reqData->rm_hsn.'</td>';
                                echo '<td class="text-right">'.round($nos,2).'</td>';
                                echo '<td class="text-right">'.round($kgs,2).'</td>';
                                echo '<td class="text-right">'.round($reqData->price,2).'</td>';
                                echo '<td class="text-right">'.round($amount,2).'</td>';
                            echo '</tr>';

                            $totalKgs += $nos;
                            $totalQty += $kgs;
                            $totalAmt += $amount;
                            $vendorHtml .= '<tr>
                                                <td style="width:20%;" height="180"></td>
                                                <td style="width:20%;"></td>
                                                <th style="width:20%; font-size:13px; vertical-align:top;" class="text-center" ></th>
                                                <th style="width:40%; font-size:12px; vertical-align:top;line-height:20px;" class="text-left">
                                                    Net wt. before Process : <br>
                                                    Scrap Wt. : <br>
                                                    Process Loss Wt. : <br>
                                                    Net wt. after Process :
                                                </th>
                                            </tr>';
                                        
                        // endforeach;
                    endif;

                    $blankLines = (1 - $i);
                    if($blankLines > 0):
                        for($j=1;$j<=$blankLines;$j++):
                            echo '<tr>
                                    <td style="border-top:none;border-bottom:none;" height="140">&nbsp;</td>
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
                    <th colspan="3" class="text-right">Total</th>
                    <th class="text-right"><?=round($totalQty,2)?></th>
                    <th class="text-right"><?=round($totalKgs,2)?></th>
                    <th></th>
                    <th class="text-right"><?=moneyFormatIndia(sprintf('%.2f',$totalAmt,2))?></th>
                </tr>
                <tr>
                    <td colspan="3"></td>
					<td colspan="2" height="100" class="text-center"><?=$outSourceData->emp_name?><br><b>Prepared By</b></td>
                    <td colspan="2" height="100" class="text-center"><br><b>Authorized By</b></td>
                </tr>
            </tbody>
        </table>

        <table class="table item-list-bb" style="margin-top:10px;">
            <tr style="background-color:#D2D8E0;">
                <th colspan="4">PART - II - TO BE FILLED BY JOBWORKER</th>
            </tr>
            <tr class="text-center">
                <th style="width:20%;">Date & Time Of Dispatch</th>
                <th style="width:20%;">Quantity Of Dispatch</th>
                <th style="width:20%;">Nature of Process Done</th>
                <th style="width:40%;">Quantity of waste material returned to the parent factory</th>
            </tr>
            <?=$vendorHtml?>
        </table>        
        
        <htmlpagefooter name="lastpage">
            <table class="table top-table" style="margin-top:10px;border-top:1px solid #545454;">
                <tr>
                    <td colspan="2" height="50"></td>
                </tr>
                <tr>
                    <td>
                        <b>Transporter : </b><?=(!empty($outSourceData->transport_name) ? $outSourceData->transport_name : '')?><br>
                        <b>Vehicle No : </b><?=(!empty($outSourceData->vehicle_no) ? $outSourceData->vehicle_no : '')?>
                    </td>
                    <td class="text-center"><br><b>Jobwork By</b></td>
                </tr>
            </table>
            <table class="table top-table" style="margin-top:10px;border-top:1px solid #545454;">
                <tr>
                    <td style="width:25%;">Challan No. & Date : <?=$outSourceData->ch_number.' ['.formatDate($outSourceData->ch_date).']'?></td>
                    <td style="width:25%;"></td>
                    <td style="width:25%;text-align:right;">Page No. {PAGENO}/{nbpg}</td>
                </tr>
            </table>
        </htmlpagefooter>
        <sethtmlpagefooter name="lastpage" value="on" />                
    </div>
</div>        