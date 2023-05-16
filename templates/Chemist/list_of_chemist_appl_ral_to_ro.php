<div class="container">
  <div class="col-lg-12 mx-auto text-center">
      <p class="fontSize26"><b>Chemist Application Forwarded From RAL to RO for Training</b></p>
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
      <th scope="col">Training Status</th>
      <th scope="col">Action</th>
      
    </tr>
  </thead>
  <tbody>
    
      <?php $i = 0;  
      if(!empty($listOfChemistApp)){
      foreach ($listOfChemistApp as $key => $list) {?>
      	<tr>
      <th scope="row"><?php echo $i+1; ?></th>
      	 <td><?php echo $list['chemist_id'];?></td>
      	 <td><?php echo $list['chemist_first_name']."&nbsp".$list['chemist_last_name'];?></td>
         <td><?php echo $ro_office[$i]; ?></td>
      	 <td><?php echo $ral_offices;?></td>
      	 <td><?php echo $list['created'];?></td>
         <?php if($list['training_completed'] == 1){ ?>
         <td><?php echo "Completed";?></td>
         <?php } ?>
         <td><a href="<?php echo $list['pdf_file'] ;?>" target="_blank" type="application/pdf" rel="alternate">View Letter</a> </td> 
     </tr>
     <?php $i++; } 
   } ?>
    
  </tbody>
</table>	
</div>
	
</div>