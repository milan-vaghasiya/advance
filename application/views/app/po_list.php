<?php
	$poDetail ='';
	if(!empty($poData))
	{
		foreach($poData as $row)
		{
			$approveParam = "{'postData':{'id':".$row->id.",'is_approve': 1,'msg':'Approved'},'message':'Are you sure want to Approve this Purchase Order?','fnsave':'approvePurchaseOrder','res_function':'getOrderResponse'}";
			$poDetail .= '<li class=" grid_item listItem item transition position-static data-category="transition">
								<div class="btn btn-icon btn-primary">
									<a href="" class="swipe-container"><i class="fa fa-user text-white"></i></a>
								</div>
								<div class="media-content">
									<div>
										<h6>'.$row->party_name.'</h6>
										<p class="mb-0">'.$row->trans_number.'| <i class="far fa-clock"></i>'.date('d, M Y', strtotime($row->trans_date)).'</p>
										<p class="mb-0">'.$row->item_name.'</p>
										<p class="time mb-0">'.$row->qty.'</p>
									</div>
								</div>
								<div class="left-content w-auto">
									<div class="d-flex mt-2">
										<a href="javascript:void(0)" class="float-end font-20" onclick="confirmStore('.$approveParam.');" flow="down"><i class="fa fa-check"></i></a>
									</div>
									<div class="d-flex mt-2">
										<a class="float-end font-20" href="'.base_url('purchaseOrders/printPO/'.$row->id).'" target="_blank"><i class="fa fa-print"></i></a>
									</div>
								</div>
								
						</li>';
		}
	}
	echo $poDetail;
?>