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
							<!-- <select name="item_id" id="item_id" class="form-control select2 float-right" multiple>
								<?=getItemListOption($itemList)?>
							</select> -->
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
                                    <!-- <button type="button" class="btn waves-effect waves-light btn-warning float-right loadData" data-pdf="1" title="PDF">
										<i class="fas fa-sync-alt"></i> PDF
									</button> -->
								</div>
							</div>
							<div class="error toDate"></div>
						</div>     
					</div> 
				</div>
            </div>
		</div>
        <div class="row">
            <div class="col-md-9">
                <div class="card">
                    <div class="card-body card-statistic-4" style="border-bottom: 4px solid #b6c2e4;">
                    
                        <div class="row">
                            <div class="col-lg-3">
                                <table class="table table-bordered">
                                    <tr class="bg-light-teal">
                                        <th class="text-center">Inh. Forging Rejection(InProcess)</th>
                                    </tr>
                                    <tr>
                                        <td class="text-center" id="totalIhForgeRej"> 0</td>
                                    </tr>
                                </table>
                            </div>

                            <div class="col-lg-3">
                                <table class="table table-bordered">
                                    <tr class="bg-light-teal">
                                        <th class="text-center">Inh. M/C. Rejection</th>
                                    </tr>
                                    <tr>
                                        <td class="text-center" id="totalIhMcRej">0</td>
                                    </tr>
                                </table>
                            </div>
                            <div class="col-lg-3">
                                <table class="table table-bordered">
                                    <tr class="bg-light-teal">
                                        <th class="text-center">Inh. Final Stage Rejection</th>
                                    </tr>
                                    <tr>
                                        <td class="text-center" id="totalFinalProcessRej"> 0 </td>
                                    </tr>
                                </table>
                            </div>

                            <div class="col-lg-3">
                                <table class="table table-bordered">
                                    <tr class="bg-light-harbor">
                                        <th class="text-center">Total InHouse Rejection</th>
                                    </tr>
                                    <tr>
                                        <td class="text-center" id="totalIhRejPer">0</td>
                                    </tr>
                                </table>
                            </div>
                            
                            <div class="col-lg-3">
                                <table class="table table-bordered">
                                    <tr class="bg-light-sky">
                                        <th class="text-center">JW Forging Rejection</th>
                                    </tr>
                                    <tr>
                                        <td class="text-center" id="totaljwForgeRej">0</td>
                                    </tr>
                                </table>
                            </div>
                            <div class="col-lg-3">
                                <table class="table table-bordered">
                                    <tr class="bg-light-sky">
                                        <th class="text-center">JW M/C. Rejection</th>
                                    </tr>
                                    <tr>
                                        <td class="text-center" id="totalJwMcRej"> 0</td>
                                    </tr>
                                </table>
                            </div>
                            <div class="col-lg-3">
                                
                            </div>
                            <div class="col-lg-3">
                                <table class="table table-bordered">
                                    <tr class="bg-light-harbor">
                                        <th class="text-center">Overall Forging Rejection (InH + JW)</th>
                                    </tr>
                                    <tr>
                                        <td class="text-center" id="totalFRejPer">0</td>
                                    </tr>
                                </table>
                            </div>
                        </div>      
                    </div>                           
                </div>

                <div class="card">
                    <div class="card-body card-statistic-4" >
                        <div class="row">
                            <div class="col-lg-3">
                                <table class="table table-bordered">
                                    <thead class="text-center">
                                        <tr class="bg-light-raspberry">
                                            <th colspan="4" class="text-center">Top 10 FRG Rej. Qty.<br>(InHouse - Inprocess)</th>
                                        </tr>
                                        <tr>
                                            <th>Part no.</th>
                                            <th>Lot Qty</th>
                                            <th>Rej. Qty</th>
                                            <th>Rej.%</th>
                                        </tr>
                                    </thead>
                                    <tbody id="top10IhForgeTbody" class="text-center">
                                        <tr>
                                            <td class="text-center" colspan="4">No Data available.</td>
                                        </tr>
                                    </tbody>
                                </table>
                            </div>
                            <div class="col-lg-3">
                                <table class="table table-bordered">
                                    <thead class="text-center">
                                        <tr class="bg-light-raspberry">
                                            <th colspan="4" class="text-center">Top 10 FRG Rej. Qty.<br>(JW - Supplier)</th>
                                        </tr>
                                        <tr>
                                            <th>Part no.</th>
                                            <th>Lot Qty</th>
                                            <th>Rej. Qty</th>
                                            <th>Rej.%</th>
                                        </tr>
                                    </thead>
                                    <tbody id="top10JwForgeTbody" class="text-center">
                                        <tr>
                                            <td class="text-center" colspan="4">No Data available.</td>
                                        </tr>
                                    </tbody>
                                </table>
                            </div>
                            <div class="col-lg-3">
                                <table class="table table-bordered">
                                    <thead class="text-center">
                                        <tr class="bg-light-raspberry">
                                            <th colspan="4" class="text-center">Top 10 M/C Rej. Qty. <br>(InHouse - Inprocess)</th>
                                        </tr>
                                        <tr>
                                            <th>Part no.</th>
                                            <th>Lot Qty</th>
                                            <th>Rej. Qty</th>
                                            <th>Rej.%</th>
                                        </tr>
                                    </thead>
                                    <tbody id="top10IhMcTbody" class="text-center">
                                        <tr>
                                            <td class="text-center" colspan="4">No Data available.</td>
                                        </tr>
                                    </tbody>
                                </table>
                            </div>
                            <div class="col-lg-3">
                                <table class="table table-bordered">
                                    <thead class="text-center">
                                        <tr class="bg-light-raspberry">
                                            <th colspan="4" class="text-center">Top 10 MC Rej. Qty.<br>(JW - Supplier)</th>
                                        </tr>
                                        <tr>
                                            <th>Part no.</th>
                                            <th>Lot Qty</th>
                                            <th>Rej. Qty</th>
                                            <th>Rej.%</th>
                                        </tr>
                                    </thead>
                                    <tbody id="top10JwMcTbody" class="text-center">
                                        <tr>
                                            <td class="text-center" colspan="4">No Data available.</td>
                                        </tr>
                                    </tbody>
                                </table>
                            </div>
                        </div>      
                    </div>                           
                </div>
            </div>
            <div class="col-md-3 mt-10">
                <!-- <div class="card"> -->
                    <!-- <div class="card-body card-statistic-4" style="border-bottom-color:rgb(3, 4, 2);"> -->
                        <div class="col-lg-12">
                            <table class="table table-bordered">
                                <tr class="bg-light-greyish">
                                    <th class="text-center">Rej. % against Sales</th>
                                </tr>
                            </table>
                        </div>
                    <!-- </div> -->
                <!-- </div> -->
                <div class="card">
                    <div class="card-body card-statistic-4" style="border-bottom-color:rgb(3, 4, 2);">
                        <div class="col-lg-12">
                            <table class="table table-bordered">
                                <thead class="text-center">
                                    <tr class="bg-light-greyish">
                                        <th class="text-center" colspan="2">Total Sales Qty.</th>
                                        <th class="text-center">Rej.%</th>
                                    </tr>
                                </thead>
                                <tbody id="salesQtyTbody" class="text-center">
                                    <tr>
                                        <td>Sales Qty.</td>
                                        <td></td>
                                        <td></td>
                                    </tr>
                                    <tr>
                                        <td>Rej. Qty.</td>
                                        <td></td>
                                        <td></td>
                                    </tr>
                                </tbody>
                            </table>
                        </div>
                    </div>
                </div>
                <div class="card">
                    <div class="card-body card-statistic-4" style="border-bottom-color:rgb(3, 4, 2);">
                        <div class="col-lg-12">
                            <table class="table table-bordered">
                                <thead class="text-center">
                                    <tr class="bg-light-greyish">
                                        <th class="text-center" colspan="2">Total Sales Amount</th>
                                        <th class="text-center">Rej.%</th>
                                    </tr>
                                </thead>
                                <tbody id="salesValueTbody" class="text-center">
                                    <tr>
                                        <td>Sales Value</td>
                                        <td></td>
                                        <td></td>
                                    </tr>
                                    <tr>
                                        <td>Rej. Value</td>
                                        <td></td>
                                        <td></td>
                                    </tr>
                                </tbody>
                            </table>
                        </div>
                    </div>
                </div>

                <div class="card">
                    <div class="card-body card-statistic-4" style="border-bottom-color:rgb(3, 4, 2);">
                        <div class="col-lg-12">
                            <table class="table table-bordered">
                                <tr class="bg-light-greyish">
                                    <th class="text-center">Total COPQ Amount</th>
                                </tr>
                                <tr>
                                    <td id="copqAmount" class="text-center">0</td>
                                    
                                </tr>
                            </table>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        
		<div class="row">
            <div class="col-md-6">
                 <div class="card">
                    <div class="card-body card-statistic-4" style="border-bottom-color:rgb(3, 4, 2);">
                        <table class="table table-bordered">
                            <thead class="text-center">
                                <tr class="bg-light-thunder">
                                    <th colspan="2" >CRRN Material Rejection Cost (Customer Wise)</th>
                                </tr>
                                <tr class="bg-light-cream">
                                    <th>Customer Name</th>
                                    <th>Total Rej. Cost</th>
                                </tr>
                            </thead>
                            <tbody id="custRejTbody" class="text-center">
                                <tr>
                                    <td colspan="2" class="text-center">No data available</td>
                                </tr>
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
            <div class="col-md-6">
                 <div class="card">
                    <div class="card-body card-statistic-4" style="border-bottom-color:rgb(3, 4, 2);">
                        <table class="table table-bordered">
                            <thead class="text-center">
                                <tr class="bg-light-thunder">
                                    <th colspan="4" >Rejection COPQ</th>
                                </tr>
                                <tr class="bg-light-cream">
                                    <th>Description</th>
                                    <th>Rej. Qty.</th>
                                    <th>Total Rej. Cost</th>
                                    <th>Rej %</th>
                                </tr>
                            </thead>
                            <tbody id="copqTbody">

                            </tbody>
                            <tfoot id="copqTFoot">

                            </tfoot>
                        </table>
                    </div>
                </div>
            </div>
        </div>		
    </div>
