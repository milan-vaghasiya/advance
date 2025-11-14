<?php
    if($status == 1){
        if(!empty($issueData)){
            foreach($issueData as $row){
                ?>
                <li class=" grid_item listItem  position-static" ">
                    <div class="media-content">
                        <div>
                            <h6 class="name "><?=$row->item_name?></h6>
                            <p class="mb-0">#<?=$row->trans_number?> | <i class="far fa-clock"></i> <?=date('d, M Y', strtotime($row->trans_date))?></p>
                            <p class="mb-0">PRC NO : <?=$row->prc_number?></p>
                        </div>
                    </div>
                    <div class="left-content w-auto float-end">
                        <?=floatval($row->issue_qty)?>/<?=floatval($row->req_qty)?>
                        <div class="d-flex float-end">
                            <a class="badge badge-md badge-primary float-end rounded-sm" href="<?=base_url('app/materialIssue/issueMaterial/'.$row->id.'/'.$row->item_id)?>"><i class="fas fa-paper-plane"></i></a>
                        </div>
                    </div>
                </li>
            <?php
            }
        }
    }else{
        if(!empty($issueData)){
            foreach($issueData as $row){
                ?>
                <li class=" grid_item listItem  position-static" ">
                    <div class="media-content">
                        <div>
                            <h6 class="name "><?=$row->item_name?></h6>
                            <p class="mb-0">#<?=$row->issue_number?> | <i class="far fa-clock"></i> <?=date('d, M Y', strtotime($row->issue_date))?></p>
                            <p class="mb-0">PRC NO : <?=$row->prc_number?></p>
                        </div>
                    </div>
                    <div class="left-content w-auto float-end">
                            <?=floatval($row->issue_qty).$row->uom?>
                            <div class="d-flex float-end">
                                <?php
                                $deleteButton = "";
                                $return_qty = floatval($row->return_qty);
                                if(empty($return_qty) ){
                                    $deleteParam = "{'postData':{'id' : ".$row->id."}, 'fndelete' : 'deleteIssueRequisition','message' : 'Stock','controller':'store','res_function':'issueResponse'}";
                                    ?>
                                    <a class="badge badge-md badge-primary float-end rounded-sm" href="javascript:void(0)" onclick="trashData(<?=$deleteParam?>);" datatip="Remove" flow="down"><i class="fas fa-trash"></i></a>
                                    <?php
                                }
                                ?>
                            </div>
                    </div>
                </li>
                <?php
            }
        }

    }
	
?>