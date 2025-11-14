<?php $this->load->view('includes/header'); ?>
<div class="page-content-tab">
	<div class="container-fluid">
        <div class="row">
            <div class="col-sm-12">
				<div class="page-title-box">
					<div class="float-end">
					    <div class="input-group ">
                                <div class="input-group-append" style="width:20%;">
                                    <select id="report_type" class="form-control select2">
                                        <option value="1">Party Wise</option>
                                        <option value="2">Item Wise</option>
                                    </select>
                                </div>
                                <div class="input-group-append" style="width:20%;">
                                    <select id="order_by" class="form-control select2">
                                        <option value="ASC">LOW TO HIGH</option>
                                        <option value="DESC">HIGH TO LOW</option>
                                    </select>
                                </div>
                                <input type="date" name="from_date" id="from_date" class="form-control" value="<?=$startDate?>" />                                    
                                <input type="date" name="to_date" id="to_date" class="form-control" value="<?=$endDate?>" />
                                <div class="input-group-append">
                                    <button type="button" class="btn waves-effect waves-light btn-success float-right refreshReportData loadData" title="Load Data">
                                        <i class="fas fa-sync-alt"></i> Load
                                    </button>
                                </div>
                            </div>
                            <div class="error fromDate"></div>
                            <div class="error toDate"></div>
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
                            <div class="table-responsive">
                                <table id='reportTable' class="table table-bordered">
                                    <thead id="theadData" class="thead-dark">
                                        <tr>
                                            <th>#</th>
                                            <th>Customer Name</th>
                                            <th>Taxable Amount</th>
                                            <th>GST Amount</th>
                                            <th>Net Amount</th>
                                        </tr>
                                    </thead>
                                    <tbody id="tbodyData"></tbody>
                                    <tfoot id="tfootData" class="thead-dark">
                                        <tr>
                                            <th colspan="2" class="text-right">Total</th>
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
$(document).ready(function(){
	reportTable();
    setTimeout(function(){$(".loadData").trigger('click');},500);
    
    $(document).on('click','.loadData',function(e){
		$(".error").html("");
		var valid = 1;
        var report_type = $("#report_type").val();
        var order_by = $("#order_by").val();
        var from_date = $('#from_date').val();
	    var to_date = $('#to_date').val();
        if($("#from_date").val() == ""){$(".fromDate").html("From Date is required.");valid=0;}
	    if($("#to_date").val() == ""){$(".toDate").html("To Date is required.");valid=0;}
	    if($("#to_date").val() < $("#from_date").val()){$(".toDate").html("Invalid Date.");valid=0;}

		if(valid){
            $.ajax({
                url: base_url + controller + '/getSalesAnalysisData',
                data: {report_type:report_type,order_by:order_by,from_date:from_date,to_date:to_date},
				type: "POST",
				dataType:'json',
				success:function(data){
                    $("#reportTable").DataTable().clear().destroy();
					$("#theadData").html(data.thead);
					$("#tbodyData").html(data.tbody);
					$("#tfootData").html(data.tfoot);
					reportTable();
                }
            });
        }
    });   
});
</script>