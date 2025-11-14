<?php $this->load->view('includes/header'); ?>
<div class="page-content-tab">
	<div class="container-fluid">
        <div class="row">
            <div class="col-12">
				<div class="col-12">
                    <div class="card">
                        <div class="card-body">
                            <form autocomplete="off" id="savePurchaseDesk" data-res_function="resSavePoDesk" enctype="multipart/form-data">
                                <div class="col-md-12">
                                    <div class="row">
                                        <div class="hiddenInput">
											<input type="hidden" name="id" id="id" value="<?= (!empty($dataRow[0]->id) && empty($is_regenerate)) ? $dataRow[0]->id : ""; ?>" />
											<input type="hidden" name="entry_type" id="entry_type" value="<?= !empty($dataRow[0]->entry_type)?$dataRow[0]->entry_type:((!empty($entry_type)) ? $entry_type : '') ?>" />
											<input type="hidden" name="trans_no" id="trans_no" value="<?=(!empty($dataRow[0]->trans_no) && empty($is_regenerate))?$dataRow[0]->trans_no:$trans_no?>" />
											<input type="hidden" name="trans_prefix" id="trans_prefix" value="<?=(!empty($dataRow[0]->trans_prefix))?$dataRow[0]->trans_prefix:$trans_prefix?>" />
                                        </div>
										
										<div class="col-md-3 form-group">
											<label for="trans_number">Enquiry No.</label>
											<input type="text" name="trans_number" id="trans_number" class="form-control req" value="<?= (!empty($dataRow[0]->trans_number) && empty($is_regenerate)) ? $dataRow[0]->trans_number : $trans_prefix.$trans_no ?>" readonly />
										</div>

										<div class="col-md-3 form-group">
											<label for="trans_date">Enquiry Date</label>
											<input type="date" id="trans_date" name="trans_date" class="form-control req" value="<?= (!empty($dataRow[0]->trans_date)) ? $dataRow[0]->trans_date : date("Y-m-d") ?>" />
										</div>

										<div class="col-md-6 form-group">
											<label for="party_id">Supplier Name</label>
											<select name="party_id[]" id="party_id" class="form-control select2 req" <?= empty($dataRow[0]->party_id) ? 'multiple="multiple"' : "" ;?>> 												
												<?=(!empty($is_regenerate) ? '<option value="">Select Party</option>' : ''); ?>
												<?=getPartyListOption($partyList,((!empty($party_id) && empty($is_regenerate))?$party_id:0))?>
											</select>
											<div class="error party_id"></div>
										</div>
                                    </div>
                                    <hr>

                                    <div class="col-md-12 row">
                                        <div class="col-md-6"><h4>Item Details : </h4></div>
                                        <div class="col-md-6">
                                            <button type="button" class="btn btn-outline-success waves-effect float-right add-item"><i class="fa fa-plus"></i> Add Item</button>
                                        </div>
                                    </div>

                                    <div class="col-md-12 mt-3">
                                        <div class="error itemData"></div>
                                        <div class="row form-group">
                                            <div class="table-responsive">
                                                <table id="purchaseOrderItems" class="table table-striped table-borderless">
                                                    <thead class="thead-dark">
                                                        <tr>
                                                            <th style="width:5%;">#</th>
                                                            <th>Item Type</th>
                                                            <th>Item Name</th>
                                                            <th>Unit.</th>
                                                            <th>Qty.</th>
                                                            <th>Remark</th>
                                                            <th class="text-center" style="width:10%;">Action</th>
                                                        </tr>
                                                    </thead>
                                                    <tbody id="tempItem" class="temp_item">
                                                        <tr id="noData">
                                                            <td colspan="12" class="text-center">No data available in table</td>
                                                        </tr>
                                                    </tbody>
                                                </table>
                                            </div>
                                        </div>
                                    </div>
                                    
                                    <hr>
                                    <div class="row">                                    
                                        <div class="col-md-12 form-group">
                                            <label for="remark">Remark</label>
                                            <input type="text" name="remark" id="remark" class="form-control" value="<?=((!empty($dataRow[0]->remark))?$dataRow[0]->remark:'')?>">
                                        </div> 
                                    </div>

                                    <?php $this->load->view('includes/terms_form',['termsList'=>$termsList,'termsConditions'=>(!empty($dataRow[0]->termsConditions)) ? $dataRow[0]->termsConditions : array()])?>
                                </div>
                            </form>
                        </div>
                        <div class="card-footer bg-facebook">
                            <div class="col-md-12">
                                <button type="button" class="btn btn-info waves-effect show_terms" >Terms & Conditions</button>
                                <span class="term_error text-danger font-bold"></span>
                                
                                
                                <button type="button" class="btn waves-effect waves-light btn-success float-right save-form" onclick="customStore({'formId':'savePurchaseDesk','txt_editor':'conditions','fnsave':'saveEnquiry'});" ><i class="fa fa-check"></i> Save</button>

                                <button type="button" class="btn btn-secondary press-close-btn btn-close-modal save-form float-right m-r-10" onclick="window.location.href='<?=base_url($headData->controller)?>'"><i class="fa fa-times"></i> Cancel</button>
                            </div>
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
					<div id="itemInputs">
						<input type="hidden" name="id" id="id" value="" />
						<input type="hidden" name="req_id" id="req_id" value="" />
						<input type="hidden" name="from_entry_type" id="from_entry_type" value="" />
						<input type="hidden" name="so_trans_id" id="so_trans_id" value="" />
						<input type="hidden" name="row_index" id="row_index" value="">
					</div>
                    <div class="col-md-12">
                        <div class="row form-group">                            
                            <div class="col-md-5 form-group">
								<label for="item_type">Item Type</label>
								<select name="item_type" id="item_type" class="form-control select2 req">
									<option value="">Select Item Type</option>
									<?php
										foreach($categoryList as $row):
											echo '<option value="'.$row->id.'">'.$row->category_name.'</option>';
										endforeach;
									?>
								</select>
							</div>

							<div class="col-md-7 form-group">
								<label for="item_id">Item Name</label>
								<select name="item_id" id="item_id" class="form-control select2 req">
									<option value="">Select Item</option>
									<option value="-1">New Item</option>
								</select>
							</div>

							<div class="col-md-12 form-group newItem">
								<label for="item_name">New Item Name</label>
								<input type="text" name="item_name" id="item_name" class="form-control req" value="" />
							</div>

							<div class="col-md-12 form-group">
                                <label for="fg_item_id">Finish Goods</label>
                                <select name="fg_item_id" id="fg_item_id" class="form-control select2">
                                    <option value="">Select Finish Goods</option>
                                    <?php
                                        echo (!empty($fgoption)? $fgoption :'')
                                      
                                    ?>
                                </select>
                            </div>
							
							<div class="col-md-6 form-group">
								<label for="uom">Unit</label>
								<select name="uom" id="uom" class="form-control select2 req">
									<option value="0">--</option>
									<?php
										foreach($unitData as $row):
											echo '<option value="'.$row->unit_name.'">['.$row->unit_name.'] '.$row->description.'</option>';
										endforeach;
									?>
								</select>
								<div class="error uom"></div>
							</div>

							<div class="col-md-6 form-group">
								<label for="qty">Quantity</label>
								<input type="text" name="qty" id="qty" class="form-control floatOnly req" value="">
							</div>

							<div class="col-md-12 form-group">
								<label for="item_remark">Remark</label>
								<textarea name="item_remark" id="item_remark" class="form-control" rows="2"></textarea>
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
<script src="<?php echo base_url(); ?>assets/js/custom/purchase_desk.js?v=<?=time()?>"></script>
<script src="<?=base_url()?>assets/plugins/tinymce/tinymce.min.js"></script>
<script>
$(document).ready(function(){
    initEditor({
        selector: '#conditions',
        height: 400
    });
});
</script>
<?php 
if(!empty($dataRow)):
    foreach($dataRow as $row):
        $row->row_index = "";
		$row->item_id_name = $row->item_name;
		$row->item_type_name = $row->category_name;
		$row->id = (!empty($dataRow[0]->id) && empty($is_regenerate)) ? $row->id : "";
        echo '<script>AddRow('.json_encode($row).');</script>';
    endforeach;
endif;

if(!empty($indentItemList)):
    foreach($indentItemList as $row): 
        $row->req_id = $row->id;
		$row->item_id_name = $row->item_name;
		$row->item_type_name = $row->category_name;
        $row->from_entry_type = $row->entry_type;
        $row->item_remark = $row->remark;
        $row->entry_type = "";
        $row->row_index = "";
        $row->id = "";
        echo '<script>AddRow('.json_encode($row).');</script>';
    endforeach;
endif;

if(!empty($rmList)):
    $i = 0;
    foreach($rmList as $row): 
        $rm_shortage = $row->required_material - ($row->rm_stock + $row->pending_po + $row->pending_grn);

        $row->fg_item_id = $row->item_id; 
        $row->item_id = $row->ref_item_id;
        $row->so_trans_id = $row->so_trans_id;
		$row->item_id_name = $row->item_name;
		$row->item_type_name = $row->category_name;
        $row->qty = round($rm_shortage,3);
        $row->entry_type = "";
        $row->row_index = "";
        $row->id = "";
        echo '<script>AddRow('.json_encode($row).');</script>';
        $i++;
    endforeach;
endif;
?>