<?php $this->load->view('includes/header'); ?>
<div class="page-content-tab">
	<div class="container-fluid">
        <div class="row">
            <div class="col-12">
				<div class="col-12">
                    <div class="card">
                        <div class="card-body">
                            <form autocomplete="off" id="saveCustomInvoice" data-res_function="resCustomInvoice" enctype="multipart/form-data">
                                <div class="col-md-12">
                                    <div class="row">
                                        <div class="hiddenInput">
                                            <input type="hidden" name="id" id="id" value="<?=(!empty($dataRow->id))?$dataRow->id:""?>">
                                            <input type="hidden" name="entry_type" id="entry_type" value="<?=(!empty($dataRow->entry_type))?$dataRow->entry_type:$entry_type?>">
                                            <input type="hidden" name="from_entry_type" id="from_entry_type" value="<?=(!empty($dataRow->from_entry_type))?$dataRow->from_entry_type:((!empty($from_entry_type))?$from_entry_type:"")?>">
                                            <input type="hidden" name="ref_id" id="ref_id" value="<?=(!empty($dataRow->ref_id))?$dataRow->ref_id:((!empty($ref_id))?$ref_id:"")?>">
                                            <input type="hidden" name="party_name" id="party_name" value="<?=(!empty($dataRow->party_name))?$dataRow->party_name:""?>">

                                            <input type="hidden" name="gst_type" id="gst_type" value="<?=(!empty($dataRow->gst_type))?$dataRow->gst_type:"2"?>">
                                            <input type="hidden" name="party_state_code" id="party_state_code" value="<?=(!empty($dataRow->party_state_code))?$dataRow->party_state_code:""?>">
                                            <input type="hidden" name="tax_class" id="tax_class" value="<?=(!empty($dataRow->tax_class))?$dataRow->tax_class:""?>">
                                            <input type="hidden" name="sp_acc_id" id="sp_acc_id" value="<?=(!empty($dataRow->sp_acc_id))?$dataRow->sp_acc_id:0?>">
                                            <input type="hidden" name="gstin" id="gstin" value="<?=(!empty($dataRow->gstin))?$dataRow->gstin:""?>">
                                        </div>

                                        <div class="col-md-2 form-group">
                                            <label for="trans_number">Inv. No.</label>
                                            <div class="input-group">
                                                <input type="text" name="trans_prefix" id="trans_prefix" class="form-control" value="<?=(!empty($dataRow->trans_prefix))?$dataRow->trans_prefix:((!empty($trans_prefix))?$trans_prefix:"")?>" readonly>
                                                <input type="text" name="trans_no" id="trans_no" class="form-control numericOnly" value="<?=(!empty($dataRow->trans_no))?$dataRow->trans_no:((!empty($trans_no))?$trans_no:"")?>">
                                            </div>
                                            <input type="hidden" name="trans_number" id="trans_number" class="form-control" value="<?=(!empty($dataRow->trans_number))?$dataRow->trans_number:((!empty($trans_number))?$trans_number:"")?>" readonly>
                                            <div class="error trans_number"></div>
                                        </div>

                                        <div class="col-md-2 form-group">
                                            <label for="trans_date">Inv. Date</label>
                                            <input type="date" name="trans_date" id="trans_date" class="form-control" value="<?=(!empty($dataRow->trans_date))?$dataRow->trans_date:getFyDate()?>">
                                        </div>

                                        <div class="col-md-4 form-group">
                                            <label for="party_id">Customer Name</label>
                                            <div class="float-right">	
                                                
                                                <span class="dropdown float-right m-r-5">
                                                    <a class="text-primary font-bold waves-effect waves-dark" href="" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false" datatip="Progress" flow="down">+ Add New</a>

                                                    <div class="dropdown-menu dropdown-menu-left user-dd animated flipInY" x-placement="start-left">
                                                        <div class="d-flex no-block align-items-center p-10 bg-primary text-white">ACTION</div>
                                                        <?php
                                                            $custParam = "{'postData':{'party_category' : 1},'modal_id' : 'bs-left-lg-modal', 'controller' : 'parties','call_function':'addParty', 'form_id' : 'addSupplier', 'title' : 'Add Customer ', 'res_function' : 'resPartyMaster', 'js_store_fn' : 'customStore'}";
                                                        ?>
                                                        <button type="button" class="dropdown-item" onclick="modalAction(<?=$custParam?>);" ><i class="fa fa-plus"></i> Customer</button>
                                                    </div>
                                                </span>
                                            </div>
                                            <select name="party_id" id="party_id" class="form-control select2 partyDetails partyOptions req" data-res_function="resPartyDetail" data-party_category="1">
                                                <option value="">Select Party</option>
                                                <?=getPartyListOption($partyList,((!empty($dataRow->party_id))?$dataRow->party_id:0))?>
                                            </select>
                                        </div>

                                        <div class="col-md-2 form-group">
                                            <label for="tax_class_id">Export Type</label>
                                            <select name="tax_class_id" id="tax_class_id" class="form-control select2 req">
                                                <?=getTaxClassListOption($taxClassList,((!empty($dataRow->tax_class_id))?$dataRow->tax_class_id:0))?>
                                            </select>
                                            <input type="hidden" id="inv_type" value="SALES">
                                        </div>

                                        <div class="col-md-2 form-group">
                                            <label for="master_t_col_1">Consignee</label>
                                            <input type="text" name="masterDetails[t_col_1]" id="master_t_col_1" class="form-control" value="<?=(!empty($dataRow->consignee))?$dataRow->consignee:""?>">
                                        </div>

                                        <div class="col-md-4 form-group">
                                            <label for="master_s_col_1">Buyer Name</label>
                                            <input type="text" name="masterDetails[s_col_1]" id="master_s_col_1" class="form-control" value="<?=(!empty($dataRow->buyer_name))?$dataRow->buyer_name:""?>">
                                        </div>

                                        <div class="col-md-8 form-group">
                                            <label for="master_t_col_2">Buyer Address</label>
                                            <input type="text" name="masterDetails[t_col_2]" id="master_t_col_2" class="form-control" value="<?=(!empty($dataRow->buyer_address))?$dataRow->buyer_address:""?>">
                                        </div>

                                        <div class="col-md-2 form-group">
                                            <label for="doc_no">PO NO.</label>
                                            <input type="text" name="doc_no" id="doc_no" class="form-control" value="<?=(!empty($dataRow->doc_no))?$dataRow->doc_no:""?>">
                                        </div>

                                        <div class="col-md-2 form-group">
                                            <label for="doc_date">PO Date</label>
                                            <input type="date" name="doc_date" id="doc_date" class="form-control" max="<?=getFyDate()?>" value="<?=(!empty($dataRow->doc_date))?$dataRow->doc_date:""?>">
                                        </div>

                                        <div class="col-md-2 form-group">
                                            <label for="master_s_col_2">Method of Dispatch</label>
                                            <input type="text" name="masterDetails[s_col_2]" id="master_s_col_2" class="form-control" value="<?=(!empty($dataRow->method_of_dispatch))?$dataRow->method_of_dispatch:""?>">
                                        </div>

                                        <div class="col-md-2 form-group">
                                            <label for="master_s_col_3">Type Of Shipment</label>
                                            <input type="text" name="masterDetails[s_col_3]" id="master_s_col_3" class="form-control" value="<?=(!empty($dataRow->type_of_shipment))?$dataRow->type_of_shipment:""?>">
                                        </div>

                                        <div class="col-md-2 form-group">
                                            <label for="master_s_col_4">Country of Origin</label>
                                            <input type="text" name="masterDetails[s_col_4]" id="master_s_col_4" class="form-control" value="<?=(!empty($dataRow->country_of_origin))?$dataRow->country_of_origin:""?>">
                                        </div>

                                        <div class="col-md-2 form-group">
                                            <label for="master_s_col_5">Country of Final Destination</label>
                                            <input type="text" name="masterDetails[s_col_5]" id="master_s_col_5" class="form-control" value="<?=(!empty($dataRow->country_of_fd))?$dataRow->country_of_fd:""?>">
                                        </div>

                                        <div class="col-md-3 form-group">
                                            <label for="master_s_col_6">Port of Loading</label>
                                            <input type="text" name="masterDetails[s_col_6]" id="master_s_col_6" class="form-control" value="<?=(!empty($dataRow->port_of_loading))?$dataRow->port_of_loading:""?>">
                                        </div>

                                        <div class="col-md-3 form-group">
                                            <label for="master_date_col_1">Date of Departure</label>
                                            <input type="date" name="masterDetails[date_col_1]" id="master_date_col_1" class="form-control" value="<?=(!empty($dataRow->date_of_departure))?$dataRow->date_of_departure:""?>">
                                        </div>

                                        <div class="col-md-3 form-group">
                                            <label for="master_s_col_7">Port of Discharge</label>
                                            <input type="text" name="masterDetails[s_col_7]" id="master_s_col_7" class="form-control" value="<?=(!empty($dataRow->port_of_discharge))?$dataRow->port_of_discharge:""?>">
                                        </div>

                                        <div class="col-md-3 form-group">
                                            <label for="master_s_col_8">Final Destination</label>
                                            <input type="text" name="masterDetails[s_col_8]" id="master_s_col_8" class="form-control" value="<?=(!empty($dataRow->final_destination))?$dataRow->final_destination:""?>">
                                        </div>

                                        <div class="col-md-3 form-group">
                                            <label for="master_s_col_9">Delivery Type</label>
                                            <input type="text" name="masterDetails[s_col_9]" id="master_s_col_9" class="form-control" value="<?=(!empty($dataRow->delivery_type))?$dataRow->delivery_type:""?>">
                                        </div>

                                        <div class="col-md-3 form-group">
                                            <label for="master_s_col_10">Delivery Location</label>
                                            <input type="text" name="masterDetails[s_col_10]" id="master_s_col_10" class="form-control" value="<?=(!empty($dataRow->delivery_location))?$dataRow->delivery_location:""?>">
                                        </div>

                                        <div class="col-md-6 form-group">
                                            <label for="master_t_col_3">Terms / Method of Payment</label>
                                            <input type="text" name="masterDetails[t_col_3]" id="master_t_col_3" class="form-control" value="<?=(!empty($dataRow->terms_and_method_of_payment))?$dataRow->terms_and_method_of_payment:""?>">
                                        </div>

                                        <div class="col-md-2 form-group">
                                            <label for="apply_round">Apply Round Off</label>
                                            <select name="apply_round" id="apply_round" class="form-control">
                                                <option value="1" <?= (!empty($dataRow) && $dataRow->apply_round == 1) ? "selected" : "" ?>>Yes</option>
                                                <option value="0" <?= (!empty($dataRow) && $dataRow->apply_round == 0) ? "selected" : "" ?>>No</option>
                                            </select>
                                        </div>

                                        <div class="col-md-2 form-group">
                                            <label for="currency">Currency</label>
                                            <input type="text" name="currency" id="currency" class="form-control" value="<?=(!empty($dataRow->currency))?$dataRow->currency:""?>" readonly />
                                        </div>

                                        <div class="col-md-2 form-group">
                                            <label for="inrrate">Currency Rate</label>
                                            <input type="text" name="inrrate" id="inrrate" class="form-control floatOnly" value="<?=(!empty($dataRow->inrrate))?$dataRow->inrrate:""?>">
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
                                                <table id="customInvoiceItems" class="table table-striped table-borderless">
                                                    <thead class="thead-dark">
                                                        <tr>
                                                            <th style="width:5%;">#</th>
                                                            <th>Item Name</th>
                                                            <th>HSN Code</th>
                                                            <th>Qty.</th>
                                                            <th>Unit</th>
                                                            <th>Price</th>
                                                            <th>Disc.</th>
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
                                                            <td colspan="15" class="text-center">No data available in table</td>
                                                        </tr>
                                                    </tbody>
                                                </table>
                                            </div>
                                        </div>
                                    </div>

                                    <hr>

                                    <div id="taxSummaryHtml"></div>

                                    <hr>

                                    <div class="row">
                                        <div class="col-md-12 form-group">
                                            <label for="remark">Remark</label>
                                            <input type="text" name="remark" id="remark" class="form-control" value="<?=(!empty($dataRow->remark))?$dataRow->remark:""?>">
                                        </div>                                        
                                    </div>
                                </div>
                            </form>
                        </div>
                        <div class="card-footer bg-facebook">
                            <div class="col-md-12"> 
                                <button type="button" class="btn waves-effect waves-light btn-success float-right save-form" onclick="customStore({'formId':'saveCustomInvoice'});" ><i class="fa fa-check"></i> Save </button>

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
    <div class="modal-dialog modal-lg" role="document">
        <div class="modal-content animated slideDown">
            <div class="modal-header" style="display:block;"><h4 class="modal-title">Add or Update Item</h4></div>
            <div class="modal-body">
                <form id="itemForm">
                    <div class="col-md-12" >
                        <div class="row form-group">
                            <div id="itemInputs">
                                <input type="hidden" id="id" name="id" value="" />
                                <input type="hidden" name="from_entry_type" id="from_entry_type" value="" />
                                <input type="hidden" name="ref_id" id="ref_id" value="" />
                                <input type="hidden" name="row_index" id="row_index" value="">
                                <input type="hidden" name="item_code" id="item_code" value="" />
                                <input type="hidden" name="item_name" id="item_name" value="" />
                                <input type="hidden" name="item_type" id="item_type" value="1" />
                                <input type="hidden" name="org_price" class="org_price" id="org_price" value="" />
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
                                            ?>
                                            <button type="button" class="dropdown-item" onclick="modalAction(<?=$productParam?>);"><i class="fa fa-plus"></i> Product</button>
                                        </div>
                                    </span>
                                </div>
                                <select name="item_id" id="item_id" class="form-control select2 itemDetails itemOptions req" data-res_function="resItemDetail" data-item_type="1">
                                    <option value="">Select Product Name</option>
                                    <?=getItemListOption($itemList); ?>
                                </select>
                            </div>

                            <div class="col-md-2 form-group">
                                <label for="qty">Quantity</label>
                                <input type="text" name="qty" id="qty" class="form-control floatOnly req" value="0">
                            </div>
                            <div class="col-md-2 form-group">
                                <label for="packing_qty">Pcs./Box</label>
                                <input type="text" name="packing_qty" id="packing_qty" class="form-control" value="0" >
                            </div>
                            <div class="col-md-2 form-group">
                                <label for="total_box">Total Box</label>
                                <input type="text" name="total_box" id="total_box" class="form-control floatOnly" value="0">
                            </div>
                            <div class="col-md-3 form-group">
                                <label for="disc_per">Disc. (%)</label>
                                <input type="text" name="disc_per" id="disc_per" class="form-control floatOnly" value="0">
                            </div>
                            <div class="col-md-3 form-group">
                                <label for="price">Price</label>
                                <input type="text" name="price" id="price" class="form-control floatOnly req" value="0" />
                            </div>

                            <div class="col-md-2 form-group">
                                <label for="pallet_qty">No. of Pallets</label>
                                <input type="text" name="pallet_qty" id="pallet_qty" class="form-control floatOnly" value="">
                            </div>
                            <div class="col-md-3 form-group">
                                <label for="net_weight">Net Weight (Kg)</label>
                                <input type="text" name="net_weight" id="net_weight" class="form-control floatOnly req" value="">
                            </div>
                            <div class="col-md-3 form-group">
                                <label for="gross_weight">Gross Weight (Kg)</label>
                                <input type="text" name="gross_weight" id="gross_weight" class="form-control floatOnly req" value="">
                            </div>
                            
                            <div class="col-md-2 form-group">
                                <label for="unit_name">Unit</label>        
                                <select name="unit_name" id="unit_name" class="form-control select2">
                                    <option value="">Select Unit</option>
                                    <?=getItemUnitListOption($unitList)?>
                                </select> 
                                <input type="hidden" name="unit_id" id="unit_id" class="form-control" value="" />                       
                            </div>
							<div class="col-md-2 form-group">
                                <label for="hsn_code">HSN Code</label>
                                <select name="hsn_code" id="hsn_code" class="form-control select2">
                                    <option value="">Select HSN Code</option>
                                    <?=getHsnCodeListOption($hsnList)?>
                                </select>
                            </div>
                            <div class="col-md-3 form-group hidden">
                                <label for="gst_per">GST Per.(%)</label>
                                <select name="gst_per" id="gst_per" class="form-control select2">
                                    <?php
                                        foreach($this->gstPer as $per=>$text):
                                            echo '<option value="'.$per.'">'.$text.'</option>';
                                        endforeach;
                                    ?>
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
<script src="<?php echo base_url(); ?>assets/js/custom/custom-invoice-form.js?v=<?= time() ?>"></script>
<script src="<?php echo base_url(); ?>assets/js/custom/calculate.js?v=<?= time() ?>"></script>
<script>
var taxSummary = <?=json_encode(((!empty($dataRow))?$dataRow:array()))?>;
</script>
<?php
if(!empty($dataRow->itemList)):
    foreach($dataRow->itemList as $row):
        $row->row_index = "";
        echo '<script>AddRow('.json_encode($row).');</script>';
    endforeach;
endif;
?>