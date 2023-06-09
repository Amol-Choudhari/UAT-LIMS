<?php ?>


<div class="progress_bar_con col-md-12 row">
	<div class="form-group">
		<a href="<?php echo $this->request->getAttribute('webroot');?>inward/sample_inward">
			<div id="sample_inward_prog_div" class="d-inline p-1 pl-3 pr-3 mr-1 bg-red pbc" role="progressbar" aria-valuenow="20" aria-valuemin="0" aria-valuemax="100">
				Sample Inward <span id="sample_inward_prog_span" class="glyphicon glyphicon-remove-sign"></span>
				<?php echo $this->form->input('sample_inward_form_status', array('type'=>'hidden', 'id'=>'sample_inward_form_status', 'value'=>$sample_inward_form_status, 'class'=>'input-field', 'label'=>false)); ?>
			</div>
		</a>
	</div>

	<?php if($_SESSION['user_flag']=='RO' || $_SESSION['user_flag']=='SO'){ ?>
		<div class="form-group">
			<a href="<?php echo $this->request->getAttribute('webroot');?>InwardDetails/sample_inward_details">
				<div id="sample_details_prog_div" class="d-inline p-1 pl-3 pr-3 mr-1 bg-red pbc" role="progressbar" aria-valuenow="40" aria-valuemin="0" aria-valuemax="100">
					Sample Details <span id="sample_details_prog_span" class="glyphicon glyphicon-remove-sign"></span>
					<?php echo $this->form->input('sample_details_form_status', array('type'=>'hidden', 'id'=>'sample_details_form_status', 'value'=>$sample_details_form_status, 'class'=>'input-field', 'label'=>false)); ?>
				</div>
			</a>
		</div>
	<?php } ?>

	<?php if($_SESSION['is_payment_applicable'] == 'yes'){ ?>
		<div class="form-group">
			<a href="<?php echo $this->request->getAttribute('webroot');?>payment/payment_details">
				<div id="payment_details_prog_div" class="d-inline p-1 pl-3 pr-3 mr-1 bg-red pbc" role="progressbar" aria-valuenow="40" aria-valuemin="0" aria-valuemax="100">
					Payment Details <span id="payment_details_prog_span" class="glyphicon glyphicon-remove-sign"></span>
					<?php echo $this->form->input('payment_details_form_status', array('type'=>'hidden', 'id'=>'payment_details_form_status', 'value'=>$payment_details_form_status, 'class'=>'input-field', 'label'=>false)); ?>
				</div>
			</a>
		</div>
	<?php } ?>
</div>
<?php echo $this->Html->Script('element/progress_bars/sample_registration_progress'); ?>
