<?php $this->load->view('includes/header'); ?>
<div class="page-content-tab">
    <div class="container-fluid bg-container">
        <div class="row">
            <div class="col-12">
			    <div class="page-title-box">
					<div class="row">
                            <div class="col-md-6">
                                <h4 class="card-title pageHeader"><?=$pageHeader?></h4>
                            </div>       
							<div class="col-md-3">
									<select name="item_type" id="item_type" class="form-control select2"> 
                                    <option value="">Select Item Type</option>
                                    <?php
										foreach($categoryList as $row):
											echo '<option value="'.$row->id.'">'.$row->category_name.'</option>';
										endforeach;  
                                    ?>
                                </select>
							</div> 
                            <div class="col-md-3">  
                                <div class="input-group">
                                    <input type="date" name="to_date" id="to_date" class="form-control" value="<?=date('Y-m-d')?>" />
                                    <div class="input-group-append">
                                        <button type="button" class="btn waves-effect waves-light btn-success float-right loaddata" title="Load Data">
									        <i class="fas fa-sync-alt"></i> Load
								        </button>
                                    </div>
                                </div>
                                <div class="error toDate"></div>
                            </div>                  
                        </div>                                         
                    </div>
				</div>
				<div class="card">
                    <div class="card-body reportDiv" style="min-height:75vh">
                        <div class="table-responsive">
                            <table id='reportTable' class="table table-bordered">
								<thead class="thead-dark" id="theadData">
                                    <tr class="text-center">
                                        <th colspan="6">Inventory Monitoring</th>
                                        <th class="totalClosing"></th>
                                        <th class="totalInventory"></th>
                                        <th class="totalValue"></th>
                                    </tr>
									<tr>
										<th>#</th>
										<th>Item Description</th>
										<th>UOM</th>
										<th>Opening Stock Qty.</th>
										<th>Total Inward</th>
										<th>Total Consumption</th>
										<th>Stock Qty.</th>
										<th>Value/Unit (INR)</th>
										<th>Total Value(INR)</th>
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


<?php $this->load->view('includes/footer'); ?>
<script>
$(document).ready(function(){
	reportTable();

    $(document).on('click','.loaddata',function(e){
		$(".error").html("");
		var valid = 1;
		var to_date = $('#to_date').val();
		var item_type = $('#item_type').val();  
		if($("#item_type").val() == ""){$(".item_type").html("Item Type is required.");valid=0;}
		if($("#to_date").val() == ""){$(".toDate").html("To Date is required.");valid=0;}
		if(valid)
		{
            $.ajax({
                url: base_url + controller + '/getInventoryMonitor',
                data: {item_type:item_type, to_date:to_date},
				type: "POST",
				dataType:'json',
				success:function(data){
                    $("#reportTable").dataTable().fnDestroy();
					$("#tbodyData").html(data.tbody);
					$(".totalInventory").html(data.totalInventory);
					$(".totalUP").html(data.totalUP);
					$(".totalClosing").html(data.totalClosing);
					$(".totalValue").html(data.totalValue);
					reportTable();
                }
            });
        }
    });   
});
</script>