<form>
    <div class="row"><div class="col-md-12 "><div class="float-end"><small><b class="font-bold">Old Weight : </b><?=(!empty($popData->weight) ? $popData->weight : 0)?></small>|<small class="font-bold"><b>Old Height : </b><?=(!empty($popData->height) ? $popData->height : 0)?></small></div></div></div>
    <div class="col-md-12">
        <div class="row">
            <input type="hidden" name="id" value="<?=$id?>" />
            <input type="hidden" name="status" value="9" />
            <input type="hidden" name="type" value="<?=((!empty($type) && $type == 'Component') ? $type : '')?>" />
            <input type="hidden" name="msg" value="<?=((!empty($type) && $type == 'Component') ? 'Approve Die Component' : 'Approve Production')?>" />
            
			<div class="col-md-3 form-group">
                <label for="length">Length <small>(MM)</small></label>
                <input type="text" name="length" class="form-control floatOnly" value="" />
            </div>
            <div class="col-md-3 form-group">
                <label for="width">Width <small>(MM)</small></label>
                <input type="text" name="width" class="form-control floatOnly" value="" />                
            </div>
			<div class="col-md-3 form-group">
                <label for="height">Height <small>(MM)</small></label>
                <input type="text" name="height" class="form-control floatOnly req" value="" />
            </div>
            <div class="col-md-3 form-group">
                <label for="weight">Weight <small>(KGS)</small></label>
                <input type="text" name="weight" class="form-control floatOnly req" value="" />                
            </div>
            
			<div class="col-md-6 form-group">
				<div class="input-group">
					<label for="min_capacity" style="width:50%;">Die Run(min)</label>
					<label for="max_capacity" style="width:50%;">Die Run(max)</label>
				</div>
				<div class="input-group">
					<input type="text" name="min_capacity" class="form-control floatOnly req" value="" style="width:50%;" />
					<input type="text" name="max_capacity" class="form-control floatOnly req" value="" style="width:50%;" />
				</div>
				<div class="error capacity"></div>
            </div>
			
			<div class="col-md-6 form-group">
                <label for="material_value">Die Value <small>(INR)</small></label>
                <input type="text" name="material_value" class="form-control floatOnly" value="" />                
            </div>
            <div class="col-md-12 form-group">
                <label for="attach_file">Attachment</label>
                <input type="file" name="attach_file" class="form-control" />                
            </div>
        </div>
    </div>
	<hr>
    <h6>Die Run History</h6>
	<div class="table-responsive">
        <table class="table jpExcelTable">
            <thead class="thead-info" id="theadData">
                <tr style="text-align:center;">
                    <th style="width:5%;">#</th>
                    <th style="width:20%">Die no</th>
                    <th style="width:20%">Die Run</th>
                    <th style="width:20%">Volume</th>
                </tr>
            </thead>
            <tbody id="tbodyData">
                <?php
					$i=1;
					if(!empty($dieRunData)):
						foreach($dieRunData as $row):
							echo '<tr class="text-center">
									<td>'.$i++.'</td>
									<td>'.$row->category_code.'-'.$row->item_code.'-'.$row->set_no.$row->sr_no.'</td>
									<td>'.$row->capacity.'</td>
									<td>'.$row->volume.'</td>
								</tr>';
						endforeach;
					else:
						echo '<td class="text-center" colspan="4">No data available.</td>';
					endif;
                ?>
            </tbody>
        </table>
    </div>

    <hr>
    <h6>POP Inspection</h6>
    <div class="table-responsive">
        <table class="table jpExcelTable">
            <thead class="thead-info" id="theadData">
                <tr style="text-align:center;">
                    <th style="width:5%;">#</th>
                    <th style="width:20%">Parameter</th>
                    <th style="width:20%">Specification</th>
                    <th style="width:20%">Instrument</th>
                    <th style="width:20%">Observation</th>
                    <th style="width:15%">Result</th>
                </tr>
            </thead>
            <tbody id="tbodyData">
                <?php
                $i=1;
                if(!empty($paramData)):
                    foreach($paramData as $row):
                        $obj = new StdClass; $result = new StdClass;
                        if(!empty($popData)):
                            $obj = json_decode($popData->observation); 
                        endif;
                        if(!empty($popData)):
                            $result = json_decode($popData->result); 
                        endif;
                        echo '<tr class="text-center">
                                <td>'.$i++.'</td>
                                <td>'.$row->parameter.'</td>
                                <td>'.$row->specification.'</td>
                                <td>'.$row->instrument.'</td>
                                <td>
                                    '.(!empty($obj->{$row->id}[0]) ? $obj->{$row->id}[0] : '').'
                                </td>
                                <td>
                                   '.(!empty($result->{$row->id}[0]) ? $result->{$row->id}[0] : '').'
                                </td>
                            </tr>';
                    endforeach;
                else:
                    echo '<td class="text-center" colspan="6">No data available.</td>';
                endif;
                ?>
            </tbody>
        </table>
    </div>
</form>