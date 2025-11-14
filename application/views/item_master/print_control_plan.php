<html>
    <head>
        <title>Inspection Print</title>
        <link rel="icon" type="image/png" sizes="16x16" href="<?=base_url();?>assets/images/favicon.png">
    </head>
    <body>
        <div class="row">
            <div class="col-12">
				<table>
					<tr>
						<td>
							<img src="<?=$letter_head?>" class="img">
						</td>
					</tr>
				</table>
				<table class="table bg-light-grey">
					<tr class="" style="letter-spacing: 2px;font-weight:bold;padding:2px !important; border-bottom:1px solid #000000;">
						<td class="fs-18 text-center"><?=$itemName->item_code.' - '.$itemName->item_name?></td>
					</tr>
				</table> 
				
                <?php              
                $processList = array_reduce($paramData, function($processList, $process) { $processList[$process->process_name][] = $process; return $processList; }, []);
				
				foreach ($processList as $process_name=>$processes):
				?>
				<hr>
				<table class="table item-list-bb">
					<thead>
						<tr class="bg-light">
							<th colspan="5" class="text-left"><?=!empty($process_name)?$process_name:'Control Plan'?> : </th>
						</tr>
						<tr style="background:#f9fafb">
							<th style="width:5%">#</th>
							<th style="width:10%">Parameter</th>
							<th style="width:10%">Specification</th>
							<th style="width:10%">Instrument</th>
							<th style="width:10%">Control Method</th>
						</tr>
					</thead>
					<tbody>
						<?php
						$i = 1;
						foreach($processes AS $row):
							?>
							<tr class="text-center">
								<td><?=$i++?></td>
								<td><?=$row->parameter?></td>
								<td><?=$row->specification?></td>
								<td><?=$row->instrument?></td>
								<td><?=$row->control_method?></td>
							</tr>
							<?php
						endforeach;
						?>
					</tbody>
				</table>
				<?php
				endforeach; 
				?>
            </div>
        </div>        
    </body>
</html>
