<?php $this->load->view('app/includes/header'); ?>
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
    						<svg class="text-dark" xmlns="http://www.w3.org/2000/svg" height="30px" viewBox="0 0 24 24" width="30px" fill="#000000"><path d="M13 14v6c0 .55.45 1 1 1h6c.55 0 1-.45 1-1v-6c0-.55-.45-1-1-1h-6c-.55 0-1 .45-1 1zm-9 7h6c.55 0 1-.45 1-1v-6c0-.55-.45-1-1-1H4c-.55 0-1 .45-1 1v6c0 .55.45 1 1 1zM3 4v6c0 .55.45 1 1 1h6c.55 0 1-.45 1-1V4c0-.55-.45-1-1-1H4c-.55 0-1 .45-1 1zm12.95-1.6L11.7 6.64c-.39.39-.39 1.02 0 1.41l4.25 4.25c.39.39 1.02.39 1.41 0l4.25-4.25c.39-.39.39-1.02 0-1.41L17.37 2.4c-.39-.39-1.03-.39-1.42 0z"></path></svg>
    					</a>
						<h5 class="title mb-0 text-nowrap">Purchase Order</h5>
					</div>
					<div class="right-content ">
					</div>
				</div>
			</div>
		</div>
	</header>
	<!-- Header -->
    
    <!-- Page Content -->
    <div class="page-content" id="poBoard" style="overflow:scroll !important;height:80vh;">
	
        <div class="content-inner pt-0" >
			<div class="container">
				<input type="text" class="form-control quicksearch qs1" placeholder="Search Here ..." />
                <div class="dz-tab style-4">
					<div class="list-grid">
						<ul id="leadContainer" class="dz-list message-list poData accordion style-2"></ul>
					</div>
				</div> 
			</div>    
		</div>
    </div>    
    <!-- Page Content End-->
</div> 
<div class="offcanvas offcanvas-bottom" tabindex="-1" id="offcanvas_bottom" aria-modal="true" role="dialog">
	<div class="offcanvas-body small mb-3">
		
	</div>
</div>
<input type="hidden" id="next_page" value="0" />
<a href="#" class="next_page" type="button" data-next_page="0" ></a> 
<?php $this->load->view('app/includes/bottom_menu'); ?>
<?php $this->load->view('app/includes/footer'); ?>
<?php $this->load->view('app/includes/sidebar'); ?>
<script>
$(document).ready(function(){
	
	var rec_per_page = "<?=$rec_per_page?>";
	setTimeout(function(){ $(".quicksearch").trigger("keyup"); }, 50);	
	$('.quicksearch').keyup(delay(function (e) {
			e.preventDefault();
			$('#next_page').val('0');
			var postdata = {};
			delete postdata.page;delete postdata.start;delete postdata.length;
			postdata.limit = parseFloat(rec_per_page);
			postdata.skey = $(this).val();
			loadHtmlData({'fnget':'getPurchaseOrderData','rescls':'poData','postdata':postdata});
	}));

	const scrollEle = $('#poBoard');
	var ScrollDebounce = true;
	$(scrollEle).scroll(function() {
		
		if($(this).scrollTop() + $(this).innerHeight() >= ($(this)[0].scrollHeight - 10)) {
			if(ScrollDebounce){
				ScrollDebounce = false;
				var np = parseFloat($('#next_page').val()) || 0;
				postdata.start = np * parseFloat(rec_per_page);
				postdata.length = rec_per_page;
				postdata.page = np;
				console.log(postdata);
				loadHtmlData({'fnget':'getPurchaseOrderData','rescls':'poData','postdata':postdata,'scroll_type':1});
				setTimeout(function () { ScrollDebounce = true; }, 500);		
			}
		}
	});
	
	$(document).on("click", "ul.poData li span.delete", function () {alert("delete");});
	$(document).on("click", "ul.poData li span.flag", function () {alert("flag");});
	$(document).on("click", "ul.poData li span.more", function () {alert("nothing");});
	
});
function getOrderResponse(data){ 
    if(data.status == 1){
		Swal.fire({ icon: 'success', title: data.message});
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