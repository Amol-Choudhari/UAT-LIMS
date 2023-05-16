<!-- Modal getmodal -->
<div class="modal fade" id="getmodal" tabindex="-1" role="dialog" aria-labelledby="getmodalTitle" aria-hidden="true">
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
					<tbody>
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

									<td><?php echo $org_val[$i][$l]; ?> </td>
									<td><?php echo $format; ?> </td>

								<?php $l++;	} ?>

							</tr>
						
							

						
						<?php $i++; $j++; } } ?>
					</tbody>
				</table>		
			</div>
			<div class="modal-footer">
				<button type="button" class="btn btn-secondary" data-dismiss="modal">Close</button>
				<!-- <button type="submit" class="btn btn-primary" id="save_zscore" name="save_zscore">Save changes</button> -->
			</div>
		<?php echo $this->Form->end(); ?>
    </div>
  </div>
</div>
<!-- Modal end-->


