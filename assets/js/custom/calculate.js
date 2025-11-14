$(document).ready(function(){
	$(".ledgerColumn").hide();gstin();
	$(".summary_desc").attr('style','width: 30%;');
	$(".cgstCol").show();$(".sgstCol").show();$(".igstCol").hide();
	$(".amountCol").hide();$(".netAmtCol").show();

	setTimeout(function(){ $("#party_id").trigger('change'); },500);

	var partyId = $("#party_id").val();
	if(partyId != ""){
		$.ajax({
			url : base_url + '/parties/getPartyDetails',
			type:'post',
			data: {id:partyId},
			dataType : 'json',
		}).done(function(response){
			if(response != ""){
				var partyDetail = response.data.partyDetail;
				$("#itemForm #item_id").attr('data-price_structure_id',partyDetail.price_structure_id);
			}else{
				$("#itemForm #item_id").attr('data-price_stracture_id',"");
			}
		});
	}

	var gst_type = $("#gst_type").val() || 1;
	if (gst_type == 1) {
		$(".cgstCol").show(); $(".sgstCol").show(); $(".igstCol").hide();
		$(".amountCol").hide(); $(".netAmtCol").show();
	} else if (gst_type == 2) {
		$(".cgstCol").hide(); $(".sgstCol").hide(); $(".igstCol").show();
		$(".amountCol").hide(); $(".netAmtCol").show();
	} else {
		$(".cgstCol").hide(); $(".sgstCol").hide(); $(".igstCol").hide();
		$(".amountCol").show(); $(".netAmtCol").hide();
	}

	$(document).on('click','#tdsLedger,#tcsLedger',function(){
		if($(this).attr('id') == "tdsLedger"){
			if($("#tdsLedgerOptions").hasClass('ledgerColumn') == true){
				$("#tdsLedgerOptions").removeClass('ledgerColumn').show();
			}else{
				$("#tdsLedgerOptions").addClass('ledgerColumn').hide();
			}
		}

		if($(this).attr('id') == "tcsLedger"){
			if($("#tcsLedgerOptions").hasClass('ledgerColumn') == true){
				$("#tcsLedgerOptions").removeClass('ledgerColumn').show();
			}else{
				$("#tcsLedgerOptions").addClass('ledgerColumn').hide();
			}
		}
	});

	var numberOfChecked = $('.termCheck:checkbox:checked').length;
	$("#termsCounter").html(numberOfChecked);
	$(document).on("click", ".termCheck", function () {
		var id = $(this).data('rowid');
		var numberOfChecked = $('.termCheck:checkbox:checked').length;
		$("#termsCounter").html(numberOfChecked);
		if ($("#md_checkbox" + id).attr('check') == "checked") {
			$("#md_checkbox" + id).attr('check', '');
			$("#md_checkbox" + id).removeAttr('checked');
			$("#term_id" + id).attr('disabled', 'disabled');
			$("#term_title" + id).attr('disabled', 'disabled');
			$("#condition" + id).attr('disabled', 'disabled');
		} else {
			$("#md_checkbox" + id).attr('check', 'checked');
			$("#term_id" + id).removeAttr('disabled');
			$("#term_title" + id).removeAttr('disabled');
			$("#condition" + id).removeAttr('disabled');
		}
	});

	$(document).on('click',".show_terms",function(){$("#termModel").modal('show');});
    $(document).on('keyup', '.calculateSummary', function () { claculateColumn(); });
    $(document).on('change','#gstin', function(){ gstin(); });
	$(document).on('change',"#apply_round",function(){ claculateColumn(); });

	$(document).on('change blur',"#itemForm .calculatePrice",function(){
		var gst_per = $("#itemForm #gst_per").val() || 0;
		var disc_per = $("#itemForm #disc_per").val() || 0;
        var price = $("#itemForm #price").val() || 0;
        var mrp = $("#itemForm #org_price").val() || 0;

		if($(this).attr('id') == "price" && price > 0){
			var new_mrp = price;
			if(parseFloat(gst_per) > 0){
				var tax_amt = parseFloat( (parseFloat(price) * parseFloat(gst_per) ) / 100 ).toFixed(2);
				new_mrp = parseFloat( parseFloat(price) + parseFloat(tax_amt) ).toFixed(2);
			}
			$("#itemForm #org_price").val(new_mrp);
			return true;
		}

		if($.inArray($(this).attr('id'), ["org_price","gst_per","disc_per"]) >= 0  && mrp > 0){
			/* Use if enter discount per. */
			var disc_amount = 0;
			if(parseFloat(disc_per) > 0){
				disc_amount = parseFloat( (parseFloat(mrp) * parseFloat(disc_per) ) / 100 ).toFixed(2);
				mrp = parseFloat( parseFloat(mrp) - parseFloat(disc_amount) ).toFixed(2);
			}

			/* Use if enter discount amount */
			/* if(parseFloat(disc_amount) > 0){
				mrp = parseFloat( parseFloat(mrp) - parseFloat(disc_amount) ).toFixed(2);
			} */
			var new_price = mrp;

			if(parseFloat(gst_per) > 0){
				var gstReverse = parseFloat(( ( parseFloat(gst_per) + 100 ) / 100 )).toFixed(2);
				new_price = parseFloat( parseFloat(mrp) / parseFloat(gstReverse) ).toFixed(3);
				disc_amount = parseFloat( parseFloat(disc_amount) / parseFloat(gstReverse) ).toFixed(2);
				new_price = parseFloat( parseFloat(new_price) + parseFloat(disc_amount) ).toFixed(2);
			}
			$("#itemForm #price").val(new_price);
			return true;
		}
	});

	$(document).on('change','#tax_class_id',function(){
		var tax_class_id = $(this).val();
		var gst_type = $(this).find(":selected").data('gst_type');
		var sp_acc_id = $(this).find(":selected").data('sp_acc_id');
		var tax_class = $(this).find(":selected").data('tax_class');
		var paertStateCode = $("#party_state_code").val() || 24;
		var company_state_code = $("select[name='cm_id']").find(":selected").data('state_code') || 24;
		$("#tax_class").val(tax_class);
		$("#sp_acc_id").val(sp_acc_id);
		$("#gst_type").val(gst_type);		

		if($.inArray(tax_class, ["EXPORTGSTACC","EXPORTTFACC","IMPORTACC","IMPORTSACC"]) >= 0){
			$(".exportData").removeClass("hidden");
		}else{
			$(".exportData").addClass("hidden");
		}		

		$.ajax({ 
            type: "post",   
            url: base_url + controller + '/getAccountSummaryHtml',   
            data: {tax_class_id:tax_class_id,taxSummary:taxSummary},
			global:false,
			beforeSend: function() {
				var columnCount = $('#summaryTable thead tr').first().children().length;
				$("#summaryTable #taxSummaryHtml").html('<tr><td colspan="'+columnCount+'" class="text-center">Loading...</td></tr>');
			},
        }).done(function(response){
            $("#taxSummaryHtml").html("");
            $("#taxSummaryHtml").html(response);            

			if($(".trans_main_id").val() == ""){
				if($("#inv_type").val() == "PURCHASE"){
					var tds_applicable = $("#tds_applicable").val() || "NO";
					var defual_tds_per = $("#defual_tds_per").val() || 0;
					var defual_tds_acc_id =	$("#defual_tds_acc_id").val() || 0;

					if(tds_applicable != "NO"){	
						if(tds_applicable == "YES-FROM-START"){
							$("#taxSummaryHtml #tds_per").val(defual_tds_per);
							$("#taxSummaryHtml #tds_acc_id").val(defual_tds_acc_id);
							initSelect2();
						} 
						
						if(tds_applicable == "YES-FROM-LIMIT"){
							if(parseFloat(($("#turnover").val() || 0)) >= parseFloat(($("#tds_limit").val() || 0))){		
								$("#taxSummaryHtml #tds_per").val(defual_tds_per);
								$("#taxSummaryHtml #tds_acc_id").val(defual_tds_acc_id);
								initSelect2();
							}else{
								$("#taxSummaryHtml #tds_per").val(0);
							}
						}
					}
				}
				
				if($("#inv_type").val() == "SALES"){
					var tcs_applicable = $("#tcs_applicable").val() || "NO";
					var defual_tcs_per = $("#defual_tcs_per").val() || 0;
					
					if(tcs_applicable != "NO"){	
						if(parseFloat(($("#turnover").val() || 0)) >= parseFloat($("#tcs_limit").val() || 0)){
							$("#taxSummaryHtml #tcs_per").val(defual_tcs_per);
						}
					}					
				}
			}	
			$("#taxSummaryHtml .select2").select2();
            
            if (gst_type == 1) {
                $(".cgstCol").show(); $(".sgstCol").show(); $(".igstCol").hide();
                $(".amountCol").hide(); $(".netAmtCol").show();
            } else if (gst_type == 2) {
                $(".cgstCol").hide(); $(".sgstCol").hide(); $(".igstCol").show();
                $(".amountCol").hide(); $(".netAmtCol").show();
            } else {
                $(".cgstCol").hide(); $(".sgstCol").hide(); $(".igstCol").hide();
                $(".amountCol").show(); $(".netAmtCol").hide();
            }

			$(".ledgerColumn").hide();
			$(".summary_desc").attr('style','width: 30%;');
			claculateColumn();
        });

		$(".tax_class_id").html("");
		if(parseInt(paertStateCode) > 0 && $("#party_id").val() != ""){
			if(paertStateCode == company_state_code && gst_type == 2){
				$(".tax_class_id").html("Party State and Gst Type mismatch.");
			}
			if(paertStateCode != company_state_code && gst_type == 1){
				$(".tax_class_id").html("Party State and Gst Type mismatch.");
			}
		}

		claculateColumn();
		initSelect2();
	});

	$(document).on('keyup change','#itemForm .calculateBoxQty',function(){
        var row_id = $(this).data('srno');
        var box_qty = $(this).val() || 0;
        var stock_qty = $("#batch_stock_"+row_id).val();
        var opt_qty = $("#opt_qty_"+row_id).val();
        var batch_qty = 0;

        batch_qty = parseFloat((parseFloat(box_qty) * parseFloat(opt_qty))).toFixed(2);
        $("#batch_qty_"+row_id).val(batch_qty); console.log(box_qty +' '+opt_qty);

        $(".batch_qty_"+row_id).html('');
        if(parseFloat(batch_qty) > parseFloat(stock_qty)){
            $(".batch_qty_"+row_id).html('Invalid Qty.');
            $("#batch_qty_"+row_id).val(0);
            $(this).val("");
        }   
        
        var boxQtyArr = $("#itemForm .calculateBoxQty").map(function(){return $(this).val();}).get();
        var boxQtySum = 0;
        $.each(boxQtyArr,function(){boxQtySum += parseFloat(this) || 0;});
        $('#total_box').html(boxQtySum); 

        var batchQtyArr = $("#itemForm .calculateBatchQty").map(function(){return $(this).val();}).get();
        var batchQtySum = 0;
        $.each(batchQtyArr,function(){batchQtySum += parseFloat(this) || 0;});
        $('#itemForm #qty').val(batchQtySum); 
    });
	
	/* $(document).on('change',"#sp_acc_id",function(){
		var tax_class = $(this).find(":selected").data('tax_class');
		var gstin = $("#gstin").find(":selected").val() || "";	
		var paertStateCode = $("#party_state_code").val() || 24;
		var company_state_code = $("#cm_id").find(":selected").data('state_code') || 24;
		$("#tax_class").val(tax_class);

		var gst_type = 1;
		if(paertStateCode == company_state_code){
			gst_type = 1;
		}else{
			gst_type = 2;
		}
		$('#sp_acc_id').select2();

		if($.inArray(tax_class, ["SALESGSTACC","SALESJOBGSTACC","PURGSTACC","PURJOBGSTACC"]) >= 0){
			gst_type = 1;
		}else if($.inArray(tax_class, ["SALESIGSTACC","SALESJOBIGSTACC","EXPORTGSTACC","SEZSTFACC","SEZSGSTACC","DEEMEDEXP","PURIGSTACC","PURJOBIGSTACC","IMPORTACC","IMPORTSACC","SEZRACC"]) >= 0){
			gst_type = 2;
		}else if($.inArray(tax_class, ["SALESTFACC","SALESEXEMPTEDTFACC","EXPORTTFACC","PURTFACC","PURURDGSTACC","PURURDIGSTACC","PUREXEMPTEDTFACC"]) >= 0){
			gst_type = 3;
		}

		if($.inArray(tax_class, ["EXPORTGSTACC","EXPORTTFACC","IMPORTACC","IMPORTSACC"]) >= 0){
			$(".exportData").removeClass("hidden");
		}else{
			$(".exportData").addClass("hidden");
		}

		$("#gst_type").val(gst_type);

		if(gst_type == 1){ 
			$(".cgstCol").show();$(".sgstCol").show();$(".igstCol").hide();
			$(".amountCol").hide();$(".netAmtCol").show();
		}else if(gst_type == 2){
			$(".cgstCol").hide();$(".sgstCol").hide();$(".igstCol").show();
			$(".amountCol").hide();$(".netAmtCol").show();
		}else{
			$(".cgstCol").hide();$(".sgstCol").hide();$(".igstCol").hide();
			$(".amountCol").show();$(".netAmtCol").hide();
		}

		claculateColumn();
	}); */
});

