
<form>
    <div class="col-md-12">
        <div class="row">
            <div class="table-responsive">
                <table class="table table-bordered">
                    <thead class="thead-dark">
                        <tr>
                            <th>Prc No.</th>
                            <th>Prc Date </th>
                            <th>Item Name</th>
                            <th>Process</th>
                        </tr>
                    </thead>
                    <tbody>
                        <tr>
                            <td><?=(!empty($dataRow->prc_number))?$dataRow->prc_number:((!empty($lineInspData->prc_number))?$lineInspData->prc_number:"")?></td>
                            <td><?=(!empty($dataRow->prc_date))?formatDate($dataRow->prc_date):((!empty($lineInspData->prc_date))?$lineInspData->prc_date:"")?></td>
                            <td><?=(!empty($dataRow->item_name))?$dataRow->item_name:((!empty($lineInspData->item_name))?$lineInspData->item_name:"")?></td>
                            <td><?=(!empty($dataRow->current_process))?$dataRow->current_process:((!empty($lineInspData->process_name))?$lineInspData->process_name:"")?></td>
                        </tr>
                    </tbody>
                </table>
                <hr>
                <div class="row">
                    <input type="hidden" name="id" id="id" value="<?=(!empty($lineInspData->id))?$lineInspData->id:""?>" />
                    <input type="hidden" name="prc_id" id="prc_id" value="<?=(!empty($dataRow->prc_id))?$dataRow->prc_id:((!empty($lineInspData->prc_id))?$lineInspData->prc_id:"")?>" />
                    <input type="hidden" name="item_id" id="item_id" value="<?=(!empty($dataRow->item_id))?$dataRow->item_id:((!empty($lineInspData->item_id))?$lineInspData->item_id:"")?>" />
                    <input type="hidden" name="process_id" id="process_id" value="<?=(!empty($dataRow->process_id))?$dataRow->process_id:((!empty($lineInspData->process_id))?$lineInspData->process_id:"")?>" />
                    <input type="hidden" name="report_type" id="report_type" value="<?=(!empty($lineInspData->report_type))?$lineInspData->report_type:((!empty($report_type))?$report_type:"1")?>" />
                    <input type="hidden" name="control_method" id="control_method" value="<?=(!empty($control_method)?$control_method:'')?>">
                    <input type="hidden" name="sampling_qty" id="sampling_qty" class="form-control floatOnly " value="1" />
                    <!--
					<input type="hidden" name="rev_no" id="rev_no" value="<?=(!empty($dataRow->rev_no))?$dataRow->rev_no:((!empty($lineInspData->rev_no))?$lineInspData->rev_no:"")?>">
					-->
					<input type="hidden" name="rev_no" id="rev_no" value="<?=(!empty($dataRow->rev_no))?$dataRow->rev_no:((!empty($lineInspData->rev_no))?$lineInspData->rev_no:$last_rev_no)?>">

                    
                    <div class="col-md-2 form-group">
                        <label for="insp_date">Date</label>
                        <input type="date" name="insp_date" id="insp_date" class="form-control req" value="<?=(!empty($lineInspData->insp_date))?$lineInspData->insp_date:date('Y-m-d')?>" />
                    </div>
                    <div class="col-md-2 form-group">
                        <label for="insp_time">Time</label>
                        <input type="time" name="insp_time" id="insp_time" class="form-control req" value="<?=(!empty($lineInspData->insp_time))?$lineInspData->insp_time:date('H:i')?>" />
                    </div>  
                    <div class="col-md-4 form-group">
                        <label for="operator_id">Operator</label>
                        <select name="operator_id" id="operator_id" class="form-control select2">
                            <option value="">Select Operator</option>
                            <?php
                                foreach ($operatorList as $row) :
                                    $selected = (!empty($lineInspData->operator_id) && $lineInspData->operator_id == $row->id) ? "selected" : "";
                                    echo '<option value="' . $row->id . '" ' . $selected . '>' . $row->emp_name . '</option>';
                                endforeach;
                            ?>
                        </select>
                    </div>
                    <div class="col-md-4 form-group">
                        <label for="machine_id">Machine</label>
                        <select name="machine_id" id="machine_id" class="form-control select2">
                        <option value="">Select Machine</option>
                            <?php 
                                  foreach ($machineList as $row) :
                                    $selected = (!empty($lineInspData->machine_id) && $lineInspData->machine_id == $row->id) ? "selected" : "";
                                    echo '<option value="' . $row->id . '" ' . $selected . '>' . $row->item_name . '</option>';
                                endforeach;   
                            ?>
                        </select>
                    </div>  
                    <div class="error general"></div>
                    <div class="table-responsive">
                        <table id="preDispatchtbl" class="table table-bordered generalTable excelTable">
                            <thead class="thead-info" id="theadData">
                                <tr style="text-align:center;">
                                    <th rowspan="2" style="width:3%;">#</th>
                                    <th rowspan="2" style="width:10%">Parameter</th>
                                    <th rowspan="2" style="width:5%">Specification</th>
                                    <th colspan="2" style="width:10%">Tolerance</th>
                                    <th colspan="2" style="width:10%">Specification Limit</th>
                                    <th rowspan="2" style="width:5%">Instrument</th>
                                    <th rowspan="2" style="width:5%">Size</th>
                                    <th rowspan="2" style="width:5%">Frequency</th>
                                    <th rowspan="2" style="width:10%">Reading</th>
                                    <th rowspan="2" style="width:7%">Result</th>
                                    <th rowspan="2" style="width:15%">Remark</th>
                                </tr>
                                <tr style="text-align:center;">
                                    <th style="width:5%">Min</th>
                                    <th style="width:5%">Max</th>
                                    <th style="width:5%">LSL</th>
                                    <th style="width:5%">USL</th>
                                </tr>
                            </thead>
                            <tbody id="tbodyData">
                                <?=$inspParamHtml?>
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        </div>
    </div>
</form>
<script>
    $(document).ready(function() {
        
		// setTimeout(function(){ $('#rev_no').trigger('change');}, 500);
        $(document).on('change', '#rev_no', function(e) {
            e.stopImmediatePropagation();e.preventDefault();

            var id = $("#id").val();
            var item_id = $('#item_id').val();
            var rev_no = $("#rev_no").val();
            var process_id = $("#process_id").val();
            $("#tbodyData").html("");
            if (rev_no) {
                $.ajax({
                    url: base_url + 'lineInspection/getInspectionParameter',
                    data: {
                        id: id,
                        item_id: item_id,
                        rev_no:rev_no,
                        process_id:process_id
                    },
                    type: "POST",
                    dataType: 'json',
                    success: function(data) {
                        $("#tbodyData").html(data.tbodyData);
                    }
                });
            }
        });

    });

</script>
