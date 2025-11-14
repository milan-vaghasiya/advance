
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
        <div class="col-md-12 form-group">Settlement Details : </div>
        <div class="col-md-12 form-group">
            <div class="table-responsive">
                <table class="table table-bordered" id="settlement">
                    <thead class="thead-dark">
                        <tr>
                            <th>Vou. No.</th>
                            <th>Vou. Date</th>
                            <th>Vou. Amount</th>
                            <th>Settlement Amount</th>
                            <th class="text-center">Action</th>
                        </tr>
                    </thead>
                    <tbody id="settlementTransaction">
                        <tr>
                            <td colspan="5" class="text-center">No data available in table</td>
                        </tr>                           
                    </tbody>
                </table>
            </div>
        </div>
    </div>
</div>

<script>
$(document).ready(function(){
    var settlementTransaction = {'postData':{'ag_ref_id':$("#id").val()},'table_id':"settlement",'tbody_id':'settlementTransaction','tfoot_id':'','fnget':'getSettledTransaction'};
    getTransHtml(settlementTransaction);
});

function resTrashBillWise(data){
    if(data.status==1){
        loadData();

        var settlementTransaction = {'postData':{'ag_ref_id':$("#id").val()},'table_id':"settlement",'tbody_id':'settlementTransaction','tfoot_id':'','fnget':'getSettledTransaction'};
        getTransHtml(settlementTransaction);

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