<form enctype="multipart/form-data">
    <div class="col-md-12">
        <div class="row">
            <input type="hidden" name="id" value="<?=(!empty($dataRow->id))?$dataRow->id:""?>" />
            <input type="hidden" name="item_id" value="<?=$item_id?>" />

			<div class="col-md-4 form-group">
                <label for="name">Entry Date</label>
				<input type="date" name="entry_date" class="form-control" value="<?=date('Y-m-d')?>"/>
            </div>
            <div class="col-md-12">
                <div class="table-responsive">
					<table id='reportTable' class="table table-bordered">
						<thead class="thead-info" id="theadData">
							<tr>
								<th>#</th>
								<th>Store</th>
								<th>Location</th>
								<th>Batch</th>
								<th>Current Stock</th>
								<th>Physical Qty.</th>
                                <th>Reason</th>
							</tr>  
						</thead>
						<tbody id="tbodyData">
							<?php   $i=1;
								if(!empty($stockData))
								{
									foreach($stockData as $row)
									{
										if(floatVal($row->qty) != 0):
											echo '<tr>
												 	<td class="text-center">'.$i.'</td>
													<td>['.$row->store_name.']</td>
													<td>'.$row->location.'</td>
													<td>'.$row->batch_no.'</td>
												 	<td>'.floatVal($row->qty).'</td>
													<td>
																<input class="form-control floatOnly" type="text" name="physical_qty[]" />
																<input type="hidden" name="stock_qty[]" value="'.floatVal($row->qty).'" />
																<input type="hidden" name="location_id[]" value="'.$row->location_id.'" />
																<input type="hidden" name="batch_no[]" value="'.$row->batch_no.'" /></td>
													<td>
														<input class="form-control" type="text" name="reason[]" />
														<div class="error reason'.$i.'"></div>
													</td>
												</tr>'; 
											$i++;
										endif;
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