<form>
    <div class="col-md-12">
        <div class="row">
            <div class="table-responsive">
            <table id="reqTbl" class="table table-bordered align-items-center">
                <thead class="thead-info">
                    <tr class="text-center">
                        <th style="width:5%;">#</th>
                        <th style="width:15%;">Req. Date</th>
                        <th style="width:15%;">Req. No.</th>
                        <th class="text-left">Item Name</th>
                        <th>Qty</th>
                    </tr>
                </thead>
                <tbody>
                    <?php
                    if(!empty($sparpartData)):
                        $i=1;
                        foreach($sparpartData as $row):
                            echo '<tr class="text-center">
								<td>'.$i++.'</td>
								<td>'.formatDate($row->trans_date).'</td>
								<td class="text-left">'.$row->trans_number.'</td>
								<td class="text-left">'.$row->item_name.'</td>
								<td>'.floatval($row->req_qty).'</td>
							</tr>';
                        endforeach;
                    else:
                        echo '<tr><td colspan="5" class="text-center">No data found.</td></tr>';
                    endif;
                    ?>
                </tbody>
            </table>
        </div>
        </div>
    </div>
</form>
