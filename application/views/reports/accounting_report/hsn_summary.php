<?php $this->load->view('includes/header'); ?>
<div class="page-content-tab">
	<div class="container-fluid">
        <div class="row">
            <div class="col-sm-12">
				<div class="page-title-box">
					<div class="float-end" >
                        <div class="input-group">
                            <div class="input-group-append">
                                <select id="report_type" class="form-control">
                                    <option value="gstr1">SALES</option>
                                    <option value="gstr2">PURCHASE</option>
                                </select>
                            </div>
                            <div class="input-group-append">
                                <input type="date" id="from_date" class="form-control fyDates" value="<?=getFyDate("Y-m-d",date("Y-m-01"))?>">
                            </div>
                            <div class="input-group-append">
                                <input type="date" id="to_date" class="form-control fyDates" value="<?=getFyDate()?>">
                            </div>
                            <div class="input-group-append">
                                <button type="button" class="btn waves-effect waves-light btn-success float-right refreshReportData loadData" title="Load Data">
                                    <i class="fas fa-sync-alt"></i> Load
                                </button>
                            </div>
                        </div>
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
                                        <tr class="text-center">
                                            <th colspan="11"><?=$pageHeader?></th>
                                        </tr>
                                        <tr>
                                            <th class="text-left">HSN</th>
                                            <th class="text-left">Description</th>
                                            <th class="text-left">UQC</th>
                                            <th>Total Quantity</th>
                                            <th>Total Value</th>
                                            <th>Rate</th>
                                            <th>Taxable Value</th>
                                            <th>Integrated Tax Amount</th>
                                            <th>Central Tax Amount</th>
                                            <th>State/UT Tax Amount</th>
                                            <th>Cess Amount</th>
                                        </tr>
                                    </thead>
                                    <tbody id="tbody">
                                    </tbody>
                                    <tfoot class="thead-dark" id="tfoot">
                                        <tr>
                                            <th colspan="3" class="text-right">Total</th>
                                            <th></th>
                                            <th>0</th>
                                            <th></th>
                                            <th>0</th>
                                            <th>0</th>
                                            <th>0</th>
                                            <th>0</th>
                                            <th>0</th>
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
    initSelect2();
    reportTable('commanTable');
	loadData();
    $(document).on('click','.loadData',function(){
		loadData();
	});  
});

function loadData(pdf=""){
    $(".error").html("");
    var valid = 1;
    var report_type = $("#report_type").val();
    var from_date = $('#from_date').val();
    var to_date = $('#to_date').val();

    if($("#from_date").val() == ""){$(".fromDate").html("From Date is required.");valid=0;}
    if($("#to_date").val() == ""){$(".toDate").html("To Date is required.");valid=0;}
    if($("#to_date").val() < $("#from_date").val()){$(".toDate").html("Invalid Date.");valid=0;}

	var postData = {report:report_type,from_date:from_date,to_date:to_date};
    $.ajax({
        url: base_url + controller + '/getHsnSummary',
        data: postData,
        type: "POST",
        dataType:'json',
        success:function(data){
            $("#commanTable").DataTable().clear().destroy();
            $("#tbody").html("");
            $("#tbody").html(data.tbody);
            $("#tfoot").html("");
            $("#tfoot").html(data.tfoot);
            reportTable('commanTable');
        }
    });
}

</script>