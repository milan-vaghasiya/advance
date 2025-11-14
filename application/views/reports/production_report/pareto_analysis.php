<?php $this->load->view('includes/header'); ?>
<style>li.nav-item{padding:0px 3px;}.process-tags span{font-size:0.75rem;color:#000;box-shadow: 0px 1px 1px rgba(9, 30, 66, 0.25), 0px 0px 1px 1px rgba(9, 30, 66, 0.13);}.prcList{padding:0.4rem;}</style>
<div class="page-content-tab">
	<div class="container-fluid">
        <div class="row">
            <div class="col-sm-12">
				<div class="page-title-box">
					<div class="row"> 
                         <div class="col-md-2">
                            <label for="process_by">Process by</label>
							<select name="process_by" id="process_by" class="form-control select2 float-right">
								<option value="1">Inhouse</option>
								<option value="2">Vendor</option>
							</select>
						</div>
						<div class="col-md-3">
                            <label for="item_id">Product</label>
							<select name="item_id" id="item_id" class="form-control select2 float-right" multiple>
								<?php
                                if(!empty($itemList)){
                                    foreach($itemList AS $row){
                                        ?><option value="<?=$row->id?>"><?=(!empty($row->item_code)?'['.$row->item_code.'] ':'').$row->item_name?></option><?php
                                    }
                                }
                                ?>
							</select>
						</div>
						<div class="col-md-2">
                            <label for="process_id">Process</label>
							<select name="process_id" id="process_id" class="form-control select2 float-right" multiple>
                            <?php
                                if(!empty($processList)){
                                    foreach($processList AS $row){
                                        ?><option value="<?=$row->id?>"><?=$row->process_name?></option><?php
                                    }
                                }
                                ?>
							</select>
						</div>
						<div class="col-md-2"> 
                            <label for="from_date">From Date</label>
							<input type="date" name="from_date" id="from_date" class="form-control"  value="<?=date('Y-m-01')?>" />
							
							<div class="error fromDate"></div>
						</div>     
						<div class="col-md-3">  
                            <label for="to_date">To Date</label>
							<div class="input-group">
								<input type="date" name="to_date" id="to_date" class="form-control" value="<?=date('Y-m-d')?>" />
								<div class="input-group-append">
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
        <div class="row">
                <div class="col-md-12">
                    <div class="row">
                        <div class="col-md-6">
                            <div class="crm-desk-right prcDetail" style="height:41vh;">
                                <div class="cd-header bg-light-sky">
                                    <h6 class="m-0 prc_number">TOTAL REJECTION</h6>
                                </div>
                                <div class="sop-body">
                                    <div class=" vh-35" style="overflow:auto;">
                                        <table class="table jpExcelTable ">
                                            <thead>
                                                <tr>
                                                    <th>Details of Defect</th>
                                                    <th class="text-right">Quantity</th>                                                        
                                                    <th class="text-right">%</th>
                                                </tr>
                                            </thead>
                                            <tbody id="rejDetailTbody">
                                            </tbody>
                                        </table>
                                    </div>
                                </div>
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="crm-desk-right prcDetail" style="height:41vh;">
                                <div class="cd-header bg-light-sky">
                                    <h6 class="m-0 prc_number">SUMMARY</h6>
                                </div>
                                <div class="sop-body vh-35" data-simplebar>
                                    <div class="prcMaterial">
                                    <div class=" vh-35" style="overflow:auto;">
                                            <table class="table jpExcelTable">
                                                <thead>
                                                    <tr>
                                                        <th>Details of Defect</th>
                                                        <th class="text-right">Quantity</th>                                                        
                                                        <th class="text-right">%</th>
                                                    </tr>
                                                </thead>
                                                <tbody id="sumDetailTbody">
                                                    <tr>
                                                        <td>
                                                            <b>Ok</b> 
                                                        </td>
                                                        <td class="text-right">0 </td>
                                                        <td class="text-right">0</td>                                                        
                                                    </tr>
                                                    <tr>
                                                        <td>
                                                            <b>Rework</b> 
                                                        </td>
                                                        <td class="text-right">0</td>
                                                        <td class="text-right">0</td>                                                       
                                                    </tr>
                                                    <tr>
                                                        <td>
                                                            <b>Rejection</b> 
                                                        </td>
                                                        <td class="text-right">0</td>
                                                        <td class="text-right">0</td>                                                       
                                                    </tr>                                               
                                                </tbody>
                                            </table>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="col-md-12">
                    <div class="crm-desk-right prcProcess">
                        <div class="sop-body m-10 data-simplebar">
                            <div class="activity salesLog processDetail">
                                <div class="table-responsive">
                                    <table class="table jpExcelTable " id='reportTable'>
                                        <thead class="bg-light-sky text-center">
                                            <tr>
                                                <th rowspan="2" style="width:5%">Sr.</th>
                                                <!-- <th rowspan="2" style="width:10%">Date</th> -->
                                                <th rowspan="2" style="width:5%">Part Name</th>
                                                <th rowspan="2" style="width:10%">Process</th>
                                                <th rowspan="2" style="width:10%">Inspected</th>
                                                <th colspan="2" style="width:15%">Ok</th>
                                                <th colspan="2" style="width:15%">Rework</th>
                                                <th colspan="2" style="width:15%">Rejection</th>
                                            </tr>
                                            <tr>
                                                <th>Qty</th>
                                                <th>%</th>
                                                <th>Qty</th>
                                                <th>%</th>
                                                <th>Qty</th>
                                                <th>%</th>
                                            </tr>
                                        </thead>
                                        <tbody id="detailTbody">
                                        </tbody>
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
		var process_id = $('#process_id').val();
		var item_id = $('#item_id').val();
        var from_date = $('#from_date').val();
	    var to_date = $('#to_date').val();
	    var process_by = $('#process_by').val();
        var is_pdf = $(this).data('pdf');
        if($("#from_date").val() == ""){$(".fromDate").html("From Date is required.");valid=0;}
	    if($("#to_date").val() == ""){$(".toDate").html("To Date is required.");valid=0;}
	    if($("#to_date").val() < $("#from_date").val()){$(".toDate").html("Invalid Date.");valid=0;}
        var postData = {process_id:process_id,item_id:item_id,from_date:from_date,to_date:to_date,process_by:process_by,is_pdf:is_pdf};
		if(valid){
            if(is_pdf == 0){
                $.ajax({
                    url: base_url + controller + '/getParetoAnalysisData',
                    data: postData,
                    type: "POST",
                    dataType:'json',
                    success:function(data){
                        $("#reportTable").DataTable().clear().destroy();
                        $("#detailTbody").html(data.detailTbody);
                        $("#rejDetailTbody").html(data.rejDetailTbody);
                        $("#sumDetailTbody").html(data.sumDetailTbody);
                        reportTable();
                    }
                });
            }else{
                var url = base_url + controller + '/getParetoAnalysisData/' + encodeURIComponent(window.btoa(JSON.stringify(postData)));
                window.open(url);
            } 
        }
    });   
});
</script>