<form data-res_function="getReviewResponse">
    <div class="row rejForm">
        <div class="col-md-4 form-group">
            <input type="hidden" name="id">
            <input type="hidden" id="prc_id" name="prc_id" value="<?= (!empty($dataRow->prc_id) ? $dataRow->prc_id : '') ?>">
            <input type="hidden" id="log_id" name="log_id" value="<?= (!empty($dataRow->id) ? $dataRow->id : '') ?>">
            <input type="hidden" id="item_id" name="item_id" value="<?= (!empty($dataRow->item_id) ? $dataRow->item_id : '') ?>">
            <input type="hidden" id="decision_type" name="decision_type" value="1">
            <input type="hidden" id="source" name="source" value="<?=$source?>">
            <?php if(in_array($source,['GRN','Manual'])){ ?>
                <input type="hidden" id="batch_no" name="batch_no" value="<?=(!empty($dataRow->batch_no) ? $dataRow->batch_no : '')?>">  
                <input type="hidden" id="rr_by" name="rr_by" value="<?=(!empty($dataRow->party_id) ? $dataRow->party_id : '')?>">  
            <?php } ?>
            <label for="qty">Rej Qty</label>
            <input type="text" id="qty" name="qty" class="form-control req numericOnly">
        </div>
        <div class="col-md-<?=((in_array($source,['GRN','Manual'])) ? '8' : '4')?> form-group">
            <label for="rr_reason">Rejection Reason</label>
            <select id="rr_reason" name="rr_reason" class="form-control select2 req">
                <option value="">Select Reason</option>
                <?php
                foreach ($rejectionComments as $row) :
                    $code = (!empty($row->code)) ? '[' . $row->code . '] - ' : '';
                    echo '<option value="' . $row->id . '" data-code="' . $row->code . '" data-reason="' . $row->remark . '" >' . $code . $row->remark . '</option>';

                endforeach;
                ?>
            </select>
        </div>
        <?php if(!in_array($source,['GRN','Manual'])){ ?>
        
        <div class="col-md-4 form-group">
            <label for="rr_stage">Rejection Process</label>
            <select id="rr_stage" name="rr_stage" class="form-control select2 req">
                <?php if (empty($dataRow->stage)) { ?> <option value="">Select Process</option> 
                <?php } else { echo $dataRow->stage; } ?>
            </select>
        </div>
		
        <div class="col-md-4 form-group">
            <label for="rr_by">Rejection By <span class="text-danger">*</span></label>
            <select id="rr_by" name="rr_by" class="form-control select2 req">
                <option value="">Select Rej. By</option>
            </select>
        </div>
		<div class="col-md-4 form-group">
            <label for="mc_op_id">Operator / Machine</label>
            <select id="mc_op_id" name="mc_op_id" class="form-control select2 req">
				<option value="">Select</option>
                
            </select>
        </div>
		<div class="col-md-4 form-group">
            <label for="in_ch_no">In Challan No</label>
            <select id="in_ch_no" name="in_ch_no" class="form-control select2">
				<option value="">Select</option>
                
            </select>
        </div>
        <?php } ?>
        <div class="col-md-12 form-group">
            <label for="rr_comment">Note</label>
            <textarea id="rr_comment" name="rr_comment" class="form-control" value=""></textarea>
        </div>
    </div>
</form>
<hr>
<div class="col-md-12">
    <div class="row">
        <h5 >Review Transaction :</h5>
        <div class="table-responsive  mb-3">
            <table id='rejTransTable' class="table table-bordered jpExcelTable mb-5">
                <thead class="text-center">
                    <tr>
                        <th style="min-width:20px">#</th>
                        <th>Qty.</th>
                        <th>Decision</th>
                        <th>Reason</th>    
                        <?php if($source == 'MFG') {?>                   
                        <th>Rej/Rw Process</th>                        
                        <th>Rej/Rw By</th>                        
                        <th>Operator</th>                        
                        <th>Machine</th>
                        <th>In Challan No</th>
                        <?php } ?>
                        <th>Remark</th>
                        <th style="width:100px;">Action</th>
                    </tr>
                </thead>
                <tbody id="rejTbodyData">
                   
                </tbody>
            </table>
        </div>
    </div>
</div>
<script>
var tbodyData = false;
if(!tbodyData){
    var postData = {'postData':{'log_id':$(".rejForm #log_id").val(),'source':$(".rejForm #source").val()},'table_id':"rejTransTable",'tbody_id':'rejTbodyData','tfoot_id':'','fnget':'getReviewHtml'};
    getTransHtml(postData);
    tbodyData = true;
}
</script>
