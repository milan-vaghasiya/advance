<?php $this->load->view('includes/header'); ?>
<div class="page-content-tab">
	<div class="container-fluid">
        <div class="row">
            <div class="col-sm-12">
				<div class="page-title-box">
					<div class="row">
						<div class="col-md-6">
							
						</div>       
						<div class="col-md-6 float-right">  
							<div class="input-group justify-content-end">
								<div class="input-group-append" style="width:80%;">
									<select id="item_id" class="form-control select2">
										<option value="">Select Product</option>
										<?=getItemListOption($itemList)?>
									</select>
								</div>
								<div class="input-group-append">
									<button type="button" class="btn waves-effect waves-light btn-success refreshReportData loadData" title="Load Data">
										<i class="fas fa-sync-alt"></i> Load
									</button>
								</div>
							</div>
							<div class="error stock_type"></div>
						</div>                  
					</div>      
				</div>
			</div>
		</div>
		<div class="row">
            <div class="col-12">
                <div class="card">
                    <div class="card-body reportDiv" style="min-height:75vh">
                        <div class="table-responsive">
                            <table id='reportTable' class="table table-bordered">
								<thead class="thead-dark" id="theadData">
								    <tr>
								        <th>#</th>
								        <th>PRC Date</th>
								        <th>PRC No</th>
								        <th>PRC Qty</th>
								        <th>Process</th>
								    </tr>
								</thead>
								<tbody id="tbodyData"></tbody>
								<tfoot id="tfootData" class="thead-dark"></tfoot>
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
		if($("#item_id").val() == ""){$(".item_id").html("Item  is required.");valid=0;}
		if(valid){
            $.ajax({
                url: base_url + controller + '/getStageWiseProductionData',
                data: {item_id:item_id},
				type: "POST",
				dataType:'json',
				success:function(data){
                    $("#reportTable").DataTable().clear().destroy();
					$("#theadData").html(data.thead);
					$("#tbodyData").html(data.tbody);
					$("#tfootData").html(data.tfoot);
					reportTable();
                }
            });
        }
    });  
});
</script>