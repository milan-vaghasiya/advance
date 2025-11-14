<?php $this->load->view('app/includes/header'); ?>
<link rel="stylesheet" href="<?=base_url()?>/assets/qrcode/dist/css/qrcode-reader.css">
<style>
.dropdown-toggle::after{display:none;}
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
						<h5 class="title mb-0 text-nowrap"  id="desk_title">Pending Store Allocation</h5>
					</div>
					<div class="mid-content" > </div>
					<div class="right-content ">
						 <!-- <div class="basic-dropdown">
							<div class="dropdown">
								<a type="button" class=" dropdown-toggle show font-20" data-bs-toggle="dropdown" aria-expanded="true">
									<i class="fas fa-ellipsis-v"></i>
								</a>
								<div class="dropdown-menu" data-popper-placement="bottom-start" style="position: absolute; inset: 0px auto auto 0px; margin: 0px; transform: translate(0px, 48px);">
									<a type="button" class="text-danger stageFilter  dropdown-item active" data-status="1"  data-postdata='{"status":"1","stage":"Requisions"}'>Requisions</a>
                                    <a type="button" class="text-danger stageFilter  dropdown-item" data-status="2"  data-postdata='{"status":"2","stage":"Issued Material"}'>Issued Material</a>
								</div>
							</div>
						</div> -->
					</div>
				</div>
			</div>
		</div>
	</header>
	<!-- Header -->
    
    <!-- Page Content -->
    <div class="page-content"  id="issueBoard" style="overflow:scroll !important;height:80vh;">
	
        <div class="content-inner pt-0" >
			<div class="container">
				<input type="text" class="form-control quicksearch qs1" placeholder="Search Here ..." />
                <div class="dz-tab style-4">
					<div class="list-grid">
						<ul id="issueContainer" class="dz-list message-list issueData accordion style-2"></ul>
					</div>
				</div> 
                <div class="review-box" >
					<a  href="<?=base_url("app/stockTransfer/stockTransfer")?>" class="add-btn  permission-write" style="margin-bottom:85px">
						<i class="fa-solid fa-plus"></i>
					</a>
				</div>
			</div>    
		</div>
    </div>    
    <!-- Page Content End-->

<input type="hidden" id="next_page" value="0" />
<a href="#" class="next_page" type="button" data-next_page="0" ></a> 
<input type="hidden" id="status" value="1">
<?php $this->load->view('app/includes/bottom_menu'); ?>
<?php $this->load->view('app/includes/footer'); ?>
<?php $this->load->view('app/includes/sidebar'); ?>
<script src="<?=base_url()?>/assets/qrcode/dist/js/qrcode-reader.min.js?v=20190604"></script>
<script>
$(document).ready(function(){
	
	var rec_per_page = "<?=$rec_per_page?>";
	setTimeout(function(){ $(".quicksearch").trigger("keyup"); }, 50);	
	$(document).on('click','.stageFilter',function(){
		console.log($(this).data('postdata') );
        var postdata = $(this).data('postdata') || {status:1,stage:'New'};
		$("#desk_title").html(postdata.stage);
		$("#status").val(postdata.status);
		var np = parseFloat($('#next_page').val()) || 0;
		postdata.start = 0;
		postdata.length = parseFloat(rec_per_page);
		postdata.page = 0;
		loadHtmlData({'fnget':'getMaterialTransferData','rescls':'issueData','postdata':postdata});
	});

	$('.quicksearch').keyup(delay(function (e) {
			e.preventDefault();
			$('#next_page').val('0');
			var postdata = {};
			postdata.status = $("#status").val();

			delete postdata.page;delete postdata.start;delete postdata.length;
			postdata.limit = parseFloat(rec_per_page);
			postdata.skey = $(this).val();
			loadHtmlData({'fnget':'getMaterialTransferData','rescls':'issueData','postdata':postdata});
	}));

	
	const scrollEle = $('#issueBoard');
	var ScrollDebounce = true;
	$(scrollEle).scroll(function() {
		
		if($(this).scrollTop() + $(this).innerHeight() >= ($(this)[0].scrollHeight - 10)) {
			if(ScrollDebounce){
				ScrollDebounce = false;
				var status = $("#status").val();
				var postdata = {status:status} || {};
				var np = parseFloat($('#next_page').val()) || 0;
				postdata.start = np * parseFloat(rec_per_page);
				postdata.length = rec_per_page;
				postdata.page = np;
				console.log(postdata);
				loadHtmlData({'fnget':'getMaterialTransferData','rescls':'issueData','postdata':postdata,'scroll_type':1});
				setTimeout(function () { ScrollDebounce = true; }, 500);		
			}
		}
	});
	

});

function issueResponse(data){
	if(data.status==1){	
		Swal.fire( 'Success', data.message, 'success' );
		$(".quicksearch").trigger("keyup");
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