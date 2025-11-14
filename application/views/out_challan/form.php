<?php $this->load->view('includes/header'); ?>
<div class="page-content-tab">
	<div class="container-fluid">
        <div class="row">
            <div class="col-12">
                <div class="card">
                    <div class="card-body">
						<form autocomplete="off" id="saveOutChallan" data-res_function="resSaveChallan">
                            <div class="col-md-12">
                                <div class="row">
                                    <input type="hidden" name="id" value="<?=(!empty($dataRow->id))?$dataRow->id:""?>">
                                    <input type="hidden" name="challan_type" id="challan_type" value="<?=(!empty($dataRow->challan_type))?$dataRow->challan_type:"2"?>">

									<div class="col-md-4 form-group">
                                        <label for="trans_number">Challan No.</label>
                                        <input type="text" name="trans_number" id="trans_number" class="form-control req" value="<?=((!empty($dataRow->trans_number)) ? $dataRow->trans_number : (!empty($trans_number) ? $trans_number : ''))?>" readonly />
                                        <input type="hidden" name="trans_no" id="trans_no" value="<?=((!empty($dataRow->trans_no)) ? $dataRow->trans_no : (!empty($trans_no) ? $trans_no : 0))?>" />
									</div>

									<div class="col-md-4 form-group">
										<label for="trans_date">Challan Date</label>
                                        <input type="date" id="trans_date" name="trans_date" class="form-control req" value="<?=(!empty($dataRow->trans_date) ? $dataRow->trans_date : date("Y-m-d"))?>" />	
									</div>

									<div class="col-md-4 form-group">
                                        <label for="party_id">Party Name</label>
                                        <select name="party_id" id="party_id" class="form-control select2 req">
                                            <option value="">Select Party</option>
                                            <?=getPartyListOption($partyList,(!empty($dataRow->party_id) ? $dataRow->party_id : 0))?>
                                        </select>
                                    </div>
									
									<div class="col-md-12 form-group">
										<label for="remark">Remark</label>
										<input type="text" name="remark" id="remark" class="form-control" placeholder="Enter Remark" value="<?=(!empty($dataRow->remark) ? $dataRow->remark : "")?>">
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
								<div class="error itemData"></div>
								<div class="row form-group">
									<div class="table-responsive ">
										<table id="challanItems" class="table table-striped table-borderless">
											<thead class="thead-dark">
												<tr>
													<th style="width:5%;">#</th>
													<th style="width:30%">Item Name</th>
													<th style="width:20%">Qty.</th>
													<th style="width:15%">Returnable</th>
													<th style="width:20%">Remark</th>
													<th class="text-center" style="width:10%;">Action</th>
												</tr>
											</thead>
											<tbody id="tempItem" class="temp_item">
												<tr id="noData">
													<td colspan="6" class="text-center">No data available in table</td>
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
							<button type="button" class="btn waves-effect waves-light btn-success float-right save-form" onclick="customStore({'formId':'saveOutChallan'});" ><i class="fa fa-check"></i> Save</button>
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
                            </div>                            

                            <div class="col-md-12 form-group">
								<label for="item_id">Item Name</label>                                
                                <select name="item_id" id="item_id" class="form-control select2 req">
                                    <option value="">Select Item Name</option>
                                    <?=getItemListOption($itemList)?>
                                </select>
                            </div>

                            <div class="col-md-6 form-group">
                                <label for="qty">Qty.</label>
                                <input type="text" name="qty" id="qty" class="form-control floatOnly req" value="0">
                            </div>    
   
                            <div class="col-md-6 form-group">
                                <label for="is_returnable">Returnable</label>
                                <select name="is_returnable" id="is_returnable" class="form-control select2">
                                    <option value="NO">NO</option>
                                    <option value="YES">YES</option>
                                </select>
                            </div>

                            <div class="col-md-12 form-group">
                                <label for="item_remark">Item Remark</label>
                                <input type="text" name="item_remark" id="item_remark" class="form-control" value="" />
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
<script src="<?php echo base_url();?>assets/js/custom/out-challan-form.js?v=<?=time()?>"></script>
<?php
if(!empty($dataRow->itemList)):
    foreach($dataRow->itemList as $row):
        $row->row_index = "";
        $row->item_name = (!empty($row->item_code) ? '['.$row->item_code.'] ' : '').$row->item_name;
        echo '<script>AddRow('.json_encode($row).');</script>';
    endforeach;
endif;
?>