
<div class="row">
	<div class="col-12">
		<table class="table item-list-bb" style="margin-top:2px;">
			<tr class="text-left">
                <th width="15%">Part Name</th>
				<td width="30%"><?=(!empty($popData->fg_item_name)) ? $popData->fg_item_name : ""?></td>
                <th style="width:20%">Customer Name</th>
				<td width="35%"><?=(!empty($popData->party_name)) ? $popData->party_name : ""?></td>
			</tr>
			<tr class="text-left">
				<th>Die No.</th>
				<td><?=$popData->category_code.$popData->fg_item_code.'-'.$popData->set_no.$popData->sr_no.'/'.lpad($popData->recut_no,2)?></td>
				<th>POP No & Date</th>
				<td><?=(!empty($popData->trans_number)) ? $popData->trans_number." & ".((!empty($popData->report_date)) ?formatDate($popData->report_date):"") : ""?></td>
			</tr>
			<tr class="text-left">
				<th>Cust. Drawing No.</th>
                <td><?=(!empty($itmRev->drawing_no)) ? $itmRev->drawing_no : ""?></td>
				<th>Rev. No</th>                
				<td ><?=(!empty($itmRev->rev_no)) ? $itmRev->rev_no : ""?></td>
			</tr>
			<tr class="text-left">
				<th>Ref. Die</th>
                <td><?=$popData->ref_cat_code.$popData->ref_fg_code.'-'.$popData->ref_set_no.$popData->ref_sr_no.'/'.lpad($popData->ref_recut_no,2)?></td>
				<th>Mfg No & Date</th>
                <td><?=(!empty($popData->prod_number)) ? $popData->prod_number." & ".((!empty($popData->prod_date)) ?formatDate($popData->prod_date):"") : ""?></td>
			</tr>	
		</table>
		
		<table class="table item-list-bb" style="margin-top:10px;">
			<thead class="thead-info" id="theadData">
                <tr style="text-align:center;">
                    <th rowspan="2">#</th>
                    <th rowspan="2">Parameter</th>
                    <th rowspan="2">Specification</th>
                    <th rowspan="2">Instrument</th>
                    <th colspan="2">Tolerance</th>
                    <th rowspan="2">Expected Value</th>
                    <th rowspan="2">Observation</th>
                    <th rowspan="2">Decision <br> (Ok / Not Ok)</th>
                </tr>
                <tr style="text-align:center;">
                    <th style="width:10%">Min</th>
                    <th style="width:10%">Max</th>
                </tr>
            </thead>
            <tbody id="tbodyData">
                <?php
                $i=1;
                if(!empty($paramData)):
                    foreach($paramData as $row):
                        $obj = new StdClass;$result = new StdClass;
                        if(!empty($popData)):
                            $obj = json_decode($popData->observation); 
                        endif;
                        if(!empty($popData)):
                            $result = json_decode($popData->result); 
                        endif;
                        // $lsl = floatVal($row->specification) - $row->min;
                        // $usl = floatVal($row->specification) + $row->max;
                        $expValue = floatVal($row->specification) * 1.015;
                        echo '<tr class="text-center">
                                <td>'.$i++.'</td>
                                <td>'.$row->parameter.'</td>
                                <td>'.$row->specification.'</td>
                                <td>'.$row->instrument.'</td>
                                <td>'.$row->min.'</td>
                                <td>'.$row->max.'</td>
                                <td>'.$expValue.'</td>
                                <td>
                                    '.$obj->{$row->id}[0].'
                                </td>
                                <td>
                                   '.$result->{$row->id}[0].'
                                </td>
                            </tr>';
                    endforeach;
                else:
                    echo '<td class="text-center" colspan="11">No data available.</td>';
                endif;
                ?>
            </tbody>
		</table>
	</div>
</div>