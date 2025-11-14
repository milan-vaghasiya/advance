<?php $this->load->view('includes/header'); ?>
<div class="page-content-tab">
	<div class="container-fluid">
        <div class="row">
            <div class="col-12">
                <div class="card">
                    <div class="card-header">
                        <div class="row">
                        </div>                                         
                    </div>
                    <div class="card-body reportDiv" style="min-height:75vh">
                        <div class="table-responsive">
                            <table id='ssReportTable' class="table table-bordered">
								<thead class="thead-dark" id="theadData">
									<tr>
										<th class="no_filter">#</th>
										<th>Batch No.</th>
										<th>Material Grade</th>
										<th>Colour Code</th>
										<th>Finish Goods</th>
										<th>Item Name</th>
										<th>Stock Qty.</th>
										<th>Make</th>
									</tr>
								</thead>
								<tbody id="tbodyData">
									<?php $i=1;
										if(!empty($batchData)){
											foreach($batchData as $row){
												$batch_no = '<a href="'.base_url("reports/qualityReport/getBatchHistory/".encodeURL($row->item_id).'/'.encodeURL($row->batch_no)).'" target="_blank" datatip="Batch" flow="right">'.$row->batch_no.'</a>';
												echo '<tr>
													<td>'.$i++.'</td>
													<td>'.$batch_no.'</td>
													<td>'.$row->material_grade.'</td>
													<td>'.$row->color_code.'</td>
													<td>'.$row->fg_item_name.'</td>
													<td>'.$row->item_name.'</td>
													<td>'.$row->stock_qty.'</td>
													<td>'.$row->mill_name.'</td>
												</tr>';
											}
										}
									?>
								</tbody>
								<tfoot id="tfootData"></tfoot>
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
	ssReportTable();
	<?php if(!empty($itemId)) { ?>
		setTimeout(function(){ $('#batch_no').val(<?=$itemId?>);$('#batch_no').comboSelect();$('#batch_no').trigger('change'); }, 50);		
	<?php } ?>
	$(document).on('change','#batch_no',function(e){
		var batch_no = $(this).val();
		if(batch_no)
		{
			$.ajax({
				url: base_url + controller + '/getBatchHistory',
				data: {batch_no:batch_no},
				type: "POST",
				dataType:'json',
				success:function(data){
                    $("#ssReportTable").DataTable().clear().destroy();
					$("#tbodyData").html(data.tbodyData);
					$("#tfootData").html(data.tfootData);
					ssReportTable();
				}
			});
		}
	});
});


function ssReportTable(tableId = "ssReportTable", tblOptions = {}) {
	var tableOptions = {
		responsive: true,
		autoWidth: false,
		order: [],
		columnDefs: [
			{ type: 'natural', targets: 0 },
			{ orderable: false, targets: "_all" },
			{ className: "text-left", targets: [0, 1] },
			{ className: "text-center", targets: "_all" }
		],
		pageLength: 25,
		language: { search: "" },
		lengthMenu: [
			[10, 25, 50, 100, -1], ['10 rows', '25 rows', '50 rows', '100 rows', 'Show all']
		],
		dom: "<'row'<'col-sm-7'B><'col-sm-5'f>>" +
		     "<'row'<'col-sm-12't>>" +
		     "<'row'<'col-sm-5'i><'col-sm-7'p>>",
		buttons: {
			dom: {
				button: {
					className: "btn btn-outline-dark"
				}
			},
			buttons: [
				'pageLength',
				{
					extend: 'excel',
					exportOptions: {
						columns: "thead th:not(.noExport)"
					}
				},
				{
					text: 'Refresh',
					action: function () {
						$(".refreshReportData").trigger('click');
					}
				}
			]
		},
		initComplete: function () {
			var api = this.api();
			// Add column-wise search inputs
			api.columns().every(function () {
				var column = this; 
				var th = $(column.header()); 

				if (!th.hasClass("no_filter")) {
					var title = th.text();
					th.html(title + '<br><input type="text" class="form-control form-control-sm" placeholder="Search ' + title + '" />');

					$('input', th).on('keyup change', function () {
						if (column.search() !== this.value) {
							column.search(this.value).draw();
						}
					});
				}
			});
		}
	};
	$.extend(tableOptions, tblOptions);
	var ssReportTable = $('#' + tableId).DataTable(tableOptions);
	ssReportTable.buttons().container().appendTo('#' + tableId + '_wrapper toolbar');
	$('.dataTables_filter .form-control-sm').css("width", "97%");
	$('.dataTables_filter .form-control-sm').attr("placeholder", "Search.....");
	$('.dataTables_filter').css("text-align", "left");
	$('.dataTables_filter label').css("display", "block");
	$('.btn-group>.btn:first-child').css("border-top-right-radius", "0");
	$('.btn-group>.btn:first-child').css("border-bottom-right-radius", "0");

	return ssReportTable;
}
</script>