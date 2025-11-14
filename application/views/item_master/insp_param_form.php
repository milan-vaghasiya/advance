<form id="inspection" data-res_function="inspectionHtml">
    <div class="row">
        <input type="hidden" name="id" id="id" class="id" value="" />
        <input type="hidden" name="item_id" id="item_id" class="item_id" value="<?=$item_id?>" />

        <div class="col-md-2 form-group">
            <label for="rev_no">Revision No.</label>
            <select name="rev_no" id="rev_no" class="form-control select2 req">
                <option value="">Select Revision No.</option>
                <?php
                    foreach($revisionList as $row):
                        echo '<option value="'.$row->rev_no.'">'.$row->rev_no.'</option>';
                    endforeach;
                ?>
            </select>
            <div class="error rev_no"></div>
        </div>
        <div class="col-md-2 form-group">
            <label for="process_id">Process</label>
            <select name="process_id" id="process_id" class="form-control select2 req">
                <option value="">Select Process</option>
                <?php if(!in_array(3,array_column($processList,'process_id'))){ ?>
                        <option value="3" >RM Cutting</option>
                <?php }
                    foreach($processList as $row):
                        echo '<option value="'.$row->process_id.'">'.$row->process_name.'</option>';
                    endforeach; 
				?>
            </select>
            <div class="error process_id"></div>
        </div>
        <div class="col-md-2 form-group">
            <label for="param_type">Parameter Type</label>
            <select name="param_type" id="param_type" class="form-control req">
                <option value="1">Product</option>
                <option value="2">Process</option>
               
            </select>
            <div class="error param_type"></div>
        </div>
        <div class="col-md-6 form-group">
            <label for="parameter">Parameter</label>
            <input type="text" name="parameter" id="parameter" class="form-control req" value="" />
        </div>
        <div class="col-md-4 form-group">
            <label for="specification">Specification</label>
            <input type="text" name="specification" id="specification" class="form-control req" value="" />
        </div>
        <div class="col-md-2 form-group">
            <label for="min">Min</label>
            <input type="text" name="min" id="min" class="form-control floatOnly" value="" />
        </div>
        <div class="col-md-2 form-group">
            <label for="max">Max</label>
            <input type="text" name="max" id="max" class="form-control floatOnly" value="" />
        </div>
        <div class="col-md-4 form-group">
            <label for="machine_tool">Machine Tools for Manufacturing	</label>
            <select name="machine_tool" id="machine_tool" class="form-control select2">
                <option value="">Select Machine Tool</option>
                <?php
                if(!empty($machineList)){
                    foreach($machineList as $row){
                        echo '<option value="'.$row->id.'">'.$row->category_name.'</option>';
                    }
                }
                ?>
            </select>
        </div>
        <div class="col-md-4 form-group">
            <label for="instrument">Instrument</label>
            <input type="text" name="instrument" id="instrument" class="form-control">
        </div>
        <div class="col-md-2 form-group">
            <label for="char_class">Special Char. Class</label>
            <select name="char_class" id="char_class" class="form-control symbl">
                <option value="">Select</option>
                <?php
                    foreach($this->classArray AS $key=>$symbol){
                        if(!empty($symbol)){ ?>
							<option value="<?=$key?>" data-img_path="<?=base_url('/assets/images/symbols/'.$key.'.png')?>"> <?=$symbol?> </option>
				<?php	}
                    }
                ?>
            </select>
        </div>
        <div class="col-md-2 form-group">
            <label for="size">Size</label>
            <input type="text" name="size" id="size" class="form-control">
        </div>
        <div class="col-md-2 form-group">
            <label for="frequency">Frequency</label>
            <input type="text" name="frequency" id="frequency" class="form-control numericOnly">
        </div>
        <div class="col-md-2 form-group">
            <label for="freq_unit">Frequency Unit</label>
            <select name="freq_unit" id="freq_unit" class="form-control select2">
                <option value="Hrs">Hrs</option>
                <option value="Lot">Lot</option>
            </select>
        </div>
        <div class="col-md-2 form-group">
            <label for="tool_name">Tool Name</label>
            <input type="text" name="tool_name" id="tool_name" class="form-control">
        </div>
        <div class="col-md-2 form-group">
            <label for="rpm">RPM</label>
            <input type="text" name="rpm" id="rpm" class="form-control">
        </div>
        <div class="col-md-2 form-group">
            <label for="feed">Feed</label>
            <input type="text" name="feed" id="feed" class="form-control">
        </div>
        <div class="col-md-6 form-group">
            <label for="reaction_plan">Reaction Plan</label>
            <input type="text" name="reaction_plan" id="reaction_plan" class="form-control">
        </div>
        <div class="col-md-6 form-group">
            <label for="control_method">Control Method</label>
            <select name="control_method[]" id="control_method" class="form-control select2 req" multiple>
                <option value="IIR">IIR (Incoming Inspection Report)</option>
                <option value="SAR">SAR (Setup Approval Report)</option>
                <option value="IPR">IPR (Inprocess Inspection Report)</option>
                <option value="FIR">FIR (Final Inspection Report)</option>
            </select>
            <div class="error control_method"></div>
        </div>        
        <div class="col-md-2 form-group">
            <?php $param = "{'formId':'inspection','fnsave':'saveInspection','controller':'items','res_function':'inspectionHtml'}"; ?>
            <button type="button" class="btn waves-effect waves-light btn-outline-success float-right mt-25 save-form btn-block" onclick="customStore(<?=$param?>)"><i class="fa fa-check"></i> Save</button>
        </div>
    </div>
