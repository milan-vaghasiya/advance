
<form data-res_function = 'getPrcResponse'>
    <div class="col-md-12 row">
        <input type="hidden" name="id" value="<?=(!empty($id))?$id:''?>" />

        <div class="col-md-4 form-group">
            <label for="receive_at">Realese Date</label>
            <input type="date" name="receive_at" id="receive_at" class="form-control req" value="<?=date("Y-m-d")?>">
        </div> 

        <div class="col-md-4 form-group" <?=($challan_type == 1)?'hidden':''?>>
            <label for="in_ch_no">In Challan No</label>
            <input type="text" name="in_ch_no" id="in_ch_no" class="form-control req" value="">
        </div>

        <div class="col-md-4 form-group" <?=($challan_type == 1)?'hidden':''?>>
            <label for="volume">Volume</label>
            <input type="text" name="volume" id="volume" class="form-control" value="">
        </div>
        
        <div class="col-md-12 form-group">
            <label for="return_remark">Remark</label>
            <textarea name="return_remark" id="return_remark" class="form-control"></textarea>
        </div>
            
    </div>
</form>