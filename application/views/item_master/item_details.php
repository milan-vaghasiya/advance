<?php $this->load->view('includes/header'); ?>

<style>
.ui-sortable-handle{cursor: move;}
.ui-sortable-handle:hover{background-color: #daeafa;border-color: #9fc9f3;cursor: move;}
</style>
<?php
	$itemImage = base_url('assets/images/users/male_user.png');
	if(!empty($itemData->item_image)):
		$itemImage = base_url("assets/uploads/item_image/".$itemData->item_image);
	endif;
	$mfgType = (!empty($itemData->mfg_type) ? ' ( '.$itemData->mfg_type.' )' : ''); 
	$editParam = "{'postData':{'id' : ".$itemData->id."},'modal_id' : 'bs-right-lg-modal', 'form_id' : 'editItem', 'title' : 'Update Finish Goods','call_function':'edit'}";    
?>
<div class="page-content-tab">
	<div class="container-fluid">
		<div class="row">
			<div class="col-12">
				<div class="card">
					<div class="card-body">
						<div class="met-profile">
							<div class="row">
								<div class="col-lg-9 align-self-center mb-3 mb-lg-0">
									<div class="met-profile-main">
										<div class="met-profile-main-pic sh-cool p-5 br-10">
											<img src="<?=$itemImage?>" alt="" height="75" class="rounded-circle1">
										</div>
										<div class="met-profile_user-detail">
											<h5 class="met-user-name fs-16"><?= (!empty($itemData->item_name)) ? $itemData->item_name : ''; ?></h5>
											<p class="mb-0 met-user-name-post1 fs-15">											
												<span class="badge bg-success p-5"><b><i class="fas fa-tags"></i></b> <?=$itemData->category_name?></span>
												<span class="badge bg-info p-5"><b><i class="fas fa-balance-scale"></i></b> <?= (!empty($itemData->wt_pcs) ? $itemData->wt_pcs : '0'); ?></span>
												<?= (!empty($itemData->material_grade)) ? '<span class="badge bg-primary p-5"><b><i class="fas fa-cubes"></i></b> '.$itemData->material_grade.'</span>' : ""; ?>
												<?= (!empty($itemData->hsn_code)) ? '<span class="badge bg-secondary p-5"><strong style="background:#FFF;color:#000;padding:0px 5px;border-radius:2px;">HSN</strong> '.$itemData->hsn_code.'</span>' : ""; ?>
											</p>
											<p class="mb-0 met-user-name-post1 fs-15">	
												<span class="badge bg-warning p-5"><b><i class="fas fa-industry"></i></b> <?=$itemData->mfg_status.$mfgType?></span>
												
												<span class="badge bg-info p-5"><b><i class="fas fa-box-open"></i></b> <?=(!empty($itemData->is_packing)) ? 'YES' : 'NO'; ?></span>
											</p>
											<?= (!empty($itemData->description)) ? '<p class="mb-0 met-user-name-post">'.$itemData->description.'</p>' : ""; ?>
										</div>
									</div>
								</div>
								<div class="col-lg-3">
									<a href="<?= base_url($headData->controller.'/list/1') ?>" class="btn waves-effect waves-light btn-dark float-right"><i class="fa fa-arrow-left"></i> Back</a>
									<a class="btn waves-effect waves-light btn-success float-right mr-5 permission-modify" type="button" href="javascript:void(0)" onclick="modalAction(<?=$editParam?>);"><i class="mdi mdi-square-edit-outline"></i> Edit</a>
									<a href="<?= base_url($headData->controller.'/productOptionPrint/'.$itemData->id)?>" type="button" class="btn waves-effect waves-light btn-primary float-right mr-5" target="_blank"><i class="fas fa-print"></i> BOM Print</a>
								</div>
							</div>
						</div>
					</div>
					<div class="card-body p-0"> 
						<ul class="nav nav-tabs" id="pills-tab" role="tablist">
							<li class="nav-item">
								<a class="nav-link itemKitDetails active" id="pills-bom-tab" data-bs-toggle="tab" href="#bom" role="tab" aria-controls="pills-bom" aria-selected="false" data-option_type="0">Material BOM</a>
							</li>
							<li class="nav-item">
								<a class="nav-link itemKitDetails" id="pills-process-tab" data-bs-toggle="tab" href="#process" role="tab" aria-controls="pills-process" aria-selected="false" data-option_type="1">Process</a>
							</li>
							<li class="nav-item">
								<a class="nav-link itemKitDetails" id="pills-cycleTime-tab" data-bs-toggle="tab" href="#cycleTime" role="tab" aria-controls="pills-cycleTime" aria-selected="false" data-option_type="2">Process Detail</a>
							</li>
							<li class="nav-item">
								<a class="nav-link itemKitDetails" id="pills-dieBom-tab" data-bs-toggle="tab" href="#dieBom" role="tab" aria-controls="pills-dieBom" aria-selected="false" data-option_type="3">Die BOM</a>
							</li>
							<li class="nav-item">
								<a class="nav-link itemKitDetails" id="pills-packStandard-tab" data-bs-toggle="tab" href="#packStandard" role="tab" aria-controls="pills-packStandard" aria-selected="false" data-option_type="4">Packing Standard</a>
							</li>
							<li class="nav-item">
								<a class="nav-link" id="pills-toolBom-tab" data-bs-toggle="tab" href="#toolBom" role="tab" aria-controls="pills-toolBom" aria-selected="false" data-option_type="0">Tool Bom</a>
							</li>
							<li class="nav-item">
								<a class="nav-link" id="pills-tcParam-tab" data-bs-toggle="tab" href="#tcParam" role="tab" aria-controls="pills-tcParam" aria-selected="false">TC Parameter</a>
							</li>
							<li class="nav-item">
								<a class="nav-link" id="pills-tcSpecification-tab" data-bs-toggle="tab" href="#tcSpecification" role="tab" aria-controls="pills-tcSpecification" aria-selected="false">TC Specification</a>
							</li>
							<li class="nav-item">
								<a class="nav-link itemKitDetails" id="pills-revision-tab" data-bs-toggle="tab" href="#revision" role="tab" aria-controls="pills-revision" aria-selected="false" data-option_type="0">Revision</a>
							</li>
							<li class="nav-item">
								<a class="nav-link itemKitDetails" id="pills-parameter-tab" data-bs-toggle="tab" href="#parameter" role="tab" aria-controls="pills-parameter" aria-selected="false" data-option_type="5">Inspection Parameter</a>
							</li>
							<li class="nav-item">
								<a class="nav-link itemKitDetails" id="pills-popParameter-tab" data-bs-toggle="tab" href="#popParameter" role="tab" aria-controls="pills-popParameter" aria-selected="false" data-option_type="6">POP Parameter</a>
							</li>
						</ul>

						<!-- Tab panes -->
						<div class="tab-content">
							<!-- BOM Start -->
							<div class="tab-pane fade show active" id="bom" role="tabpanel" aria-labelledby="pills-bom-tab">
								<form id="addProductKitItems" data-res_function="getProductKitHtml">
									<div class="card-body">
										<div class="row">
											<input type="hidden" name="id" id="id" value="" />
											<input type="hidden" name="item_id" id="item_id" value="<?=(!empty($itemData->id))?$itemData->id:""?>" />
											<input type="hidden" name="form_type" id="form_type" value="addProductKitItems" />

											<div class="col-md-2">
												<label for="process_id">Required In</label>
												<select id="processId" name="process_id" class="form-control select2 req">
													<?php                                                                        
														if(!in_array(3,array_column($process,'process_id'))){
															?>
															<option value="3">RM Cutting</option>
															<?php
														}
														foreach($process as $row):
															echo '<option value="'.$row->process_id.'" >'.$row->process_name.'</option>';
														endforeach;
													?>
												</select>
											</div>

											<div class="col-md-4">
												<label for="kit_item_id">Item To Be Used</label>
												<select id="kit_item_id" name="kit_item_id" class="form-control select2 req">
													<option value="">Select Item</option>
													<?php
														foreach($rawMaterial as $row):
															echo '<option value="'.$row->id.'" data-unit_id="'.$row->unit_id.'">'.$row->item_code.' - '.$row->item_name.'</option>';
														endforeach;
													?>
												</select>
											</div>

											<div class="col-md-3">
												<label for="ref_id">Alternative Of</label>
												<select id="ref_id" name="ref_id" class="form-control select2">
													<?=((!empty($mbOptions)) ? $mbOptions : '')?>
												</select>
											</div>

											<div class="col-md-3">
												<label for="kit_item_qty">Consumption Qty</label>
												<div class="input-group">
													<input type="text" id="kit_item_qty" name="kit_item_qty" class="form-control floatOnly req" value="" min="0" />
													<div class="input-group-append">
														<?php
															$param = "{'formId':'addProductKitItems','fnsave':'saveProductKit','res_function':'getProductKitHtml'}";
														?>
														<button type="button" class="btn waves-effect waves-light btn-outline-success btn-save save-form float-right" onclick="customStore(<?=$param?>)" style="height:36px"><i class="fa fa-check"></i> Save</button>
													</div>
												</div>                
											</div>
										</div>
									</div>
								</form>
								<hr>
								<div class="card-body">
									<div class="table-responsive">
										<table id="productKit" class="table table-bordered align-items-center">
											<thead class="thead-info">
												<tr class="text-center">
													<th style="width:5%;">#</th>
													<th>Process</th>
													<th>Item Code</th>
													<th>Item Name</th>
													<th>Is Alt.?</th>
													<th>Bom Qty</th>
													<th style="width:10%;">Action</th>
												</tr>
											</thead>
											<tbody id="kitItems">
											</tbody>
										</table>
									</div>
								</div>
							</div>
							<!-- BOM End -->

							<!-- Process Start -->
							<div class="tab-pane fade" id="process" role="tabpanel" aria-labelledby="pills-process-tab">
								<form id="viewProductProcess" data-res_function="getProductProcessHtml">
									<div class="card-body">
										<div class="row">
											<div class="float-right">
												<div class="col-md-4 float-right" hidden>
													<label for="production_type">Production Flow</label>
													<select name="production_type" id="production_type" class="form-control select2">
														<option value="1" <?=(!empty($itemData->production_type) && $itemData->production_type == 1)?"selected":""?>>Manual Flow</option>
														<option value="2" <?=(!empty($itemData->production_type) && $itemData->production_type == 2)?"selected":""?>>Fixed Flow</option> 
													</select>
												</div>
												<div class="col-md-4 form-group float-right">
													<label for="cutting_flow">Cutting ?</label>
													<select name="cutting_flow" id="cutting_flow" class="form-control select2 productionSetting">
														<option value="1" <?=(!empty($itemData->cutting_flow) && $itemData->cutting_flow == 1)?"selected":""?>>No</option>
														<option value="2" <?=(!empty($itemData->cutting_flow) && $itemData->cutting_flow == 2)?"selected":""?>>Yes</option> 
													</select>
												</div>
											</div>                                                        
										</div>

										<div class="row">
											<input type="hidden" name="id" id="id"  value="" />
											<input type="hidden" name="item_id" id="item_id" value="<?= (!empty($itemData->id)) ? $itemData->id : ""; ?>" />
											<input type="hidden" name="form_type" id="form_type" value="viewProductProcess">

											<div class="col-md-10 form-group">
												<label for="process_id">Production Process</label>
												<select name="process_id" id="processId" class="form-control select2">
													<option value="">Select Process</option>
													<?php
													foreach ($processDataList as $row) :
														echo '<option value="' . $row->id . '">' . $row->process_name . '</option>';
													endforeach;
													?>
												</select>
											</div>

											<div class="col-md-2">
												<label for="process_id">&nbsp;</label>
												<?php $param = "{'formId':'viewProductProcess','fnsave':'saveProductProcess','res_function':'getProductProcessHtml'}"; ?>
												<button type="button" class="btn btn-block waves-effect waves-light btn-outline-success btn-save save-form float-right" onclick="customPrcStore(<?=$param?>)" style="height:36px"><i class="fa fa-check"></i> Add</button>
											</div>
										</div>
									</div>
								</form>
								<hr style="margin:0px;">
								<div class="card-body">
									<h6 style="color:#ff0000;font-size:1rem;"><i>Note : Drag & Drop Row to Change Process Sequence</i></h6>
									<div class="table-responsive">
										<table id="itemProcess" class="table excel_table table-bordered">
											<thead class="thead-info">
												<tr class="text-center">
													<th style="width:10%;">#</th>
													<th style="width:70%;">Process Name</th>
													<th style="width:20%;">Sequence</th>
													<th style="width:20%;">Action</th>
												</tr>
											</thead>
											<tbody id="itemProcessData">
											</tbody>
										</table>
									</div>
								</div>
							</div>
							<!-- Process End -->

							<!-- Cycle Time Start -->
							<div class="tab-pane fade" id="cycleTime" role="tabpanel" aria-labelledby="pills-cycleTime-tab">
								<form id="addCycleTime" data-res_function="getCycleTimeHtml">
									<div class="card-body">
										<div class="row">
											<input type="hidden" name="item_id" id="item_id" value="<?= (!empty($itemData->id)) ? $itemData->id : ""; ?>" />
											<h6 style="color:#ff0000;font-size:1rem;"><i>Note : Cycle Time Per Piece In Seconds</i></h6>
											<table id="ctTable" class="table excel_table table-bordered">
												<thead class="thead-info">
													<tr class="text-center">
														<th style="width:3%;">#</th>
														<th style="width:17%;">Process Name</th>
														<th style="width:10%;">Process No.</th>
														<th style="width:10%;">Cycle Time <small>(Seconds)</small></th>
														<th style="width:10%;">Finished Weight</th>
														<th style="width:10%;">Process Cost</th>
														<th style="width:10%;">Cost Per Unit</th>
														<th style="width:10%;">Output Qty</th>
														<th style="width:20%;">MFG Instruction</th>
														<th style="width:10%;">Drawing File</th>
														<th style="width:10%;">Download File</th>
														<th style="width:10%;">Action</th>
													</tr>
												</thead>
												<tbody id="ctBody">
												</tbody>
											</table>
											<div class="col-md-12">
												<?php 
													$param = "{'formId':'addCycleTime','fnsave':'saveCT','res_function':'getCycleTimeHtml'}"; 
												?>
												<button type="button" class="btn waves-effect waves-light btn-outline-success btn-save save-form float-right" onclick="customStore(<?=$param?>)" style="height:36px"><i class="fa fa-check"></i> Save</button>
											</div>
										</div>
									</div>
								</form>
							</div>
							<!-- Cycle Time End -->

							<!-- Die BOM Start -->
							<div class="tab-pane fade" id="dieBom" role="tabpanel" aria-labelledby="pills-dieBom-tab">
								<form id="addDieBom" data-res_function="getDieBomHtml">
									<div class="card-body">
										<div class="row">
											<input type="hidden" name="id" id="id" value="" />
											<input type="hidden" name="item_id" id="item_id" value="<?=(!empty($itemData->id))?$itemData->id:""?>" />
											<input type="hidden" name="form_type" id="form_type" value="addDieBom" />

											<div class="col-md-4">
												<label for="ref_cat_id">Category</label>
												<select id="ref_cat_id" name="ref_cat_id" class="form-control select2 req">
													<option value="">Select Category</option>
													<?php
														foreach($categoryList as $row):
															echo '<option value="'.$row->id.'" >'.$row->category_name.'</option>';
														endforeach;
													?>
												</select>
											</div>

											<div class="col-md-4">
												<label for="ref_item_id">Used To Be Item</label>
												<select id="refItemId" name="ref_item_id" class="form-control select2 req" style="min-width:100px;">
													<option value="">Select Item</option>
													<?php
														foreach($dieMaterial as $row):
															echo '<option value="'.$row->id.'">'.(!empty($row->item_code)?'['.$row->item_code.'] ':'').$row->item_name.'</option>';
														endforeach;
													?>
												</select>
											</div>

											<div class="col-md-4">
												<label for="qty">Bom Qty<small>(KGS)</small></label>
												<div class="input-group">
													<input type="text" id="qty" name="qty" class="form-control floatOnly req" value="" />
													<div class="input-group-append">
														<?php $param = "{'formId':'addDieBom','fnsave':'saveDieBom','res_function':'getDieBomHtml'}"; ?>
														<button type="button" class="btn waves-effect waves-light btn-outline-success btn-save save-form float-right" onclick="customStore(<?=$param?>)" style="height:36px"><i class="fa fa-check"></i> Save</button>
													</div>
												</div>
											</div>
										</div>
									</div>
								</form>
								<hr>
								<div class="card-body">
									<div class="table-responsive">
										<table id="dieBomTbl" class="table table-bordered align-items-center">
											<thead class="thead-info">
												<tr class="text-center">
													<th style="width:5%;">#</th>
													<th>Category</th>
													<th>Item Name</th>
													<th>Bom Qty<small>(KGS)</small></th>
													<th style="width:10%;">Action</th>
												</tr>
											</thead>
											<tbody id="bomItems">
											</tbody>
										</table>
									</div>
								</div>
							</div>
							<!-- Die BOM End -->

							<!-- Packing Standard Start -->
							<div class="tab-pane fade" id="packStandard" role="tabpanel" aria-labelledby="pills-packStandard-tab">
								<form id="addPackingStandard" data-res_function="getPackingStandardHtml">
									<div class="card-body">
										<div class="row">
											<input type="hidden" name="id" id="id" value="" />
											<input type="hidden" name="item_id" id="item_id" value="<?=(!empty($itemData->id))?$itemData->id:""?>" />
											<input type="hidden" name="form_type" id="form_type" value="addPackingStandard" />
											<div class="col-md-2">
												<label for="packing_type">Packing Type</label>
												<select name="packing_type" id="packing_type" class="form-control">
													<option value="1">Primary Packing</option>
													<!--<option value="2">Final Pcking</option>-->
												</select>
											</div>
											<div class="col-md-2">
												<label for="group_name">Standard</label>
												<select name="group_name" id="group_name" class="form-control">
													<option value="Standard 1">Standard 1</option>
													<option value="Standard 2">Standard 2</option>
													<option value="Standard 3">Standard 3</option>
												</select>
											</div>
											<div class="col-md-3">
												<label for="ref_item_id">Packing Material</label>
												<select id="ref_item_id" name="ref_item_id" class="form-control select2 req">
													<option value="">Select Material</option>
													<?php
													if(!empty($itemList)):
														foreach($itemList as $row):
															echo '<option data-wt_pcs="'.floatval($row->wt_pcs).'" value="'.$row->id.'">'.(!empty($row->item_code) ? '['.$row->item_code.'] ' : '').$row->item_name.'</option>';
														endforeach;
													endif;
													?>
												</select>
											</div>

											<div class="col-md-2">
												<label for="qty">Qty Per Box</label>
												<input type="text" id="qty" name="qty" class="form-control floatOnly req" value="" />
											</div>

											<div class="col-md-3">
												<label for="pack_wt">Packing Weight(KGS)</label>
												<div class="input-group">
													<input type="text" id="pack_wt" name="pack_wt" class="form-control floatOnly req" value="" />
													<div class="input-group-append">
														<?php
															$param = "{'formId':'addPackingStandard','fnsave':'savePackingStandard','res_function':'getPackingStandardHtml'}";
														?>
														<button type="button" class="btn waves-effect waves-light btn-outline-success btn-save save-form float-right" onclick="customStore(<?=$param?>)" style="height:36px"><i class="fa fa-check"></i> Save</button>
													</div>
												</div>                
											</div>
										</div>
									</div>
								</form>
								<hr>
								<div class="card-body">
									<div class="table-responsive">
										<table id="packingTbl" class="table table-bordered align-items-center">
											<thead class="thead-info">
												<tr class="text-center">
													<th style="width:5%;">#</th>
													<th>Packing Type</th>
													<th>Standard</th>
													<th>Packing Material</th>
													<th>Qty Per Box</th>
													<th>Packing Weight(KGS)</th>
													<th style="width:10%;">Action</th>
												</tr>
											</thead>
											<tbody id="packingBody">
											</tbody>
										</table>
									</div>
								</div>
							</div>
							<!-- Packing Standard End -->
							
							<!-- Tool Bom  Start -->
							<div class="tab-pane fade" id="toolBom" role="tabpanel" aria-labelledby="pills-toolBom-tab">
								<form id="addToolBom" data-res_function="toolBomHtml">
									<div class="card-body">
										<div class="row">
											<input type="hidden" name="id" id="id" value="" />
											<input type="hidden" name="item_id" id="item_id" value="<?=(!empty($itemData->id))?$itemData->id:""?>" />

											<div class="col-md-3 form-group">
												<label for="process_id">Process</label>
												<select name="process_id" id="processIds" class="form-control select2">
													<option value="">Select Process</option>
													<?php
														foreach($process as $row):
															echo '<option value="'.$row->process_id.'">'.$row->process_name.'</option>';
														endforeach;
													?>
												</select>
											</div>
											<div class="col-md-3 form-group">
												<label for="tool_id">Tool Name</label>
												<select id="tool_id" name="tool_id" class="form-control select2 req">
													<option value="">Select Tool Name</option>
													<?php
														foreach($consumable as $row):
															echo '<option value="'.$row->id.'">'.(!empty($row->item_code)?'['.$row->item_code.'] ':'').$row->item_name.'</option>';
														endforeach;
													?>
												</select>
											</div>

											<div class="col-md-3 form-group">
												<label for="tool_life">Tool Life</label>
												<input type="text" name="tool_life" id="tool_life" class="form-control floatOnly" value="" />
											</div>

											<div class="col-md-3 form-group">
												<label for="cutting_lenght">Cutting Lenght</label>
												<input type="text" name="cutting_lenght" id="cutting_lenght" class="form-control" value="" />
											</div>

											<div class="col-md-3 form-group">
												<label for="no_of_pass">No Of Pass</label>
												<input type="text" name="no_of_pass" id="no_of_pass" class="form-control floatOnly" value="" />
											</div>

											<div class="col-md-3 form-group">
												<label for="rpm">RPM</label>
												<input type="text" name="rpm" id="rpm" class="form-control" value="" />
											</div>

											<div class="col-md-3 form-group">
												<label for="feed">Feed</label>
												<input type="text" name="feed" id="feed" class="form-control" value="" />
											</div>

											<div class="col-md-3">
												<label for="part_life">Part Life(Nos)</label>
												<div class="input-group">
													<input type="text" id="part_life" name="part_life" class="form-control floatOnly" value="" />
													<div class="input-group-append">
														<?php
															$param = "{'formId':'addToolBom','fnsave':'saveToolBom','res_function':'toolBomHtml'}";
														?>
														<button type="button" class="btn waves-effect waves-light btn-outline-success btn-save save-form float-right" onclick="customStore(<?=$param?>)" style="height:36px"><i class="fa fa-check"></i> Save</button>
													</div>
												</div>                
											</div>
										</div>
									</div>
								</form>
								<hr>
								<div class="card-body">
									<div class="table-responsive">
										<table id="toolBomTbl" class="table table-bordered align-items-center">
											<thead class="thead-info">
												<tr class="text-center">
													<th style="width:5%;">#</th>
													<th>Process</th>
													<th>Tool Name</th>
													<th>Tool Life</th>
													<th>Cutting Lenght</th>
													<th>No. Of Pass</th>
													<th>RPM</th>
													<th>Feed</th>
													<th>Part Life(Nos)</th>
													<th style="width:10%;">Action</th>
												</tr>
											</thead>
											<tbody id="toolBomBody">
											</tbody>
										</table>
									</div>
								</div>
							</div>
							<!-- Tool Bom End -->
							
							<!-- TC Parameter Start -->
							<div class="tab-pane fade" id="tcParam" role="tabpanel" aria-labelledby="pills-tcParam-tab">
								<form id="addTcParam" data-res_function="getTcParamResponse">
									<div class="card-body">
										<div class="row">
											<input type="hidden" name="item_id" id="item_id" value="<?= (!empty($itemData->id)) ? $itemData->id : ""; ?>" />
											<input type="hidden" name="grade_id" id="grade_id" value="<?= (!empty($itemData->grade_id)) ? $itemData->grade_id : ""; ?>" />
											<?php
												$tc = [];
												if(!empty($tcData)){
													foreach($tcData as $row){
														$tc[$row->test_type]=$row;
													}
												}
												
												$tcHeads = array_reduce($tcHeadList, function($tcHeads, $head) { $tcHeads[$head->test_name][] = $head; return $tcHeads; }, []);
												foreach ($tcHeads as $head_name => $heads):
													$jsonData = new stdClass();
													$id="";$insp_type = "";
													if(!empty($tc[$heads[0]->test_type])){
														$jsonData = json_decode($tc[$heads[0]->test_type]->parameter);
														$id = $tc[$heads[0]->test_type]->id;
														$ins_type = $tc[$heads[0]->test_type]->ins_type;
													}
													?>
													<div class="col-md-6"><h6><?=$heads[0]->head_name?> [ <?=$head_name?> ] :</h6></div>
													<div class="col-md-6 float-end">
														<div class="col-md-4 form-group float-end">
															<label for="ins_type">Inspection Type</label>
															<select name="ins_type[]" class="form-control float-end">
																<option value="">NA</option>
																<option value="GRN" <?=(!empty($ins_type) && $ins_type == 'GRN')?'selected':''?>>GRN</option>
																<option value="FIR" <?=(!empty($ins_type) && $ins_type == 'FIR')?'selected':''?>>FIR</option>
															</select>
														</div>
													</div>
													<input type="hidden" name="test_type[]"  value="<?=$heads[0]->test_type?>">
													<input type="hidden" name="id[]"  value="<?=$id?>">
													<?php
													$thead = '';$tbody = '';
													
													foreach ($heads as $row):
														$colspan="";$placeholder = "";$cls ='floatOnly';$nm='';
														if($row->requirement == 1){ $colspan = 2; $placeholder='Min - Max';}
														if($row->requirement == 2){$placeholder = "Min";$nm='min';}
														elseif($row->requirement == 3){$placeholder = "Max";$nm='max';}
														elseif($row->requirement == 4){$placeholder = "";$cls="";$nm='other';}
														$thead .='<th colspan="'.$colspan.'" class="text-center">'.((count($heads) > 1) ? $row->parameter : $row->test_name).' <br>'.(($row->requirement == 4) ? '' : '( '.$placeholder.' )').'<input type="hidden" name="param['.$row->test_type.']['.$row->id.'][param]" value="'.$row->parameter.'"></th>';
														if($row->requirement == 1){
															
															$tbody .= '<td style="min-width:100px;"><input type="text" class="form-control text-center" name="param['.$row->test_type.']['.$row->id.'][min]"  placeholder="Min" value="'.(!empty($jsonData->{str_replace(" ","",$row->parameter)}->min)?$jsonData->{str_replace(" ","",$row->parameter)}->min:'').'"></td>';
															$tbody .= '<td style="min-width:100px;"><input type="text" class="form-control text-center" name="param['.$row->test_type.']['.$row->id.'][max]"  placeholder="Max" value="'.(!empty($jsonData->{str_replace(" ","",$row->parameter)}->max)?$jsonData->{str_replace(" ","",$row->parameter)}->max:'').'"></td>';
														}else{
														
															$tbody .= '<td style="min-width:100px;"><input type="text" class="form-control text-center'.$cls.'" name="param['.$row->test_type.']['.$row->id.']['.$nm.']" placeholder="'.$placeholder.'" value="'.(!empty($jsonData->{str_replace(" ","",$row->parameter)}->{$nm})?$jsonData->{str_replace(" ","",$row->parameter)}->{$nm}:'').'"></td>';
														}       
													endforeach;
													?>
													<div class="col-md-12 form-group">
															<div class="table-responsive">
																<table class="table table-bordered">
																	<thead class="thead-info">
																		<tr> <?=$thead?> </tr>
																	</thead>
																	<tbody>
																		<tr> <?=$tbody?>  </tr>
																	</tbody>
																</table>
															</div>
														</div>
													<?php
												endforeach;
											?>
											<div class="col-md-12">
												<?php 
													$param = "{'formId':'addTcParam','fnsave':'saveInspectionParam','controller':'materialGrade'}"; 
												?>
												<button type="button" class="btn waves-effect waves-light btn-outline-success btn-save save-form float-right" onclick="customStore(<?=$param?>)" style="height:36px"><i class="fa fa-check"></i> Save</button>
											</div>
										</div>
									</div>
								</form>
							</div>
							<!-- TC Parameter End -->
							
							<!-- TC Specification Start -->
							<div class="tab-pane fade" id="tcSpecification" role="tabpanel" aria-labelledby="pills-tcSpecification-tab">
								<form id="addTcSpecification" data-res_function="getTcSpecificationResponse">
									<div class="card-body">
										<div class="row">
											<input type="hidden" name="id" id="id" value="<?= (!empty($tcSpecification->id) ? $tcSpecification->id : "");?>" />
											<input type="hidden" name="item_id" id="item_id" value="<?= (!empty($itemData->id)) ? $itemData->id : ""; ?>" />
											<div class="col-md-4 form-group">
												<label for="forging_prc">Forging Process</label>
												<input type="text" name="forging_prc" id="forging_prc" class="form-control" value="<?= (!empty($tcSpecification->forging_prc) ? $tcSpecification->forging_prc : "");?>" />
											</div>
											<div class="col-md-4 form-group">
												<label for="dimensional_insp">Dimensional Inspection</label>
												<input type="text" name="dimensional_insp" id="dimensional_insp" class="form-control" value="<?= (!empty($tcSpecification->dimensional_insp) ? $tcSpecification->dimensional_insp : "");?>" />
											</div>
											<div class="col-md-4 form-group">
												<label for="visual_insp">Visual Inspection</label>
												<input type="text" name="visual_insp" id="visual_insp" class="form-control" value="<?= (!empty($tcSpecification->visual_insp) ? $tcSpecification->visual_insp : "");?>" />
											</div>
											<div class="col-md-12 form-group">
												<label for="note">Note</label>
												<input type="text" name="note" id="note" class="form-control" value="<?= (!empty($tcSpecification->note) ? $tcSpecification->note : "");?>" />
											</div>
											<div class="col-md-12 form-group">
												<label for="special_req">Special Requirement</label>
												<input type="text" name="special_req" id="special_req" class="form-control" value="<?= (!empty($tcSpecification->special_req) ? $tcSpecification->special_req : "");?>" />
											</div>
											<div class="col-md-12 form-group">
												<label for="remark">Remark</label>
												<input type="text" name="remark" id="remark" class="form-control" value="<?= (!empty($tcSpecification->remark) ? $tcSpecification->remark : "");?>" />
											</div>
											<div class="col-md-12">
												<?php 
													$param = "{'formId':'addTcSpecification','fnsave':'saveTcSpecification','controller':'items'}"; 
												?>
												<button type="button" class="btn waves-effect waves-light btn-outline-success btn-save save-form float-right" onclick="customStore(<?=$param?>)" style="height:36px"><i class="fa fa-check"></i> Save</button>
											</div>
										</div>
									</div>
								</form>
							</div>
							<!-- TC Specification End -->
							
							<!-- Revision Start -->
							<div class="tab-pane fade show" id="revision" role="tabpanel" aria-labelledby="pills-revision-tab">
								<form id="addRevision" data-res_function="getRevisionHtml">

									
									<div class="card-body">
										<input type="hidden" name="item_id" id="item_id" value="<?=(!empty($itemData->id))?$itemData->id:""?>" />

										<div class="table-responsive">
											<table id="revisionTbl" class="table table-bordered align-items-center">
												<thead class="thead-info">
													<tr class="text-center">
														<th style="width:5%;">#</th>
														<th>Drawing No</th>
														<th>Cust. Rev. No</th>
														<th>Cust. Rev. Date</th>
														<th>Rev No</th>
														<th>Rev Date</th>
														<th>Status</th>
														<th>Customer Drg. File</th>
														<th>Company Drg. File</th>
														<th style="width:20%;">Action</th>
													</tr>
												</thead>
												<tbody id="revisionBody"></tbody>
											</table>
										</div>
									</div>
								</form>
							</div>
							<!-- Revision End -->

							<!-- Parameter Start -->
							<div class="tab-pane fade" id="parameter" role="tabpanel" aria-labelledby="pills-parameter-tab">
								<form id="addParameter" data-res_function="inspectionHtml">
									<div class="card-body">
										<div class="row">
											<input type="hidden" name="id" id="id" value="" />
											<input type="hidden" name="item_id" id="item_id" value="<?=(!empty($itemData->id))?$itemData->id:""?>" />

											<div class="error general_error"></div>
											
											<div class="col-md-2 form-group">
												<label for="rev_no">Revision No.</label>
												<select name="rev_no" id="rev_no" class="form-control select2 req">
													<option value="">Select Revision No.</option>
													<?php
														foreach($revisionList as $row):
															echo '<option value="'.$row->rev_no.'">'.$row->rev_no.'</option>';
														endforeach;
													?>
												</select>
												<div class="error rev_no"></div>
											</div>
											<div class="col-md-2 form-group">
												<label for="process_id">Process</label>
												<select name="process_id" id="process_id" class="form-control select2 req">
													<option value="">Select Process</option>
													<?php
														if(!in_array(3,array_column($processList,'process_id'))){
															?>
															<option value="3" >RM Cutting</option>
															<?php
														}
														foreach($process as $row):
															echo '<option value="'.$row->process_id.'">'.$row->process_name.'</option>';
														endforeach;
													?>
												</select>
												<div class="error process_id"></div>
											</div>
											<div class="col-md-2 form-group">
												<label for="param_type">Parameter Type</label>
												<select name="param_type" id="param_type" class="form-control req">
													<option value="1">Product</option>
													<option value="2">Process</option>
												
												</select>
												<div class="error param_type"></div>
											</div>
											<div class="col-md-3 form-group">
												<label for="parameter">Parameter</label>
												<input type="text" name="parameter" id="parameter" class="form-control req" value="" />
											</div>
											<div class="col-md-3 form-group">
												<label for="specification">Specification</label>
												<input type="text" name="specification" id="specification" class="form-control req" value="" />
											</div>
											<div class="col-md-2 form-group">
											<div class="input-group">
												<div class="input-group-append" style="width:50%;">
													<label for="min">Min</label>
													<input type="text" name="min" id="min" class="form-control floatOnly" value="" placeholder="Min"/>
												</div>
												<div class="input-group-append" style="width:50%;">
													<label for="max">Max</label>
													<input type="text" name="max" id="max" class="form-control floatOnly" value="" placeholder="Max"/>
												</div>
											</div>
											</div>
											<div class="col-md-2 form-group">
												<label for="machine_tool">Machine Tools(MFG)</label>
												<select name="machine_tool" id="machine_tool" class="form-control select2">
													<option value="">Select Machine Tool</option>
													<?php
													if(!empty($machineList)){
														foreach($machineList as $row){
															echo '<option value="'.$row->id.'">'.$row->category_name.'</option>';
														}
													}
													?>
												</select>
											</div>
											<div class="col-md-2 form-group">
												<label for="instrument">Instrument</label>
												<input type="text" name="instrument" id="instrument" class="form-control">
											</div>
											<div class="col-md-3 form-group">
												<label for="char_class">Special Char. Class</label>
												<select name="char_class" id="char_class" class="form-control symbl1 select2">
													<option value="">Select</option>
													<?php
														foreach($this->classArray AS $key=>$symbol){ 
															if(!empty($symbol)){
																//echo '<option value="'.$key.'" data-img_path="'.base_url('/assets/images/symbols/'.$key.'.png').'"> '.$symbol.'</option>';
																echo '<option value="'.$key.'" > '.$symbol.'</option>';
															}
														}
													?>
												</select>
											</div>
											<div class="col-md-3 form-group">
											<div class="input-group">
												<div class="input-group-append" style="width:33%;">
													<label for="size">Size</label>
													<input type="text" name="size" id="size" class="form-control">
												</div>
												<div class="input-group-append" style="width:33%;">
													<label for="frequency">Frequency</label>
													<input type="text" name="frequency" id="frequency" class="form-control numericOnly">
												</div>
												<div class="input-group-append" style="width:34%;">
													<label for="freq_unit">Frequency Unit</label>
													<select name="freq_unit" id="freq_unit" class="form-control select2">
														<option value="Hrs">Hrs</option>
														<option value="Lot">Lot</option>
													</select>
												</div>
											</div>
											</div>
											<div class="col-md-4 form-group">
											<div class="input-group">
												<div class="input-group-append" style="width:34%;">
													<label for="tool_name">Tool Name</label>
													<input type="text" name="tool_name" id="tool_name" class="form-control">
												</div>
												<div class="input-group-append" style="width:33%;">
													<label for="rpm">RPM</label>
													<input type="text" name="rpm" id="rpm" class="form-control">
												</div>
												<div class="input-group-append" style="width:33%;">
													<label for="feed">Feed</label>
													<input type="text" name="feed" id="feed" class="form-control">
												</div>
											</div>
											</div>
											<div class="col-md-5 form-group">
												<label for="reaction_plan">Reaction Plan</label>
												<input type="text" name="reaction_plan" id="reaction_plan" class="form-control">
											</div>
											<div class="col-md-3 form-group">
												<label for="control_method">Control Method</label>
												<div class="input-group">
													<div class="input-group-append" style="width:73%;">
													<select name="control_method[]" id="control_method" class="form-control select2 req" multiple>
														<option value="IIR">IIR (Incoming Inspection Report)</option>
														<option value="SAR">SAR (Setup Approval Report)</option>
														<option value="IPR">IPR (Inprocess Inspection Report)</option>
														<option value="FIR">FIR (Final Inspection Report)</option>
													</select>
													<div class="error control_method"></div>
													</div>
													<div class="input-group-append" style="width:27%;">
														<?php $param = "{'formId':'addParameter','fnsave':'saveInspection','controller':'items','res_function':'inspectionHtml'}"; ?>
														<button type="button" class="btn waves-effect waves-light btn-outline-success float-right save-form" onclick="customStore(<?=$param?>)"><i class="fa fa-check"></i> Save</button>
													</div>        
												</div>
											</div>
										</div>
										<hr>
										<div class="row">
											<div class="col-md-4">
											
											</div>
											<div class="col-md-2">
												<a href="<?= base_url($headData->controller . '/createProductInspExcel/' . $itemData->id.'/' ) ?>" class="btn btn-block btn-info bg-info-dark mr-2" target="_blank">
													<i class="fa fa-download"></i> Download</span>
												</a>
											</div>
											<div class="col-md-4">
												<input type="file" name="insp_excel" id="insp_excel" class="form-control float-left" />
												<h6 class="col-md-12 msg text-primary text-center mt-1"></h6>
											</div>
											<div class="col-md-2">
												<a href="javascript:void(0);" class="btn btn-block btn-success bg-success-dark ml-2 importProductExcel" type="button">
													<i class="fa fa-upload"></i> Upload</span>
												</a>
											</div>
										</div>
									</div>
								</form>
								<hr>
								<div class="card-body">
									<div class="row" id="inspectionId">
										<div class="col-md-12" id="inspectionBody" >
										</div> 
									</div>
								</div>
								<div class="card-body">
									<div class="row">
										<div class="col-md-12">
										
											<div class="table-responsive">
												<table class="table table-bordered">
													<thead id="theadData" class="thead-info"></thead>
													<tbody id="tbodyData"></tbody>
												</table>
											</div>
										</div>
									</div>
								</div>
							</div>
							<!-- Parameter End -->
							
							<!-- Pop Parameter Start -->
							<div class="tab-pane fade" id="popParameter" role="tabpanel" aria-labelledby="pills-popParameter-tab">
								<form id="addPopInsp" data-res_function="popInspectionHtml">
									<div class="card-body">
										<div class="row">
											<input type="hidden" name="id" id="id" value="" />
											<input type="hidden" name="item_id" id="item_id" value="<?=(!empty($itemData->id))?$itemData->id:""?>" />
            								<input type="hidden" name="control_method" id="control_method" value="POP" />

											<div class="col-md-4 form-group">
												<label for="category_id">Category</label>
												<select name="category_id[]" id="category_id" class="form-control select2 req" multiple>
													<?php
													if(!empty($categoryList)){
														foreach($categoryList as $row){
															echo '<option value="'.$row->id.'">'.$row->category_name.'</option>';
														}
													}
													?>
												</select>
												<div class="error category_id"></div>
											</div>
											<div class="col-md-5 form-group">
												<label for="parameter">Parameter</label>
												<input type="text" name="parameter" id="parameter" class="form-control req" value="" />
											</div>
											<div class="col-md-3 form-group">
												<label for="specification">Specification</label>
												<input type="text" name="specification" id="specification" class="form-control req" value="" />
											</div>
											<div class="col-md-3 form-group">
												<label for="min">Min</label>
												<input type="text" name="min" id="min" class="form-control floatOnly" value="" />
											</div>
											<div class="col-md-3 form-group">
												<label for="max">Max</label>
												<input type="text" name="max" id="max" class="form-control floatOnly" value="" />
											</div>
											<div class="col-md-6 form-group">
												<label for="instrument">Instrument</label>
												<div class="input-group">
													<input type="text" name="instrument" id="instrument" class="form-control" value="" />
													<div class="input-group-append">
														<?php $param = "{'formId':'addPopInsp','fnsave':'saveInspection','controller':'items','res_function':'popInspectionHtml'}"; ?>
														<button type="button" class="btn waves-effect waves-light btn-outline-success save-form" onclick="customStore(<?=$param?>)"><i class="fa fa-check"></i> Save</button>
													</div>
												</div>
											</div>
										</div>
										<hr>
										<div class="row">
											<div class="col-md-4"></div>
											<div class="col-md-2">
												<a href="<?= base_url($headData->controller . '/createPopInspExcel/' . $itemData->id.'/')?>" class="btn btn-block btn-info bg-info-dark mr-2" target="_blank">
													<i class="fa fa-download"></i> Download</span>
												</a>
											</div>
											<div class="col-md-4">
												<input type="file" name="insp_excel" id="insp_excel" class="form-control float-left" />
												<h6 class="col-md-12 msg text-primary text-center mt-1"></h6>
											</div>
											<div class="col-md-2">
												<a href="javascript:void(0);" class="btn btn-block btn-success bg-success-dark ml-2 importPopExcel" type="button">
													<i class="fa fa-upload"></i> Upload</span>
												</a>
											</div>
										</div>
									</div>
								</form>
								<hr>
								<div class="card-body">
									<div class="table-responsive">
										<table id="popInspectionId" class="table table-bordered align-items-center">
											<thead class="thead-info">
												<tr>
													<th style="width:5%;">#</th>
													<th style="width:30%;">Category</th>
													<th style="width:20%;">Parameter</th>
													<th style="width:20%;">Specification</th>
													<th style="width:10%;">Min</th>
													<th style="width:10%;">Max</th>
													<th style="width:20%;">Instrument</th>
													<th class="text-center" style="width:5%;">Action</th>
												</tr>
											</thead>
											<tbody id="popInspectionBody">
											</tbody>
										</table>
									</div>
								</div>
							</div>
							<!-- Pop Parameter End -->
						</div>        
					</div>
				</div>
			</div>
		</div>
	</div>
</div>
<?php $this->load->view('includes/footer'); ?>
<script src="<?php echo base_url();?>assets/js/custom/product.js"></script>

<script>
$(document).ready(function(){
	$(document).on('click','.process_tab',function(){ 
		$('.process_tab').removeClass('active');
		$(this).addClass('active'); 
	});
	
	$(document).on('change', '#ref_item_id', function () {
		var wt_pcs = $("#ref_item_id :selected").data('wt_pcs');
		$('#pack_wt').val(wt_pcs);
	});
	
});
</script>