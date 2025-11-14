<form>
<div class="col-12">
        <div class="row">
            <!-- Column -->
            <div class="col-lg-12 col-xlg-12 col-md-12">
                <div class="card">
                    <div class="card-header" style="background-color:#45729f ;color:white ;">
                        <h5 class="card-title">Loan Detail</h5>
                    </div>
                    <input type="hidden" name="id" value="<?=(!empty($dataRow->id))?$dataRow->id:""; ?>" />
                    <input type="hidden" name="sanctioned_amount" value="<?=(!empty($dataRow->approved_amount))?$dataRow->approved_amount:""; ?>" />

                    <div class="card-body scrollable" style="height:40vh;border-bottom: 5px solid #45729f;padding-bottom:5px;">
                        <div class="table-responsive">
                            <table class="table">
                                <tr>
                                    <th>Loan No.</th>
                                    <td>: <?= $dataRow->trans_number ?></td>
                                    <th>Date </th>
                                    <td>: <?= date("d-m-Y H:i:s", strtotime($dataRow->entry_date)) ?></td>
                                    <th>Employee </th>
                                    <td>: <?= $dataRow->emp_name ?></td>
                                </tr>
                                <tr>
                                    <th>Deman Amount </th>
                                    <td>: <?= $dataRow->demand_amount ?></td>
                                    <th>Total EMI </th>
                                    <td>: <?= floatval($dataRow->total_emi) ?></small></td>
                                    <th>EMI Amount</th>
                                    <td>: <?= floatval($dataRow->emi_amount)?></small></td>
                                    
                                </tr>
                                <tr>
                                    <th>Approved Amount </th>
                                    <td>: <?= $dataRow->approved_amount ?></td>
                                    <th>Approved By </th>
                                    <td>: <?= ($dataRow->approve_by_name) ?></small></td>
                                    <th>Approved At</th>
                                    <td>: <?= date("Y-m-d H:i:d",strtotime($dataRow->approved_at))?></small></td>
                                </tr>
                              
                            </table>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</form>

<script>
$(document).ready(function(){
    $(document).on('change',".calEMI",function(){   
        
        if($(this).prop('id') == 'emi_amount'){$("#total_emi").val('');}
        if($(this).prop('id') == 'total_emi'){$("#emi_amount").val('');}
        
        var amount = parseFloat($("#demand_amount").val());
		var total_emi = parseFloat($("#total_emi").val());
		var emi_amount = parseFloat($("#emi_amount").val());
        var emiAmt = 0;var totalEmi = 0;
        if((total_emi > 0 || emi_amount > 0) && amount > 0)
        {
            if(emi_amount > 0){total_emi = parseFloat((parseFloat(amount) / parseFloat(emi_amount)).toFixed());}
            if(total_emi > 0){emi_amount = parseFloat((parseFloat(amount) / parseFloat(total_emi))).toFixed(2); }
        }
        $("#total_emi").val(total_emi);
        $("#emi_amount").val(emi_amount);
    });

});

</script>