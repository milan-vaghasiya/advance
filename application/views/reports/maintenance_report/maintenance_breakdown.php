<?php $this->load->view('includes/header'); ?>
<div class="page-content-tab">
	<div class="container-fluid">
        <div class="row">
			<div class="col-sm-12">
				<div class="page-title-box">
					<div class="row">
						<div class="col-md-8">
							<h4 class="card-title pageHeader"><?=$pageHeader?></h4>
						</div>
                        <div class="col-md-4 float-right">  
                            <div class="input-group">
								<input type="date" name="from_date" id="from_date" class="form-control" value="<?=$startDate?>" />                                    
                                <input type="date" name="to_date" id="to_date" class="form-control" value="<?=$endDate?>" />
                                <div class="input-group-append">
                                    <button type="button" class="btn waves-effect waves-light btn-success refreshReportData loadData" title="Load Data">
                                        <i class="fas fa-sync-alt"></i> Load
                                    </button>
                                </div>
                            </div>    
                            <div class="error fromDate"></div>
                            <div class="error toDate"></div>
                        </div>                  
                    </div>                 
				</div>                                         
			</div>
		</div>
		<div class="row">
			<div class="col-12">
				<div class="card">
					<div class="card-body reportDiv" style="min-height:75vh">
						<div class="table-responsive">
							<table id='reportTable' class="table table-bordered">
								<thead id="theadData" class="thead-dark">
									<tr class="text-center">
										<th>#</th>
										<th>M.T.No.</th>
                                        <th>Start Date</th>
                                        <th>End Date</th>
                                        <th>Prc No.</th>
                                        <th>Part Code</th>
                                        <th>Machine Code</th>
                                        <th>Machine Name</th>
                                        <th>Idle Reason</th>
                                        <th>Solution</th>
                                        <th>Downtime (Day)</th>
                                        <th>Downtime (Hrs)</th>
                                        <th>TD (HR)</th>
                                        <th>TD (MIN)</th>
									</tr>
								</thead>
                                <tbody id="tbodyData"></tbody>
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
    setTimeout(function(){$(".loadData").trigger('click');},500);
    
    $(document).on('click','.loadData',function(e){
		$(".error").html("");
		var valid = 1;
        var from_date = $('#from_date').val();
	    var to_date = $('#to_date').val();
        if($("#from_date").val() == ""){$(".fromDate").html("From Date is required.");valid=0;}
	    if($("#to_date").val() == ""){$(".toDate").html("To Date is required.");valid=0;}
	    if($("#to_date").val() < $("#from_date").val()){$(".toDate").html("Invalid Date.");valid=0;}

		if(valid){
            $.ajax({
                url: base_url + controller + '/getMaintenanceBreakdownData',
                data: { from_date:from_date, to_date:to_date },
				type: "POST",
				dataType:'json',
				success:function(data){
                    $("#reportTable").DataTable().clear().destroy();
					$("#tbodyData").html(data.tbody);
					reportTable();
                }
            });
        }
    });
});
</script>