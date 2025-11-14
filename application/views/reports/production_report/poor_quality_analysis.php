<?php $this->load->view('includes/header'); ?>
<div class="page-content-tab">
	<div class="container-fluid">
        <div class="row">
            <div class="col-sm-12">
				<div class="page-title-box">
					<div class="row"> 
                        <div class="col-md-3">
                        </div>   
						<div class="col-md-4">
							<select name="item_id" id="item_id" class="form-control select2 float-right" multiple>
								<?=getItemListOption($itemList)?>
							</select>
						</div>
						<div class="col-md-2">   
							<input type="date" name="from_date" id="from_date" class="form-control"  value="<?=date('Y-m-01')?>" />
							
							<div class="error fromDate"></div>
						</div>     
						<div class="col-md-3">  
							<div class="input-group">
								<input type="date" name="to_date" id="to_date" class="form-control" value="<?=date('Y-m-d')?>" />
								<div class="input-group-append">
									<button type="button" class="btn waves-effect waves-light btn-success loadData" data-pdf="0" title="Load Data">
										<i class="fas fa-sync-alt"></i> Load
									</button>
                                    <button type="button" class="btn waves-effect waves-light btn-warning float-right loadData" data-pdf="1" title="PDF">
										<i class="fas fa-sync-alt"></i> PDF
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
                                <table id='reportTable' class="table table-bordered">
                                    <thead id="theadData" class="thead-dark text-center">
										<tr>
											<th  rowspan="2">#</th>
											<th  rowspan="2">Part</th>
											<th  colspan="3">InHouse - Forging Rej.</th>
											<th  colspan="3">Job Work - Forging Rej.</th>
											<th  colspan="3">InHouse - Machining Rej.</th>
											<th  colspan="3">Job Work - Machining Rej.</th>
											<th  colspan="3">Customer's Return Rej.</th>
											<th  rowspan="2">Total Cost</th>
										</tr>
                                        <tr>
                                            <th>Qty</th>
                                            <th>Rate</th>
                                            <th>Cost</th>

                                            <th>Qty</th>
                                            <th>Rate</th>
                                            <th>Cost</th>

                                            <th>Qty</th>
                                            <th>Rate</th>
                                            <th>Cost</th>

                                            <th>Qty</th>
                                            <th>Rate</th>
                                            <th>Cost</th>

                                            <th>Qty</th>
                                            <th>Rate</th>
                                            <th>Cost</th>
                                        </tr>
                                    </thead>
                                    <tbody id="tbodyData"></tbody>
									<tfoot id="tfootData" class="thead-dark">
                                        <tr>
                                            <th colspan="2" class="text-right">Total</th>
                                            <th>0</th>
                                            <th>0</th>
                                            <th>0</th>
                                            <th></th>
                                            <th></th>
                                            <th></th>
                                            <th></th>
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
    // setTimeout(function(){$(".loadData").trigger('click');},500);
    
    $(document).on('click','.loadData',function(e){
		$(".error").html("");
		var valid = 1;
		var is_pdf = $(this).data('pdf');
		var item_id = $('#item_id').val();
        var from_date = $('#from_date').val();
	    var to_date = $('#to_date').val();
        if($("#from_date").val() == ""){$(".fromDate").html("From Date is required.");valid=0;}
	    if($("#to_date").val() == ""){$(".toDate").html("To Date is required.");valid=0;}
	    if($("#to_date").val() < $("#from_date").val()){$(".toDate").html("Invalid Date.");valid=0;}
        var postData = {item_id:item_id,from_date:from_date,to_date:to_date,is_pdf:is_pdf};
		if(valid){
            if(is_pdf == 0){
                $.ajax({
                    url: base_url + controller + '/poorQualityCostData',
                    data: postData,
                    type: "POST",
                    dataType:'json',
                    success:function(data){
                        $("#reportTable").DataTable().clear().destroy();
                        $("#tbodyData").html(data.tbody);
                        $("#tfootData").html(data.tfoot);
                        reportTable();
                    }
                });
            }else{
                var url = base_url + controller + '/poorQualityCostData/' + encodeURIComponent(window.btoa(JSON.stringify(postData)));
                window.open(url);
            }
        }
    });   
});
</script>