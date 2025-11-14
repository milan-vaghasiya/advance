<?php $this->load->view('includes/header'); ?>
<div class="page-content-tab">
	<div class="container-fluid">
        <div class="row">
            <div class="col-sm-12">
				<div class="page-title-box">
					<div class="float-end">
					
					</div>
				</div>
            </div>
		</div>
        <div class="row">
            <div class="col-12">
				<div class="col-12">
					<div class="card">
					    <div class="card-body reportDiv" style="min-height:75vh">
                            <div class="panel-group wrap" id="bs-collapse">
                                <?php
                                foreach ($permission as $row) :
                                    if(!empty($row->subMenuData)):
                                ?>
                                    <div class="panel">
                                        <div class="panel-heading">
                                            <h4 class="panel-title">
                                                <a class="collapsed" data-toggle="collapse" data-parent="#bs-collapse" href="#Rmenu<?= $row->id ?>"><?= $row->menu_name ?></a>

                                            </h4>
                                        </div>
                                        <div id="Rmenu<?= $row->id ?>" class="panel-collapse collapse">
                                            <div class="panel-body">
                                            
                                                <div class="row report">
                                                    <?= $row->subMenuData ?>
                                                </div>
                                            
                                            </div>
                                        </div>
                                    </div>
                                <?php endif; endforeach; ?>
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
    $(document).ready(function() {
        $('.collapse.in').prev('.panel-heading').addClass('active');
        $('#bs-collapse').on('show.bs.collapse', function(a) {
            $(a.target).prev('.panel-heading').addClass('active');
        }).on('hide.bs.collapse', function(a) {
            $(a.target).prev('.panel-heading').removeClass('active');
        });
    });
</script>