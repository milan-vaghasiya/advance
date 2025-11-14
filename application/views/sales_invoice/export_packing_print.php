<div class="row">
    <div class="col-12">
        <table class="table table-bordered">
            <tr>
                <th colspan="4" class="text-center" style="width:100%;">
                    <h1>PACKING LIST</h1>
                </th>
            </tr>
            <tr>
                <td colspan="2" rowspan="2" class="text-left" style="width:50%;">
                    <b>Exporter</b><hr style="margin:5px 0px;">
                    <b><?=$companyData->company_name?></b><br>
                    <?=$companyData->company_address."<br>".$companyData->company_city_name.", ".$companyData->company_state_name." - ".$companyData->company_pincode.", ".$companyData->company_country_name?><br>
                    Mobile No. : <?=$companyData->company_phone?><br>
                    Contact Person : <?=$companyData->company_contact_person?>
                </td>
                <td style="width:25%;">Invoice No. <br> <b><?=$invData->trans_number?></b></td>
                <td style="width:25%;">Date <br> <b><?=formatDate($invData->trans_date,"d F Y")?></b></td>
            </tr>
            <tr>
                <td colspan="2" class="text-left" style="width:100%;">Other references : <b>See Below</b></td>
            </tr>
            <tr>
                <td colspan="2" class="text-left" style="width:50%;">Consignee</td>
                <td colspan="2" class="text-left" style="width:50%;">Buyer (If not Consignee)</td>
            </tr>
            <tr>
                <td colspan="2" class="text-left" style="width:50%;vertical-align: top;">
                    <b><?=$invData->party_name?></b><br>
                    <?=(!empty($partyData->party_address) ? $partyData->party_address : '')?>
                </td>
                <td colspan="2" class="text-left" style="width:50%;vertical-align: top;height:80px;">
                    <b>Same as Consignee</b>
                </td>
            </tr>
            <tr>
                <td class="text-left">
                    Metdod of Dispatch<br>
                    <b><?=$invData->method_of_dispatch?></b>
                </td>
                <td class="text-left">
                    Type of Shipment<br>
                    <b><?=$invData->type_of_shipment?></b>
                </td>
                <td class="text-left">
                    Country Of Origin<br>
                    <b><?=$invData->country_of_origin?></b>
                </td>
                <td class="text-left">
                    Country of Final Destination<br>
                    <b><?=$invData->country_of_fd?></b>
                </td>
            </tr>
            <tr>
                <td class="text-left">
                    Port of Loading<br>
                    <b><?=$invData->port_of_loading?></b>
                </td>
                <td class="text-left">
                    Date of Departure<br>
                    <b><?=(!empty($invData->date_of_departure))?formatDate($invData->date_of_departure):""?></b>
                </td>
                <td colspan="2" rowspan="2" class="text-left" style="vertical-align: top;">
                    Terms of Delivery & Payment<br>
                    <b><?=$invData->terms_of_delivery?></b>
                </td>
            </tr>
            <tr>
                <td class="text-left">
                    Port of Discharge<br>
                    <b><?=$invData->port_of_discharge?></b>
                </td>
                <td class="text-left">
                    Final Destination<br>
                    <b><?=$invData->final_destination?></b>
                </td>
            </tr>
        </table>

        <table class="table table-bordered">
            <thead>
                <tr>
                    <th class="text-center">Marks & Nos.</th>
                    <th class="text-center">PO. No.</th>
                    <th class="text-center">Part No.</th>
                    <th class="text-center">Part Description</th>
                    <th class="text-center">HSN Code</th>
                    <th class="text-center">Quantity<br>In Nos</th>
                    <th class="text-center">Net Wt.<br>In Kgs.</th>
                    <th class="text-center">Gross Wt.<br>In Kgs.</th>
                </tr>
            </thead>
            <tbody>
                <?php
                    $i=1;$totalQty = $totalBox = $totalDiscAmt = $totalNetAmount = $totalPallets = $totalNetWeight = $totalGrossWeight = 0;
                    if(!empty($invData->itemList)):
                        $groupedBox = array_reduce($invData->itemList, function($itemData, $row) {
                            $itemData[$row->package_no][] = $row;
                            return $itemData;
                        }, []);

                        $packageNo = [];$maxPackageNo = '';
                        $totalQty = $totalNetWeight = $totalGrossWeight = 0;
                        foreach($groupedBox as $packageNumber => $items):
                            $netWeight = array_sum(array_map(fn($rowItem) => $rowItem->total_qty * $rowItem->wt_pcs, $items));
                            $packingDetail = json_decode($items[0]->packing_detail);
                            $boxWeight = array_reduce($packingDetail, function($carry, $pd) {
                                return $carry + ($pd->pack_wt * $pd->std_qty);
                            }, 0);
                            $netWeight = round($netWeight,2);
                            $grossWeight = round(($netWeight + $boxWeight),2);

                            $itemCount = count($items);
                            $i = 1;
                            foreach($items as $row):
                                echo '<tr>';
                                    if($i == 1):
                                        echo '<td rowspan="'.$itemCount.'" class="text-center">Box No. '.$row->package_no.'</td>';
                                    endif;
                                    echo '<td class="text-center">'.$row->doc_no.'</td>';
                                    echo '<td class="text-center">'.$row->doc_date.'</td>';
                                    echo '<td class="text-center">'.$row->item_name.'</td>';
                                    echo '<td class="text-center">'.$row->hsn_code.'</td>';
                                    echo '<td class="text-center">'.floatval($row->total_qty).'</td>';
                                    if($i == 1):
                                        echo '<td rowspan="'.$itemCount.'" class="text-center">'.sprintf("%.2f",$netWeight).'</td>';
                                        echo '<td rowspan="'.$itemCount.'" class="text-center">'.sprintf("%.2f",$grossWeight).'</td>';
                                    endif;
                                echo '</tr>';
                                $i++;
                                $totalQty += $row->total_qty;
                            endforeach;

                            $totalNetWeight += $netWeight;
                            $totalGrossWeight += $grossWeight;
                            $maxPackageNo = $packageNumber;
                        endforeach;                       
                    endif;
                ?>
                <tr>
                    <th colspan="2">Net Weight :<br><?=$totalNetWeight?> KGS.</th>
                    <th>Gross Weight :<br><?=$totalGrossWeight?></th>
                    <th colspan="2">No. of Cases/Boxes :<br><?=$maxPackageNo?> Nos. Wooden Box</th>
                    <th class="text-center"><?=$totalQty?></th>
                    <th><?=$totalNetWeight?></th>
                    <th class="text-center"><?=$totalGrossWeight?></th>
                </tr>
                <tr>
                    <td colspan="5" style="border-right:none;height:100px;">

                    </td>
                    <td class="text-bottom text-left" colspan="3" style="border-left:none;vertical-align: bottom;">
                        <b>Signature & Date: <?=formatDate($invData->trans_date)?></b>
                    </td>
                </tr>                
            </tbody>
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
    </div>
</div>