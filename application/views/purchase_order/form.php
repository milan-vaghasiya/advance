<?php $this->load->view('includes/header'); ?>
<div class="page-content-tab">
	<div class="container-fluid">
        <div class="row">
            <div class="col-12">
				<div class="col-12">
                    <div class="card">
                        <div class="card-body">
                            <form autocomplete="off" id="savePurchaseOrder" data-res_function="resSaveOrder" enctype="multipart/form-data">
                                <div class="col-md-12">
                                    <div class="row">

                                        <div class="hiddenInput">
											<input type="hidden" name="id" id="id" class="trans_main_id" value="<?=(!empty($dataRow->id))?$dataRow->id:""?>">
                                            <input type="hidden" name="entry_type" id="entry_type" value="<?=(!empty($dataRow->entry_type))?$dataRow->entry_type:$entry_type?>">

                                            <input type="hidden" name="from_entry_type" id="from_entry_type" value="<?=(!empty($dataRow->from_entry_type))?$dataRow->from_entry_type:((!empty($from_entry_type))?$from_entry_type:"")?>">

                                            <input type="hidden" name="ref_id" id="ref_id" value="<?=(!empty($dataRow->ref_id))?$dataRow->ref_id:((!empty($ref_id))?$ref_id:"")?>">

                                            <input type="hidden" name="trans_prefix" id="trans_prefix" value="<?=(!empty($dataRow->trans_prefix))?$dataRow->trans_prefix:((!empty($trans_prefix))?$trans_prefix:"")?>">

                                            <input type="hidden" name="trans_no" id="trans_no" value="<?=(!empty($dataRow->trans_no))?$dataRow->trans_no:((!empty($trans_no))?$trans_no:"")?>">

                                            <input type="hidden" name="gst_type" id="gst_type" value="<?=(!empty($dataRow->gst_type))?$dataRow->gst_type:""?>">

                                            <input type="hidden" name="party_state_code" id="party_state_code" value="<?=(!empty($dataRow->party_state_code))?$dataRow->party_state_code:""?>">
                                            <input type="hidden" name="apply_round" id="apply_round" value="<?=(!empty($dataRow->apply_round))?$dataRow->apply_round:"1"?>">

                                            <input type="hidden" name="ledger_eff" id="ledger_eff" value="0">
                                            <input type="hidden" id="inv_type" value="PURCHASE ">
                                            
                                            <input type="hidden" name="tax_class" id="tax_class" value="<?=(!empty($dataRow->tax_class))?$dataRow->tax_class:""?>">

                                        </div>

                                        <div class="col-md-2 form-group">
                                            <label for="trans_number">PO. No.</label>
                                            <input type="text" name="trans_number" id="trans_number" class="form-control" value="<?=(!empty($dataRow->trans_number))?$dataRow->trans_number:((!empty($trans_number))?$trans_number:"")?>" readonly>
                                        </div>

                                        <div class="col-md-2 form-group">
                                            <label for="trans_date">PO. Date</label>
                                            <input type="date" name="trans_date" id="trans_date" class="form-control" value="<?=(!empty($dataRow->trans_date))?$dataRow->trans_date:getFyDate()?>">
                                        </div>

                
                                        <div class="col-md-5 form-group">
                                            <label for="party_id">Party Name</label>
                                            <div class="float-right">	
                                                <span class="dropdown float-right">
                                                    <a class="text-primary font-bold waves-effect waves-dark" href="" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false" datatip="Progress" flow="down">+ Add New</a>

                                                    <div class="dropdown-menu dropdown-menu-left user-dd animated flipInY" x-placement="start-left">
                                                        <div class="d-flex no-block align-items-center p-10 bg-primary text-white">ACTION</div>

                                                        <?php
                                                            $custParam = "{'postData':{'party_category' : 1},'modal_id' : 'bs-left-lg-modal', 'controller' : 'parties','call_function':'addParty', 'form_id' : 'addSupplier', 'title' : 'Add Customer ', 'res_function' : 'resPartyMaster', 'js_store_fn' : 'customStore'}";

                                                            $supParam = "{'postData':{'party_category' : 2},'modal_id' : 'bs-left-lg-modal', 'controller' : 'parties','call_function':'addParty', 'form_id' : 'addSupplier', 'title' : 'Add Supplier ', 'res_function' : 'resPartyMaster', 'js_store_fn' : 'customStore'}";
                                                        ?>
                                                        <button type="button" class="dropdown-item" onclick="modalAction(<?=$custParam?>);" ><i class="fa fa-plus"></i> Customer</button>

                                                        <button type="button" class="dropdown-item " onclick="modalAction(<?=$supParam?>);" ><i class="fa fa-plus"></i> Supplier</button>
                                                    </div>
                                                </span>
                                            </div>
                                            <select name="party_id" id="party_id" class="form-control select2 partyDetails partyOptions req" data-res_function="resPartyDetail" data-party_category="1,2,3">
                                                <option value="">Select Party</option>
                                                <?=getPartyListOption($partyList,((!empty($dataRow->party_id))?$dataRow->party_id:(!empty($enqItemList[0]->party_id)?$enqItemList[0]->party_id:'')))?>
                                            </select>
                                        </div>

                                        <div class="col-md-3 form-group">
                                            <label for="gstin">GST NO.</label>
                                            <select name="gstin" id="gstin" class="form-control select2">
                                                <option value="">Select GST No.</option>
                                                <?php
                                                    if(!empty($dataRow->party_id)):
                                                        foreach($gstinList as $row):
                                                            $selected = ($dataRow->gstin == $row->gstin)?"selected":"";
                                                            echo '<option value="'.$row->gstin.'" '.$selected.'>'.$row->gstin.'</option>';
                                                        endforeach;
                                                    endif;
                                                ?>
                                            </select>
                                        </div>
										
										<div class="col-md-3 form-group hidden">
                                            <label for="tax_class_id">GST Type </label>
                                            <select name="tax_class_id" id="tax_class_id" class="form-control select2 req">
                                                <?=getTaxClassListOption($taxClassList,((!empty($dataRow->tax_class_id))?$dataRow->tax_class_id:0))?>
                                            </select> 
                                        </div>
										
										<div class="col-md-2 form-group">
                                            <label for="doc_no">Ref. No.</label>
                                            <input type="text" name="doc_no" id="doc_no" class="form-control" value="<?=(!empty($dataRow->doc_no))?$dataRow->doc_no:(!empty($quoteNo)?$quoteNo:"")?>">
                                        </div>

                                        <div class="col-md-2 form-group">
                                            <label for="doc_date">Ref. Date</label>
                                            <input type="date" name="doc_date" id="doc_date" class="form-control" value="<?=(!empty($dataRow->doc_date))?$dataRow->doc_date:(!empty($quoteDate)?$quoteDate:"")?>">
                                        </div>

                                        <div class="col-md-6 form-group">
                                            <label for="master_t_col_3">Delivery Address</label>
                                            <input type="text" name="masterDetails[t_col_3]" id="master_t_col_3" class="form-control" value="<?=(!empty($dataRow->delivery_address))?$dataRow->delivery_address:""?>">
                                        </div>
										
										<div class="col-md-2 form-group">
                                            <label for="master_t_col_4">Delivery Pincode</label>
                                            <input type="text" name="masterDetails[t_col_4]" id="master_t_col_4" class="form-control" value="<?=(!empty($dataRow->delivery_pincode))?$dataRow->delivery_pincode:""?>">
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
                                                            <th>Item Name</th>
                                                            <th>Qty.</th>
                                                            <th>Unit</th>
                                                            <th>Price</th>
                                                            <th class="igstCol">IGST</th>
                                                            <th class="cgstCol">CGST</th>
                                                            <th class="sgstCol">SGST</th>
                                                            <th class="amountCol">Amount</th>
                                                            <th class="netAmtCol">Amount</th>
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

                                    <?php //$this->load->view('includes/tax_summary',['expenseList'=>$expenseList,'taxList'=>$taxList,'ledgerList'=>array(),'dataRow'=>((!empty($dataRow))?$dataRow:array())])?>

									<div id="taxSummaryHtml"></div>

                                    <hr>

                                    <div class="row">                                    
                                        <div class="col-md-12 form-group">
                                            <label for="remark">Remark</label>
                                            <input type="text" name="remark" id="remark" class="form-control" value="<?=((!empty($dataRow->remark))?$dataRow->remark:(!empty($enqData->quote_remark)?$enqData->quote_remark:''))?>">
                                        </div> 
                                    </div>

                                    <?php $this->load->view('includes/terms_form',['termsList'=>$termsList,'termsConditions'=>(!empty($dataRow->termsConditions)) ? $dataRow->termsConditions : array()])?>  
                                </div>
                            </form>
                        </div>
                        <div class="card-footer bg-facebook">
                            <div class="col-md-12">
                                <button type="button" class="btn btn-info waves-effect show_terms" >Terms & Conditions</button>
                                <span class="term_error text-danger font-bold"></span>
                                
                                
                                <button type="button" class="btn waves-effect waves-light btn-success float-right save-form" onclick="customStore({'formId':'savePurchaseOrder','txt_editor':'conditions'});" ><i class="fa fa-check"></i> Save</button>

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
                    <div class="col-md-12">

                        <div class="row form-group">
							<div id="itemInputs">
								<input type="hidden" name="id" id="id" value="" />
								<input type="hidden" name="from_entry_type" id="from_entry_type" value="" />
                                <input type="hidden" name="ref_id" id="ref_id" value=""  />
                                <input type="hidden" name="req_id" id="req_id" value=""  />
								<input type="hidden" name="row_index" id="row_index" value="">
								<input type="hidden" name="gst_per" id="gst_per" value="">
								<input type="hidden" name="cnv_value" id="cnv_value" value="">
								<input type="hidden" name="com_unit" id="com_unit" value="">
								<input type="hidden" name="unit_name" id="unit_name" value="">
						        <input type="hidden" name="so_trans_id" id="so_trans_id" value="" />
                            </div>
                            
                            <div class="col-md-12 form-group">
								<label for="item_id">Product Name</label>
								<div class="float-right">	
                                    <span class="dropdown float-right">
                                        <a class="text-primary font-bold waves-effect waves-dark" href="" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false" datatip="Progress" flow="down">+ Add New</a>

                                        <div class="dropdown-menu dropdown-menu-left user-dd animated flipInY" x-placement="start-left">
                                            <div class="d-flex no-block align-items-center p-10 bg-primary text-white">ACTION</div>
                                            
                                            <?php
                                                $productParam = "{'postData':{'item_type':1},'modal_id' : 'bs-left-lg-modal','controller':'items', 'call_function':'addItem', 'form_id' : 'addItem', 'title' : 'Add Product','res_function':'resItemMaster','js_store_fn':'customStore'}";

                                                $rmParam = "{'postData':{'item_type':3},'modal_id' : 'bs-left-lg-modal','controller':'items', 'call_function':'addItem', 'form_id' : 'addItem', 'title' : 'Add Raw Material','res_function':'resItemMaster','js_store_fn':'customStore'}";

                                                $conParam = "{'postData':{'item_type':2},'modal_id' : 'bs-left-lg-modal','controller':'items', 'call_function':'addItem', 'form_id' : 'addItem', 'title' : 'Add Consumable','res_function':'resItemMaster','js_store_fn':'customStore'}";

                                                $serviceParam = "{'postData':{'item_type':8},'modal_id' : 'bs-left-lg-modal','controller':'items', 'call_function':'addItem', 'form_id' : 'addItem', 'title' : 'Add Service Item','res_function':'resItemMaster','js_store_fn':'customStore'}";

                                                $machineParam = "{'postData':{'item_type':5},'modal_id' : 'bs-left-lg-modal','controller':'items', 'call_function':'addItem', 'form_id' : 'addItem', 'title' : 'Add Machineries','res_function':'resItemMaster','js_store_fn':'customStore'}";
                                            ?>
                                            <button type="button" class="dropdown-item" onclick="modalAction(<?=$productParam?>);"><i class="fa fa-plus"></i> Product</button>

                                            <button type="button" class="dropdown-item" onclick="modalAction(<?=$rmParam?>);"><i class="fa fa-plus"></i> Raw Material</button>

                                            <button type="button" class="dropdown-item" onclick="modalAction(<?=$conParam?>);"><i class="fa fa-plus"></i> Consumable</button>

                                            <button type="button" class="dropdown-item" onclick="modalAction(<?=$machineParam?>);"><i class="fa fa-plus"></i> Machineries</button>
                                        </div>
                                    </span>
                                </div>
                                <select name="item_id" id="item_id" class="form-control select2 itemDetails itemOptions" data-res_function="resItemDetail" data-item_type="1,2,3,5,8">
                                    <option value="">Select Product Name</option>
                                    <?php 
                                    foreach($itemList as $row):
                                        echo '<option value="'.$row->id.'" data-item_type="'.$row->item_type.'">'.(!empty($row->item_code) ? '[ '.$row->item_code.' ] ' : '').$row->item_name.(!empty($row->material_grade) ? ' '.$row->material_grade : '').'</option>';
                                    endforeach;
                                    ?>
                                </select>
                            </div>
                            <div class="col-md-12 form-group">
                                <label for="fg_item_id">Finish Goods</label>
                                <select name="fg_item_id" id="fg_item_id" class="form-control select2">
                                    <option value="">Select Finish Goods</option>
                                    <?php echo (!empty($fgoption)? $fgoption :'')?>
                                </select>
                            </div>
                            <div class="col-md-6 form-group ">
                                <label for="qty">Quantity</label><span id="uom_span" class="float-right"></span>
                                <input type="text" name="qty" id="qty" class="form-control floatOnly req calcComQty" value="0">
                                <input type="hidden" name="com_qty" id="com_qty" value="0">
                                <input type="hidden" name="uom" id="uom" value="0" class="calcComQty">
                            </div>
							<div class="col-md-6 form-group">
                                <label for="price">Price</label>
                                <input type="text" name="price" id="price" class="form-control floatOnly req" value="0" />
                            </div>
							<div class="col-md-6 form-group">
                                <label for="mill_name">Make</label>
                                <input type="text" name="mill_name" id="mill_name" class="form-control" value="">
                            </div>
                            <div class="col-md-6 form-group">
                                <label for="delivery_date">Delivery Date</label>
                                <input type="date" name="delivery_date" id="delivery_date" class="form-control" value="">
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
<script src="<?php echo base_url(); ?>assets/js/custom/purchase-order-form.js?v=<?=time()?>"></script>
<script src="<?php echo base_url(); ?>assets/js/custom/calculate.js"></script>
<script src="<?=base_url()?>assets/plugins/tinymce/tinymce.min.js"></script>
<script>
    $(document).ready(function(){
        initEditor({
            selector: '#conditions',
            height: 400
        });
    });
	
	var taxSummary = <?=json_encode(((!empty($dataRow))?$dataRow:array()))?>;
