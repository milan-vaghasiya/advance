<?php $this->load->view('includes/header'); ?>
<div class="page-content-tab">
	<div class="container-fluid">
        <div class="row">
            <div class="col-12">
				<div class="col-12">
                    <div class="card">
                        <div class="card-body">
							<form autocomplete="off" data-res_function="resJournalEntry" id="saveJournalEntry">
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

										<div class="col-md-3">
											<label for="trans_number">Journal No.</label>

											<div class="input-group">
												<input type="text" name="trans_prefix" id="trans_prefix" class="form-control" value="<?=(!empty($dataRow->trans_prefix))?$dataRow->trans_prefix:((!empty($trans_prefix))?$trans_prefix:"")?>">
												<input type="text" name="trans_no" id="trans_no" class="form-control numericOnly" value="<?=(!empty($dataRow->trans_no))?$dataRow->trans_no:((!empty($trans_no))?$trans_no:"")?>">
											</div>

											<input type="hidden" name="trans_number" id="trans_number" class="form-control" value="<?=(!empty($dataRow->trans_number))?$dataRow->trans_number:((!empty($trans_number))?$trans_number:"")?>" readonly>
										</div>

										<div class="col-md-3">
											<label for="trans_date">Journal Date</label>
											<input type="date" id="trans_date" name="trans_date" class="form-control fyDates req" value="<?= (!empty($dataRow->trans_date)) ? $dataRow->trans_date : getFyDate() ?>" />
										</div>

									</div>
								</div>

								<hr>

								<div class="col-md-12 row">
									<div class="col-md-6">
										<h4>Journal Details : </h4>
									</div>
									<div class="col-md-6">
										<button type="button" class="btn btn-outline-success waves-effect float-right add-item"><i class="fa fa-plus"></i> Add New Entry</button>
									</div>
								</div>
								<div class="col-md-12 mt-3">
									<div class="error item_name_error"></div>
									<div class="row form-group">
										<div class="table-responsive ">
											<table id="journalEntryData" class="table table-striped table-borderless" >
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
														<th colspan="2" class="error total_cr_dr_amt"></th>
													</tr>
												</tfoot>
											</table>
										</div>
									</div>

									<hr>

									<div class="row form-group">
										<div class="col-md-12">
											<div class="row">
												<div class="col-md-12 form-group">
													<label for="remark">Remark</label>
													<input type="text" name="remark" class="form-control" value="<?= (!empty($dataRow->remark)) ? $dataRow->remark : "" ?>" />
												</div>
											</div>
										</div>
									</div>
								</div>
							
							</form>
							</div>
                        <div class="card-footer bg-facebook">
                            <div class="col-md-12"> 
                                <button type="button" class="btn waves-effect waves-light btn-success float-right save-form" onclick="customStore({'formId':'saveJournalEntry'});" ><i class="fa fa-check"></i> Save </button>

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
	<div class="modal-dialog" role="document">
		<div class="modal-content animated slideDown">
			<div class="modal-header">
				<h4 class="modal-title">Add or Update Entry</h4>
			</div>
			<div class="modal-body">
				<form id="itemForm">
					<div class="col-md-12">
						<div class="row form-group">
                            <div id="itemInputs">
                                <input type="hidden" name="id" id="id" value="" />		
                                <input type="hidden" name="row_index" id="row_index" value="">
                                <input type="hidden" name="ledger_name" id="ledger_name" value="" />
                            </div>

							<div class="col-md-12 form-group">
								<label for="acc_id">Ledger</label>
								<select name="acc_id" id="acc_id" class="form-control select2 partyDetails req" data-res_function="resPartyDetail">
									<option value="">Select Ledger</option>
									<?=getPartyListOption($partyList)?>
								</select>
							</div>

							<div class="col-md-7 form-group">
								<label for="price">Amount</label>
								<input type="text" name="price" id="price" class="form-control floatOnly" value="0">
							</div>

							<div class="col-md-5 form-group">
								<label for="gst_per">CR./DR.</label>
								<select name="cr_dr" id="cr_dr" class="form-control">
									<option value="">Select Type</option>
									<option value="CR">Credit</option>
									<option value="DR">Debit</option>
								</select>
							</div>

							<div class="col-md-12 form-group">
								<label for="item_remark">Remark</label>
								<input type="text" name="item_remark" id="item_remark" class="form-control" value="">
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
<script src="<?php echo base_url(); ?>assets/js/custom/journal-entry-form.js?v=<?= time() ?>"></script>

<?php
if(!empty($dataRow->ledgerData)):
    foreach($dataRow->ledgerData as $row):
        $row->row_index = "";
        $row->price = $row->amount;
        $row->cr_dr = $row->c_or_d;
        $row->credit_amount = ($row->c_or_d=='CR') ? $row->amount : 0;
		$row->debit_amount = ($row->c_or_d=='DR') ? $row->amount : 0;
        $row->item_remark = $row->remark;
        echo '<script>AddRow('.json_encode($row).');</script>';
    endforeach;
endif;
?>