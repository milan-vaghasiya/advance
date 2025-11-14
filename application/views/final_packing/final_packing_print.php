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
                    <thead>
                        <tr class="bg-light">
                            <th>Sr  No.</th>
                            <th>OA. NO.</th>
                            <th>Customer PO No.</th>
                            <th>PO Date</th>
                            <th>Part No</th>
                            <th>Part Name</th>
                            <th>Material Grade</th>
                            <th>Batch No.</th>
                            <th>Qty Dispatch</th>
                            <th>Qty / Box</th>
                            <th>No. Of Box</th>
                            <th>Wt/Piece in kg</th>
                            <th>Total Weight in kg</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php
                        $i=1; $totalWeight=0; $toalQty=0; $totalWtBox=0; $totalBox=0; 

                        // Group data by item_id and total_qty
                        $boxList = array_reduce($dataRow->itemData, function($boxList, $item) {
                            $key = $item->item_id . '-' . $item->total_qty;
                            $boxList[$key][] = $item;
                            return $boxList;
                        }, []);

                        $boxCount = 1; $boxDetail = []; $weightPerBox = 0;
                        foreach($boxList AS $boxKey=>$item){
                            $row = new stdClass(); 
                            $row = $item[0];

                            $qty = (!empty($row->total_qty) ? array_sum(array_column($item,'total_qty')) : 0);
                            $box = (!empty($row->package_no) ? count(array_column($item, 'package_no')) : '');
                            $weight = $qty * $row->wt_pcs;
                            $totalWeight += $weight;
                            // $weightPerBox += $weight;
                            $toalQty += $qty;
                            $totalBox += $box;
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
                                <td class="text-center"><?=floatval($qty)?></td>
                                <td class="text-center"><?=(!empty($row->total_qty) ? floatval($row->total_qty) : 0)?></td>
                                <td class="text-center"><?=floatval($box)?></td>
                                <td class="text-center"><?=$row->wt_pcs?></td>
                                <td class="text-center"><?=floatval($weight)?></td>
                            </tr>
                            <?php
                            
                            $packingDetail = json_decode($item[0]->packing_detail);$detail = "";
                            foreach($packingDetail AS $pd){
                                $pack_qty = ($pd->std_qty * $box);
                                $pack_wt = ($pd->pack_wt * $box);

                                $weightPerBox += $pack_wt;

                                // $detail .= $pd->item_name.' [Qty : '.$pack_qty.', Weight : '.$pack_wt.'] ';
                            }
                            $totalWtBox += $weightPerBox;
                            $boxDetail []='(<b>Box No : '.$boxCount.' </b>wt. - '.$weightPerBox.' kg.)';
                            $boxCount++;
                        }
                        ?>
                    </tbody>
                    <tfoot>
                        <tr>
                            <th colspan="8" class="text-right">Total</th>
                            <th><?=floatval($toalQty)?></th>
                            <th></th>
                            <th><?=floatval($totalBox)?></th>
                            <th></th>
                            <th><?=floatval($totalWeight)?></th>
                        </tr>
                    </tfoot>
                </table>
                <table class="table item-list-bb">
                    <tr class="text-left">
                        <td rowspan="4" style="width:50%">
                            Box Size : <br>
                            <?=implode(",",$boxDetail)?>
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