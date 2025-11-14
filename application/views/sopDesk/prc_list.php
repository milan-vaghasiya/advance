<?php
    $prcList = '';
    $partyName = (!empty($row->party_name) ? '<p class="fs-13"><i class="fas fa-user"></i> '.$row->party_name.'</p>' : '');
    foreach($prcData as $row){
		$btn = "";
		$mtParam = "{'postData':{'id' : ".$row->id.",'prc_qty' : ".$row->prc_qty.",'item_id':".$row->item_id."},'modal_id' : 'bs-right-lg-modal', 'form_id' : 'prcMaterial', 'title' : 'Material Request For : ".$row->prc_number."', 'fnsave' : 'savePrcMaterial', 'js_store_fn' : 'storeSop','call_function':'requiredMaterial'}";
		$btn .= ' <a class="dropdown-item" href="javascript:void(0)" datatip="Material Request" flow="down" onclick="modalAction('.$mtParam.')"><i class="far fa-paper-plane text-muted font-12"></i> Material Request</a>';
		if($row->status == 1 ){
			$startParam = "{'postData':{'id' : ".$row->id."},'message' : 'Are you sure you want to start PRC ? once you start you can not edit or delete','fnsave':'startPRC','res_function':'getPrcResponse'}";
			$btn .= ' <a class="dropdown-item" href="javascript:void(0)" datatip="Start" flow="down" onclick="confirmSOPStore('.$startParam.')"><i class="mdi mdi-play-circle-outline text-muted"></i> Start</a>';

			$editParam = "{'postData':{'id' : ".$row->id."},'modal_id' : 'bs-right-lg-modal', 'form_id' : 'editPrc', 'title' : 'Update PRC', 'fnsave' : 'savePRC', 'js_store_fn' : 'storeSop'}";
			$btn .= ' <a class="dropdown-item" href="javascript:void(0)" datatip="Edit" flow="down" onclick="modalAction('.$editParam.')"><i class="mdi mdi-square-edit-outline text-muted"></i> Edit</a>';

			$deleteParam = "{'postData':{'id' : ".$row->id."},'message' : 'PRC','res_function':'getPrcResponse'}";
			$btn .= ' <a class="dropdown-item" href="javascript:void(0)" datatip="Delete" flow="down" onclick="trashSop('.$deleteParam.')"><i class="mdi mdi-trash-can-outline text-muted"></i> Delete</a>';
		}
		

    	$prcList .='<div href="#" class="media grid_item">
                		<div class="media-body">
                			<div class="d-inline-block">
                				<h6><a href="javascript:void(0)" type="button" class="text-primary prcNumber" data-id="'.$row->id.'" >#'.$row->prc_number.'</a></h6>
                				<p class="text-muted"><i class="mdi mdi-clock"></i> '.$row->prc_date.' | '.$row->mfg_type.'</p>
                				<p class="fs-13"><i class="fa fa-bullseye"></i> '.$row->item_name.'</p>
                				'.$partyName.'
                			</div>
                			<div></div>
                		</div>
                		<div class="media-right">
                			<a class="dropdown-toggle lead-action" data-bs-toggle="dropdown" href="#" role="button"><i class="mdi mdi-chevron-down fs-3"></i></a>
                			<div class="dropdown-menu">
                			   '.$btn.'
                			</div><br>
                			<p class="text-danger"> '.floatval($row->prc_qty).' <small class="">'.$row->uom.'</small></p>
                		</div>
                	</div>';
    }
    echo $prcList;
?>