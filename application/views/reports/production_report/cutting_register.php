<?php $this->load->view('includes/header'); ?>
<div class="page-content-tab">
	<div class="container-fluid">
        <div class="row">
            <div class="col-sm-12">
				<div class="page-title-box">
                    <div class="row">
						<div class="col-md-3">
							<select id="operator_id" class="form-control select2">
                                <option value="">Select ALL Operator</option>
								<?php
									foreach($employeeList as $row):
										echo '<option value="'.$row->id.'">'.$row->emp_name.'</option>';
									endforeach;
								?>
							</select>
						</div>
                        <div class="col-md-3">
                            <select name="item_id" id="item_id" class="form-control select2">
                                <option value="">Select ALL Product</option>
                                <?php
                                if(!empty($itemList)): 
                                    foreach($itemList as $row):
                                            echo '<option value="'.$row->id.'">'.(!empty($row->item_code) ? '['.$row->item_code.'] ' : '').$row->item_name.'</option>';
                                    endforeach;
                                endif;
                                ?>
                            </select>
                        </div>
                        <div class="col-md-3">   
                            <input type="date" name="from_date" id="from_date" class="form-control" max="<?=date('Y-m-d')?>" value="<?=date('Y-m-01')?>" />
                            <div class="error fromDate"></div>
                        </div>     
                        <div class="col-md-3">  
                            <div class="input-group">
                                <input type="date" name="to_date" id="to_date" class="form-control" value="<?=date('Y-m-d')?>" />
                                <div class="input-group-append">
                                    <button type="button" class="btn waves-effect waves-light btn-success float-right loadData" title="Load Data">
                                        <i class="fas fa-sync-alt"></i> Load
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
            <div class="col-md-12">
				<div class="card">
					<div class="card-body reportDiv" style="min-height:75vh">
						<div class="table-responsive">
							<table id='reportTable' class="table table-bordered">
								<thead class="thead-dark" id="theadData">
									<tr class="text-center">
										<th>#</th>
										<th style="min-width:80px;">PRC No.</th>
										<th style="min-width:80px;">Operator</th>
										<th style="min-width:80px;">Date</th>
										<th style="min-width:80px;">Machine Name</th>
										<th style="min-width:80px;">Batch No.</th>
										<th style="min-width:50px;">Material Grade</th>
										<th style="min-width:50px;">Dia</th>
										<th style="min-width:50px;">Cutting Rate</th>
										<th style="min-width:50px;">Part No</th>
										<th style="min-width:50px;">Cutting Qty</th>
										<th style="min-width:50px;">Cut Wt.</th>
										<th style="min-width:50px;">Length(MM)</th>
										<th style="min-width:50px;">Total Consu Wt(KG)</th>
										<th style="min-width:50px;">End Pic. Wt</th>
										<th style="min-width:50px;">Rs.</th>
									</tr>
								</thead>
								<tbody id="tbodyData"> </tbody>
								<tfoot id="tfootData"> 
									<tr class="thead-dark">
										<th colspan="10" class="text-right">Total</th>
										<th>0</th>
										<th colspan="5"></th>
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

<?php $this->load->view('includes/footer'); ?>
<script>
$(document).ready(function(){
	reportTable();
    $(document).on('click','.loadData',function(e){
		$(".error").html("");
		var valid = 1;
		var item_id = $('#item_id').val();
		var operator_id = $('#operator_id').val();
		var from_date = $('#from_date').val();
		var to_date = $('#to_date').val();
		if($("#from_date").val() == ""){$(".fromDate").html("From Date is required.");valid=0;}
		if($("#to_date").val() == ""){$(".toDate").html("To Date is required.");valid=0;}
		if($("#to_date").val() < $("#from_date").val()){$(".toDate").html("Invalid Date.");valid=0;}
        
		if(valid)
		{
            $.ajax({
                url: base_url + controller + '/getCuttingRegister',
                data: { item_id:item_id, operator_id:operator_id, from_date:from_date, to_date:to_date }, 
				type: "POST",
				dataType:'json',
				success:function(data){
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