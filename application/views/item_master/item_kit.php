<form data-res_function="resItemBom">
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
            <div class="col-md-4 form-group">
                <label for="ref_item_id">Item Name</label>
                <select name="ref_item_id" id="ref_item_id" class="form-control select2">
                    <option value="">Select Item</option>
                    <?=getItemListOption($itemList)?>
                </select>
            </div>
            <div class="col-md-2 form-group">
                <label for="qty">Qty.</label>
                <input type="text" name="qty" id="qty" class="form-control floatOnly" value="">
            </div>
            <div class="col-md-6 form-group">
                <label for="remark">Remark</label>
                <div class="input-group">
                    <input type="text" name="remark" id="remark" class="form-control" value="">

                    <div class="input-group-append">
                        <button type="button" class="btn btn-outline-success btn-custom-save">
                            <i class="fa fa-plus"></i>
                        </button>
                    </div>
                </div>
            </div>
        </div>
    </div>
</form>

<hr>

<div class="col-md-12">
    <div class="row">
        <div class="col-md-12">
            <div class="table-responsive">
                <table id="itemBomData" class="table table-bordered">
                    <thead class="thead-dark" id="itemTheadData">
                        <tr>
                            <th>#</th>
                            <th>Item Name</th>
                            <th>Qty.</th>
                            <th>Remark</th>
                            <th class="text-center">Action</th>
                        </tr>
                    </thead>
                    <tbody id="itemTbodyData">
                        <tr>
                            <td colspan="5" class="text-center">No data available in table</td>
                        </tr>
                    </tbody>
                </table>
            </div>
        </div>
    </div>
</div>

<script>
$(document).ready(function(){
    var postData = {'postData':{'item_id':$("#item_id").val()},'table_id':"itemBomData",'tbody_id':'itemTbodyData','tfoot_id':'','fnget':'getItemBomTransHtml'};
    getTransHtml(postData);
});

function resItemBom(data,formId){
    if(data.status==1){
        $('#'+formId)[0].reset();
        initSelect2();

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
</script>