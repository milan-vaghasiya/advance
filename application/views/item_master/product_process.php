<style>
.ui-sortable-handle{cursor: move;}
.ui-sortable-handle:hover{background-color: #daeafa;border-color: #9fc9f3;cursor: move;}
</style>
<form data-res_function="resItemProcess">
    <div class="col-md-12">
        <div class="row">
            <div class="col-md-12">
                <table class="table table-bordered">
                    <tr>
                        <th>Item Name</th>
                        <th><?=$itemData->item_name?></th>
                        <th>Standard Qty.</th>
                        <th><?=$itemData->packing_standard?></th>
                    </tr>
                </table>
            </div>
        </div>
        <div class="row">
            <input type="hidden" name="id" id="id" value="">
            <input type="hidden" name="item_id" id="item_id" value="<?=$itemData->id?>">            
            <div class="col-md-10 form-group">
                <label for="process_id">Process</label>
                <select name="process_id[]" id="process_id" class="form-control select2" multiple = "multiple">
                    <option value="">Select Process</option>
                    
                </select>
            </div>
           
            <div class="col-md-2 form-group ">
                <label for="">&nbsp;</label>
                <button type="button" class="btn btn-outline-success btn-custom-save btn-block" >
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
                <table id="itemProcessData" class="table table-bordered">
                    <thead class="thead-dark" id="itemTheadData">
                        <tr>
                            <th>#</th>
                            <th>Process</th>
                            <th style="width:10%;">Sequence</th>
                        </tr>
                    </thead>
                    <tbody id="itemPTbodyData">
                        <tr>
                            <td colspan="3" class="text-center">No data available in table</td>
                        </tr>
                    </tbody>
                </table>
            </div>
        </div>
    </div>
</div>

<script>
$(document).ready(function(){
    var postData = {'postData':{'item_id':$("#item_id").val()},'table_id':"itemProcessData",'tbody_id':'itemPTbodyData','tfoot_id':'','fnget':'getProduuctProcessTransHtml'};
    getProcessTransHtml(postData);
    $("#itemProcessData tbody").sortable({
        items: 'tr',
        cursor: 'pointer',
        axis: 'y',
        dropOnEmpty: false,
        helper: fixWidthHelper,
        start: function (e, ui) {
            ui.item.addClass("selected");
        },
        stop: function (e, ui) {
            ui.item.removeClass("selected");
            $(this).find("tr").each(function (index) {
                $(this).find("td").eq(2).html(index+1);
            });
        },
        update: function () 
        {
            var ids='';
            $(this).find("tr").each(function (index) {ids += $(this).attr("id")+",";});
            var lastChar = ids.slice(-1);
            if (lastChar == ',') {ids = ids.slice(0, -1);}
            
            $.ajax({
                url: base_url + controller + '/updateProductProcessSequance',
                type:'post',
                data:{id:ids},
                dataType:'json',
                global:false,
                success:function(data){}
            });
        }
    }); 
});

function resItemProcess(data,formId){
    if(data.status==1){
        $('#'+formId)[0].reset();
        initSelect2();

        Swal.fire({ icon: 'success', title: data.message});

        var postData = {'postData':{'item_id':$("#item_id").val()},'table_id':"itemProcessData",'tbody_id':'itemPTbodyData ','tfoot_id':'','fnget':'getProduuctProcessTransHtml'};
        getProcessTransHtml(postData);
    }else{
        if(typeof data.message === "object"){
            $(".error").html("");
            $.each( data.message, function( key, value ) { $("."+key).html(value); });
        }else{
            Swal.fire({ icon: 'error', title: data.message });
        }			
    }
}

function resTrashItemBom(data){
    if(data.status==1){
        Swal.fire({ icon: 'success', title: data.message});

        var postData = {'postData':{'item_id':$("#item_id").val()},'table_id':"itemBomData",'tbody_id':'itemTbodyData','tfoot_id':'','fnget':'getItemBomTransHtml'};
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

function fixWidthHelper(e, ui) {
    ui.children().each(function() {
        $(this).width($(this).width());
    });
    return ui;
}


</script>