</form>
<hr>
<div class="row">
<div class="col-md-4"></div>
    <div class="col-md-2">
        <a href="<?= base_url($headData->controller . '/createProductInspExcel/' . $item_id.'/' ) ?>" class="btn btn-block btn-info bg-info-dark mr-2" target="_blank">
            <i class="fa fa-download"></i> Download</span>
        </a>
    </div>
    <div class="col-md-4">
        <input type="file" name="insp_excel" id="insp_excel" class="form-control float-left" />
        <h6 class="col-md-12 msg text-primary text-center mt-1"></h6>
    </div>
    <div class="col-md-2">
        <a href="javascript:void(0);" class="btn btn-block btn-success bg-success-dark ml-2 importProductExcel" type="button">
            <i class="fa fa-upload"></i> Upload</span>
        </a>
    </div>
</div>
<hr>
<div class="row" id="inspectionId">
    <h5>Control Plan Detail : </h5>
    <div class="col-md-12" id="inspectionBody" >
        <!-- <table id="inspectionId" class="table table-bordered align-items-center fhTable">
            <thead class="thead-info">
                <tr>
                    <th style="width:5%;">#</th>
                    <th>Rev No</th>
                    <th>Process</th>
                    <th>Parameter</th>
                    <th>Specification</th>
                    <th>Special Char. Class</th>
                    <th>Instrument</th>
                    <th>Size</th>
                    <th>Frequency</th>
                    <th>Control Method</th>
                    <th class="text-center" style="width:10%;">Action</th>
                </tr>
            </thead>
            <tbody id="inspectionBody" class="scroll-tbody scrollable maxvh-60">
            </tbody>
        </table> -->
    </div>
</div>
</div>
<script>
var tbodyData = false;
$(document).ready(function(){
    setTimeout(function() {
        $('.symbl').select2({
            templateResult: formatSymbol
        });
        
    }, 30);
    if(!tbodyData){
        var postData = {'postData':{'item_id':$("#item_id").val()},'table_id':"inspectionId",'tbody_id':'inspectionBody','tfoot_id':'','fnget':'inspectionHtml'};
        getTransHtml(postData);
        tbodyData = true;
    }
    $(document).on('click', '.importProductExcel', function(e) {
            e.stopImmediatePropagation();e.preventDefault();
            $(this).attr("disabled", "disabled");
            var fd = new FormData();
            fd.append("insp_excel", $("#insp_excel")[0].files[0]);
            fd.append("item_id", $("#item_id").val());
            $.ajax({
                url: base_url + controller + '/importProductExcel',
                data: fd,
                type: "POST",
                processData: false,
                contentType: false,
                dataType: "json",
            }).done(function(data) { 
                $(".msg").html(data.message);
                $(this).removeAttr("disabled");
                $("#insp_excel").val(null);
                if (data.status == 1) {
                    inspectionHtml(data);   
                    // initTable(0);
                }
            });
        });
});

function inspectionHtml(data,formId="inspection"){ 
    if(data.status==1){
        // $('#'+formId)[0].reset();
        $("#parameter").val("");
        $("#specification").val("");
        $("#instrument").val("");
        $("#id").val("");
        var postData = {'postData':{'item_id':$("#item_id").val()},'table_id':"inspectionId",'tbody_id':'inspectionBody','tfoot_id':'','fnget':'inspectionHtml'};
        getTransHtml(postData);
    }else{
        if(typeof data.message === "object"){
            $(".error").html("");
            $.each( data.message, function( key, value ) {$("."+key).html(value);});
        }else{
            initTable();
            Swal.fire({ icon: 'error', title: data.message });
        }			
    }
}

function editInspParam(data, button) {
	$.each(data, function (key, value) { 
        $("#inspection #" + key).val(value); });
    $.each(data.control_method.split(","), function(i,e){ $("#control_method option[value='" + e + "']").prop("selected", true); });
    $('.symbl').select2({  templateResult: formatSymbol });
    $("#rev_no").select2();
    $("#process_id").select2();
    $("#freq_unit").select2();
    $("#control_method").select2();
    $("#machine_tool").select2();
}
</script>