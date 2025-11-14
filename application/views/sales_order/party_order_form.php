<form>
    <div class="col-md-12">
        <div class="row">
            <div class="col-md-12">
                <div class="error item_error"></div>
                <div class="table table-responsive">
                    <table class="table table-bordered">
                        <thead class="thead-dark">
                            <tr>
                                <th>#</th>
                                <th style="width:40%;">Item Name</th>
                                <th style="width:20%;">Brand Name</th>
                                <th style="width:10%;">Qty</th>
                                <th style="width:20%;">Remark</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php
                                $i=1;
                                foreach($itemList as $row):
                                    echo '<tr>
                                        <td>'.$i.'</td>
                                        <td>
                                            '.$row->item_name.'
                                            <input type="hidden" name="itemData['.$i.'][item_id]" id="item_id_.'.$i.'" value="'.$row->id.'">
                                        </td>
                                        <td>
                                            <select name="itemData['.$i.'][brand_id]" id="brand_id_'.$i.'" class="form-control select2">
                                                <option value="">Select Brand</option>
                                                '.getBrandListOption($brandList).'
                                            </select>
                                        </td>
                                        <td>
                                            <input type="text" name="itemData['.$i.'][qty]" id="qty_'.$i.'" class="form-control floatOnly" value="">
                                        </td>
                                        <td>
                                            <input type="text" name="itemData['.$i.'][item_remark]" id="item_remark_'.$i.'" class="form-control" value="">
                                        </td>
                                    </tr>';
                                    $i++;
                                endforeach;
                            ?>
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>
</form>