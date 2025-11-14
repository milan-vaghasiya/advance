<div class="col-md-12">
    <div class="row">
        <div class="table-responsive">
            <table class="table table-bordered">
                <thead class="thead-dark">
                    <tr>
                        <th class="text-center">Rev. No.</th>
                        <th class="text-center">Rev. Date</th>
                        <th class="text-center">Action</th>
                    </tr>
                </thead>
                <tbody>
                    <?php
                        foreach($dataRow as $row):
                            echo '<tr>
                                <td class="text-center">'.sprintf("%02d",$row->quote_rev_no).'</td>
                                <td class="text-center">'.formatDate($row->doc_date).'</td>
                                <td class="text-center">
                                    <a class="btn btn-sm btn-outline-success btn-edit permission-approve" href="'.base_url('salesQuotation/printQuotation/'.$row->id).'" target="_blank" datatip="Print" flow="left"><i class="fas fa-print" ></i></a>
                                </td>
                            </tr>';
                        endforeach;
                    ?>
                </tbody>
            </table>
        </div>
    </div>
</div>