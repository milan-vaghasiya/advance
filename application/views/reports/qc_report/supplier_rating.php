<?php $this->load->view('includes/header'); ?>
<div class="page-content-tab">
	<div class="container-fluid">
        <div class="row">
            <div class="col-sm-12">
				<div class="page-title-box">
					<div class="card-header">
						<div class="row">
                            <div class="col-md-4">
                                <h4 class="card-title pageHeader"><?=$pageHeader?></h4>
                            </div>      
                            <div class="col-md-3">
								<select name="party_id" id="party_id" class="form-control select2">
                                    <option value="">Select Supplier</option>
                                    <?php
										foreach($supplierData as $row):
											echo '<option value="'.$row->id.'">'.$row->party_name.'</option>';
										endforeach;  
                                    ?>
                                </select>
							</div>
                            <div class="col-md-2">   
                                <input type="date" name="from_date" id="from_date" class="form-control" max="<?=date('Y-m-d')?>" value="<?=date('Y-m-d')?>" />
                                <div class="error fromDate"></div>
                            </div>     
                            <div class="col-md-3">  
                                <div class="input-group">
                                    <input type="date" name="to_date" id="to_date" class="form-control" value="<?=date('Y-m-d')?>" />
                                    <div class="input-group-append ml-2">
                                        <button type="button" class="btn waves-effect waves-light btn-success loadData" data-pdf="0" title="Load Data">
											<i class="fas fa-sync-alt"></i> Load
										</button>
										<button type="button" class="btn waves-effect waves-light btn-warning float-right loadData" data-pdf="1" title="PDF">
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
		</div>
        <div class="row">
            <div class="col-12">
				<div class="col-12">
					<div class="card">
                        <div class="card-body reportDiv" style="min-height:75vh">
                            <div class="table-responsive">
                                <table id='reportTable' class="table table-bordered">
                                    <thead id="theadData" class="thead-dark">
									<tr class="text-center">
										<th colspan="15">SUPPLIER RATING REPORT</th>
									</tr>
									
									<tr class="text-center">
										<th>Sr No.</th>
										<th>Supplier Name</th>
										<th>GRN No & Date</th>
										<th>CH/Inv. No. & Date</th>
										<th>Po No. & Date</th>
										<th>Item Description</th>
										<th>GRN Qty.</th>
										<th>Accepted Qty</th>
										<th>Rejected Qty</th>
										<th>Short Qty</th>
										<th>Rejection Rating <br>(%)</th>
										<th>Delivery Date</th>
										<th>Delay Days</th>
										<th>Delivered Rating <br>(%)</th>
										<th>Total</th>
									</tr>
                                    </thead>
                                    <tbody id="tbodyData"></tbody>
                                    <tfoot id="tfootData" class="thead-dark">
										<tr>
											<th colspan="6" style="text-right">Total</th>
											<th></th>
											<th></th>
											<th></th>
											<th></th>
											<th></th>
											<th></th>
											<th></th>
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
	reportTable();
    $(document).on('click','.loadData',function(e){
		$(".error").html("");
		var valid = 1;
        var party_id = $("#party_id").val();
        var from_date = $('#from_date').val();
	    var to_date = $('#to_date').val();
		var is_pdf = $(this).data('pdf');

        if($("#from_date").val() == ""){$(".fromDate").html("From Date is required.");valid=0;}
	    if($("#to_date").val() == ""){$(".toDate").html("To Date is required.");valid=0;}
	    if($("#to_date").val() < $("#from_date").val()){$(".toDate").html("Invalid Date.");valid=0;}
		var postData = {party_id:party_id,from_date:from_date,to_date:to_date,is_pdf:is_pdf};

		if(valid){
            if(is_pdf == 0){
				$.ajax({
					url: base_url + controller + '/getSupplierRating',
					data: postData,
					type: "POST",
					dataType:'json',
					success:function(data){
						$("#reportTable").DataTable().clear().destroy();
						$("#tbodyData").html(data.tbodyData);
						$("#tfootData").html(data.tfootData);
						reportTable();
					}
				});
		 	}else{
				var url = base_url + controller + '/getSupplierRating/' + encodeURIComponent(window.btoa(JSON.stringify(postData)));
				window.open(url);
			}
        }
    });   
});
</script>