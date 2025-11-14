<?php $this->load->view('includes/header'); ?>
<div class="page-content-tab">
	<div class="container-fluid">
        <div class="row">
            <div class="col-sm-12">
				<div class="page-title-box">
					<div class="card-header">
						<div class="row">
                            <div class="col-md-6">
                                <h4 class="card-title pageHeader"><?=$pageHeader?></h4>
                            </div>   
                            <div class="col-md-3">
								<select name="item_id" id="item_id" class="form-control select2">
                                    <option value="">Select ALL</option>
                                    <?php
										foreach($itemList as $row):
											echo '<option value="'.$row->id.'">'.(!empty($row->item_code) ? '['.$row->item_code.'] ' : '') . $row->item_name.'</option>';
										endforeach;  
                                    ?>
                                </select>
							</div>   
                            <div class="col-md-3 form-group">
                                <select name="process_id" id="process_id" class="form-control select2">
                                    <option value="0">Select All</option>
                                    <?php
                                        foreach($processList as $row):
                                            echo '<option value="'.$row->id.'">'.$row->process_name.'</option>';
                                        endforeach;
                                    ?>
                                </select>
                            </div>
                            <div class="col-md-2">
                                <select name="rr_by" id="rr_by" class="form-control select2">
                                    <option value="ALL">Select ALL </option>
                                    <option value="0">INHOUSE</option>
                                    <option value="1">JW-SUPPLIER</option>
                                </select>
                            </div>

                            <div class="col-md-2">
								<select name="mc_vn_id" id="mc_vn_id" class="form-control select2">
                                    <option value="">Select ALL</option>
                                    
                                </select>
							</div>

                            <div class="col-md-3">
								<select name="operator_id" id="operator_id" class="form-control select2">
                                    <option value="">Select ALL Operator</option>
                                    <?php
										foreach($employeeList as $row):
											echo '<option value="'.$row->id.'">'. $row->emp_name.'</option>';
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
                                            <th colspan="8">REJECTION MONITORING  REPORT</th>
                                            <th colspan="4">REJECTION FOUND AT</th>
                                            <th colspan="4">REJECTION BELONGS TO</th>
                                        </tr>
									
                                        <tr>
                                            <th style="min-width:50px;">#</th>
                                            <th style="min-width:100px;">Date</th>
                                            <th style="min-width:80px;">Part No</th>
                                            <th style="min-width:50px;">Jobcard</th>
											
                                            <th style="min-width:50px;">Prod. Qty.</th>
                                            <th style="min-width:50px;">Rej. Qty.</th>
                                            <th style="min-width:50px;">Quality Ratio</th>
                                            <th style="min-width:50px;">Reason of Rej.</th>
											
                                            <th style="min-width:80px;">Process</th>
                                            <th style="min-width:150px;">Process By</th>
                                            <th style="min-width:150px;">Machine / Vendor</th>
                                            <th style="min-width:50px;">Operator/ Challan No.</th>
											
                                            <th style="min-width:50px;">Process</th>
                                            <th style="min-width:150px;">Process By</th>
                                            <th style="min-width:150px;">Machine / Vendor</th>
                                            <th style="min-width:50px;">Operator / Challan No.</th>
                                        </tr>
                                    </thead>
                                    <tbody id="tbodyData"></tbody>
                                    <tfoot id="tfootData" class="thead-dark">
										<tr>
											<th colspan="4">Total</th>
											<th></th><th></th><th></th><th></th>
											<th></th><th></th><th></th><th></th>
											<th></th><th></th><th></th><th></th>
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
        var item_id = $("#item_id").val();
        var process_id = $("#process_id").val();
        var rr_by = $("#rr_by").val();
        var mc_vn_id = $("#mc_vn_id").val();
        var operator_id = $("#operator_id").val();
        var from_date = $('#from_date').val();
	    var to_date = $('#to_date').val();
        if($("#from_date").val() == ""){$(".fromDate").html("From Date is required.");valid=0;}
	    if($("#to_date").val() == ""){$(".toDate").html("To Date is required.");valid=0;}
	    if($("#to_date").val() < $("#from_date").val()){$(".toDate").html("Invalid Date.");valid=0;}

		if(valid){
            $.ajax({
                url: base_url + controller + '/getRejectionMonitoring',
                data: {item_id:item_id,from_date:from_date,to_date:to_date,process_id:process_id,rr_by:rr_by,mc_vn_id:mc_vn_id,operator_id:operator_id},
				type: "POST",
				dataType:'json',
				success:function(data){
                    $("#reportTable").DataTable().clear().destroy();
					$("#tbodyData").html(data.tbodyData);
					$("#tfootData").html(data.tfootData);
					reportTable();
                }
            });
        }
    });   

    $(document).on('change', '#rr_by', function() {
        var rr_by = $(this).val();
        $.ajax({
            type: "POST",
            url: base_url + controller + '/getProcessByWiseList',
            data: {rr_by:rr_by},
            dataType:'json',
        }).done(function(response) {
            if(response.status == 1){
                $('#mc_vn_id').html('');
                $('#mc_vn_id').html(response.option);
                initSelect2();
            }
        });
    }); 
});
</script>