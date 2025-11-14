<?php $this->load->view('includes/header'); ?>
<div class="page-content-tab">
	<div class="container-fluid">
		<div class="row">
            <div class="col-sm-12">
				<div class="page-title-box">
					</div>
					<div class="float-end">
						<a href="<?= base_url('reports/storeReport/stockRegister') ?>" class="btn waves-effect waves-light btn-outline-dark float-right"><i class="fa fa-arrow-left"></i> Back</a>

						<div class="input-group">
							<input type="hidden" id="item_id" value="<?=(!empty($itemData))?$itemData->id:""?>">
							<input type="hidden" id="location_id" value="<?=(!empty($location_id))?$location_id:""?>">
						</div>
					</div>
					<h4 class="card-title pageHeader"><?=$pageHeader?></h4>
		
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
                                    <tr class="text-center">
										<th colspan="8" class="text-left"><?=(!empty($itemData)?$itemData->item_name:'Item History')?></th>
                                    </tr>
									<tr>
										<th style="min-width:25px;">#</th>
										<th style="min-width:100px;">Location</th>
										<th style="min-width:100px;">Heat No.</th>
										<th style="min-width:100px;">Batch No</th>
										<th style="min-width:100px;">Supplier</br>Finish Goods</th>
										<th style="min-width:50px;">Qty</th>
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
	loadData();   
});

function loadData(){
	$(".error").html("");
	var valid = 1;
	var item_id = $('#item_id').val();
	var location_id = $('#location_id').val(); 
	
	if(item_id == ""){$(".item_id").html("Item is required.");valid=0;}	
	
	if(valid){
		$.ajax({
			url: base_url + controller + '/getBatchStockHistory',
			data: {item_id:item_id, location_id:location_id},
			type: "POST",
			dataType:'json',
			success:function(data){
				$("#reportTable").dataTable().fnDestroy();
				$("#tbodyData").html(data.tbody);
				reportTable();
			}
		});
	}
}

</script>