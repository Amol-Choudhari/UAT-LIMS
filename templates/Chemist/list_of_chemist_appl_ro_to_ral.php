<div class="container">
  <div class="col-lg-12 mx-auto text-center">
      <p class="fontSize26"><b>Chemist Application Forwarded From RO to RAL for Training</b></p>
       <hr/>
    </div>
<div class="row">
 <table class="table table-bordered">
  <thead>
    <tr>
      <th scope="col">#</th>
      <th scope="col">Chemist ID</th>
      <th scope="col">Chemist Name</th>
      <th scope="col">RO Office</th>
      <th scope="col">RAL/CAL Office</th>
      <th scope="col">Forwarded On</th>
      <th scope="col">Training End On</th>
      <th scope="col">Action</th>
      
    </tr>
  </thead>
  <tbody>
    
      <?php $i = 0;  
	  if(!empty($listOfChemistApp)){ 
      foreach ($listOfChemistApp as $key => $list) {
           $shedule_to = date('d-m-Y', strtotime(str_replace('/','.', $list['shedule_to'])));
           $forwarded = date('d-m-Y', strtotime(str_replace('/','.', $list['created'])));
        ?>
      	<tr>
      <th scope="row"><?php echo $i+1; ?></th>
      	 <td><?php echo $list['chemist_id'];?></td>
      	 <td><?php echo $list['chemist_first_name']."&nbsp".$list['chemist_last_name'];?></td>
         <td><?php echo $ro_office[$i]; ?></td>
      	 <td><?php echo $ral_offices;?></td>
      	 <td><?php echo $forwarded;?></td>
         <td><?php echo $shedule_to;?></td>
         <td>
           <?php if($list['is_forwordedtoral'] == 'yes' && (empty($is_training_completed[$i])) && empty($reshedule_status[$i])) {?>

          <a href="<?php echo $this->request->getAttribute('webroot');?>chemist/forward_applicationto_ro/<?php echo $list['id'];?>" class="btn btn-success">Confirm Training Dates</a>
          
          <?php }elseif($list['is_forwordedtoral'] == 'yes' && ($reshedule_status[$i] == 'confirm') && (empty($is_training_completed[$i]))) {?>
            <a href="<?php echo $reschedule_pdf[$i] ;?>" target="_blank" type="application/pdf" rel="alternate">View Letter</a> |
          <a href="<?php echo $this->request->getAttribute('webroot');?>chemist/forward_applicationto_ro/<?php echo $list['id'];?>" type="button" class="btn btn-success text-white">Mark Training Complete</a>

        <?php }elseif(!empty($is_training_completed[$i])){?>
          <a href="<?php echo $reschedule_pdf[$i] ;?>" target="_blank" type="application/pdf" rel="alternate">View Letter</a> |
          <p class="text-white bg-green"><b>Training Completed at RAL</b></p>
        <?php }?>
        </td> 
     </tr>
     <?php $i++; }
	  } ?>
    
  </tbody>
</table>	
</div>
	
</div>