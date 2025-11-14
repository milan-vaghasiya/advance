<?php $this->load->view('includes/header'); ?>
<div class="page-content-tab">
	<div class="container-fluid">
        <div class="row">
            <div class="col-sm-12">
				<div class="page-title-box">
					<div class="row">
						<div class="col-md-3"><h4><?=$pageHeader?></h4></div>
						<div class="col-md-9">
							<div class="input-group">
								<select name="report_type" id="report_type" class="form-control single-select" style="width: 15%;">
									<?php
										foreach($gstr1Types as $key=>$value):
											echo '<option value="'.$key.'">'.$value.'</option>';
										endforeach;
									?>
								</select>
								<input type="date" name="from_date" id="from_date" class="form-control" value="<?= $startDate ?>" />
								<div class="error fromDate"></div>
								<input type="date" name="to_date" id="to_date" class="form-control" value="<?= $endDate ?>" />
								<div class="input-group-append ml-2">
									<button type="button" class="btn waves-effect waves-light btn-success float-right " onclick="loadData('VIEW')" title="Load Data">
										<i class="fas fa-sync-alt"></i> Load
									</button>
								</div>
								<div class="input-group-append">
									<button type="button" class="btn waves-effect waves-light btn-warning float-right " onclick="loadData('EXCEL')" title="Load Data">
										<i class="fa fa-file-excel-o"></i> Excel
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
								<table id='gstr1Table' class="table table-bordered">
									
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
function loadData(file_type = "VIEW") {
	$(".error").html("");
	var valid = 1;
	var from_date = $('#from_date').val();
	var to_date = $('#to_date').val();
    var report_type=$("#report_type").val();

	if ($("#from_date").val() == "") {
		$(".fromDate").html("From Date is required.");
		valid = 0;
	}
	if ($("#to_date").val() == "") {
		$(".toDate").html("To Date is required.");
		valid = 0;
	}
	if ($("#to_date").val() < $("#from_date").val()) {
		$(".toDate").html("Invalid Date.");
		valid = 0;
	}	
	
	var postData = {
		from_date: from_date,
		to_date: to_date,
		file_type: file_type,
		report_type: report_type
	};
	
	if (valid) {
		if (file_type == "VIEW") {
			$.ajax({
				url: base_url + controller + '/getGstr1Report',
				data: postData,
				type: "POST",
				dataType: 'json',
				success: function(data) {	
					$("#gstr1Table").html("");
					$("#gstr1Table").html(data.html);
				}
			});
		}else{
			var url = base_url + controller + '/getGstr1Report/' + encodeURIComponent(window.btoa(JSON.stringify(postData)));
			window.open(url);
		}
	}
}
</script>