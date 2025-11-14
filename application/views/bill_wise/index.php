<?php $this->load->view('includes/header'); ?>
<style>
.table tr td hr{
    margin : 0px !important;
    background-color: #000;
}
</style>
<div class="page-content-tab">
	<div class="container-fluid">
        <div class="row">
            <div class="col-sm-12">
				<div class="page-title-box">
					<div class="float-end" style="width:50%;">
					    <div class="input-group">
                            <div class="input-group-append" style="width:65%;">
                                <select id="party_id" class="form-control select2">
                                    <option value="">Select Party Name</option>
                                    <?=getPartyListOption($partyList,$party_id)?>
                                </select>
                            </div>
                            <div class="input-group-append">
                                <select id="status" class="form-control">
                                    <!-- <option value="">ALL</option> -->
                                    <option value="0">Pending</option>
                                    <option value="1">Completed</option>
                                </select>
                            </div>
                            <div class="input-group-append">
                                <button type="button" class="btn waves-effect waves-light btn-success float-right refreshReportData loadData" title="Load Data">
                                    <i class="fas fa-sync-alt"></i> Load
                                </button>
                            </div>
                        </div>
                        <div class="error fromDate"></div>
                        <div class="error toDate"></div>
					</div>
                    <h4 class="card-title pageHeader"><?=$pageHeader?></h4>
				</div>
            </div>
		</div>
        <div class="row">
            <div class="col-12">
				<div class="col-12">
					<div class="card">
                        <div class="card-body reportDiv" style="min-height:75vh">
                            <div class="table-responsive" style="width: 100%;">
                                <table id='commanTable' class="table table-bordered" style="width:100%;">
                                    <thead class="thead-dark" id="theadData">
                                        <tr>
                                            <th class="noExport text-center">#</th>
                                            <th class="text-left">Vou. Type</th>
                                            <th class="text-left">Vou. No.</th>
                                            <th class="text-left">Vou. Date</th>
                                            <th class="text-right">Vou. Amount</th>
                                            <th class="text-right">Settled Amount</th>
                                            <th class="text-right">Unsettled Amount</th>
                                            <th class="text-right">Balance Amount</th>
                                            <th class="text-center">Settled Vou. No.</th>
                                        </tr>
                                    </thead>
                                    <tbody id="billWiseTransaction">
                                    </tbody>
                                </table>
                            </div>
                        </div>
					</div>
				</div>
            </div>
        </div>
    </div>
</div>

<?php $this->load->view('includes/footer'); ?>
<script>
var tableOption = {
    responsive: true,
    "scrollY": '52vh',
    "scrollX": true,
    deferRender: true,
    scroller: true,
    destroy: true,
    "autoWidth" : false,
    order: [],
    "columnDefs": [
        {type: 'natural',targets: 0},
        {orderable: false,targets: "_all"},
        {className: "text-center",targets: [0, 1]},
        {className: "text-center","targets": "_all"}
    ],
    pageLength: 25,
    language: {search: ""},
    lengthMenu: [
        [ 10, 20, 25, 50, 75, 100, 250,500 ],
        [ '10 rows', '20 rows', '25 rows', '50 rows', '75 rows', '100 rows','250 rows','500 rows' ]
    ],
    dom: "<'row'<'col-sm-7'B><'col-sm-5'f>>" + "<'row'<'col-sm-12't>>" + "<'row'<'col-sm-5'i><'col-sm-7'p>>",
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
    "fnInitComplete":function(){ /* $('.dataTables_scrollBody').perfectScrollbar(); */ },
    "fnDrawCallback": function() { /* $('.dataTables_scrollBody').perfectScrollbar('destroy').perfectScrollbar(); */ }
};
$(document).ready(function(){
    reportTable('commanTable',tableOption);
    loadData();
    $(document).on('click','.loadData',function(){
		loadData();
	});  
});

function loadData(){
	$(".error").html("");
	var valid = 1;
	var party_id = $('#party_id').val();
	var status = $('#status').val();
    
	if($("#party_id").val() == ""){$(".party_id").html("Party Name is required.");valid=0;}

	var postData = {party_id:party_id,status:status};
	if(valid){
        $.ajax({
            url: base_url + controller + '/getUnsettledTransactions',
            data: postData,
            type: "POST",
            dataType:'json',
            success:function(data){
                $("#commanTable").DataTable().clear().destroy();
                $("#billWiseTransaction").html("");
                $("#billWiseTransaction").html(data.tbody);
                reportTable('commanTable',tableOption);
            }
        });
	}
}

</script>