<?php $this->load->view('includes/header'); ?>
<div class="page-content-tab">
	<div class="container-fluid">
        <div class="row">
            <div class="col-12">
				<div class="col-12">
                    <div class="card">
                        <div class="card-body">
                            <form autocomplete="off" id="saveJwo" data-res_function="resSaveJwo" enctype="multipart/form-data">
                                <div class="col-md-12">
                                    <div class="row">
                                        <div class="hiddenInput">
                                            <input type="hidden" name="id" id="id" class="trans_main_id" value="<?=((!empty($is_revision) && $is_revision == 1) ? "" : (!empty($dataRow->id)?$dataRow->id:""));?>">
                                            <input type="hidden" name="ref_id" id="ref_id" class="ref_id" value="<?=((!empty($is_revision) && $is_revision == 1) ? (!empty($dataRow->id)?$dataRow->id:"") : "");?>">
                                            <input type="hidden" name="trans_no" id="trans_no" class="form-control numericOnly req" value="<?= ((!empty($is_revision) && $is_revision == 1) ? $trans_no : (!empty($dataRow->trans_no)?$dataRow->trans_no:((!empty($trans_no))?$trans_no:"")))?>">
                                        </div>

										<div class="col-md-3 form-group">
                                            <label for="trans_number">Order No.</label>
                                            <input type="text" name="trans_number" id="trans_number" class="form-control" value="<?= ((!empty($is_revision) && $is_revision == 1) ? $trans_number : (!empty($dataRow->trans_number)?$dataRow->trans_number:((!empty($trans_number))?$trans_number:"")))?>" readonly>
                                            <div class="error trans_number"></div>
                                        </div>

                                        <div class="col-md-3 form-group">
                                            <label for="order_date">Order Date</label>
                                            <input type="date" name="order_date" id="order_date" class="form-control" value="<?=(!empty($dataRow->trans_date))?$dataRow->trans_date:getFyDate()?>">
                                        </div>

                                        <div class="col-md-6 form-group">
                                            <label for="vendor_id">Vendor</label>
                                            <select name="vendor_id" id="vendor_id" class="form-control select2 req">
                                                <option value="">Select Vendor</option>
                                                <?=getPartyListOption($vendorList,((!empty($dataRow->vendor_id))?$dataRow->vendor_id:0))?>
                                            </select>
                                        </div>
                                    </div>
                                    <hr>

                                    <div class="col-md-12" id="itemForm">
                                        <div class="col-md-6"><h4>Item Details : </h4></div>
                                        
                                        <div class="row form-group">
                                            <div id="itemInputs">
                                                <input type="hidden" id="id" class="itemFormInput" value="" />
                                                <input type="hidden" id="row_index" class="itemFormInput" value="">
                                            </div>

                                            <div class="col-md-4 form-group">
                                                <label for="item_id">Product Name</label>
                                                <select id="item_id" class="form-control select2 itemFormInput partyReq" data-res_function="resItemDetail" data-item_type="1">
                                                    <option value="">Select Product Name</option>
                                                    <?=getItemListOption($itemList); ?>
                                                </select>
                                            </div>
                                            <div class="col-md-4 form-group">
                                                <label for="process_id">Process</label>
                                                <select id="process_id" class="form-control select2 req itemFormInput">
                                                </select>
                                            </div>
                                            
                                            <div class="col-md-2 form-group">
                                                <label for="rate_per_unit">Rate Per</label>
                                                <select  id="rate_per_unit" class="form-control select2 req itemFormInput">
                                                    <option value="">Select Rate Per</option> 
                                                    <option value="PCS" >Per Pcs.</option>
                                                    <option value="KGS" >Per Kg.</option>
                                                </select>
                                            </div>
                                                                                    
                                            <div class="col-md-2 form-group">
                                                <label for="rate">Rate</label>
                                                <input type="text" id="rate" class="form-control floatOnly req calculatePrice itemFormInput" value="0" />
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
                                                            <th>Process</th>
                                                            <th>Unit</th>
                                                            <th>Rate</th>
                                                            <th>Remark</th>
                                                            <th class="text-center" style="width:10%;">Action</th>
                                                        </tr>
                                                    </thead>
                                                    <tbody id="tempItem" class="temp_item">
                                                        <tr id="noData">
                                                            <td colspan="8" class="text-center">No data available in table</td>
                                                        </tr>
                                                    </tbody>
                                                </table>
                                            </div>
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

                                <button type="button" class="btn waves-effect waves-light btn-success float-right save-form" onclick="customStore({'formId':'saveJwo','txt_editor':'conditions'});" ><i class="fa fa-check"></i> Save </button>

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
<script src="<?php echo base_url(); ?>assets/js/custom/jobwork-order-form.js?v=<?= time() ?>"></script>
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
if(!empty($dataRow->itemData)):
    foreach($dataRow->itemData as $row):
        $row->row_index = "";
		if(!empty($is_revision) && $is_revision == 1){
			$row->ref_id = $row->id;
			$row->is_revision = $is_revision;
		}
        echo '<script>AddRow('.json_encode($row).');</script>';
    endforeach;
endif;
?>