<form>
    <div class="col-md-12">
        <div class="row">
            <div class="table-responsive">
                <table class="table table-bordered">
					<tr>
						<th class="bg-light">Prc No.</th>
						<td><?=(!empty($dataRow->prc_number))?$dataRow->prc_number:((!empty($lineInspData->prc_number))?$lineInspData->prc_number:"")?></td>
						<th class="bg-light">Prc Date </th>
						<td><?=(!empty($dataRow->prc_date))?formatDate($dataRow->prc_date):((!empty($lineInspData->prc_date))?$lineInspData->prc_date:"")?></td>
						<th class="bg-light">Log Date</th>
						<td><?= (!empty($prcLog->trans_date))?formatDate($prcLog->trans_date):""?></td>
					</tr>
					<tr>
						<th class="bg-light">Process</th>
						<td><?=(!empty($dataRow->current_process))?$dataRow->current_process:((!empty($lineInspData->process_name))?$lineInspData->process_name:"")?></td>
						<th class="bg-light">In Challan No.</th>
						<td><?= $prcLog->in_challan_no?></td>
						<th class="bg-light">Qty</th>
						<td><?= (!empty($prcLog->qty))?$prcLog->qty:""?></td>
					</tr>
					<tr>
						<th class="bg-light">Challan No.</th>
						<td><?=(!empty($challan_no))?$challan_no:"";?></td>
						<th class="bg-light">Item Name</th>
						<td colspan="3"><?=(!empty($dataRow->item_name))?$dataRow->item_name:((!empty($lineInspData->item_name))?$lineInspData->item_name:"")?></td>
					</tr>
                </table>
                <hr>
                <div class="row">
                    <input type="hidden" name="id" id="id" value="<?=(!empty($lineInspData->id))?$lineInspData->id:""?>" />
                    <input type="hidden" name="prc_id" id="prc_id" value="<?=(!empty($dataRow->prc_id))?$dataRow->prc_id:((!empty($lineInspData->prc_id))?$lineInspData->prc_id:"")?>" />
                    <input type="hidden" name="item_id" id="item_id" value="<?=(!empty($dataRow->item_id))?$dataRow->item_id:((!empty($lineInspData->item_id))?$lineInspData->item_id:"")?>" />
                    <input type="hidden" name="process_id" id="process_id" value="<?=(!empty($dataRow->process_id))?$dataRow->process_id:((!empty($lineInspData->process_id))?$lineInspData->process_id:"")?>" />
                    <input type="hidden" name="report_type" id="report_type" value="3" />
					<input type="hidden" name="rev_no" id="rev_no" value="<?=((!empty($lineInspData->rev_no))?$lineInspData->rev_no:$last_rev_no)?>">
					<input type="hidden" name="ref_id" id="ref_id" value="<?=(!empty($prcLog->id)?$prcLog->id:0)?>">
					<input type="hidden" name="vendor_id" id="vendor_id" value="<?=((!empty($vendor_id))?$vendor_id:0)?>">

                    
                    <div class="col-md-2 form-group">
                        <label for="insp_date">Date</label>
                        <input type="date" name="insp_date" id="insp_date" class="form-control req" value="<?=(!empty($lineInspData->insp_date))?$lineInspData->insp_date:date('Y-m-d')?>" />
                    </div>
					<?php $sample_size = 5?>
					<div class="col-md-2 form-group">  
						<label for="sampling_qty">Sampling Qty.</label>
						<div class="input-group">
							<input type="text" name="sampling_qty" id="sampling_qty" class="form-control floatOnly req" value="<?=$sample_size?>" />
							<button type="button" class="btn waves-effect waves-light btn-success float-center loaddata" title="Load Data">
								<i class="fas fa-sync-alt"></i>
							</button>
						</div>
					</div>
					
					<div class="col-md-4 form-group">
						<label for="inspection_file">Inspection Report</label>
						<input type="file" name="inspection_file" id="inspection_file" class="form-control" />                
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
									<th colspan="<?= $sample_size?>">Observation on Samples</th>
                                    <th rowspan="2" style="width:7%">Result</th>
                                </tr>
                                <tr style="text-align:center;">
                                    <th style="width:5%">Min</th>
                                    <th style="width:5%">Max</th>
                                    <th style="width:5%">LSL</th>
                                    <th style="width:5%">USL</th>
									<?php for($j=1;$j<=$sample_size;$j++):?> 
										<th><?= $j ?></th>
									<?php endfor;?>  
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
		$(document).on('click', '.loaddata', function(e) { 
			e.stopImmediatePropagation();e.preventDefault();
			var sampling_qty = $("#sampling_qty").val()||5;
			var item_id = $('#item_id').val();
			var process_id = $('#process_id').val();
			var control_method = "IPR";
			var rev_no = $('#rev_no').val();
			if (rev_no) {
				$.ajax({
					url: base_url + 'vendorInspection/getInspectionParameter/0',
					data: {
						sampling_qty:sampling_qty,
						item_id: item_id,
						process_id:process_id,
						control_method:control_method,
						rev_no:rev_no,
						is_json : 1
					},
					type: "POST",
					dataType: 'json',
					success: function(data) {
						$("#theadData").html(data.theadData);
						$("#tbodyData").html(data.tbodyData);
					}
				});
			}
		});
    });

</script>
