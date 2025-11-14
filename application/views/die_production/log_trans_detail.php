<form data-res_function="getLogTransResponse">    
    <div class="col-md-12">
        <div class="row">
            <input type="hidden" name="die_id" id="die_id" value="<?=(!empty($die_id) ? $die_id : 0)?>">

            <div class="table-responsive">
                <table id='logTransTable' class="table table-bordered jpExcelTable">
                    <thead class="text-center">
                        <tr>
                            <th style="width:20px">#</th>
                            <th>Log Date</th>
                            <th>Die Type</th>
                            <th>Product</th>
                            <th>Job No</th>
                            <th>Machine</th>
                            <th>Setter</th>
                            <th>Production Time</th>
                            <th>Material Value</th>
                            <th>Machine Hourly Rate</th>
                            <th>Setup Description</th>
                            <th style="width:50px;">Action</th>
                        </tr>
                    </thead>
                    <tbody id="logTbodyData">            
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
        var postData = {'postData':{'die_id':$("#die_id").val()},'table_id':"logTransTable",'tbody_id':'logTbodyData','tfoot_id':'','fnget':'getLogTransHtml'};
        getTransHtml(postData);
        tbodyData = true;
    }
});

function getLogTransResponse(data,formId="logTansDetail"){ 
    if(data.status==1){
        $('#'+formId)[0].reset();
        var postData = {'postData':{'die_id':$("#die_id").val()},'table_id':"logTransTable",'tbody_id':'logTbodyData','tfoot_id':'','fnget':'getLogTransHtml'};
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