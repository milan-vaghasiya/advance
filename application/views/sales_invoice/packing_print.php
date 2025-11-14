<html>
    <head>
        <title>Packing Print</title>
    </head>
    <body>
        <table class="table item-list-bb">
            <thead>
                <tr>
                    <th>Size</th>
                    <th>Weight/Pcs.</th>
                    <th>Qty</th>
                    <th>Total Weight</th>
                    <th>Box</th>
                </tr>
            </thead>
            <tbody>
                <?php
                    $totalQty = $totalWeight = $totalBox = 0;
                    foreach($postData['itemData'] as $row):
                        $row = (object) $row;
                        echo '<tr>
                            <td class="text-center">
                                '.$row->size.'
                            </td>
                            <td class="text-right">
                                '.$row->weight.'
                            </td>
                            <td class="text-right">
                                '.floatVal($row->qty).'
                            </td>
                            <td class="text-right">
                                '.$row->total_weight.'
                            </td>
                            <td class="text-right">
                                '.$row->box.'
                            </td>
                        </tr>';
                        $totalQty += $row->qty;
                        $totalWeight += $row->total_weight; 
                        $totalBox += $row->box;
                    endforeach;

                    foreach($postData['otherItem'] as $row):
                        $row = (object) $row;
                        if(!empty($row->item_name)):
                            echo '<tr>
                                <td class="text-center">
                                    '.$row->item_name.'
                                </td>
                                <td>
                                    
                                </td>
                                <td>
                                    
                                </td>
                                <td class="text-right">
                                    '.$row->weight.'
                                </td>
                                <td>
                                    
                                </td>
                            </tr>';

                            $totalWeight += $row->weight; 
                        endif;
                    endforeach;
                ?>
            </tbody>
            <tfoot>
                <tr>
                    <th colspan="2">Total</th>
                    <th class="text-right"><?=$totalQty?></th>
                    <th class="text-right"><?=$totalWeight?></th>
                    <th class="text-right"><?=$totalBox?></th>
                </tr>
                <?php if(!empty($postData['cc_attached'])): ?>
                <tr>
                    <th colspan="5" class="text-center">
                        <?=$postData['cc_attached']?>
                    </th>
                </tr>
                <?php endif; ?>
            </tfoot>
        </table>
    </body>
</html>