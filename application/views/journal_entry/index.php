<?php $this->load->view('includes/header'); ?>
<div class="page-content-tab">
	<div class="container-fluid">
        <div class="row">
            <div class="col-sm-12">
				<div class="page-title-box">
					<div class="float-end">
                        <a href="javascript:void(0)" class="btn waves-effect waves-light btn-outline-dark float-right permission-write press-add-btn m-l-5" onclick="window.location.href='<?=base_url($headData->controller.'/addGstHavalaEntry')?>'"><i class="fa fa-plus"></i> GST Havala</a>

                        <a href="javascript:void(0)" class="btn waves-effect waves-light btn-outline-dark float-right permission-write press-add-btn" onclick="window.location.href='<?=base_url($headData->controller.'/addGstJournalEntry')?>'"><i class="fa fa-plus"></i> GST Journal</a>
					</div>
                    <h4 class="card-title">Journal Entry</h4>
				</div>
            </div>
		</div>

        <div class="row">
            <div class="col-12">
                <div class="col-12">
                    <div class="card">
                        <div class="card-body">
                            <form autocomplete="off" data-res_function="resJournalEntry" id="journalEntryForm">
                                <div class="col-md-12">

                                    <div class="hiddenInput">
                                        <input type="hidden" name="id" id="id" value="<?=(!empty($dataRow->id))?$dataRow->id:""?>">
                                        <input type="hidden" name="entry_type" id="entry_type" value="<?=(!empty($dataRow->entry_type))?$dataRow->entry_type:$entry_type?>">
                                    </div>

                                    <div class="row form-group">

                                        <div class="col-md-2 form-group <?=($this->cm_id_count == 1)?"hidden":""?>">
                                            <label for="cm_id">Select Unit</label>
                                            <select name="cm_id" id="cm_id" class="form-control" data-selected_cm_id="<?=(!empty($dataRow->cm_id))?$dataRow->cm_id:""?>">
                                                <?=getCompanyListOptions($companyList,((!empty($dataRow->cm_id))?$dataRow->cm_id:""))?>
                                            </select>
                                        </div>

                                        <div class="col-md-2">
                                            <label for="trans_number">Journal No.</label>

                                            <div class="input-group">
                                                <input type="text" name="trans_prefix" id="trans_prefix" class="form-control" value="<?=(!empty($dataRow->trans_prefix))?$dataRow->trans_prefix:((!empty($trans_prefix))?$trans_prefix:"")?>">
                                                <input type="text" name="trans_no" id="trans_no" class="form-control numericOnly" value="<?=(!empty($dataRow->trans_no))?$dataRow->trans_no:((!empty($trans_no))?$trans_no:"")?>">
                                            </div>

                                            <input type="hidden" name="trans_number" id="trans_number" class="form-control" value="<?=(!empty($dataRow->trans_number))?$dataRow->trans_number:((!empty($trans_number))?$trans_number:"")?>" readonly>
                                        </div>

                                        <div class="col-md-2">
                                            <label for="trans_date">Journal Date</label>
                                            <input type="date" id="trans_date" name="trans_date" class="form-control fyDates req" value="<?= (!empty($dataRow->trans_date)) ? $dataRow->trans_date : getFyDate() ?>" />
                                        </div>

                                        <div class="col-md-6 form-group">
                                            <label for="remark">Remark</label>
                                            <input type="text" name="remark" class="form-control" value="<?= (!empty($dataRow->remark)) ? $dataRow->remark : "" ?>" />
                                        </div>
                                    </div>
                                </div>

                                <hr>

                                <!-- <div class="col-md-12 row">
                                    <div class="col-md-12">
                                        <h5>Journal Details : </h5>
                                    </div>
                                    <div class="col-md-6">
                                        <button type="button" class="btn btn-outline-success waves-effect float-right add-item"><i class="fa fa-plus"></i> Add New Entry</button>
                                    </div>
                                </div> -->
                                <div class="col-md-12">
                                    <div class="error item_name_error"></div>
                                    <div class="error total_cr_dr_amt"></div>
                                    <div class="row">
                                        <div class="table-responsive ">
                                            <table id="journalEntryData" class="table table-striped table-borderless">
                                                <thead class="thead-dark">
                                                    <tr>
                                                        <th style="width:5%;">#</th>
                                                        <th>Ledger</th>
                                                        <th>CR</th>
                                                        <th>DR</th>
                                                        <th>Remark</th>
                                                        <th class="text-center" style="width:10%;">Action</th>
                                                    </tr>
                                                </thead>
                                                <tbody id="tempItem" class="temp_item">
                                                    <tr id="noData">
                                                        <td colspan="6" class="text-center">No data available in table</td>
                                                    </tr>
                                                </tbody>
                                                <tfoot class="thead-dark">
                                                    <tr>
                                                        <th colspan="2" class="font-bold">Total</th>
                                                        <th id="total_cr_amount" class="font-bold">0.00</th>
                                                        <th id="total_dr_amount" class="font-bold">0.00</th>
                                                        <th colspan="2"></th>
                                                    </tr>
                                                    <tr id="itemForm">
                                                        <td colspan="2" style="width:30%;">
                                                            <div id="itemInputs">
                                                                <input type="hidden" id="id" class="itemFormInput" value="" />		
                                                                <input type="hidden" id="row_index" class="itemFormInput" value="">
                                                                <input type="hidden" id="ledger_name" class="itemFormInput" value="" />
                                                            </div>

                                                            <select id="acc_id" class="form-control select2 partyDetails itemFormInput req" data-res_function="resPartyDetail">
                                                                <option value="">Select Ledger</option>
                                                                <?=getPartyListOption($partyList)?>
                                                            </select>
                                                        </td>

                                                        <td style="width:20%;">
                                                            <div class="input-group">
                                                                <select id="cr_dr" class="form-control itemFormInput" style="width:40%;">
                                                                    <option value="CR">Credit</option>
                                                                    <option value="DR">Debit</option>
                                                                </select>
                                                                <input type="text" id="price" class="form-control floatOnly itemFormInput" value="0" style="width:60%;">
                                                            </div>
                                                        </td>

                                                        <td colspan="2">
                                                            <input type="text" id="item_remark" class="form-control itemFormInput" value="">
                                                        </td>

                                                        <td class="text-center">
                                                            <button type="button" class="btn waves-effect waves-light btn-outline-success saveItem btn-save" data-fn="save"><i class="fa fa-check"></i></button>
                                                        </td>
                                                    </tr>
                                                </tfoot>
                                            </table>
                                        </div>
                                    </div>
                                </div>
                            
                            </form>
                        </div>
                        <div class="card-footer bg-facebook">
                            <div class="col-md-12"> 
                                <button type="button" class="btn waves-effect waves-light btn-success float-right save-form" onclick="customStore({'formId':'journalEntryForm'});" ><i class="fa fa-check"></i> Save </button>

                                <button type="button" class="btn btn-secondary press-close-btn btn-close-modal save-form float-right m-r-10" onclick="window.location.href='<?=base_url($headData->controller)?>'"><i class="fa fa-times"></i> Cancel</button>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    <!-- </div>
</div>

<div class="page-content-tab">
	<div class="container-fluid"> -->
        
        <div class="row">
            <div class="col-12">
				<div class="col-12">
					<div class="card">
                        <div class="card-body">
                            <div class="table-responsive">
                                <table id='journalEntryTable' class="table table-bordered ssTable ssTable-cf" data-url='/getDTRows'></table>
                            </div>
                        </div>
					</div>
				</div>
            </div>
        </div>
    </div>
</div>
<?php $this->load->view('includes/footer'); ?>
<script src="<?php echo base_url(); ?>assets/js/custom/jv-entry-form.js?v=<?= time() ?>"></script>