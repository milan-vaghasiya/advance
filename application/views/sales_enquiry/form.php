<?php $this->load->view('includes/header'); ?>
<div class="page-content-tab">
	<div class="container-fluid">
        <div class="row">
            <div class="col-12">
                <div class="card">
                    <div class="card-body">
						<form autocomplete="off" id="saveSalesEnquiry" data-res_function="resSaveEnquiry">
                            <div class="col-md-12">
                                <div class="row">

                                    <input type="hidden" name="id" id="id" value="<?=(!empty($dataRow->id))?$dataRow->id:""?>">
                                    <input type="hidden" name="entry_type" id="entry_type" value="<?=(!empty($dataRow->entry_type))?$dataRow->entry_type:$entry_type?>">

									<div class="col-md-3 form-group">
                                        <label for="enq_no">Enquiry No.</label>
                                        <div class="input-group mb-3">
                                            <input type="text" name="trans_prefix" class="form-control req" value="<?=(!empty($dataRow->trans_prefix))?$dataRow->trans_prefix:$trans_prefix?>" readonly />
                                            <input type="text" name="trans_no" class="form-control" value="<?=(!empty($dataRow->trans_no))?$dataRow->trans_no:$trans_no?>" readonly />
                                        </div>
									</div>

									<div class="col-md-2 form-group">
										<label for="trans_date">Enquiry Date</label>
                                        <input type="date" id="trans_date" name="trans_date" class=" form-control req" placeholder="dd-mm-yyyy" aria-describedby="basic-addon2" value="<?=(!empty($dataRow->trans_date))?$dataRow->trans_date:date("Y-m-d")?>" />	
									</div>

									 <div class="col-md-4 form-group">
                                        <label for="party_id">Customer Name</label>
                                        <div class="float-right">	
                                            <span class="dropdown float-right">
                                                <a class="text-primary font-bold waves-effect waves-dark" href="" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false" datatip="Progress" flow="down">+ Add New</a>

                                                <div class="dropdown-menu dropdown-menu-left user-dd animated flipInY" x-placement="start-left">
                                                    <div class="d-flex no-block align-items-center p-10 bg-primary text-white">ACTION</div>
                                                    
                                                    <?php
                                                        $custParam = "{'postData':{'party_category' : 1,'party_type':2},'modal_id' : 'bs-left-lg-modal', 'controller' : 'parties','call_function':'addParty', 'form_id' : 'addSupplier', 'title' : 'Add Customer ', 'res_function' : 'resPartyMaster', 'js_store_fn' : 'customStore'}"; //09-12-24
                                                    ?>
                                                    <button type="button" class="dropdown-item" onclick="modalAction(<?=$custParam?>);" ><i class="fa fa-plus"></i> Customer</button>                                                        
                                                </div>
                                            </span>
                                        </div>
                                        <select name="party_id" id="party_id" class="form-control select2 partyDetails partyOptions req" data-res_function="resPartyDetail" data-party_category="1" data-party_type ="1,2"> <!--09-12-24 -->
                                            <option value="">Select Party</option>
                                            <?=getPartyListOption($partyList,((!empty($dataRow->party_id))?$dataRow->party_id:0))?>
                                        </select>
                                        <div class="error party_id"></div>
                                    </div>
									
									<div class="col-md-3 form-group">
                                        <label for="currency">Currency</label>
                                        <input type="text"  id="currency" class="form-control" value="<?=(!empty($dataRow->currency))?$dataRow->currency:""?>"readOnly>
                                    </div>
									
									<div class="col-md-3 form-group">
										<label for="ref_by">Referance By</label>
                                        <input type="text" id="ref_by" name="ref_by" class=" form-control" value="<?=(!empty($dataRow->ref_by))?$dataRow->ref_by:""?>" />	
									</div>
									
									<div class="col-md-9 form-group">
										<label for="remark">Remark</label>
										<input type="text" name="remark" id="remark" class="form-control" placeholder="Enter Remark" value="<?=(!empty($dataRow->remark))?$dataRow->remark:""?>">
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
										<table id="salesEnqItems" class="table table-striped table-borderless">
											<thead class="thead-dark">
												<tr>
													<th style="width:5%;">#</th>
													<th>Part/Drg. No.</th>
													<th>Part Description</th>
													<th>Annual Vol</th>
													<th>MOQ/Lot Qty.</th>
													<th>Material Grade </th>
													<th>Remark</th>
													<th class="text-center" style="width:10%;">Action</th>
												</tr>
											</thead>
											<tbody id="tempItem" class="temp_item">
												<tr id="noData">
													<td colspan="7" class="text-center">No data available in table</td>
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
							<button type="button" class="btn waves-effect waves-light btn-success float-right save-form" onclick="customStore({'formId':'saveSalesEnquiry'});" ><i class="fa fa-check"></i> Save</button>
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
                                <input type="hidden" name="item_name" id="item_name" value="" />
                                <input type="hidden" name="item_code" id="item_code" value="" />
                                <input type="hidden" name="material_grade" id="material_grade" value="" />
                            </div>                            

                            <div class="col-md-12 form-group">
								<label for="item_id">Product Name</label>
                                <span class="dropdown float-right">
                                    <a class="text-primary font-bold waves-effect waves-dark" href="" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false" datatip="Progress" flow="down">+ Add New</a>

                                    <div class="dropdown-menu dropdown-menu-left user-dd animated flipInY" x-placement="start-left">
                                        <div class="d-flex no-block align-items-center p-10 bg-primary text-white">ACTION</div>
                                        <?php
                                            $productParam = "{'postData':{'item_type':1,'is_active':2},'modal_id' : 'bs-left-lg-modal','controller':'items', 'call_function':'addItem', 'form_id' : 'addItem', 'title' : 'Add Product','res_function':'resItemMaster','js_store_fn':'customStore'}";
                                        ?>
                                        <button type="button" class="dropdown-item" id="addnewitem" onclick="modalAction(<?=$productParam?>);"><i class="fa fa-plus"></i> Product</button>                                     
                                    </div>
                                </span>
                                
                                <select name="item_id" id="item_id" class="form-control select2 itemDetails itemOptions" data-res_function="resItemDetail" data-item_type="1" data-is_active="1,2">
                                    <option value="">Select Product Name</option>
                                    <?=getItemListOption($itemList)?>
                                </select>
                            </div>
                            <div class="col-md-6 form-group">
                                <label for="qty">MOQ/Lot Qty.</label>
                                <input type="text" name="qty" id="qty" class="form-control floatOnly req" value="0">
                            </div>     
							<div class="col-md-6 form-group">
                                <label for="annual_vol">Quantity(Annual Vol)</label>
                                <input type="text" name="annual_vol" id="annual_vol" class="form-control floatOnly req" value="0">
                            </div> 
                            <div class="col-md-6 form-group">
                                <label for="drw_no">Part/Drg No.</label>
                                <input type="text" name="drw_no" id="drw_no" class="form-control" value="">
                            </div>
							<div class="col-md-6 form-group">
                                <label for="fg_weight">FG Weight</label>
                                <input type="text" name="fg_weight" id="fg_weight" class="form-control floatOnly" value="">
                            </div>       
                            <div class="col-md-6 form-group">
                                <label for="drg_receive">Drawing Receipt</label>
                                <select name="drg_receive" id="drg_receive" class="form-control select2">
                                    <option value="YES">YES</option>
                                    <option value="NO">NO</option>
                                </select>
                            </div>
                            <div class="col-md-12 form-group">
                                <label for="item_remark">Remark</label>
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
<script src="<?php echo base_url();?>assets/js/custom/sales-enquiry-form.js?v=<?=time()?>"></script>
<?php
if(!empty($dataRow->itemList)):
    foreach($dataRow->itemList as $row):
        $row->row_index = "";
        echo '<script>AddRow('.json_encode($row).');</script>';
    endforeach;
endif;
?>