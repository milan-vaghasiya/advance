<form>
    <div class="col-md-12">
        <div class="row">

            <input type="hidden" name="id" id="id" value="<?=(!empty($gateInwardData->id))?$gateInwardData->id:""?>">
            <input type="hidden" name="trans_prefix" id="trans_prefix" value="<?=(!empty($gateInwardData->trans_prefix))?$gateInwardData->trans_prefix:$trans_prefix?>">
            <input type="hidden" name="trans_no" id="trans_no" value="<?=(!empty($gateInwardData->trans_no))?$gateInwardData->trans_no:$trans_no?>">

            <div class="col-md-2 form-group">
                <label for="trans_no">GRN No.</label>
                <input type="text" name="trans_number" id="trans_number" class="form-control" value="<?=(!empty($gateInwardData->trans_number))?$gateInwardData->trans_number:$trans_number?>" readonly>
            </div>

            <div class="col-md-2 form-group">
                <label for="trans_date">GRN Date</label>
                <input type="date" name="trans_date" id="trans_date" class="form-control" value="<?=(!empty($gateInwardData->trans_date))?$gateInwardData->trans_date:getFyDate("Y-m-d")?>">
            </div>
            
            <div class="col-md-2 form-group">
                <label for="type">GRN Type</label>
                <select name="type" id="type" class="form-control select2">
                    <option value="1" <?=((!empty($gateInwardData->type) && $gateInwardData->type == 1) ? 'selected' : '')?>>Purchase</option>
                    <option value="2" <?=((!empty($gateInwardData->type) && $gateInwardData->type == 2) ? 'selected' : '')?>>Jobwork</option>
                    <!--<option value="3" <?=((!empty($gateInwardData->type) && $gateInwardData->type == 3) ? 'selected' : '')?>>Customer Return</option>-->
                </select>
            </div>

            <div class="col-md-6 form-group">
                <label for="party_id">Party Name</label>
                <select name="party_id" id="party_id" class="form-control select2">
                    <option value="">Select Party Name</option>
                    <?=getPartyListOption($partyList,((!empty($gateInwardData->party_id))?$gateInwardData->party_id:""))?>
                </select>                
            </div>

            <div class="col-md-2 form-group">
                <label for="doc_no">CH/Inv. No.</label>
                <input type="text" name="doc_no" id="doc_no" class="form-control req text-uppercase" value="<?=(!empty($gateInwardData->doc_no))?$gateInwardData->doc_no:""?>">
            </div>

            <div class="col-md-2 form-group">
                <label for="doc_date">CH/Inv. Date</label>
                <input type="date" name="doc_date" id="doc_date" class="form-control req" value="<?=(!empty($gateInwardData->doc_date))?$gateInwardData->doc_date:getFyDate("Y-m-d")?>" >
            </div>

            <div class="col-md-4 form-group">
                <label for="po_id">Purchase Order</label>
                <select id="po_id" class="form-control select2">
                    <option value="">Select Purchase Order</option>
                </select>
                <div class="error po_id"></div>
                <input type="hidden" id="po_trans_id" value="">
            </div>

			<div class="col-md-4 form-group">
                <label for="item_id">Item Name</label>
                <select id="item_id" class="form-control itemDetails select2 req" data-res_function="resItemDetail">
                    <option value="">Select Item Name</option>
                    <?php 
                    foreach($itemList as $row):
                        echo '<option value="'.$row->id.'" data-item_type="'.$row->item_type.'">'.(!empty($row->item_code) ? '[ '.$row->item_code.' ] ' : '').$row->item_name.(!empty($row->material_grade) ? ' '.$row->material_grade : '').'</option>';
                    endforeach;
                    ?>
                </select>
                <input type="hidden" id="so_trans_id" value="">
            </div>  
            
            <div class="col-md-4 form-group">
                <label for="fg_item_id">Finish Goods</label>
                <select id="fg_item_id" class="form-control select2">
                    <option value="">Select Finish Goods</option>
                    <?php echo (!empty($fgoption)? $fgoption :'')?>
                </select>
            </div>       
          
			<div class="col-md-3 form-group">
                <label for="heat_no">Ref./Heat No.</label>
				<input type="text" id="heat_no" class="form-control" value="">
            </div>

            <div class="col-md-2 form-group">
                <label for="qty">Qty</label><span id="uom_span" class="float-right"></span>
                <input type="text" id="qty" class="form-control floatOnly req calcComQty" value="0">
                <input type="hidden" id="com_qty" value="0">
                <input type="hidden" id="com_unit" class="calcComQty" value="0">
            </div>

            <div class="col-md-3 form-group">  
                <label for="price">Price</label>
                <input type="text" id="price" class="form-control floatVal  req" value="">
            </div> 

            <div class="col-md-3 form-group">
                <label for="location_id">Location</label>
                <select id="location_id" class="form-control select2 req">
                    <option value="">Select Location</option>
                    <?= getLocationListOption($locationList);?>
                </select>  
            </div>
            
            <div class="col-md-9 form-group">  
                <label for="item_remark">Remark</label>
                <div class="input-group">
                <input type="text" id="item_remark" class="form-control" value="">
                    <button type="button" class="btn btn-info float-center addBatch" >
                        <i class="fas fa-plus"></i>Add
                    </button>
                </div>
            </div> 
        </div>

        <hr>

        <div class="row">
            <div class="error batch_details"></div>
            <div class="table-responsive">
                <table id="batchTable" class="table table-bordered">
                    <thead class="thead-dark">
                        <tr>
                            <th>#</th>
                            <th>PO No</th>
                            <th>Item Name</th>
                            <th>Finish Goods</th>
                            <th>Ref./Heat No.</th>
                            <th>Qty</th>
                            <th>Price</th>
                            <th>Location</th>
                            <th>Remark</th>
                            <th>Action</th>
                        </tr>
                    </thead>
                    <tbody id="batchData">                            
                        <tr id="noData">
                            <td class="text-center" colspan="9">No data available in table</td>
                        </tr>
                    </tbody>
                </table>
            </div>
        </div>
    </div>
</form>
<script src="<?php echo base_url();?>assets/js/custom/gate-inward-form.js?V=<?=time()?>"></script>
<?php
if(!empty($gateInwardData->itemData)):
	foreach($gateInwardData->itemData as $row):
		echo "<script>AddBatchRow(".json_encode($row).");</script>";
	endforeach;
endif;
?>