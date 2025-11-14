<?php $this->load->view('includes/header'); ?>
<div class="page-content-tab">
    <div class="container-fluid">
        <form id="empPermission" data-res_function="resPermission">
            <div class="row">
                <div class="col-12">
                    <div class="page-title-box">
                        <div class="float-start">
                            <ul class="nav nav-pills">
                                <a href="<?= base_url($headData->controller) ?>" class="btn waves-effect waves-light btn-outline-primary  permission-write"> General Permission</a>
                                <a href="<?= base_url($headData->controller . "/empPermissionReport/") ?>" class="btn waves-effect waves-light btn-outline-warning permission-write active"> Report Permission</a>
                                <button type="button" class="btn waves-effect waves-light btn-outline-success float-center permission-write" onclick="modalAction({'modal_id' : 'modal-md', 'form_id' : 'copyPermission','fnedit':'copyPermission','call_function':'copyPermission', 'title' : 'Copy Permission','js_store_fn':'confirmStore'});">Copy Permission</button>
                                <!--<a href="<?= base_url($headData->controller . "/appPermission/") ?>" class="btn waves-effect waves-light btn-outline-warning permission-write"> App Permission</a>-->
                            </ul>
                        </div>
                        <div class="float-end" style="width:30%;">
                            <input type="hidden" name="menu_type" id="menu_type" value="1">
                            <select name="emp_id" id="emp_id" class="form-control select2">
                                <option value="">Select Employee</option>
                                <?php
                                    foreach ($empList as $row) :
                                        $empName = (!empty($row->emp_code))?'[' . $row->emp_code . '] ' . $row->emp_name:$row->emp_name;
                                        echo '<option value="' . $row->id . '">' . $empName . '</option>';
                                    endforeach;
                                ?>
                            </select>
                        </div>
                    </div>
                </div>
            </div>
            <div class="row">
                <div class="col-12">
                    <div class="col-12">
                        <div class="card">
                            <div class="card-body reportDiv" style="min-height:75vh">
                                <div class="table-responsive">
                                    <div class="panel-group wrap" id="bs-collapse">
                                        <?php
                                            foreach($permission as $row):
                                        ?>
                                            <div class="panel">
                                                <div class="panel-heading">
                                                    <h4 class="panel-title">
                                                        <a data-toggle="collapse" data-parent="#bs-collapse" href="#menu_<?=$row->id?>"><?=$row->menu_name?></a>
                                                        <input type="hidden" name="menu_id[]" value="<?=$row->id?>">
                                                        <input type="hidden" name="is_master[]" value="<?=$row->is_master?>">
                                                        <?php 
                                                            if(empty($row->is_master)):
                                                                echo '<input type="hidden" name="main_id[]" value="'.$row->id.'">';
                                                            endif;
                                                        ?>
                                                    </h4>
                                                </div>
                                                <div id="menu_<?=$row->id?>" class="panel-collapse collapse">
                                                    <div class="panel-body">
                                                        <table id='reportTable' class="table table-bordered ">
                                                            <tr class="thead-dark">
                                                                <th class="text-center">#</th>
                                                                <th>
                                                                    Menu/Page Name
                                                                    <input type="checkbox" id="masterSelect_<?=$row->id?>" class="filled-in chk-col-success checkAll" value="<?=$row->id?>"><label for="masterSelect_<?=$row->id?>">Select All</label>
                                                                </th>
                                                                <th class="text-center">Read</th>
                                                                <th class="text-center">Write</th>
                                                                <th class="text-center">Modify</th>
                                                                <th class="text-center">Delete</th>
                                                                <th class="text-center">Approve</th>
                                                            </tr>
                                        <?php
                                            $j=1;
                                            foreach($row->subMenus as $subRow):
                                                if(!empty($row->id)):
                                                if(empty($subRow->menu_id)):
                                                    $inputReadName = "menu_read_".$row->id;
                                                    $inputWriteName = "menu_write_".$row->id;
                                                    $inputModifyName = "menu_modify_".$row->id;
                                                    $inputDeleteName = "menu_delete_".$row->id;
                                                else:
                                                    $inputReadName = "sub_menu_read_".$subRow->id."_".$row->id;
                                                    $inputWriteName = "sub_menu_write_".$subRow->id."_".$row->id;
                                                    $inputModifyName = "sub_menu_modify_".$subRow->id."_".$row->id;
                                                    $inputDeleteName = "sub_menu_delete_".$subRow->id."_".$row->id;
                                                    $inputApproveName = "sub_menu_approve_".$subRow->id."_".$row->id;
                                                endif;
                                        ?>
                                        <tr>
                                                <td class="text-center"><?=$j++?></td>
                                                <td>
                                                    <?=$subRow->sub_menu_name?>
                                                    <?php 
                                                        if(!empty($subRow->menu_id)):
                                                            echo '<input type="hidden" name="sub_menu_id_'.$row->id.'[]" value="'.$subRow->id.'">';
                                                        endif;
                                                    ?>
                                                </td>
                                                <td class="text-center">
                                                    <input type="checkbox" id="<?=$inputReadName?>" name="<?=$inputReadName?>[]" class="filled-in chk-col-success check_<?=$row->id?>" value="1">
                                                    <label for="<?=$inputReadName?>"></label>
                                                </td>
                                                <td class="text-center">
                                                    <?php if($subRow->is_report == 0):?>
                                                    <input type="checkbox" id="<?=$inputWriteName?>" name="<?=$inputWriteName?>[]" class="filled-in chk-col-success check_<?=$row->id?>" value="1">
                                                    <label for="<?=$inputWriteName?>"></label>
                                                    <?php endif; ?>
                                                </td>
                                                <td class="text-center">
                                                    <?php if($subRow->is_report == 0):?>
                                                    <input type="checkbox" id="<?=$inputModifyName?>" name="<?=$inputModifyName?>[]" class="filled-in chk-col-success check_<?=$row->id?>" value="1">
                                                    <label for="<?=$inputModifyName?>"></label>
                                                    <?php endif; ?>
                                                </td>
                                                <td class="text-center">
                                                    <?php if($subRow->is_report == 0):?>
                                                    <input type="checkbox" id="<?=$inputDeleteName?>" name="<?=$inputDeleteName?>[]" class="filled-in chk-col-success check_<?=$row->id?>" value="1">
                                                    <label for="<?=$inputDeleteName?>"></label>
                                                    <?php endif; ?>
                                                </td>
                                                <td class="text-center">
                                                    <?php if($subRow->is_approve_req == 1):?>
                                                    <input type="checkbox" id="<?=$inputApproveName?>" name="<?=$inputApproveName?>[]" class="filled-in chk-col-success check_<?=$row->id?>" value="1">
                                                    <label for="<?=$inputApproveName?>"></label>
                                                    <?php endif; ?>
                                                </td>
                                        </tr> 
                                        
                                        <?php endif; endforeach; ?>
                                                </table>
                                                </div>
                                            </div>
                                        </div>
                                    <?php endforeach; ?>
                                </div>							
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </form>
    </div>
</div>

<div class="bottomBtn bottom-25 right-25 permission-write">
    <button type="button" class="btn btn-primary btn-rounded font-bold permission-write save-form" style="letter-spacing:1px;" onclick="customStore({'formId':'empPermission','fnsave':'savePermission'});">SAVE PERMISSION</button>
</div>

<?php $this->load->view('includes/footer'); ?>
<script src="<?php echo base_url();?>assets/js/custom/emp-permission.js?v=<?=time()?>"></script>
