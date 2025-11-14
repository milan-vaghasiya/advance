<?php $this->load->view('includes/header'); ?>
<div class="page-content-tab">
	<div class="container-fluid">
        <div class="row">
			<div class="col-sm-12">
				<div class="page-title-box">
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
								<thead id="theadData" class="thead-dark">
									<tr>
										<th>#</th>
										<th>M/C No.</th>
										<th>Description</th>
										<th>Make/Brand</th>
										<th>Capacity</th>
										<th>Serial No.</th>
										<th>Installation Year</th>
										<th>Preventive Maint.</th>
										<th>Specification</th>
									</tr>
								</thead>
								<tbody>
									<?php 
									if(!empty($machineData)):
										$i=1; 
										foreach($machineData as $row):
											echo '<tr>
													<td>'.$i++.'</td>
													<td>'.$row->item_code.'</td>
													<td>'.$row->item_name.'</td>
													<td>'.$row->make_brand.'</td>
													<td>'.$row->size.'</td>
													<td>'.$row->part_no.'</td>
													<td>'.$row->installation_year.'</td>
													<td>'.$row->prev_maint_req.'</td>
													<td>'.$row->description.'</td>
												</tr>';
										endforeach; 
									endif;
									?>
								</tbody>
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
});	
</script>