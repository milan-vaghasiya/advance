<?php $this->load->view('includes/header'); ?>
<div class="page-content-tab">
	<div class="container-fluid">
		<div class="row">
            <div class="col-sm-12">
				<div class="page-title-box">
					<div class="float-end">
						<a href="<?= base_url('reports/qualityReport/batchHistory') ?>" class="btn waves-effect waves-light btn-outline-dark float-right"><i class="fa fa-arrow-left"></i> Back</a>
					
						<div class="input-group">
							<input type="hidden" id="item_id" value="<?=(!empty($item_id))?$item_id:""?>">
							<input type="hidden" id="batch_no" value="<?=(!empty($batch_no))?$batch_no:""?>">
						</div>
					</div>
					<h4 class="card-title pageHeader">Batch History</h4>
					
					
				</div>
            </div>
		</div>
        <div class="row">
            <div class="col-12">				
                <div class="card">
                    <div class="card-body reportDiv" style="min-height:75vh">
                        <div class="table-responsive">
                            <table id='reportTable' class="table table-bordered">
								<thead class="thead-dark">
                                    <tr class="text-center">
										<th colspan="10" class="text-left">Item Name : <?=(!empty($itemData)?$itemData->item_name:'Batch History')?></th>
										<th colspan="7" class="text-right">Batch No.: <?=(!empty($batch_no)?$batch_no:'')?></th>
                                    </tr>
                                    <tr class="text-center">
                                        <th colspan="6">Batch Details</th>
                                        <th colspan="11">Manufacturing/Issue Details</th>
                                    </tr>
                                    <tr class="text-center">
                                        <th rowspan="2">#</th>
                                        <th rowspan="2">GRN No.</th>
                                        <th rowspan="2">GRN Date</th>
										<th rowspan="2">CH. No.</th>
                                        <th rowspan="2">GRN Qty</th>
										<th rowspan="2">Party Name</th>
                                        <th rowspan="2">Part Name</th>
                                        <th rowspan="2">PRC No.</th>
                                        <th rowspan="2">PRC Qty</th>
                                        <th rowspan="2">Cut Wt.</th>
                                        <th rowspan="2">Issue Qty</th>
                                        <th rowspan="2">Used Qty</th>
                                        <th colspan="3">Return Qty</th>
                                        <th rowspan="2">PRC Stock</th>
                                        <th rowspan="2">Balance Qty</th>
                                    </tr>
                                    <tr class="text-center">
                                        <th>End Piece</th>
                                        <th>Scrap</th>
                                        <th>Return</th>
                                    </tr>
								</thead>
								<tbody id="tbodyData"></tbody>
                                <tfoot id="tfootData">
									<tr class="thead-dark">
										<th colspan="4" class="text-right">Total</th>
										<th class="text-center">0</th> 
										<th colspan="3"></th> 
										<th class="text-center">0</th> 
										<th></th>
										<th class="text-center">0</th> 
										<th class="text-center">0</th> 
										<th class="text-center">0</th> 
										<th class="text-center">0</th> 
										<th class="text-center">0</th> 
										<th class="text-center">0</th> 
										<th class="text-center">0</th> 
									</tr>
								</tfoot>
							</table>
                        </div>
                    </div>
                </div>
            </div>
        </div>        
    </div>
</div>
<?php $this->load->view('includes/footer'); ?>
<script>
$(document).ready(function(){
	reportTable();
	loadData();    
});

function reportTable(tableId = "reportTable",tblOptions = {}){
	var tableOptions = {
        responsive: true,
        "autoWidth" : false,
        order:[],
        "columnDefs": [
            { type: 'natural', targets: 0 },
            { orderable: false, targets: "_all" }, 
            { className: "text-left", targets: [0,1] }, 
            { className: "text-center", "targets": "_all" } 
        ],
        pageLength:25,
        language: { search: "" },
        lengthMenu: [
            [ 10, 25, 50, 100, -1 ],[ '10 rows', '25 rows', '50 rows', '100 rows', 'Show all' ]
        ],
        dom: "<'row'<'col-sm-7'B><'col-sm-5'f>>" +"<'row'<'col-sm-12't>>" + "<'row'<'col-sm-5'i><'col-sm-7'p>>",
        buttons: {
            dom: {
                button: {
                    className: "btn btn-outline-dark"
                }
            },
            buttons:[ 
                'pageLength', 
                {
                    extend: 'excel',
                    exportOptions: {
                        columns: "thead th:not(.noExport)"
                    }
                },
                {
                    text: 'Refresh',
                    action: function (){ 
                        loadData();
                    } 
                }
            ]
        },
    };
	
	$.extend( tableOptions, tblOptions );
	var reportTable = $('#'+tableId).DataTable(tableOptions);
	reportTable.buttons().container().appendTo( '#'+tableId+'_wrapper toolbar' );
	$('.dataTables_filter .form-control-sm').css("width","97%");
	$('.dataTables_filter .form-control-sm').attr("placeholder","Search.....");
	$('.dataTables_filter').css("text-align","left");
	$('.dataTables_filter label').css("display","block");
	$('.btn-group>.btn:first-child').css("border-top-right-radius","0");
	$('.btn-group>.btn:first-child').css("border-bottom-right-radius","0");

	return reportTable;
}

function loadData(){
	$(".error").html("");
	var valid = 1;
	var item_id = $('#item_id').val();
	var batch_no = $('#batch_no').val();
	
	if(item_id == ""){$(".item_id").html("Item is required.");valid=0;}
	
	if(valid){
		$.ajax({
			url: base_url + controller + '/getBatchHistoryData',
			data: {item_id:item_id,batch_no:batch_no},
			type: "POST",
			dataType:'json',
			success:function(data){
				$("#reportTable").dataTable().fnDestroy();
				$("#tbodyData").html(data.tbody);
				$("#tfootData").html(data.tfoot);
				reportTable();
			}
		});
	}
}
</script>