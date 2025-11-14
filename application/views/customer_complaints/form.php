<form>
    <div class="col-md-12">
        <div class="row">
            <input type="hidden" name="id" value="<?=(!empty($dataRow->id))?$dataRow->id:""; ?>" />
            <input type="hidden" name="trans_prefix" class="form-control" value="<?=(!empty($dataRow->trans_prefix)) ? $dataRow->trans_prefix : $trans_prefix?>" readonly />
            <input type="hidden" name="trans_no" class="form-control req" value="<?=(!empty($dataRow->trans_no)) ? $dataRow->trans_no : sprintf("%03d",$nextTransNo) ?>" readonly />
			<input type="hidden" name="complaint_text" id="complaint_text" class="form-control" value="" readonly />
            <div class="col-md-6 form-group">
                <label for="trans_number">Complaint No.</label>
                <input type="text" class="form-control" value="<?=(!empty($dataRow->trans_number)) ? $dataRow->trans_number : $trans_prefix.sprintf("%03d",$nextTransNo)?>" readonly>					
            </div>
			
			<div class="col-md-6 form-group">
                <label for="trans_date">Complaint Date</label>
                <input type="date" id="trans_date" name="trans_date" class=" form-control req" placeholder="dd-mm-yyyy" value="<?=(!empty($dataRow->trans_date))?$dataRow->trans_date:date('Y-m-d')?>" />	
			</div>
			
            <div class="col-md-6 form-group">
                <label for="party_id">Party Name</label>
                <select name="party_id" id="party_id" class="form-control select2 req">
                    <option value="">Select Party</option>
                    <?=getPartyListOption($partyList,((!empty($dataRow->party_id))?$dataRow->party_id:((!empty($party_id))?$party_id:0)))?>
                </select>									
            </div>

            <div class="col-md-6 form-group">
                <label for="item_id">Item Name</label>
                <select id="item_id" name="item_id" class="form-control itemDetails select2 req" data-res_function="resItemDetail">
                <?=getItemListOption($itemList,((!empty($dataRow->item_id))?$dataRow->item_id:((!empty($itmOptions))?$itmOptions:0)))?>
                </select>
                <input type="hidden" name="inv_trans_id" id="inv_trans_id" value="<?=(!empty($dataRow->inv_trans_id))?$dataRow->inv_trans_id:''?>" />	
            </div>
            
			<div class="col-md-4 form-group">
                <label for="inv_id">Ref. of Complaint</label>
                <select name="inv_id" id="inv_id" class="form-control select2 partyOptions req" >
                    <?= (!empty($soOptions) ? $soOptions : "")?> 
                </select>	
                <input type="hidden" name="inv_date" id="inv_date" value="<?=(!empty($dataRow->inv_date))?$dataRow->inv_date:date('Y-m-d')?>" /> 
            </div>

            <div class="col-md-4 form-group">
                <label for="product_returned">Product Returned</label>
                <select name="product_returned" id="product_returned" class="form-control select2">
                    <option value="">Select Option</option>
                    <option value="1" <?= (!empty($dataRow) && $dataRow->product_returned == 1) ? "selected" : "" ?><?=(!empty($dataRow) && $dataRow->product_returned != 1) ? 'disabled' : ''?>>No</option>
                    <option value="2" <?= (!empty($dataRow) && $dataRow->product_returned == 2) ? "selected" : "" ?> >Yes</option>
                </select>
            </div>
			
			<div class="col-md-4 form-group">
				<label for="defect_image">Defect photos</label>
				<input type="file" name="defect_image[]" id="defect_image" class="form-control" accept=".jpg,.jpeg,.png,.pdf" multiple/>
				<input type="hidden" name="old_defect_image" value="<?= (!empty($dataRow->defect_image) ? $dataRow->defect_image : "");?>">               
			</div>
			
			<div class="col-md-4 form-group ref_complaint_div">
                <label for="ref_complaint">Ref. Complaint</label>
                <input type="text" id="ref_complaint" name="ref_complaint" class=" form-control req" value="<?=(!empty($dataRow->ref_complaint))?$dataRow->ref_complaint:''?>" />
            </div>
			
			<div class="col-md-4 form-group ref_batch_no">
                <label for="ref_batch_no">Batch No</label>
                <input type="text" id="ref_batch_no" name="ref_batch_no" class=" form-control req" value="<?=(!empty($dataRow->batch_no))?$dataRow->batch_no:''?>" />
            </div>

            <div class="col-md-4 form-group batch_no">
                <label for="batch_no">Batch No.</label>  
                <select name="batch_no" id="batch_no" class="form-control select2 req">
                    <?= (!empty($batchNo) ? $batchNo : "")?> 
                </select>
            </div>

            <div class="col-md-4 form-group qty">
                <label for="qty">Qty.</label>
                <input type="text" id="qty" name="qty" class="form-control floatOnly" value="<?=(!empty($dataRow->qty))?$dataRow->qty:''?>" />
            </div>

            <div class="col-md-4 form-group">
                <label for="price">Price</label>
                <input type="text" id="price" name="price" class="form-control floatOnly" value="<?=(!empty($dataRow->price))?$dataRow->price:''?>" />
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
    $("#product_returned").trigger('change');
	
    $('.qty, .batch_no, .ref_batch_no, .ref_complaint_div, .price').hide();
	
        var product_returned = $('#product_returned').val();
        if(product_returned == 2){
            $('.qty, .batch_no, .price').show();
        }else{
            $('.qty, .batch_no, .price').hide();
        }

    $(document).on('change','#product_returned',function(e){
        e.stopImmediatePropagation();e.preventDefault();
		var product_returned = $(this).val();
		var inv_id = $("#inv_id").val();
		
		if(inv_id != ""){			
			if(product_returned == 2){
				$('.batch_no, .qty, .price').show();
			}else{
				$('.batch_no, .qty, .price').hide();
			}
		}else{
			if(product_returned == 2){
				$(".ref_batch_no, .qty, .price").show();
			}else{
				$(".ref_batch_no, .qty, .price").hide();
			}
		}
    });

	$(document).on('change', '#party_id', function (e) {
        e.stopImmediatePropagation();e.preventDefault();
        var party_id = $(this).val();

        $("#item_id").html('<option value="">Select Item</option>');  
        $("#batch_no").html('<option value="">Select Batch No</option>');    
        $("#inv_date").val('');                                        
        $("#inv_trans_id").val('');                                    
        $("#qty").val(''); 
        $("#price").val(''); 
		$('.ref_complaint_div').show();

        $.ajax({
            url: base_url + 'customerComplaints' + '/getPObyParty',
            data: { party_id: party_id },
            type: "POST",
            dataType: 'json',
        }).done(function(response){
            $("#inv_id").html(response.partyOptions);
            $("#inv_id").trigger('change');
        });
        initSelect2();
    });
	
	
    $("#inv_id").trigger('change');

    $(document).on('change', '#inv_id', function (e) {
        e.stopImmediatePropagation();e.preventDefault();
        var inv_id = $("#inv_id").val();
        var invDate = $("#inv_id :selected").data('trans_date'); 
		var product_returned = $("#product_returned").val();
		var complaint_text = $(this).find("option:selected").text();
		
		$("#complaint_text").val(complaint_text);
        $("#inv_date").val(invDate);
      
        $("#batch_no").html('<option value="">Select Batch No</option>');                                    
        $("#qty").val(''); 
        $("#price").val(''); 
		if(inv_id != ""){
			if(product_returned == 2){
				$('.batch_no, .qty, .price').show();
				$('.ref_batch_no, .ref_complaint_div').hide();
			}else{
				$('.qty, .batch_no, .ref_batch_no, .ref_complaint_div, .price').hide();
			}
		}else{
			if(product_returned == 2){
				$('.ref_batch_no, .qty, .price').show();
				$('.batch_no').hide();
			}else{
				$('.qty, .batch_no, .ref_batch_no, .price').hide();
			}
			$('.ref_complaint_div').show();
		}
        $.ajax({
            url: base_url + 'customerComplaints' + '/getItemList',
            data: { inv_id: inv_id},
            type: "POST",
            dataType: 'json',
        }).done(function(response){ 
            $("#item_id").html(response.itemOptions);
        });
        initSelect2();
    });

    $(document).on('change', '#item_id', function (e) {
        e.stopImmediatePropagation();e.preventDefault();
        var item_id = $("#item_id").val();               
        var inv_trans_id = $("#item_id").find(":selected").data('inv_trans_id') || 0;
        $("#inv_trans_id").val(inv_trans_id); 

        $.ajax({
            url: base_url + 'customerComplaints' + '/getbatchNoList',
            data: { item_id: item_id,inv_trans_id:inv_trans_id},
            type: "POST",
            dataType: 'json',
        }).done(function(response){ 
            $("#batch_no").html(response.batchOption);
            $("#price").val(response.price);
        });
        initSelect2();
    });

    $(document).on('change','#batch_no',function(e){
        e.stopImmediatePropagation();e.preventDefault();
		$("#qty").val(($("#batch_no :selected").data('qty') || 0));
		initSelect2();
	});

});

function resItemDetail(response = ""){
    if(response != ""){
        var itemDetail = response.data.itemDetail;
        if($("#inv_id").find(":selected").val() == ""){
            $("#inv_trans_id").val("");
        }else{
            $("#inv_trans_id").val(($("#item_id").find(":selected").data('inv_trans_id') || 0));
        }        
    }else{
        $("#inv_trans_id").val(""); 
    }

}
</script>