<?php //$this->load->view('change_password');?>
<script>
	var base_url = '<?=base_url("admin/");?>'; 
	var controller = '<?=(isset($headData->controller)) ? $headData->controller : ''?>'; 
	var popupTitle = '<?=POPUP_TITLE;?>';

	var theads = '<?=(isset($tableHeader)) ? $tableHeader[0] : ''?>';
	var textAlign = '<?=(isset($tableHeader[1])) ? $tableHeader[1] : ''?>';
	var srnoPosition = '<?=(isset($tableHeader[2])) ? $tableHeader[2] : 1?>';
	var sortable = '<?=(isset($tableHeader[3])) ? $tableHeader[3] : ''?>';
	var tableHeaders = {'theads':theads,'textAlign':textAlign,'srnoPosition':srnoPosition,'sortable':sortable};
</script>
<div class="chat-windows"></div>

<!-- Permission Checking -->
<?php
	$script= "";
	$script .= '<script>
		var permissionRead = "1";
		var permissionWrite = "1";
		var permissionModify = "1";
		var permissionRemove = "1";
		var permissionApprove = "1";
	</script>';
	echo $script;
?>

<!-- ============================================================== -->
<!-- All Jquery -->
<!-- ============================================================== -->

<script src="<?=base_url()?>assets/js/jquery/dist/jquery.min.js"></script>
<script src="<?=base_url()?>assets/js/app.js"></script>

<!-- Data Table -->
<script src="<?=base_url()?>assets/plugins/datatables/bootstrap-datatable/js/datatables.net/jquery.dataTables.min.js"></script>
<script src="<?=base_url()?>assets/plugins/datatables/bootstrap-datatable/js/dataTables.bootstrap4.min.js"></script>
<script src="<?=base_url()?>assets/plugins/datatables/bootstrap-datatable/js/dataTables.buttons.min.js"></script>
<script src="<?=base_url()?>assets/plugins/datatables/bootstrap-datatable/js/buttons.bootstrap4.min.js"></script>
<script src="<?=base_url()?>assets/plugins/datatables/bootstrap-datatable/js/jszip.min.js"></script>
<script src="<?=base_url()?>assets/plugins/datatables/bootstrap-datatable/js/pdfmake.min.js"></script>
<script src="<?=base_url()?>assets/plugins/datatables/bootstrap-datatable/js/vfs_fonts.js"></script>
<script src="<?=base_url()?>assets/plugins/datatables/bootstrap-datatable/js/buttons.html5.min.js"></script>
<script src="<?=base_url()?>assets/plugins/datatables/bootstrap-datatable/js/buttons.print.min.js"></script>
<script src="<?=base_url()?>assets/plugins/datatables/bootstrap-datatable/js/buttons.colVis.min.js"></script>
<script src="<?=base_url()?>assets/plugins/datatables/bootstrap-datatable/js/natural.js"></script>
<script src="<?=base_url()?>assets/plugins/datatables/bootstrap-datatable/js/moment.js"></script>
<script src="<?=base_url()?>assets/plugins/datatables/bootstrap-datatable/js/dataTables.fixedHeader.min.js"></script>

<!-- Select2 js -->
<script src="<?=base_url()?>assets/js/pages/popper.js/dist/umd/popper.min.js"></script>
<script src="<?=base_url()?>assets/js/pages/bootstrap/dist/js/bootstrap.min.js"></script>
<script src="<?=base_url()?>assets/js/pages/multiselect/js/bootstrap-multiselect.js"></script>
<script src="<?=base_url()?>assets/plugins/select2/js/select2.full.min.js"></script>
<script src="<?=base_url()?>assets/plugins/sweet-alert2/sweetalert2.min.js"></script>

<!-- overlayScrollbars -->
<script src="<?=base_url("assets/plugins/overlayScrollbars/js/jquery.overlayScrollbars.min.js")?>"></script>

<!-- Custom Scripts -->
<script src="<?=base_url()?>assets/js/custom/comman-js.js?v=<?=time()?>"></script>
<script src="<?=base_url()?>assets/js/custom/custom_ajax.js?v=<?=time()?>"></script>
<script src="<?=base_url()?>assets/js/custom/typehead.js?v=<?=time()?>"></script>
<script src="<?=base_url()?>assets/extra-libs/jquery-ui/jquery-ui.min.js?v=<?=time()?>"></script>

<div class="ajaxModal"></div>
<div class="centerImg">
	<img src="<?=base_url()?>assets/images/logo.png" style="width:85%;height:auto;"><br>
	<img src="<?=base_url()?>assets/images/ajaxLoading.gif" style="margin-top:-25px;">
</div>