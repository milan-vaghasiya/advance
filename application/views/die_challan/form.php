<?php $this->load->view('includes/header'); ?>
<div class="page-content-tab">
	<div class="container-fluid">
        <div class="row">
            <div class="col-12">
                <div class="card">
                    <div class="card-body">
                        <form autocomplete="off" id="saveDieChallan" data-res_function="resSaveDieChallan">
                            <div class="col-md-12">
								<div class="row">
									
									<input type="hidden" name="id" value="<?=(!empty($dataRow->id)?$dataRow->id:"")?>" />

									<div class="col-md-2 form-group">
                                        <label for="trans_number">Challan No.</label>
                                        <div class="input-group mb-3">
											<input type="text" name="trans_number" id="trans_number" class="form-control req" value="<?=(!empty($dataRow->trans_number))?$dataRow->trans_number:((!empty($trans_number))?$trans_number:"")?>" readonly />
                                            <input type="hidden" name="trans_prefix" id="trans_prefix" value="<?=(!empty($dataRow->trans_prefix))?$dataRow->trans_prefix:((!empty($trans_prefix))?$trans_prefix:"")?>" />
                                            <input type="hidden" name="trans_no" id="trans_no" value="<?=(!empty($dataRow->trans_no))?$dataRow->trans_no:((!empty($trans_no))?$trans_no:"")?>" />
                                        </div>
									</div>

									<div class="col-md-2 form-group">
										<label for="trans_date">Challan Date</label>
										<input type="date" id="trans_date" name="trans_date" class="form-control req" value="<?=(!empty($dataRow->trans_date))?$dataRow->trans_date:date("Y-m-d")?>" />	
									</div>
									<input type="hidden" id="challan_type" name="challan_type" value="2">

									<div class="col-md-5 form-group">
										<label for="party_id">Issue To</label>
										<select name="party_id" id="party_id" class="form-control select2 req">
											<option value="">Select Party</option>
											<?php
											if(!empty($partyData)):
												foreach($partyData as $row):
													$selected = "";
													if(!empty($dataRow->party_id) && $dataRow->party_id == $row->id){$selected = "selected";}
													echo '<option value="'.$row->id.'" '.$selected.'>'.$row->party_name.'</option>';
												endforeach;
											endif;
											?>
										</select>
									</div>	

									<div class="col-md-12 form-group">
										<label for="remark">Remark</label>
										<input type="text" name="remark" id="remark" class="form-control" value="<?=(!empty($dataRow->remark))?$dataRow->remark:""?>">
									</div>
																		
								</div>
							</div>
							<hr>
                            <div class="col-md-12 row">
                                <div class="col-md-6"><h4>Item Details : </h4></div>
								<div class="col-md-6">
									<button type="button" class="btn btn-success waves-effect float-right add-item"><i class="fa fa-plus"></i> Add Item</button>
								</div>
							</div>														
							<div class="col-md-12 mt-3">
								<div class="error item_name_error"></div>
								<div class="row form-group">
									<div class="table-responsive ">
										<table id="dieChallanItems" class="table table-striped table-borderless">
											<thead class="thead-dark">
												<tr class="text-center">
													<th style="width:5%;">#</th>
													<th style="width:40%">Item Name</th>
													<!-- <th style="width:20%">PRC Number</th> -->
													<th style="width:25%;">Remark</th>
													<th style="width:10%;">Action</th>
												</tr>
											</thead>
											<tbody id="tempItem" class="temp_item">
												<tr id="noData">
													<td colspan="5" class="text-center">No data available in table</td>
												</tr>
											</tbody>
										</table>
									</div>
								</div>
							</div>
                        </form>
                    </div>
                    <div class="card-footer">
						<div class="col-md-12">
							<button type="button" class="btn waves-effect waves-light btn-success float-right save-form" onclick="customStore({'formId':'saveDieChallan'});" ><i class="fa fa-check"></i> Save</button>
                            <button type="button" class="btn btn-secondary press-close-btn btn-close-modal save-form float-right m-r-10" onclick="window.location.href='<?=base_url($headData->controller)?>'"><i class="fa fa-times"></i> Cancel</button>
                        </div>
                    </div>
                </div>
            </div>
        </div>        
    </div>
</div>

<div class="modal modal-right fade" id="itemModel" role="dialog" data-bs-backdrop="static" data-bs-keyboard="false">
    <div class="modal-dialog modal-md" role="document">
        <div class="modal-content animated slideDown">
            <div class="modal-header" style="display:block;"><h4 class="modal-title">Add or Update Item</h4></div>
            <div class="modal-body">
                <form id="itemForm">
                    <div class="col-md-12">

                        <div class="row form-group">
							<div id="itemInputs">
								<input type="hidden" name="id" id="id" value="" />
								<input type="hidden" name="row_index" id="row_index" value="">
                                <input type="hidden" name="item_id" id="item_id" value="" />
                                <input type="hidden" name="die_set_no" id="die_set_no" value="" />
                            </div>   
							
							<div class="col-md-12 form-group">
								<label for="die_id">Die</label>                                
                                <select name="die_id" id="die_id" class="form-control select2 req">
                                    <option value="">Select Die</option>
                                    <?php
                                        foreach($itemData as $row):
											echo "<option value='".$row->id."' data-item_id='".$row->fg_id."' data-die_set_no='".$row->set_no."'>".$row->item_code."</option>";
                                        endforeach;   
                                    ?>
                                </select>
                            </div>
                            <div class="col-md-12 form-group">
                                <label for="item_remark">Remark</label>
								<textarea name="item_remark" id="item_remark"  class="form-control"></textarea>
                            </div>                            
                        </div>
                    </div>          
                </form>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn waves-effect waves-light btn-outline-success saveItem btn-save" data-fn="save"><i class="fa fa-check"></i> Save</button>
                <button type="button" class="btn waves-effect waves-light btn-outline-warning saveItem btn-save-close" data-fn="save_close"><i class="fa fa-check"></i> Save & Close</button>
                <button type="button" class="btn waves-effect waves-light btn-outline-secondary btn-item-form-close" data-bs-dismiss="modal"><i class="fa fa-times"></i> Close</button>
            </div>
        </div>
    </div>
</div>

<?php $this->load->view('includes/footer'); ?>
<script src="<?php echo base_url();?>assets/js/custom/die-challan-form.js?v=<?=time()?>"></script>
<?php
if(!empty($dataRow->itemList)):
    foreach($dataRow->itemList as $row):
        $row->row_index = "";
		$row->item_name = $row->die_code;
        echo '<script>AddRow('.json_encode($row).');</script>';
    endforeach;
endif;
?>