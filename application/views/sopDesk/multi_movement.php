<form enctype="multipart/form-data">
    <div class="col-md-12">
        <div class="row">
            <input type="hidden" name="request_id" value="<?=(!empty($reqData->id))?$reqData->id:""?>" />
            <input type="hidden" name="item_id" value="<?=(!empty($reqData->item_id))?$reqData->item_id:""?>" />
            <input type="hidden" name="next_process_id" value="<?=(!empty($reqData->req_from))?$reqData->req_from:""?>" />
            <input type="hidden" name="process_id" value="<?=(!empty($reqData->req_to))?$reqData->req_to:""?>" />

			<div class="col-md-4 form-group">
                <label for="trans_date">Date</label>
				<input type="date" name="trans_date" class="form-control" value="<?=date('Y-m-d')?>"/>
            </div>
            <div class="col-md-12">
                <div class="table-responsive">
					<table id='reportTable' class="table table-bordered">
						<thead class="thead-info" id="theadData">
							<tr>
								<th>#</th>
								<th>PRC No</th>
								<th>Type</th>
								<th>Process From</th>
								<th>Current Stock</th>
                                <th>Issue</th>
							</tr>  
						</thead>
						<tbody id="tbodyData">
							<?php   $i=1;
								if(!empty($prcData))
								{
									foreach($prcData as $row)
									{
                                        $pending_Qty = $row->ok_qty - $row->movement_qty;
                                        ?>
                                        <tr>
                                            <td><?=$i?></td>
                                            <td><?=$row->prc_number?></td>
                                            <td><?=($row->trans_type == 1)?'Regular':'Rework'?></td>
                                            <td><?=$row->from_process_name?></td>
                                            <td><?=$pending_Qty?></td>
                                            <td>
                                                <input class="form-control floatOnly" type="text" name="qty[]" />
                                                <input type="hidden" name="prc_id[]" value="<?=$row->prc_id?>" />
                                                <input type="hidden" name="move_from[]" value="<?=$row->trans_type?>" />
                                                <input type="hidden" name="process_from[]" value="<?=$row->process_from?>" />
                                                <input type="hidden" name="move_type[]" value="1" />
                                                <div class="error qty<?=$i?>"></div>
                                            </td>
                                        </tr>
                                        <?php	
                                        $i++;
									}
								}
							?>
						</tbody>
					</table>
				</div>				
            </div>
        </div>
    </div>
</form>