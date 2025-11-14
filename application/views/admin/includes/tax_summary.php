<div class="row">
    <div class="col-md-12">
        <div class="table-responsive">
            <table id="summaryTable" class="table">
                <thead class="thead-dark">
                    <tr>
                        <th class="summary_desc" style="width: 30%;">Descrtiption</th>
                        <th class="ledgerColumn" style="width: 30%;">Ledger</th>
                        <th style="width: 10%;">Percentage</th>
                        <th style="width: 10%;">Amount</th>
                        <th style="width: 20%;">Net Amount</th>
                    </tr>
                </thead>
                <tbody>
                    <tr>
                        <td>Sub Total</td>
                        <td class="ledgerColumn"></td>
                        <td></td>
                        <td></td>
                        <td>
                            <input type="hidden" name="total_amount" id="total_amount" class="form-control" value="0" />
                            <input type="text" name="taxable_amount" id="taxable_amount" class="form-control summaryAmount" value="0" readonly />
                        </td>
                    </tr>
                    <?php
                    $beforExp = "";
                    $afterExp = "";
                    $tax = "";
                    $invExpenseData = (!empty($dataRow->expenseData)) ? $dataRow->expenseData : array();

                    foreach ($expenseList as $row) :

                        $expPer = 0;
                        $expAmt = 0;
                        $perFiledName = $row->map_code . "_per";
                        $amtFiledName = $row->map_code . "_amount";
                        if (!empty($invExpenseData) && $row->map_code != "roff") :
                            $expPer = $invExpenseData->{$perFiledName};
                            $expAmt = abs($invExpenseData->{$amtFiledName});
                        endif;

                        $options = '';
                        $options = '<select class="form-control select2" name="'.(($row->map_code != "roff")?'expenseData[' . $row->map_code . '_acc_id]':"round_off_acc_id").'" id="' . $row->map_code . '_acc_id">';

                        foreach ($ledgerList as $ledgerRow) :
                            if ($ledgerRow->group_code != "DT") :
                                $filedName = $row->map_code . "_acc_id";
                                if (!empty($invExpenseData->{$filedName})) :
                                    if ($row->map_code != "roff") :
                                        $selected = ($ledgerRow->id == $invExpenseData->{$filedName}) ? "selected" : (($ledgerRow->id == $row->acc_id) ? 'selected' : '');
                                    else :
                                        $selected = ($ledgerRow->id == $dataRow->round_off_acc_id) ? "selected" : (($ledgerRow->id == $row->acc_id) ? 'selected' : '');
                                    endif;
                                else :
                                    $selected = ($ledgerRow->id == $row->acc_id) ? 'selected' : '';
                                endif;

                                $options .= '<option value="' . $ledgerRow->id . '" ' . $selected . '>' . $ledgerRow->party_name . '</option>';
                            endif;
                        endforeach;
                        $options .= '</select>';

                        if ($row->position == 1) :
                            $beforExp .= '<tr>
                                <td>' . $row->exp_name . '</td>
                                <td class="ledgerColumn">' . $options . '</td>
                                <td>';

                            $readonly = "";
                            $perBoxType = "text";
                            $calculateSummaryPer = "calculateSummary";
                            $calculateSummaryAmt = "calculateSummary";
                            if ($row->calc_type != 1) :
                                $perBoxType = "text";
                                $readonly = "readonly";
                                $calculateSummaryPer = "calculateSummary";
                                $calculateSummaryAmt = "";
                            else :
                                $perBoxType = "hidden";
                                $readonly = "";
                                $calculateSummaryPer = "";
                                $calculateSummaryAmt = "calculateSummary";
                            endif;



                            $beforExp .= "<input type='" . $perBoxType . "' name='expenseData[" . $row->map_code . "_per]' id='" . $row->map_code . "_per' data-row='" . json_encode($row) . "' value='" . $expPer . "' class='form-control " . $calculateSummaryPer . " floatOnly'> ";

                            $beforExp .= "</td>
                            <td><input type='text' id='" . $row->map_code . "_amt' class='form-control floatOnly " . $calculateSummaryAmt . "' data-sm_type='exp' data-row='" . json_encode($row) . "' value='" . $expAmt . "' " . $readonly . "></td>
                            <td><input type='text' name='expenseData[" . $row->map_code . "_amount]' id='" . $row->map_code . "_amount'  value='0' class='form-control summaryAmount' readonly /> <input type='hidden' id='other_" . $row->map_code . "_amount' class='otherGstAmount' value='0'> </td>
                            </tr>";

                        else :

                            $afterExp .= '<tr>
                                <td>' . $row->exp_name . '</td>
                                <td class="ledgerColumn">' . $options . '</td>
                                <td>';

                            $readonly = "";
                            $perBoxType = "text";
                            $calculateSummaryPer = "calculateSummary";
                            $calculateSummaryAmt = "calculateSummary";
                            if ($row->map_code != "roff" && $row->calc_type != 1) :
                                $perBoxType = "text";
                                $readonly = "readonly";
                                $calculateSummaryPer = "calculateSummary";
                                $calculateSummaryAmt = "";
                            else :
                                $perBoxType = "hidden";
                                $readonly = "";
                                $calculateSummaryPer = "";
                                $calculateSummaryAmt = "calculateSummary";
                            endif;

                            $afterExp .= "<input type='" . $perBoxType . "' name='".(($row->map_code != "roff") ? "expenseData[" . $row->map_code . "_per]" : "")."' id='" . $row->map_code . "_per' data-row='" . json_encode($row) . "' value='" . $expPer . "' class='form-control  floatOnly " . $calculateSummaryPer . "'> ";

                            $readonly = ($row->map_code == "roff") ? "readonly" : $readonly;
                            $amtType = ($row->map_code == "roff") ? "hidden" : "text";
                            $afterExp .= "</td>
                            <td><input type='" . $amtType . "' id='" . $row->map_code . "_amt' class='form-control " . $calculateSummaryAmt . "  floatOnly ' data-sm_type='exp' data-row='" . json_encode($row) . "' value='" . $expAmt . "' " . $readonly . "></td>
                            <td><input type='text' name='".(($row->map_code == "roff") ? "round_off_amount":"expenseData[" . $row->map_code . "_amount]")."' id='" . $row->map_code . "_amount' value='0' class='form-control floatOnly " . (($row->map_code == "roff") ? "" : "summaryAmount") . "' readonly /> </td>
                            </tr>";
                        endif;
                    endforeach;

                    foreach ($taxList as $taxRow) :
                        $options = '';
                        $options = '<select class="form-control select2" name="' . $taxRow->map_code . '_acc_id" id="' . $taxRow->map_code . '_acc_id">';

                        foreach ($ledgerList as $ledgerRow) :
                            if ($ledgerRow->group_code == "DT") :
                                $filedName = $taxRow->map_code . "_acc_id";
                                if (!empty($dataRow->{$filedName})) :
                                    $selected = ($ledgerRow->id == $dataRow->{$filedName}) ? "selected" : (($ledgerRow->id == $taxRow->acc_id) ? 'selected' : '');
                                else :
                                    $selected = ($ledgerRow->id == $taxRow->acc_id) ? 'selected' : '';
                                endif;

                                $options .= '<option value="' . $ledgerRow->id . '" ' . $selected . '>' . $ledgerRow->party_name . '</option>';
                            endif;
                        endforeach;
                        $options .= '</select>';

                        $taxClass = "";
                        $perBoxType = "text";
                        $calculateSummary = "calculateSummary";
                        $taxPer = 0;
                        $taxAmt = 0;
                        if (!empty($dataRow->id)) :
                            $taxPer = $dataRow->{$taxRow->map_code . '_per'};
                            $taxAmt = abs($dataRow->{$taxRow->map_code . '_amount'});
                        endif;
                        if ($taxRow->map_code == "cgst") :
                            $taxClass = "cgstCol";
                            $perBoxType = "hidden";
                            $calculateSummary = "";
                        elseif ($taxRow->map_code == "sgst") :
                            $taxClass = "sgstCol";
                            $perBoxType = "hidden";
                            $calculateSummary = "";
                        elseif ($taxRow->map_code == "igst") :
                            $taxClass = "igstCol";
                            $perBoxType = "hidden";
                            $calculateSummary = "";
                        endif;

                        $tax .= '<tr class="' . $taxClass . '">
                            <td>' . $taxRow->name . '</td>
                            <td class="ledgerColumn">' . $options . '</td>
                            <td>';

                        $tax .= "<input type='" . $perBoxType . "' name='" . $taxRow->map_code . "_per' id='" . $taxRow->map_code . "_per' data-row='" . json_encode($taxRow) . "' value='" . $taxPer . "' class='form-control floatOnly " . $calculateSummary . "'> ";

                        $tax .= "</td>
                            <td><input type='" . $perBoxType . "' id='" . $taxRow->map_code . "_amt' class='form-control floatOnly' data-sm_type='tax'data-row='" . json_encode($taxRow) . "' value='" . $taxAmt . "' readonly ></td>
                            <td><input type='text' name='" . $taxRow->map_code . "_amount' id='" . $taxRow->map_code . "_amount'  value='0' class='form-control floatOnly summaryAmount' readonly /> </td>
                        </tr>";
                    endforeach;

                    echo $beforExp;
                    echo $tax;
                    echo $afterExp;
                    ?>

                </tbody>
                <tfoot class="thead-dark">
                    <tr>
                        <th>Net. Amount</th>
                        <th class="ledgerColumn"></th>
                        <th></th>
                        <th></th>
                        <th>
                            <input type="text" name="net_amount" id="net_amount" class="form-control floatOnly" value="0" readonly />
                        </th>
                    </tr>
                </tfoot>
            </table>									
        </div>
    </div>
</div>