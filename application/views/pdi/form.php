<style>li.nav-item{padding:0px 3px;}.process-tags span{font-size:0.75rem;color:#000;box-shadow: 0px 1px 1px rgba(9, 30, 66, 0.25), 0px 0px 1px 1px rgba(9, 30, 66, 0.13);}.prcList{padding:0.4rem;}</style>

<form>
    <div class="card">
        <div class="media align-items-center btn-group process-tags">
            <span class="badge bg-light-peach btn flex-fill">Item : <?=$invData->item_name?></span>
            <span class="badge bg-light-cream btn flex-fill">Inv Qty :  <?=floatval($invData->qty)?></span>
        </div>                                       
    </div>
    <input type="hidden" name="so_trans_id" value="<?=$invData->id?>">
    <input type="hidden" name="so_id" value="<?=$invData->trans_main_id?>">
    <div class="row">
        <div class="col-md-12">
            <div class="error general_error"></div>
            <div class="table-responsive">
                <table class="table jpExcelTable">
                    <thead class="thead-info">
                        <tr>
                            <th></th>
                            <th>Report Date</th>
                            <th>Report No</th>
                            <th>Rev No</th>
                            <th>Ok Qty</th>
                            <th>Sample Qty</th>
                            <th>Action</th>
                        </tr>
                    </thead>
                    <tbody>
                    <?php
                        $genReports = [];
                        if(!empty($pdiData)){
                            $genReports = array_reduce($pdiData, function($genReports, $batch) { $genReports[$batch->batch_no] = $batch; return $genReports; }, []);
                        }
                        //$prcBatch = array_reduce($batchData, function($prcBatch, $batch) { $prcBatch[$batch->batch_no][$batch->id] = $batch; return $prcBatch; }, []);
						$prcBatch = array_reduce($batchData, function($prcBatch, $batch) { $prcBatch[$batch->pack_batch][$batch->id] = $batch; return $prcBatch; }, []);
                        $i=1;
                        foreach($prcBatch AS $key=>$prc_number){
                           ?>
                           <tr  class="bg-light">
                                <th><?=$i?></th>
                                <th colspan="4"><?=$key?><div class="error fir_<?=$i?>"></div></th>
                                <th style="width:15%">
                                    <input type="hidden"  name="batch_no[]" value="<?=$key?>">
                                    <input type="hidden"  name="id[]" value="<?=(!empty($genReports[$key]->id)?$genReports[$key]->id:'')?>">
                                    <input type="text" class="form-control"  name="qty[]" value="<?=(!empty($genReports[$key]->qty)?$genReports[$key]->qty:floatval($invData->qty))?>">
									<div class="error qty_<?=$i?>"></div>
                                </th>
                           </tr>
                           <?php
                           $i++;
                           foreach($prc_number AS $row){
							   $invClass = ($row->pdi_status > 0) ? 'text-danger' : ''; 
                            ?>
                            <tr>
                                <td>
									<input type="checkbox" id="md_checkbox_<?= $row->id ?>" name="firdata[<?=$key?>][fir_id]" class="filled-in chk-col-success reportCheck" data-batch_no="<?=$row->pack_batch?>" data-rowid="<?=$row->id ?>" value="<?= $row->id ?>"  <?=((!empty($genReports[$key]->fir_id) && $row->id == $genReports[$key]->fir_id)?'checked':'')?>><label for="md_checkbox_<?= $row->id ?>" class="mr-3"></label>
                                    
                                    <input type="hidden"  name="firdata[<?=$key?>][rm_batch]" value="<?=$row->rm_batch?>">
                                </td>
                                <td><?=formatDate($row->insp_date)?></td>
                                <td class="<?=$invClass?>"><?=$row->trans_number?></td>
                                <td><?=$row->rev_no?></td>
                                <td><?=(!empty($row->ok_qty) ? floatval($row->ok_qty) : '')?></td> 
                                <td><?=floatval($row->sampling_qty)?></td>
                                <td>
                                    <a href="<?=base_url('sopDesk/printFinalInspection/' . $row->id) ?>" target="_blank" class="btn btn-sm btn-success waves-effect waves-light mr-1" title="Inspection Tag"><i class="fas fa-print"></i></a>
                                </td>
                            </tr>
                            <?php
                           }
                        }
                    ?>
                    </tbody>
                </table>
            </div>
        </div>
    </div>
</form>
<script>
    $(document).ready(function() {
        $(document).on('click','.reportCheck', function() {
            var batchNo = $(this).data('batch_no');  // Get batch number of clicked checkbox
            
            $('.reportCheck').each(function() {
                if ($(this).data('batch_no') === batchNo && this !== event.target) {
                    $(this).prop('checked', false);  // Uncheck the other checkboxes in the same batch
                }
            });
        });
    });
</script>