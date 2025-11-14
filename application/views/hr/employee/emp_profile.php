<?php $this->load->view('includes/header'); ?>

<?php
    $profile_pic = 'male_user.png';
    if(!empty($empData->emp_profile)):
        $profile_pic = $empData->emp_profile;
    else:
        if(!empty($empData->emp_gender) and $empData->emp_gender=="Female"):
            $profile_pic = 'female_user.png';
        endif;
    endif;
?>

<link href="<?=base_url();?>assets/css/icard.css?v=<?=time()?>" rel="stylesheet" type="text/css">

<div class="page-content-tab">
	<div class="container-fluid">
        <div class="row">
            <div class="col-12">
				<div class="col-12">
                    <div class="card">
                        <div class="card-header">
                            <div class="row">
                                <div class="col-md-6">
                                    <h4 class="card-title">
                                        <?= (!empty($empData->emp_name)) ? $empData->emp_name : "Employee Profile"; ?>
                                        - <small><i><?= (!empty($empData->designation_name)) ? $empData->designation_name : "-"; ?> (<?= (!empty($empData->department_name)) ? $empData->department_name : ""; ?>)</i></small>
                                    </h4>
                                </div>
                                <div class="col-md-6">
                                    <a href="<?= base_url($headData->controller) ?>" class="btn waves-effect waves-light btn-outline-dark float-right"><i class="fa fa-arrow-left"></i> Back</a>
                                    <!--<a href="<?= base_url($headData->controller.'/icard/'.$emp_id) ?>" class="btn waves-effect waves-light btn-outline-dark float-right" target="_blank"><i class="fa fa-address-book"></i> Icard</a>-->
                                </div>
                            </div>                    
                        </div>

                        <div class="card-body">
                            <div class="col-md-12">
                                <div class="row">
                                    <!-- Employee Basic Info Start -->
                                    <div class="col-lg-3 col-xlg-3 col-md-3">
                                        <div class="card">
                                            <div class="card-body p-2">
                                                <form id="profileForm" action="POST" enctype="multipart/form-data">
                                                    <div class="profile-pic-wrapper">
                                                        <div class="pic-holder">
                                                            <!-- uploaded pic shown here -->
                                                            <img id="profilePic" class="pic" src="<?= base_url('assets/uploads/emp_profile/'.$profile_pic) ?>">
                                                            <Input class="uploadProfileInput" type="file" name="profile_pic" id="newProfilePhoto" accept="image/*" style="opacity: 0;" />
                                                            <label for="newProfilePhoto" class="upload-file-block">
                                                                <div class="text-center">
                                                                    <div class="mb-2"><i class="fa fa-camera fa-2x"></i></div>
                                                                    <div class="text-uppercase">Update <br /> Profile Photo</div>
                                                                </div>
                                                            </label>
                                                            <input type="hidden" name="emp_id" id="emp_id" value="<?=(!empty($empData->id))?$empData->id:""; ?>" />
                                                        </div>
                                                    </div>
                                                </form>
                                            </div>
                                            <h4 class="card-title m-t-5 m-b-5 text-center p-1" style="background:#009ee3;color:#FFFFFF;">EMP CODE : <?= (!empty($empData->emp_code)) ? $empData->emp_code : "-"; ?></h4>
                                            <div class="card-body p-3">
                                                <strong>Phone</strong>
                                                <p class="text-muted"><?= (!empty($empData->emp_contact)) ? $empData->emp_contact : "-"; ?></p>
                                                <strong>Gender</strong>
                                                <p class="text-muted"><?= (!empty($empData->emp_gender)) ? $empData->emp_gender : "-" ?></p>
                                                <strong>Date Of Birth</strong>
                                                <p class="text-muted"><?= (!empty($empData->emp_birthdate)) ? date("d-m-Y", strtotime($empData->emp_birthdate)) : "-"; ?></p>
                                                <strong>Joining Date</strong>
                                                <p class="text-muted"><?= (!empty($empData->emp_joining_date)) ? date("d-m-Y", strtotime($empData->emp_joining_date)) : "-"; ?></p>
                                            </div>
                                        </div>
                                    </div>
                                    <!-- Employee Basic Info End -->

                                    <div class="col-lg-9 col-xlg-9 col-md-9">
                                        <div class="card">
                                            <!-- Tabs -->
                                            <ul class="nav nav-pills custom-pills" id="pills-tab" role="tablist">
                                                <li class="nav-item">
                                                    <a class="nav-link active" id="pills-personal-tab" data-toggle="pill" href="#personal" role="tab" aria-controls="pills-personal" aria-selected="true">Personal</a>
                                                </li>
                                                <li class="nav-item">
                                                    <a class="nav-link" id="pills-workprofile-tab" data-toggle="pill" href="#workprofile" role="tab" aria-controls="pills-workprofile" aria-selected="false">Work Profile</a>
                                                </li>
                                                <li class="nav-item">
                                                    <a class="nav-link" id="pills-documents-tab" data-toggle="pill" href="#documents" role="tab" aria-controls="pills-documents" aria-selected="false">Documents</a>
                                                </li>
                                                <li class="nav-item">
                                                    <a class="nav-link" id="pills-nomination-tab" data-toggle="pill" href="#nomination" role="tab" aria-controls="pills-nomination" aria-selected="false">Nomination</a>
                                                </li>
                                                <li class="nav-item">
                                                    <a class="nav-link" id="pills-education-tab" data-toggle="pill" href="#education" role="tab" aria-controls="pills-education" aria-selected="false">Education</a>
                                                </li>
                                                <!-- <li class="nav-item">
                                                    <a class="nav-link" id="pills-salary-tab" data-toggle="pill" href="#salary" role="tab" aria-controls="pills-salary" aria-selected="true">Salary</a>
                                                </li> -->
                                                <!-- <li class="nav-item">
                                                    <a class="nav-link" id="pills-staffSkill-tab" data-toggle="pill" href="#staffSkill" role="tab" aria-controls="pills-staffSkill" aria-selected="true">Staff Skill</a>
                                                </li> -->
                                                <!-- <li class="nav-item">
                                                    <a class="nav-link" id="pills-facility-tab" data-toggle="pill" href="#facility" role="tab" aria-controls="pills-facility" aria-selected="true">Facility</a>
                                                </li> -->
                                                <!-- <li class="nav-item">
                                                    <a class="nav-link" id="pills-icard-tab" data-toggle="pill" href="#icard" role="tab" aria-controls="pills-icard" aria-selected="true">I-Card</a>
                                                </li> -->
                                            </ul>
                                            <!-- Tabs -->

                                            <div class="tab-content" id="pills-tabContent">

                                                <!-- Profile Tab Start -->
                                                <div class="tab-pane fade show active" id="personal" role="tabpanel" aria-labelledby="pills-personal-tab">
                                                    <div class="card">
                                                        <div class="card-body">
                                                            <form id="personalDetail" data-res_function="resProfileDetail">
                                                                <div class="row">
                                                                    <input type="hidden" name="id" value="<?=(!empty($empData->id))?$empData->id:""; ?>" />
                                                                    <input type="hidden" name="form_type" value="personalDetail" />

                                                                    <div class="col-md-4 form-group">
                                                                        <label for="emp_name">Employee Name</label>
                                                                        <input type="text" name="emp_name" id="emp_name" class="form-control text-capitalize req" placeholder="Emp Name" value="<?=(!empty($empData->emp_name))?$empData->emp_name:""; ?>" />
                                                                    </div>

                                                                    <div class="col-md-4 form-group">
                                                                        <label for="father_name">Father Name</label>
                                                                        <input type="text" name="father_name" class="form-control" value="<?=(!empty($empData->father_name))?$empData->father_name:""?>" />
                                                                    </div>

                                                                    <div class="col-md-4 form-group">
                                                                        <label for="mother_name">Mother Name</label>
                                                                        <input type="text" name="mother_name" class="form-control" value="<?=(!empty($empData->mother_name))?$empData->mother_name:""?>" />
                                                                    </div>

                                                                    <div class="col-md-3 form-group">
                                                                        <label for="emp_contact">Phone No.</label>
                                                                        <input type="number" name="emp_contact" class="form-control numericOnly req" placeholder="Phone No." value="<?=(!empty($empData->emp_contact))?$empData->emp_contact:""?>" />
                                                                    </div>

                                                                    <div class="col-md-3 form-group">
                                                                        <label for="emp_alt_contact">Emergency Contact</label>
                                                                        <input type="number" name="emp_alt_contact" class="form-control numericOnly req" placeholder="Phone No." value="<?=(!empty($empData->emp_alt_contact))?$empData->emp_alt_contact:""?>" />
                                                                    </div>

                                                                    <div class="col-md-3 form-group">
                                                                        <label for="emp_gender">Gender</label>
                                                                        <select name="emp_gender" id="emp_gender" class="form-control select2">
                                                                            <option value="">Select Gender</option>
                                                                            <?php
                                                                                foreach($this->gender as $value):
                                                                                    $selected = (!empty($empData->emp_gender) && $value == $empData->emp_gender)?"selected":"";
                                                                                    echo '<option value="'.$value.'" '.$selected.'>'.$value.'</option>';
                                                                                endforeach;
                                                                            ?>
                                                                        </select>
                                                                    </div>

                                                                    <div class="col-md-3 form-group">
                                                                        <label for="marital_status">Marital Status</label>
                                                                        <select name="marital_status" id="marital_status" class="form-control " >
                                                                            <option value="">Select</option>
                                                                            <?php
                                                                                foreach($this->maritalStatus as $status):
                                                                                    $selected = ((!empty($empData->marital_status)) && $empData->marital_status == $status)?"selected":"";
                                                                                    echo '<option value="'.$status.'">'.$status.'</option>';
                                                                                endforeach;
                                                                            ?>
                                                                        </select>
                                                                    </div>

                                                                    <div class="col-md-3 form-group">
                                                                        <label for="emp_birthdate">Date of Birth</label>
                                                                        <input type="date" name="emp_birthdate" id="emp_birthdate" class="form-control" value="<?=(!empty($empData->emp_birthdate))?$empData->emp_birthdate:date("Y-m-d")?>" max="<?=(!empty($empData->emp_birthdate))?$empData->emp_birthdate:date("Y-m-d")?>" />
                                                                    </div>

                                                                    <div class="col-md-3 form-group">
                                                                        <label for="pf_applicable">PF Applicable</label>
                                                                        <select name="pf_applicable" id="pf_applicable" class="form-control " >
                                                                            <option value="0" <?=(!empty($empData->pf_applicable) && $empData->pf_applicable == "0")?"selected":""?>>No</option>
                                                                            <option value="1" <?=(!empty($empData->pf_applicable) && $empData->pf_applicable == "1")?"selected":""?>>Yes</option>
                                                                        </select>
                                                                    </div>

                                                                    <div class="col-md-3 form-group">
                                                                        <label for="pf_no">PF Number</label>
                                                                        <input type="text" name="pf_no" id="pf_no" class="form-control" value="<?=(!empty($empData->pf_no))?$empData->pf_no:""?>" />
                                                                    </div>

                                                                    <div class="col-md-3 form-group">
                                                                        <label for="uan_no">UAN Number</label>
                                                                        <input type="text" name="uan_no" id="uan_no" class="form-control" value="<?=(!empty($empData->uan_no))?$empData->uan_no:""?>" />
                                                                    </div>

                                                                    <div class="col-md-12 form-group">
                                                                        <label for="mark_id">Mark of Identification</label>
                                                                        <input type="text" name="mark_id" class="form-control" placeholder="Mark of Identification" value="<?=(!empty($empData->mark_id))?$empData->mark_id:""?>" />
                                                                    </div>

                                                                    <div class="col-md-12 form-group">
                                                                        <label for="emp_address">Address</label>
                                                                        <textarea name="emp_address" class="form-control" placeholder="Address" style="resize:none;" rows="1"><?=(!empty($empData->emp_address))?$empData->emp_address:""?></textarea>
                                                                    </div>

                                                                    <div class="col-md-12 form-group">
                                                                        <label for="permenant_address">Permenant Address</label>
                                                                        <textarea name="permenant_address" class="form-control" placeholder="Permenant Address" style="resize:none;" rows="1"><?=(!empty($empData->permenant_address))?$empData->permenant_address:""?></textarea>
                                                                    </div>

                                                                </div>
                                                            </form>
                                                        </div>
                                                        <div class="card-footer">   
                                                            <button type="button" class="btn waves-effect waves-light btn-outline-success btn-save float-right" onclick="customStore({'formId':'personalDetail','fnsave':'editProfile'},);"><i class="fa fa-check"></i> Save </button>
                                                        </div>
                                                    </div>
                                                </div>
                                                <!-- Profile Tab End -->

                                                <!-- Work Profile Start -->
                                                <div class="tab-pane fade" id="workprofile" role="tabpanel" aria-labelledby="pills-workprofile-tab">
                                                    <div class="card">
                                                        <div class="card-body">
                                                            <form id="workProfile" data-res_function="resProfileDetail">
                                                                <div class="row">
                                                                    <div class="col-md-3 form-group">
                                                                        <input type="hidden" name="id" value="<?=(!empty($empData->id))?$empData->id:""; ?>" />
                                                                        <input type="hidden" name="form_type" value="workprofile" />
                                                                        
                                                                        <label for="emp_grade">Grade</label>
                                                                        <select name="emp_grade" id="emp_grade" class="form-control select2">
                                                                            <option value="">Select Grade</option>
                                                                            <?php
                                                                                foreach($this->empGrade as $value):
                                                                                    $selected = (!empty($empData->emp_grade) && $value == $empData->emp_grade)?"selected":"";
                                                                                    echo '<option value="'.$value.'" '.$selected.'>'.$value.'</option>';
                                                                                endforeach;
                                                                            ?>
                                                                        </select>
                                                                    </div>

                                                                    <div class="col-md-3 form-group">
                                                                        <label for="emp_category">Punch Category</label>
                                                                        <select name="emp_category" id="emp_category" class="form-control select2 req">
                                                                            <option value="">Select Category</option>
                                                                            <?php
                                                                                foreach($empCategoryList as $row):
                                                                                    $selected = (!empty($empData->emp_category) && $row->id == $empData->emp_category)?"selected":"";
                                                                                    echo '<option value="'.$row->id.'" '.$selected.'>'.$row->category.'</option>';
                                                                                endforeach;
                                                                            ?>
                                                                        </select>
                                                                    </div>

                                                                    <div class="col-md-3 form-group">
                                                                        <label for="emp_joining_date">Joining Date</label>
                                                                        <input type="date" name="emp_joining_date" id="emp_joining_date" class="form-control" value="<?=(!empty($empData->emp_joining_date))?$empData->emp_joining_date:date("Y-m-d")?>" max="<?=(!empty($empData->emp_joining_date))?$empData->emp_joining_date:date("Y-m-d")?>" />
                                                                    </div>
                                                                
                                                                    <div class="col-md-3 form-group">
                                                                        <label for="emp_type">Employee Type</label>
                                                                        <select name="emp_type" id="emp_type" class="form-control select2 req " >
                                                                            <option value="">Select Type</option>
                                                                            <?php
                                                                                foreach($this->empType as $id=>$type):
                                                                                    $selected = (!empty($empData->emp_type) && $empData->emp_type == $id)?"selected":"";
                                                                                    echo '<option value="'.$id.'" '.$selected.'>'.$type.'</option>';
                                                                                endforeach;
                                                                            ?>
                                                                        </select>
                                                                    </div>

                                                                    <div class="col-md-3 form-group">
                                                                        <label for="emp_dept_id">Department</label>
                                                                        <select name="emp_dept_id" id="emp_dept_id" class="form-control select2 req">
                                                                            <option value="">Select Department</option>
                                                                            <?php
                                                                                foreach($departmentList as $row):
                                                                                    $selected = (!empty($empData->emp_dept_id) && $row->id == $empData->emp_dept_id)?"selected":"";
                                                                                    echo '<option value="'.$row->id.'" '.$selected.'>'.$row->name.'</option>';
                                                                                endforeach;
                                                                            ?>
                                                                        </select>
                                                                    </div>

                                                                    <div class="col-md-3 from-group">
                                                                        <label for="emp_designation">Designation</label>
                                                                        <select name="emp_designation" id="emp_designation" class="form-control select2 req">
                                                                            <option value="">Select Designation</option>
                                                                            <?php
                                                                                foreach($designationList as $row):
                                                                                    $selected = (!empty($empData->emp_designation) && $row->id == $empData->emp_designation)?"selected":"";
                                                                                    echo '<option value="'.$row->id.'" '.$selected.'>'.$row->title.'</option>';
                                                                                endforeach;
                                                                            ?>
                                                                        </select>
                                                                        <input type="hidden" id="designationTitle" name="designationTitle" value="" />
                                                                    </div>

                                                                    <div class="col-md-3 form-group">
                                                                        <label for="emp_sys_desc_id">System Designation</label>
                                                                        <select name="emp_sys_desc_id" id="emp_sys_desc_id" class="form-control select2">
                                                                            <option value="">System Designation</option>
                                                                            <?php
                                                                                foreach($this->systemDesignation as $key=>$value):
                                                                                    $selected = (!empty($empData->emp_sys_desc_id) && $empData->emp_sys_desc_id == $key)?"selected":"";
                                                                                    echo '<option value="'.$key.'" '.$selected.'>'.$value.'</option>';
                                                                                endforeach;
                                                                            ?>
                                                                        </select>
                                                                    </div>
                                                                    <div class="col-md-3 form-group">
                                                                        <label for="emp_type">Payment Mode</label>
                                                                        <select name="sal_pay_mode" id="sal_pay_mode" class="form-control select2 req " >
                                                                            <option value="">Select Type</option>
                                                                            <?php
                                                                                foreach($this->paymentMode as $mode):
                                                                                    $selected = ((!empty($empData->sal_pay_mode)) && $empData->sal_pay_mode == $mode)?"selected":"";
                                                                                    echo '<option value="'.$mode.'" '.$selected.'>'.$mode.'</option>';
                                                                                endforeach;
                                                                            ?>
                                                                        </select>
                                                                    </div>
                                                                    <div class="col-md-6 form-group">
                                                                        <label for="bank_name">Bank Name</label>
                                                                        <input type="text" name="bank_name" class="form-control" value="<?=(!empty($empData->bank_name))?$empData->bank_name:""?>" />
                                                                    </div>
                                                                    <div class="col-md-3 form-group">
                                                                        <label for="account_no">Account No</label>
                                                                        <input type="text" name="account_no" class="form-control" value="<?=(!empty($empData->account_no))?$empData->account_no:""?>" />
                                                                    </div>
                                                                    <div class="col-md-3 form-group">
                                                                        <label for="ifsc_code">Ifsc Code</label>
                                                                        <input type="text" name="ifsc_code" class="form-control" value="<?=(!empty($empData->ifsc_code))?$empData->ifsc_code:""?>" />
                                                                    </div>
                                                                </div>
                                                            </form>
                                                        </div>
                                                        <div class="card-footer">   
                                                            <button type="button" class="btn waves-effect waves-light btn-outline-success btn-save float-right" onclick="customStore({'formId':'workProfile','fnsave':'editProfile'},);"><i class="fa fa-check"></i> Save </button>
                                                        </div>
                                                    </div>
                                                </div>
                                                <!-- Work Profile End -->
                                                
                                                <!-- Employee Document Start -->
                                                <div class="tab-pane fade" id="documents" role="tabpanel" aria-labelledby="pills-documents-tab">
                                                    <form id="empDocs" data-res_function="resEmpDocs" enctype="multipart/form-data">
                                                        <div class="card-body">
                                                            <div class="row">
                                                                <input type="hidden" name="id" id="id" value="" />
                                                                <input type="hidden" name="emp_id" id="emp_id" value="<?=(!empty($empData->id))?$empData->id:""?>" />
                                                                <input type="hidden" name="form_type" id="form_type" value="empDocs" />

                                                                <div class="col-md-4 form-group">
                                                                    <label for="doc_type">Document Type</label>
                                                                    <select name="doc_type" id="doc_type" class="form-control req">
                                                                        <option value="">Select Document Type </option>
                                                                        <option value="1">Extra Document</option>
                                                                        <option value="2">Aadhar Card</option>
                                                                        <option value="3">Basic Rules</option>
                                                                    </select>               
                                                                </div>
                                                                <div class="col-md-4 form-group">
                                                                    <label for="doc_name">Document Name</label>
                                                                    <input type="text" name="doc_name" id="doc_name" class="form-control req" value="" />
                                                                </div>
                                                                <div class="col-md-4 form-group">
                                                                    <label for="doc_no">Document No.</label>
                                                                    <input type="text" name="doc_no" id="doc_no" class="form-control req" value="" />
                                                                </div>
                                                                <div class="col-md-4 form-group">
                                                                    <label for="">Document File</label>
                                                                    <div class="input-group">
                                                                        <div class="custom-file">
                                                                            <input type="file" name="doc_file" id="doc_file" class="custom-file-input form-control" accept=".jpg, .jpeg, .png" />
                                                                            <!-- <label class="custom-file-label" for="doc_file">Choose file</label> -->
                                                                        </div>
                                                                    </div>
                                                                    <div class="error doc_file"></div>
                                                                </div>
                                                                
                                                                <div class="col-md-2 form-group">
                                                                    <button type="button" class="btn waves-effect waves-light btn-outline-success btn-save float-right m-t-20" onclick="customStore({'formId':'empDocs','fnsave':'editProfile'},);"><i class="fa fa-check"></i> Save </button>
                                                                </div>
                                                            </div>
                                                        </div>
                                                    </form>
                                                    <hr>
                                                    <div class="card-body">
                                                        <div class="table-responsive">
                                                            <table id="empDocDetail" class="table table-bordered align-items-center">
                                                                <thead class="thead-dark">
                                                                    <tr>
                                                                        <th style="width:5%;">#</th>
                                                                        <th class="text-center">Document Name</th>
                                                                        <th class="text-center">Document No.</th>                        
                                                                        <th class="text-center">Document Type</th>
                                                                        <th class="text-center">Document File</th>
                                                                        <th class="text-center" style="width:10%;">Action</th>
                                                                    </tr>
                                                                </thead>
                                                                <tbody id="docBody">
                                                                </tbody>
                                                            </table>
                                                        </div>
                                                    </div>
                                                </div>
                                                <!-- Employee Document End -->

                                                <!-- Nomination Start -->
                                                <div class="tab-pane fade" id="nomination" role="tabpanel" aria-labelledby="pills-nomination-tab">
                                                    <form id="empNomination" data-res_function="resEmpNomination">
                                                        <div class="card-body">
                                                            <div class="row">
                                                                <input type="hidden" name="id" id="id"  value="" />
                                                                <input type="hidden" name="emp_id" id="emp_id" value="<?= (!empty($empData->id)) ? $empData->id : ""; ?>" />
                                                                <input type="hidden" name="form_type" id="form_type" value="empNomination">

                                                                <div class="col-md-4 form-group">
                                                                    <label for="nom_name">Name</label>
                                                                    <input type="text" id="nom_name" name="nom_name" class="form-control req" placeholder="Name" value="" />
                                                                </div>
                                                                <div class="col-md-4 form-group">
                                                                    <label for="nom_gender">Gender</label>
                                                                    <select id="nom_gender" name="nom_gender" class="form-control select2">
                                                                        <?php
                                                                            foreach ($this->gender as $value) :
                                                                                echo '<option value="' . $value . '">' . $value . '</option>';
                                                                            endforeach;
                                                                        ?>
                                                                    </select>
                                                                </div>
                                                                <div class="col-md-4 form-group">
                                                                    <label for="nom_relation">Relation</label>
                                                                    <input type="text" id="nom_relation" name="nom_relation" class="form-control req" placeholder="Relation" value="" />
                                                                </div>
                                                                <div class="col-md-4 form-group">
                                                                    <label for="nom_dob">Date of Birth</label>
                                                                    <input type="date" id="nom_dob" name="nom_dob" class="form-control req" placeholder="mm-dd-yyyy" value="" />
                                                                </div>
                                                                <div class="col-md-4 form-group">
                                                                    <label for="nom_proportion">Proportion</label>
                                                                    <input type="text" id="nom_proportion" name="nom_proportion" class="form-control" placeholder="Proportion" value="" />
                                                                </div>
                                                                <div class="col-md-2">
                                                                    <button type="button" class="btn waves-effect waves-light btn-outline-success btn-save float-right m-t-20" onclick="customStore({'formId':'empNomination','fnsave':'editProfile'});"><i class="fa fa-check"></i> Save </button>
                                                                </div>
                                                            </div>
                                                        </div>
                                                    </form>
                                                    <div class="card-body">
                                                        <div class="table-responsive">
                                                            <table id="empNomTbl" class="table table-bordered align-items-center">
                                                                <thead class="thead-dark">
                                                                    <tr>
                                                                        <th style="width:5%;">#</th>
                                                                        <th>Name</th>
                                                                        <th>Gender</th>
                                                                        <th>Relation</th>
                                                                        <th>Date of Birth</th>
                                                                        <th>Proportion</th>
                                                                        <th class="text-center" style="width:10%;">Action</th>
                                                                    </tr>
                                                                </thead>
                                                                <tbody id="empNomBody">
                                                                </tbody>
                                                            </table>
                                                        </div>
                                                    </div>
                                                </div>
                                                <!-- Nomination End -->

                                                <!-- Education Start -->
                                                <div class="tab-pane fade" id="education" role="tabpanel" aria-labelledby="pills-education-tab">
                                                    <form id="empEdu" data-res_function="resEmpEdu">
                                                        <div class="card-body">
                                                            <div class="row">
                                                                <input type="hidden" name="id" id="id" class="id" value="" />
                                                                <input type="hidden" name="emp_id" id="emp_id" value="<?= (!empty($empData->id)) ? $empData->id : ""; ?>" />
                                                                <input type="hidden" name="form_type" id="form_type"  value="empEdu" />

                                                                <div class="col-md-2 form-group">
                                                                    <label for="course">Course</label>
                                                                    <input type="text" id="course" name="course" class="form-control req" placeholder="Course" value="" />
                                                                </div>
                                                                <div class="col-md-3 form-group">
                                                                    <label for="university">University/Board</label>
                                                                    <input type="text" id="university" name="university"  class="form-control" placeholder="University/Board" value="" />
                                                                </div>
                                                                <div class="col-md-3 form-group">
                                                                    <label for="passing_year">Passing Year</label>
                                                                    <input type="text" id="passing_year" name="passing_year"  class="form-control req" placeholder="Passing Year" value="" />
                                                                </div>
                                                                <div class="col-md-2 form-group">
                                                                    <label for="grade">Per./Grade</label>
                                                                    <input type="text" id="grade" name="grade"  class="form-control req" placeholder="Per./Grade" value="" />
                                                                </div>
                                                                <div class="col-md-2 form-group">
                                                                    <button type="button" class="btn waves-effect waves-light btn-outline-success btn-save float-right m-t-20" onclick="customStore({'formId':'empEdu','fnsave':'editProfile'},);"><i class="fa fa-check"></i> Save </button>
                                                                </div>
                                                            </div>
                                                        </div>
                                                    </form>
                                                    <div class="card-body">
                                                        <div class="table-responsive">
                                                            <table id="empEduTbl" class="table table-bordered align-items-center">
                                                                <thead class="thead-dark">
                                                                    <tr>
                                                                        <th style="width:5%;">#</th>
                                                                        <th>Course</th>
                                                                        <th>University/Board</th>
                                                                        <th>Passing Year</th>
                                                                        <th>Per./Grade</th>
                                                                        <th class="text-center" style="width:10%;">Action</th>
                                                                    </tr>
                                                                </thead>
                                                                <tbody id="empEduBody">
                                                                    
                                                                </tbody>
                                                            </table>
                                                        </div>
                                                    </div>
                                                </div>
                                                <!-- Education End -->

                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
