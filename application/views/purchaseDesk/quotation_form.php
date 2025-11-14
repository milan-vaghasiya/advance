<form>
    <div class="col-md-12">

        <div class="row">
            <div class="table-responsive">
                <table class="table table-bordered">
                    <tr class="thead-info">
                        <th>Enquiry No.</th>
                        <th>Enquiry Date</th>
                        <th>Supplier</th>
                    </tr>
                    <tr class="bg-light">
                        <td><?= (!empty($dataRow->trans_number)) ? $dataRow->trans_number : '' ?></td>
                        <td><?= (!empty($dataRow->trans_date)) ? formatDate($dataRow->trans_date) : '' ?></td>
                        <td><?= (!empty($dataRow->party_name)) ? $dataRow->party_name : '' ?></td>
                    </tr>
                </table>
            </div>       
        </div>

        <hr>
        <div class="row">

            <input type="hidden" name="id" id="id" value="" />
            <input type="hidden" name="enq_id" id="enq_id" value="<?= (!empty($dataRow->id)) ? $dataRow->id : '' ?>" />
            <input type="hidden" name="party_id" id="party_id" value="<?= (!empty($dataRow->party_id)) ? $dataRow->party_id : '' ?>" />
            <input type="hidden" name="item_id" id="item_id" value="<?= (!empty($dataRow->item_id)) ? $dataRow->item_id : '' ?>" />
            
            <div class="table-responsive">
                <table class="table table-bordered">
                    <tr class="bg-light">
                        <td>Item Name</td>
                        <td>
                            <input type="text" id="item_name" class="form-control" value="<?= (!empty($dataRow->item_name)) ? $dataRow->item_name : '' ?>" readOnly></td>
                    </tr>
                    <tr class="bg-light">
                        <td>MOQ</td>
                        <td>
                            <input type="text" name="qty" id="qty" class="form-control floatOnly" value="<?= (!empty($dataRow->qty)) ? floatval($dataRow->qty) : '' ?>">
                        </td>
                    </tr>
                    <tr class="bg-light">
                        <td>Price</td>
                        <td>
                            <input type="text" name="price" id="price" class="form-control floatOnly" value="">
                        </td>
                    </tr>
                    <tr class="bg-light">
                        <td>Lead Time (In Days)</td>
                        <td>
                            <input type="text" name="lead_time" id="lead_time" class="form-control floatOnly" value="">
                        </td>
                    </tr>
                    <tr class="bg-light">
                        <td>Feasible</td>
                        <td>
                            <select name="feasible" id="feasible" class="form-control select2">
                                <option value="1">Yes</option>
                                <option value="2">No</option>
                            </select>
                        </td>
                    </tr>
                    <tr class="bg-light">
                        <td>Quotation No</td>
                        <td>
							<input type="text" name="quote_no" id="quote_no" class="form-control" value="">                            
                        </td>
                    </tr>
                    <tr class="bg-light">
                        <td>Quotation Date</td>
                        <td>
							<input type="date" name="quote_date" id="quote_date" class="form-control " value="<?=date('Y-m-d')?>">
                        </td>
                    </tr>
                    <tr class="bg-light">
                        <td>Remark</td>
                        <td>
							<input type="text" name="quote_remark" id="quote_remark" class="form-control" value="">
                        </td>
                    </tr>
                </table>
            </div>
        </div>

    </div>
</form>