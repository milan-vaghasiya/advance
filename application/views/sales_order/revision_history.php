<div class="col-md-12">
    <div class="row">
        <div class="table-responsive">
            <table class="table table-bordered">
                <thead class="thead-dark">
                    <tr class="text-center">
                        <th>Rev. No.</th>
                        <th>Rev. Date</th>
                        <th>Action</th>
                    </tr>
                </thead>
                <tbody>
                    <?php
                        echo '<tr class="text-center">
                            <td>'.sprintf("%02d",$soData->so_rev_no).'</td>
                            <td>'.formatDate($soData->doc_date).'</td>
                            <td>
                                <a class="btn btn-sm btn-outline-success btn-edit permission-approve1" href="'.base_url('salesOrders/printOrder/'.encodeurl(['id'=>$soData->id])).'" target="_blank" datatip="Print" flow="left"><i class="fas fa-print"></i></a>
                            </td>
                        </tr>';

                        foreach($dataRow as $row):
                            echo '<tr class="text-center">
                                <td>'.sprintf("%02d",$row->so_rev_no).'</td>
                                <td>'.formatDate($row->doc_date).'</td>
                                <td>
                                    <a class="btn btn-sm btn-outline-success btn-edit permission-approve1" href="'.base_url('salesOrders/printOrder/'.encodeurl(['id'=>$row->id, 'pdf_type'=>'Revision'])).'" target="_blank" datatip="Print" flow="left"><i class="fas fa-print"></i></a>
                                </td>
                            </tr>';
                        endforeach;
                    ?>
                </tbody>
            </table>
        </div>
    </div>
</div>