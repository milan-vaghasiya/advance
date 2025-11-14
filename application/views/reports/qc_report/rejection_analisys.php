<?php $this->load->view('includes/header'); ?>
<div class="page-content-tab">
	<div class="container-fluid">
        <div class="row">
            <div class="col-12">
                <div class="card">
                    <div class="card-header">
						<div class="row">
                            <div class="col-md-3 form-group">
                                <h4 class="card-title pageHeader"><?=$pageHeader?></h4>
                            </div>
                            <div class="col-md-2 form-group">
                                <select name="item_id" id="item_id" class="form-control select2">
                                    <option value="0">Select All</option>
                                    <?php
                                        foreach($itemList as $row):
                                            echo '<option value="'.$row->id.'">'.$row->item_code.'</option>';
                                        endforeach;
                                    ?>
                                </select>
                            </div>
                            <div class="col-md-2 form-group">
                                <select name="process_id" id="process_id" class="form-control select2">
                                    <option value="0">Select All</option>
                                    <?php
                                        foreach($processList as $row):
                                            echo '<option value="'.$row->id.'">'.$row->process_name.'</option>';
                                        endforeach;
                                    ?>
                                </select>
                            </div>
                            <div class="col-md-2 form-group">
                                <input type="date" name="from_date" id="from_date" class="form-control" max="<?=date('Y-m-d')?>" value="<?=date('Y-m-d')?>" />
                                <div class="error fromDate"></div>
                            </div>
                            <div class="col-md-3 form-group">
                                <div class="input-group">
                                    <input type="date" name="to_date" id="to_date" class="form-control" max="<?=date('Y-m-d')?>" value="<?=date('Y-m-d')?>" />
                                    <button type="button" class="btn waves-effect waves-light btn-success float-right loaddata" title="Load Data">
                                        <i class="fas fa-sync-alt"></i> Load
                                    </button>
                                </div>
                            </div>
                        </div>                                        
                    </div>
                    <div class="card-body reportDiv" style="min-height:75vh">
                        <div class="table-responsive">
                            <table id='reportTable' class="table table-bordered">
								<thead class="thead-dark" id="theadData">
									<tr>
                                        <th style="width:8%;">#</th>
                                        <th style="width:25%;">Part No.</th>
                                        <th>Production Qty</th>
                                        <th>Total Rej.</th>
                                        <th>Quality Rate</th>
                                        <th>Rej. Rate</th>
                                        <th>Rej. Qty</th>
                                        <th>Reason of Rej.</th>
                                        <th>Defect Belong To</th>
                                        <th>Rej. From</th>
                                        <th>Rej. Remark</th>
                                    </tr>
								</thead>
								<tbody id="tbodyData"></tbody>
								<tfoot class="thead-dark" id="tfootData">
                                   <tr>
                                        <th class="text-right" colspan="2">Total</th>
                                        <th>0</th>
                                        <th>0</th>
                                        <th>0</th>
                                        <th>0</th>
                                        <th>0</th>
                                        <th colspan="4"></th>
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
    $(document).on('click','.loaddata',function(e){
		$(".error").html("");
		var valid = 1;
        var item_id = $('#item_id').val();
        var process_id = $("#process_id").val();
		var from_date = $('#from_date').val();
		var to_date = $('#to_date').val();
		
		if(from_date == ""){$(".fromDate").html("From Date is required.");valid=0;}
		if(to_date == ""){$(".toDate").html("To Date is required.");valid=0;}

		if(valid){
            $.ajax({
                url: base_url + controller + '/getRejectionAnalysisData',
                data: {item_id:item_id,process_id:process_id,from_date:from_date, to_date:to_date},
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
});


</script>