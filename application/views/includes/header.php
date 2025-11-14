<!DOCTYPE html>
<html dir="ltr" lang="en">
<?php
    $hdrFile = 'headerfiles';
    if(isset($is_minFiles) AND $is_minFiles == 1){ $hdrFile = 'headerfiles_min'; } 
    $this->load->view('includes/'.$hdrFile);
?>
	<body id="body" class="dark-sidebar">
		<div class="preloader">
			<div class="lds-ripple">
				<div class="lds-pos"></div>
				<div class="lds-pos"></div>
			</div>
		</div>
		<?php $this->load->view('includes/topbar'); ?>
		<?php $this->load->view('includes/sidebar'); ?>
    	<div class="page-wrapper">
			