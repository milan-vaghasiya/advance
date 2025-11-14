<form data-res_function="resPaymentVoucher">
    <div class="col-md-12">
        <div class="row">
            <input type="hidden" name="id" id="payment_id" value="<?= (!empty($dataRow->id)) ? $dataRow->id : ""; ?>" />

            <input type="hidden" name="trans_prefix" id="trans_prefix" value="<?= (!empty($dataRow->trans_prefix)) ? $dataRow->trans_prefix : $trans_prefix ?>" />
            <input type="hidden" name="trans_no" id="trans_no" value="<?= (!empty($dataRow->trans_no)) ? $dataRow->trans_no : $trans_no ?>" />

            <div class="col-md-4 form-group">
                <label for="trans_no">Voucher No.</label>
                <input type="text" name="trans_number" id="trans_number" class="form-control req" value="<?= (!empty($dataRow->trans_number)) ? $dataRow->trans_number : $trans_number ?>" readonly />
            </div>

            <div class="col-md-4 form-group">
                <label for="trans_date">Voucher Date</label>
                <input type="date" class="form-control fyDates" name="trans_date" value="<?=(!empty($dataRow->trans_date))?$dataRow->trans_date:getFyDate()?>">
            </div>

            <div class="col-md-4 form-group">
                <label for="vou_name_s">Entry Type</label>
                <select name="vou_name_s" id="vou_name_s" class="form-control select2" >
                    <option value="BCRct" <?=(!empty($dataRow->vou_name_s) && $dataRow->vou_name_s == "BCRct")?"selected":"" ;?> >Receive</option>
                    <option value="BCPmt" <?=(!empty($dataRow->vou_name_s) && $dataRow->vou_name_s == "BCPmt")?"selected":"" ;?> >Paid</option>
                </select>
            </div>

            <div class="col-md-6 form-group">
                <label>Party Name</label> 
                <span class="float-right">Balance : <span  id="opp_acc_balance">0</span></span>
                <select name="opp_acc_id" id="opp_acc_id" class="form-control partyDetails select2" data-res_function="resOppAcc">
                    <option value="">Select Party</option>
                    <?=getPartyListOption($partyList,((!empty($dataRow->opp_acc_id))?$dataRow->opp_acc_id:0))?>
                </select>
                <input type="hidden" name="party_name" id="party_name" value="<?=(!empty($dataRow->party_name))?$dataRow->party_name:""?>">
            </div>

            <div class="col-md-6 form-group">
                <label>Bank/Cash Account</label>
                <span class="float-right">Balance : <span  id="vou_acc_balance">0</span></span>
                <select name="vou_acc_id" id="vou_acc_id" class="form-control partyDetails select2" data-res_function="resVouAcc">
                <option value="">Select Ledger</option>
                    <?=getPartyListOption($ledgerList,((!empty($dataRow->vou_acc_id))?$dataRow->vou_acc_id:0))?>
                </select>
            </div>

            <div class="col-md-3 form-group">
                <label>Amount</label>
                <input type="text" name="net_amount" id="net_amount" class="form-control floatOnly" value="<?= (!empty($dataRow->net_amount)) ? $dataRow->net_amount : ""; ?>">
            </div>

            <div class="col-md-2 form-group kasarAmount">
                <label for="round_off_amount">Kasar Amount</label>
                <input type="text" name="round_off_amount" id="round_off_amount" class="form-control floatOnly" value="<?=(!empty($dataRow->round_off_amount))?$dataRow->round_off_amount:""?>">
            </div>

            <div class="col-md-3 form-group">
                <label>Payment Mode</label>
                <select name="payment_mode" id="payment_mode" class="form-control select2" data-selected="<?=(!empty($dataRow->payment_mode)) ? $dataRow->payment_mode:''?>">
                    <option value="">Select Payment Mode</option>
                    <?php
                        foreach($this->paymentMode as $row):
                            $selected = (!empty($dataRow->payment_mode) && $row == $dataRow->payment_mode) ? "selected":"";
                            echo '<option value="'.$row.'" '.$selected.'>'.$row.'</option>';
                        endforeach;
                    ?>
                </select>
            </div>

            <div class="col-md-2 form-group">
                <label>Ref. No.</label>
                <input type="text" class="form-control" id="doc_no" name="doc_no" value="<?= (!empty($dataRow->doc_no)) ? $dataRow->doc_no : ""; ?>">
            </div>

            <div class="col-md-2 form-group">
                <label>Ref. Date</label>
                <input type="date" class="form-control" id="doc_date" name="doc_date" max="<?=getFyDate()?>" value="<?= (!empty($dataRow->doc_date)) ? $dataRow->doc_date : getFyDate(); ?>">
            </div>

            <div class="col-md-12 form-group">
                <label for="remark">Note</label>
                <input type="text" name="remark" id="remark" class="form-control" value="<?= (!empty($dataRow->remark)) ? $dataRow->remark : ""; ?>">
            </div>
         <div>
    </div>

