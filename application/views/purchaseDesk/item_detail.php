<?php
    $itemDetail = '<img src="'.base_url('assets/images/background/dnf_1.png').'" style="width:100%;">
				    <h3 class="text-danger text-center font-24 fw-bold line-height-lg">Sorry!<br><span class="text-dark">Data Not Found</span></h3>
				    <div class="text-center text-muted font-16 fw-bold pt-3 pb-1">Pleasae click any <strong>ENQUIRY</strong> to see Data</div>';
    
    if(!empty($itemOrdData))
    {
        $itemDetail="";
        foreach($itemOrdData as $row){   
            
            $itemDetail .= '<div class=" grid_item" style="width:100%;">
                                <div class="card">
                                    <div class="card-body">                                    
                                        <div class="task-box">
                                            <div class="task-priority-icon"><i class="fas fa-circle text-primary" style="border: 5px solid #e9edf2;"></i></div>
                                            <div class="float-end">
                                                <span class="badge badge-soft-pink fw-semibold ms-2 v-super"><i class="far fa-fw fa-clock"></i> '.formatDate($row->trans_date,"d M Y").'</span>
                                                <h6 class="text-right" > '.floatval($row->qty).' '.$row->uom.'    </h6>
                                            </div>
                                            <h5 class="mt-0 mb-0 fs-15 cursor-pointer" >'.$row->party_name.'</h5>
                                            <p class="mt-0 text-muted mb-0 font-13"> #'.$row->trans_number.'</p>
                                            <p class="text-muted mb-0 font-13"><span class="fw-semibold">Price :</span> '.floatval($row->price).'</p>                    
                                                                        
                                        </div>
                                    </div>
                                </div>
                            </div>';
        }
    }
    
    echo $itemDetail;

?>