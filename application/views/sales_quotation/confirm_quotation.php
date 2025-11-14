<form autocomplete="off">
    <div class="col-md-12">
        <div class="row">
            <div class="table-responsive">
                <table class="table table-bordered">
                    <thead class="thead-dark">
                        <tr>
                            <th>Customer Name </th>
                            <th>Quotation No. </th>
                            <th>Quotation Date</th>
                        </tr>
                    </thead>
                    <tbody>
                        <tr>
                            <td><?=$dataRow->party_name?></td>
                            <td><?=$dataRow->trans_number?></td>
                            <td><?=formatDate($dataRow->trans_date)?></td>
                        </tr>
                    </tbody>
                </table>
            </div>
			<input type="hidden" name="id" id="id" value="<?=$dataRow->id?>" />
			<input type="hidden" name="party_id" id="party_id" value="<?=$dataRow->party_id?>" />
			<div class="col-md-3 form-group">
				<label for="approve_date">Approve Date</label>
				<input type="date" id="approve_date" name="approve_date" class=" form-control req" placeholder="dd-mm-yyyy" aria-describedby="basic-addon2" value="<?=date("Y-m-d")?>" />	
			</div>
        </div>
        <hr>
        <div class="error item_name_error"></div>
        <div class="table-responsive">
            <table class="table table-bordered align-items-center">
                <thead class="thead-info">
                    <tr>
                        <th class="text-center" style="width:5%;">Part/Drg No.</th>
                        <th>Item Name</th>
                        <th>Qty</th>
                        <th>Price</th>
                        <th>Status</th>
                    </tr>
                </thead>
                <tbody id="sqData">
                    <?php
                        $tbodyData="";$i=1; 
                        if(!empty($dataRow)):
                            foreach($dataRow->itemList as $row):
								$tbodyData.= '<tr>
									<td>'.$row->drw_no.'</td>
									<td>'.$row->item_name.'
										<input type="hidden" name="trans_id[]" id="trans_id" value="'.$row->id.'" />
										<input type="hidden" name="item_id[]" id="item_id" value="'.$row->item_id.'" />
									</td>
									<td>'.$row->qty.'</td>   
									<td>'.$row->price.'</td>   
									<td>
										<select class="form-control" name="is_approve[]" id="is_approve">
											<option value="1">Approve</option>
											<option value="2">Cancel</option>
										</select>
									</td>
								</tr>';
                                $i++;
                            endforeach;
                        else:
                            $tbodyData.= '<tr><td colspan="5" style="text-align:center;">No Data Found</td></tr>';
                        endif;
                        echo $tbodyData;
                    ?>
                </tbody>
            </table>
        </div>
        
    </div>
</form>