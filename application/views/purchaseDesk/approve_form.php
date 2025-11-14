<form>
    <div class="col-md-12">
        <div class="row">
            <div class="table-responsive">
                <table class="table jpExcelTable">
                    <tr>
                        <td><b>Supplier</b></td>
                        <td colspan="3"><?= (!empty($dataRow->party_name)) ? $dataRow->party_name : '' ?></td>
                    </tr>
                    <tr>
                        <td><b>Product</b></td>
                        <td colspan="3"><?= (!empty($dataRow->item_name)) ? $dataRow->item_name : '' ?></td>
                    </tr>
                    <tr>
                        <td style="width:25%"><b>MOQ</b></td>
                        <td style="width:25%"><?= (!empty($dataRow->qty) ? floatval($dataRow->qty) : '').(!empty($dataRow->uom) ? ' '.$dataRow->uom : '') ?></td>
                        <td style="width:25%"><b>Price</b></td>
                        <td style="width:25%"><?= (!empty($dataRow->price)) ? floatval($dataRow->price) : '' ?></td>
                    </tr>
                    <tr>
                        <td><b>Lead Time (In Days)</b></td>
                        <td><?= (!empty($dataRow->lead_time)) ? $dataRow->lead_time : '' ?></td>
                        <td><b>Feasible</b></td>
                        <td><?= ((!empty($dataRow->feasible) && $dataRow->feasible == 1)) ? 'Yes' : ((!empty($dataRow->feasible) && $dataRow->feasible == 2) ? 'No' : '') ?></td>
                    </tr>
                    <tr>
                        <td><b>Quotation No.</b></td>
                        <td><?= (!empty($dataRow->quote_no)) ? $dataRow->quote_no : '' ?></td>
                        <td><b>Quotation Date</b></td>
                        <td><?= (!empty($dataRow->quote_date)) ? formatDate($dataRow->quote_date) : '' ?></td>
                    </tr>
                    <tr>
                        <td><b>Remark</b></td>
                        <td colspan="3"><?= (!empty($dataRow->remark)) ? $dataRow->remark : '' ?></td>
                    </tr>
                </table>
            </div>

            <?php
                $approveParam = "{'postData':{'id' : ".$dataRow->id.", 'enq_id' : '".$dataRow->enq_id."', 'val' : '2', 'msg' : 'Approved'}, 'message' : 'Are you sure you want to Approve this Quotation ?', 'fnsave':'chageEnqStatus', 'js_store_fn' : 'storeEnquiry', 'res_function':'loadDesk'}";

                $rejectParam = "{'postData':{'id' : ".$dataRow->id.", 'enq_id' : '".$dataRow->enq_id."', 'val' : '3', 'msg' : 'Rejected'}, 'message' : 'Are you sure you want to Reject this Quotation ?', 'fnsave':'chageEnqStatus', 'js_store_fn' : 'storeEnquiry', 'res_function':'loadDesk'}";
            ?>
            <div class="col-md-6">                
                <a class="btn btn-block btn-success" href="javascript:void(0)" datatip="Approve" flow="down" onclick="confirmPurchaseStore(<?=$approveParam?>)"><i class="fas fa-check"></i> Approve</a>
            </div>

            <div class="col-md-6">
                <a class="btn btn-block btn-danger" href="javascript:void(0)" datatip="Reject" flow="down" onclick="confirmPurchaseStore(<?=$rejectParam?>)"><i class="fas fa-close"></i> Reject</a>
            </div>

        </div>
    </div>
</form>