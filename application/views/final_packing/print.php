<html>
    <head>
        <title>Dispatch Print</title>
        <link rel="icon" type="image/png" sizes="16x16" href="<?=base_url();?>assets/images/favicon.png">
    </head>
    <body>
        <div class="row">
            <div class="col-12">
				<table class="table top-table-border">
					<tr class="">
						<td class="org_title text-center" style="font-size:1.3rem;width:40%"> <?=$companyData->company_name?></td>
						<td colspan="2" class="org_title text-center" style="font-size:1rem;width:40%">Dispatch Details</td>
						<td colspan="2" class="text-right" style="width:20%"> DIS-F-01 <br>(Rev.No.01 & Dt.01-01-2025)</td>
					</tr>
                    <tr class="text-left">
                        <td rowspan="3">
                            <b><?=$dataRow->party_name?></b><br>
                            <?=$dataRow->party_address?><br>
                            <b>Contact No : </b><?=$dataRow->party_mobile?>
                        </td>
                        <th>Report No</th>
                        <td><?=$dataRow->trans_number?></td>
                        <th>Report Date</th>
                        <td><?=formatDate($dataRow->trans_date)?></td>
                    </tr>
                    <tr class="text-left">
                        <th>Transport</th>
                        <td><?=$dataRow->transport_name?></td>
                        <th>Vehicle No</th>
                        <td></td>
                    </tr>
                    <tr class="text-left">
                        <th>LR No.</th>
                        <td><?=$dataRow->lr_no?></td>
                        <th>LR. Date</th>
                        <td><?=formatDate($dataRow->lr_date)?></td>
                    </tr>
				</table> 
				<table class="table item-list-bb " style="margin-top:5px">
                    <thead >
                        <tr class="bg-light">
                            <th>Sr  No.</th>
                            <th>OA. NO.</th>
                            <th>Customer PO No.</th>
                            <th>PO Date</th>
                            <th>Part No</th>
                            <th>Part Name</th>
                            <th>Material Grade</th>
                            <th> Batch No.</th>
                            <th> Qty Dispatch</th>
                            <th> Qty / Box</th>
                            <th> No. Of Box</th>
                            <th>Wt/Piece in kg</th>
                            <th>Total Weight in kg</th>
                        </tr>

                    </thead>
                    <tbody>
                        <?php
                        $i=1;$totalWeight = 0;$toalQty=0; $totalWtBox=0;  $totalBox =0;
                        $boxList = array_reduce($dataRow->itemData , function($boxList, $item) { $boxList[$item->package_no][] = $item; return $boxList; }, []);
                        $batchList = array_reduce($batchData , function($batchList, $batch) { $batchList[$batch->pack_trans_id][] = $batch; return $batchList; }, []);
                        $primaryBox = array_reduce($batchData , function($primaryBox, $batch) { $primaryBox[$batch->pack_trans_id][$batch->primary_pack_id][] = $batch; return $primaryBox; }, []);
                        $boxCount = 1;
                        $boxDetail = [];$weightPerBox = 0;
                        foreach($boxList AS $package_no=>$item){
                            $firstRow = true; 
                            $rowspan=0; 
                            foreach($item AS $key=>$row){ 
                                if(!empty($batchList[$row->id])) {
                                    $rowspan += count($batchList[$row->id]);
                                }
                            }
                            foreach($item AS $key=>$row){
                                if(!empty($batchList[$row->id])) {
                                    foreach($batchList[$row->id] AS $batch){
                                        $qty = $batch->qty;//(count($primaryBox[$row->id][$batch->primary_pack_id]) > 1)?$batch->batch_qty:$batch->qty;
                                        $weight = $qty * $row->wt_pcs;
                                        $totalWeight += $weight;
                                        //$weightPerBox += $weight;
                                        ?>
                                        <tr >
                                            <td class="text-center"><?=$i++?></td>
                                            <td class="text-center"><?=$row->so_number?></td>
                                            <td class="text-center"><?=$row->doc_no?></td>
                                            <td class="text-center"><?=formatDate($row->doc_date)?></td>
                                            <td class="text-center"><?=$row->item_code?></td>
                                            <td class="text-left"><?=$row->item_name?></td>
                                            <td class="text-center"><?=$row->material_grade?></td>
                                            <td class="text-center"><?=$batch->prd_batch?></td>
                                            <td class="text-center"><?=$qty?></td>
                                            <?php if($firstRow == true){ $toalQty += array_sum(array_column($item,'total_qty')); ?>
                                                <td rowspan="<?=$rowspan?>" class="text-center"><?=array_sum(array_column($item,'total_qty'))?></td>
                                                <td rowspan="<?=$rowspan?>" class="text-center"><?=$row->package_no.'/'.count($boxList)?></td>
											<?php $firstRow =false ; } ?>
                                            <td class="text-center"><?=$row->wt_pcs?></td>
                                            <td class="text-center"><?=$weight?></td>
                                        </tr>
                                        <?php
                                        
                                    }
                                   
                                } 
                                else {
                                    $qty = (!empty($row->total_qty) ? floatval($row->total_qty) : 0);
                                    $weight = $qty * $row->wt_pcs;
                                    $totalWeight += $weight;
                                    //$weightPerBox += $weight;
                                    $toalQty += $qty;
                                    ?>
                                    <tr>
                                        <td class="text-center"><?=$i++?></td>
                                        <td class="text-center"><?=$row->so_number?></td>
                                        <td class="text-center"><?=$row->doc_no?></td>
                                        <td class="text-center"><?=formatDate($row->doc_date)?></td>
                                        <td class="text-center"><?=$row->item_code?></td>
                                        <td class="text-left"><?=$row->item_name?></td>
                                        <td class="text-center"><?=$row->material_grade?></td>
                                        <td class="text-center"></td>
                                        <td class="text-center"><?=$qty?></td>
                                        <td rowspan="<?=$rowspan?>" class="text-center"><?=$qty?></td>
                                        <td rowspan="<?=$rowspan?>" class="text-center"><?=$row->package_no.'/'.count($boxList)?></td>
                                        <td class="text-center"><?=$row->wt_pcs?></td>
                                        <td class="text-center"><?=$weight?></td>
                                    </tr>
                                    <?php
                                }
                            }
                            $packingDetail = json_decode($item[0]->packing_detail);$detail = "<br>";
                            foreach($packingDetail AS $pd){
                                $weightPerBox += ($pd->pack_wt * $pd->std_qty);
                               
                                // $detail .= $pd->item_name.'<br> [Qty : '.$pd->std_qty.', Weight : '.$pd->pack_wt.'] <br>';
                            }
                            $totalWtBox += $weightPerBox;
                            $boxDetail []='(<b>Box No : '.$boxCount.' </b> wt. - '.$weightPerBox.' kg.)';
                            $boxCount++;
                        }
                        ?>
                    </tbody>
                    <tfoot>
                        <tr>
                            <th colspan="8" class="text-right">Total</th>
                            <th></th>
							<th><?=$toalQty?></th>
                            <th><?=count($boxList)?></th>
                            <th></th>
                            <th><?=$totalWeight?></th>
                        </tr>
                    </tfoot>
                </table>
                <table class="table item-list-bb">
                    <tr class="text-left">
                        <td rowspan="4" style="width:50%">
                            <b>Box Size :</b> <br>
                            <?=implode(",<br>",$boxDetail)?>
                        </td>
                        <th style="width:10%"> Port of Loading</th>
                        <td style="width:10%"><?=$dataRow->port_of_loading?></td>
                        <th style="width:10%">Total Box</th>
                        <td style="width:10%">
                            <?php
                            $maxPackageNo = 0;
                            if (!empty($dataRow->itemData)) {
                                $maxPackageNo = max(array_column($dataRow->itemData, 'package_no'));
                            }                            
                            echo floatval($maxPackageNo).' '.$dataRow->p_type.' ';                           
                            ?>
                        </td>
                        <td rowspan="4" style="width:10%;text-align:center;vertical-align:bottom">                           
                            <?=$dataRow->prepared_by?>
                            <br>
                            <b>Sign. :</b>
                        </td>
                    </tr>
                    <tr  class="text-left">
                        <th>Port of Discharge</th>
                        <td><?=$dataRow->port_of_discharge?></td>
                        <th>Total Weight</th>
                        <td><?=$totalWeight?></td>
                    </tr>
                    <tr class="text-left">
                        <th> Terms of Delivery</th>
                        <td><?=$dataRow->terms_of_delivery?></td>
                        <th>Total Gross Wt.</th>
                        <td><?=($totalWeight + $totalWtBox)?></td>
                    </tr>
                    <tr class="text-left">
                        <th>Fright Terms</th>
                        <td><?=$dataRow->fright_terms?></td>
                        <th>Total Qty.</th>
                        <td><?=$toalQty?></td>
                    </tr>
                </table>
            </div>
        </div>        
    </body>
</html>