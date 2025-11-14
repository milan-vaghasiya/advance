<form data-res_function="getDieBomHtml" >
    <div class="col-md-12">
        <div class="row">

            <div class="col-md-12">
                <h5 class="text-dark"><span id="productName"></span></h5>
                <div class="error gerenal_error"></div>
            </div>

            <input type="hidden" name="id" id="id" value="">
            <input type="hidden" name="item_id" id="item_id" class="item_id" value="<?=$item_id?>" />

            <div class="col-md-4">
                <label for="ref_cat_id">Category</label>
                <select id="ref_cat_id" name="ref_cat_id" class="form-control select2 req">
                    <option value="">Select Category</option>
                    <?php
                        foreach($categoryList as $row):
                            echo '<option value="'.$row->id.'" >'.$row->category_name.'</option>';
                        endforeach;
                    ?>
                </select>
            </div>

            <div class="col-md-4">
                <label for="ref_item_id">Used To Be Item</label>
				<select id="ref_item_id" name="ref_item_id" class="form-control select2 req" style="min-width:100px;">
					<option value="">Select Item</option>
					<?php
						foreach($rawMaterial as $row):
							echo '<option value="'.$row->id.'">'.(!empty($row->item_code)?'['.$row->item_code.'] ':'').$row->item_name.'</option>';
						endforeach;
					?>
				</select>
            </div>
			<div class="col-md-4">
                <label for="qty">Bom Qty<small>(KGS)</small></label>
                <div class="input-group">
                    <input type="text" id="qty" name="qty" class="form-control floatOnly req" value="" />
					<div class="input-group-append">
						<?php $param = "{'formId':'addDieBom','fnsave':'saveDieBom','controller':'productOption','res_function':'getDieBomHtml'}"; ?>
						<button type="button" class="btn waves-effect waves-light btn-outline-success btn-save save-form float-right" onclick="customStore(<?=$param?>)" style="height:36px"><i class="fa fa-check"></i> Save</button>
					</div>
				</div>
			</div>
		</div>
        <hr>
        <div class="row">
            <div class="table-responsive">
            <table id="dieBomTbl" class="table table-bordered align-items-center">
                <thead class="thead-info">
                    <tr>
                        <th style="width:5%;">#</th>
                        <th>Category</th>
                        <th>Item Name</th>
						<th>Bom Qty<small>(KGS)</small></th>
                        <th class="text-center" style="width:10%;">Action</th>
                    </tr>
                </thead>
                <tbody id="bomItems">
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
        var postData = {'postData':{'item_id':$("#item_id").val()},'table_id':"dieBomTbl",'tbody_id':'bomItems','tfoot_id':'','fnget':'dieBomHtml'};
        getTransHtml(postData);
        tbodyData = true;
    }
});

function getDieBomHtml(data,formId="addDieBom"){ 
    if(data.status==1){
        $('#'+formId)[0].reset();
        var postData = {'postData':{'item_id':$("#item_id").val()},'table_id':"dieBomTbl",'tbody_id':'bomItems','tfoot_id':'','fnget':'dieBomHtml'};
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