<?php $this->load->view('includes/footer'); ?>
<script>
$(document).ready(function(){

    var selectedTab = localStorage.getItem('empProfileSelectedTab');
	if (selectedTab != null) {
        $("#"+selectedTab).trigger('click');
    }

    $(document).on('click','.nav-link',function(){
        var id = $(this).attr('id');
        localStorage.setItem('empProfileSelectedTab', id);
    });

    var empDocTrans = {'postData':{'emp_id':$("#empDocs #emp_id").val()},'table_id':"empDocDetail",'tbody_id':'docBody','tfoot_id':'','fnget':'getEmpDocumentsHtml'};
    getTransHtml(empDocTrans);

    var empNomTrans = {'postData':{'emp_id':$("#empNomination #emp_id").val()},'table_id':"empNomTbl",'tbody_id':'empNomBody','tfoot_id':'','fnget':'getEmpNominationsHtml'};
    getTransHtml(empNomTrans);

    var empEduTrans = {'postData':{'emp_id':$("#empEdu #emp_id").val()},'table_id':"empEduTbl",'tbody_id':'empEduBody','tfoot_id':'','fnget':'getEmpEducationsHtml'};
    getTransHtml(empEduTrans);

    $(document).on("change", ".uploadProfileInput", function () {
        var triggerInput = this;
        var currentImg = $(this).closest(".pic-holder").find(".pic").attr("src");
        var holder = $(this).closest(".pic-holder");
        var wrapper = $(this).closest(".profile-pic-wrapper");
        $(wrapper).find('[role="alert"]').remove();
        triggerInput.blur();
        var files = !!this.files ? this.files : [];
        if (!files.length || !window.FileReader) {return;}
        var emp_id = $("#profileForm #emp_id").val();
        if (/^image/.test(files[0].type)) {
            // only image file
            var reader = new FileReader(); // instance of the FileReader
            reader.readAsDataURL(files[0]); // read the local file

            reader.onloadend = function () {
                $(holder).addClass("uploadInProgress");
                $(holder).find(".pic").attr("src", this.result);
                $(holder).append('<div class="upload-loader"><div class="spinner-border text-primary" role="status"><span class="sr-only">Loading...</span></div></div>');
                
                var fd = new FormData();
                var files_pics = $('#newProfilePhoto')[0].files;
                if(files_pics.length > 0 ){
                    fd.append('emp_profile',files_pics[0]);
                    fd.append('id',emp_id);
                    fd.append('form_type',"updateProfilePic");
                    $.ajax({
                        url: base_url + controller + '/editProfile',
                        data:fd,
                        type: "POST",
                        processData:false,
                        contentType:false,
                        cache: false,
                        global:false,
                        dataType:"json",
                    }).done(function(data){
                        if(data.status===0){
                            Swal.fire({ icon: 'error', title: data.message });
                            window.location.reload();
                        }else if(data.status==1){ 
                            Swal.fire({ icon: 'success', title: data.message});
                        }
                        $(holder).removeClass("uploadInProgress");
                        $(holder).find(".upload-loader").remove();
                        $(triggerInput).val("");
                    });
                }
            };
        }
        else{
            Swal.fire({ icon: 'error', title: "Please choose the valid image." });
            $(wrapper).find('role="alert"').remove();
        }
    });

});

