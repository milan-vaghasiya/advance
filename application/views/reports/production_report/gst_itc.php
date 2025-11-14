<?php $this->load->view('includes/header'); ?>
<div class="page-content-tab">
	<div class="container-fluid">
        <div class="row">
            <div class="col-sm-12">
                <div class="page-title-box">
					<div class="float-end" style="width:40%;">
					    <div class="input-group">
                            <div class="input-group-append" style="width:30%;">
                                <select name="itc_type" id="itc_type" class="form-control select2">
                                    <option value="1">TABLE - 4</option>
                                    <option value="2">TABLE - 5A</option>
                                </select>
                            </div>
                            <input type="date" name="from_date" id="from_date" class="form-control" value="<?=$startDate?>"/>                                    
                            <input type="date" name="to_date" id="to_date" class="form-control" value="<?=$endDate?>"/>
                            <div class="input-group-append">
                                <button type="button" class="btn waves-effect waves-light btn-success loadData" data-file_type= "0" title="Load Data">
                                    <i class="fas fa-sync-alt"></i> Load
                                </button>
                                
                                <button type="button" class="btn waves-effect waves-light btn-warning loadData" data-file_type='1' title="Download Excel">
                                    <i class="fas fa-file-excel"></i> EXCEL
                                </button>
                            </div>
                            <div class="error fromDate"></div>
                            <div class="error toDate"></div>
                        </div> 
                    </div>
                </div>
            </div>
            <div class="row">
                <div class="col-md-12">
                    <div class="card">
                        <div class="card-body reportDiv" style="min-height:75vh">
                            <div class="table-responsive">
                                <table id='reportTable' class="table table-bordered">
                                    <thead class="thead-dark" id="theadData">
                                        <tr class="text-center"><th colspan="15" style="background:#B5B3B3;color:black">5(A)Details of inputs/capital goods received from Job worker to whom such goods were sent for job work;losses & wastes</th></tr>
                                        <tr>
                                            <th style="min-width:100px;">Job Worker</th>
                                            <th style="min-width:80px;">Job Worker GSTIN</th>
                                            <th style="min-width:50px;">State</th>
                                            <th style="min-width:100px;">Job Worker's Type</th>
                                            <th style="min-width:80px;">Challan Number</th>
                                            <th style="min-width:100px;">Challan Date</th>
                                            <th style="min-width:100px;">Types of Goods</th>
                                            <th style="min-width:100px;">Description of Goods</th>
                                            <th style="min-width:100px;">UQC</th>
                                            <th style="min-width:100px;">QTY</th>
                                            <th style="min-width:50px;">Taxable Value</th>
                                            <th style="min-width:50px;">IGST Rate</th>
                                            <th style="min-width:50px;">CGST Rate</th>
                                            <th style="min-width:50px;">SGST Rate</th>
                                            <th style="min-width:50px;">Cess</th>
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
    	reportTable();
    $(document).on('click','.loadData',function(e){
		$(".error").html("");
		var valid = 1;
		var itc_type = $('#itc_type').val();
		var from_date = $('#from_date').val();
		var to_date = $('#to_date').val();
        var file_type = $(this).data('file_type');
		if($("#from_date").val() == ""){$(".fromDate").html("From Date is required.");valid=0;}
		if($("#to_date").val() == ""){$(".toDate").html("To Date is required.");valid=0;}
		if($("#to_date").val() < $("#from_date").val()){$(".toDate").html("Invalid Date.");valid=0;}
        var postData = {itc_type:itc_type, file_type:file_type, from_date:from_date, to_date:to_date};
        
		if(valid){
            if(file_type == 0){
                $.ajax({
                    url: base_url + controller + '/getGSTITC4',
                    data: postData, 
                    type: "POST",
                    dataType:'json',
                    success:function(data){
                        $("#reportTable").dataTable().fnDestroy();
                        $("#theadData").html(data.thead);
                        $("#tbodyData").html(data.tbody);
                        $("#tfootData").html(data.tfoot);
                        reportTable();
                    }
                });
            }else{
                var url = base_url + controller + '/getGSTITC4/' + encodeURIComponent(window.btoa(JSON.stringify(postData)));
                window.open(url)
            }
        }
    }); 
});
</script>