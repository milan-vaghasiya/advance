<form data-res_function="getPackingStandardHtml" >
    <div class="col-md-12">
        <div class="row">

            <input type="hidden" name="id" id="id" value="">
            <input type="hidden" name="item_id" id="item_id" class="item_id" value="<?=$item_id?>" />

            <div class="col-md-5">
                <label for="ref_item_id">Packing Material</label>
                <select id="ref_item_id" name="ref_item_id" class="form-control select2 req">
                    <option value="">Select Material</option>
                    <?php
                    if(!empty($itemList)):
                        foreach($itemList as $row):
                            echo '<option value="'.$row->id.'">'.(!empty($row->item_code) ? '['.$row->item_code.'] ' : '').$row->item_name.'</option>';
                        endforeach;
                    endif;
                    ?>
                </select>
            </div>

            <div class="col-md-3">
                <label for="qty">Qty Per Box</label>
                <input type="text" id="qty" name="qty" class="form-control floatOnly req" value="" />
            </div>

            <div class="col-md-4">
                <label for="pack_wt">Packing Weight(KGS)</label>
                <div class="input-group">
                    <input type="text" id="pack_wt" name="pack_wt" class="form-control floatOnly req" value="" />
                    <div class="input-group-append">
                        <?php
                            $param = "{'formId':'addPackingStandard','fnsave':'savePackingStandard','controller':'productOption','res_function':'getPackingStandardHtml'}";
                        ?>
                        <button type="button" class="btn waves-effect waves-light btn-outline-success btn-save save-form float-right" onclick="customStore(<?=$param?>)" style="height:36px"><i class="fa fa-check"></i> Save</button>
                    </div>
                </div>                
            </div>

        </div>
        <hr>
        <div class="row">
            <div class="table-responsive">
                <table id="packingTbl" class="table table-bordered align-items-center">
                    <thead class="thead-info">
                        <tr class="text-center">
                            <th style="width:5%;">#</th>
                            <th>Packing Material</th>
                            <th>Qty Per Box</th>
                            <th>Packing Weight(KGS)</th>
                            <th class="text-center" style="width:10%;">Action</th>
                        </tr>
                    </thead>
                    <tbody id="packingBody">
                    </tbody>
                </table>
            </div>
        </div>
    </div>
</form>
<script>
var tbodyData = false;
$(document).ready(function(){
    setPlaceHolder();
    if(!tbodyData){
        var postData = {'postData':{'item_id':$("#item_id").val()},'table_id':"packingTbl",'tbody_id':'packingBody','tfoot_id':'','fnget':'packingStandardHtml'};
        getTransHtml(postData);
        tbodyData = true;
    }
});

function getPackingStandardHtml(data,formId="addPackingStandard"){ 
    if(data.status==1){
        $('#'+formId)[0].reset();
        var postData = {'postData':{'item_id':$("#item_id").val()},'table_id':"packingTbl",'tbody_id':'packingBody','tfoot_id':'','fnget':'packingStandardHtml'};
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