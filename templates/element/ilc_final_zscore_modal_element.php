<!-- Modal getmodal -->
<div class="modal fade" id="finalzscoremodal" tabindex="-1" role="dialog" aria-labelledby="getmodalTitle" aria-hidden="true">
  <div class="modal-dialog modal-dialog-centered modal-lg " role="document">
    <div class="modal-content ">
      	<div class="modal-header ">
			<h5 class="modal-title " id="exampleModalLongTitle"> Z-Score <?php echo $_SESSION['user_flag']; ?> </h5>
			
			<button type="button" class="close" data-dismiss="modal" aria-label="Close">
			<span aria-hidden="true">&times;</span>
			</button>
      	</div>
		<?php echo $this->Form->create(); ?>
			<div class="modal-body">
			
				<div class="row">
					<div class="col-md-3">
						<P><b> Org Sample Code </b> <?php echo $getcommodity['org_sample_code']; ?></p>
					</div>
					<div class="col-md-3">
						<P><b> Category </b> <?php echo $getcommodity['category_name']; ?></p>
					</div>
					<div class="col-md-3">
						<P><b> Commodity </b> <?php echo $getcommodity['commodity_name']; ?>   </p>
					</div>
					<div class="col-md-3">
						<P><b> Sample Type </b> <?php echo $getcommodity['sample_type_desc']; ?></p>
					</div>
				</div>
			
				<table class="table table-bordered table-responsive">
					<thead>
						<tr>
							<th scope="col">Sr.No</th>
							<th scope="col">Test</th>
							<?php
							// change the condtion accoring to ro_office type by shreey on date [14-07-2023]
							foreach ($result as $eachoff) { ?>
								<?php if ($eachoff['ro_office'] == 'CAL Nagpur' || $eachoff['ro_office'] == 'Nagpur') { ?>
									<th scope="col"><?php echo $eachoff['ro_office']; ?> (<?php echo $office_type = 'CAL'; ?>) Actual Value</th>
									<th scope="col"><?php echo $eachoff['ro_office']; ?> (<?php echo $office_type = 'CAL'; ?>) Zscore</th>
								<?php } else { ?>

									<th scope="col"><?php echo $eachoff['ro_office']; ?> (<?php echo $eachoff['office_type']; ?>) Actual Value</th>
									<th scope="col"><?php echo $eachoff['ro_office']; ?> (<?php echo $eachoff['office_type']; ?>) Zscore</th>
								<?php } ?>
							<?php } ?>

						</tr>

					</thead>
					<tbody id="save_selected_zscore">
					<?php		

						if (isset($testarr)) {	

							$j=1;		
							$i=0;	
							foreach ($testarr as $eachtest) { ?>
							
							<tr>
								<td padding: 2px;><?php echo $j; ?></td>   
								<td><?php echo $testnames[$i]; ?> </td>
								<?php

									$l=0;
								
									foreach($smplList as $eachoff){
										
									?>
									<?php
									
										$num = $zscorearr[$i][$l];
										//number format 
										if(is_numeric($num)){

											$format = floor($num * 100)/100;
										}else{
											$format = $num ;
										}
									?>

									<!-- added condition for if actule value is string show Satisfactory & Un-Satisfactory : 14-07-2023-->
									<td>
										<?php
										echo $org_val[$i][$l];
										$org_opt_val = $org_val[$i][$l];
										if ($org_opt_val == 'Positive' || $org_opt_val == 'Present') {
											$org_option = array('Satisfactory');
										} elseif ($org_opt_val == 'Negative' || $org_opt_val == 'Absent') {
											$org_option = array('Un-Satisfactory');
										} else {
											// Set a default option if none of the above conditions match
											$org_option = array('N/A');
										}
									
										?>
									</td>

									<td>
										<?php 
										if(is_numeric($format)){
											echo $format; 
										}else{
											echo implode(', ', $org_option);
											//echo $this->Form->control('org_val', array('type' => 'select', 'options' => array('Satisfactory' => 'Satisfactory', 'Un-Satisfactory' => 'Un-Satisfactory'), 'default' => $org_option, 'label' => false, 'class' => 'form-control org_val', 'required' => true));
											//echo $this->Form->control('org_val', array('type'=>'select', 'options'=>array('Satisfactory','Un-Satisfactory'), 'value'=>'', 'label'=>false,'class'=>'form-control org_val','required'=>true,));	
										//echo $this->Form->control('org_val', array('type'=>'text', 'value'=>$org_option, 'label'=>false,'class'=>'form-control org_val','required'=>true,));
										}
										?>
									</td>

								<?php $l++;	} ?>

							</tr>

							<?php $i++; $j++; } } ?>
					</tbody>
				</table>		
			</div>
			<div class="modal-footer">
				<button type="button" class="btn btn-secondary" data-dismiss="modal">Close</button>
				<button type="submit" class="btn btn-primary" id="save_zscore" name="save">Save</button>
				
			</div>	
		<?php echo $this->Form->end(); ?>
		
		<input type="hidden" id="tstarr" value='<?php echo $testparameter;?>'>
		<input type="hidden" id="smplarr" value='<?php echo $lablist; ?>'>
    </div>
  </div>
</div>
<!-- Modal end-->


