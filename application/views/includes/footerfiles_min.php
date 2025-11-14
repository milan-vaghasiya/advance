<input type="hidden" id="next_page" value="0" /><a href="#" class="next_page" type="button" data-next_page="0" ></a>
<?php $this->load->view('change_password');?>
<script>
	var base_url = '<?=base_url();?>'; 
	var controller = '<?=(isset($headData->controller)) ? $headData->controller : ''?>'; 
	var popupTitle = '<?=POPUP_TITLE;?>';

	var startYearDate = '<?=$this->startYearDate?>';
	var endYearDate = '<?=$this->endYearDate?>';
</script>
<div class="chat-windows"></div>

<!-- Permission Checking -->
<?php
	$script= "";
	if($permission = $this->session->userdata('emp_permission')):
		if(!empty($headData->pageUrl)):
			$empPermission = $permission[$headData->pageUrl];
			$script .= '<script>
				var permissionRead = "'.$empPermission['is_read'].'";
				var permissionWrite = "'.$empPermission['is_write'].'";
				var permissionModify = "'.$empPermission['is_modify'].'";
				var permissionRemove = "'.$empPermission['is_remove'].'";
				var permissionApprove = "'.$empPermission['is_approve'].'";
			</script>';
			echo $script;
		else:
			$script .= '<script>
				var permissionRead = "1";
				var permissionWrite = "1";
				var permissionModify = "1";
				var permissionRemove = "1";
				var permissionApprove = "1";
			</script>';
			echo $script;
		endif;
	else:
		$script .= '<script>
				var permissionRead = "";
				var permissionWrite = "";
				var permissionModify = "";
				var permissionRemove = "";
				var permissionApprove = "";
			</script>';
		echo $script;
	endif;
?>

<!-- ============================================================== -->
<!-- All Jquery -->
<!-- ============================================================== -->

<script src="<?=base_url()?>assets/js/jquery/dist/jquery.min.js"></script>
<script src="<?=base_url()?>assets/js/app.js"></script>

<!-- Select2 js -->
<script src="<?=base_url()?>assets/plugins/select2/js/select2.full.min.js"></script>
<script src="<?=base_url()?>assets/js/pages/multiselect/js/bootstrap-multiselect.js"></script>
<script src="<?=base_url()?>assets/plugins/sweet-alert2/sweetalert2.min.js"></script>

<!-- Custom Scripts -->
<script src="<?=base_url()?>assets/js/custom/comman-js.js?v=<?=time()?>"></script>

<div class="ajaxModal"></div>
<div class="centerImg">
	<img src="<?=base_url()?>assets/images/logo.png" style="width:85%;height:auto;"><br>
	<img src="<?=base_url()?>assets/images/ajaxLoading.gif" style="margin-top:-25px;">
</div>