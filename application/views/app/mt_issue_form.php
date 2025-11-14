<?php $this->load->view('app/includes/header'); ?>
<link rel="stylesheet" href="<?=base_url()?>/assets/qrcode/dist/css/qrcode-reader.css">
<style>
</style>
    <!-- Header -->
	<header class="header">
		<div class="main-bar bg-primary-2">
			<div class="container">
				<div class="header-content">
					<div class="left-content">
						<a href="javascript:void(0);" class="menu-toggler me-2">
    						<!-- <i class="fa-solid fa-bars font-16"></i> -->
    						<svg class="text-dark" xmlns="http://www.w3.org/2000/svg" height="30px" viewBox="0 0 24 24" width="30px" fill="#000000"><path d="M13 14v6c0 .55.45 1 1 1h6c.55 0 1-.45 1-1v-6c0-.55-.45-1-1-1h-6c-.55 0-1 .45-1 1zm-9 7h6c.55 0 1-.45 1-1v-6c0-.55-.45-1-1-1H4c-.55 0-1 .45-1 1v6c0 .55.45 1 1 1zM3 4v6c0 .55.45 1 1 1h6c.55 0 1-.45 1-1V4c0-.55-.45-1-1-1H4c-.55 0-1 .45-1 1zm12.95-1.6L11.7 6.64c-.39.39-.39 1.02 0 1.41l4.25 4.25c.39.39 1.02.39 1.41 0l4.25-4.25c.39-.39.39-1.02 0-1.41L17.37 2.4c-.39-.39-1.03-.39-1.42 0z"></path></svg>
    					</a>
						<h5 class="title mb-0 text-nowrap"  id="desk_title">Material Issue</h5>
					</div>
					<div class="mid-content" > </div>
					<div class="right-content ">
                        <!-- <a href="#" class="font-24 "   >
                            <i class="fa fa-qrcode"></i>
                        </a> -->
                  

					</div>
				</div>
			</div>
		</div>
	</header>
	<!-- Header -->
    
    <!-- Page Content -->
    <div class="page-content"  id="issueBoard" style="overflow:scroll !important;height:80vh;">
	
        <div class="content-inner pt-0" >
			<div class="container qCode">
				<form id="issue_form" data-res_function="getIssueResponse">
                    <!-- <input type="hidden" name="item_id" id="item_id" value="<?=$item_id?>"> -->
                    <input type="hidden" name="req_id" id="req_id" value="<?=$req_id?>">
                    <input type="hidden"  id="location_id" value="">
                    <div class="row">
                        <div class="col-6">
                            <label for="prc_id">PRC No.</label>
                            <select name="prc_id" id="prc_id" class="form-control select2">
                                <option value="">Select Prc No</option>
                                <?php
                                    if(!empty($prcData)){
                                        foreach ($prcData as $row) {
                                            echo "<option value='".$row->id."' >".$row->prc_number."</option>";
                                        }
                                    }
                                ?>
                            </select>
                        </div>
                        <div class="col-6">
                            <label for="issued_to">Issued To</label>
                            <select name="issued_to" id="issued_to" class="form-control select2 req">
                                <option value="">Select Issued To</option>
                                <?php
                                    if(!empty($empData)){
                                        foreach ($empData as $row) {
                                            echo "<option value='".$row->id."'>".$row->emp_name."</option>";
                                        }
                                    }
                                ?>
                            </select>
                            <div class="error item_err"></div>
                        </div>
                    </div>
                    <div class="row mt-3">
                                <div clas="col">
                                    <label for="item_id">Item</label>
                                    <select name="item_id" id="item_id" class="form-control select2 req">
                                        <option value="">Select Item</option>
                                        <?php
                                            if(!empty($itemList)){
                                                foreach ($itemList as $row) {
                                                    $selected = (!empty($item_id) && $item_id == $row->id)?'selected':'';
                                                    echo "<option value='".$row->id."' ".$selected.">".$row->item_code.' - '.$row->item_name."</option>";
                                                }
                                            }
                                        ?>
                                    </select>
                                    <p class="mt-3 fw-bolder float-end" id="item_name"></p>
                                </div>
                            </div>
                    <div class="row mt-3">
                        <div class="col">
                            <button type="button" class="btn btn-sm btn-primary btn-save float-start" id="openreader-single3" data-qrr-target="#single3" data-qrr-audio-feedback="true">Scan Location</button><br>
                            <p class="mt-3 fw-bolder float-start" id="locationData"></p>
                        </div>
                        <div class="col">
                            <button type="button" class="btn btn-sm btn-primary btn-save float-end" id="openreader-single2" data-qrr-target="#single2" data-qrr-audio-feedback="true"  aria-label="QR outline">Scan Item</button><br> 
                        </div>
                        
                    </div>
                   
                    <div class="table-responsive mt-3">               
                        <table class="table" id="stockData" width="100%"></table>
                        <div class="error table_err"></div>
                    </div>
                    
                </form>
                <div class="footer fixed">
                    <div class="container">
                        <?php $param = "{'formId':'issue_form','fnsave':'saveIssueRequisition','controller':'store','res_function':'getIssueResponse'}"; ?>
                        <a href="javascript:void(0)" class="btn btn-primary btn-block flex-1 text-uppercase btn-save" onclick="storeData(<?=$param?>)">Confirm & Issue</a>
                    </div>
                </div>
	        </div>
		</div>
    </div>    
    <!-- Page Content End-->