function resProfileDetail(data,formId){
    if(data.status==1){
        Swal.fire({ icon: 'success', title: data.message});
        window.location.reload();
    }else{
        if(typeof data.message === "object"){
            $(".error").html("");
            $.each( data.message, function( key, value ) { $("."+key).html(value); });
        }else{
            Swal.fire({ icon: 'error', title: data.message });
        }			
    }
}

function resEmpDocs(data,formId){
    if(data.status==1){
        $('#'+formId)[0].reset();

        Swal.fire({ icon: 'success', title: data.message});

        var postData = {'postData':{'emp_id':$("#empDocs #emp_id").val()},'table_id':"empDocDetail",'tbody_id':'docBody','tfoot_id':'','fnget':'getEmpDocumentsHtml'};
        getTransHtml(postData);
    }else{
        if(typeof data.message === "object"){
            $(".error").html("");
            $.each( data.message, function( key, value ) { $("."+key).html(value); });
        }else{
            Swal.fire({ icon: 'error', title: data.message });
        }			
    }
}

function resTrashEmpDocs(data){
    if(data.status==1){
        Swal.fire({ icon: 'success', title: data.message});

        var postData = {'postData':{'emp_id':$("#empDocs #emp_id").val()},'table_id':"empDocDetail",'tbody_id':'docBody','tfoot_id':'','fnget':'getEmpDocumentsHtml'};
        getTransHtml(postData);
    }else{
        if(typeof data.message === "object"){
            $(".error").html("");
            $.each( data.message, function( key, value ) { $("."+key).html(value); });
        }else{
            Swal.fire({ icon: 'error', title: data.message });
        }			
    }
}

