<form>
    <div class="col-md-12">
        <div class="row">
            <input type="hidden" name="id" id="payment_id" value="<?= (!empty($dataRow->id)) ? $dataRow->id : ""; ?>" />
            <input type="hidden" name="vou_name_s" id="vou_name_s" value="<?= (!empty($dataRow->vou_name_s)) ? $dataRow->vou_name_s : "BCGSTPmt"; ?>" />
            <input type="hidden" name="entry_type" id="entry_type" value="<?=(!empty($dataRow->entry_type))?$dataRow->entry_type:$entry_type?>">

            <input type="hidden" name="trans_prefix" id="trans_prefix" value="<?= (!empty($dataRow->trans_prefix)) ? $dataRow->trans_prefix : $trans_prefix ?>" />
            <input type="hidden" name="trans_no" id="trans_no" value="<?= (!empty($dataRow->trans_no)) ? $dataRow->trans_no : $trans_no ?>" />

            <div class="col-md-3 form-group">
                <label for="trans_no">Voucher No.</label>
                <input type="text" name="trans_number" id="trans_number" class="form-control req" value="<?= (!empty($dataRow->trans_number)) ? $dataRow->trans_number : $trans_number ?>" readonly />
            </div>

            <div class="col-md-3 form-group">
                <label for="trans_date">Voucher Date</label>
                <input type="date" class="form-control req fyDates" name="trans_date" value="<?=(!empty($dataRow->trans_date))?$dataRow->trans_date:getFyDate()?>">
            </div>

            <div class="col-md-6 form-group">
                <label>Bank/Cash Account</label>
                <small class="float-right">Balance : <span  id="vou_acc_balance">0</span></small>
                <select name="vou_acc_id" id="vou_acc_id" class="form-control partyDetails select2 req" data-res_function="resVouAcc">
                    <option value="">Select Ledger</option>
                    <?=getPartyListOption($ledgerList,((!empty($dataRow->vou_acc_id))?$dataRow->vou_acc_id:0))?>
                </select>
            </div>

            <div class="col-md-2 form-group">
                <label>CHL. No.</label>
                <input type="text" class="form-control" id="doc_no" name="doc_no" value="<?= (!empty($dataRow->doc_no)) ? $dataRow->doc_no : ""; ?>">
            </div>

            <div class="col-md-2 form-group">
                <label>CHL. Date</label>
                <input type="date" class="form-control" id="doc_date" name="doc_date" max="<?=getFyDate()?>" value="<?= (!empty($dataRow->doc_date)) ? $dataRow->doc_date : getFyDate(); ?>">
            </div>

            <div class="col-md-8 form-group">
                <label for="remark">Note</label>
                <input type="text" name="remark" id="remark" class="form-control" value="<?= (!empty($dataRow->remark)) ? $dataRow->remark : ""; ?>">
            </div>

            <div class="col-md-12 form-group">
                <div class="error general_error"></div>
                <div class="table table-responsive">
                    <table class="table table-bordered">
                        <thead class="thead-dark">
                            <tr>
                                <th>GST Type</th>
                                <th>GST</th>
                                <th>Interest</th>
                                <th>Penalty</th>
                                <th>Fees</th>
                                <th>Other</th>
                                <th>Total</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php
                                $ledgerData = [];
                                if(!empty($dataRow->ledgerData)):
                                    $ledgerData = array_reduce($dataRow->ledgerData, function($itemData, $row) {
                                        $itemData[$row->vou_acc_id] = $row->amount;
                                        return $itemData;
                                    }, []);
                                endif;

                                $i=1;$j=1;
                                echo '<tr>';
                                echo '<td>IGST</td>';

                                foreach($igstLedgerList as $row):
                                    echo '<td>
                                        <input type="text" name="itemData['.$i.'][amount]" class="form-control floatOnly calculateAmount igst col'.$j.' '.$row->system_code.'" value="'.((!empty($ledgerData[$row->id]))?$ledgerData[$row->id]:"").'">
                                        <input type="hidden" name="itemData['.$i.'][acc_id]" value="'.$row->id.'">
                                    </td>';
                                    $i++;$j++;
                                endforeach;

                                echo '<td>
                                    <input type="text" name="igst_amount" id="igst_amount" class="form-control netAmount" value="" readonly>
                                </td>';
                                echo '</tr>';

                                $j=1;
                                echo '<tr>';
                                echo '<td>CGST</td>';

                                foreach($cgstLedgerList as $row):
                                    echo '<td>
                                        <input type="text" name="itemData['.$i.'][amount]" class="form-control floatOnly calculateAmount cgst col'.$j.' '.$row->system_code.'" value="'.((!empty($ledgerData[$row->id]))?$ledgerData[$row->id]:"").'">
                                        <input type="hidden" name="itemData['.$i.'][acc_id]" value="'.$row->id.'">
                                    </td>';
                                    $i++;$j++;
                                endforeach;

                                echo '<td>
                                    <input type="text" name="cgst_amount" id="cgst_amount" class="form-control netAmount" value="" readonly>
                                </td>';
                                echo '</tr>';

                                $j=1;
                                echo '<tr>';
                                echo '<td>SGST</td>';

                                foreach($sgstLedgerList as $row):
                                    echo '<td>
                                        <input type="text" name="itemData['.$i.'][amount]" class="form-control floatOnly calculateAmount sgst col'.$j.' '.$row->system_code.'" value="'.((!empty($ledgerData[$row->id]))?$ledgerData[$row->id]:"").'">
                                        <input type="hidden" name="itemData['.$i.'][acc_id]" value="'.$row->id.'">
                                    </td>';
                                    $i++;$j++;
                                endforeach;

                                echo '<td>
                                    <input type="text" name="sgst_amount" id="sgst_amount" class="form-control netAmount" value="" readonly>
                                </td>';
                                echo '</tr>';

                                $j=1;
                                echo '<tr>';
                                echo '<td>CESS</td>';

                                foreach($cessLedgerList as $row):
                                    echo '<td>
                                        <input type="text" name="itemData['.$i.'][amount]" class="form-control floatOnly calculateAmount cess col'.$j.' '.$row->system_code.'" value="'.((!empty($ledgerData[$row->id]))?$ledgerData[$row->id]:"").'">
                                        <input type="hidden" name="itemData['.$i.'][acc_id]" value="'.$row->id.'">
                                    </td>';
                                    $i++;$j++;
                                endforeach;

                                echo '<td>
                                    <input type="text" name="cess_amount" id="cess_amount" class="form-control netAmount" value="" readonly>
                                </td>';
                                echo '</tr>';
                            ?>
                        </tbody>
                        <tfoot class="thead-dark">
                            <tr>
                                <th>Total</th>
                                <th>
                                    <input type="text" id="gst_amount" class="form-control" value="" readonly>
                                </th>
                                <th>
                                    <input type="text" id="interest_amount" class="form-control" value="" readonly>
                                </th>
                                <th>
                                    <input type="text" id="penalty_amount" class="form-control" value="" readonly>
                                </th>
                                <th>
                                    <input type="text" id="fess_amount" class="form-control" value="" readonly>
                                </th>
                                <th>
                                    <input type="text" id="other_amount" class="form-control" value="" readonly>
                                </th>
                                <th>
                                    <input type="text" name="net_amount" id="net_amount" class="form-control" value="" readonly>
                                </th>
                            </tr>
                        </tfoot>
                    </table>
                </div>
            </div>
        </div>
    </div>
