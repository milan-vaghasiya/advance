<?php
	$leadDetail ='';
	if(!empty($prcList))
	{
		foreach($prcList as $row)
		{
			$partyName = (!empty($row->party_name) ? '<p class="fs-13"><i class="fas fa-user"></i> '.$row->party_name.'</p>' : '');
			?>
			<li class=" grid_item listItem item transition position-static ">
								
				<div class="btn btn-icon btn-primary">
					<a href="<?=base_url("app/sop/prcDetail/".$row->id)?>" class="swipe-container"><i class="fas fa-cog text-white"></i></a>
				</div>
				<div class="media-content">
					
						<div>
							<h6 class="name "><?=$row->prc_number?></h6>
							<p class="mb-0"><i class="fas fa-clock"></i> <?=$row->prc_date.' | '.$row->mfg_type?></p>
							<p class="mb-0"><?=$row->item_name?></p>
							<p class="time mb-0"><?=$partyName?></p>
						</div>
					
				</div>
				<div class="left-content w-auto">
						<div class="d-flex mt-2">
							<p class="text-danger"><?=floatval($row->prc_qty)?> <small class=""><?=$row->uom?></small></p>
						</div>
				</div>
			
		</li>
			<?php
		}
	}
?>