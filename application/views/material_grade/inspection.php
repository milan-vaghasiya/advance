<form>
    <div class="col-md-12">
        <div class="error generalError"></div>
        <div class="row">

            <input type="hidden" name="grade_id" grade_="grade_id" value="<?=(!empty($dataRow->id))?$dataRow->id:""; ?>" />
			<input type="hidden" name="approve_by" id="approve_by" value="<?=(!empty($approve_by))?$approve_by:(!empty($dataRow->approve_by)?$dataRow->approve_by:0); ?>" />
            <?php
                $tc = [];
                if(!empty($tcData)){
                    foreach($tcData as $row){
                        $tc[$row->test_type]=$row;
                    }
                }
                
                $tcHeads = array_reduce($tcHeadList, function($tcHeads, $head) { $tcHeads[$head->test_name][] = $head; return $tcHeads; }, []);
                foreach ($tcHeads as $head_name => $heads):
                    $jsonData = new stdClass();
                    $id="";$insp_type = "";
                    if(!empty($tc[$heads[0]->test_type])){
                        $jsonData = json_decode($tc[$heads[0]->test_type]->parameter);
                        $id = $tc[$heads[0]->test_type]->id;
                        $ins_type = $tc[$heads[0]->test_type]->ins_type;
                    }
                    ?>
                    <div class="col-md-6"><h6><?=$heads[0]->head_name?> [ <?=$head_name?> ] :</h6></div>
                    <div class="col-md-6 float-end">
                        <div class="col-md-4 form-group float-end">
                            <label for="ins_type">Inspection Type</label>
                            <select name="ins_type[]" class="form-control float-end">
                                <option value="">NA</option>
                                <option value="GRN" <?=(!empty($ins_type) && $ins_type == 'GRN')?'selected':''?>>GRN</option>
                                <option value="FIR" <?=(!empty($ins_type) && $ins_type == 'FIR')?'selected':''?>>FIR</option>
                            </select>
                        </div>
                    </div>
                    <input type="hidden" name="test_type[]"  value="<?=$heads[0]->test_type?>">
                    <input type="hidden" name="id[]"  value="<?=$id?>">
                    <?php
                    $thead = '';$tbody = '';
                    
                    foreach ($heads as $row):
                        $colspan="";$placeholder = "";$cls ='floatOnly';$nm='';
                        if($row->requirement == 1){ $colspan = 2; $placeholder='Min - Max';}
                        if($row->requirement == 2){$placeholder = "Min";$nm='min';}
                        elseif($row->requirement == 3){$placeholder = "Max";$nm='max';}
                        elseif($row->requirement == 4){$placeholder = "";$cls="";$nm='other';}
                        $thead .='<th colspan="'.$colspan.'" class="text-center">'.((count($heads) > 1) ? $row->parameter : $row->test_name).' <br>'.(($row->requirement == 4) ? '' : '( '.$placeholder.' )').'<input type="hidden" name="param['.$row->test_type.']['.$row->id.'][param]" value="'.$row->parameter.'"></th>';
                        if($row->requirement == 1){
                            
                             $tbody .= '<td style="min-width:100px;"><input type="text" class="form-control text-center" name="param['.$row->test_type.']['.$row->id.'][min]"  placeholder="Min" value="'.(!empty($jsonData->{str_replace(" ","",$row->parameter)}->min)?$jsonData->{str_replace(" ","",$row->parameter)}->min:'').'"></td>';
                             $tbody .= '<td style="min-width:100px;"><input type="text" class="form-control text-center" name="param['.$row->test_type.']['.$row->id.'][max]"  placeholder="Max" value="'.(!empty($jsonData->{str_replace(" ","",$row->parameter)}->max)?$jsonData->{str_replace(" ","",$row->parameter)}->max:'').'"></td>';
                        }else{
                           
                            $tbody .= '<td style="min-width:100px;"><input type="text" class="form-control text-center'.$cls.'" name="param['.$row->test_type.']['.$row->id.']['.$nm.']" placeholder="'.$placeholder.'" value="'.(!empty($jsonData->{str_replace(" ","",$row->parameter)}->{$nm})?$jsonData->{str_replace(" ","",$row->parameter)}->{$nm}:'').'"></td>';
                        }       
                    endforeach;
                    ?>
                    <div class="col-md-12 form-group">
                            <div class="table-responsive">
                                <table class="table table-bordered">
                                    <thead class="thead-info">
                                        <tr> <?=$thead?> </tr>
                                    </thead>
                                    <tbody>
                                        <tr> <?=$tbody?>  </tr>
                                    </tbody>
                                </table>
                            </div>
                        </div>
                    <?php
                endforeach;
            ?>
        </div>
    </div>
</form>