</form>

<script>
var old_no = ""; var old_prefix = "";
$(document).ready(function(){
	setTimeout(function(){ $(".calculateAmount").trigger('change'); },500);

    $(".partyDetails").trigger('change');

    $(document).on('change keyup','.calculateAmount',function(){ calculateAmount(); });
});


function resVouAcc(response=""){
    if(response != ""){
        var partyDetail = response.data.partyDetail;
        $("#vou_acc_balance").html(inrFormat(partyDetail.closing_balance)+' '+partyDetail.closing_type);
    }else{
		$("#vou_acc_balance").html(0);    
    }
    initSelect2();
}

function calculateAmount(){
    var igstArray = $(".igst").map(function () { return $(this).val(); }).get();
	var igstSum = 0;
	$.each(igstArray, function () { igstSum += parseFloat(this) || 0; });
    $("#igst_amount").val(igstSum.toFixed(2));

    var sgstArray = $(".sgst").map(function () { return $(this).val(); }).get();
	var sgstSum = 0;
	$.each(sgstArray, function () { sgstSum += parseFloat(this) || 0; });
    $("#sgst_amount").val(sgstSum.toFixed(2));

    var cgstArray = $(".cgst").map(function () { return $(this).val(); }).get();
	var cgstSum = 0;
	$.each(cgstArray, function () { cgstSum += parseFloat(this) || 0; });
    $("#cgst_amount").val(cgstSum.toFixed(2));

    var cessArray = $(".cess").map(function () { return $(this).val(); }).get();
	var cessSum = 0;
	$.each(cessArray, function () { cessSum += parseFloat(this) || 0; });
    $("#cess_amount").val(cessSum.toFixed(2));

    var gstArray = $(".col1").map(function () { return $(this).val(); }).get();
	var gstSum = 0;
	$.each(gstArray, function () { gstSum += parseFloat(this) || 0; });
    $("#gst_amount").val(gstSum.toFixed(2));

    var interestArray = $(".col2").map(function () { return $(this).val(); }).get();
	var interestSum = 0;
	$.each(interestArray, function () { interestSum += parseFloat(this) || 0; });
    $("#interest_amount").val(interestSum.toFixed(2));

    var penaltyArray = $(".col3").map(function () { return $(this).val(); }).get();
	var penaltySum = 0;
	$.each(penaltyArray, function () { penaltySum += parseFloat(this) || 0; });
    $("#penalty_amount").val(penaltySum.toFixed(2));

    var fessArray = $(".col4").map(function () { return $(this).val(); }).get();
	var fessSum = 0;
	$.each(fessArray, function () { fessSum += parseFloat(this) || 0; });
    $("#fess_amount").val(fessSum.toFixed(2));

    var otherArray = $(".col4").map(function () { return $(this).val(); }).get();
	var otherSum = 0;
	$.each(otherArray, function () { otherSum += parseFloat(this) || 0; });
    $("#other_amount").val(otherSum.toFixed(2));

    var netAmtArray = $(".netAmount").map(function () { return $(this).val(); }).get();
	var netAmtSum = 0;
	$.each(netAmtArray, function () { netAmtSum += parseFloat(this) || 0; });
    $("#net_amount").val(netAmtSum.toFixed(2));
}
</script>