function checkPartyTurnover(partyDetail){

	var postData = {party_id : partyDetail.id, vou_name_s : partyDetail.vou_name_s, trans_date : partyDetail.trans_date, id : partyDetail.trans_main_id};

	$.ajax({
		url : base_url + controller + '/getPartyNetInvoiceSum',
		type : 'post',
		data : postData,
		dataType : 'json'
	}).done(function(resData){
		$("#turnover").val(resData.netInvoiceSum);
		$("#Turnover").html(inrFormat(resData.netInvoiceSum));

		if($.inArray($("#vou_name_s").val(),["Purc","GExp"]) >= 0){		
			$("#tds_limit").val(resData.accountSetting.tds_limit);
			$("#taxSummaryHtml #tds_per").val(0);

			if(partyDetail.tds_applicable != "NO"){		
				$("#tds_applicable").val(partyDetail.tds_applicable);
				$("#defual_tds_per").val(partyDetail.tds_per);
				$("#defual_tds_acc_id").val(partyDetail.tds_acc_id);

				if(partyDetail.tds_applicable == "YES-FROM-START"){
					$("#taxSummaryHtml #tds_per").val(partyDetail.tds_per);
					$("#taxSummaryHtml #tds_acc_id").val(partyDetail.tds_acc_id);
					initSelect2();
				} 
				
				if(partyDetail.tds_applicable == "YES-FROM-LIMIT"){
					if(parseFloat(resData.netInvoiceSum) >= parseFloat(resData.accountSetting.tds_limit)){		
						$("#taxSummaryHtml #tds_per").val(partyDetail.tds_per);
						$("#taxSummaryHtml #tds_acc_id").val(partyDetail.tds_acc_id);
						initSelect2();
					}else{
						$("#taxSummaryHtml #tds_per").val(0);
					}
				}
			}
		}

		if($.inArray($("#vou_name_s").val(),["Sale","GInc"]) >= 0){	
			$("#taxSummaryHtml #tcs_per").val(0);
					
			if(partyDetail.tcs_applicable == "YES-SALES"){
				var tcs_per = (partyDetail.pan_no != "")?resData.accountSetting.tcs_with_pan_per:resData.accountSetting.tcs_without_pan_per;
				$("#defual_tcs_per").val(tcs_per);
				$("#tcs_limit").val(resData.accountSetting.tcs_limit);
				$("#tcs_applicable").val(partyDetail.tcs_applicable);

				if(parseFloat(resData.netInvoiceSum) >= parseFloat(resData.accountSetting.tcs_limit)){
					$("#taxSummaryHtml #tcs_per").val(tcs_per);
				}else{
					$("#taxSummaryHtml #tcs_per").val(0);
				}
			}
		}

		claculateColumn();
	});
}

