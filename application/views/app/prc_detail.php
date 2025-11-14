<?php $this->load->view('app/includes/header'); ?>
<style>

</style>
	<!-- Header -->
	<header class="header">
		<div class="main-bar">
			<div class="container">
				<div class="header-content">
					<div class="left-content">
						<a href="javascript:void(0);" class="menu-toggler me-2">
    						<!-- <i class="fa-solid fa-bars font-16"></i> -->
    						<svg class="text-dark" xmlns="http://www.w3.org/2000/svg" height="30px" viewBox="0 0 24 24" width="30px" fill="#000000"><path d="M13 14v6c0 .55.45 1 1 1h6c.55 0 1-.45 1-1v-6c0-.55-.45-1-1-1h-6c-.55 0-1 .45-1 1zm-9 7h6c.55 0 1-.45 1-1v-6c0-.55-.45-1-1-1H4c-.55 0-1 .45-1 1v6c0 .55.45 1 1 1zM3 4v6c0 .55.45 1 1 1h6c.55 0 1-.45 1-1V4c0-.55-.45-1-1-1H4c-.55 0-1 .45-1 1zm12.95-1.6L11.7 6.64c-.39.39-.39 1.02 0 1.41l4.25 4.25c.39.39 1.02.39 1.41 0l4.25-4.25c.39-.39.39-1.02 0-1.41L17.37 2.4c-.39-.39-1.03-.39-1.42 0z"></path></svg>
    					</a>
						<h6 class="title mb-0 text-nowrap"><?=$prcData->prc_number?><small> <?= date("d M Y", strtotime($prcData->prc_date)) ?></small></h6>
					</div>
					<div class="mid-content">
					</div>
					<div class="right-content">
                        <?= floatval($prcData->prc_qty) ?>NOS
					</div>
				</div>
			</div>
		</div>
	</header>
	<!-- Header -->
    <div class="container bg-light-sky">
        <div class=" order-box mb-0 mt-0" >
		    <div class="mb-0  mt-0">
    			<div class="order-content mb-0  mt-0">
    				<div class="right-content">
    					<h6 class="order-number">  <?= (!empty($prcData->item_code) ? '['.$prcData->item_code.'] '.$prcData->item_name : $prcData->item_name ) ?></h6>
    					<ul>
    					    <li> <h6 class="order-time"><?= (!empty($prcData->party_name)?$prcData->party_name:'Self') ?></h6> 	</li>
    						<li> <p class="order-name"> <?= $prcData->remark ?></p> </li>
    					</ul>
    				</div>
    			</div>
		    </div>
	    </div>
    </div>
	<div class="page-content"  id="prcBoard" style="overflow:scroll !important;height:80vh;">
		<div class="content-inner pt-0" >
			<div class="container">
				<div class=" prcProcess">
				</div>
			</div>
		</div>
	</div>
<script>
	
</script>
<?php $this->load->view('app/includes/bottom_menu'); ?>
<?php $this->load->view('app/includes/footer'); ?>
<?php $this->load->view('app/includes/sidebar'); ?>
<script src="<?=base_url()?>assets/app/js/sop.js?v=<?=time()?>"></script>


<script>
	loadPrcDetail();
	function loadPrcDetail(){
        var id = <?=$prcData->id?>;
		$.ajax({
            url: base_url  + 'app/sop/getPrcDetailHtml',
            data:{id:id},
            type: "POST",
            dataType:"json",
        }).done(function(response){
            $(".prcProcess").html(response.processDetail);                   
        });
	}
</script>