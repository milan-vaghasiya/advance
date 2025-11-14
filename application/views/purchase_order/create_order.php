<form>
    <div class="col-md-12">
        <div class="row">
            <div class="table-responsive">
                <table class="table jpExcelTable">
                    <thead class="thead-dark">
                        <tr class="text-center">
                            <th>#</th>
                            <th><input type="checkbox" id="masterSelect" class="filled-in chk-col-success BulkOrder" value=""><label for="masterSelect">ALL</label></th>
                            <th>Enq. No.</th>
                            <th>Enq. Date</th>
                            <th>Quotation No.</th>
                            <th>Item Name</th>
                            <th>Pending Qty.</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php
                        if(!empty($enqItems)):
                            $i=1;
                            foreach($enqItems as $row):
                                if(floatval($row->pending_qty) > 0):
                                    echo "<tr class='text-center'>
                                        <td>".$i."</td>
                                        <td>
                                            <input type='checkbox' name='ref_id[]' id='ref_id_".$i."' class='filled-in chk-col-success BulkOrder' value='".$row->id."'><label for='ref_id_".$i."'></label>
                                        </td>
                                        <td>".$row->trans_number."</td>
                                        <td>".formatDate($row->trans_date)."</td>
                                        <td>".$row->quote_no."</td>
                                        <td>".$row->item_name."</td>
                                        <td>".floatval($row->pending_qty)."</td>
                                    </tr>";
                                    $i++;
                                endif;
                            endforeach;
                        endif;
                        ?>
                    </tbody>
                </table>
            </div>
        </div>
    </div>
</form>
<script>
$(document).ready(function() {
    $(document).on('click', '.BulkOrder', function() {
		if ($(this).attr('id') == "masterSelect") {
			if ($(this).prop('checked') == true) {
				$("input[name='ref_id[]']").prop('checked', true);
			} else {
				$("input[name='ref_id[]']").prop('checked', false);
			}
		} else {
			if ($("input[name='ref_id[]']").not(':checked').length != $("input[name='ref_id[]']").length) {
				$("#masterSelect").prop('checked', false);
			} else {
			}

			if ($("input[name='ref_id[]']:checked").length == $("input[name='ref_id[]']").length) {
				$("#masterSelect").prop('checked', true);
			}
			else{$("#masterSelect").prop('checked', false);}
		}
	});
});
function createOrder(){
    var ref_id = [];
    $("input[name='ref_id[]']:checked").each(function() {
        ref_id.push(this.value);
    });
    var ids = ref_id.join("~");
    var send_data = {
        ids
    };			
    window.open(base_url + 'purchaseOrders/createOrder/' + ids, '_self');
}
</script>