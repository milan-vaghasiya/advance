<html>
    <head>
        <title>Delivery Challan</title>
        <!-- Favicon icon -->
        <link rel="icon" type="image/png" sizes="16x16" href="<?=base_url();?>assets/images/favicon.png">
    </head>
    <body>
        <div class="row">
            <div class="col-12">
                <table>
                    <tr>
                        <td>
                            <?php if(!empty($letter_head)): ?>
                                <img src="<?=$letter_head?>" class="img">
                            <?php endif;?>
                        </td>
                    </tr>
                </table>

               
                
                <table class="table item-list-bb fs-22" style="margin-top:5px;">
                    <tr>
                        <th colspan="3" class="text-center" style="font-size:0.8rem;border:0px"> PACKING LIST </th>
                    </tr>
                    <tr >
                        <td rowspan="4" style="width:67%;vertical-align:top;">
                            <b>M/S. <?=$challanData->party_name?></b><br>
                            <?=(!empty($challanData->delivery_address) ? $challanData->delivery_address : '')?><br>
                            <b>Kind. Attn. : <?=$partyData->contact_person?></b> <br>
                            Contact No. : <?=$partyData->party_mobile?><br>
                            Email : <?=$partyData->party_email?><br><br>
                            GSTIN : <?=$challanData->gstin?>
                        </td>
                        <td>
                            <b>DC. No.</b>
                        </td>
                        <td>
                            <?=$challanData->trans_number?>
                        </td>
                    </tr>
                    <tr>
				        <th class="text-left">DC Date</th>
                        <td><?=formatDate($challanData->trans_date)?></td>
                    </tr>
                    <tr>
                        <th class="text-left">Cust. PO. No.</th>
                        <td><?=$challanData->doc_no?></td>
                    </tr>
                    <tr>
                        <th class="text-left">Cust. PO. Date</th>
                        <td><?=(!empty($challanData->doc_date)) ? formatDate($challanData->doc_date) : ""?></td>
                    </tr>
                </table>
                
    
                <table class="table item-list-bb" style="margin-top:10px;">
                    
                    <thead>
                   
                        <tr>
                            <th class="text-center">Cartoon No</th>
                            <th class="text-left">Item Description</th>
                            <th class="text-center">Box Per Qty</th>
                            <th class="text-center">Total Box</th>
                            <th class="text-center">Total Qty</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php
                            $i=1;$totalQty = $totalBoxQty = 0;
                            if(!empty($annexData)):
                                $cartoonArray = array_reduce($annexData, function($cartoonArray, $log) { $cartoonArray[$log->cartoon_no][] = $log; return $cartoonArray; }, []);
                                foreach ($cartoonArray as $cartoon_no=>$logs):
                                        ?>
                                        <tr>
                                            <td rowspan="<?=count($logs)?>"  class="text-center"><?=$cartoon_no?></td>
                                            <td><?=$logs[0]->item_name?></td>
                                            <td  class="text-center"><?=$logs[0]->qty_per_box?></td>
                                            <td  class="text-center"><?=$logs[0]->box_qty?></td>
                                            <td  class="text-center"><?=$logs[0]->total_qty?></td>
                                        </tr>
                                        <?php
                                        $totalBoxQty += $logs[0]->box_qty;
                                        $totalQty += $logs[0]->total_qty;
                                        for($i =1; $i<count($logs);$i++): ?>
                                            <tr class="text-center">
                                                <td ><?=$logs[$i]->item_name?></td>
                                                <td  class="text-center"><?=$logs[$i]->qty_per_box?></td>
                                                <td  class="text-center"><?=$logs[$i]->box_qty?></td>
                                                <td  class="text-center"><?=$logs[$i]->total_qty?></td>
                                            </tr> <?php
                                            $totalBoxQty += $logs[$i]->box_qty;
                                            $totalQty += $logs[$i]->total_qty;
                                        endfor;
                                        ?>
                                        
                                        <?php
                                endforeach;
                                            
                            endif;
                        ?>                        
                    </tbody>  
                    <tfoot>
                        <tr>
                            <th colspan="3"  class="text-right">Total</th>
                            <th><?=$totalBoxQty?></th>
                            <th><?=$totalQty?></th>
                        </tr>
                    </tfoot>
                </table>
                        
            </div>
        </div>        
    </body>
</html>
