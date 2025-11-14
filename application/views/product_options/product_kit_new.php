<style>
	label, input.form-control{font-size: 12px !important;}
	.select2-container .select2-selection--single { height: 2rem !important;}
	.select2-container--default .select2-selection--single .select2-selection__rendered{line-height: 30px !important;}
	.select2-container .select2-selection--single .select2-selection__rendered, .select2-results__option { font-size: 12px !important; }
</style>
<form data-res_function="getProductKitHtml" >
    <div class="col-md-12">
        <div class="row">

            <div class="col-md-12">
                <h5 class="text-dark"><span id="productName"></span></h5>
                <div class="error gerenal_error"></div>
            </div>

            <input type="hidden" name="id" id="id" value="">
            <input type="hidden" name="item_id" id="item_id" class="item_id" value="<?=$item_id?>" />
			<!--
            <div class="col-md-3">
                <label for="group_name">Group Name</label>
                <input type="text" name="group_name" id="group_name" class="form-control req" value="" />
            </div>
			-->
            <div class="col-md-2">
              
                <label for="process_id">Required In</label>
                <select id="process_id" name="process_id" class="form-control select2 req">
                    <?php
                        
                        if(!in_array(3,array_column($process,'process_id'))){
                            ?>
                            <option value="3" >RM Cutting</option>
                            <?php
                        }
                        foreach($process as $row):
                            echo '<option value="'.$row->process_id.'" >'.$row->process_name.'</option>';
                        endforeach;
                    ?>
                </select>
            </div>
            <div class="col-md-4">
                <label for="kit_item_id">Item To Be Used</label>
                <select id="kit_item_id" name="kit_item_id" class="form-control select2 req">
                    <option value="">Select Item</option>
                    <?php
                        foreach($rawMaterial as $row):
                            echo '<option value="'.$row->id.'" data-unit_id="'.$row->unit_id.'">'.$row->item_code.' - '.$row->item_name.'</option>';
                        endforeach;
                    ?>
                </select>
            </div>
            <div class="col-md-3">
                <label for="ref_id">Alternative Of</label>
                <select id="ref_id" name="ref_id" class="form-control select2">
                    <?=((!empty($mbOptions)) ? $mbOptions : '')?>
                </select>
            </div>

            <div class="col-md-3">
                <label for="kit_item_qty">Consumption Qty</label>
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
                    <tr class="text-center">
                        <th style="width:5%;">#</th>
                        <th class="text-left">Process</th>
                        <th class="text-left">Item Code</th>
                        <th class="text-left">Item Name</th>
                        <th>Is Alt.?</th>
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
		$("#ref_id").html(data.mbOptions);
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