function gstin(){
	var party_id = $("#party_id").find(":selected").val();
	var gst_type = $("#tax_class_id").find(":selected").data('gst_type') || "";
    var gstin = $("#gstin").find(":selected").val();	
    var paertStateCode = $("#party_state_code").val() || 24;
	var company_state_code = $("select[name='cm_id']").find(":selected").data('state_code') || 24;

	if(gst_type == ""){
		if(paertStateCode == company_state_code){
			gst_type = 1;
		}else{
			gst_type = 2;
		}
		$("#gst_type").val(gst_type);
	}else{
		$("#gst_type").val(gst_type);
	}
	
	if($(".trans_main_id").val() == ""){
		var inv_type = $("#inv_type").val(); 
		if(inv_type == "SALES"){
			$('#tax_class_id').find('option[data-tax_class="SALESGSTACC"]:first').prop('selected', true);
			$("#tax_class").val("SALESGSTACC");
		}else{
			$('#tax_class_id').find('option[data-tax_class="PURGSTACC"]:first').prop('selected', true);
			$("#tax_class").val("PURGSTACC");
		}
		
		if(party_id != ""){
			if(gstin != "" && gstin != "URP"){
				if(paertStateCode == company_state_code){
					if(inv_type == "SALES"){
						$('#tax_class_id').find('option[data-tax_class="SALESGSTACC"]:first').prop('selected', true);
						$("#tax_class").val("SALESGSTACC");
					}else{
						$('#tax_class_id').find('option[data-tax_class="PURGSTACC"]:first').prop('selected', true);
						$("#tax_class").val("PURGSTACC");
					}
				}else{
					if(inv_type == "SALES"){
						$('#tax_class_id').find('option[data-tax_class="SALESIGSTACC"]:first').prop('selected', true);
						$("#tax_class").val("SALESIGSTACC");
					}else{
						$('#tax_class_id').find('option[data-tax_class="PURIGSTACC"]:first').prop('selected', true);
						$("#tax_class").val("PURIGSTACC");
					}			
				}
			}else{
				if(inv_type == "PURCHASE"){
					$('#tax_class_id').find('option[data-tax_class="PURTFACC"]:first').prop('selected', true);
					$("#tax_class").val("PURTFACC");
				}else if(inv_type == "SALES"){
					if(paertStateCode == company_state_code){
						$('#tax_class_id').find('option[data-tax_class="SALESGSTACC"]:first').prop('selected', true);
						$("#tax_class").val("SALESGSTACC");
					}else{
						$('#tax_class_id').find('option[data-tax_class="SALESIGSTACC"]:first').prop('selected', true);
						$("#tax_class").val("SALESIGSTACC");
					}			
				}
			}
		}
	}

	setTimeout(function(){$("#tax_class_id").trigger('change');},10);

	if($.inArray(inv_type, ["PURCHASE","SALES"]) >= 0){
		var tax_class = $(this).find(":selected").data('tax_class');
		if($.inArray(tax_class, ["EXPORTGSTACC","EXPORTTFACC","IMPORTACC","IMPORTSACC"]) >= 0){
			$(".exportData").removeClass("hidden");
		}else{
			$(".exportData").addClass("hidden");
		}
	}

    if(gst_type == 1){ 
		$(".cgstCol").show();$(".sgstCol").show();$(".igstCol").hide();
		$(".amountCol").hide();$(".netAmtCol").show();
	}else if(gst_type == 2){
		$(".cgstCol").hide();$(".sgstCol").hide();$(".igstCol").show();
		$(".amountCol").hide();$(".netAmtCol").show();
	}else{
		$(".cgstCol").hide();$(".sgstCol").hide();$(".igstCol").hide();
		$(".amountCol").show();$(".netAmtCol").hide();
	}

	initSelect2();
    claculateColumn();
}

