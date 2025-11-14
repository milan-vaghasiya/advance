<form id="pop_inspection" data-res_function="popInspectionHtml">
    <div class="col-md-12">
        <div class="row">
            <input type="hidden" name="id" id="id" class="id" value="" />
            <input type="hidden" name="item_id" id="item_id" class="item_id" value="<?=$item_id?>" />
            <input type="hidden" name="control_method" id="control_method"  value="POP" />

            <div class="col-md-6 form-group">
                <label for="category_id">Category</label>
                <select name="category_id[]" id="category_id" class="form-control select2 req" multiple>
                    <?php
                    if(!empty($catData)){
                        foreach($catData as $row){
                            echo '<option value="'.$row->id.'">'.$row->category_name.'</option>';
                        }
                    }
                    ?>
                </select>
                <div class="error category_id"></div>
            </div>
            <div class="col-md-6 form-group">
                <label for="parameter">Parameter</label>
                <input type="text" name="parameter" id="parameter" class="form-control req" value="" />
            </div>
            <div class="col-md-6 form-group">
                <label for="specification">Specification</label>
                <input type="text" name="specification" id="specification" class="form-control req" value="" />
            </div>
            <div class="col-md-3 form-group">
                <label for="min">Min</label>
                <input type="text" name="min" id="min" class="form-control floatOnly" value="" />
            </div>
            <div class="col-md-3 form-group">
                <label for="max">Max</label>
                <input type="text" name="max" id="max" class="form-control floatOnly" value="" />
            </div>
            <div class="col-md-6 form-group">
                <label for="instrument">Instrument</label>
                <div class="input-group">
                    <input type="text" name="instrument" id="instrument" class="form-control" value="" />
                    <div class="input-group-append">
                        <?php
                        $param = "{'formId':'pop_inspection','fnsave':'saveInspection','controller':'items','res_function':'popInspectionHtml'}";
                        ?>
                        <button type="button" class="btn waves-effect waves-light btn-outline-success btn-save save-form" onclick="customStore(<?=$param?>)"><i class="fa fa-check"></i> Save</button>
                    </div>
                </div>
            </div>
        </div>
    </div>
    <hr>
    <div class="row">
        <div class="col-md-4">
            <a href="<?= base_url($headData->controller.'/createPopInspExcel/'.$item_id.'/')?>" class="btn btn-block btn-info bg-info-dark mr-2" target="_blank">
                <i class="fa fa-download"></i> Download</span>
            </a>
        </div>
        <div class="col-md-4">
            <input type="file" name="insp_excel" id="insp_excel" class="form-control float-left" />
            <h6 class="col-md-12 msg text-primary text-center mt-1"></h6>
        </div>
        <div class="col-md-4">
            <a href="javascript:void(0);" class="btn btn-block btn-success bg-success-dark ml-2 importPopExcel" type="button">
                <i class="fa fa-upload"></i> Upload</span>
            </a>
        </div>
    </div>
    <hr>
    <div class="col-md-12">
        <div class="row">
            <div class="table-responsive">
                <table id="popInspectionId" class="table table-bordered align-items-center">
                    <thead class="thead-info">
                        <tr>
                            <th style="width:5%;">#</th>
                            <th style="width:30%;">Category</th>
                            <th style="width:20%;">Parameter</th>
                            <th style="width:20%;">Specification</th>
                            <th style="width:10%;">Min</th>
                            <th style="width:10%;">Max</th>
                            <th style="width:20%;">Instrument</th>
                            <th class="text-center" style="width:5%;">Action</th>
                        </tr>
                    </thead>
                    <tbody id="inspectionBody">
                    </tbody>
                </table>
            </div>
        </div>
    </div>
</form>

<script>
var tbodyData = false;
$(document).ready(function(){
    if(!tbodyData){
        var postData = {'postData':{'item_id':$("#item_id").val()},'table_id':"popInspectionId",'tbody_id':'inspectionBody','tfoot_id':'','fnget':'popInspectionHtml'};
        getTransHtml(postData);
        tbodyData = true;
    }

    $(document).on('click', '.importPopExcel', function(e) {
        e.stopImmediatePropagation();e.preventDefault();
        $(this).attr("disabled", "disabled");
        var fd = new FormData();
        fd.append("insp_excel", $("#insp_excel")[0].files[0]);
        fd.append("item_id", $("#item_id").val());
        fd.append("control_method", $("#control_method").val());
        $.ajax({
            url: base_url + controller + '/importPopExcel',
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
                popInspectionHtml(data);   
            }
        });
    });
});

function popInspectionHtml(data,formId="pop_inspection"){
    if(data.status==1){
        $('#parameter').val('');
        $('#specification').val('');
        $('#instrument').val('');

        var postData = {'postData':{'item_id':$("#item_id").val()},'table_id':"popInspectionId",'tbody_id':'inspectionBody','tfoot_id':'','fnget':'popInspectionHtml'};
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

</script>