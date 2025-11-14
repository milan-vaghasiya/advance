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
                <td style="text-align:center; background-color:#D2D8E0;"><b><?=(!empty($outSourceData->delivery_date) ? formatDate($outSourceData->delivery_date) : '')?></b></td>
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
			<?php $price_uom = (!empty($reqData->uom) ? '<br><small>(As Per '.$reqData->uom.')</small>' : '<br><small>(As Per NOS)</small>'); ?>
            <tr style="background-color:#D2D8E0;">
                <th style="width:40px;">No.</th>
                <th class="text-left">Item Description</th>
                <th style="width:80px;">HSN Code</th>
                <th style="width:80px;">Nos.</th>
                <th style="width:80px;">Kgs.</th>
                <th style="width:80px;">Material Value</th>
                <th style="width:80px;">Price <?=$price_uom?></th>
                <th style="width:80px;">Amount <?=$price_uom?></th>
            </tr>
            <tbody>
                <?php
                    $i=1; $totalQty=0; $totalAmt=0; $kgs=0; $totalKgs=0;$vendorHtml = '';
                    if(!empty($reqData)):
                        // foreach($reqData as $reqData):
                            $beforWt = ((empty($reqData->process_from))?$material_wt:((!empty($reqData->prev_weight) && $reqData->prev_weight > 0)?$reqData->prev_weight:0)) ;
                            $kgs = $beforWt * $reqData->qty;
                            $amount = ((!empty($reqData->uom) && $reqData->uom == 'KGS') ? ($kgs * $reqData->price) : ($reqData->qty * $reqData->price));
                            $process = (!empty($reqData->process_name) ? '<br><b>Process : </b>'.$reqData->process_name : '');
                            $prc_no = (!empty($reqData->prc_number) ? '<br><b>PRC No. : </b>'.$reqData->prc_number : '');
                            $batch_no = (!empty($reqData->batch_no) ? '<br><b>Batch No. : </b>'.$reqData->batch_no : '');
                            $grade = (!empty($reqData->material_grade) ? '<br><b>Material Grade : </b>'.$reqData->material_grade : '');
                            $remark = (!empty($reqData->mfg_instruction) ? '<br><b>Remark : </b>'.$reqData->mfg_instruction : '');
							
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
                                echo '<td style="line-height:20px;">'.(!empty($reqData->item_code) ? '['.$reqData->item_code.'] ' : '').$reqData->item_name.$process.$prc_no.$batch_no.$grade.$remark.$paramData.'</td>';
                                echo '<td class="text-center">'.$reqData->hsn_code.'</td>';
                                echo '<td class="text-right">'.round($reqData->qty,2).'</td>';
                                echo '<td class="text-right">'.round($kgs,2).'</td>';
                                echo '<td class="text-right">'.round($reqData->material_value,2).'</td>';
                                echo '<td class="text-right">'.round($reqData->price,2).'</td>';
                                echo '<td class="text-right">'.round($amount,2).'</td>';
                            echo '</tr>';

                            $totalKgs += $kgs;
                            $totalQty += $reqData->qty;
                            $totalAmt += $amount;
                            $bpWt = (!empty($reqData->ok_qty) && $reqData->ok_qty > 0)?(($reqData->ok_qty+$reqData->rej_qty) * $beforWt):0;
                            $scrapWt = (!empty($reqData->rej_qty) && $reqData->rej_qty > 0)?($reqData->rej_qty * $beforWt):0;
                            $processLossWt = (!empty($reqData->ok_qty) && $reqData->ok_qty > 0 && $reqData->finish_wt > 0)?((($beforWt -$reqData->finish_wt ) * $reqData->ok_qty)):0;
                            $apWt = (!empty($reqData->ok_qty) && $reqData->ok_qty > 0) && $reqData->finish_wt > 0?(($reqData->ok_qty) * $reqData->finish_wt):0;
							
							/*Scrap calc*/
							$prcKgs = round($kgs,2);
							$itemQty = round($reqData->qty,2);
							$kg_per_nos = (!empty($itemQty) ? ($prcKgs / $itemQty) : 0);
							$scrap_wt = round($kg_per_nos - $reqData->finish_wt,4);
							$total_scrap = ($scrap_wt * $itemQty);
							$process_cost = ($reqData->rej_qty * $kg_per_nos);
							
                            $vendorHtml .= '<tr>
								<td style="width:20%;" height="180"></td>
								<td style="width:20%;"></td>
								<th style="width:20%; font-size:13px; vertical-align:top;" class="text-center" >'.$reqData->process_name.'</th>
								<th style="width:40%; font-size:12px; vertical-align:top;line-height:20px;" class="text-left">
									Net wt. before Process : '.$bpWt.'<br>
									Scrap Wt. : '.$scrap_wt.'<br>
									Total scrap  : '.$total_scrap.'<br>
									Process Lost : '.$process_cost.' <br>
									Net wt. after Process : '.($bpWt - $total_scrap - $process_cost).'
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
                    <th></th>
                    <th class="text-right"><?=moneyFormatIndia(sprintf('%.2f',$totalAmt,2))?></th>
                </tr>
                <tr>
                    <td colspan="8">
                        <b>Description Of Goods : </b>
                        <?php
							//echo (empty($reqData->from_process_name))?(!empty($description_good)?$description_good:''):'Forged Artical';
							echo (empty($reqData->from_process_name))? 'STEEL BAR' : 'Forged Artical';
                        ?>
                    </td>
                </tr>
				<tr>
                    <td colspan="8">
                        <b>Notes : </b>
                        <?php
							echo (!empty($outSourceData->remark) ?  $outSourceData->remark : '');
                        ?>
                    </td>
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
                    <td style="width:30%;"></td>
                    <td style="width:20%;"></td>
                    <td style="width:20%;"></td>
                    <th class="text-center">For, <?=$companyData->company_name?></th>
                </tr>
                <tr>
                    <td colspan="4" height="50"></td>
                </tr>
                <tr>
                    <td>
                        <b>Transporter : </b><?=(!empty($outSourceData->transport_name) ? $outSourceData->transport_name : '')?><br>
                        <b>Vehicle No : </b><?=(!empty($outSourceData->vehicle_no) ? $outSourceData->vehicle_no : '')?>                                
                    </td>
                    <td class="text-center"><br><b>Jobwork By</b></td>
                    <td class="text-center"><?=$outSourceData->emp_name?><br><b>Prepared By</b></td>
                    <td class="text-center"><br><b>Authorised By</b></td>
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