function resEmpNomination(data,formId){
    if(data.status==1){
        $('#'+formId)[0].reset();

        Swal.fire({ icon: 'success', title: data.message});

        var postData = {'postData':{'emp_id':$("#empNomination #emp_id").val()},'table_id':"empNomTbl",'tbody_id':'empNomBody','tfoot_id':'','fnget':'getEmpNominationsHtml'};
        getTransHtml(postData);
    }else{
        if(typeof data.message === "object"){
            $(".error").html("");
            $.each( data.message, function( key, value ) { $("."+key).html(value); });
        }else{
            Swal.fire({ icon: 'error', title: data.message });
        }			
    }
}

function resTrashEmpNomination(data){
    if(data.status==1){
        Swal.fire({ icon: 'success', title: data.message});

        var postData = {'postData':{'emp_id':$("#empNomination #emp_id").val()},'table_id':"empNomTbl",'tbody_id':'empNomBody','tfoot_id':'','fnget':'getEmpNominationsHtml'};
        getTransHtml(postData);
    }else{
        if(typeof data.message === "object"){
            $(".error").html("");
            $.each( data.message, function( key, value ) { $("."+key).html(value); });
        }else{
            Swal.fire({ icon: 'error', title: data.message });
        }			
    }
}

function resEmpEdu(data,formId){
    if(data.status==1){
        $('#'+formId)[0].reset();

        Swal.fire({ icon: 'success', title: data.message});

        var empEduTrans = {'postData':{'emp_id':$("#empEdu #emp_id").val()},'table_id':"empEduTbl",'tbody_id':'empEduBody','tfoot_id':'','fnget':'getEmpEducationsHtml'};
        getTransHtml(empEduTrans);
    }else{
        if(typeof data.message === "object"){
            $(".error").html("");
            $.each( data.message, function( key, value ) { $("."+key).html(value); });
        }else{
            Swal.fire({ icon: 'error', title: data.message });
        }			
    }
}

function resTrashEmpEdu(data){
    if(data.status==1){
        Swal.fire({ icon: 'success', title: data.message});

        var empEduTrans = {'postData':{'emp_id':$("#empEdu #emp_id").val()},'table_id':"empEduTbl",'tbody_id':'empEduBody','tfoot_id':'','fnget':'getEmpEducationsHtml'};
        getTransHtml(empEduTrans);
    }else{
        if(typeof data.message === "object"){
            $(".error").html("");
            $.each( data.message, function( key, value ) { $("."+key).html(value); });
        }else{
            Swal.fire({ icon: 'error', title: data.message });
        }			
    }
}
</script>