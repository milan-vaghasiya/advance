
<div class="cd-header">
    <h6 class="m-0 partyName">QUOTATION DETAIL</h6>
</div>
<div class="sop-body vh-35" data-simplebar>
    <div class="prcMaterial">
    <?php
    if(empty($enqData->quotation_count)){
        ?>
        <form id="addQuotation" data-res_function="getPurchaseResponse">
            <div class="col-md-12">
                <div class="row">
                    <input type="hidden" name="id" id="id" value="" />
                    <input type="hidden" name="enq_id" id="enq_id" value="<?= (!empty($enqData->id)) ? $enqData->id : '' ?>" />
                    <input type="hidden" name="party_id" id="party_id" value="<?= (!empty($enqData->party_id)) ? $enqData->party_id : '' ?>" />
                    <input type="hidden" name="item_id" id="item_id" value="<?= (!empty($enqData->item_id)) ? $enqData->item_id : '' ?>" />
                    
                    <div class="table-responsive">
                        <table class="table table-bordered">
                            <tr >
                                <td>Feasible</td>
                                <td>
                                    <select name="feasible" id="feasible" class="form-control select2">
                                        <option value="1">Yes</option>
                                        <option value="2">No</option>
                                    </select>
                                </td>
                            </tr>
                            <tr >
                                <td>MOQ</td>
                                <td>
                                    <input type="text" name="qty" id="qty" class="form-control floatOnly" value="<?= (!empty($enqData->qty)) ? floatval($enqData->qty) : '' ?>">
                                </td>
                            </tr>
                            <tr >
                                <td>Price</td>
                                <td>
                                    <input type="text" name="price" id="price" class="form-control floatOnly" value="">
                                </td>
                            </tr>
                            <tr >
                                <td>Lead Time (In Days)</td>
                                <td>
                                    <input type="text" name="lead_time" id="lead_time" class="form-control floatOnly" value="">
                                </td>
                            </tr>
                            <tr >
                                <td>Quotation No</td>
                                <td>
                                    <input type="text" name="quote_no" id="quote_no" class="form-control" value="">                            
                                </td>
                            </tr>
                            <tr >
                                <td>Quotation Date</td>
                                <td>
                                    <input type="date" name="quote_date" id="quote_date" class="form-control " value="<?=date('Y-m-d')?>">
                                </td>
                            </tr>
                            <tr >
                                <td>Remark</td>
                                <td>
                                    <input type="text" name="quote_remark" id="quote_remark" class="form-control" value="">
                                </td>
                            </tr>
                        </table>
                    </div>
                    <div class="col-md-12 float-end">         
                        <?php
                        $param = "{'formId':'addQuotation','fnsave':'saveQuotation','res_function':'getPurchaseResponse','controller':'purchaseDesk'}";
                        ?>       
                        <a class="btn btn-success float-end" href="javascript:void(0)" datatip="Approve" flow="down" onclick="storeQuotation(<?=$param?>)"><i class="fas fa-check"></i> Save</a>
                    </div>
                </div>
            </div>
        </form>
        <?php
    }else{
        ?>
        <form>
            <div class="col-md-12">
                <div class="row">
                    <div class="table-responsive">
                        <table class="table jpExcelTable">
                            <tr>
                                <td><b>Quotation No.</b></td>
                                <td><?= (!empty($quoteData->quote_no)) ? $quoteData->quote_no : '' ?></td>
                                <td><b>Quotation Date</b></td>
                                <td><?= (!empty($quoteData->quote_date)) ? formatDate($quoteData->quote_date) : '' ?></td>
                            </tr>
                            <tr>
                                <td><b>Feasible</b></td>
                                <td><?= ((!empty($quoteData->feasible) && $quoteData->feasible == 1)) ? 'Yes' : ((!empty($quoteData->feasible) && $quoteData->feasible == 2) ? 'No' : '') ?></td>
                                <td style="width:25%"><b>MOQ</b></td>
                                <td style="width:25%"><?= (!empty($quoteData->qty) ? floatval($quoteData->qty) : '').(!empty($quoteData->uom) ? ' '.$quoteData->uom : '') ?></td>
                            </tr>
                            <tr>
                                <td style="width:25%"><b>Price</b></td>
                                <td style="width:25%"><?= (!empty($quoteData->price)) ? floatval($quoteData->price) : '' ?></td>
                                <td><b>Lead Time (In Days)</b></td>
                                <td><?= (!empty($quoteData->lead_time)) ? $quoteData->lead_time : '' ?></td> 
                            </tr>
                            <tr>
                                <td><b>Remark</b></td>
                                <td colspan="3"><?= (!empty($quoteData->quote_remark)) ? $quoteData->quote_remark : '' ?></td>
                            </tr>
                        </table>
                    </div>

                    <?php
                    if($quoteData->trans_status == 1){
                        $approveParam = "{'postData':{'id' : ".$quoteData->id.", 'enq_id' : '".$quoteData->enq_id."', 'val' : '2', 'msg' : 'Approved'}, 'message' : 'Are you sure you want to Approve this Quotation ?', 'fnsave':'chageEnqStatus', 'js_store_fn' : 'storeEnquiry', 'res_function':'getPurchaseResponse'}";

                        $rejectParam = "{'postData':{'id' : ".$quoteData->id.", 'enq_id' : '".$quoteData->enq_id."', 'val' : '3', 'msg' : 'Rejected'}, 'message' : 'Are you sure you want to Reject this Quotation ?', 'fnsave':'chageEnqStatus', 'js_store_fn' : 'storeEnquiry', 'res_function':'getPurchaseResponse'}";
                        ?>
                        <div class="col-md-6">                
                            <a class="btn btn-block btn-success" href="javascript:void(0)" datatip="Approve" flow="down" onclick="confirmPurchaseStore(<?=$approveParam?>)"><i class="fas fa-check"></i> Approve</a>
                        </div>

                        <div class="col-md-6">
                            <a class="btn btn-block btn-danger" href="javascript:void(0)" datatip="Reject" flow="down" onclick="confirmPurchaseStore(<?=$rejectParam?>)"><i class="fas fa-close"></i> Reject</a>
                        </div>
                        <?php
                        
                    }
                    ?>
                </div>
            </div>
        </form>
        <?php
    }
    ?>
    </div>
</div>

