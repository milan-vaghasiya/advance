<div class="org_title text-center" style="font-size:1.2rem;"><u>Inspection Certificate</u><br><small>as per EN 10204 : 3.1</div>
<table class="table item-list-bb" style="margin-top:2px">
    <thead class="text-left">
        <tr>
            <th rowspan="2">Customer</th>
            <td rowspan="2"><?=$tcItems->party_name?></td>
            <th class="text-left">TC No</th>
            <td><?="IC/".$this->shortYear.'/'.$tcItems->trans_no?></td>
            <th class="text-left">TC Date</th>
            <td><?=formatDate($tcItems->trans_date)?></td>
        </tr>
        <tr>
            <th  class=" text-left">DC No</th>
            <td><?=$tcItems->trans_number?></td>
            <th  class=" text-left">DC Date</th>
            <td><?=formatDate($tcItems->trans_date)?></td>
        </tr>
        <tr>
            <th>Part Name</th>
            <td><?=$tcItems->item_name?></td>
            <th  class=" text-left">PO No.</th>
            <td><?=$tcItems->doc_no?></td>
            <th >PO Date.</th>
            <td><?=formatDate($tcItems->doc_date)?></td>
        </tr>
        <tr>
            <th  class=" text-left">Part/Drg No :</th>
            <td><?=$tcItems->drg_no?></td>
            <th colspan="2">Process Route & Supply Condition</th>
            <td colspan="2"><?=$tcItems->mfg_type?></td>
        </tr>
        <tr>
            <th>Qty Supplied</th>
            <td><?=$qty?></td>
            <th colspan="2">Heat Treatment</th>
            <td colspan="2"><?=$tcItems->heat_treatment?></td>
        </tr>
        <tr>
            <th> Material Specifications</th>
            <td><?=$tcItems->material_grade?></td>
            <th colspan="2">Surface Treatment/Platting</th>
            <td colspan="2"><?=$tcItems->surface?></td>
        </tr>
        
    </thead>
</table>

