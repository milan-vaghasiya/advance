<form enctype="multipart/form-data" data-res_function="getTestReportHtml">
    <div class="col-md-12">
        <table class="table jpExcelTable">
            <tr class="bg-light">
                <th>GRN No</th>
				<th>Party</th>
                <th>Item</th>
				<th>Finish Goods</th>
                <th>Grade</th>
                <th>Qty.</th>
            </tr>
            <tr>
                <td><?=$giData->trans_number?></td>
				<td><?=$giData->party_name?></td>
                <td><?=(!empty($giData->item_code) ? "[".$giData->item_code."] " : "").$giData->item_name?></td>
				<td><?=(!empty($giData->fg_item_code) ? "[".$giData->fg_item_code."] " : "").$giData->fg_item_name?></td>
                <td><?=$giData->material_grade?></td>
                <td><?=floatval($giData->qty)?></td>
            </tr>
        </table>
    </div>
    <div class="col-md-12">
        <div class="row">
            <input type="hidden" name="id" id="id" value=""/>
            <input type="hidden" name="grn_id" id="grn_id" value="<?= (!empty($giData->grn_id)) ? $giData->grn_id : ""; ?>"/>
            <input type="hidden" name="grn_trans_id" id="grn_trans_id" value="<?= (!empty($giData->id)) ? $giData->id : ""; ?>"/>
            <input type="hidden" name="heat_verify" id="heat_verify" value="<?= (!empty($giData->heat_verify)) ? $giData->heat_verify : ""; ?>"/>
            <input type="hidden" id="item_id" value="<?=(!empty($giData->item_id))?$giData->item_id:""; ?>"/>
            <input type="hidden" id="grn_qty" value="<?=(!empty($giData->qty))?$giData->qty:""; ?>"/>
            <input type="hidden" id="party_id" value="<?=(!empty($giData->party_id))?$giData->party_id:""; ?>"/>
            <input type="hidden" id="mill_name" value="<?=(!empty($giData->mill_name))?$giData->mill_name:""; ?>"/>
            <input type="hidden" id="po_trans_id" value="<?=(!empty($giData->po_trans_id))?$giData->po_trans_id:""; ?>"/>

            <div class="col-md-3 form-group">
                <label for="name_of_agency">Name Of Agency</label>
                <select name="agency_id" id="agency_id" class="form-control select2 req">
                    <option value="">Select Agency</option>
                    <option value="0">Inhouse</option>
                    <?php
                    if(!empty($partyList)){
                        foreach($partyList as $row){
                            echo '<option value="'.$row->id.'" >'.$row->party_name.'</option>';
                        }
                    }
                    ?>
                </select>
                <input type="hidden" name="name_of_agency" id="name_of_agency" class="form-control req" value="" />
            </div>
            <div class="col-md-3 form-group">
                <label for="ins_type">Inspection Type</label>
                <select name="ins_type" id="ins_type" class="form-control select2">
                    <option value="">Select Inspection</option>
                    <option value="GRN" <?=(!empty($ins_type) && $ins_type == 'GRN')?'selected':''?>>GRN</option>
                    <option value="FIR" <?=(!empty($ins_type) && $ins_type == 'FIR')?'selected':''?>>FIR</option>
                </select>
            </div>
            <div class="col-md-3 form-group">
                <label for="test_type">Test Type</label>
                <select name="test_type" id="test_type" class="form-control req select2">
                </select>
            </div>
            <div class="col-md-3 form-group">
                <label for="sample_qty">Sample Qty/ Total Weight</label>
                <input type="text" name="sample_qty" id="sample_qty" class="form-control floatOnly req" value="" readOnly/>
            </div>
            <div class="col-md-3 form-group">
                <label for="heat_no">Ref./Heat No. </label>
                <?php
                if(empty($giData->heat_verify)){
                    ?>
                    <span class="float-right">
                        <a class="text-primary font-bold" id="getHeatNo" href="javascript:void(0)">Verify</a>
                    </span>
                    <?php
                }
                ?>
                <input type="text" name="heat_no" id="heat_no" class="form-control req" value="<?= ((!empty($dataRow->heat_no)) ? $dataRow->heat_no : (!empty($giData->heat_no) ? $giData->heat_no : '')) ?>" <?=(!empty($giData->heat_verify) ? 'readOnly' : '')?> />
            </div>
            <div class="col-md-3 form-group">
                <label for="batch_no">Batch No. </label>
                <input type="text" name="batch_no" id="batch_no" class="form-control req" value="<?= ((!empty($dataRow->batch_no)) ? $dataRow->batch_no : (!empty($giData->batch_no) ? $giData->batch_no : '')) ?>" <?=(!empty($giData->heat_verify) ? 'readOnly' : '')?> />
            </div>
			<div class="col-md-3 form-group">
				<label for="ht_batch">HT Batch </label>
                <input type="text" name="ht_batch" id="ht_batch" class="form-control" value="<?= ((!empty($dataRow->ht_batch)) ? $dataRow->ht_batch : ""); ?>" />
            </div>
            <hr>
            <input type="hidden" name="grade_id" id="grade_id" value="<?=(!empty($giData->grade_id))?$giData->grade_id:""; ?>" />
			<input type="hidden" name="fg_item_id" id="fg_item_id" value="<?=(!empty($giData->fg_item_id))?$giData->fg_item_id:""; ?>" />
			
            <div class="row" id="tcParameter">
			
            </div>
			
            <div class="col-md-3 form-group instrumentDiv">
                <label for="inst_id">Instrument</label>
                <select name="inst_id" id="inst_id" class="form-control select2">
                    <option value="">Select Instrument</option>
                    <?php
                    if(!empty($itemList)){
                        foreach($itemList as $row){
                            echo '<option value="'.$row->id.'">'.$row->item_name.'</option>';
                        }
                    }
                    ?>
                </select>
            </div> 
            <div class="col-md-3 form-group" >
                <label for="test_report_no">Test Report No</label>
                <input type="text" name="test_report_no" id="test_report_no" class="form-control" value="" />
            </div>
            <div class="col-md-3 form-group">
                <label for="inspector_name">Inspector Name</label>
                <input type="text" name="inspector_name" id="inspector_name" class="form-control" value="" />
            </div>
            <div class="col-md-3 form-group" >
                <label for="test_result">Test Result</label>
                <select name="test_result" id="test_result" class="form-control select2">
                    <option value="">Pending</option> 
                    <option value="Accept">Accept</option>
                    <option value="Reject">Reject</option>
                    <option value="Accept U.D.">Accept U.D.</option>
                </select>
            </div>
            <div class="col-md-3 form-group" >
                <label for="tc_file">T.C. File</label>
                <input type="file" name="tc_file[]" id="tc_file" class="form-control" multiple="multiple" />
            </div>
            <div class="col-md-12 form-group testRemark">
                <label for="test_remark">Test Remark</label>
                <div class="input-group">
                    <input type="text" name="test_remark" id="test_remark" class="form-control" value="" />
                   
                </div>
            </div>
			<div class="col-md-12 form-group">
                <label for="spc_instruction">Special Instruction</label>
                <div class="input-group">
                    <input type="text" name="spc_instruction" id="spc_instruction" class="form-control" value="" />
                    <div class="input-group-append">
                        <?php
                            $param = "{'formId':'testReport','fnsave':'saveTestReport','controller':'gateInward','res_function':'getTestReportHtml'}";
                        ?>
                        <button type="button" class="btn waves-effect waves-light btn-outline-success btn-save save-form float-right" onclick="customStore(<?=$param?>)" style="height:36px"><i class="fa fa-check"></i> Save</button>
                    </div>
                </div>
            </div>
        </div>
    </div>
