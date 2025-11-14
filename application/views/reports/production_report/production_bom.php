<?php $this->load->view('includes/header'); ?>

<div class="page-content-tab">
	<div class="container-fluid">
		<div class="row">
            <div class="col-sm-12">
				<div class="page-title-box">
					<div class="row">
						<div class="col-md-4 form-group">
							
						</div>
						<div class="col-md-4 form-group">
							<label for="item_id">Fininsh Goods</label>
							<select name="item_id" id="item_id" class="form-control select2">
								<option value="">Select Fininsh Goods</option>
								<?php $rmOptions = '';
									foreach($itemData as $row): 
									
										$row->item_name = (!empty($row->item_code)?'['.$row->item_code.'] ':'').$row->item_name;
										if($row->item_type == 1){
											echo '<option value="'.$row->id.'">'.$row->item_name.'</option>';
										}
										$rmOptions .= '<option value="'.$row->id.'">'.$row->item_name.'</option>'; 
									endforeach; 
								?>
							</select>
						</div>
						<div class="col-md-4 form-group">
							<label for="ref_item_id">Bom Item</label>
							<select name="ref_item_id" id="ref_item_id" class="form-control select2">
								<option value="">Select Bom Item</option>
								<?php   
									echo $rmOptions;
								?>
							</select>
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
                                        <th style="min-width:50px;">#</th>
										<th style="min-width:100px;">Item Name</th>
										<th style="min-width:100px;">Item Code</th>
										<th style="min-width:80px;">Qty</th>
									</tr>
								</thead>
								<tbody id="tbodyData"></tbody>
								<tfoot id="tfootData">
									<tr class="thead-dark">
										<th colspan="3" class="text-right">Total</th>
										<th>0</th>
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
	$(document).on('change','#item_id',function(e){
        e.stopImmediatePropagation();e.preventDefault();
		var item_id = $(this).val();
		$('#ref_item_id').val("");
		$('#ref_item_id').select2();
		if(item_id)
		{
			$.ajax({
				url: base_url + controller + '/getItemBomData',
				data: { item_id:item_id },
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

	$(document).on('change','#ref_item_id',function(e){
        e.stopImmediatePropagation();e.preventDefault();
		var ref_item_id = $(this).val();
		$('#item_id').val("");
		$('#item_id').select2();
		if(ref_item_id)
		{
			$.ajax({
				url: base_url + controller + '/getItemBomData',
				data: { ref_item_id:ref_item_id },
				type: "POST",
				dataType:'json',
				success:function(data){
                    $("#reportTable").DataTable().clear().destroy();
					$("#theadData").html(data.thead);
					$("#tbodyData").html(data.tbody);
					reportTable();
				}
			});
		}
	});      
});
</script>