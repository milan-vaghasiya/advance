<!-- Top Bar Start -->
<div class="topbar">            
    <!-- Navbar -->
    <nav class="navbar-custom" id="navbar-custom">    
        <ul class="list-unstyled topbar-nav float-end mb-0">
            
            <!-- <li class="hide-phone nav-item">
                <select id="financialYearSelection" class="form-control">
                    <?php
                       /*  $yearList = $this->db->get('financial_year')->result();
                        $cyKey = array_search(1,array_column($yearList,'is_active'));
                        foreach($yearList as $key=>$row):
                            if($cyKey >= $key):
                                $selected = ($this->session->userdata('financialYear') == $row->financial_year)?"selected":"";
                                echo "<option value='".$row->financial_year."' ".$selected.">".$row->year."</option>";
                            endif;
                        endforeach;	 */							
                    ?>
                </select>
            </li> -->            

            <li class="dropdown">
                <a class="nav-link dropdown-toggle nav-user" data-bs-toggle="dropdown" href="#" role="button"
                    aria-haspopup="false" aria-expanded="false">
                    <div class="d-flex align-items-center">
                        <img src="<?=base_url()?>assets/images/users/user_default.png" alt="profile-user" class="rounded-circle me-2 thumb-sm" />
                        <div>
                            <small class="d-none d-md-block font-11"><?=$this->session->userdata('roleName')?></small>
                            <span class="d-none d-md-block fw-semibold font-12"><?=$this->session->userdata('emp_name')?><i class="mdi mdi-chevron-down"></i></span>
                        </div>
                    </div>
                </a>
                <div class="dropdown-menu dropdown-menu-end">
                    <!-- <a class="dropdown-item" href="<?=base_url("hr/employees/empProfile/".$this->session->userdata('loginId'))?>">
                        <i class="ti ti-user font-16 me-1 align-text-bottom"></i> Profile
                    </a> -->
                    <a class="dropdown-item" href="javascript:void(0)" data-bs-toggle="modal" data-bs-target="#change-psw">
                        <i class="mdi mdi-key font-16 me-1 align-text-bottom"></i> Change Password
                    </a>

                    <?php 
                        if($_SERVER['HTTP_HOST'] == 'localhost'): 

                            $syncParam = "{'modal_id' : 'modal-md', 'controller' : 'dbUtility', 'call_function':'dbForm', 'fnsave' : 'syncDbQuery', 'savebtn_text' : 'SYNC', 'savebtn_icon' : 'fa fa-retweet', 'form_id' : 'dbForm', 'title' : 'SYNC DB LIVE TO LOCAL'}";

                            $exeSqlQueryParam = "{'modal_id' : 'modal-md', 'controller' : 'dbUtility', 'call_function':'loadQueryForm', 'fnsave' : 'executeSqlQuerys', 'savebtn_text' : 'Submit', 'savebtn_icon' : 'fa fa-check', 'form_id' : 'executeSqlQuerys', 'title' : 'Execute SQL Querys In Live DB'}";
                    ?>
                        <a class="dropdown-item" href="javascript:void(0)" onclick="modalAction(<?=$syncParam?>);">
                            <i class="mdi mdi-link font-16 me-1 align-text-bottom"></i> SYNC DB
                        </a>

                        <a class="dropdown-item" href="javascript:void(0)" onclick="modalAction(<?=$exeSqlQueryParam?>);">
                            <i class="mdi mdi-server font-16 me-1 align-text-bottom"></i> Execute SQL Querys
                        </a>

                        <a class="dropdown-item" href="<?=LIVE_LINK?>dbUtility/exportDBfile/Nbt-<?=date("dmY")?>/<?=MASTER_DB?>" target="_blank">
                            <i class="mdi mdi-content-save-all font-16 me-1 align-text-bottom"></i> Export Live DB
                        </a>
                    <?php endif; ?>

                    <div class="dropdown-divider mb-0"></div>

                    <a class="dropdown-item" href="<?=base_url('admin/logout')?>">
                        <i class="ti ti-power font-16 me-1 align-text-bottom"></i> Logout
                    </a>
                </div>
            </li> 
        </ul><!--end topbar-nav-->

        <ul class="list-unstyled topbar-nav mb-0">                        
            <li>
                <button class="nav-link button-menu-mobile nav-icon" id="togglemenu">
                    <i class="ti ti-menu-2"></i>
                </button>
            </li> 
            <!--<li class="hide-phone app-search">
                <form role="search" action="#" method="get">
                    <input type="search" name="search" class="form-control top-search mb-0" placeholder="Type text...">
                    <button type="submit"><i class="ti ti-search"></i></button>
                </form>
            </li>-->
            <li class="hide-phone nav-item d-none d-md-block text-facebook font-20 font-bold" style="line-height:45px;">
                <?=(!empty($headData->pageTitle)) ? $headData->pageTitle : SITENAME?>
            </li>
        </ul>
    </nav>
    <!-- end navbar-->
</div>
<!-- Top Bar End -->