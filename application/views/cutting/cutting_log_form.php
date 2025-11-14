<form data-res_function="getCuttingResponse">
    <div class="card">
        <div class="media align-items-center btn-group process-tags">
            <span class="badge bg-light-peach btn flex-fill">PRC No :
                <?=!empty($dataRow->prc_number)?$dataRow->prc_number:''?></span>
            <span class="badge bg-light-teal btn flex-fill">Plan Qty : <?=floatval($dataRow->prc_qty)?></span>
            <span class="badge bg-light-cream btn flex-fill" id="total_prod_qty">Production Qty : </span>
        </div>
    </div>
    <div class="row">
        <input type="hidden" name="id" id="id" value="">
        <input type="hidden" name="prc_id" id="prc_id" value="<?=$dataRow->id?>">
        <input type="hidden" name="process_id" id="process_id" value="3">
        <div class="col-md-4 form_group">
            <label for="trans_date">Date</label>
            <input type="date" name="trans_date" id="trans_date" class="form-control" value="<?=date("Y-m-d")?>"
                max="<?=date("Y-m-d")?>">
        </div>

        <div class="col-md-4 form-group">
            <label for="qty">Production Qty</label>
            <input type="text" id="qty" name="qty" class="form-control numericOnly req" value="">

        </div>
        <div class="col-md-4 form-group">
            <label for="wt_nos">Weight Per Nos.</label>
            <input type="text" name="wt_nos" id="wt_nos" class="form-control floatOnly " value="<?=$max_cut_weight;?>">
            <div class="error batch_stock_error"></div>
        </div>
		<!--
        <div class="col-md-3 form-group">
            <label for="process_rate">Cutting Rate</label>
            <input type="text" name="process_rate" id="process_rate" class="form-control floatOnly " value="0">
        </div>
		-->
        <div class="col-md-4">
            <label for="processor_id">Machine</label>
            <select name="processor_id" id="processor_id" class="form-control select2">
                <option value="">Select</option>
                <?php
                if(!empty($machineList)){
                    foreach($machineList AS $row){
                        ?><option value="<?=$row->id?>"><?=((!empty($row->item_code))?$row->item_code.' ':'').$row->item_name?></option><?php
                    }
                }
                ?>
            </select>
        </div>
        <div class="col-md-4 form-group">
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
        <div class="col-md-4  form-group">
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

        <div class="col-md-12 form-group">
            <label for="remark">Remark</label>
            <div class="input-group">
                <input type="text" name="remark" id="remark" class="form-control" value="">
                <div class="input-group-append">
                    <?php
                        $param = "{'formId':'addCuttingLog','fnsave':'saveCuttingLog','res_function':'getCuttingResponse','controller':'cutting'}";
                    ?>
                    <button type="button"
                        class="btn waves-effect waves-light btn-success btn-save save-form float-right btn-block"
                        onclick="customStore(<?=$param?>)" style="height:35px"><i class="fa fa-check"></i> Save</button>
                </div>
            </div>
        </div>
    </div>
</form>
<div class="col-md-12">
    <div class="row">
        <h5 style="width:100%;margin:0 auto;vertical-align:middle;border-top:1px solid #ccc;padding:5px 0px;">Process
            Transaction :</h5>
        <div class="table-responsive  mb-3">
            <table id='logTransTable' class="table table-bordered jpExcelTable mb-5">
                <thead class="text-center">
                    <tr>
                        <th style="min-width:20px">#</th>
                        <th style="min-width:100px">Date</th>
                        <th style="min-width:100px">Qty</th>
                        <th style="min-width:100px">Weight Per Nos.</th>
                        <th>Machine</th>
                        <th>Shift</th>
                        <th>Operator</th>
                        <th>Remark</th>
                        <th style="width:20px;">Action</th>
                    </tr>
                </thead>
                <tbody id="logTbodyData" class="text-center">

                </tbody>
            </table>
        </div>
    </div>
</div>
<script>
var tbodyData = false;
$(document).ready(function() {
    setTimeout(function() {
        $('#process_by').trigger('change');
    }, 50);

    if (!tbodyData) {
        var postData = {
            'postData': {
                'prc_id': $("#prc_id").val(),
            },
            'table_id': "logTransTable",
            'tbody_id': 'logTbodyData',
            'tfoot_id': '',
            'fnget': 'getCuttingLogHtml',
            'controller': 'cutting'
        };
        getCuttingLogHtml(postData);
        tbodyData = true;
    }

    
});

function getCuttingResponse(data, formId = "addCuttingLog") {
    if (data.status == 1) {
        $('#' + formId)[0].reset();
        var postData = {
            'postData': {
                'prc_id': $("#prc_id").val(),
            },
            'table_id': "logTransTable",
            'tbody_id': 'logTbodyData',
            'tfoot_id': '',
            'fnget': 'getCuttingLogHtml',
            'controller': 'cutting'
        };
        getCuttingLogHtml(postData);
    } else {
        if (typeof data.message === "object") {
            $(".error").html("");
            $.each(data.message, function(key, value) {
                $("." + key).html(value);
            });
        } else {
            Swal.fire({
                icon: 'error',
                title: data.message
            });
        }
    }
}
</script>