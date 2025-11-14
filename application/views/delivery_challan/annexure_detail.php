<form data-res_function="getAnnexureResponse">
    <div class="row">
        <input type="hidden" name="dc_id" id="dc_id" value="<?=$dc_id?>">
        <input type="hidden" name="item_id" id="item_id" value="">
        <div class="col-md-8 form-group">
            <label for="dc_trans_id">Item</label>
            <select name="dc_trans_id" id="dc_trans_id" class="form-control select2 req">
                <option value="">Select Item</option>
                <?php
                if(!empty($dcItemList)){
                    foreach($dcItemList AS $row){
                        ?><option value="<?=$row->id?>" data-item_id="<?=$row->item_id?>"><?=(!empty($row->item_code)?'['.$row->item_code.'] ':'').$row->item_name?></option><?php
                    }
                }
                ?>
            </select>
        </div>
        <div class="col-md-4 form-group">
            <label for="cartoon_no">Cartoon No</label>
            <input type="text" name="cartoon_no" id="cartoon_no" class="form-control req">
        </div>
        <div class="error batchDetail"></div>
        <div class="col-md-12 form-group">
            <table class="table jpExcelTable">
                <thead>
                    <tr>
                        <th>#</th>
                        <th>Batch No.</th>
                        <th>Stock (Box Qty)</th>
                        <th>Box Qty.</th>
                    </tr>
                </thead>
                <tbody id="annexTbody">
                    <tr>
                        <th class="text-center" colspan="4">No Data Available.</th>
                    </tr>
                </tbody>
            </table>
        </div>
    </div>
    <div class="row">
        <div class="float-right">
            <?php
                $param = "{'formId':'addAnnexureDetail','fnsave':'saveAnnexureDetail','res_function':'getAnnexureResponse','controller':'deliveryChallan'}";
            ?>
            <button type="button" class="btn waves-effect waves-light btn-success btn-save save-form float-right" onclick="customStore(<?=$param?>)" style="height:35px"><i class="fa fa-check"></i> Save</button>                
        </div>
    </div>
</form>
<hr>
<div class="row">
    <div class="table-responsive">
        <table class="table jpExcelTable"  id="logTransTable">
            <thead>
                <tr>
                    <th>#</th>
                    <th>Item</th>
                    <th>Cartoon No</th>
                    <th>Batch No</th>
                    <th>Total Box</th>
                    <th>Total Qty</th>
                    <th>Action</th>
                </tr>
            </thead>
            <tbody id="logTbodyData">

            </tbody>
        </table>
    </div>
</div>
<script>
    var tbodyData = false;
    $(document).ready(function(){
        if(!tbodyData){
            var postData = {'postData':{'dc_id':$("#dc_id").val(),},'table_id':"logTransTable",'tbody_id':'logTbodyData','tfoot_id':'','fnget':'getAnnexureHtml','controller':'deliveryChallan'};
            getAnnexureHtml(postData);
            tbodyData = true;
        }
        $(document).on('change','#dc_trans_id',function(e){
            e.stopImmediatePropagation();e.preventDefault();
            var dc_trans_id = $('#dc_trans_id').val();
            var dc_id = $('#dc_id').val();
            var item_id = $('#dc_trans_id :selected').data('item_id');
            $("#item_id").val(item_id);
            $("#annexTbody").html("");
            if (item_id) {
                $.ajax({
                    url: base_url + 'deliveryChallan/getPackingBoxDetail',
                    type: 'post',
                    data: { item_id: item_id,dc_trans_id:dc_trans_id,dc_id:dc_id },
				    dataType:'json',
                    success: function (data) {
                       $("#annexTbody").html(data.tbodyData);
                    }
                });
            } else {
                $('.item_id').html("Item is required.");
            }
        });

        $(document).on('keyup change','.calculateBox',function(e){
            e.stopImmediatePropagation();e.preventDefault();
            var row_id = $(this).data('srno');
            var box_qty = $(this).val() || 0;
            var stock_qty = $("#batch_stock_"+row_id).val();
            var opt_qty = $("#opt_qty_"+row_id).val();
            var batch_qty = 0;

            batch_qty = parseFloat((parseFloat(box_qty) * parseFloat(opt_qty))).toFixed(2);
            $("#batch_qty_"+row_id).val(batch_qty); console.log(box_qty +' '+opt_qty);

            $(".batch_qty_"+row_id).html('');
            if(parseFloat(batch_qty) > parseFloat(stock_qty)){
                $(".batch_qty_"+row_id).html('Invalid Qty.');
                $("#batch_qty_"+row_id).val(0);
                $(this).val("");
            }   
        });
    });

    function getAnnexureResponse(data,formId="addAnnexureDetail"){ 
        if(data.status==1){
            var cartoon_no = $("#cartoon_no").val();
            $('#'+formId)[0].reset();
            $("#annexTbody").html("");
            $("#cartoon_no").val(cartoon_no);
            var postData = {'postData':{'dc_id':$("#dc_id").val(),},'table_id':"logTransTable",'tbody_id':'logTbodyData','tfoot_id':'','fnget':'getAnnexureHtml','controller':'deliveryChallan'};
            getAnnexureHtml(postData);
        }else{
            if(typeof data.message === "object"){
                $(".error").html("");
                $.each( data.message, function( key, value ) {$("."+key).html(value);});
            }else{
                Swal.fire({ icon: 'error', title: data.message });
            }			
        }
    }

    function getAnnexureHtml(data){
	    var postData = data.postData || {};
        var fnget = data.fnget || "";
        var controllerName = data.controller || controller;

        var table_id = data.table_id || "";
        var thead_id = data.thead_id || "";
        var tbody_id = data.tbody_id || "";
        var tfoot_id = data.tfoot_id || "";	

        if(thead_id != ""){ $("#"+table_id+" #"+thead_id).html(data.thead);  }
        
        $.ajax({
            url: base_url + controllerName + '/' + fnget,
            data:postData,
            type: "POST",
            dataType:"json",
            beforeSend: function() {
                if(table_id != ""){
                    var columnCount = $('#'+table_id+' thead tr').first().children().length;
                    $("#"+table_id+" #"+tbody_id).html('<tr><td colspan="'+columnCount+'" class="text-center">Loading...</td></tr>');
                }
            },
        }).done(function(res){
            $("#"+table_id+" #"+tbody_id).html('');
            $("#"+table_id+" #"+tbody_id).html(res.tbodyData);
            initSelect2();
            if(tfoot_id != ""){
                $("#"+table_id+" #"+tfoot_id).html('');
                $("#"+table_id+" #"+tfoot_id).html(res.tfootData);
            }
        });
    }

</script>