<form data-res_function="getProductKitHtml" >
    <div class="col-md-12">
        <div class="row">

            <div class="col-md-12">
                <h5 class="text-dark"><span id="productName"></span></h5>
                <div class="error gerenal_error"></div>
            </div>

            <input type="hidden" name="id" id="id" value="">
            <input type="hidden" name="item_id" id="item_id" class="item_id" value="<?=$item_id?>" />

            <div class="col-md-3">
                <label for="group_name">Group Name</label>
                <input type="text" name="group_name" id="group_name" class="form-control req" value="" />
            </div>
            <div class="col-md-2">
                <label for="process_id">Process</label>
                <select id="process_id" name="process_id" class="form-control select2 req">
                    <?php
                        foreach($process as $row):
                            echo '<option value="'.$row->process_id.'" >'.$row->process_name.'</option>';
                        endforeach;
                    ?>
                </select>
            </div>
            <div class="col-md-4">
                <label for="kit_item_id">Used To Be Item</label>
                <select id="kit_item_id" name="kit_item_id" class="form-control select2 req">
                    <option value="">Select Item</option>
                    <?php
                        foreach($rawMaterial as $row):
                            echo '<option value="'.$row->id.'" data-unit_id="'.$row->unit_id.'">'.$row->item_code.' -'.$row->item_name.'</option>';
                        endforeach;
                    ?>
                </select>
            </div>

            <div class="col-md-3">
                <label for="kit_item_qty">Bom Qty</label>
                <div class="input-group">
                    <input type="text" id="kit_item_qty" name="kit_item_qty" class="form-control floatOnly req" value="" min="0" />
                    <div class="input-group-append">
                        <?php
                            $param = "{'formId':'addProductKitItems','fnsave':'saveProductKit','controller':'productOption','res_function':'getProductKitHtml'}";
                        ?>
                        <button type="button" class="btn waves-effect waves-light btn-outline-success btn-save save-form float-right" onclick="customStore(<?=$param?>)" style="height:36px"><i class="fa fa-check"></i> Save</button>
                    </div>
                </div>                
            </div>

        </div>
        <hr>
        <div class="row">
            <div class="table-responsive">
            <table id="productKit" class="table table-bordered align-items-center">
                <thead class="thead-info">
                    <tr>
                        <th style="width:5%;">#</th>
                        <th>Group Name</th>
                        <th>Process</th>
                        <th>Item Name</th>
                        <th>Bom Qty</th>
                        <th class="text-center" style="width:10%;">Action</th>
                    </tr>
                </thead>
                <tbody id="kitItems">
                </tbody>
            </table>
        </div>
        </div>
    </div>
</form>
<script src="<?php echo base_url();?>assets/js/custom/product.js"></script>
<script>
var tbodyData = false;
$(document).ready(function(){
    setPlaceHolder();
    if(!tbodyData){
        var postData = {'postData':{'item_id':$("#item_id").val()},'table_id':"productKit",'tbody_id':'kitItems','tfoot_id':'','fnget':'productKitHtml'};
        getTransHtml(postData);
        tbodyData = true;
    }
});

function getProductKitHtml(data,formId="addProductKitItems"){ 
    if(data.status==1){
        $('#'+formId)[0].reset();
        var postData = {'postData':{'item_id':$("#item_id").val()},'table_id':"productKit",'tbody_id':'kitItems','tfoot_id':'','fnget':'productKitHtml'};
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