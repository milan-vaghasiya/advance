<form>
    <div class="col-md-12">
        <div class="row">
            <div class="table-responsive">
                <table class="table jpExcelTable" id="soTable">
                    <thead class="thead-dark">
                        <tr>
                            <th>#</th>
                            <th>Vou. No.</th>
                            <th>Vou. Date</th>
                            <th>Po. No.</th>
                            <th>Item Name</th>
                            <th>Pending Qty.</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php
                            $i=1;
                            foreach($orderItems as $row):
                                $row->from_entry_type = $row->entry_type;
                                $row->ref_id = $row->id;
                                unset($row->id,$row->entry_type);
                                $row->row_index = "";
                                $row->entry_type = "";
								$row->unit_name = $row->uom;
                                $jsonData = htmlspecialchars(json_encode($row), ENT_QUOTES, 'UTF-8');
                                echo "<tr>
                                    <td class='text-center'>
                                        <input type='checkbox' id='md_checkbox_" . $i . "' class='filled-in chk-col-success orderItem' data-row='".$jsonData."' ><label for='md_checkbox_" . $i . "' class='mr-3 check" . $row->ref_id . "'></label>
                                    </td>
                                    <td>".$row->trans_number."</td>
                                    <td>".formatDate($row->trans_date)."</td>
                                    <td>".$row->doc_no."</td>
                                    <td>".$row->item_name."</td>
                                    <td>".floatval($row->pending_qty)."</td>
                                </tr>";
                                $i++;
                            endforeach;
                        ?>
                    </tbody>
                </table>
            </div>
        </div>
    </div>
</form>
<script>
$(document).ready(function() {
    setTimeout(function(){
        soTable('soTable');
    },5);
});

function soTable(tableId = "soTable"){
	var tableOptions = {
        responsive: true,
        "autoWidth" : false,
		"paging": false,
        order:[],
        "columnDefs": [
            { type: 'natural', targets: 0 },
            { orderable: false, targets: "_all" }, 
            { className: "text-left", targets: [0,1] }, 
            { className: "text-center", "targets": "_all" } 
        ],
        language: { search: "" },
        dom: "<'row'<'col-sm-7'B><'col-sm-5'f>>" +"<'row'<'col-sm-12't>>" + "<'row'<'col-sm-5'i><'col-sm-7'p>>",
        buttons: { buttons:[] },
    };
	
	$.extend( tableOptions );
	var soTable = $('#'+tableId).DataTable(tableOptions);
	soTable.buttons().container().appendTo( '#'+tableId+'_wrapper toolbar' );
	$('.dataTables_filter .form-control-sm').css("width","97%");
	$('.dataTables_filter .form-control-sm').attr("placeholder","Search.....");
	$('.dataTables_filter').css("text-align","left");
	$('.dataTables_filter label').css("display","block");
	$('.btn-group>.btn:first-child').css("border-top-right-radius","0");
	$('.btn-group>.btn:first-child').css("border-bottom-right-radius","0");

	return soTable;
}
</script>