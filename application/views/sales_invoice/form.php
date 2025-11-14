<?php $this->load->view('includes/header'); ?>
<div class="page-content-tab">
	<div class="container-fluid">
        <div class="row">
            <div class="col-12">
				<div class="col-12">
                    <div class="card">
                        <div class="card-body">
                            <form autocomplete="off" id="saveSalesInvoice" data-res_function="resSaveInvoice" enctype="multipart/form-data">
                                <div class="col-md-12">
                                    <div class="row">
                                        <div class="hiddenInput">
                                            <input type="hidden" name="id" id="id" class="trans_main_id" value="<?=(!empty($dataRow->id))?$dataRow->id:""?>">
                                            <input type="hidden" name="entry_type" id="entry_type" value="<?=(!empty($dataRow->entry_type))?$dataRow->entry_type:$entry_type?>">
                                            <input type="hidden" name="from_entry_type" id="from_entry_type" value="<?=(!empty($dataRow->from_entry_type))?$dataRow->from_entry_type:($packingData->from_entry_type??'')?>">
                                            <input type="hidden" name="ref_id" id="ref_id" value="<?=(!empty($dataRow->ref_id))?$dataRow->ref_id:($packingData->ref_id??'')?>">

                                            <input type="hidden" name="party_name" id="party_name" value="<?=(!empty($dataRow->party_name))?$dataRow->party_name:""?>">
                                            <input type="hidden" name="gst_type" id="gst_type" value="<?=(!empty($dataRow->gst_type))?$dataRow->gst_type:""?>">
                                            <input type="hidden" name="party_state_code" id="party_state_code" value="<?=(!empty($dataRow->party_state_code))?$dataRow->party_state_code:""?>">
                                            <input type="hidden" name="tax_class" id="tax_class" value="<?=(!empty($dataRow->tax_class))?$dataRow->tax_class:""?>">
                                            <input type="hidden" name="sp_acc_id" id="sp_acc_id" value="<?=(!empty($dataRow->sp_acc_id))?$dataRow->sp_acc_id:0?>">

                                            <input type="hidden" id="inv_type" value="SALES">
                                            <input type="hidden" id="tcs_applicable" value="">
                                            <input type="hidden" id="tcs_limit" value="">
                                            <input type="hidden" id="defual_tcs_per" value="">
                                            <input type="hidden" id="turnover" value="">
                                            <input type="hidden" id="vou_name_s" value="<?=(!empty($entryData))?$entryData->vou_name_short:""?>">
                                        </div>

                                        <div class="col-md-2 form-group">
                                            <label for="trans_number">Inv. No.</label>
                                            <div class="input-group">
                                                <!-- <input type="text" name="trans_prefix" id="trans_prefix" class="form-control" value="<?=(!empty($dataRow->trans_prefix))?$dataRow->trans_prefix:((!empty($trans_prefix))?$trans_prefix:"")?>" readonly> -->

                                                <select name="trans_prefix" id="trans_prefix" class="form-control">
                                                    <?php
                                                        $transPrefix = ['T/'.$this->shortYear.'/', 'EX/'.$this->shortYear.'/', 'S/'.$this->shortYear.'/'];
                                                        foreach($transPrefix as $prefix):
                                                            echo '<option value="'.$prefix.'" '.((!empty($dataRow->trans_prefix) && $dataRow->trans_prefix == $prefix)?"selected":"").'>'.$prefix.'</option>';
                                                        endforeach;
                                                    ?>
                                                </select>

                                                <input type="text" name="trans_no" id="trans_no" class="form-control numericOnly" value="<?=(!empty($dataRow->trans_no))?$dataRow->trans_no:((!empty($trans_no))?$trans_no:"")?>">
                                            </div>
                                            <input type="hidden" name="trans_number" id="trans_number" class="form-control" value="<?=(!empty($dataRow->trans_number))?$dataRow->trans_number:((!empty($trans_number))?$trans_number:"")?>" readonly>
                                            <div class="error trans_number"></div>
                                        </div>

                                        <div class="col-md-2 form-group">
                                            <label for="trans_date">Inv. Date</label>
                                            <input type="date" name="trans_date" id="trans_date" class="form-control" value="<?=(!empty($dataRow->trans_date))?$dataRow->trans_date:getFyDate()?>">
                                        </div>

                                        <div class="col-md-5 form-group">
                                            <label for="party_id">Customer Name</label>
                                            <div class="float-right">	
                                                
                                                <!-- <span class="dropdown float-right m-r-5">
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

                                                <span class="float-right m-r-10">
                                                    <a class="text-primary font-bold waves-effect waves-dark getPendingOrders" href="javascript:void(0)">+ Sales Order</a>
                                                </span> -->

                                                <span class="dropdown float-right m-r-10" >
                                                    <a class="text-primary font-bold waves-effect waves-dark" href="" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false" datatip="Progress" flow="down">+ Create Inv.</a>

                                                    <div class="dropdown-menu dropdown-menu-right user-dd animated flipInY" x-placement="start-left" style="left: -87px;">
                                                        <div class="d-flex no-block align-items-center p-10 bg-primary text-white">ACTION</div>

                                                        <button type="button" class="dropdown-item getPendingOrders"><i class="fa fa-plus"></i> Sales Order</button>  

                                                        <!--<button type="button" class="dropdown-item getPendingChallan"><i class="fa fa-plus"></i> Delivery Challan</button>-->
                                                    </div>                                                    
                                                </span>
                                            </div>

                                            <select name="party_id" id="party_id" class="form-control select2 partyDetails partyOptions req" data-res_function="resPartyDetail" data-party_category="1,2">
                                                <option value="">Select Party</option>
                                                <?=getPartyListOption($partyList,((!empty($dataRow->party_id))?$dataRow->party_id:($packingData->party_id??0)))?>
                                            </select>

                                            <small>Cl. Balance : <span id="closing_balance">0</span></small>
                                            <small class="float-right">T.O. : <span id="Turnover">0</span></small>
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

                                        <div class="col-md-3 form-group">
                                            <label for="memo_type">Memo Type</label>
                                            <select name="memo_type" id="memo_type" class="form-control">
                                                <option value="DEBIT" <?=(!empty($dataRow->memo_type) && $dataRow->memo_type == "DEBIT")?"selected":""?> >Debit</option>
                                                <option value="CASH" <?=(!empty($dataRow->memo_type) && $dataRow->memo_type == "CASH")?"selected":""?> >Cash</option>
                                            </select>
                                        </div>

                                        <div class="col-md-3 form-group">
                                            <label for="sp_acc_id">GST Type </label>
                                            <select name="tax_class_id" id="tax_class_id" class="form-control select2 req">
                                                <?=getTaxClassListOption($taxClassList,((!empty($dataRow->tax_class_id))?$dataRow->tax_class_id:0))?>
                                            </select>
                                        </div>

                                        <div class="col-md-3 form-group hidden">
                                            <label for="doc_no">PO. No.</label>
                                            <input type="text" name="doc_no" id="doc_no" class="form-control" value="<?=(!empty($dataRow->doc_no))?$dataRow->doc_no:""?>">
                                        </div>

                                        <div class="col-md-3 form-group hidden">
                                            <label for="doc_date">PO. Date</label>
                                            <input type="date" name="doc_date" id="doc_date" class="form-control" value="<?=(!empty($dataRow->doc_date))?$dataRow->doc_date:getFyDate()?>">
                                        </div>

                                        <div class="col-md-3 form-group">
                                            <label for="master_i_col_2">Transport Name</label>
                                            <select name="masterDetails[i_col_2]" id="master_i_col_2" class="form-control select2">
                                                <option value="">Select Transporter</option>
                                                <?php
                                                    foreach($transportList as $row):
                                                        $selected = (!empty($dataRow->transport_id) && $dataRow->transport_id == $row->id)?"selected":((!empty($packingData->transport_id) && $packingData->transport_id == $row->id)?'selected':'');
                                                        echo '<option value="'.$row->id.'" data-t_id="'.$row->transport_id.'" '.$selected.'>'.$row->transport_name.'</option>';
                                                    endforeach;
                                                ?>
                                            </select>
                                            <input type="hidden" name="masterDetails[t_col_4]" id="master_t_col_4" value="<?=(!empty($dataRow->transporter_name))?$dataRow->transporter_name:((!empty($packingData->transport_name))?$packingData->transport_name:"")?>">                                            
                                        </div>

                                        <div class="col-md-3 form-group">
                                            <label for="master_t_col_5">Trasport Id</label>
                                            <input type="text" name="masterDetails[t_col_5]" id="master_t_col_5" class="form-control" value="<?=(!empty($dataRow->transporter_gst_no))?$dataRow->transporter_gst_no:((!empty($packingData->transporter_gst_no))?$packingData->transporter_gst_no:"")?>" readonly />
                                        </div>

                                        <div class="col-md-2 form-group hidden">
                                            <label for="challan_no">Challan No.</label>
                                            <input type="text" name="challan_no" class="form-control" placeholder="Enter Challan No." value="<?= (!empty($dataRow->challan_no)) ? $dataRow->challan_no : "" ?>" />
                                        </div>

                                        <div class="col-md-2 form-group hidden">
                                            <label for="apply_round">Apply Round Off</label>
                                            <select name="apply_round" id="apply_round" class="form-control">
                                                <option value="1" <?= (!empty($dataRow) && $dataRow->apply_round == 1) ? "selected" : "" ?>>Yes</option>
                                                <option value="0" <?= (!empty($dataRow) && $dataRow->apply_round == 0) ? "selected" : "" ?>>No</option>
                                            </select>
                                        </div>
                                        
                                        <div class="col-md-2 form-group hidden">
                                            <label for="master_t_col_1">Contact Person</label>
                                            <input type="text" name="masterDetails[t_col_1]" id="master_t_col_1" class="form-control" value="<?=(!empty($dataRow->contact_person))?$dataRow->contact_person:""?>">
                                        </div>

                                        <div class="col-md-2 form-group hidden">
                                            <label for="master_t_col_2">Contact No.</label>
                                            <input type="text" name="masterDetails[t_col_2]" id="master_t_col_2" class="form-control numericOnly" value="<?=(!empty($dataRow->contact_no))?$dataRow->contact_no:""?>">
                                        </div>

                                        <div class="col-md-3 form-group">
                                            <label for="ship_to">Ship To</label>
                                            <select name="ship_to" id="ship_to" class="form-control select2">
                                                <option value="">Select Party</option>
                                                <?=getPartyListOption($partyList,(!empty($dataRow->ship_to) ? $dataRow->ship_to : 0))?>
                                            </select>
                                        </div>

                                        <div class="col-md-2 form-group">
                                            <label for="vehicle_no">Vehicle No.</label>
                                            <input type="text" name="vehicle_no" id="vehicle_no" class="form-control" value="<?=(!empty($dataRow->vehicle_no))?$dataRow->vehicle_no:""?>">
                                        </div>
										
                                        <div class="col-md-2 form-group">
                                            <label for="lr_no">LR No.</label>
                                            <input type="text" name="lr_no" id="lr_no" class="form-control" value="<?=(!empty($dataRow->lr_no))?$dataRow->lr_no:""?>">
                                        </div>

                                        <div class="col-md-2 form-group">
                                            <label for="lr_date">LR Date</label>
                                            <input type="date" name="lr_date" id="lr_date" class="form-control" value="<?=(!empty($dataRow->lr_date))?$dataRow->lr_date:date('Y-m-d')?>">
                                        </div>

                                        <div class="col-md-3 form-group">
                                            <label for="sys_per">Sys. Code</label>
                                            <input type="text" name="sys_per" id="sys_per" class="form-control numericOnly" value="<?=(!empty($dataRow->sys_per))?floatval($dataRow->sys_per):"100"?>">
                                        </div>

                                        <div class="col-md-2 form-group exportData <?=(empty($dataRow))?"hidden":((!empty($dataRow->tax_class) && !in_array($dataRow->tax_class,["EXPORTGSTACC","EXPORTTFACC"]))?"hidden":"")?>">
                                            <label for="port_code">Port Code</label>
                                            <input type="text" name="port_code" id="port_code" class="form-control" value="<?=(!empty($dataRow->port_code))?$dataRow->port_code:""?>">
                                        </div>

                                        <div class="col-md-2 form-group exportData <?=(empty($dataRow))?"hidden":((!empty($dataRow->tax_class) && !in_array($dataRow->tax_class,["EXPORTGSTACC","EXPORTTFACC"]))?"hidden":"")?>">
                                            <label for="ship_bill_no">Shipping Bill No.</label>
                                            <input type="text" name="ship_bill_no" id="ship_bill_no" class="form-control" value="<?=(!empty($dataRow->ship_bill_no))?$dataRow->ship_bill_no:""?>">
                                        </div>

                                        <div class="col-md-2 form-group exportData <?=(empty($dataRow))?"hidden":((!empty($dataRow->tax_class) && !in_array($dataRow->tax_class,["EXPORTGSTACC","EXPORTTFACC"]))?"hidden":"")?>">
                                            <label for="ship_bill_date">Shipping Bill Date</label>
                                            <input type="date" name="ship_bill_date" id="ship_bill_date" class="form-control" value="<?=(!empty($dataRow->ship_bill_date))?$dataRow->ship_bill_date:""?>">
                                        </div>

                                        <div class="col-md-2 form-group exportData <?=(empty($dataRow))?"hidden":((!empty($dataRow->tax_class) && !in_array($dataRow->tax_class,["EXPORTGSTACC","EXPORTTFACC"]))?"hidden":"")?>">
                                            <label for="currency">Currency</label>
                                            <input type="text" name="currency" id="currency" class="form-control" value="<?=(!empty($dataRow->currency))?$dataRow->currency:""?>" readonly />
                                        </div>

                                        <div class="col-md-2 form-group exportData <?=(empty($dataRow))?"hidden":((!empty($dataRow->tax_class) && !in_array($dataRow->tax_class,["EXPORTGSTACC","EXPORTTFACC"]))?"hidden":"")?>">
                                            <label for="inrrate">Currency Rate</label>
                                            <input type="text" name="inrrate" id="inrrate" class="form-control floatOnly" value="<?=(!empty($dataRow->inrrate))?$dataRow->inrrate:""?>">
                                        </div>
                                    </div>

                                    <hr>

                                    <div class="col-md-12" id="itemForm">
                                        <!-- <div class="col-md-6"><h4>Item Details : </h4></div>
                                        <div class="col-md-6">
                                            <button type="button" class="btn btn-outline-success waves-effect float-right add-item"><i class="fa fa-plus"></i> Add Item</button>
                                        </div> -->
                                        <div class="row form-group">
                                            <div id="itemInputs">
                                                <input type="hidden" id="id" class="itemFormInput" value="" />
                                                <input type="hidden" id="from_entry_type" class="itemFormInput" value="" />
                                                <input type="hidden" id="ref_id" class="itemFormInput" value="" />
                                                <input type="hidden" id="request_id" class="itemFormInput" value="" />
                                                <input type="hidden" id="row_index" class="itemFormInput" value="">
                                                <input type="hidden" id="item_code" class="itemFormInput" value="" />
                                                <input type="hidden" id="item_name" class="itemFormInput" value="" />
                                                <input type="hidden" id="item_type" class="itemFormInput" value="1" />
                                                <input type="hidden" id="stock_eff" class="itemFormInput" value="1" />
                                                <input type="hidden" id="sys_price" class="itemFormInput" value="0" />
                                                <input type="hidden" id="make" class="itemFormInput" value="" />
                                            </div>

                                            <div class="col-md-4 form-group">
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
                                                <select id="item_id" class="form-control select2 itemDetails itemOptions itemFormInput partyReq" data-res_function="resItemDetail" data-item_type="1">
                                                    <option value="">Select Product Name</option>
                                                    <?=getItemListOption($itemList); ?>
                                                </select>
                                            </div>
                                            <div class="col-md-2 form-group">
                                                <label for="qty">Quantity</label>
                                                <input type="text" id="qty" class="form-control floatOnly req itemFormInput" value="0">
                                            </div>
                                            <div class="col-md-2 form-group">
                                                <label for="disc_per">Disc. (%)</label>
                                                <input type="text" id="disc_per" class="form-control floatOnly itemFormInput" value="0">
                                            </div>                                            
                                            <div class="col-md-2 form-group">
                                                <label for="price">Price</label>
                                                <input type="text" id="price" class="form-control floatOnly req calculatePrice itemFormInput" value="0" />
                                            </div>
                                            <div class="col-md-2 form-group">
                                                <label for="org_price">MRP</label>
                                                <input type="text" id="org_price" class="form-control floatOnly calculatePrice itemFormInput" value="0" />
                                            </div>
                                            <div class="col-md-2 form-group hidden">
                                                <label for="unit_id">Unit</label>        
                                                <select id="unit_id" class="form-control select2 itemFormInput">
                                                    <option value="">Select Unit</option>
                                                    <?php /*echo getItemUnitListOption($unitList)*/ ?>
                                                </select> 
                                                <input type="hidden" id="unit_name" class="form-control itemFormInput" value="" />                       
                                            </div>
                                            <div class="col-md-2 form-group hidden">
                                                <label for="hsn_code">HSN Code</label>
                                                <input type="text" id="hsn_code" class="form-control numericOnly req itemFormInput" value="" />
                                            </div>
                                            <div class="col-md-2 form-group hidden">
                                                <label for="gst_per">GST Per.(%)</label>
                                                <select id="gst_per" class="form-control select2 itemFormInput">
                                                    <?php
                                                        foreach($this->gstPer as $per=>$text):
                                                            echo '<option value="'.floatVal($per).'">'.$text.'</option>';
                                                        endforeach;
                                                    ?>
                                                </select>
                                            </div>

                                            <div id="batchTransactions" class="col-md-12 form-group hidden">
                                                <h4>Batch Detail : </h4>
                                                <div class="error batchError"></div>
                                                <div class="table table-responsive">
                                                    <table id="batchDetail" class="table table-bordered">
                                                        <thead class="thead-dark">
                                                            <tr>
                                                                <th>Location</th>
                                                                <th>Batch No.</th>
                                                                <th>Stock</th>
                                                                <th> Qty.</th>
                                                            </tr>
                                                        </thead>
                                                        <tbody id="batchTrans">
                                                            <tr>
                                                                <td colspan="4" class="text-center">No data available in table</td>
                                                            </tr>
                                                        </tbody>
                                                        <tfoot class="thead-dark">
                                                            <tr>
                                                                <th colspan="3" class="text-right">Total Qty</th>
                                                                <th>
                                                                    <span id="total_qty">0</span>
                                                                </th>
                                                            </tr>
                                                        </tfoot>
                                                    </table>
                                                </div>
                                            </div>   

                                            <div class="col-md-10 form-group">
                                                <label for="item_remark">Remark</label>
                                                <input type="text" id="item_remark" class="form-control itemFormInput" value="" />
                                            </div>
                                            <div class="col-md-2 form-group">
                                                <label for="">&nbsp;</label>
                                                <button type="button" class="btn btn-outline-success btn-block saveItem" style="line-height: 1.8;"><i class="fa fa-plus"></i> Add</button>
                                            </div>
                                        </div>
                                    </div>

                                    <div class="col-md-12 mt-3">
                                        <div class="error itemData"></div>
                                        <div class="row form-group">
                                            <div class="table-responsive">
                                                <table id="salesInvoiceItems" class="table table-striped table-borderless">
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

                                    <?php $this->load->view('includes/terms_form',['termsList'=>$termsList,'termsConditions'=>(!empty($dataRow->termsConditions)) ? $dataRow->termsConditions : array()])?>
                                </div>
                            </form>
                        </div>
                        <div class="card-footer bg-facebook">
                            <div class="col-md-12"> 
                                <button type="button" class="btn btn-success waves-effect show_terms" >Terms & Conditions</button>
                                <span class="term_error text-danger font-bold"></span>

                                <button type="button" class="btn waves-effect waves-light btn-success float-right save-form" onclick="customStore({'formId':'saveSalesInvoice','txt_editor':'conditions'});" ><i class="fa fa-check"></i> Save </button>

                                <button type="button" class="btn btn-secondary press-close-btn btn-close-modal save-form float-right m-r-10" onclick="window.location.href='<?=base_url($headData->controller)?>'"><i class="fa fa-times"></i> Cancel</button>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- <div class="modal modal-right fade" id="itemModel" role="dialog" data-bs-backdrop="static" data-bs-keyboard="false">
    <div class="modal-dialog modal-md" role="document">
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
                                <input type="hidden" name="stock_eff" id="stock_eff" value="1" />
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
                                <select name="item_id" id="item_id" class="form-control select2 itemDetails itemOptions" data-res_function="resItemDetail" data-item_type="1">
                                    <option value="">Select Product Name</option>
									<?php /*echo getItemListOption($itemList)*/ ?>
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
                            <div class="col-md-4 form-group">
                                <label for="unit_id">Unit</label>        
                                <select name="unit_id" id="unit_id" class="form-control select2">
                                    <option value="">Select Unit</option>
                                    <?php /*echo getItemUnitListOption($unitList)*/ ?>
                                </select> 
                                <input type="hidden" name="unit_name" id="unit_name" class="form-control" value="" />                       
                            </div>
                            <div class="col-md-4 form-group">
                                <label for="hsn_code">HSN Code</label>
                                <input type="text" name="hsn_code" id="hsn_code" class="form-control numericOnly req" value="" />
                            </div>
                            <div class="col-md-4 form-group">
                                <label for="gst_per">GST Per.(%)</label>
                                <select name="gst_per" id="gst_per" class="form-control select2">
                                    <?php
                                        /*foreach($this->gstPer as $per=>$text):
                                            echo '<option value="'.$per.'">'.$text.'</option>';
                                        endforeach;*/
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
</div> -->

