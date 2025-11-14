<form>
    <div class="row">
        <div class="col-md-12">
            <div class="table-responsive">
                <table class="table jpExcelTable" style="margin-bottom:30px !important">
                    <thead class="bg-light-peach">
                        <tr>
                            <th>#</th>
                            <th>Item</th>
                            <th>Issued No</th>
                            <th>Issued Date</th>
                            <th>Issued Qty</th>
                            <th>Batch No</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php
                        if(!empty($extMaterial)){
                            $i=1;
                            foreach($extMaterial AS $row){
                            ?> <tr>
                                    <td><?=$i++?></td>
                                    <td><?=$row->item_name?></td>
                                    <td><?=$row->issue_number?></td>
                                    <td><?=formatDate($row->issue_date) ?></td>
                                    <td><?=$row->issue_qty?></td>
                                    <td><?=$row->batch_no?></td>
                                </tr> <?php
                            }
                        }
                        ?>
                    </tbody>
                </table>
            </div>
        </div>
    </div>
</form>