</form>
<hr>
<div class="row">
    <h6>Report Details : </h6>
    <div class="col-md-12 form-group">
        <div class="table-responsive">
            <table id="testReport" class="table jpExcelTable">
                <thead class="thead-info">
                    <tr class="text-center">
                        <th style="min-width:10px;">#</th>
                        <th style="min-width:50px;">Name Of Agency</th>
                        <th style="min-width:30px;">Insp. Type</th>
                        <th style="min-width:50px;">Test Description</th>
                        <th style="min-width:50px;">Test Report No</th>
                        <th style="min-width:50px;">Inspector Name</th>
                        <th style="min-width:30px;">Sample Qty</th>
                        <th style="min-width:50px;">Batch No.</th>
                        <th style="min-width:50px;">Ref./Heat No.</th>
                        <th style="min-width:50px;">Test Result</th>
                        <th style="min-width:30px;">T.C. File</th>
                        <th style="min-width:50px;">Test Remark</th>
                        <th style="min-width:50px;">Special Instruction</th>
                        <th style="min-width:30px;">Status</th>
                        <th style="min-width:150px;">Action</th>
                    </tr>
                </thead>
                <tbody id="testReportBody">
                </tbody>
            </table>
        </div>
    </div>
</div>

<script>
var tbodyData = false;
$(document).ready(function(){
    setPlaceHolder();	
    $('.instrumentDiv').hide();

    $(document).on('change',"#agency_id",function(e){
        e.stopImmediatePropagation();e.preventDefault();
        var party_name = $("#agency_id :selected").text();
        $("#name_of_agency").val(party_name);

        var agency_id = $(this).val();
        if(agency_id == '0'){
            $('.instrumentDiv').show();
            $("#test_report_no").prop("readonly", true);    
            $('.testRemark').removeClass('col-md-12');        
            $('.testRemark').addClass('col-md-9');  
        }else{
            $('.instrumentDiv').hide();
            $("#test_report_no").prop("readonly", false);  
            $('.testRemark').removeClass('col-md-9');        
            $('.testRemark').addClass('col-md-12');  
        }
    });

    $(document).on('change',"#inst_id",function(e){
        e.stopImmediatePropagation();e.preventDefault();
        var inst_id = $("#inst_id").val();
        if(grade_id){
            $.ajax({
                url : base_url + controller + '/getTestReportNo',
                type : 'post',
                data : {inst_id:inst_id},
                dataType : 'json'
            }).done(function(response){
                $("#test_report_no").val(response.test_report_no);
            });
        }
		initSelect2();
    });

	$(document).on('change',"#ins_type",function(e){
        e.stopImmediatePropagation();e.preventDefault();
        var id = $("#id").val();
        var fg_item_id = $("#fg_item_id").val();
        var ins_type = $("#ins_type").val();
        var grn_trans_id = $("#grn_trans_id").val();
        if(fg_item_id){
            $.ajax({
                url : base_url + controller + '/getTestTypeList',
                type : 'post',
                data : {id:id, fg_item_id:fg_item_id, ins_type:ins_type, grn_trans_id:grn_trans_id},
                dataType : 'json'
            }).done(function(response){
                $("#test_type").html(response.options);
            });
        }
		initSelect2();
    });
	
    if(!tbodyData){
        var postData = {'postData':{'grn_id':$("#grn_id").val(),'grn_trans_id':$("#grn_trans_id").val()},'table_id':"testReport",'tbody_id':'testReportBody','tfoot_id':'','fnget':'testReportHtml'};
        getTransHtml(postData);
        tbodyData = true;
    }

    $(document).on('change keyup',".validateReading",function(e){
        e.stopImmediatePropagation();e.preventDefault();
        var sample_value = $(this).val();
        var requirement = $(this).data('requirement');
        var min = $(this).data('min');
        var max = $(this).data('max');
        $(this).removeClass('bg-danger');
        // $('#test_result').html('<option value="Ok">Ok</option><option value="Not Ok">Not Ok</option>');

        if(requirement == 1){
            if(parseFloat(sample_value) <  parseFloat(min)  || parseFloat(sample_value) > parseFloat(max)){
                $(this).addClass('bg-danger');
                // $('#test_result').html('<option value="Ok" disabled>Ok</option><option value="Not Ok" selected>Not Ok</option>');
            }else{
               
            }
        }else if(requirement == 2){
            if(parseFloat(sample_value) < parseFloat(min)){
                $(this).addClass('bg-danger');
                // $('#test_result').html('<option value="Ok" disabled>Ok</option><option value="Not Ok" selected>Not Ok</option>');
            }
        }else if(requirement == 3){
            if(parseFloat(sample_value) > parseFloat(max)){
                $(this).addClass('bg-danger');
                // $('#test_result').html('<option value="Ok" disabled>Ok</option><option value="Not Ok" selected>Not Ok</option>');
            }
        }
        
    });

    $(document).on('click','#getHeatNo',function(e){
        e.stopImmediatePropagation();e.preventDefault();
        var heat_no = $("#heat_no").val();
        var batch_no = $("#batch_no").val();
        var grn_trans_id = $("#grn_trans_id").val();
        var grn_id = $("#grn_id").val();
        var item_id = $("#item_id").val();
        var party_id = $("#party_id").val();
        var po_trans_id = $("#po_trans_id").val();
        var mill_name = $("#mill_name").val();
        var fg_item_id = $("#fg_item_id").val();

        $(".heat_no").html("");
        if(heat_no == ""){
            $(".heat_no").html("Please enter Ref./Heat No.");
        }else{
            $.ajax({
                url : base_url + controller + "/getHeatNo",
                type : 'POST',
                data : { batch_no:batch_no, heat_no:heat_no, grn_trans_id:grn_trans_id, grn_id:grn_id, item_id:item_id, party_id:party_id, po_trans_id:po_trans_id, mill_name:mill_name,fg_item_id:fg_item_id },
                dataType : 'json',
                success:function(response){
                    if(response.status != 1){
                        if(typeof response.message === "object"){
                            $(".error").html(""); 
                            $.each( response.message, function( key, value ) {$("."+key).html(value);});
                        }else{
                            Swal.fire({ icon: 'error', title: response.message });
                        }			
                    }else{
                        Swal.fire({ icon: 'success', title: response.message });

                        Swal.fire({
                            title: 'Confirm!',
                            text: "Are you sure want to update Ref./Heat No.?",
                            icon: 'warning',
                            showCancelButton: true,
                            confirmButtonColor: '#3085d6',
                            cancelButtonColor: '#d33',
                            confirmButtonText: 'Yes, Do it!',
                        }).then(function(result) {
                            if (result.isConfirmed){
                                $("#heat_no").val(response.heat_no);
                                $("#batch_no").val(response.batch_no);
                                $("#heat_verify").val(response.heat_verify);
                                if(response.heat_verify == 1){
                                    $("#heat_no").prop("readonly", true);
                                    $("#batch_no").prop("readonly", true);
                                    $('#getHeatNo').hide();  
                                }    
                                initTable();
                            }
                        });
                    }
                }
            }); 
        }
    });
});

