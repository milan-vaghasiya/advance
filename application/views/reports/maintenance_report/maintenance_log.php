<?php $this->load->view('includes/header'); ?>
<div class="page-content-tab">
	<div class="container-fluid">
        <div class="row">
			<div class="col-sm-12">
				<div class="page-title-box">
					<div class="row">
						<div class="col-md-7">
							<h4 class="card-title pageHeader"><?=$pageHeader?></h4>
						</div>
                        <div class="col-md-5 float-right">  
                            <div class="input-group">
								<input type="date" name="from_date" id="from_date" class="form-control" value="<?=$startDate?>" />                                    
                                <input type="date" name="to_date" id="to_date" class="form-control" value="<?=$endDate?>" />
                                <div class="input-group-append">
                                    <button type="button" class="btn waves-effect waves-light btn-success refreshReportData loadData" title="Load Data">
                                        <i class="fas fa-sync-alt"></i> Load
                                    </button>
									<button type="button" class="btn waves-effect waves-light btn-primary printMaintenanceLog" title="PDF">
                                        <i class="fa fa-file-pdf"></i> PDF
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
										<th rowspan="2">#</th>
										<th rowspan="2">Date</th>
										<th rowspan="2">PRC No.</th>
										<th rowspan="2">Machine No.</th>
                                        <th colspan="2">Time</th>
                                        <th rowspan="2">Idle Reason</th>
                                        <th rowspan="2">Solution</th>
									</tr>
                                    <tr class="text-center">
                                        <th>From</th>
                                        <th>To</th>
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
                url: base_url + controller + '/getMaintenanceLogData',
                data: { from_date:from_date, to_date:to_date },
				type: "POST",
				dataType:'json',
				success:function(data){
                    $("#reportTable").DataTable().clear().destroy();
					$("#tbodyData").html(data.tbody);
					$("#tfootData").html(data.tfoot);
					reportTable();
                }
            });
        }
    });   
    
    $(document).on('click','.printMaintenanceLog',function(e){
		$(".error").html("");
		var valid = 1;
		var from_date = $('#from_date').val();
		var to_date = $('#to_date').val();
		if($("#from_date").val() == ""){$(".fromDate").html("From Date is required.");valid=0;}
		if($("#to_date").val() == ""){$(".toDate").html("To Date is required.");valid=0;}
		if($("#to_date").val() < $("#from_date").val()){$(".toDate").html("Invalid Date.");valid=0;}

		if(valid){
        	window.open(base_url + controller + '/printMaintenanceLog/'+from_date+'~'+to_date).focus();
        }
    });
});
</script>