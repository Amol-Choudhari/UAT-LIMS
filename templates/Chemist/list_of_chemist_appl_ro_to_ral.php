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

          <a class=" btn btn-success text-white trainingConfirm" id="trainingDatesConfirm">Confirm Dates</a>

          <a href="<?php echo $this->request->getAttribute('webroot');?>chemist/forward_applicationto_ro/<?php echo $list['id'];?>" class="btn btn-success reschedule" id="rescheduleDates">Reschedule Training Dates</a>
          
          <?php }elseif($list['is_forwordedtoral'] == 'yes' && ($reshedule_status[$i] == 'confirm') && (empty($is_training_completed[$i]))) {?>
            <a href="<?php echo $reschedule_pdf[$i] ;?>" target="_blank" type="application/pdf" rel="alternate">View Letter</a> |
          <a href="<?php echo $this->request->getAttribute('webroot');?>chemist/forward_applicationto_ro/<?php echo $list['id'];?>" type="button" class="btn btn-success text-white">Mark Training Complete</a>

        <?php }elseif(!empty($is_training_completed[$i])){?>
          <a href="<?php echo $reschedule_pdf[$i] ;?>" target="_blank" type="application/pdf" rel="alternate">View Letter</a> |
          <p class="text-white bg-green"><b>Training Completed at RAL</b></p>
        <?php }?>
        <?php if(empty($is_training_completed[$i])){ ?>
         <br> <button id="rejectApp_<?php echo $list['id']; ?>" class = "rejectModel" value='<?php echo $list['chemist_id']; ?>' appl_type ="<?php echo $appl_type[$i] ?>"> <span class="glyphicon glyphicon-remove rejectAPP"></span></button>
        <?php } ?>
      </td> 
     </tr>
     <?php $i++; }
	  } ?>
    
  </tbody>
</table>	
</div>

	
<!-- reject application model body -->
<!-- The Modal -->
<div id="myModal" class="modal">

 

<!--Modal content -->
<div class="modal-content">
 <div class="modal-header">
  
   <h4>Rejection of Application for Chemist Training</h4>
   <span class="close">&times;</span>
 </div>
 <div class="modal-body">
   <table id="rej-appl-table" class="table table-striped table-bordered">
     <thead>
       <tr>
         <th>Application Type</th>
         <th>Application Id</th>
         <th>Remark/Reason</th>
         <th>Action</th>
     </tr>
     </thead>
     <tbody>
       <tr>
       <?php  echo $this->Form->create(null, array( 'enctype'=>'multipart/form-data', 'id'=>'rejectApp','class'=>'form_name'));  ?>
         <td>
          <?php echo $this->Form->control('application_type', array('type'=>'text', 'readonly'=>true, 'class'=>'cvOn cvReq cvAlphaNum applicationType', 'value'=>'', 'label'=>false)) ;?>
         </td>
         <td><?php echo $this->Form->control('application_id', array('type'=>'text', 'readonly'=>true, 'class'=>'cvOn cvReq cvAlphaNum chemistId ', 'label'=>false)) ;?>
         
       </td>
         
         <td><?php  echo $this->Form->control('remark', array('type'=>'textarea', 'id'=>'remark', 'escape'=>false,  'placeholder'=>'Enter Remark/Reason', 'value'=>'','class'=>'cvOn cvReq cvAlphaNum reject',   'label'=>false)); ?>
         <div><b class="errorClass text-red"></b></div></td>
         <td><a class="btn btn-primary" type="submit" id="rejectBtn">Reject</a></td>
         <?php  echo $this->Form->end();  ?>
       </tr>
     </tbody>
 </table>
 </div>
 <div class="modal-footer">
 
 </div>
</div> 

</div>


</div>
<?php echo $this->Html->css('rejectAPP');?>
<?php echo $this->Html->script('rejectAPP');?>