<!-- <?php //$this->load->view('app/includes/bottom_menu'); ?> -->
<?php $this->load->view('app/includes/footer'); ?>
<?php $this->load->view('app/includes/sidebar'); ?>
<script src="<?=base_url()?>/assets/qrcode/dist/js/qrcode-reader.min.js?v=20190604"></script>


<script>
    $(".select2").select2();
	$(function(){
		// overriding path of JS script and audio 
		$.qrCodeReader.jsQRpath = "<?=base_url()?>/assets/qrcode/dist/js/jsQR/jsQR.min.js";
		$.qrCodeReader.beepPath = "<?=base_url()?>/assets/qrcode/dist/audio/beep.mp3";
		// read or follow qrcode depending on the content of the target input
		$("#openreader-single2").qrCodeReader({callback: function(code) {
		if (code) {
            //Check Aready scan or not
            var qrCodes = $("input[name='qrCode[]']").map(function(){return $(this).val();}).get();
            var location_id = $("#location_id").val();
            var qr = code+'~'+location_id;
            if(qrCodes.length > 0 && jQuery.inArray(qr, qrCodes) != -1) {
                Swal.fire({ icon: 'error', title:'already scan it.'});
            } else {
                $.ajax({
                    url: base_url + controller +'/getMaterialData',
                    data: {code:code,location_id:location_id},
                    type: "POST",
                    dataType:"json",
                    success:function(response){
                        if(response.status==0){
                            Swal.fire({ icon: 'error', title: response.message });
                        }else{
                            var valid =1;
                            var item_id = $("#item_id").val();
                            var splitQr = code.split('~');
                            var scan_item = splitQr[0];
                            if(item_id == "" ){ // Set First Item item
                                $("#item_id").val(scan_item);
                                $("#item_id").select2();
                            }else if(item_id != "" &&  item_id != scan_item){ // If item is diffrent
                                Swal.fire({ icon: 'error', title:'Different Items Scanned.'});
                                valid = 0;
                            }
                            if(valid){
                                // $("#item_name").html(response.item_name);
                                $("#stockData").append(response.html);
                                $(".stepper").TouchSpin();
                            }
                        }
                    }
                });
            }
		}  
		}}).off("click.qrCodeReader").on("click", function(){
            var qrcode = $("#single2").val();
            var location_id = $("#location_id").val();
            if(location_id){
                if (qrcode) {
                    window.location.href = qrcode;
                }else{
                    $.qrCodeReader.instance.open.call(this);
                }
            }else{
                Swal.fire({ icon: 'error', title: 'Scan Location first' });
            }
            
		});


        $("#openreader-single3").qrCodeReader({callback: function(code) {
            if (code) {
                $.ajax({
                    url: base_url  +'app/stockTransfer/getLocationData',
                    data: {code:code},
                    type: "POST",
                    dataType:"json",
                    success:function(response)
                    {
                        if(response.status==0){
                            Swal.fire({ icon: 'error', title: response.message });
                        }else{
                            $("#location_id").val(response.location_id);
                            $("#locationData").html(response.html);
                        }
                    }
                });
            }  
		}}).off("click.qrCodeReader").on("click", function(){
            var qrcode = $("#single3").val();
            if (qrcode) {
                window.location.href = qrcode;
            } else {
                $.qrCodeReader.instance.open.call(this);
            }
		});
	});

    $(document).ready(function(){
        $(document).on("change input",".batchQty", function(){
            var qty = parseFloat($(this).val());
            var qrCode = $(this).data('qr_code');
            var stock_qty = parseFloat($(this).data('stock_qty'));
            if(qty > stock_qty){
                $(this).val("");
                $(".batch_qty_"+qrCode).html('Stock Not Available');
            }
        
        });
        $(document).on("change","#item_id", function(){
            var item_id = $(this).val();
            $("#stockData").html("");
            if(item_id){
                $.ajax({
                    url: base_url + controller +'/getStockData',
                    data: {item_id:item_id},
                    type: "POST",
                    dataType:"json",
                    success:function(response){
                        if(response.status==0){
                            Swal.fire({ icon: 'error', title: response.message });
                        }else{
                            $("#stockData").append(response.html);
                            $(".stepper").TouchSpin();
                        }
                    }
                });
            }
        
        });
    });
    function Remove(button) {
        //Determine the reference of the Row using the Button.
        var row = $(button).closest("TR");
        var table = $("#stockData")[0];
        table.deleteRow(row[0].rowIndex);
        var qrCodes = $("input[name='qrCode[]']").map(function(){return $(this).val();}).get();
        if(qrCodes.length == 0){
            $("#item_id").val();
            $("#item_name").html("");
        }
    };
    function getIssueResponse(data){
        if(data.status==1){
            Swal.fire({
                title: "Success",
                text: data.message,
                icon: "success",
                showCancelButton: false,
                confirmButtonColor: "#3085d6",
                cancelButtonColor: "#d33",
                confirmButtonText: "Ok!"
            }).then((result) => {
               window.location.reload();
            });
            
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