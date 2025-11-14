<form>
    <div class="col-md-12">
        <div class="row">
            <div class="table-responsive">
                <table class="table table-bordered">
                    <thead class="thead-dark">
                        <tr>
                            <th>#</th>
                            <th>SQ. No.</th>
                            <th>SQ. Date</th>
                            <th>Item Name</th>
                            <th>Pending Qty.</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php
                            $i=1;
                            foreach($orderItems as $row):
                                $row->from_entry_type = $row->entry_type;
                                $row->ref_id = $row->id;
                                
                                unset($row->id,$row->entry_type);
                                $row->row_index = "";
                                $row->entry_type = "";
                                $jsonData = htmlspecialchars(json_encode($row), ENT_QUOTES, 'UTF-8');
                                echo "<tr>
                                    <td class='text-center'>
                                        <input type='checkbox' id='md_checkbox_" . $i . "' class='filled-in chk-col-success orderItem' data-row='".$jsonData."' ><label for='md_checkbox_" . $i . "' class='mr-3 check" . $row->ref_id . "'></label>
                                    </td>
                                    <td>".$row->trans_number."</td>
                                    <td>".formatDate($row->trans_date)."</td>
                                    <td>".$row->item_name."</td>
                                    <td>".floatval($row->qty)."</td>
                                </tr>";
                                $i++;
                            endforeach;
                        ?>
                    </tbody>
                </table>
            </div>
        </div>
    </div>
</form>