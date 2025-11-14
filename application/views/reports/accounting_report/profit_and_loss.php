<?php $this->load->view('includes/header'); ?>
<div class="page-content-tab">
	<div class="container-fluid">
        <div class="row">
            <div class="col-sm-12">
				<div class="page-title-box">
                    <div class="row">
                        <div class="col-md-4"><h4><?=$pageHeader?></h4></div>
                        <div class="col-md-2">   
                            <select name="is_consolidated" id="is_consolidated" class="form-control single-select float-right" style="width:100%">
                                <option value="0">Details</option>
                                <option value="1">Summary</option>
                            </select>
                        </div> 
                        <div class="col-md-2">   
                            <input type="date" name="from_date" id="from_date" class="form-control float-right" value="<?=$startDate?>" />
                            <div class="error fromDate"></div>
                        </div>     
                        <div class="col-md-4">  
                            <div class="input-group">
                                <input type="date" name="to_date" id="to_date" class="form-control float-right" value="<?=$endDate?>" />
                                <div class="input-group-append ml-2">
                                    <button type="button" class="btn waves-effect waves-light btn-success float-right refreshReportData loadData" title="Load Data">
                                        <i class="fas fa-sync-alt"></i> Load
                                    </button>
                                </div>
                                <div class="input-group-append ml-2">
                                    <button type="button" class="btn waves-effect waves-light btn-info float-right pdf" title="PDF">
                                        <i class="fas fa-print"></i> PDF
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
                                        <tr class="text-center"><th class="th_main_head" colspan="6"><?=$pageHeader?> <span id="rptTag">Details</span></th></tr>
                                        <tr>
                                            <th class="text-left">Particulars</th>
                                            <th class="text-center is_consolidated_th" colspan="2">Amount</th>
                                            <th class="text-left">Particulars</th>
                                            <th class="text-center is_consolidated_th" colspan="2">Amount</th>
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
</div>
<?php $this->load->view('includes/footer'); ?>
<script>
$(document).ready(function(){
    loadData();
    $(document).on('click','.loadData',function(){
		loadData();
	});
    
    $(document).on('click','.pdf',function(){
		loadData(1);
	});
});

function loadData(pdf=""){
	$(".error").html("");
	var valid = 1;
	var from_date = $('#from_date').val();
	var to_date = $('#to_date').val();
	var is_consolidated = $("#is_consolidated").val();

	if($("#from_date").val() == ""){$(".fromDate").html("From Date is required.");valid=0;}
	if($("#to_date").val() == ""){$(".toDate").html("To Date is required.");valid=0;}
	if($("#to_date").val() < $("#from_date").val()){$(".toDate").html("Invalid Date.");valid=0;}

	var postData = {from_date:from_date, to_date:to_date,is_consolidated:is_consolidated};

	if(valid){
		if(pdf == ""){
			$.ajax({
				url: base_url + controller + '/getProfitAndLossData',
				data: postData,
				type: "POST",
				dataType:'json',
				success:function(data){
                    var is_consolidated = $("#is_consolidated :selected").val();
                    if(is_consolidated == 0){
                        $(".is_consolidated_th").attr('colspan','2');
                        $(".th_main_head").attr('colspan','6');
                    }else{
                        $(".is_consolidated_th").removeAttr('colspan');
                        $(".th_main_head").attr('colspan','4');
                    }

					$("#tbodyData").html("");
					$("#tbodyData").html(data.tbody);

                    $("#rptTag").html("");
                    var tag = $("#is_consolidated :selected").text();
                    $("#rptTag").html(tag);
				}
			});
		}else{
			var url = base_url + controller + '/getProfitAndLossData/' + encodeURIComponent(window.btoa(JSON.stringify(postData)));
			window.open(url);
		}
	}
}
</script>