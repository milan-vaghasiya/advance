<?php
	$data = [];
	foreach($bomData as $row){
		$tmp = array();
		$tmp['id'] = $row->ref_item_id;
		$tmp['parent_id'] = $row->item_id;
		$tmp['text'] = $row->item_code.' - '.$row->item_name;
		$tmp['qty'] = $row->qty;
		$tmp['net_weight'] = $row->net_weight;
		$tmp['purchase_price'] = $row->purchase_price;
		$tmp['process_cost'] = $row->process_cost;
		$tmp['scrap_rate'] = (!empty($row->scrap_rate) ? $row->scrap_rate : 0); 
		$tmp['item_type'] = $row->item_type;
		array_push($data, $tmp);
	}
	$itemsByReference = array();
	
	foreach($data as $key => &$item) { $itemsByReference[$item['id']] = &$item; }

	foreach($data as $key => &$item) {
		if($item['parent_id'] && isset($itemsByReference[$item['parent_id']])) { $itemsByReference[$item['parent_id']]['nodes'][] = &$item; }
	}

	foreach($data as $key => &$item) {
		if($item['parent_id'] && isset($itemsByReference[$item['parent_id']])) { unset($data[$key]); }
	}

?>

<thead>
    <tr>
        <th>Level</th>
        <th>Item</th>
        <th>Qty</th>
        <th>Purchase Cost</th>
        <th>Process/Jobwork Cost</th>
        <th>Net Weight</th>
        <th>Gross Weight</th>
        <th>Scrap rate</th>
        <th>Amount</th>
    </tr>
</thead>
<tbody>
<?php
	$lvl = 1;$itmLevel = [];
	foreach($data as $row){
		$row = (object) $row;
		$lvl_str = '1.'.$lvl;
        $purchaseCost = 0;
        $process_cost = (!empty($row->process_cost))?$row->process_cost*$row->qty:0;
		$gross_weight="";$net_weight="";$scrap_rate="";$final_rate = 0;
		if($row->item_type == 3){
			$gross_weight = (!empty($row->qty))?$row->qty:0;
			$net_weight = (!empty($row->net_weight))?$row->net_weight:0;
			$scrap_rate = (!empty($row->scrap_rate))?$row->scrap_rate:0;
			$total_mt_rate = $gross_weight*$row->purchase_price;
			$scrap_rate = $scrap_rate * ($gross_weight - $net_weight);
			$final_rate = $total_mt_rate - $scrap_rate;
		}else{
			$purchaseCost = (!empty($row->purchase_price))?$row->purchase_price*$row->qty:0;
		}
        
        $amount = $purchaseCost + $process_cost + $final_rate;
		echo '<tr class="bg-light-sky">';
			echo '<td class="bg-light-sky">'.$lvl_str.'</td>';
			echo '<td class="text-left bg-light-sky">'.$row->text.'</td>';
			echo '<td class="bg-light-sky text-right">'.$row->qty.'</td>';
			echo '<td class="bg-light-sky text-right">'.$purchaseCost.'</td>';
			echo '<td class="bg-light-sky text-right">'.$process_cost.'</td>';
			echo '<td class="bg-light-sky text-right">'.$net_weight.'</td>';
			echo '<td class="bg-light-sky text-right">'.$gross_weight.'</td>';
			echo '<td class="bg-light-sky text-right">'.$scrap_rate.'</td>';
			echo '<td class="bg-light-sky text-right">'.$amount.'</td>';
		echo '</tr>';
		
		$itmLevel[$row->parent_id]['lvl'] = $lvl;
		$itmLevel[$row->parent_id]['lvl_str'] = $lvl_str;
		
		if (isset($row->nodes)) {
			$currentParent = $row->id;
			buildTreeForCost($row->nodes, $currentParent,$lvl_str,1,$itmLevel);
		}
		$lvl++;
    }
?>
</tbody>
<tfoot>
	<tr>
        <th></th>
        <th></th>
        <th></th>
        <th></th>
        <th></th>
        <th></th>
        <th> </th>
        <th></th>
        <th></th>
    </tr>
</tfoot>