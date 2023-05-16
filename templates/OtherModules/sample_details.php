<?php echo $this->Html->css('element/sample_details/sample_details'); ?>
<div class="content-header">
	<div class="container-fluid">
		<div class="row mb-2">
			<div class="col-sm-6"><?php echo $this->Html->link('Back', array('controller' => 'dashboard', 'action'=>'home'),array('class'=>'add_btn btn btn-secondary')); ?></div>
			<div class="col-sm-6">
				<ol class="breadcrumb float-sm-right">
					<li class="breadcrumb-item"><?php echo $this->Html->link('Dashboard', array('controller' => 'dashboard', 'action'=>'home')); ?></li>
					<li class="breadcrumb-item active">Sample Details</li>
				</ol>
			</div>
		</div>
	</div>
	<section class="content form-middle">
		<div class="container-fluid">
			<div class="row">
				<div class="col-md-12 bg-light text-righ">
					<div class="text-right">
						<?php echo $this->Html->link('Add Samples To Junk', array('controller' => 'OtherModules', 'action'=>'add_sample_code'),array('class'=>'add_btn btn btn-primary mb-1')); ?>
						<?php echo $this->Form->create(); ?>
							<div class="card card-lims">
								<div class="card-header"><h3 class="card-title-new">List of Junked Samples</h3></div>
								<table id="sample_list_table" class="table table-bordered table-hover table-striped">
									<thead class="tablehead">
										<tr>
											<th>SR.No</th>
											<th>Sample Code</th>
											<th>Sample Type</th>
											<th>Commodity</th>
											<th>Location</th>
											<th>Last Action</th>
											<th>Reason for junk</th>
											<th>Remark</th>
											<th>Action</th>
										</tr>
									</thead>
									<tbody>
										<?php 
										if(!empty($added_sample_details)){
											$sr_no = 1;
											foreach($added_sample_details as $each_sample){ 
												?>
											<tr>
												<td><?php echo $sr_no; ?></td>
												<td><?php echo $each_sample['sample_code']; ?></td>
												<td><?php echo $each_sample['sample_type'];  ?></td>
												<td><?php echo "<span class='badge'>".$each_sample['commodity']."</span>"; ?></td>
												<td><?php echo $each_sample['locations']; ?></td>
												<td><?php echo $each_sample['last_action']; ?></td>
												<td><?php echo $each_sample['remark']; ?></td>
												<td><input type="textarea" placeholder="Enter Remark here."  class="form-control remark_<?php echo $sr_no; ?>"></input>
													<span id='error_remark<?php echo $sr_no; ?>' class='error invalid-feedback'></span>
													<div class="error-msg" id="error_remark_name<?php echo $sr_no; ?>"></div>
												</td>
												<td>
													<button type='button' id='<?php echo $each_sample['id']; ?>' name='unjunk'class='btn btn-success btn-xs delete_sample_id'>Unjunk</button>
												</td>
											</tr>
										<?php $sr_no++; } } ?>
									</tbody>
								</table>
							</div>
						<?php echo $this->Form->end(); ?>
					</div>
				</div>
			</div>
		</div>
	</section>
</div>
<?php echo $this->Html->script('sampleDetails/sample_details'); ?>
