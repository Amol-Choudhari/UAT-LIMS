<?php ?>
<div class="content-header">
	<div class="container-fluid">
		<div class="row mb-2">
			<div class="col-sm-6"><?php echo $this->Html->link('Back', array('controller' => 'dashboard', 'action'=>'home'),array('class'=>'add_btn btn btn-secondary')); ?></div>
				<div class="col-sm-6">
					<ol class="breadcrumb float-sm-right">
						<li class="breadcrumb-item"><?php echo $this->Html->link('Dashboard', array('controller' => 'dashboard', 'action'=>'home')); ?></li>
						<li class="breadcrumb-item active">Approved Chemist</li>
					</ol>
				</div>
			</div>
		</div>

        <section class="content form-middle">
			<div class="container-fluid">
				<div class="row">
				  <div class="col-md-12">
                     <div class="card card-lims">
					    <div class="card-header"><h3 class="card-title-new">List of All Approved Chemist</h3></div>
						<table id="pages_list_table" class="table table-bordered table-hover table-striped">
						  <thead class="tablehead">
                          <tr>
                            <th>SR.No</th>
                            <th>Customer ID</th>
                            <th>Chemist Name</th>
                            <th>RO/SO office</th>
                            <th>Certificate</th>
                           </tr>
                           </thead>
                           <tbody>
                           <?php 
                           $i = 0;
                            if(!empty($grant_list)){
                            foreach ($grant_list as $key => $glist) {  ?>
                            <tr>
                              <td><?php echo $i+1; ?></td>
                              <td><?php echo $glist['customer_id']; ?></td>
                              <?php if(!empty($chemist_fname[$i]) && !empty($chemist_lname[$i])){?>
                              <td><?php echo $chemist_fname[$i].'&nbsp;'.$chemist_lname[$i]; ?></td>
                              <?php } ?>
                              <?php if(!empty($ro_office_name[$i])){?>
                              <td><?php echo $ro_office_name[$i] ; ?></td>
                              <?php } ?>
                              <td><a href="<?php echo $glist['pdf_file']; ?>" target= "_blanck">Certificate</a></td>
                            </tr>
                            <?php $i++;
                             }  } ?>
                           </tbody>
                        </table>
                        </div>
                      </div>
                    </div>
                </div>
            </div>
       </section>    
    </div>
</div>

<?php echo $this->Html->script('chemist_approved');?>