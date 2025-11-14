<?php $this->load->view('includes/header'); ?>
<div class="page-content-tab">
	<div class="container-fluid">
        <div class="row">
            <div class="col-sm-12">
				<div class="page-title-box">
					<div class="float-end" style="width:60%;">
					    <div class="input-group">
                                <select name="report_type" id="report_type" class="form-control">
                                    <option value="Receivable" <?=(!empty($report_type) && $report_type == "Receivable")?"selected":""?>>Receivable</option>
                                    <option value="Payable" <?=(!empty($report_type) && $report_type == "Payable")?"selected":""?>>Payable</option>
                                </select>  
                                <div class="input-group-append" style="width:30%;">
                                    <select name="party_id" id="party_id" class="form-control select2">
                                        <option value="">All Party</option>
                                        <?=getPartyListOption($partyList)?>
                                    </select>
                                </div>
                                <select name="due_type" id="due_type" class="form-control">
                                    <option value="">ALL</option>
                                    <option value="under_due">Under Due</option>
                                    <option value="over_due">Over Due</option>
                                </select> 
                                <input type="date" name="due_date" id="due_date" class="form-control" value="<?=getFyDate()?>" />
                                <div class="input-group-append">
                                    <button type="button" class="btn waves-effect waves-light btn-success float-right refreshReportData loadData" title="Load Data">
                                        <i class="fas fa-sync-alt"></i> Load
                                    </button>
                                </div>
                            </div>
                            <div class="error dueDate"></div>
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
                                <table id='commanTable' class="table table-bordered">
                                    <thead class="thead-dark" id="theadData">
                                        <tr>
                                            <th>#</th>
                                            <th>Vou. No.</th>
                                            <th>Vou. Date</th>
                                            <th>Party Name</th>
                                            <th>Contact Number</th>
                                            <th>Vou. Amount</th>
                                            <th>Received/Paid Amount</th>
                                            <th>Due Amount</th>
                                            <th>Due Date</th>
                                            <th>Due Days</th>
                                        </tr>
                                    </thead>
                                    <tbody id="tbodyData"></tbody>
                                    <tfoot class="thead-dark" id="tfootData">
                                    <tr>
                                        <th colspan="5" class="text-right">Total</th>
                                        <th>0</th>
                                        <th>0</th>
                                        <th>0</th>
                                        <th></th>
                                        <th></th>
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
    loadData();
    $(document).on('click','.loadData',function(){
		loadData();
	}); 
});

function loadData(pdf=""){
	$(".error").html("");
	var valid = 1;
	var report_type = $('#report_type').val();
    var party_id = $("#party_id").val();
	var due_type = $('#due_type').val();
	var due_date = $("#due_date").val();

	if($("#due_date").val() == ""){$(".dueDate").html("Due Date is required.");valid=0;}

	var postData = {report_type:report_type,party_id:party_id,due_type:due_type,due_date:due_date};

	if(valid){
        $.ajax({
            url: base_url + controller + '/getDuePaymentReminderData',
            data: postData,
            type: "POST",
            dataType:'json',
            success:function(data){
                $("#commanTable").DataTable().clear().destroy();
                $("#tbodyData").html("");
                $("#tbodyData").html(data.tbody);
                $("#tfootData").html("");
                $("#tfootData").html(data.tfoot);
                reportTable('commanTable');
            }
        });
	}
}
</script>