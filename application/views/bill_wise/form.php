<form data-res_function="resSaveBillWise">
    <div class="col-md-12">
        <div class="error settlement_error"></div>
        <input type="hidden" name="id" id="id" value="<?=$dataRow->id?>">
        <div class="row">
            <div class="col-md-12 form-group">Voucher Detail : </div>
            <div class="col-md-12 form-group">
                <div class="table-responsive">
                    <table class="table table-bordered">
                        <thead class="thead-dark">
                            <tr>
                                <th>Vou. No.</th>
                                <th>Vou. Date</th>
                                <th>Vou. Amount</th>
                                <th>Unsettled Amount</th>
                            </tr>
                        </thead>
                        <tbody>
                            <tr>
                                <td><?=$dataRow->trans_number?></td>
                                <td><?=formatDate($dataRow->trans_date)?></td>
                                <td><?=numberFormatIndia(floatval($dataRow->net_amount))?></td>
                                <td><?=numberFormatIndia(floatval($dataRow->pending_amount))?></td>
                            </tr>
                        </tbody>
                    </table>
                </div>
            </div>
        </div>

        <hr>

        <div class="row">
            <div class="col-md-12 form-group">Refernce Details : </div>
            <div class="col-md-12 form-group">
                <div class="table-responsive">
                    <table class="table table-bordered">
                        <thead class="thead-dark">
                            <tr>
                                <th>Vou. No.</th>
                                <th>Vou. Date</th>
                                <th>Vou. Amount</th>
                                <th>Unsettled Amount</th>
                                <th style="width:20%;">Settlement Amount</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php
                                $i=1;
                                if(!empty($unsettledTrans)):
                                    $pendingAmt = floatval($dataRow->pending_amount);
                                    foreach($unsettledTrans as $row):
                                        $setAmt = ($row->pending_amount > $pendingAmt)?$pendingAmt:$row->pending_amount;
                                        $setAmt = ($setAmt > 0)?floatval($setAmt):0;
                                        echo '<tr>
                                            <td>'.$row->trans_number.'</td>
                                            <td>'.formatDate($row->trans_date).'</td>
                                            <td>'.numberFormatIndia(floatval($row->net_amount)).'</td>
                                            <td>'.numberFormatIndia(floatval($row->pending_amount)).'</td>
                                            <td>
                                                <input type="hidden" name="billWise['.$i.'][id]" value="">
                                                <input type="hidden" name="billWise['.$i.'][entry_type]" value="'.$dataRow->entry_type.'">
                                                <input type="hidden" name="billWise['.$i.'][trans_main_id]" value="'.$dataRow->trans_main_id.'">
                                                <input type="hidden" name="billWise['.$i.'][trans_date]" value="'.$dataRow->trans_date.'">
                                                <input type="hidden" name="billWise['.$i.'][trans_number]" value="'.$dataRow->trans_number.'">
                                                <input type="hidden" name="billWise['.$i.'][party_id]" value="'.$dataRow->party_id.'">
                                                <input type="hidden" name="billWise['.$i.'][ref_type]" value="2">
                                                <input type="hidden" name="billWise['.$i.'][ag_ref_id]" value="'.$row->id.'">
                                                <input type="text" name="billWise['.$i.'][amount]" class="form-control floatOnly settlementAmount" value="'.$setAmt.'">
                                                <div class="error amount_'.$i.'"></div>
                                            </td>
                                        </tr>';
                                        $i++;

                                        $pendingAmt -= $setAmt;
                                    endforeach;
                                else:
                                    echo '<tr>
                                        <td colspan="5" class="text-center">No data available in table</td>
                                    </tr>';
                                endif;
                            ?>                            
                        </tbody>
                        <tfoot>
                            <tr>
                                <th colspan="4" class="text-right">Total</th>
                                <th id="settlementTotal" class="text-right">0</th>
                            </tr>
                        </tfoot>
                    </table>
                </div>
            </div>
        </div>
    </div>
</form>
<script>
$(document).ready(function(){
    settlementTotal();
    $(document).on('keyup change','.settlementAmount',function(){ settlementTotal(); });
});

function settlementTotal(){
    var amountArray = $(".settlementAmount").map(function () { return $(this).val(); }).get();
	var amountSum = 0;
	$.each(amountArray, function () { amountSum += parseFloat(this) || 0; });
	$("#settlementTotal").html(amountSum.toFixed(2));
}

function resSaveBillWise(data,formId){
    if(data.status==1){
        loadData();
        $('#'+formId)[0].reset(); closeModal(formId);
        Swal.fire({ icon: 'success', title: data.message});
    }else{
        if(typeof data.message === "object"){
            $(".error").html("");
            $.each( data.message, function( key, value ) {$("."+key).html(value);});
        }else{
            loadData();
            Swal.fire({ icon: 'error', title: data.message });
        }			
    }	
}
</script>