</form>
<script>
$(document).ready(function(){

    $(".partyDetails").trigger('change');
	
	$(document).on("change","#vou_name_s",function(){       
        var vou_name_s = $("#vou_name_s").val();
        $(".entry_type").html("");
        if(vou_name_s != ''){
		    $.ajax({
				url : base_url + controller + '/getTransNo',
				type: 'post',
				data:{vou_name_s:vou_name_s},
				dataType:'json',
				success:function(res){                    
                    $("#trans_prefix").val(res.data.trans_prefix);
                    $("#trans_no").val(res.data.trans_no);
                    $("#trans_number").val(res.data.trans_number);
				}
			}); 
        }else{
            $(".vou_name_s").html("Entry Type is required.");
        }
    });

    /* $(document).on('change',"#opp_acc_id",function(){
        $("#ref_id").html(''); //$("#ref_id").select2();
        $(".vou_name_s").html("");
        $(".opp_acc_id").html("");
        var vou_name_s = $("#vou_name_s").val();
        var party_id = $(this).val();
        var ref_id = $("#ref_id").data('selected');
        if(vou_name_s != '' && party_id != ''){
		    $.ajax({
				url : base_url + controller + '/getReference',
				type: 'post',
				data:{vou_name_s:vou_name_s,party_id:party_id,ref_id:ref_id},
				dataType:'json',
				success:function(response){                    
                    $("#ref_id").html(response.referenceData);
                    //$("#ref_id").select2();
				}
			}); 
        }else{
            if(vou_name_s == ""){
                $(".vou_name_s").html("Entry Type is required.");
            }
            if(party_id == ""){
                $(".opp_acc_id").html("Party Name is required.");
            }
        }   
        initSelect2();     
    }); */
    
});

function resOppAcc(response=""){
    if(response != ""){
        var partyDetail = response.data.partyDetail;
        $("#party_name").val(partyDetail.party_name);
        $("#opp_acc_balance").html(inrFormat(partyDetail.closing_balance)+' '+partyDetail.closing_type);
    }else{
        $("#party_name").val("");
		$("#opp_acc_balance").html(0);        
    }
    $("#opp_acc_balance").focus();
}

function resVouAcc(response=""){
    if(response != ""){
        var partyDetail = response.data.partyDetail;
        $("#vou_acc_balance").html(inrFormat(partyDetail.closing_balance)+' '+partyDetail.closing_type);

        if(partyDetail.group_code == "CS"){
            $('#payment_mode option[value="CASH"]').prop('disabled', false);
            $('#payment_mode option:not([value="CASH"])').prop('disabled', true);
            $('#payment_mode').val('CASH');
            $('#payment_mode').select2();
        }else{            
            $('#payment_mode option[value="CASH"]').prop('disabled', true);
            $('#payment_mode option:not([value="CASH"])').prop('disabled', false);

            var paymentMode = $("#payment_mode").data('selected');
            $('#payment_mode').val(paymentMode);
            $('#payment_mode').select2();
        }
    }else{
		$("#vou_acc_balance").html(0);   
        $('#payment_mode option').prop('disabled', false);
        $('#payment_mode').val('');
        $('#payment_mode').select2();     
    }
    initSelect2();
}

function resPaymentVoucher(response,formId){
    if(response.status==1){
        initTable(); 

        $("#payment_id").val("");
        $("#opp_acc_id").val("");
        $("#party_name").val("");
		$("#opp_acc_balance").html(0);  
        $("#net_amount").val("");
        $("#round_off_amount").val("");
        $("#doc_no").val("");
        $("#remark").val("");

        setTimeout(function(){
            initSelect2();
        },5);

        Swal.fire({ icon: 'success', title: response.message});
        //$(".modal-select2").select2();
        // toastr.success(response.message, 'Success', { "showMethod": "slideDown", "hideMethod": "slideUp", "closeButton": true, positionClass: 'toastr toast-bottom-center', containerId: 'toast-bottom-center', "progressBar": true });
    }else{
        if(typeof response.message === "object"){
            $(".error").html("");
            $.each( response.message, function( key, value ) {$("."+key).html(value);});
        }else{
            initTable();
            Swal.fire({ icon: 'error', title: response.message });
            // toastr.error(response.message, 'Error', { "showMethod": "slideDown", "hideMethod": "slideUp", "closeButton": true, positionClass: 'toastr toast-bottom-center', containerId: 'toast-bottom-center', "progressBar": true });
        }			
    }	
}
</script>
