<div class="row">
    <div class="col-12">
        <table class="table table-bordered">
            <tr>
                <th colspan="4" class="text-center" style="width:100%;">
                    <h1>EXPORT INVOICE</h1>
                    <p><?=($invData->gst_type == 3)?'"Supply Meant For Export under bond without Payment of IGST"':'"Supply Meant For Export under bond with Payment of IGST"'?></p>
                    <p><?=($invData->gst_type == 3)?'Goods Cleared Against LUT ARN No.AD240325049124F Dt: 22.03.2025':''?></p>
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
                    <th class="text-center">Rate/No.<br>IN <?=$invData->currency?></th>
                    <th class="text-center">Amount<br><?=$invData->currency?></th>
                </tr>
            </thead>
            <tbody>
                <?php
                    $i=1;$totalQty = $totalNetAmount = $totalNetWeight = $totalGrossWeight = 0;
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
                                $row->inv_price = round(($row->inv_price / $invData->inrrate),2);
                                $row->net_amount = round(($row->total_qty * $row->inv_price),2);

                                echo '<tr>';
                                    if($i == 1):
                                        echo '<td rowspan="'.$itemCount.'" class="text-center">Box No. '.$row->package_no.'</td>';
                                    endif;
                                    echo '<td class="text-center">'.$row->doc_no.'</td>';
                                    echo '<td class="text-center">'.$row->doc_date.'</td>';
                                    echo '<td class="text-center">'.$row->item_name.'</td>';
                                    echo '<td class="text-center">'.$row->hsn_code.'</td>';
                                    echo '<td class="text-center">'.floatval($row->total_qty).'</td>';
                                    echo '<td class="text-center">'.sprintf("%.2f",$row->inv_price).'</td>';
                                    echo '<td class="text-center">'.sprintf("%.2f",$row->net_amount).'</td>';
                                echo '</tr>';
                                $i++;
                                $totalQty += $row->total_qty;
                                $totalNetAmount += $row->net_amount; 
                            endforeach;

                            $totalNetWeight += $netWeight;
                            $totalGrossWeight += $grossWeight;
                            $maxPackageNo = $packageNumber;
                        endforeach;  

                        /* $maxPackageNo = max(array_column($invData->itemList,'package_no'));
                        $itemCount = count($invData->itemList); $packageNo = [];
                        foreach($invData->itemList as $row):
                            $row->inv_price = round(($row->inv_price / $invData->inrrate),2);
                            $row->net_amount = round(($row->total_qty * $row->inv_price),2);
                            echo '<tr>';
                                if($i == 1):
                                    $box = ($maxPackageNo == $row->package_no)?"Box ".$row->package_no:"Box ".$row->package_no." to ".$maxPackageNo;
                                    echo '<td rowspan="'.$itemCount.'" class="text-center">'.$box.'</td>';
                                endif;
                                echo '<td class="text-center">'.$row->doc_no.'</td>';
                                echo '<td class="text-center">'.$row->doc_date.'</td>';
                                echo '<td class="text-center">'.$row->item_name.'</td>';
                                echo '<td class="text-center">'.$row->hsn_code.'</td>';
                                echo '<td class="text-center">'.floatval($row->total_qty).'</td>';
                                echo '<td class="text-center">'.sprintf("%.2f",$row->inv_price).'</td>';
                                echo '<td class="text-center">'.sprintf("%.2f",$row->net_amount).'</td>';
                            echo '</tr>';

                            $i++;
                            
                            $itemWeight = round($row->total_qty * $row->wt_pcs,3);

                            $weightPerBox = 0;
                            if(!in_array($row->package_no,$packageNo)):
                                $packageNo[] = $row->package_no;
                                $packingDetail = json_decode($row->packing_detail);
                                $weightPerBox = array_reduce($packingDetail, function($carry, $pd) {
                                    return $carry + ($pd->pack_wt * $pd->std_qty);
                                }, 0);                                
                            endif;

                            $totalNetWeight += $itemWeight;
                            $totalGrossWeight += ($itemWeight + $weightPerBox);
                            $totalQty += $row->total_qty;
                            $totalNetAmount += $row->net_amount; 
                        endforeach; */
                    endif;
                    
                ?>
                <tr>
                    <th colspan="2">Net Weight :<br><?=$totalNetWeight?> KGS.</th>
                    <th>Gross Weight :<br><?=$totalGrossWeight?></th>
                    <th colspan="2">No. of Cases/Boxes :<br><?=$maxPackageNo?> Nos. Wooden Box</th>
                    <th class="text-center"><?=$totalQty?></th>
                    <th>CIF</th>
                    <th class="text-center"><?=$invData->currency." ".sprintf("%.2f",$totalNetAmount)?></th>
                </tr>
                <tr>
                    <th class="text-left" colspan="2">Total Value in Wards : </th>
                    <th class="text-left" colspan="6"><?=$invData->currency." ".numToWordEnglish(sprintf('%.2f',$totalNetAmount))?></th>
                </tr>
                <tr>
                    <th class="text-left" colspan="8" style="border-bottom:none;">
                        STATEMENT ON ORIGIN
                    </th>
                </tr>
                <tr>
                    <td colspan="8" style="border-top:none;border-bottom:none;">
                        <p>We hereby declares that, these products are of Preferential origin according to rules of origin of the Generalized System of Preferences of the European Union and that the of the European Union and that the origin criterion met is “P”origin of the Generalized System of Preferences.</p>
                        <p>WE HEREBY CERTIFY THAT THE GOODS ARE AS PER MERCHANDISE DESCRIPTION AND ARE OF INDIAN ORIGIN</p>
                    </td>
                </tr>
                <tr>
                    <td colspan="5" style="border-right:none;border-bottom:none;border-top:none;">
                        <h4>REX number: INREX2413010165EC006</h4>
                    </td>
                    <td class="text-bottom text-center" rowspan="2" colspan="3" style="border-left:none;border-top:none;border-bottom:none;vertical-align: bottom;"><b>Signature & Date: <?=formatDate($invData->trans_date)?></b></td>
                </tr>
                <tr>
                    <td colspan="5" style="border-right:none;border-top:none;border-bottom:none;">
                        <h4>DECLARATION :</h4>
                        <p>We hereby declare this invoice shows the actual price of the goods described and that are true and Correct.</p>
                        <br>
                        <h4>CIF Value in Rs.</h4>
                        <h4>FOB Value in Rs.</h4>
                    </td>
                </tr>
                <tr>
                    <td class="text-center" colspan="8" style="border-top:none; font-size:0.68rem;">
                        <p>Bank:JP Morgan Chase Bank, Frankfurt, A/c 623-16-02308 Swift: CHASDEFX IBAN:DE26501108006231602308 Further Credit to HDFC Bank Ltd A/c No.50200009870870 Swift:HDFCINBBXXX,</p>
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