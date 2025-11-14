<?php $this->load->view('app/includes/header'); ?>
<link rel="stylesheet" href="<?=base_url()?>/assets/qrcode/dist/css/qrcode-reader.css">
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
                    <h5 class="title mb-0 text-nowrap"  id="desk_title">Scan Sop</h5>
                </div>
                <div class="mid-content" > </div>
                <div class="right-content ">
                    <a href="#" class="font-24 "   id="openreader-single2" data-qrr-target="#single2" data-qrr-audio-feedback="true"  aria-label="QR outline">
                        <i class="fa fa-qrcode"></i>
                    </a>
                

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
                <!-- <div class="row mt-3">
                    <div class="col">
                        <button type="button" class="btn btn-sm btn-primary btn-save float-end" id="openreader-single2" data-qrr-target="#single2" data-qrr-audio-feedback="true"  aria-label="QR outline">Scan Item</button><br>
                    </div>
                    
                </div>
                -->
                <input type="hidden" id="code">
                <div class="table-responsive mt-1" id="prcDetail">
                    
                    <div class="error table_err"></div>
                </div>
                <div class="row mt-3" id="btnDiv">
                    <div class="col">
                        <button type="button" class="btn btn-sm btn-primary btn-save float-start" id="openreader-single3" data-qrr-target="#single3" data-qrr-audio-feedback="true">Scan Location</button><br>
                        <p class="mt-3 fw-bolder float-start" id="locationData"></p>
                        <input type="hidden"  id="location_id" value="">
                    </div>
                    <div class="col">
                        <button type="button" class="btn btn-sm btn-primary btn-save float-end" id="openreader-single2" data-qrr-target="#single2" data-qrr-audio-feedback="true"  aria-label="QR outline">Scan Item</button><br>
                        <p class="mt-3 fw-bolder float-end" id="item_name"></p>
                    </div>
                    
                </div>
            </form>
            <div class="footer fixed">
                <div class="container">
                    <?php //$param = "{'formId':'issue_form','fnsave':'saveStockTransfer','controller':'app/stockTransfer','res_function':'getIssueResponse'}"; ?>
                    <!-- <a href="javascript:void(0)" class="btn btn-primary btn-block flex-1 text-uppercase btn-save" onclick="storeData(<?=$param?>)">Verify & Transfer</a> -->
                </div>
            </div>
        </div>
    </div>
</div>    
<!-- Page Content End-->
<?php $this->load->view('app/includes/footer'); ?>
<?php $this->load->view('app/includes/sidebar'); ?>
<script src="<?=base_url()?>assets/app/js/sop.js?v=<?=time()?>"></script>

<script src="<?=base_url()?>/assets/qrcode/dist/js/qrcode-reader.min.js?v=20190604"></script>


<script>
    $("#btnDiv").hide();
    $(".select2").select2();
	$(function(){
		// overriding path of JS script and audio 
		$.qrCodeReader.jsQRpath = "<?=base_url()?>/assets/qrcode/dist/js/jsQR/jsQR.min.js";
		$.qrCodeReader.beepPath = "<?=base_url()?>/assets/qrcode/dist/audio/beep.mp3";
		// read or follow qrcode depending on the content of the target input
		$("#openreader-single2").qrCodeReader({callback: function(code) {
            
		if (code) {
            $("#code").val(code);
            loadPrcDetail(code);
		}  
		}}).off("click.qrCodeReader").on("click", function(){
            var qrcode = $("#single2").val();
            if (qrcode) {
                    window.location.href = qrcode;
                }else{
                    $.qrCodeReader.instance.open.call(this);
                }
		});

	});
  
    function loadPrcDetail(code = ""){
        var code = code || $("#code").val();
        $.ajax({
            url: base_url + controller +'/getProcessDetail',
            data: {code:code},
            type: "POST",
            dataType:"json",
            success:function(response){
                if(response.status==0){
                    Swal.fire({ icon: 'error', title: response.message });
                }else{
                    $("#prcDetail").html(response.html);
                }
            }
        });
    }
</script>