function calculatePrice(postData,returnType = "price"){
	if(returnType == "price" && parseFloat(postData.org_price) > 0){		
		var disc_amount = 0;
		if(parseFloat(postData.disc_per) > 0){
			/* Use if enter discount per. */
			disc_amount = parseFloat( (parseFloat(postData.org_price) * parseFloat(postData.disc_per) ) / 100 ).toFixed(2);
			postData.org_price = parseFloat( parseFloat(postData.org_price) - parseFloat(disc_amount) ).toFixed(2);
		}else if(parseFloat(postData.disc_amount) > 0){
			/* Use if enter discount amount */
			postData.org_price = parseFloat( parseFloat(postData.org_price) - parseFloat(postData.disc_amount) ).toFixed(2);
		}

		var new_price = postData.org_price;

		if(parseFloat(postData.gst_per) > 0){
			var gstReverse = parseFloat(( ( parseFloat(postData.gst_per) + 100 ) / 100 )).toFixed(2);
			new_price = parseFloat( parseFloat(postData.org_price) / parseFloat(gstReverse) ).toFixed(2);
			disc_amount = parseFloat( parseFloat(disc_amount) / parseFloat(gstReverse) ).toFixed(2);
			new_price = parseFloat( parseFloat(new_price) + parseFloat(disc_amount) ).toFixed(2);
		}
		return new_price;
	}

	if(returnType == "mrp" && parseFloat(postData.price) > 0){
		var new_price = postData.price;

		if(parseFloat(postData.gst_per) > 0){
			new_price = parseFloat( (((parseFloat(postData.price) * parseFloat(postData.gst_per)) / 100 )) + parseFloat(postData.price) ).toFixed(2);
		}
		return new_price;
	}

	return 0;
}

