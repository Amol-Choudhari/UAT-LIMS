<div class="container">
    <div class="row">
        <div class="col-lg-3 col-md-2"></div>
        <div class="col-lg-6 col-md-8 login-box">
            <div class="col-lg-12 login-key"><i class="fas fa-user-shield"></i></div>
            <div class="col-lg-12 login-title">Master Login (LIMS)</div>
            <div class="col-lg-12 login-form">
                <div class="col-lg-12 login-form">
                    <?php echo $this->Form->create(null, array('autocomplete'=>'off','id'=>'login')); ?>
                        <div class="form-group">
                            <input type="text"  name="username"  id="username" class="form-control" placeholder="User Email">
                        </div>
                        <div class="form-group">
                            <input type="text"  name="passcode" class="form-control" placeholder="Pass Code">
                        </div>
                        <div class="col-lg-12 loginbttm">
                            <div class="col-lg-6 login-btm login-button">
                                <?php echo $this->Form->control('Submit', array('type'=>'submit', 'id'=>'submit','name'=>'submit', 'label'=>false, 'class'=>'btn btn-success float-left')); ?>
                            </div>
                        </div>
                    <?php echo $this->form->end(); ?>
                </div>
            </div>
        </div>
    </div>
</div>




