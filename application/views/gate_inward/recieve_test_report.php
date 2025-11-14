<form enctype="multipart/form-data" data-res_function="getTestReportHtml">
    <div class="col-md-12">
        <table class="table jpExcelTable">
            <tr class="bg-light text-center">
                <th>GI No</th>
                <th>Item</th>
                <th>Grade</th>
                <th>Inspection Type</th>                
                <th colspan="2">Test Type</th>
            </tr>
            <tr>
                <td><?=$testData->trans_number?></td>
                <td><?=(!empty($testData->item_code) ? "[".$testData->item_code."] " : "").$testData->item_name?></td>
                <td><?=$testData->material_grade?></td>
                <td><?=$testData->ins_type?></td>
                <td colspan="2"><?=$testData->test_description?></td>
            </tr>
            
            <tr>
                <th class="bg-light">Agency</th>
                <td ><?=$testData->name_of_agency?></td>
                <th class="bg-light">Batch No.</th>
                <td><?=$testData->batch_no?></td>
                <th class="bg-light">Ref./Heat No.</th>
                <td><?=$testData->heat_no?></td>
            </tr>
        </table>
    </div>
    <div class="col-md-12">
        <div class="row">
            <input type="hidden" name="id" id="id" value="<?= (!empty($testData->id)) ? $testData->id : ""; ?>"/>
            <input type="hidden" name="grn_id" id="grn_id" value="<?=(!empty($testData->grn_id))?$testData->grn_id:""; ?>"/>
            <input type="hidden" name="grn_trans_id" id="grn_trans_id" value="<?=(!empty($testData->grn_trans_id))?$testData->grn_trans_id:""; ?>"/>
            <input type="hidden" name="test_type" id="test_type" value="<?=(!empty($testData->test_type))?$testData->test_type:""; ?>">
            <input type="hidden" name="ins_type" id="ins_type" value="<?=(!empty($testData->ins_type))?$testData->ins_type:""; ?>">
            <input type="hidden" name="agency_id" id="agency_id" value="<?=(!empty($testData->agency_id))?$testData->agency_id:""; ?>">
            <input type="hidden" name="batch_no" id="batch_no" value="<?=(!empty($testData->batch_no))?$testData->batch_no:""; ?>">
            
            <input type="hidden" name="heat_no" id="heat_no" value="<?=(!empty($testData->heat_no))?$testData->heat_no:""; ?>">
            <input type="hidden" name="name_of_agency" id="name_of_agency" class="form-control req" value="<?=$testData->name_of_agency?>" />
            <div class="col-md-2 form-group">
                <label for="sample_qty">Sample Qty/Total Weight</label>
                <input type="text" name="sample_qty" class="form-control floatOnly req" value="<?=(!empty($testData->sample_qty))?$testData->sample_qty:""; ?>" readOnly/>
            </div>
            <div class="col-md-2 form-group" >
                <label for="test_report_no">Test Report No</label>
                <input type="text" name="test_report_no" id="test_report_no" class="form-control" value="" />
            </div>
            <div class="col-md-3 form-group">
                <label for="inspector_name">Inspector Name</label>
                <input type="text" name="inspector_name" id="inspector_name" class="form-control" value="" />
            </div>
            <div class="col-md-2 form-group" >
                <label for="test_result">Test Result</label>
                <select name="test_result" id="test_result" class="form-control select2 ">
                    <option value="Accept">Accept</option>
                    <option value="Reject">Reject</option>
                    <option value="Accept U.D.">Accept U.D.</option>
                </select>
            </div>
            <div class="col-md-3 form-group" >
                <label for="tc_file">T.C. File</label>
                <input type="file" name="tc_file" id="tc_file" class="form-control req"  />
            </div>
            <div class="col-md-12 form-group" >
                <label for="test_remark">Test Remark</label>
                <div class="input-group">
                    <input type="text" name="test_remark" id="test_remark" class="form-control" value="" />
                   
                </div>
            </div>
            <input type="hidden" name="grade_id"  id="grade_id" value="<?=(!empty($testData->grade_id))?$testData->grade_id:""; ?>" />
			<input type="hidden" name="fg_item_id"  id="fg_item_id" value="<?=(!empty($testData->fg_item_id))?$testData->fg_item_id:""; ?>" />
			
            <div class="row" id="tcParameter" >
            
			</div>
        </div>
    </div>
</form>
<hr>

<script>
var tbodyData = false;
$(document).ready(function(){
    setPlaceHolder();
	setTimeout(function(){ $("#test_type").trigger("change"); }, 1000);	

    $(document).on('change',"#agency_id",function(e){
        e.stopImmediatePropagation();e.preventDefault();
        var party_name = $("#agency_id :selected").text();
        $("#name_of_agency").val(party_name);
    });

	$(document).on('change',"#test_type",function(e){
        e.stopImmediatePropagation();e.preventDefault();
        var fg_item_id = $("#fg_item_id").val();
        var test_type = $("#test_type").val();
        if(fg_item_id){
            $.ajax({
                url : base_url + controller + '/getTestReportParam',
                type : 'post',
                data : {fg_item_id : fg_item_id,test_type:test_type},
                dataType : 'json'
            }).done(function(response){
                $("#tcParameter").html(response.html);
            });
        }
    });
   
    $(document).on('change',"#ins_type",function(e){
        e.stopImmediatePropagation();e.preventDefault();
        var fg_item_id = $("#fg_item_id").val();
        var ins_type = $("#ins_type").val();
        var id = $("#id").val();
        if(fg_item_id){
            $.ajax({
                url : base_url + controller + '/getTestTypeList',
                type : 'post',
                data : {fg_item_id : fg_item_id,ins_type:ins_type ,id:id},
                dataType : 'json'
            }).done(function(response){
                $("#test_type").html(response.options);
            });
        }
		initSelect2();
    });

    $(document).on('change keyup',".validateReading",function(e){
        e.stopImmediatePropagation();e.preventDefault();
        var sample_value = $(this).val();
        var requirement = $(this).data('requirement');
        var min = $(this).data('min');
        var max = $(this).data('max');
        $(this).removeClass('bg-danger');
        if(requirement == 1){
            if(parseFloat(sample_value) <  parseFloat(min)  || parseFloat(sample_value) > parseFloat(max)){
                $(this).addClass('bg-danger');
            }else{
               
            }
        }else if(requirement == 2){
            if(parseFloat(sample_value) < parseFloat(min)){
                $(this).addClass('bg-danger');
            }
        }else if(requirement == 3){
            if(parseFloat(sample_value) > parseFloat(min)){
                $(this).addClass('bg-danger');
            }
        }
        
    });
});
function getTestReportHtml(data,formId="testReport"){ 
    if(data.status==1){
        $('#'+formId)[0].reset(); initSelect2();
        $("#tcParameter").html("");
        var postData = {'postData':{'grn_id':$("#grn_id").val()},'table_id':"testReport",'tbody_id':'testReportBody','tfoot_id':'','fnget':'testReportHtml'};
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
</script>