</script>
<?php 
if(!empty($dataRow->itemList)): 
    foreach($dataRow->itemList as $row):
        $row->row_index = "";
        $row->gst_per = floatVal($row->gst_per);
        $row->disc_amount = 0; 
        echo '<script>AddRow('.json_encode($row).');</script>';
    endforeach;
endif;
?>

<?php 
    if(!empty($reqItemList)):
        foreach($reqItemList as $row): 
			$row->req_id = $row->id;
			$row->com_unit = $row->com_uom;
			$row->com_qty = $row->qty;
			$row->unit_name = $row->uom;
            $row->gst_per = floatval($row->gst_per);
            $row->disc_per = 0;
            $row->item_remark = $row->remark;
            $row->disc_amount = 0; 
			$row->from_entry_type = $row->entry_type;
			$row->entry_type = "";
            $row->row_index = "";
            $row->id = "";
            $row = json_encode($row);
            echo '<script>AddRow('.$row.');</script>';
        endforeach;
    endif;
	
	if(!empty($enqItemList)): 
        foreach($enqItemList as $row):
            $row->from_entry_type = $row->entry_type;
            $row->so_trans_id = $row->so_trans_id;
            $row->ref_id = $row->id;
            $row->qty = $row->pending_qty;
            $row->com_qty = $row->qty;
            $row->req_id = 0;
            $row->id = "";          
            $row->row_index = "";
            $row->gst_per = (!empty($row->gst_per)) ? floatVal($row->gst_per) : 0;
            $row->disc_per = 0;
            $row->disc_amount = 0; 
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
			$row->com_unit = $row->uom;
			$row->price = $row->price;
			$row->com_qty = round($rm_shortage,3);
            $row->qty = $rm_shortage;
			$row->unit_name = $row->uom;
            $row->gst_per = floatval($row->gst_per);
            $row->disc_per = 0;
            $row->disc_amount = 0; 
			$row->entry_type = "";
            $row->row_index = "";
            $row->id = "";
            $row = json_encode($row);
            echo '<script>AddRow('.$row.');</script>';
            $i++;
        endforeach;
    endif;
?>