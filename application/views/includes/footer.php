
        </div>
		
		<?php
		    $footerFile = 'footerfiles';
		    if(isset($is_minFiles) AND $is_minFiles == 1){ $footerFile = 'footerfiles_min'; }
		    $this->load->view('includes/'.$footerFile);
		?>
		<?php $this->load->view('includes/modal');?>
		
	</body>
</html>