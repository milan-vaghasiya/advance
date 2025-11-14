<form>
    <div class="col-md-12">
        <div class="row">
            <input type="hidden" name="main_ref_id" id="main_ref_id" value="<?=$main_ref_id?>">
            <div class="col-md-4 form-group">
                <label for="entry_date">Date</label>
                <input type="date" name="entry_date" id="entry_date" class="form-control fyDates" max="<?=getFyDate()?>" value="<?=getFyDate()?>">
            </div>
            <div class="col-md-4 form-group">
                <label for="received_by">Received By</label>
                <input type="text" name="received_by" id="received_by" class="form-control" value="">
            </div>
            <div class="col-md-4 form-group">
                <label for="amount">Amount</label>
                <input type="text" name="amount" id="amount" class="form-control floatOnly" value="">
            </div>
            <div class="col-md-12 form-group">
                <label for="remark">Remark</label>
                <div class="input-group">
                    <input type="text" name="remark" id="remark" class="form-control" value="">
                    <div class="input-group-append">
                        <button type="button" class="btn waves-effect waves-light btn-outline-success btn-save save-form" onclick="customStore({'formId':'estimatePayment','controller':'estimate','fnsave':'saveEstimatePayment'});"><i class="fa fa-check"></i> Save</button>
                    </div>
                </div>
            </div>
        </div>
    </div>
</form>
<hr>
<div class="col-md-12">
    <div class="row">
        <div class="col-md-12">
            <div class="table table-responsive">
                <table id="estimatePaymentDetails" class="table table-bordered">
                    <thead class="thead-dark">
                        <tr>
                            <th>#</th>
                            <th>Date</th>
                            <th>Received By</th>
                            <th>Amount</th>
                            <th>Remark</th>
                            <th>Action</th>
                        </tr>
                    </thead>
                    <tbody id="esPaymentData">
                        <tr>
                            <td class="text-center" colspan="6">No data available in table</td>
                        </tr>
                    </tbody>
                </table>
            </div>
        </div>
    </div>
</div>

<script>
$(document).ready(function(){
    var paymentTrans = {'postData':{'main_ref_id':$("#estimatePayment #main_ref_id").val()},'table_id':"estimatePaymentDetails",'tbody_id':'esPaymentData','tfoot_id':'','fnget':'getEstimatePaymentTrans'};
    getTransHtml(paymentTrans);
});

function resSaveEstimatePayment(data,formId){
    if(data.status==1){
        var main_ref_id = $("#estimatePayment #main_ref_id").val();
        $('#'+formId)[0].reset();
        $("#estimatePayment #main_ref_id").val(main_ref_id);

        toastr.success(data.message, 'Success', { "showMethod": "slideDown", "hideMethod": "slideUp", "closeButton": true, positionClass: 'toastr toast-bottom-center', containerId: 'toast-bottom-center', "progressBar": true });

        var paymentTrans = {'postData':{'main_ref_id':$("#estimatePayment #main_ref_id").val()},'table_id':"estimatePaymentDetails",'tbody_id':'esPaymentData','tfoot_id':'','fnget':'getEstimatePaymentTrans'};
        getTransHtml(paymentTrans);
    }else{
        if(typeof data.message === "object"){
            $(".error").html("");
            $.each( data.message, function( key, value ) {$("."+key).html(value);});
        }else{
            initTable();
            toastr.error(data.message, 'Error', { "showMethod": "slideDown", "hideMethod": "slideUp", "closeButton": true, positionClass: 'toastr toast-bottom-center', containerId: 'toast-bottom-center', "progressBar": true });
        }			
    }	
}

function resTrashEstimatePayment(data){
    if(data.status==1){
        toastr.success(data.message, 'Success', { "showMethod": "slideDown", "hideMethod": "slideUp", "closeButton": true, positionClass: 'toastr toast-bottom-center', containerId: 'toast-bottom-center', "progressBar": true });

        var paymentTrans = {'postData':{'main_ref_id':$("#estimatePayment #main_ref_id").val()},'table_id':"estimatePaymentDetails",'tbody_id':'esPaymentData','tfoot_id':'','fnget':'getEstimatePaymentTrans'};
        getTransHtml(paymentTrans);
    }else{
        if(typeof data.message === "object"){
            $(".error").html("");
            $.each( data.message, function( key, value ) { $("."+key).html(value); });
        }else{
            toastr.error(data.message, 'Error', { "showMethod": "slideDown", "hideMethod": "slideUp", "closeButton": true, positionClass: 'toastr toast-bottom-center', containerId: 'toast-bottom-center', "progressBar": true });
        }			
    }
}
</script>