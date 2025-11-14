<?php $this->load->view('includes/header'); ?>
<div class="page-content-tab">
	<div class="container-fluid">
        <div class="row">
            <div class="col-sm-12">
				<div class="page-title-box">
					<div class="float-end" >
                        <div class="input-group">
                            <input type="hidden" id="hsn_code" value="<?=$hsnCode?>">
                            <div class="input-group-append">
                                <select id="report" class="form-control">
                                    <option value="gstr1" <?=($report == "gstr1")?"selected":""?>>SALES</option>
                                    <option value="gstr2" <?=($report == "gstr2")?"selected":""?>>PURCHASE</option>
                                </select>
                            </div>
                            <div class="input-group-append">
                                <select id="report_type" class="form-control">
                                    <option value="SUMMARY">SUMMARY</option>
                                    <option value="ITEMWISE">ITEM WISE</option>
                                </select>
                            </div>
                            <div class="input-group-append">
                                <input type="date" id="from_date" class="form-control fyDates" value="<?=$startDate?>">
                            </div>
                            <div class="input-group-append">
                                <input type="date" id="to_date" class="form-control fyDates" value="<?=$endDate?>">
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
                                    <thead class="thead-dark" id="thead">
                                        <tr class="text-center">
                                            <th colspan="16"><?=$pageHeader?></th>
                                        </tr>
                                        <tr>
                                            <th class="text-left">Vou. Type</th>
                                            <th class="text-left">Vou. No.</th>
                                            <th class="text-left">Vou. Date</th>
                                            <th class="text-left">Party Name</th>
                                            <th class="text-left">POS</th>
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
                                            <th colspan="7" class="text-right">Total</th>
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
    var hsn_code = $("#hsn_code").val();
    var report = $("#report").val();
    var report_type = $("#report_type").val();
    var from_date = $('#from_date').val();
    var to_date = $('#to_date').val();

    if($("#from_date").val() == ""){$(".fromDate").html("From Date is required.");valid=0;}
    if($("#to_date").val() == ""){$(".toDate").html("To Date is required.");valid=0;}
    if($("#to_date").val() < $("#from_date").val()){$(".toDate").html("Invalid Date.");valid=0;}

	var postData = {hsn_code:hsn_code,report:report,report_type:report_type,from_date:from_date,to_date:to_date};
    $.ajax({
        url: base_url + controller + '/getHsnTransactions',
        data: postData,
        type: "POST",
        dataType:'json',
        success:function(data){
            $("#commanTable").DataTable().clear().destroy();
            $("#thead").html("");
            $("#thead").html(data.thead);
            $("#tbody").html("");
            $("#tbody").html(data.tbody);
            $("#tfoot").html("");
            $("#tfoot").html(data.tfoot);
            reportTable('commanTable');
        }
    });
}

</script>