<?php $this->load->view('includes/header'); ?>
<div class="page-content-tab">
    <div class="container-fluid bg-container">
        <div class="row">
            <div class="col-12">
			<div class="page-title-box">
                        <div class="row">
                            <div class="col-md-4">
                                <h4 class="card-title pageHeader"><?=$pageHeader?></h4>
                            </div>      
                            <div class="col-md-3">
								<select name="item_type" id="item_type" class="form-control select2 req">
									<option value="">Select All</option>
									<?php 
										foreach($itemTypeData as $row):
												echo '<option value="'.$row->id.'">'.$row->category_name.'</option>';
										endforeach;
									?>
								</select>
							</div>
                            <div class="col-md-2">   
                                <input type="date" name="from_date" id="from_date" class="form-control" max="<?=date('Y-m-d')?>" value="<?=date('Y-m-01')?>" />
                                <div class="error fromDate"></div>
                            </div>     
                            <div class="col-md-3">  
                                <div class="input-group">
                                    <input type="date" name="to_date" id="to_date" class="form-control" value="<?=date('Y-m-d')?>" />
                                    <div class="input-group-append ml-2">
                                        <button type="button" class="btn waves-effect waves-light btn-success float-right loaddata" title="Load Data">
									        <i class="fas fa-sync-alt"></i> Load
								        </button>
                                    </div>
                                </div>
                                <div class="error toDate"></div>
                            </div>                 
                        </div>                                         
					<div class="card">
                    <div class="card-body reportDiv" style="min-height:75vh">
                        <div class="table-responsive">
                            <table id='reportTable' class="table table-bordered">
								<thead class="thead-dark" id="theadData">
                                    <tr class="text-center">
                                        <th colspan="10">Purchase Inward</th>
                                    </tr>
									<tr class="text-center">
										<th>#</th>
										<th style="min-width:100px;">G.I. No.</th>
										<th style="min-width:80px;">G.I. Date</th>
										<th style="min-width:80px;">P.O. No.</th>
										<th style="min-width:80px;">P.O. Date</th>
										<th style="min-width:100px;">Supplier Name</th>
										<th style="min-width:100px;">Item Description</th>
										<th style="min-width:50px;">Qty.</th>
										<th style="min-width:50px;">Price</th>
										<th style="min-width:50px;">Total Amount</th>
									</tr>
								</thead>
								<tbody id="tbodyData"> </tbody>
                                <tfoot class="thead-dark" id="tfootData">
                                    <tr>
                                        <th colspan="7">Total</th>
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
    initModalSelect();
	reportTable();
    $(document).on('click','.loaddata',function(e){
		$(".error").html("");
		var valid = 1;
		var item_type = $('#item_type').val();
		var from_date = $('#from_date').val();
		var to_date = $('#to_date').val();
		if($("#from_date").val() == ""){$(".fromDate").html("From Date is required.");valid=0;}
		if($("#to_date").val() == ""){$(".toDate").html("To Date is required.");valid=0;}
		if($("#to_date").val() < $("#from_date").val()){$(".toDate").html("Invalid Date.");valid=0;}
		if(valid)
		{
            $.ajax({
                url: base_url + controller + '/getPurchaseInward',
                data: {item_type:item_type,from_date:from_date, to_date:to_date},
				type: "POST",
				dataType:'json',
				success:function(data){
                    console.log(data);
                    $("#reportTable").dataTable().fnDestroy();
					$("#tbodyData").html(data.tbody);
					$("#tfootData").html(data.tfoot);
					reportTable();
                }
            });
        }
    });   
});


</script>