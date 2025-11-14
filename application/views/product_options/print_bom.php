<html>
    <head>
        <title>Product Option</title>
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
						<td class="fs-18 text-center"><?=$itemName->item_name?></td>
					</tr>
				</table>               
                <h4>BOM Details :- </h4>
                <table class="table item-list-bb" style="margin-top:10px;">
					<thead>
						<tr>
							<th style="width:40px;">No.</th>
							<th>Group</th>
							<th>Process</th>
							<th>Item</th>
							<th style="width:80px;">Per Piece Consumption</th>
						</tr>
					</thead>
					<tbody>
						<?php
							$i=1;
							if(!empty($itemData)):
								foreach($itemData as $row):
                                    echo  '<tr>
                                    <td class="text-center">'.$i++.'</td>
                                    <td class="text-center">'.$row->group_name.'</td>
                                    <td class="text-center">'.$row->process_name.'</td>
                                    <td class="text-center">'.$row->item_name.'</td>
                                    <td class="text-center">'.$row->qty.'</td>
								 </tr>';
                                endforeach;
                            endif;
						?>
					</tbody>
                </table>
                <h4>Process Details :- </h4>
                <table class="table item-list-bb" style="margin-top:10px;">
					<thead>
						<tr>
							<th style="width:40px;">No.</th>
							<th style="width:100px;">Process Name</th>
							<th style="width:75px;">Cycle Time</th>
							<th style="width:90px;">Finish Weight</th>
						</tr>
					</thead>
					<tbody>
						<?php
							$i=1;
							if(!empty($processData)):
								foreach($processData as $row):
                                    echo  '<tr>
                                    <td class="text-center">'.$i++.'</td>
                                    <td class="text-center">'.$row->process_name.'</td>
                                    <td class="text-center">'.$row->cycle_time.'</td>
                                    <td class="text-center">'.$row->finish_wt.'</td>
								 </tr>';
                                endforeach;
                            endif;
						?>
					</tbody>
                </table>
            </div>
        </div>        
    </body>
</html>