function calculateItem(formData){
	formData.disc_per = (parseFloat(formData.disc_per) > 0)?formData.disc_per:0;
	var qty = formData.qty;
	var disc_amt = 0;
	var amount = 0; var taxable_amount = 0; var igst_amt = 0;
	var cgst_amt = 0; var sgst_amt = 0; var net_amount = 0; 
	var cgst_per = 0; var sgst_per = 0; var igst_per = 0;

	
	if (formData.disc_per == "" && formData.disc_per == "0") {
		taxable_amount = amount = parseFloat(parseFloat(qty) * parseFloat(formData.price)).toFixed(2);
	} else {
		amount = parseFloat(parseFloat(qty) * parseFloat(formData.price)).toFixed(2);
		disc_amt = parseFloat((amount * parseFloat(formData.disc_per)) / 100).toFixed(2);
		taxable_amount = parseFloat(amount - disc_amt).toFixed(2);
	}

	formData.gst_per = igst_per = parseFloat(formData.gst_per);
	formData.gst_amount = igst_amt = parseFloat((igst_per * taxable_amount) / 100).toFixed(2);

	cgst_per = parseFloat(parseFloat(igst_per) / 2).toFixed(2);
	sgst_per = parseFloat(parseFloat(igst_per) / 2).toFixed(2);

	cgst_amt = parseFloat((cgst_per * taxable_amount) / 100).toFixed(2);
	sgst_amt = parseFloat((sgst_per * taxable_amount) / 100).toFixed(2);

	net_amount = parseFloat(parseFloat(taxable_amount) + parseFloat(igst_amt)).toFixed(2);

	formData.qty = parseFloat(formData.qty).toFixed(2);
	formData.cgst_per = cgst_per;
	formData.cgst_amount = cgst_amt;
	formData.sgst_per = sgst_per;
	formData.sgst_amount = sgst_amt;
	formData.igst_per = igst_per;
	formData.igst_amount = igst_amt;
	formData.disc_amount = disc_amt;
	formData.amount = amount;
	formData.taxable_amount = taxable_amount;
	formData.net_amount = net_amount;

	return formData;
}