</div>


<?php $this->load->view('includes/footer'); ?>
<script>
$(document).ready(function(){
	// reportTable();
    // setTimeout(function(){$(".loadData").trigger('click');},500);
    
    $(document).on('click','.loadData',function(e){
		$(".error").html("");
		var valid = 1;
		var is_pdf = $(this).data('pdf');
        var from_date = $('#from_date').val();
	    var to_date = $('#to_date').val();
        if($("#from_date").val() == ""){$(".fromDate").html("From Date is required.");valid=0;}
	    if($("#to_date").val() == ""){$(".toDate").html("To Date is required.");valid=0;}
	    if($("#to_date").val() < $("#from_date").val()){$(".toDate").html("Invalid Date.");valid=0;}
        var postData = {from_date:from_date,to_date:to_date,is_pdf:is_pdf};
		if(valid){
                $.ajax({
                    url: base_url + controller + '/overallSummaryReportData',
                    data: postData,
                    type: "POST",
                    dataType:'json',
                    success:function(data){
                       $("#totalFinalProcessRej").html(data.totalFinalProcessRej);
                       $("#totalJwMcRej").html(data.totalJwMcRej);
                       $("#totalIhMcRej").html(data.totalIhMcRej);
                       $("#totalIhForgeRej").html(data.totalIhForgeRej);
                       $("#totaljwForgeRej").html(data.totaljwForgeRej);
                       $("#totalFRejPer").html(data.totalFRejPer);
                       $("#totalIhRejPer").html(data.totalIhRejPer);

                       $("#top10IhForgeTbody").html(data.top10IhForgeTbody);
                       $("#top10JwForgeTbody").html(data.top10JwForgeTbody);
                       $("#top10IhMcTbody").html(data.top10IhMcTbody);
                       $("#top10JwMcTbody").html(data.top10JwMcTbody);

                       $("#salesQtyTbody").html(data.salesQtyTbody);
                       $("#salesValueTbody").html(data.salesValueTbody);
                       
                       $("#custRejTbody").html(data.custRejTbody);

                       $("#copqTbody").html(data.copqTbody);
                       $("#copqTFoot").html(data.copqTFoot);

                       $("#copqAmount").html(data.copqAmount);
                    }
                });
        }
    });   
});
</script>