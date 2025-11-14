<form autocomplete="off">
    <div class="col-md-12">
        <div class="row">
            <div class="col-md-6">
                <label for="party_name">Party Name : <br><span id="party_name"></span></label>
            </div>
            <div class="col-md-3">
                <label for="enquiry_no">Enquiry No. : <br><span id="enquiry_no"></span></label>
            </div>
            <div class="col-md-3">
                <label for="enquiry_date">Enquiry Date : <br><span id="enquiry_date"></span></label>
            </div>
        </div>
        <input type="hidden" name="enq_id" id="enq_id" value="" />
        <hr>
        <div class="error item_name_error"></div>
        <div class="table-responsive">
            <table class="table table-bordered align-items-center">
                <thead class="thead-info">
                    <tr>
                        <th class="text-center">#</th>
                        <th>Item Name</th>
                        <th>Feasible</th>
                        <th>MOQ</th>
                        <th style="width:80px">Price</th>
                        <th style="width:80px">Lead Time (In Days)</th>
                        <th>Make</th>
                        <th>Quotation No</th>
                        <th>Quotation Date</th>
						<th>Delivery Date</th>
                        <th>Remark</th>
                    </tr>
                </thead>
                <tbody id="enquiryData">
                    <?php if(!empty($dataRow)): echo $dataRow; else:?>
                    <tr><td colspan="11" class="text-center">No data available in table</td></tr>
                    <?php endif; ?>
                </tbody>
            </table>
        </div>
    </div>
</form>