function claculateColumn() {
	var gst_type = $("#gst_type").val();
	if (gst_type == 1) {
		$(".cgstCol").show(); $(".sgstCol").show(); $(".igstCol").hide();
		$(".amountCol").hide(); $(".netAmtCol").show();
	} else if (gst_type == 2) {
		$(".cgstCol").hide(); $(".sgstCol").hide(); $(".igstCol").show();
		$(".amountCol").hide(); $(".netAmtCol").show();
	} else {
		$(".cgstCol").hide(); $(".sgstCol").hide(); $(".igstCol").hide();
		$(".amountCol").show(); $(".netAmtCol").hide();
	}
	
	var amountArray = $(".amount").map(function () { return $(this).val(); }).get();
	var amountSum = 0;
	$.each(amountArray, function () { amountSum += parseFloat(this) || 0; });
	$("#total_amount").html(amountSum.toFixed(2));

	var taxableAmountArray = $(".taxable_amount").map(function () { return $(this).val(); }).get();
	var taxableAmountSum = 0;
	$.each(taxableAmountArray, function () { taxableAmountSum += parseFloat(this) || 0; });
	$("#taxable_amount").val(taxableAmountSum.toFixed(2));

	calculateSummary();
}

function calculateSummary() {
	$(".calculateSummary").each(function () {
		var row = $(this).data('row');

		var map_code = row.map_code;
		var amtField = $("#" + map_code + "_amt");
		var netAmountField = $("#" + map_code + "_amount");
		var perField = $("#" + map_code + "_per");
		var sm_type = amtField.data('sm_type');

		if (sm_type == "exp") {
			if (row.position == "1") {
				var itemGstArray = $(".gst_per").map(function () { return $(this).val(); }).get();
				var maxGstPer = Math.max.apply(Math, itemGstArray);
				maxGstPer = (maxGstPer != "" && !isNaN(maxGstPer)) ? maxGstPer : 0;

				if (row.calc_type == "1") {
					var amount = (amtField.val() != "") ? amtField.val() : 0;
					amount = parseFloat(parseFloat(amount) * parseFloat(row.add_or_deduct)).toFixed(2);
					netAmountField.val(amount);
					var gstAmount = parseFloat((parseFloat(maxGstPer) * parseFloat(amount)) / 100).toFixed(2);
				} else {
					var taxable_amount = ($("#taxable_amount").val() != "") ? $("#taxable_amount").val() : 0;
					var per = (perField.val() != "") ? perField.val() : 0;

					var amount = parseFloat((parseFloat(taxable_amount) * parseFloat(per)) / 100).toFixed(2);
					amtField.val(amount);
					amount = parseFloat(parseFloat(amount) * parseFloat(row.add_or_deduct)).toFixed(2);
					netAmountField.val(amount);
					var gstAmount = parseFloat((parseFloat(maxGstPer) * parseFloat(amount)) / 100).toFixed(2);
				}

				$("#other_" + map_code + "_amount").val(gstAmount);

			} else {
				if (row.calc_type == "1") {
					var amount = (amtField.val() != "") ? amtField.val() : 0;
					amount = parseFloat(parseFloat(amount) * parseFloat(row.add_or_deduct)).toFixed(2);
					netAmountField.val(amount);
				} else {
					var taxable_amount = ($("#taxable_amount").val() != "") ? $("#taxable_amount").val() : 0;
					var per = (perField.val() != "") ? perField.val() : 0;
					var amount = parseFloat((parseFloat(taxable_amount) * parseFloat(per)) / 100).toFixed(2);
					amtField.val(amount);
					amount = parseFloat(parseFloat(amount) * parseFloat(row.add_or_deduct)).toFixed(2);
					netAmountField.val(amount);
				}
			}
		}

		if (sm_type == "tax") {
			if(row.calculation_type == 2){
				var oldAmt = amtField.val();
				oldAmt = (parseFloat(oldAmt) > 0)?oldAmt:0;	
				var per = (perField.val() != "")?perField.val():0;
				calculateSummaryAmount();		

				var summaryAmtArray = $(".summaryAmount").map(function(){return $(this).val();}).get();
				var summaryAmtSum = 0;
				$.each(summaryAmtArray,function(){summaryAmtSum += parseFloat(this) || 0;});
				
				if(parseFloat(summaryAmtSum) > 0){
					summaryAmtSum = parseFloat(parseFloat(summaryAmtSum) - parseFloat(oldAmt)).toFixed(2);
				}else{
					amtField.val(0);
				}

				if(map_code == "tcs"){
					var tcs_applicable = $("#tcs_applicable").val() || "";	
					if(tcs_applicable == "YES-SALES"){
						var tcs_per = $("#defual_tcs_per").val() || $("#taxSummaryHtml #tcs_per").val();
						var turnover = parseFloat(($("#turnover").val() || 0)).toFixed(2);
						var tcs_limit = $("#tcs_limit").val() || 0;

						if(parseFloat(turnover) < parseFloat(tcs_limit)){
							var newTurnover = parseFloat(parseFloat(turnover) + parseFloat(summaryAmtSum)).toFixed(2);
							if(parseFloat(newTurnover) >= parseFloat(tcs_limit)){
								summaryAmtSum = parseFloat(parseFloat(newTurnover) - parseFloat(tcs_limit)).toFixed(2);
								per = tcs_per;

								$("#taxSummaryHtml #tcs_per").val(tcs_per);
							}
						}
					}
				}			
								
				var amount = parseFloat((parseFloat(summaryAmtSum) * parseFloat(per)) / 100).toFixed(2);				
				amtField.val(amount);
				amount = parseFloat(parseFloat(amount) * parseFloat(row.add_or_deduct)).toFixed(2);
				netAmountField.val(amount);
			}else if (row.calculation_type == 1) {
				var taxable_amount = ($("#taxable_amount").val() != "") ? $("#taxable_amount").val() : 0;
				var per = (perField.val() != "") ? perField.val() : 0;

				if(map_code == "tds"){
					var tds_applicable = $("#tds_applicable").val() || "";	
					if(tds_applicable == "YES-FROM-LIMIT"){
						var tds_per = $("#defual_tds_per").val() || $("#taxSummaryHtml #tds_per").val();
						var tds_acc_id = $("#defual_tds_acc_id").val() || $("#taxSummaryHtml #tds_acc_id").val();
						var turnover = parseFloat(($("#turnover").val() || 0)).toFixed(2);
						var tds_limit = $("#tds_limit").val() || 0;

						if(parseFloat(turnover) < parseFloat(tds_limit)){
							var newTurnover = parseFloat(parseFloat(turnover) + parseFloat(taxable_amount)).toFixed(2);
							if(parseFloat(newTurnover) >= parseFloat(tds_limit)){
								taxable_amount = parseFloat(parseFloat(newTurnover) - parseFloat(tds_limit)).toFixed(2);
								per = tds_per;

								$("#taxSummaryHtml #tds_per").val(tds_per);
								$("#taxSummaryHtml #tds_acc_id").val(tds_acc_id);
								initSelect2();
							}
						}
					}
				}

				var amount = parseFloat((parseFloat(taxable_amount) * parseFloat(per)) / 100).toFixed(2);
				amtField.val(amount);
				amount = parseFloat(parseFloat(amount) * parseFloat(row.add_or_deduct)).toFixed(2);
				netAmountField.val(amount);
			} else {
				var qtyArray = $(".item_qty").map(function () { return $(this).val(); }).get();
				var qtySum = 0;
				$.each(qtyArray, function () { qtySum += parseFloat(this) || 0; });

				var per = (perField.val() != "") ? perField.val() : 0;
				var amount = parseFloat(parseFloat(qtySum) * parseFloat(per)).toFixed(2);
				amtField.val(amount);
				amount = parseFloat(parseFloat(amount) * parseFloat(row.add_or_deduct)).toFixed(2);
				netAmountField.val(amount);
			}
		}


	});

	calculateSummaryAmount();
}

