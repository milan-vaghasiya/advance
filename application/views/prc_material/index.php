<?php $this->load->view('includes/header'); ?>
<div class="page-content-tab">
	<div class="container-fluid">
		<div class="row">
			<div class="col-sm-12">
				<div class="page-title-box">
					<div class="float-end">
                        <?php
                            $issueParam = "{'modal_id' : 'bs-right-lg-modal', 'call_function':'addPrcMaterial', 'form_id' : 'addPrcMaterial', 'title' : 'Issue Material'}";
                        ?>
						<button type="button" class="btn btn-outline-dark btn-sm float-right permission-write press-add-btn" onclick="modalAction(<?=$issueParam?>);" ><i class="fa fa-plus"></i> Issue Material</button>

                       
					</div>
					<h4 class="page-title">PRC Material Issue</h4>
				</div>
			</div>
		</div>
        <div class="row">
            <div class="col-12">
                <div class="card">
                    <div class="card-body">
                        <div class="table-responsive">
                            <table id='materialTable' class="table table-bordered ssTable ssTable-cf" data-url='/getDTRows/'></table>
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
        $(document).on('change','#prc_id',function(){
            var item_id = $("#prc_id :selected").data("item_id");
            $("#bom_group").html("");
            if(item_id){
                $.ajax({
                    url:base_url + controller + "/getBomGroupList",
                    type:'post',
                    data:{item_id:item_id},
                    dataType:'json',
                    success:function(data){
                        $("#bom_group").html(data.groupOptions);   
                    }
                });
            }
            $("#bom_group").select2();
            
        });
        $(document).on('change','#bom_group',function(){
            var bom_group = $("#bom_group").val();
            var item_id = $("#prc_id :selected").data("item_id");
            var prc_id = $("#prc_id").val();
            $("#item_id").html("");
            if(bom_group){
                $.ajax({
                    url:base_url + controller + "/getBomList",
                    type:'post',
                    data:{bom_group:bom_group,item_id:item_id,prc_id:prc_id},
                    dataType:'json',
                    success:function(data){
                        $("#item_id").html(data.bomOptions);
                    }
                });
            }
            $("#item_id").select2();
            $("#item_id").trigger("click");
        });

        $(document).on('change','#item_id',function(){
            var item_id = $("#item_id").val();
            var item_type = $("#item_id :selected").data("item_type") || 0;
            $("#item_type").val(item_type);
            $("#stockTbody").html("");
            if(item_id){
                $.ajax({
                    url:base_url + controller + "/getBatchWiseStock",
                    type:'post',
                    data:{item_id:item_id},
                    dataType:'json',
                    success:function(data){
                        $("#stockTbody").html(data.tbodyData);
                    }
                });
            }
            
        });
    });
</script>