<?php $this->load->view('includes/footer'); ?>
<script src="<?php echo base_url(); ?>assets/js/custom/sales-invoice-form.js?v=<?= time() ?>"></script>
<script src="<?php echo base_url(); ?>assets/js/custom/calculate.js?v=<?= time() ?>"></script>
<script src="<?=base_url()?>assets/plugins/tinymce/tinymce.min.js"></script>
<script>
var taxSummary = <?=json_encode(((!empty($dataRow->id))?$dataRow:array()))?>;
</script>
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
if(!empty($packingData->itemData)):
    // print_r("<pre>");print_r($packingData->itemData);exit;
    foreach($packingData->itemData as $row):
        $row->ref_id = $row->so_trans_id;
        $row->qty = $row->total_box_qty;
        $row->row_index = "";
        $row->disc_per = 0;
        $row->disc_amount = 0;
        $row->stock_eff = 0;
        $row->from_entry_type = $packingData->from_entry_type;
        $row->batch_detail="";
        $row->taxable_amount = $row->amount = $row->qty * $row->price;
        $row->igst_per = $row->gst_per = floatVal($row->gst_per);
        $row->igst_amount = $row->gst_amount = ( $row->gst_per * $row->amount)/100;
        $row->sgst_per = $row->cgst_per = $row->gst_per/2;
        $row->sgst_amount = $row->cgst_amount =( $row->cgst_per * $row->amount)/100;
        $row->unit_id = 0;
        $row->sys_price =$row->price;
        $row->org_price = $row->price;
        $row->unit_name = $row->uom;
        $row->item_type = 1;
        $row->net_amount = $row->taxable_amount + $row->gst_amount;
        $row->item_remark = "";
        $row->id="";
        echo '<script>AddRow('.json_encode($row).');</script>';
    endforeach;
endif;
?>