function calculateSummaryAmount() {
	var gst_type = $("#gst_type").val();

	$('#cgst_amount').val("0");
	$('#sgst_amount').val("0");
	if (gst_type == 1) {
		var cgstAmtArr = $(".cgst_amount").map(function () { return $(this).val(); }).get();
		var cgstAmtSum = 0;
		$.each(cgstAmtArr, function () { cgstAmtSum += parseFloat(this) || 0; });
		$('#cgst_amount').val(parseFloat(cgstAmtSum).toFixed(2));

		var sgstAmtArr = $(".sgst_amount").map(function () { return $(this).val(); }).get();
		var sgstAmtSum = 0;
		$.each(sgstAmtArr, function () { sgstAmtSum += parseFloat(this) || 0; });
		$('#sgst_amount').val(parseFloat(sgstAmtSum).toFixed(2));
	}

	$('#igst_amount').val("0");
	if (gst_type == 2) {
		var igstAmtArr = $(".igst_amount").map(function () { return $(this).val(); }).get();
		var igstAmtSum = 0;
		$.each(igstAmtArr, function () { igstAmtSum += parseFloat(this) || 0; });
		$('#igst_amount').val(parseFloat(igstAmtSum).toFixed(2));
	}

	var otherGstAmtArray = $(".otherGstAmount").map(function () { return $(this).val(); }).get();
	var otherGstAmtSum = 0;
	$.each(otherGstAmtArray, function () { otherGstAmtSum += parseFloat(this) || 0; });

	var cgstAmt = 0;
	var sgstAmt = 0;
	var igstAmt = 0;
	if (gst_type == 1) {
		cgstAmt = parseFloat(parseFloat(otherGstAmtSum) / 2).toFixed(2);
		sgstAmt = parseFloat(parseFloat(otherGstAmtSum) / 2).toFixed(2);
		$("#cgst_amount").val(parseFloat(parseFloat($("#cgst_amount").val()) + parseFloat(cgstAmt)).toFixed(2));
		$("#sgst_amount").val(parseFloat(parseFloat($("#sgst_amount").val()) + parseFloat((sgstAmt))).toFixed(2));
	} else if (gst_type == 2) {
		igstAmt = otherGstAmtSum;
		$("#igst_amount").val(parseFloat(parseFloat($("#igst_amount").val()) + parseFloat((igstAmt))).toFixed(2));
	}

	var summaryAmtArray = $(".summaryAmount").map(function () { return $(this).val(); }).get();
	var summaryAmtSum = 0;
	$.each(summaryAmtArray, function () { summaryAmtSum += parseFloat(this) || 0; });

	if ($("#roff_amount").length > 0) {
		var totalAmount = parseFloat(summaryAmtSum).toFixed(2);
		var decimal = totalAmount.split('.')[1];
		var roundOff = 0;
		var netAmount = 0;
		if (decimal !== 0) {
			if (decimal >= 50) {
				if ($('#apply_round').val() == "1") { roundOff = (100 - decimal) / 100; }
				netAmount = parseFloat(parseFloat(totalAmount) + parseFloat(roundOff)).toFixed(2);
			} else {
				if ($('#apply_round').val() == "1") { roundOff = (decimal - (decimal * 2)) / 100; }
				netAmount = parseFloat(parseFloat(totalAmount) + parseFloat(roundOff)).toFixed(2);
			}
			$("#roff_amount").val(parseFloat(roundOff).toFixed(2));
		}
		$("#net_amount").val(netAmount);
	} else {
		$("#net_amount").val(summaryAmtSum.toFixed(2));
	}
}


