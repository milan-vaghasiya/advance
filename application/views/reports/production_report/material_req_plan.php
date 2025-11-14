<?php $this->load->view('includes/header'); ?>
<div class="page-content-tab">
	<div class="container-fluid">
		<div class="row">
            <div class="col-sm-12">
				<div class="page-title-box">
					<div class="row">
						<div class="col-md-8">
							
						</div>
						<div class="col-md-3">
							<select name="item_id" id="item_id" class="form-control select2">
								<option value="">Select Item</option>
								<?php   
								if(!empty($itemList)):
									foreach($itemList as $row):
										$pending_qty = $row->order_qty - $row->total_dispatch;
										echo '<option value="'.$row->item_id.'" data-order_qty="'.$row->order_qty.'" data-dispatch_qty="'.$row->pending_dispatch.'" data-pending_qty="'.$pending_qty.'">'.$row->item_name.'</option>';
									endforeach;
								endif;
								?>
							</select>
						</div>
						<div class="col-md-1">
							<button type="button" class="btn waves-effect waves-light btn-success loadData" title="Load Data">
								<i class="fas fa-sync-alt"></i> Load
							</button>
						</div>
					</div>
				</div>
			</div>
		</div>
        <div class="row">
            <div class="col-12">
                <div class="card">
                    <div class="card-body reportDiv" style="min-height:75vh">
                        <div class="row">
                            <div class="col-md-12 form-group">
                                <div class="table-responsive">
                                    <table class="table table-bordered">
                                        <thead class="thead-dark" id="theadData">
                                            <tr>
                                                <th>Item Code</th>
                                                <th>Order Receive</th>
                                                <th>Dispatched</th>
                                                <th>Ready Stock</th>
                                                <th>In Packing</th>
                                                <th>WIP</th>
                                                <th>Shortage</th>
                                            </tr>
                                        </thead>
                                        <tbody id="summaryData">
                                            <tr>
                                                <td id="item_code">-</td>
                                                <td id="ord_qty"></td>
                                                <td id="disp_qty"></td>
                                                <td id="stock_qty"></td>
                                                <td id="in_packing"></td>
                                                <td id="wip_qty"></td>
                                                <td id="req_qty"></td>
                                            </tr>
                                        </tbody>
                                    </table>
                                </div>
                            </div>

                            <div class="col-md-12 form-group">
                                <div class="table-responsive">
                                    <table id='stockDetails' class="table table-bordered">
                                        <thead class="thead-dark" id="theadData">
                                            <tr class="text-center">
                                                <th colspan="4">Finish Stock Details</th>
                                            </tr>
                                            <tr>
                                                <th style="min-width:25px;">#</th>
                                                <th style="min-width:100px;">Location</th>
                                                <th style="min-width:100px;">Batch No.</th>
                                                <th style="min-width:50px;">Qty</th>
                                            </tr>
                                        </thead>
                                        <tbody id="stockData">
                                            <tr>
                                                <td colspan="4" class="text-center">
                                                    No data available in table
                                                </td>
                                            </tr>
                                        </tbody>
                                        <tfoot class="thead-dark" id="stockFooter">
                                            <tr>
                                                <th colspan="3" class="text-right">Total</th>
                                                <th>0</th>
                                            </tr>
                                        </tfoot>
                                    </table>
                                </div>
                            </div>

                            <div class="col-md-12 form-group">
                                <div class="table-responsive">
                                    <table id='wipDetails' class="table table-bordered">
                                        <thead class="thead-dark" id="theadData">
                                            <tr class="text-center">
                                                <th colspan="5">WIP Details</th>
                                            </tr>
                                            <tr>
                                                <th style="min-width:25px;">#</th>
                                                <th style="min-width:100px;">Store</th>
                                                <th style="min-width:50px;">WIP Qty</th>
                                                <th style="min-width:50px;">Stock Qty</th>
                                                <th style="min-width:50px;">Total Qty</th>
                                            </tr>
                                        </thead>
                                        <tbody id="wipData">
                                            <tr>
                                                <td colspan="35" class="text-center">
                                                    No data available in table
                                                </td>
                                            </tr>
                                        </tbody>
                                        <tfoot class="thead-dark" id="wipFooter">
                                            <tr>
                                                <th colspan="4" class="text-right">Total</th>
                                                <th>0</th>
                                            </tr>
                                        </tfoot>
                                    </table>
                                </div>
                            </div>

                            <div class="col-md-12 form-group">
                                <div class="table-responsive">
                                    <table id='materialDetails' class="table table-bordered">
                                        <thead class="thead-dark" id="theadData">
                                            <tr class="text-center">
                                                <th colspan="5">Material Requirement Details [ Pen. Ord. Qty : <span id="reqQty">0</span> ]</th>
                                            </tr>
                                            <tr>
                                                <th style="min-width:25px;">#</th>
                                                <th style="min-width:100px;">Item Name</th>
                                                <th style="min-width:50px;">Req. Qty</th>
                                                <th style="min-width:50px;">Stock Qty</th>
                                                <th style="min-width:50px;">Shortage</th>
                                            </tr>
                                        </thead>
                                        <tbody id="materialData">
                                            <tr>
                                                <td colspan="5" class="text-center">
                                                    No data available in table
                                                </td>
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
    </div>
</div>


<?php $this->load->view('includes/footer'); ?>
<script>
$(document).ready(function(){
	reportTable();
    $(document).on('click','.loadData',function(){
		$(".error").html("");
		var valid = 1;
		var item_id = $('#item_id').val();
        var pending_qty = $("#item_id :selected").data('pending_qty');

        var item_code = $("#item_id :selected").text();
        var order_qty = $("#item_id :selected").data('order_qty');
        var dispatch_qty = $("#item_id :selected").data('dispatch_qty');
        $("#item_code").html("-");

        $("#reqQty").html(0);
		if($("#item_id").val() == ""){$(".item_id").html("Item is required.");valid=0;}
		if(valid){
            $.ajax({
                url: base_url + controller + '/getMaterialReqPlanData',
                data: { item_id:item_id, pending_qty:pending_qty },
				type: "POST",
				dataType:'json',
				success:function(data){
                    $("#stockData").html(data.stockTbody);
                    $("#stockFooter").html(data.stockTfoot);
                    $("#wipData").html(data.wipTbody);
                    $("#wipFooter").html(data.wipTfoot);
                    $("#materialData").html(data.materialTbody);
                    $("#reqQty").html(data.req_qty);

                    $("#item_code").html(item_code);
                    $("#ord_qty").html(order_qty);
                    $("#disp_qty").html(dispatch_qty);
                    $("#stock_qty").html(data.totalStockQty);
                    $("#wip_qty").html(data.totalWIPQty);
                    $("#in_packing").html(data.in_packing);
                    $("#req_qty").html(data.req_qty);
                }
            });
        }
    });
});
</script>