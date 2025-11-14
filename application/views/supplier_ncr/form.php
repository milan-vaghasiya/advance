<form>
    <div class="col-md-12">
        <div class="row">
            <input type="hidden" name="id" value="<?=(!empty($dataRow->id))?$dataRow->id:""; ?>" />
            <input type="hidden" name="trans_prefix" class="form-control" value="<?=(!empty($dataRow->trans_prefix)) ? $dataRow->trans_prefix : $trans_prefix?>" readonly />
            <input type="hidden" name="trans_no" class="form-control req" value="<?=(!empty($dataRow->trans_no)) ? $dataRow->trans_no : $trans_no ?>" readonly />

            <div class="col-md-3 form-group">
                <label for="trans_number">NCR No.</label>
                <input type="text" class="form-control" value="<?=(!empty($dataRow->trans_number)) ? $dataRow->trans_number : $trans_numer?>" readonly>					
            </div>

            <div class="col-md-3 form-group">
                <label for="trans_date">NCR Date</label>
                <input type="date" id="trans_date" name="trans_date" class=" form-control req" placeholder="dd-mm-yyyy" value="<?=(!empty($dataRow->trans_date))?$dataRow->trans_date:date('Y-m-d')?>" />	
			</div>

            <div class="col-md-6 form-group">
                <label for="party_id">Party Name</label>
                <select name="party_id" id="party_id" class="form-control select2 req">
                    <option value="">Select Party</option>
                    <?=getPartyListOption($partyList,((!empty($dataRow->party_id))?$dataRow->party_id:((!empty($party_id))?$party_id:0)))?>
                </select>									
            </div>

            <div class="col-md-3 form-group">
                <label for="ncr_type">NCR Type</label>
                <select name="ncr_type" id="ncr_type" class="form-control select2">
                    <option value="">Select Type</option>
                    <option value="1" <?= (!empty($dataRow) && $dataRow->ncr_type == 1) ? "selected" : "" ?>>Purchase</option>
                    <option value="2" <?= (!empty($dataRow) && $dataRow->ncr_type == 2) ? "selected" : "" ?>>Outsource</option>
                </select>
            </div>

            <div class="col-md-6 form-group">
                <label for="item_id">Item Name</label>
                <select id="item_id" name="item_id" class="form-control select2 req">
                    <?=getItemListOption($itemList)?>
                    <?=(!empty($itmOptions) ? $itmOptions : "")?>
                </select>
                <input type="hidden" name="grn_trans_id" id="grn_trans_id" value="" />	
            </div> 

            <div class="col-md-3 form-group">
                <label for="batch_no">Batch No.</label>
                <input type="text" id="batch_no" name="batch_no" class=" form-control" value="<?=(!empty($dataRow->batch_no))?$dataRow->batch_no:''?>" />	
			</div>

            <div class="col-md-3 form-group">
                <label for="qty">Lot Qty.</label>
                <input type="text" id="qty" name="qty" class=" form-control" floatOnly value="<?=(!empty($dataRow->qty))?$dataRow->qty:''?>" />	
			</div>

            <div class="col-md-3 form-group">
                <label for="rej_qty">Rej Qty.</label>
                <input type="text" id="rej_qty" name="rej_qty" class=" form-control" floatOnly value="<?=(!empty($dataRow->rej_qty))?$dataRow->rej_qty:''?>" />	
			</div>

            <div class="col-md-6 form-group">
                <label for="ref_of_complaint">Ref. of Complaint</label>
                <input type="text" name="ref_of_complaint" id="ref_of_complaint" class="form-control" value="<?=(!empty($dataRow->ref_of_complaint))?$dataRow->ref_of_complaint:''?>"/>	
            </div>

            <div class="col-md-12 form-group">
                <label for="complaint">Details of Complaint</label>
                <textarea name="complaint" id="complaint" class="form-control req"><?=(!empty($dataRow->complaint))?$dataRow->complaint:""?></textarea>
            </div>
        </div>
    </div>
</form>

<script>
$(document).ready(function(){
	$(document).on('change', '#ncr_type', function (e) {
        e.stopImmediatePropagation();e.preventDefault();
        var ncr_type = $('#ncr_type').val();
        var party_id = $('#party_id').val();
        $.ajax({
            url: base_url + 'supplierNcr' + '/getItemList',
            data: { party_id: party_id,ncr_type:ncr_type },
            type: "POST",
            dataType: 'json',
        }).done(function(response){
            $("#item_id").html(response.itemOptions);
        });
        initSelect2();
    });

    $(document).on('change', '#party_id', function (e) {
        e.stopImmediatePropagation();e.preventDefault();
        $('#ncr_type').val('').trigger('change'); 
        $('#item_id').html(''); 
        $('#qty').val('');
        $('#batch_no').val('');
        $('#grn_trans_id').val('');
    });

    $(document).on('change','#item_id',function(e){
        e.stopImmediatePropagation();e.preventDefault();
		$("#qty").val(($("#item_id :selected").data('qty') || 0));
		$("#batch_no").val(($("#item_id :selected").data('batch_no')));
        $("#grn_trans_id").val(($("#item_id :selected").data('grn_trans_id') || 0));
		initSelect2();
	});
    
});

</script> 
               