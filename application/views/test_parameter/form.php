<form data-res_function="resTestParameter">
    <div class="col-md-12">
        <div class="row">
                    
            <input type="hidden" name="id" id="id" value="">
            <input type="hidden" name="test_type" id="test_type" value="<?=(!empty($test_type) ? $test_type : '')?>">
			
            <div class="col-md-6 form-group">
                <label for="parameter">Parameter</label>
                <input type="text" name="parameter" id="parameter" class="form-control req" value="">
            </div>

            <div class="col-md-5 form-group">
                <label for="requirement">Input Type</label>
                <select name="requirement" id="requirement" class="form-control select2 req">
                    <option value="1">Range</option>
                    <option value="2">Min</option>
                    <option value="3">Max</option>
                    <option value="4">Other</option>
                </select>
            </div>

            <div class="col-md-1 form-group">
                <label>&nbsp;</label>
                <button type="button" class="btn btn-success btn-custom-save">
                    <i class="fa fa-plus"></i>
                </button>
            </div>
        </div>
    </div>
</form>

<hr>

<div class="col-md-12">
    <div class="row">
        <div class="col-md-12">
            <div class="table-responsive">
                <table id="testParaData" class="table table-bordered">
                    <thead class="thead-dark" id="theadData">
                        <tr>
                            <th>#</th>
                            <th>Parameter</th>
                            <th>Requirement</th>
                            <th class="text-center">Action</th>
                        </tr>
                    </thead>
                    <tbody id="tbodyData">
                        <tr>
                            <td colspan="4" class="text-center">No data available in table</td>
                        </tr>
                    </tbody>
                </table>
            </div>
        </div>
    </div>
</div>

<script>
$(document).ready(function(){
    setTimeout(function(){ $("#test_type").trigger('change'); }, 500);
    $(document).on('change','#test_type',function(){
        getTestParameterData();
    });
});

function getTestParameterData(){
    var test_type = $("#test_type").val();
    var postData = {'postData':{'test_type':$("#test_type").val()},'table_id':"testParaData",'tbody_id':'tbodyData','tfoot_id':'','fnget':'getTestParaHtml'};
    getTransHtml(postData);
}

function resTestParameter(data,formId){
    if(data.status==1){
        $("#id,#parameter,#requirement").val("");
        initSelect2();
        initTable();

        Swal.fire({ icon: 'success', title: data.message});

        var postData = {'postData':{'test_type':$("#test_type").val()},'table_id':"testParaData",'tbody_id':'tbodyData','tfoot_id':'','fnget':'getTestParaHtml'};
        getTransHtml(postData);
    }else{
        if(typeof data.message === "object"){
            $(".error").html("");
            $.each( data.message, function( key, value ) { $("."+key).html(value); });
        }else{
            Swal.fire({ icon: 'error', title: data.message });
        }			
    }
}

function resTrashTestPara(data){
    if(data.status==1){
        Swal.fire({ icon: 'success', title: data.message});
        initTable();

        var postData = {'postData':{'test_type':$("#test_type").val()},'table_id':"testParaData",'tbody_id':'tbodyData','tfoot_id':'','fnget':'getTestParaHtml'};
        getTransHtml(postData);
    }else{
        if(typeof data.message === "object"){
            $(".error").html("");
            $.each( data.message, function( key, value ) { $("."+key).html(value); });
        }else{
            Swal.fire({ icon: 'error', title: data.message });
        }			
    }
}

</script>