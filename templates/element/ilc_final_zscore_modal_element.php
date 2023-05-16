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
							foreach($result as $eachoff){ ?>
							 	<th scope="col">Value</th>
								<th scope="col"><?php echo $eachoff['ro_office']; ?> (<?php echo $eachoff['office_type']; ?>)</th>
							<?php
							}
							
							?>
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
										//number format in not match display NA
										if($num != 'NA'){

											$format = floor($num * 100)/100;
										}else{
											$format = $num ;
										}
									?>

									<!-- if value is not numeric show dropdown list  Dtae: 16-03-2023-->
									<td>
										<?php echo $org_val[$i][$l]; 
										//$org_opt_val = $org_val[$i][$l];
										// if($org_opt_val == 'Positive'||$org_opt_val =='Present'){
										// 	$org_option = array('Satisfactory');
										// }else{
										// 	$org_option = array('Un-Satisfactory');
										// }

										?>
									</td>
									<td>
										<?php 
										if(is_numeric($format)){
											echo $format; 
										}else{
											echo $this->Form->control('org_val', array('type'=>'select', 'options'=>array('Satisfactory','Un-Satisfactory'), 'value'=>array('Satisfactory','Un-Satisfactory'), 'label'=>false,'class'=>'form-control org_val','required'=>true,));
											// 'options'=>$org_option,
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


