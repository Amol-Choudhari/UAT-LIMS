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
						<?php echo $this->Form->create(); ?>
							<div class="card card-lims">
								<div class="card-header"><h3 class="card-title-new">To Get Sample Code Details</h3></div>
								<div class="form-group row col-lg-6 col-lg-offset-4 m-3">
									<?php echo $this->Form->control('state', array('type'=>'text', 'id'=>'sample_code', 'label'=>false, 'class'=>'form-control','placeholder'=>'Enter Sample Code here..')); ?>
									<button type="button" class="btn btn-info " id="get_details_btn">Get Details</button>
								</div>
								<div id="sample_details">
									<div id="sample_details_content"></div>
								</div>
							</div>
						<?php echo $this->Form->end(); ?>
					</div>
				</div>
			</div>
		</div>
	</section>
</div>
<?php echo $this->Html->script("sampleDetails/sample_details") ?>