<?php
$tcHeads = array_reduce($tcHeadList, function($tcHeads, $head) { $tcHeads[$head->test_name] = $head; return $tcHeads; }, []);
foreach($tcHeadList As $row){
    $masterParam = (array)json_decode($row->parameter);
    $countHead = 0;$thead = '';$tbodyMinTr = "";$tbodyMaxTr = "";$tbodyResultTr = "";$otherTr = "";
    if($row->test_name != 'Mill TC' AND in_array($row->test_name,array_column($reportList,'head_name')))
    {    
        $flagOther = false; $flagMinMax = false;
        foreach($masterParam AS $key=>$param){
            if((!empty($param->min) && $param->min != '-') || (!empty($param->max) && $param->max != '-') || (!empty($param->other) && $param->other != '-')){
                $flagOther = (!empty($param->other) ? true : false);
				$flagMinMax = ((!empty($param->min) || !empty($param->max)) ? true : false);
                $thead .= '<th class=" text-center">'.$param->param.'</th>';
                $tbodyMinTr .= '<td class=" text-center">'.(!empty($param->min)?$param->min:'-').'</td>';
                $tbodyMaxTr .= '<td class=" text-center">'.(!empty($param->max)?$param->max:'-').'</td>';
                $otherTr .= '<td class=" text-center">'.(!empty($param->other)?$param->other:'-').'</td>';
                $countHead ++;
            }else{
                unset($masterParam[$key]);
            }
            
        }
        
        if($countHead > 0){ ?>
            <table class="table item-list-bb" style="margin-top:0px">
                <thead>
                    <tr>
                        <th class="bg-light" colspan="<?=$countHead+1?>"><?=$row->head_name?></th>
                    </tr>
                    <tr>
                        <th class="bg-light text-left" style="width:15%">Test Specification</th>
                        <?= $thead?> 
                    </tr>
					<?php if($flagMinMax){ ?>
                    <tr>
                        <th class="bg-light text-left" style="width:15%">Minimum</th>
                        <?=$tbodyMinTr?>
                    </tr>
                    <tr>
                        <th class="bg-light text-left" style="width:15%">Maximum</th>
                        <?=$tbodyMaxTr?>
                    </tr>
                    <?php } ?>
                    <?php if($flagOther){ ?>
                    <tr>
                        <th class="bg-light text-left" style="width:15%">Other</th>
                        <?= $otherTr;?>
                    </tr>
                    <?php } ?>
                </thead>
                <tbody>
                    <?php
                    if(!empty($reportList)){
                        foreach($reportList As $report){
                            if($report->test_type == $row->test_type){
                                $paramArray = (array)json_decode($report->parameter);
                                $tbodyResultTr = "";
                                foreach($masterParam AS $param){
                                    $tbodyResultTr .= '<td class=" text-center">
                                        '.(!empty($paramArray[str_replace(" ","",$param->param)]->result)?$paramArray[str_replace(" ","",$param->param)]->result:'-').
                                    '</td>';
                                }
                    ?>
                                <tr>
									<td>Out Test (<?=$report->batch_no?> <?= (!empty($report->ht_batch) ? ' - '.$report->ht_batch : "");?>)</td>
                                    <?=$tbodyResultTr?>
                                </tr>
                    <?php
                            }
                        }
                    }
                    ?>
                </tbody>
            </table>
            <?php
        }    
    } 
}
?>
<table class="table item-list-bb" style="margin-top:0px">
	<thead>
		<tr>
			<th class="bg-light" colspan="5">Inclusion Rating (ASTM E45/IS4163)</th>
		</tr>
		<tr>
			<th class="bg-light text-left" style="width:15%">Macro Test</th>
			<th>A</th> 
			<th>B</th> 
			<th>C</th> 
			<th>D</th> 
		</tr>
		<tr>
			<th class="bg-light text-left" style="width:15%">Thin</th>
			<th>-</th> 
			<th>-</th> 
			<th>-</th> 
			<th>-</th> 
		</tr>
		<tr>
			<th class="bg-light text-left" style="width:15%">Thik</th>
			<th>-</th> 
			<th>-</th> 
			<th>-</th> 
			<th>-</th> 
		</tr>
	</thead>
</table>

<table class="table item-list-bb" style="border:1px solid">
    <tr>
        <td colspan="3">
            <p><b>Forging Process:</b> <?=(!empty($tcItems->forging_prc) ? $tcItems->forging_prc : "Closed Die Forging.")?></p>
            <p><b>Dimensional Inspection:</b> <?=(!empty($tcItems->dimensional_insp) ? $tcItems->dimensional_insp : "Accepeted As per Respective Component Drawing.")?></p>
            <p><b>Visual Inspection: </b> <?=(!empty($tcItems->visual_insp) ? $tcItems->visual_insp : "100% Checked And Found Satisfactory.")?></p>
            <p><b> Note:</b> <?=(!empty($tcItems->note) ? $tcItems->note : "All Forging Are Free From Radio Active Contamination")?></p>
        </td>
    </tr>
    <tr>
        <td colspan="3">
            <?=(!empty($tcItems->remark) ? $tcItems->remark : "We hereby certify that items mentioned above have been inspected in our presence and are found to be in accordance with the drawing as satisfy the requirement of the specification.")?>
        </td>
    </tr>
    <tr>
        <th>Special Requirement:</th>
        <td rowspan="4" style="vertical-align: bottom;text-align: center;"><b>Prepared By:</b></td>
        <td rowspan="4" style="vertical-align: bottom;text-align: center;"><b>Approved By:</b></td>
    </tr>
    <tr>
        <td height="18"><?=(!empty($tcItems->special_req) ? $tcItems->special_req : "")?></td>
    </tr>
    <tr>
        <td height="18"></td>
    </tr>
    <tr>
        <td height="18"></td>
    </tr>
    
</table>