<?php echo $this->getContent(); ?>

<div align="right">
    <?php echo $this->tag->linkTo(array('users/create', '<i class=\'icon-plus-sign\'></i> Create Users', 'class' => 'btn btn-primary')); ?>
</div>

<form method="post" action="<?php echo $this->url->get('users/search'); ?>" autocomplete="off">

    <div class="center scaffold">

        <h2>Search users</h2>

        <div class="clearfix">
            <label for="id">Id</label>
            <?php echo $form->render('id'); ?>
        </div>

        <div class="clearfix">
            <label for="name">Name</label>
            <?php echo $form->render('name'); ?>
        </div>

        <div class="clearfix">
            <label for="email">E-Mail</label>
            <?php echo $form->render('email'); ?>
        </div>

        <div class="clearfix">
            <label for="profilesId">Profile</label>
            <?php echo $form->render('profilesId'); ?>
        </div>

        <div class="clearfix">
            <?php echo $this->tag->submitButton(array('Search', 'class' => 'btn btn-primary')); ?>
        </div>

    </div>

</form>