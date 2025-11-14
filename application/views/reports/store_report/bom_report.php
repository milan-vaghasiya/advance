<?php $this->load->view('includes/header'); ?>
<link href="<?=base_url('assets/plugins/tree/listree.min.css')?>" rel="stylesheet" type="text/css" />
<div class="page-content-tab">
    <div class="container-fluid bg-container">
        <div class="row">
            <div class="col-12">
			    <div class="page-title-box">
					<div class="row">
                            <div class="col-md-4">
                                <h4 class="card-title pageHeader">Bill Of Material</h4>
                            </div>  
                            <div class="col-md-8">
								<div class="input-group ">
									<div class="input-group-append" style="width:70%;">
										<select name="item_id" id="item_id" class="form-control select2"> 
											<option value="">Select Item</option>
											<?php
												foreach($itemList as $row):
													$item_name = $row->item_code.' '.$row->item_name;
													echo '<option value="'.$row->id.'" data-item_name="'.$item_name.'">'.$row->item_code.' '.$row->item_name.'</option>';
												endforeach;  
											?>
										</select>
									</div>
									<div class="input-group-append" style="width:15%;">
										<button type="button" class="btn btn-block waves-effect waves-light btn-info float-left loaddata" title="Load Data" data-report_type="1">
											<i class="fas fa-sitemap"></i> Load BOM
										</button>
									</div>
									<div class="input-group-append" style="width:15%;">
										<button type="button" class="btn btn-block waves-effect waves-light btn-success float-left loaddata" title="BOM Data Cost" data-report_type="2">
											<i class="fas fa-inr"></i> BOM Cost
										</button>
									</div>
								</div>
                            </div>                  
                        </div>                                         
                    </div>
				</div>
				<div class="card">
                    <div class="card-body reportDiv" style="min-height:75vh">
                        <div class="table-responsive">
                            <table id='reportTable' class="table table-bordered table-striped">
								<thead class="thead-dark" id="theadData">
								</thead>
								<tbody id="tbodyData"></tbody>
								<tfoot></tfoot>
							</table>
                        </div>
					</div>
				</div>
            </div>
        </div>        
    </div>
</div>


<?php $this->load->view('includes/footer'); ?>
<script src="<?=base_url('assets/plugins/tree/listree.umd.min.js')?>"></script>
<!--<script src="<?=base_url('assets/pages/tree.init.js')?>"></script>-->
<script>
$(document).ready(function(){
	reportTable();

    $(document).on('click','.loaddata',function(e){
        var report_type = $(this).data('report_type');
        console.log(report_type);
		$(".error").html("");
		var valid = 1;
		var item_id = $('#item_id').val();  
		if($("#item_id").val() == ""){$(".item_id").html("Item is required.");valid=0;}
		if(valid)
		{
			var item_name = $('#item_id').find(':selected').data('item_name'); 
            $.ajax({
                url: base_url + controller + '/getBomData',
                data: {item_id:item_id,item_name:item_name,report_type:report_type},
				type: "POST",
				dataType:'json',
				success:function(data){
                    $("#reportTable").dataTable().fnDestroy();
					$("#reportTable").html(data.html);
					if(report_type == 2){
						reportTableBOM("reportTable",{pageLength:-1});
					}else{
						reportTable("reportTable",{pageLength:-1});
					}
					
                }
            });
        }
    });   
     
});
function reportTableBOM(tableId = "reportTable",tblOptions = {}){
	//if(tableOptions == ""){
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
			/* buttons: [ 'pageLength', 'excel', {text: 'Refresh',action: function (){$(".refreshReportData").trigger('click');} }] */
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
							$(".refreshReportData").trigger('click');
						} 
					}
				]
			},
			footerCallback: function (tfoot, data, start, end, display) {
				var api = this.api();
					$(api.column(3).footer()).html(
						api.column(3).data().reduce(function (a, b) {
							return (parseFloat(a) + parseFloat(b)).toFixed(3);
					}, 0),
				);
				var api = this.api();
					$(api.column(4).footer()).html(
						api.column(4).data().reduce(function (a, b) {
							return (parseFloat(a) + parseFloat(b)).toFixed(3);
					}, 0),
				);
				var api = this.api();
					$(api.column(8).footer()).html(
						api.column(8).data().reduce(function (a, b) {
							return (parseFloat(a) + parseFloat(b)).toFixed(3);
					}, 0),
				);
			}
		};
	//}
	
	$.extend( tableOptions, tblOptions );
	var reportTable = $('#'+tableId).DataTable(tableOptions);
	reportTable.buttons().container().appendTo( '#'+tableId+'_wrapper toolbar' );
	$('.dataTables_filter .form-control-sm').css("width","97%");
	$('.dataTables_filter .form-control-sm').attr("placeholder","Search.....");
	$('.dataTables_filter').css("text-align","left");
	$('.dataTables_filter label').css("display","block");
	$('.btn-group>.btn:first-child').css("border-top-right-radius","0");
	$('.btn-group>.btn:first-child').css("border-bottom-right-radius","0");

	/* setTimeout(function(){ reportTable.columns.adjust().draw();}, 10);
	$('.page-wrapper').resizer(function() { reportTable.columns.adjust().draw(); }); */


	return reportTable;
}
</script>