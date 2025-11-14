<?php
   if(!empty($issueData)){
        foreach($issueData as $row){
            ?>
            <li class=" grid_item listItem  position-static" ">
                <div class="media-content">
                    <div>
                        <h6 class="name "><?=$row->item_name?></h6>
                        <p class="mb-0"><?=date("d M y",strtotime($row->ref_date))?></p>
                        <p class="mb-0">Batch No : <?=$row->batch_no?></p>
                    </div>
                </div>
                <div class="left-content w-auto float-end">
                        <?=floatval($row->qty).$row->uom?>
                        <div class="d-flex float-end">
                            <?php
                            $locationParam = "{'postData':{'id' : ".$row->id."},'modal_id' : 'modalLong', 'call_function':'addLocation', 'form_id' : 'addLocation', 'title' : 'Add Location', 'js_store_fn' : 'storeData', 'fnsave' : 'saveTransferedLocation'}";
                            $location = '<a href="javascript:void(0)" onclick="loadform('.$locationParam.')" class="badge badge-md badge-primary float-end rounded-sm" datatip="Add Log" flow="down">Add Location</a>';
                            echo $location;
                            ?>
                        </div>
                </div>
            </li>
            <?php
        }
    }
	
?>