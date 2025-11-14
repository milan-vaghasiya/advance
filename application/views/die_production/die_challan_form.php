<form data-res_function="getDieChallanResponse">
    <div class="row">
        <input type="hidden" name="id" id="id" value="">
        <input type="hidden" name="die_id" id="die_id" value="<?=$die_id?>">
        <input type="hidden" name="pending_qty" id="pending_qty" value="<?=$qty?>">

        <div class="col-md-3 form_group">
            <label for="trans_date">Date</label>
            <input type="date" name="trans_date" id="trans_date" class="form-control" value="<?=date("Y-m-d")?>" max="<?=date("Y-m-d")?>">
        </div>
        
        <div class="col-md-3 form-group">
            <label for="production_qty">Production Qty</label>
            <input type="text" id="production_qty" class="form-control numericOnly req qtyCal" value="">
        </div>

        <div class="col-md-3 form-group">
            <label for="ok_qty">Ok Qty</label>
            <input type="text" name="ok_qty" id="ok_qty" class="form-control numericOnly req " value="" readonly>
            <div class="error batch_stock_error"></div>
        </div>

        <div class="col-md-3 form-group" >
            <label for="rej_found">Rejection Qty</label>
            <input type="text" name="rej_found" id="rej_found" class="form-control numericOnly qtyCal">
        </div>
       
        <div class="col-md-3 form-group">
            <label for="production_time">Production Time</label>
            <input type="text" name="production_time" id="production_time" class="form-control">
        </div>

        <div class="col-md-3">
            <label for="process_by">Process By</label>
            <select name="process_by" id="process_by" class="form-control select2">
                <option value="1">Inhouse Machining</option>
                <option value="2">Department Process</option>
            </select>
        </div>

        <div class="col-md-3">
            <label for="processor_id">Machine/Dept.</label>
            <select name="processor_id" id="processor_id" class="form-control select2">
                <option value="0">Select</option>
            </select>
        </div>

        <div class="col-md-3 form-group">
            <label for="shift_id">Shift</label>
            <select name="shift_id" id="shift_id" class="form-control select2">
                <option value="">Select Shift</option>
                <?php
                if(!empty($shiftData)){
                    foreach ($shiftData as $row) :
                        echo '<option value="' . $row->id . '" >' . $row->shift_name . '</option>';
                    endforeach;
                }
                ?>
            </select>
            <div class="error shift_id"></div>
        </div>

        <div class="col-md-3">
            <label for="operator_id">Operator</label>
            <select name="operator_id" id="operator_id" class="form-control select2">
                <option value="0">Select</option>
                <?php
                if(!empty($operatorList)){
                    foreach($operatorList as $row){
                        ?><option value="<?=$row->id?>"><?=$row->emp_name?></option><?php
                    }
                }
                ?>
            </select>
        </div>
        
        <div class="col-md-9 form-group">
            <label for="remark">Remark</label>
            <div class="input-group">
                <input type="text" name="remark" id="remark" class="form-control" value="">
                <div class="input-group-append">
                    <?php
                        $param = "{'formId':'addDieChallan','fnsave':'saveDieChallan','res_function':'getDieChallanResponse'}";
                    ?>
                    <button type="button" class="btn waves-effect waves-light btn-success btn-save save-form float-right btn-block" onclick="customStore(<?=$param?>)" style="height:35px"><i class="fa fa-check"></i> Save</button>
                </div>
            </div>
        </div>

    </div>
</form>
<div class="col-md-12">
    <div class="row">
        <h5 style="width:100%;margin:0 auto;vertical-align:middle;border-top:1px solid #ccc;padding:5px 0px;">Process Transaction :</h5>
        <div class="table-responsive  mb-3">
            <table id='challanTransTable' class="table table-bordered jpExcelTable mb-5">
                <thead class="text-center">
                    <tr>
                        <th style="min-width:20px">#</th>
                        <th style="min-width:100px">Date</th>
                        <th>Production Time</th>
                        <th>Department/Machine</th>
                        <th>Operator</th>
                        <th>Shift</th>
                        <th>OK Qty.</th>
                        <th>Rejection Qty.</th>
                        <th>Remark</th>
                        <th style="width:50px;">Action</th>
                    </tr>
                </thead>
                <tbody id="challanTbodyData">                   
                </tbody>
            </table>
        </div>
    </div>
</div>
<script>
var tbodyData = false;
$(document).ready(function(){
    if(!tbodyData){
        var postData = {'postData':{'die_id':$("#die_id").val()},'table_id':"challanTransTable",'tbody_id':'challanTbodyData','tfoot_id':'','fnget':'getDieChallanHtml'};
        getTransHtml(postData);
        tbodyData = true;
    }
});
function getDieChallanResponse(data,formId="addDieChallan"){ 
    if(data.status==1){
        $('#'+formId)[0].reset();
        var postData = {'postData':{'die_id':$("#die_id").val()},'table_id':"challanTransTable",'tbody_id':'challanTbodyData','tfoot_id':'','fnget':'getDieChallanHtml'};
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