<?php $this->load->view('includes/header'); ?>
<div class="page-content-tab">
	<div class="container-fluid">
        <div class="row">
            <div class="col-sm-12">
				<div class="page-title-box">
					<div class="row">
					    <div class="col-md-2">
                            <select name="os_type" id="os_type" class="form-control">
                                <option value="R">Recievable</option>
                                <option value="P">Payable</option>
                            </select>
                        </div> 
                        <div class="col-md-2">
                            <select name="report_type" id="report_type" class="form-control">
                                <option value="1">Summary</option>
                                <option value="2">Agewise</option>
                            </select>
                        </div>    
                        <div class="col-md-3">
                            <div class="input-group hidden" id="age_data">
                                <input type="text" name="days_range[]" id="d1" class="form-control numericOnly days_range" value="30" style="max-width:25%;">
                                <input type="text" name="days_range[]" id="d2" class="form-control numericOnly days_range" value="60" style="max-width:25%;">
                                <input type="text" name="days_range[]" id="d3" class="form-control numericOnly days_range" value="90" style="max-width:25%;">
                                <input type="text" name="days_range[]" id="d4" class="form-control numericOnly days_range" value="120" style="max-width:25%;">
                            </div>
                        </div>    							
                        <div class="col-md-2">   
                            <input type="date" name="from_date" id="from_date" class="form-control" value="<?=$startDate?>" />
                            <div class="error fromDate"></div>
                        </div>     
                        <div class="col-md-3">  
                            <div class="input-group">
                                <input type="date" name="to_date" id="to_date" class="form-control" value="<?=$endDate?>" />
                                <div class="input-group-append ml-2">
                                    <button type="button" class="btn waves-effect waves-light btn-success float-right refreshReportData loadData" title="Load Data">
                                        <i class="fas fa-sync-alt"></i> Load
                                    </button>
                                </div>
                            </div>
                            <div class="error toDate"></div>
                        </div> 
					</div>
				</div>
            </div>
		</div>
        <div class="row">
            <div class="col-12">
				<div class="col-12">
					<div class="card">
                        <div class="card-body reportDiv" style="min-height:75vh">
                            <div class="table-responsive">
                                <table id='commanTable' class="table table-bordered">
                                    <thead class="thead-dark" id="theadData">
                                        <tr class="text-center"><th colspan="6"><?=$pageHeader?></th></tr>
                                        <tr>
                                            <th style="min-width:25px;">#</th>
                                            <th style="min-width:80px;">Account Name</th>
                                            <th style="min-width:50px;">City</th>
                                            <th style="min-width:50px;">Contact Person</th>
                                            <th style="min-width:50px;">Contact Number</th>
                                            <th style="min-width:50px;">Closing Balance</th>
                                        </tr>
                                    </thead>
                                    <tbody id="tbodyData"></tbody>
                                    <tfoot class="thead-dark" id="tfootData">
                                    <tr>
                                        <th colspan="5" class="text-right">Total</th>
                                        <th id="totalAmount">0.00</th>
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
                text: 'Pdf',
                action: function ( e, dt, node, config ) {
                    loadData('pdf');
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
    "fnDrawCallback": function() { /* $('.dataTables_scrollBody').perfectScrollbar('destroy').perfectScrollbar();  */}
};

$(document).ready(function(){
    $(document).on('change',"#report_type",function(){
        var report_type = $(this).val();
        if(report_type == 1){
            $("#age_data").addClass("hidden");
        }else{
            $("#age_data").removeClass("hidden");
        }
    });

    loadData();
    $(document).on('click','.loadData',function(){
		loadData();
	}); 
});

function loadData(pdf=""){
	$(".error").html("");
	var valid = 1;
	var from_date = $('#from_date').val();
	var to_date = $('#to_date').val();
	var os_type = $("#os_type").val();
	var report_type = $("#report_type").val();
	var days_range = $('input[name="days_range[]"]').map(function(){ if(parseFloat(this.value) > 0 ){return this.value;} }).get();

	if($("#from_date").val() == ""){$(".fromDate").html("From Date is required.");valid=0;}
	if($("#to_date").val() == ""){$(".toDate").html("To Date is required.");valid=0;}
	if($("#to_date").val() < $("#from_date").val()){$(".toDate").html("Invalid Date.");valid=0;}

	var postData = {from_date:from_date, to_date:to_date,os_type:os_type,report_type:report_type,days_range:days_range};

	if(valid){
		if(pdf == ""){
			$.ajax({
				url: base_url + controller + '/getOutstandingData',
				data: postData,
				type: "POST",
				dataType:'json',
				success:function(data){
					$("#commanTable").DataTable().clear().destroy();
                    $("#theadData").html("");
                    $("#theadData").html(data.thead);
					$("#tbodyData").html("");
					$("#tbodyData").html(data.tbody);
					$("#tfootData").html("");
					$("#tfootData").html(data.tfoot);
					reportTable('commanTable',tableOption);
				}
			});
		}else{
			var url = base_url + controller + '/getOutstandingData/' + encodeURIComponent(window.btoa(JSON.stringify(postData)));
			window.open(url);
		}
	}
}
</script>