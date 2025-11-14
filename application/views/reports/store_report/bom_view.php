
<?php
 
    //$mainItmNm = [];
    //$bomArray = array_reduce($bomData, function($kitItems, $item) { $kitItems[$item->item_id][] = $item; return $kitItems; }, []);
    //$mainItmNm = array_reduce($bomData, function($kitItems, $item) { $kitItems[$item->ref_item_id] = $item->item_code.' '.$item->item_name; return $kitItems; }, []);
	
	$data = [];
	foreach($bomData as $row){
		$tmp = array();
		$tmp['id'] = $row->ref_item_id;
		$tmp['parent_id'] = $row->item_id;
		$tmp['text'] = $row->item_code.' - '.$row->item_name;
		$tmp['qty'] = $row->qty;
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
    </tr>
</thead>
<tbody>
<?php
	$lvl = 1;$itmLevel = [];
	foreach($data as $row){
		$row = (object) $row;
		$lvl_str = '1.'.$lvl;
		echo '<tr class="bg-light-sky">';
			echo '<td class="bg-light-sky">'.$lvl_str.'</td>';
			echo '<td class="text-left bg-light-sky">'.$row->text.'</td>';
			echo '<td class="bg-light-sky text-right">'.$row->qty.'</td>';
		echo '</tr>';
		
		$itmLevel[$row->parent_id]['lvl'] = $lvl;
		$itmLevel[$row->parent_id]['lvl_str'] = $lvl_str;
		
		if (isset($row->nodes)) {
			$currentParent = $row->id;
			buildTree($row->nodes, $currentParent,$lvl_str,1,$itmLevel);
		}
		$lvl++;
    }
?>
</tbody>