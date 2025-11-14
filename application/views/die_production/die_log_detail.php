<form>
    <input type="hidden" name="id" value="<?=$logData->id?>">
    <div class="col-md-12">
        <div class="table-responsive">
            <table class="table jpExcelTable">
                <tr>
                    <th>Die Type</th>
                    <td colspan="3"><?=$logData->category_name?></td>
                </tr>
                <tr>
                    <th>Product</th>
                    <td colspan="3"><?=(!empty($logData->fg_item_code)?$logData->fg_item_code:'').' '.$logData->fg_item_name?></td>
                </tr>
                <tr>
                    <th>Job No</th>
                    <td><?=$logData->trans_number?></td>
                    <th>Log Date</th>
                    <td><?=formatDate($logData->trans_date)?></td>
                </tr>
                <?php if($logData->process_by == 1){ ?>
                <tr>
                    <th>Machine</th>
                    <td><?=$logData->processor_name?></td>
                    <th>Operator</th>
                    <td><?=$logData->emp_name?></td>
                </tr>
                <tr>
                    <th>Shift</th>
                    <td><?=$logData->shift_name?></td>
                    <th>Production Time</th>
                    <td><?=$logData->production_time?></td>
                </tr>
                <?php }else{ ?>
                    <tr>
                        <th>Vendor</th>
                        <td><?=$logData->processor_name?></td>
                        <th>In Challan No</th>
                        <td>
							<?=$logData->in_challan_no?>
							<?php if (!empty($logData->attachment)) : ?>
								<?='  |  <a href="' . base_url('assets/uploads/die_outsource/' . $logData->attachment) . '" target="_blank"><i class="fa fa-download"></i></a>'; ?>
							<?php endif; ?>
						</td>
                    </tr>
                <?php } ?>
                <tr>
                    <th>Production Time</th>
                    <td><?=$logData->production_time?></td>
                    <th>Material Value</th>
                    <td><?=$logData->material_cost?></td>
                </tr>
                <tr>
                    <th>Machine Hourly Rate</th>
                    <td><?=$logData->mhr?></td>
                    <th>Total Value</th>
                    <td><?=$logData->total_value?></td>
                </tr>
                <tr>
                    <th>Note</th>
                    <td colspan="3"><?=$logData->remark?></td>
                </tr>
            </table>
        </div>
    </div>
</form>