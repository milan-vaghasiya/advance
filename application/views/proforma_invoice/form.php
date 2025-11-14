<?php $this->load->view('includes/header'); ?>
<div class="page-content-tab">
	<div class="container-fluid">
        <div class="row">
            <div class="col-12">
				<div class="col-12">
                    <div class="card">
                        <div class="card-body">
                            <form autocomplete="off" id="saveProformaInv" data-res_function="resSaveProformaInv" enctype="multipart/form-data">
                                <div class="col-md-12">
                                    <div class="row">

                                        <div class="hiddenInput">
                                            <input type="hidden" name="id" id="id" value="<?=(!empty($dataRow->id))?$dataRow->id:""?>">
                                            <input type="hidden" name="entry_type" id="entry_type" value="<?=(!empty($dataRow->entry_type))?$dataRow->entry_type:$entry_type?>">
                                            <input type="hidden" name="from_entry_type" id="from_entry_type" value="<?=(!empty($dataRow->from_entry_type))?$dataRow->from_entry_type:((!empty($from_entry_type))?$from_entry_type:"")?>">
                                            <input type="hidden" name="ref_id" id="ref_id" value="<?=(!empty($dataRow->ref_id))?$dataRow->ref_id:((!empty($ref_id))?$ref_id:"")?>">

                                            <input type="hidden" name="trans_prefix" id="trans_prefix" value="<?=(!empty($dataRow->trans_prefix))?$dataRow->trans_prefix:((!empty($trans_prefix))?$trans_prefix:"")?>">
                                            <input type="hidden" name="trans_no" id="trans_no" value="<?=(!empty($dataRow->trans_no))?$dataRow->trans_no:((!empty($trans_no))?$trans_no:"")?>">

                                            <input type="hidden" name="gst_type" id="gst_type" value="<?=(!empty($dataRow->gst_type))?$dataRow->gst_type:""?>">
                                            <input type="hidden" name="party_state_code" id="party_state_code" value="<?=(!empty($dataRow->party_state_code))?$dataRow->party_state_code:""?>">
                                            <input type="hidden" name="apply_round" id="apply_round" value="<?=(!empty($dataRow->apply_round))?$dataRow->apply_round:"1"?>">
                                            <input type="hidden" name="ledger_eff" id="ledger_eff" value="0">
                                           <input type="hidden" name="gstin" id="gstin" value="<?=(!empty($dataRow->gstin))?$dataRow->gstin:""?>">

                                        </div>

                                        <div class="col-md-2 form-group">
                                            <label for="trans_number">PINV. No.</label>
                                            <input type="text" name="trans_number" id="trans_number" class="form-control" value="<?=(!empty($dataRow->trans_number))?$dataRow->trans_number:((!empty($trans_number))?$trans_number:"")?>" readonly>
                                        </div>

                                        <div class="col-md-2 form-group">
                                            <label for="trans_date">PINV. Date</label>
                                            <input type="date" name="trans_date" id="trans_date" class="form-control" value="<?=(!empty($dataRow->trans_date))?$dataRow->trans_date:getFyDate()?>">
                                        </div>

                                        <div class="col-md-5 form-group">
                                            <label for="party_id">Customer Name</label>
                                           
                                            <select name="party_id" id="party_id" class="form-control select2 partyDetails partyOptions req" data-res_function="resPartyDetail" data-party_category="1,2">
                                                <option value="">Select Party</option>
                                                <?=getPartyListOption($partyList,((!empty($dataRow->party_id))?$dataRow->party_id:0))?>
                                            </select>
                                        </div>

                                        <div class="col-md-3 form-group">
                                            <label for="carriage_by">Pre-Carriage By</label>
                                            <input type="text" name="carriage_by" id="carriage_by" class="form-control" value="<?=(!empty($dataRow->carriage_by))?$dataRow->carriage_by:""?>">
                                        </div>

                                        <div class="col-md-3 form-group">
                                            <label for="rcpt_by">Rcpt at by Pre-Carrier</label>
                                            <input type="text" name="rcpt_by" id="rcpt_by" class="form-control" value="<?=(!empty($dataRow->rcpt_by))?$dataRow->rcpt_by:""?>">
                                        </div> 
                                       

                                        <div class="col-md-3 form-group">
                                            <label for="flight_no">Vessel/Flight No.</label>
                                            <input type="text" name="flight_no" id="flight_no" class="form-control" value="<?=(!empty($dataRow->flight_no))?$dataRow->flight_no:""?>">
                                        </div>

                                        <div class="col-md-3 form-group">
                                            <label for="port_loading">Port of Loading</label>
                                            <input type="text" name="port_loading" id="port_loading" class="form-control" value="<?=(!empty($dataRow->port_loading))?$dataRow->port_loading:''?>">
                                        </div> 

                                        <div class="col-md-3 form-group">
                                            <label for="destination">Final Destination</label>
                                            <input type="text" name="destination" id="destination" class="form-control" value="<?=(!empty($dataRow->destination))?$dataRow->destination:""?>">
                                        </div>

                                        <div class="col-md-3 form-group">
                                            <label for="port_discharge">Port of Discharge</label>
                                            <input type="text" name="port_discharge" id="port_discharge" class="form-control" value="<?=(!empty($dataRow->port_discharge))?$dataRow->port_discharge:''?>">
                                        </div> 
                                      

                                        <div class="col-md-3 form-group">
                                            <label for="terms"> Terms of Delivery & Payment</label>
                                            <input type="text" name="terms" id="terms" class="form-control" value="<?=(!empty($dataRow->terms))?$dataRow->terms:''?>">
                                        </div> 
                                    
                                    </div>

                                    <hr>

                                    <div class="col-md-12 row">
                                        <div class="col-md-6"><h4>Item Details : </h4></div>
                                        <div class="col-md-6">
                                            <!-- <button type="button" class="btn btn-outline-success waves-effect float-right add-item"><i class="fa fa-plus"></i> Add Item</button> -->
                                        </div>
                                    </div>

                                    <div class="col-md-12 mt-3">
                                        <div class="error itemData"></div>
                                        <div class="row form-group">
                                            <div class="table-responsive">
                                                <table id="pInvItems" class="table table-striped table-borderless">
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
                                                            <th>Delivery Date</th>
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

                                    <?php $this->load->view('includes/tax_summary',['expenseList'=>$expenseList,'taxList'=>$taxList,'ledgerList'=>array(),'dataRow'=>((!empty($dataRow))?$dataRow:array())])?>

                                    <hr>

                                    <div class="row">
                                        <div class="col-md-12 form-group">
                                            <label for="remark">Remark</label>
                                            <input type="text" name="remark" id="remark" class="form-control" value="<?=(!empty($dataRow->remark))?$dataRow->remark:""?>">
                                        </div>
                                    </div>

                                    <div class="card-footer bg-facebook">
                                        <div class="col-md-12"> 
                                            <button type="button" class="btn btn-success waves-effect show_terms">Terms & Conditions</button>
                                            <span class="term_error text-danger font-bold"></span>

                                            <button type="button" class="btn waves-effect waves-light btn-success float-right save-form" onclick="customStore({'formId':'saveProformaInv','txt_editor':'conditions'});" ><i class="fa fa-check"></i> <?=(!empty($is_approve))?"Acccept":"Save"?> </button>

                                            <button type="button" class="btn btn-secondary press-close-btn btn-close-modal save-form float-right m-r-10" onclick="window.location.href='<?=base_url($headData->controller)?>'"><i class="fa fa-times"></i> Cancel</button>
                                        </div>
                                        <?php $this->load->view('includes/terms_form',['termsList'=>$termsList,'termsConditions'=>(!empty($dataRow->termsConditions)) ? $dataRow->termsConditions : array()])?>
                                    </div>
                                </div>
                            </form>
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
								<input type="hidden" name="row_index" id="row_index" value="">
                                <input type="hidden" name="item_name" id="item_name" value="" />
                                <input type="hidden" name="unit_name" id="unit_name" value="" />
                                <input type="hidden" name="hsn_code" id="hsn_code" value="" />
                            </div>
                            

                            <div class="col-md-12 form-group">
								<label for="item_id">Product Name</label>

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

                                <select name="item_id" id="item_id" class="form-control select2 itemDetails itemOptions" data-res_function="resItemDetail"  data-item_type="1">
                                    <option value="">Select Product Name</option>
                                    <?=getItemListOption($itemList)?>
                                </select>
                            </div>
                            <div class="col-md-4 form-group">
                                <label for="qty">Quantity</label>
                                <input type="text" name="qty" id="qty" class="form-control floatOnly req" value="0">
                            </div>
                            <div class="col-md-4 form-group">
                                <label for="disc_per">Disc. (%)</label>
                                <input type="text" name="disc_per" id="disc_per" class="form-control floatOnly" value="0">
                            </div>
                            <div class="col-md-4 form-group">
                                <label for="price">Price</label>
                                <input type="text" name="price" id="price" class="form-control floatOnly req" value="0" />
                            </div>
                            <div class="col-md-6 form-group">
                                <label for="gst_per">GST Per.(%)</label>
                                <select name="gst_per" id="gst_per" class="form-control select2">
                                    <?php
                                        foreach($this->gstPer as $per=>$text):
                                            echo '<option value="'.$per.'">'.$text.'</option>';
                                        endforeach;
                                    ?>
                                </select>
                            </div>
                            <div class="col-md-6 form-group">
                                <label for="cod_date">Delivery Date</label>
                                <input type="date" name="cod_date" id="cod_date" class="form-control" min="<?=date("Y-m-d")?>" value="">
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
<script src="<?php echo base_url(); ?>assets/js/custom/proforma-invoice-form.js?v=<?= time() ?>"></script>
<script src="<?php echo base_url(); ?>assets/js/custom/calculate.js?v=<?= time() ?>"></script>
<script src="<?=base_url()?>assets/plugins/tinymce/tinymce.min.js?v=<?= time() ?>"></script>
<script>
    $(document).ready(function(){
        initEditor({
            selector: '#conditions',
            height: 400
        });
    });
</script>

<?php
if(!empty($dataRow->itemList)):
    foreach($dataRow->itemList as $row):
        $row->row_index = "";
        $row->gst_per = floatVal($row->gst_per);
        echo '<script>AddRow('.json_encode($row).');</script>';
    endforeach;
endif;
?>