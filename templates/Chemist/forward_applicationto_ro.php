<!-- new file added by laxmi B. on 21-12-2022 -->
<div class="container site-page">

   <div class="row">
    <div class="col-lg-12 mx-auto text-center">
      <?php if(empty($training_completed) && !empty($reshedule_status)){ ?>
      <p class="fontSize26"><b> Training Completed</b></p>
    <?php }else{ ?>
      <p class="fontSize26"><b>Reshedule The Training And Confirmed</b></p>
    <?php } ?>
       <!-- <hr/> -->
    </div>
  </div>
<?php  echo $this->Form->create(null, array( 'enctype'=>'multipart/form-data', 'id'=>'ro_toral','class'=>'form_name'));  ?>
 <div class="form-horizontal">
    <div class="card-body">
      <div class="row">
        <div class="col-md-12 row">
          <div class="col-md-2">
            <label for="field3"><span>RAL First Name <span class="required-star">*</span></span></label>
          </div>
          <div class="col-md-4">
            <?php echo $this->Form->control('ro_first_name', array('type'=>'text', 'id'=>'rofirstname', 'escape'=>false, 'value'=>$ral_fname, 'placeholder'=>'Enter First Name', 'class'=>'cvOn cvReq cvAlphaNum form-control', 'maxlength'=>255, 'readonly'=>true, 'label'=>false)); ?>
            <div class="err_cv"></div>
          </div>
          <div class="col-md-2">
            <label for="field3"><span>Last Name <span class="required-star">*</span></span></label>
          </div>
          <div class="col-md-4">
            <?php echo $this->Form->control('ro_last_name', array('type'=>'text', 'id'=>'rolastname', 'escape'=>false, 'value'=>$ral_lname, 'placeholder'=>'Enter Last Name', 'class'=>'cvOn cvReq cvAlphaNum form-control', 'maxlength'=>255, 'readonly'=>true, 'label'=>false)); ?>
            <div class="err_cv"></div>
          </div>
        </div>
      </div>
    </div>
  </div>
  <div class="form-horizontal">
    <div class="card-body">
      <div class="row">
        <div class="col-md-12 row">
          <div class="col-md-2">
            <label for="field3"><span>Chemist First Name <span class="required-star">*</span></span></label>
          </div>
          <div class="col-md-4">
            <?php echo $this->Form->control('chemist_first_name', array('type'=>'text', 'id'=>'chemistfirstname', 'escape'=>false, 'value'=>$chemist_fname, 'placeholder'=>'Enter First Name', 'class'=>'cvOn cvReq cvAlphaNum form-control', 'maxlength'=>255, 'readonly'=>true, 'label'=>false)); ?>
            <div class="err_cv"></div>
          </div>
          <div class="col-md-2">
            <label for="field3"><span>Chemist Last Name <span class="required-star">*</span></span></label>
          </div>
          <div class="col-md-4">
            <?php echo $this->Form->control('chemist_last_name', array('type'=>'text', 'id'=>'chemistlastname', 'escape'=>false, 'value'=>$chemist_lname, 'placeholder'=>'Enter Last Name', 'class'=>'cvOn cvReq cvAlphaNum form-control', 'maxlength'=>255, 'readonly'=>true, 'label'=>false)); ?>
            <div class="err_cv"></div>
          </div>
        </div>
      </div>
    </div>
  </div>

                    <div class="form-horizontal">
                    <div class="card-body">
                    <div class="row">
                    <div class="col-md-12 row">
                    <div class="col-md-2">
                    <label for="field3"><span>Chemist Id <span class="required-star">*</span></span></label>
                    </div>
                    <div class="col-md-4">
                    <?php echo $this->Form->control('chemist_id', array('type'=>'text', 'id'=>'chemistId', 'escape'=>false, 'value'=>$chemist_id, 'placeholder'=>'Enter First Name', 'class'=>'cvOn cvReq cvAlphaNum form-control', 'maxlength'=>255, 'readonly'=>true, 'label'=>false)); ?>
                    <div class="err_cv"></div>
                    </div>
                    <div class="col-md-2">
                    <?php if(!empty($reshedule_status) && empty($training_completed)){ ?>
                    <label for="field3"><span>Ro Offices <span class="required-star">*</span></span></label>
                    </div>
                    <div class="col-md-4">
                    <?php echo $this->Form->control('ro_office', array('type'=>'text', 'id'=>'roOffice', 'escape'=>false, 'value'=>$ro_office, 'placeholder'=>'Enter First Name', 'class'=>'cvOn cvReq cvAlphaNum form-control', 'maxlength'=>255, 'disabled'=>true, 'label'=>false)); ?>
                    <?php }else{ ?>
                    <label for="field3"><span>Reshedule From Date<span class="required-star">*</span></span></label>
                    </div>
                    <div class="col-md-4">
                    <?php echo $this->Form->control('reshedule_from_date',['class' => 'form-control datepicker-here', 'label' => false,'id' => 'sheduleFrom','value'=>$scheduleFrom, 'type' => 'Text']); ?>
                    <?php } ?>
                    <div class="err_cv_ro_office"></div>
                    </div>
                    </div>
                    </div>
                    </div>
                    </div>
                    <div class="form-horizontal">
                    <div class="card-body">
                    <div class="row">
                    <div class="col-md-12 row">
                    <div class="col-md-2">
                    <label for="field3"><span>Remark </span></label>
                    </div>
                    <div class="col-md-4">
                    <?php echo $this->Form->control('remark', array('type'=>'textarea', 'id'=>'remark', 'escape'=>false,  'placeholder'=>'Enter Remark', 'class'=>'cvOn cvReq cvAlphaNum form-control',   'label'=>false)); ?>
                    <div class="err_cv_remark text-red"></div>
                    </div>
                     <?php if(!empty($reshedule_status) && empty($training_completed)){ ?>
                    <div class="col-md-2">
                    <label for="field3"><span>Upload </span></label>
                    </div>
                    <div class="col-md-4">
                    <?php echo $this->Form->control('document', array('type'=>'file', 'id'=>'document', 'escape'=>false, 'value'=>'yes','label'=>false)); ?>
                    </div>
                    <?php }else{ ?>
                      <div class="col-md-2">
                       <label for="field3"><span>Reshedule To Date<span class="required-star">*</span></span></label>
                    </div>
                    <div class="col-md-4">
                    <?php echo $this->Form->control('reshedule_to_date',['class' => 'form-control datepicker-here', 'label' => false,'id' => 'sheduleTo','value'=>$scheduleTo, 'type' => 'Text']); ?>
                  
                    </div>
                    <?php } ?>
                    </div>
                    </div>
                    </div>


                    </div>
                    <?php if(!empty($reshedule_status) && empty($training_completed)){ ?>
                    <div class="form-horizontal">
                    <div class="card-body">
                    <div class="row">
                    <div class="col-md-12 row">
                    <div class="col-md-3">
                    <label for="field3"><span>Training has been completed <span class="required-star">*</span></span></label> 
                    </div>
                    <div class="col-md-1"> <?php echo $this->Form->control('training_completed', array('type'=>'checkbox', 'id'=>'trainingCompleted', 'escape'=>false, 'checked' =>false,'label'=>false)); ?>

                    </div>
                    <div class="col-md-2"></div>

                    <div class="col-md-3">
                    <button type="submit" value="submit" id="submitbtn" class="form-control btn btn-success">Training Completed
                    </button>
                    </div>
                    </div>
                    </div>
                    </div>
                    </div>
                    <?php }else{ ?>
                    <div class="col-md-2"></div>
                    <div class="col-md-3">
                    <button type="submit" value="submit" id="submitbtnn" class="form-control btn btn-success">Reschedule Dates
                    </button>
                    <?php } ?>
                    <?php  echo $this->Form->end();  ?>
                    </div>

                    <?php echo $this->Html->script('forward_applicationto_ro');?>
