<?php $this->load->view('includes/header'); ?>
<div class="page-content-tab">
	<div class="container-fluid">
        <div class="row">
            <div class="col-12">
				<div class="col-12">
                    <div class="card">
                        <div class="card-body">
                            <form autocomplete="off" id="saveFinalPacking" data-res_function="resFinalPacking" enctype="multipart/form-data">
                                <div class="col-md-12">
                                    <div class="row">
                                        <div class="hiddenInput">
                                            <input type="hidden" name="id"  value="<?=(!empty($dataRow->id))?$dataRow->id:""?>">                                          
                                        </div>

                                        <div class="col-md-2 form-group">
                                            <label for="trans_number">Pck. No.</label>
                                            <input type="text" name="trans_number" id="trans_number" class="form-control numericOnly" value="<?=(!empty($dataRow->trans_number))?$dataRow->trans_number:((!empty($trans_number))?$trans_number:"")?>" readonly>
                                        </div>

                                        <div class="col-md-2 form-group">
                                            <label for="trans_date">Pck. Date</label>
                                            <input type="date" name="trans_date" id="trans_date" class="form-control" value="<?=(!empty($dataRow->trans_date))?$dataRow->trans_date:getFyDate()?>">
                                        </div>

                                        <div class="col-md-3 form-group">
                                            <label for="party_id">Customer Name</label>
                                    
                                            <select name="party_id" id="party_id" class="form-control select2 req  partyOptions" data-party_category="1" >
                                                <option value="">Select Party</option>
                                                <?=getPartyListOption($partyList,((!empty($dataRow->party_id))?$dataRow->party_id:0))?>
                                            </select>
                                        </div>

                                        <div class="col-md-3 form-group">
                                            <label for="transport_id">Transport Name</label>
                                            <select name="transport_id" id="transport_id" class="form-control select2">
                                                <option value="">Select Transporter</option>
                                                <?php
                                                    foreach($transportList as $row):
                                                        $selected = (!empty($dataRow->transport_id) && $dataRow->transport_id == $row->id)?"selected":"";
                                                        echo '<option value="'.$row->id.'" data-t_id="'.$row->transport_id.'" '.$selected.'>'.$row->transport_name.'</option>';
                                                    endforeach;
                                                ?>
                                            </select>
                                        </div>

                                        <div class="col-md-2 form-group">
                                            <label for="vehicle_no">Vehicle No</label>
                                            <input type="text" name="vehicle_no" id="vehicle_no" class="form-control" value="<?=(!empty($dataRow->vehicle_no))?$dataRow->vehicle_no:""?>">
                                        </div>

                                        <div class="col-md-2 form-group">
                                            <label for="lr_no">LR No.</label>
                                            <input type="text" name="lr_no" id="lr_no" class="form-control" value="<?=(!empty($dataRow->lr_no))?$dataRow->lr_no:""?>">
                                        </div>

                                        <div class="col-md-2 form-group">
                                            <label for="lr_date">LR. Date</label>
                                            <input type="date" name="lr_date" id="lr_date" class="form-control" value="<?=(!empty($dataRow->lr_date))?$dataRow->lr_date:getFyDate()?>">
                                        </div>

                                        <div class="col-md-2 form-group">
                                            <label for="port_of_loading">Port of Loading</label>
                                            <input type="text" name="port_of_loading" id="port_of_loading" class="form-control " value="<?=(!empty($dataRow->port_of_loading))?$dataRow->port_of_loading:""?>" >
                                        </div>

                                        <div class="col-md-2 form-group">
                                            <label for="port_of_discharge">Port of Discharge</label>
                                            <input type="text" name="port_of_discharge" id="port_of_discharge" class="form-control" value="<?=(!empty($dataRow->port_of_discharge))?$dataRow->port_of_discharge:""?>" >
                                        </div>

                                        <div class="col-md-2 form-group">
                                            <label for="method_of_dispatch">Method of Dispatch</label>
                                            <input type="text" name="method_of_dispatch" id="method_of_dispatch" class="form-control" value="<?=(!empty($dataRow->method_of_dispatch))?$dataRow->method_of_dispatch:""?>">
                                        </div>

                                        <div class="col-md-2 form-group">
                                            <label for="type_of_shipment">Type Of Shipment</label>
                                            <input type="text" name="type_of_shipment" id="type_of_shipment" class="form-control" value="<?=(!empty($dataRow->type_of_shipment))?$dataRow->type_of_shipment:""?>">
                                        </div>

                                        <div class="col-md-2 form-group">
                                            <label for="country_of_origin">Country of Origin</label>
                                            <input type="text" name="country_of_origin" id="country_of_origin" class="form-control" value="<?=(!empty($dataRow->country_of_origin))?$dataRow->country_of_origin:""?>">
                                        </div>

                                        <div class="col-md-2 form-group">
                                            <label for="country_of_fd">Country of Final Destination</label>
                                            <input type="text" name="country_of_fd" id="country_of_fd" class="form-control" value="<?=(!empty($dataRow->country_of_fd))?$dataRow->country_of_fd:""?>">
                                        </div>

                                        <div class="col-md-3 form-group">
                                            <label for="terms_of_delivery">Terms of Delivery</label>
                                            <input type="text" name="terms_of_delivery" id="terms_of_delivery" class="form-control" value="<?=(!empty($dataRow->terms_of_delivery))?$dataRow->terms_of_delivery:""?>" >
                                        </div>

                                        <div class="col-md-3 form-group">
                                            <label for="fright_terms">Fright Terms</label>
                                            <input type="text" name="fright_terms" id="fright_terms" class="form-control" value="<?=(!empty($dataRow->fright_terms))?$dataRow->fright_terms:""?>" >
                                        </div>
                                        <div class="col-md-2 form-group">
                                            <label for="p_type">Packing Type</label>
                                            <select name="p_type" id="p_type" class="form-control select2">
                                                <option value="">Select Packing Type</option>
                                                <option value="Corrugated Box" <?=((!empty($dataRow->p_type) && $dataRow->p_type == "Corrugated Box") ? 'selected' : '')?>>Corrugated Box</option>
                                                <option value="Wooden Pallet" <?=((!empty($dataRow->p_type) && $dataRow->p_type == "Wooden Pallet") ? 'selected' : '')?>>Wooden Pallet</option>
												<option value="Wireframe Box" <?=((!empty($dataRow->p_type) && $dataRow->p_type == "Wireframe Box") ? 'selected' : '')?>>Wireframe Box</option>
												<option value="Gunny Bag" <?=((!empty($dataRow->p_type) && $dataRow->p_type == "Gunny Bag") ? 'selected' : '')?>>Gunny Bag</option>
                                            </select>
                                        </div>
                                    </div>

                                    <hr>
                                    <div class="row" id="itemForm">
                                        <div id="itemInputs">
                                            <input type="hidden"  id="id" value="" class="itemFormInput"/>
                                            <input type="hidden"  id="row_index" value="" class="itemFormInput">
                                            <input type="hidden"  id="item_id" value="" class="itemFormInput">
                                            <input type="hidden"  id="packing_type" value="" class="itemFormInput">
                                        </div>

                                        <div class="col-md-4 form-group">
                                            <label for="pack_mt_id">Packing Material</label>
                                            <select id="pack_mt_id" class="form-control select2 req">
                                                <option value="">Select Material</option>
                                                <?php
                                                if(!empty($packMtList)):
                                                    foreach($packMtList as $row):
                                                        echo '<option value="'.$row->id.'" data-wt_pcs="'.floatval($row->wt_pcs).'">'.(!empty($row->item_code) ? '['.$row->item_code.'] ' : '').$row->item_name.'</option>';
                                                    endforeach;
                                                endif;
                                                ?>
                                            </select>
                                        </div>

                                        <div class="col-md-3 form-group">
                                            <label for="std_qty">Qty</label>
                                            <input type="text" id="std_qty" class="form-control floatOnly req" value="" />
                                        </div>

                                        <div class="col-md-3 form-group">
                                            <label for="pack_wt">Packing Weight(KGS)</label>
                                            <div class="input-group">
                                                <input type="text" id="pack_wt" class="form-control floatOnly req" value="" />
                                                <div class="input-group-append">
                                                    
                                                    <button type="button" class="btn waves-effect waves-light btn-outline-success btn-save save-form float-right" onclick="addStandard()" style="height:36px"><i class="fa fa-plus"></i> Add</button>
                                                </div>
                                            </div>                
                                        </div>

                                        <div class="col-md-12 form-group row" id="packStandardDiv"></div>
                                        <div class="error packDtl"></div>
                                        
                                        <hr>
                                        <div class="col-md-4 form-group itemDiv">
                                            <label for="so_trans_id">Product </label>
                                            <select id="so_trans_id" class="form-control select2 itemList req itemFormInput">
                                                <?php
                                                if(!empty($itemOptions)){
                                                    echo $itemOptions;
                                                }else{
                                                    echo '<option value="" data-item_id="0">Select Product</option>';
                                                }
                                                ?>
                                                
                                            </select>
                                        </div>	
                                        
                                        <div class="col-md-2 form-group">
                                            <label for="total_qty">Qty Per Box (Nos)</label>
                                            <input type="text" id="total_qty" class="form-control floatOnly itemFormInput req" value="" />
                                        </div>

                                        <div class="col-md-2 form-group">
                                            <label for="total_box">No. Of Box (Nos)</label>
                                            <input type="text" id="total_box" class="form-control numericOnly itemFormInput req" value="" />
                                        </div>

                                        <div class="col-md-2 form-group">
                                            <label for="package_no">Carton No.</label>
                                            <select  id="package_no" class="form-control select2 req itemFormInput">
                                                <?php
                                                    for($i = 1; $i <= 100; $i++):
                                                        echo '<option value="'.sprintf("%02d",$i).'">'.sprintf("%02d",$i).'</option>';
                                                    endfor;
                                                ?>
                                            </select>
                                        </div>

                                        <div class="col-md-2 form-group">
                                            <button type="button" class="btn btn-success saveItem mt-20" style="height:36px;"><i class="fa fa-plus"></i> Add Item</button>
                                        </div>
                                    </div>

                                    <hr>
                                    <div class="row">
                                        <div class="col-md-6"><h4>Item Details : </h4></div>
                                    </div>
                                    <div class="row mt-3">
                                        <div class="error itemData"></div>
                                        <div class="table-responsive">
                                            <table id="packingListItems" class="table table-striped table-borderless">
                                                <thead class="thead-dark">
                                                    <tr>
                                                        <th style="width:5%;">#</th>
                                                        <th>Item Name</th>
                                                        <th>Carton No.</th>
                                                        <th>Qty.</th>
                                                        <th class="text-center" style="width:10%;">Action</th>
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
                                <button type="button" class="btn waves-effect waves-light btn-success float-right save-form" onclick="customStore({'formId':'saveFinalPacking'});" ><i class="fa fa-check"></i> Save </button>

                                <button type="button" class="btn btn-secondary press-close-btn btn-close-modal save-form float-right m-r-10" onclick="window.location.href='<?=base_url($headData->controller)?>'"><i class="fa fa-times"></i> Cancel</button>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<?php $this->load->view('includes/footer'); ?>
<script src="<?php echo base_url(); ?>assets/js/custom/final-packing-form.js?v=<?= time() ?>"></script>
<?php
if(!empty($dataRow->itemData)):
    foreach($dataRow->itemData as $row):
        $row->row_index = "";
        echo '<script>AddRow('.json_encode($row).');</script>';
    endforeach;
endif;
?>