
<form>
    <div class="col-md-12">
        <div class="row">
            <div class="table-responsive">
                <table class="table table-bordered">
                    <thead class="thead-dark">
                        <tr>
                            <th>POP No.</th>
                            <?php if(!empty($dataRow->trans_number)): ?>
                            <th>Die No.</th>
                            <th>Die Date </th>
                            <?php endif; ?>                            
                            <th>Tool Type</th>
                            <th>Item Name</th>
                        </tr>
                    </thead>
                    <tbody>
                        <tr>  
                            <?php $itemCode = (!empty($dataRow->fg_item_code))?'['.$dataRow->fg_item_code.'] ':''; ?>                         
                            <td><?=(!empty($trans_number)) ? $trans_number : ''?></td>
                            <?php if(!empty($dataRow->trans_number)): ?>
                            <td><?=(!empty($dataRow->trans_number)) ? $dataRow->trans_number : ''?></td>
                            <td><?=(!empty($dataRow->trans_date)) ? formatDate($dataRow->trans_date) : ''?></td>
                            <?php endif; ?>
                            <td><?=(!empty($dataRow->category_name)) ? $dataRow->category_name : ''?></td>
                            <td><?=(!empty($dataRow->fg_item_name)) ? $itemCode.$dataRow->fg_item_name : ''?></td>
                        </tr>
                    </tbody>
                </table>
            </div>
        </div>
        <hr>
        <div class="row">
            <input type="hidden" name="id" id="id" value="" />
            <input type="hidden" name="trans_number" id="trans_number" value="<?=(!empty($trans_number)) ? $trans_number : ''?>" />
            <input type="hidden" name="trans_no" id="trans_no" value="<?=(!empty($trans_no)) ? $trans_no : ''?>" />
            <input type="hidden" name="item_id" id="item_id" value="<?=(!empty($dataRow->fg_item_id))?$dataRow->fg_item_id:''?>" />
            <input type="hidden" name="category_id" id="category_id" value="<?=(!empty($dataRow->item_id))?$dataRow->item_id:''?>" />

            <?php if(!empty($type)): ?>
            <input type="hidden" name="type" id="type" value="<?=(!empty($type))?$type:''?>" />            
            <input type="hidden" name="die_main_id" id="die_main_id" value="<?=(!empty($die_main_id)?$die_main_id:"")?>" />
            <?php else: ?>                
            <input type="hidden" name="die_job_id" id="die_job_id" value="<?=(!empty($dataRow->id))?$dataRow->id:((!empty($id))?$id:"")?>" /> 
            <?php endif; ?>          
            
            <?php
            $dieArray = !empty($paramData)?explode(",",$paramData[0]->category_id):[];
            $otherDie = "";
            if(count($dieArray) > 1){
                array_splice($dieArray, array_search($dataRow->item_id, $dieArray ), 1); 
                $otherDie = $dieArray[0];
                ?>
                <div class="col-md-6 form-group">
                    <label for="insp_type">Inspection Type</label>
                    <select name="insp_type" id="insp_type" class="form-control select2">
                        <option value="">Select Type</option>
                        <option value="1">New</option>
                        <option value="2">Existing</option>
                    </select>
                </div>

                <div class="col-md-6 form-group">
                    <label for="insp_die_id">Inspected Die</label>
                    <select name="insp_die_id" id="insp_die_id" class="form-control select2">
                        <option value="">Select Die</option>
                    </select>
                </div>
                <?php
            }
            ?>
            <input type="hidden" name="other_die" id="other_die" value="<?=$otherDie?>" />
            
        </div>
        <div class="row">
            <div class="table-responsive">
                <table id="preDispatchtbl" class="table table-bordered generalTable">
                    <thead class="thead-info" id="theadData">
                        <tr style="text-align:center;">
                            <th rowspan="2" style="width:5%;">#</th>
                            <th rowspan="2" style="width:20%">Parameter</th>
                            <th rowspan="2" style="width:20%">Specification</th>
                            <th colspan="2" style="width:20%">Tolerance</th>
                            <th colspan="2" style="width:20%">Specification Limit</th>
                            <th rowspan="2" style="width:20%">Expected Value</th>
                            <th rowspan="2" style="width:20%">Instrument</th>
                            <th rowspan="2" style="width:20%">Observation</th>
                            <th rowspan="2" style="width:20%">Result</th>
                        </tr>
                        <tr style="text-align:center;">
                            <th style="width:10%">Min</th>
                            <th style="width:10%">Max</th>
                            <th style="width:10%">LSL</th>
                            <th style="width:10%">USL</th>
                        </tr>
                    </thead>
                    <tbody id="tbodyData">
                        <?php
                        $i=1;
                        if(!empty($paramData)):
                            foreach($paramData as $row):
                                $lsl = floatVal($row->specification) - $row->min;
                                $usl = floatVal($row->specification) + $row->max;
                                $expValue = floatVal($row->specification) * 1.015;
                                echo '<tr class="text-center">
                                        <td>'.$i++.'</td>
                                        <td>'.$row->parameter.'</td>
                                        <td>'.floatVal($row->specification).'</td>
                                        <td>'.$row->min.'</td>
                                        <td>'.$row->max.'</td>
                                        <td>'.$lsl.'</td>
                                        <td>'.$usl.'</td> 
                                        <td>'.sprintf("%.3f",$expValue).'</td>
                                        <td>'.$row->instrument.'</td>
                                        <td>
                                            <input type="text" name="observation_sample_'.$row->id.'" class="form-control text-center value="">
                                        </td>
                                        <td>
                                            <select name="result_'.$row->id.'" class="form-control select2">
                                                <option value="Ok">Ok</option>
                                                <option value="Not Ok">Not Ok</option>
                                            </select>
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
    </div>
</form>
<script>
$(document).ready(function(){
	$(document).on('change',"#insp_type",function(e){
        e.stopImmediatePropagation();e.preventDefault();
		var insp_type = $(this).val();
        var item_id = $('#item_id').val();
        var category_id = $('#other_die').val();
		if(insp_type){
			$.ajax({
				url : base_url + 'dieProduction/getDieListOptions',
				type : 'post',
				data : { insp_type:insp_type, category_id:category_id, item_id:item_id },
				dataType : 'json'
			}).done(function(response){
				$("#insp_die_id").html(response.options);
			});
		}
	});
});		
</script>