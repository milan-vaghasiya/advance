<style>
.ui-sortable-handle{cursor: move;}
.ui-sortable-handle:hover{background-color: #daeafa;border-color: #9fc9f3;cursor: move;}
</style>

<form data-res_function="getProductProcessHtml">
    <div class="col-md-12">
        <div class="row ">
            <div class="float-right">
                <div class="col-md-4 float-right" hidden>
                    <label for="production_type">Production Flow</label>
                    <select name="production_type" id="production_type" class="form-control select2">
                        <option value="1" <?=(!empty($itemData->production_type) && $itemData->production_type == 1)?"selected":""?>>Manual Flow</option>
                        <option value="2" <?=(!empty($itemData->production_type) && $itemData->production_type == 2)?"selected":""?>>Fixed Flow</option> 
                    </select>
                </div>
                <div class="col-md-4 form-group float-right">
                    <label for="cutting_flow">Cutting ?</label>
                    <select name="cutting_flow" id="cutting_flow" class="form-control select2 productionSetting">
                        <option value="1" <?=(!empty($itemData->cutting_flow) && $itemData->cutting_flow == 1)?"selected":""?>>No</option>
                        <option value="2" <?=(!empty($itemData->cutting_flow) && $itemData->cutting_flow == 2)?"selected":""?>>Yes</option> 
                    </select>
                </div>
            </div>
            
        </div>
        <div class="row">

            <input type="hidden" name="id" id="id" value="" />
            <input type="hidden" name="item_id" id="item_id" value="<?=$item_id?>" />
            <div class="col-md-10 form-group">
                <label for="process_id">Production Process</label>
                <select name="process_id" id="process_id" class="form-control select2">
                    <option value="">Select Process</option>
                    <?php
                    foreach ($processDataList as $row) :
                        echo '<option value="' . $row->id . '">' . $row->process_name . '</option>';
                    endforeach;
                    ?>
                </select>
            </div>

            <div class="col-md-2">
                <label for="process_id">&nbsp;</label>
                <?php $param = "{'formId':'viewProductProcess','fnsave':'saveProductProcess','controller':'productOption','res_function':'getProductProcessHtml'}"; ?>
                <button type="button" class="btn btn-block waves-effect waves-light btn-outline-success btn-save save-form float-right" onclick="customStore(<?=$param?>)" style="height:36px"><i class="fa fa-check"></i> Add</button>
            </div>
        
        </div>
    </div>
    <hr>
    <div class="col-md-12">
        <div class="row">
            <div class="col-md-12 form-group">
                <h6 style="color:#ff0000;font-size:1rem;"><i>Note : Drag & Drop Row to Change Process Sequence</i></h6>
            </div>      
            
            <table id="itemProcess" class="table excel_table table-bordered">
                <thead class="thead-info">
                    <tr>
                        <th style="width:10%;text-align:center;">#</th>
                        <th style="width:70%;">Process Name</th>
                        <th style="width:20%;">Sequence</th>
                        <th style="width:20%;">Action</th>
                    </tr>
                </thead>
                <tbody id="itemProcessData">
                </tbody>
            </table>
        </div>
    </div>
</form>

<script>
var tbodyData = false;
$(document).ready(function(){
    if(!tbodyData){
        var postData = {'postData':{'item_id':$("#item_id").val()},'table_id':"itemProcess",'tbody_id':'itemProcessData','tfoot_id':'','fnget':'productProcessHtml'};
        getTransHtml(postData);
        tbodyData = true;
    }

    $(document).on('change','.productionSetting', function(e){
        e.stopImmediatePropagation();e.preventDefault();
        var production_type = $('#production_type').val();
        var cutting_flow = $('#cutting_flow').val();
        var item_id = $("#item_id").val();
        $.ajax({
            url:base_url + 'productOption/setProductionType',
            method:"POST",
            data:{production_type:production_type,cutting_flow:cutting_flow,item_id:item_id},
            dataType:"json",
            success:function(data){
                
            }
        });
    });
});

function getProductProcessHtml(data,formId="viewProductProcess"){ 
    if(data.status==1){
        $('#'+formId)[0].reset();
        var postData = {'postData':{'item_id':$("#item_id").val()},'table_id':"itemProcess",'tbody_id':'itemProcessData','tfoot_id':'','fnget':'productProcessHtml'}; 
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
