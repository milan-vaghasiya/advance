<table class="table item-list-bb" style="margin-top:5px">
    <tr>
        <th class="bg-light text-left">Item Name</th>
        <?php $itemCode = (!empty($dataRow->item_code) ? $dataRow->item_code : ''); ?>
        <td colspan="3"><?=(!empty($dataRow->item_name) ? $itemCode.$dataRow->item_name : '')?></td>
        <th class="bg-light text-left">TC Date</th>
        <td><?=(!empty($dataRow->created_at) ? formatDate($dataRow->created_at) : '')?></td>
    </tr>
    <tr>
        <th style="width:18%" class="bg-light text-left">Material Grade</th>
        <td style="width:16%"><?=(!empty($dataRow->material_grade) ? $dataRow->material_grade : '')?></td>
        <th style="width:17%" class="bg-light text-left">GRN No.</th>
        <td style="width:16%"><?=(!empty($dataRow->trans_number) ? $dataRow->trans_number : '')?></td>
        <th style="width:17%" class="bg-light text-left">Ref./Heat No.</th>
        <td style="width:16%"><?=(!empty($dataRow->heat_no) ? $dataRow->heat_no : '')?></td>
    </tr>
    <tr>
        <th class="bg-light text-left">Instrument</th>
        <td><?=(!empty($dataRow->instrument_name) ? $dataRow->instrument_name : '')?></td>
        <th class="bg-light text-left">Batch Qty.</th>
        <td><?=(!empty($dataRow->sample_qty) ? floatval($dataRow->sample_qty) : '')?></td>
        <th style="width:17%" class="bg-light text-left">Batch No.</th>
        <td style="width:16%"><?=(!empty($dataRow->batch_no) ? $dataRow->batch_no : '')?></td>
    </tr>
    <tr>
        <th class="bg-light text-left">TC No.</th>
        <td><?=(!empty($dataRow->test_report_no) ? $dataRow->test_report_no : '')?></td>
        <th class="bg-light text-left">Cal. Date & Due Date</th>
        <?php
            $calDate = (!empty($dataRow->last_cal_date) ? formatDate($dataRow->last_cal_date) : '');
            $dueDate = (!empty($dataRow->next_cal_date) ? formatDate($dataRow->next_cal_date) : '');
        ?>
        <td><?=$calDate.((!empty($calDate) && !empty($dueDate)) ? ' & ' : '').$dueDate?></td>
        <th class="bg-light text-left">Testing Location</th>
        <td><?=(!empty($dataRow->name_of_agency) ? $dataRow->name_of_agency : '')?></td>
    </tr>
	<tr>
        <th class="bg-light text-left">Finish Goods</th>
        <td colspan="5"><?=(!empty($dataRow->fg_item_code) ? '[ '.$dataRow->fg_item_code.' ] ' : '').(!empty($dataRow->fg_item_name) ? $dataRow->fg_item_name : '')?></td>
    </tr>
</table>
<?php
 $tc = [];$html='';
 if(!empty($tcData)){
     foreach($tcData as $row){
         $tc[$row->head_id]=$row;
     }
 } 
 $tcHeads = array_reduce($tcHeadList, function($tcHeads, $head) { $tcHeads[$head->test_name][] = $head; return $tcHeads; }, []);
 foreach ($tcHeads as $head_name => $heads):
    $jsonData = new stdClass();$tcMaster = [];
    $id="";
    if(!empty($tc[$heads[0]->test_type])){
        $jsonData = json_decode($tc[$heads[0]->test_type]->parameter);
        $id = $tc[$heads[0]->test_type]->id;
    
        if(!empty($heads[0]->parameter)){
            $tcMaster = json_decode($heads[0]->parameter);
        }        
        $thead=''; $tbody=''; $headValue=''; $headLable=''; $headCount=1;      
        foreach ($heads as $row):          
            
            $minReq = (!empty($tcMaster->{str_replace(" ","",$row->insp_param)}->min)?$tcMaster->{str_replace(" ","",$row->insp_param)}->min:'');
            $maxReq = (!empty($tcMaster->{str_replace(" ","",$row->insp_param)}->max)?$tcMaster->{str_replace(" ","",$row->insp_param)}->max:'');
            $otherReq = (!empty($tcMaster->{str_replace(" ","",$row->insp_param)}->other)?$tcMaster->{str_replace(" ","",$row->insp_param)}->other:'');

            if($row->requirement == 1){ $headValue = (!empty($tcMaster->{str_replace(" ","",$row->insp_param)}->min)?$tcMaster->{str_replace(" ","",$row->insp_param)}->min.'-':'').(!empty($tcMaster->{str_replace(" ","",$row->insp_param)}->max)?$tcMaster->{str_replace(" ","",$row->insp_param)}->max:''); 
            $headLable = (!empty($tcMaster->{str_replace(" ","",$row->insp_param)}->min)?'Min-':'').(!empty($tcMaster->{str_replace(" ","",$row->insp_param)}->max)?'Max':'');}
            elseif($row->requirement == 2){$headValue = $minReq; $headLable = 'Min';}
            elseif($row->requirement == 3){$headValue = $maxReq; $headLable = 'Max';}
            elseif($row->requirement == 4){$headValue = $otherReq; $headLable = 'Other';}

            if(!empty($jsonData->{str_replace(" ","",$row->insp_param)}->result)):
                $thead .= '<th  class="text-center" style="width:11%;font-size:0.8rem">'.$row->insp_param.''.(!empty($headLable) ? '<hr style="margin:0px;border-top:1px solid #123455;">( '.$headLable.' )' : '').'<br>'.$headValue.' <input type="hidden" name="param['.$row->test_type.']['.$row->param_id.'][param]" value="'.$row->insp_param.'"></th>';
                
                $tbody .= '<td class="text-center"  style="font-size:0.8rem">'.(!empty($jsonData->{str_replace(" ","",$row->insp_param)}->result)?$jsonData->{str_replace(" ","",$row->insp_param)}->result:'').'</td>';  

                $headCount++;
            endif;
        endforeach;

        $html .= ' <table class="table item-list-bb" style="margin-top:10px">
			<thead>
				<tr class="bg-light"><th colspan="'.($headCount).'" style="font-size:0.9rem">'.$head_name.'</th></tr>
				<tr class="bg-light" ><th class="text-left" style="width:10%;font-size:0.8rem">Element</th>'.$thead.'</tr>
			</thead>
			<tbody>
				<tr><th class="bg-light text-left" style="width:10%;font-size:0.8rem">Actual</th>'.$tbody.'</tr>
			</tbody>
		</table>';
    }
 endforeach;

 echo $html;
?>