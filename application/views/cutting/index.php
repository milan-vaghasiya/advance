<?php $this->load->view('includes/header'); ?>
<style>li.nav-item{padding:0px 3px;}.process-tags span{font-size:0.75rem;color:#000;box-shadow: 0px 1px 1px rgba(9, 30, 66, 0.25), 0px 0px 1px 1px rgba(9, 30, 66, 0.13);}.prcList{padding:0.4rem;}</style>

<div class="page-content-tab">
	<div class="container-fluid">
        <div class="row">
            <div class="col-sm-12">
				<div class="page-title-box">
                    <div class="float-start">
					    <ul class="nav nav-pills">
                            <li class="nav-item">
                                <button onclick="statusTab('cuttingTable','1');" class="nav-tab btn waves-effect waves-light btn-outline-danger active" style="outline:0px" data-bs-toggle="tab" aria-expanded="false">Pending</button>
                            </li>
                            <li class="nav-item">
                                <button onclick="statusTab('cuttingTable','2');" class="nav-tab btn waves-effect waves-light btn-outline-success"  style="outline:0px" data-bs-toggle="tab" aria-expanded="false">Inprogress</button>
                            </li>
                            <li class="nav-item">
                                <button onclick="statusTab('cuttingTable','3');" class="nav-tab btn waves-effect waves-light btn-outline-success" style="outline:0px" data-bs-toggle="tab" aria-expanded="false">Completed</button>
                            </li>
                            <li class="nav-item">
                                <button onclick="statusTab('cuttingTable','5');" class="nav-tab btn waves-effect waves-light btn-outline-danger" style="outline:0px" data-bs-toggle="tab" aria-expanded="false">Closed</button>
                            </li>
                        </ul>
					</div>
					<div class="float-end">
                        <?php
                            $addParam = "{'modal_id' : 'bs-right-lg-modal', 'call_function':'addCuttingPRC', 'form_id' : 'addCuttingPRC', 'title' : 'Cutting PRC','fnsave':'saveCutting'}";
                        ?>
                        <button type="button" class="btn waves-effect waves-light btn-outline-dark permission-write float-right press-add-btn" onclick="modalAction(<?=$addParam?>);"><i class="fa fa-plus"></i> Add Cutting PRC</button>
					</div>
                    <h4 class="card-title text-center"></h4>
				</div>
            </div>
		</div>
        <div class="row">
            <div class="col-12">
				<div class="col-12">
					<div class="card">
                        <div class="card-body">
                            <div class="table-responsive">
                                <table id='cuttingTable' class="table table-bordered ssTable ssTable-cf" data-url='/getDTRows'></table>
                            </div>
                        </div>
					</div>
				</div>
            </div>
        </div>
    </div>
</div>
<?php $this->load->view('includes/footer'); ?>

<script>
    $(document).ready(function(){
        $(document).on('change','#party_id',function(){
            var party_id = $(this).val();
            
            $.ajax({
                url:base_url + controller + "/getProductList",
                type:'post',
                data:{party_id:party_id},
                dataType:'json',
                success:function(data){
                    $("#item_id").html("");
                    $("#item_id").html(data.options);
                }
            });
        });
    });
    function getCuttingLogHtml(data){
        var postData = data.postData || {};
        var fnget = data.fnget || "";
        var controllerName = data.controller || controller;

        var table_id = data.table_id || "";
        var thead_id = data.thead_id || "";
        var tbody_id = data.tbody_id || "";
        var tfoot_id = data.tfoot_id || "";	

        if(thead_id != ""){
            $("#"+table_id+" #"+thead_id).html(data.thead);
        }
        
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
                $("#total_prod_qty").html("Production Qty : "+res.production_qty);
                initTable();
                
                initSelect2();
                if(tfoot_id != ""){
                    $("#"+table_id+" #"+tfoot_id).html('');
                    $("#"+table_id+" #"+tfoot_id).html(res.tfootData);
                }
        });
    }

    function resItemDetail(response = ""){
        var so_trans_id  = $("#item_id").find(":selected").data("so_trans_id");
        $('#so_trans_id').val(so_trans_id);
        if(response != ""){
            var itemDetail = response.data.itemDetail;
            $("#cut_weight").val(itemDetail.cut_weight);
        }else{
            $("#cut_weight").val("");
        }
    }
   
</script>