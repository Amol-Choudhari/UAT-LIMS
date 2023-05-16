<!DOCTYPE html>
<html lang="en">
    <head>
    <title>Master Login</title>
    <!-- Include external files and scripts here (See HTML helper for more info.) -->
    <?php
        echo $this->fetch('meta');
        echo $this->fetch('css');
        echo $this->fetch('script');
        echo $this->Html->css('bootstrap.min');
        echo $this->Html->css('all.min');

        echo $this->Html->css('jquery-confirm.min');
        echo $this->Html->css('devs_login');
        echo $this->Html->script('bootstrap.bundle.min');
        echo $this->Html->script('jquery_main.min'); 
    ?>
    </head>
    <body class="hold-transition login-page">
        <?php echo $this->fetch('content'); ?>
    </body>
    <?php
    if (!empty($message)) {
        echo $this->element('message_boxes');
    }?>
</html>