<?php
    $enqList = '';
    foreach($enqData as $row){
    	$enqList .='<div href="#" class="media grid_item">
                		<div class="media-body">
                			<div class="d-inline-block">
                				<h6><a href="javascript:void(0)" type="button" class="text-primary enqNumber" data-id="'.$row->id.'" >#'.$row->trans_number.'</a></h6>
                				<p class="fs-13"><i class="fa fa-user"></i> '.$row->party_name.'</p>
                				<p class="fs-13"><i class="fa fa-bullseye"></i> '.$row->item_name.'</p>
                				<p class="text-muted"><i class="mdi mdi-clock"></i> '.formatDate($row->trans_date).'</p>
                			</div>
                			<div></div>
                		</div>
                		<div class="media-right">
							<p class="text-danger"> '.floatval($row->qty).' <small class="">'.$row->uom.'</small></p>
                		</div>
                	</div>';
    }
    echo $enqList;
?>