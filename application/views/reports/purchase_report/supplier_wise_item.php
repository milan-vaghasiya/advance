<?php $this->load->view('includes/header'); ?>
<div class="page-content-tab">
    <div class="container-fluid bg-container">
        <div class="row">
            <div class="col-12">
				<div class="page-title-box">
					<div class="row">
						<div class="col-md-4">
                        </div> 
						<div class="col-md-3">
							<select name="party_id" id="party_id" class="form-control select2 req">
								<option value="">Select Party</option>
								<?=getPartyListOption($partyList)?>
							</select>
						</div>     
						<div class="col-md-3">
							<select name="item_id" id="item_id" class="form-control select2 req">
								<option value="">Select Item</option>
								<?=getItemListOption($itemList)?>
							</select>
						</div>
						<div class="col-md-2"> 
							<div class="input-group">
								<div class="input-group-append"> 
									<button type="button" class="btn waves-effect waves-light btn-success float-right loaddata" title="Load Data">
                                	<i class="fas fa-sync-alt"></i> Load
                            		</button>
								</div>
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
										<th colspan="3">Supplier Wise Item & Item Wise Supplier</th>
									</tr>
									<tr>
										<th>#</th>
										<th>Supplier's Name</th>
										<th>Item Name</th>
									</tr>
								</thead>
								<tbody id="tbodyData"> </tbody>
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
		var party_id = $('#party_id').val();
		var item_id = $('#item_id').val();		
		if(valid)
		{
            $.ajax({
                url: base_url + controller + '/getSupplierWiseItem',
                data: {party_id:party_id,item_id:item_id},
				type: "POST",
				dataType:'json',
				success:function(data){
                    $("#reportTable").dataTable().fnDestroy();
					$("#tbodyData").html(data.tbody);
					reportTable();
                }
            });
        }
    });   
});
</script>