function getTestReportHtml(data,formId="testReport"){ 
    if(data.status==1){
        $('#'+formId)[0].reset(); initSelect2();
        $("#tcParameter").html("");
		$('#agency_id option:disabled').prop('disabled', false); $("#agency_id").val(""); $("#agency_id").select2('destroy');$("#agency_id").select2();
		
        var postData = {'postData':{'grn_id':$("#grn_id").val(),'grn_trans_id':$("#grn_trans_id").val()},'table_id':"testReport",'tbody_id':'testReportBody','tfoot_id':'','fnget':'testReportHtml'};

        getTransHtml(postData);
    }else{
        if(typeof data.message === "object"){
            $(".error").html("");
            $.each( data.message, function( key, value ) {$("."+key).html(value);});
        }else{
            Swal.fire({ icon: 'error', title: data.message });
        }			
    }
}

function approvalStore(data){
	setPlaceHolder();

	var fnsave = data.fnsave || "save";
	var controllerName = data.controller || controller;

    var fd = data.postData;
    var resFunctionName = data.res_function || "";
    var msg = data.message || "Are you sure want to save this change ?";
    var ajaxParam = {
        url: base_url + controllerName + '/' + fnsave,
        data:fd,
        type: "POST",
        dataType:"json"
    };

	Swal.fire({
		title: 'Are you sure?',
		text: msg,
		icon: 'warning',
		showCancelButton: true,
		confirmButtonColor: '#3085d6',
		cancelButtonColor: '#d33',
		confirmButtonText: 'Yes, Do it!',
	}).then(function(result) {
		if (result.isConfirmed){
			$.ajax(ajaxParam).done(function(response){
				if(resFunctionName != ""){
					window[resFunctionName](response);
				}else{
					if(response.status==1){
						initTable();
						Swal.fire( 'Success', response.message, 'success' );
					}else{
						if(typeof response.message === "object"){
							$(".error").html("");
							$.each( response.message, function( key, value ) {$("."+key).html(value);});
						}else{
							initTable();
							Swal.fire( 'Sorry...!', response.message, 'error' );
						}			
					}	
				}			
			});
		}
	});
}
</script>