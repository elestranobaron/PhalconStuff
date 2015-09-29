<div class="navbar">
    <div class="navbar-inner">
      <div class="container" style="width: auto;">
        <a class="btn btn-navbar" data-toggle="collapse" data-target=".nav-collapse">
          <span class="icon-bar"></span>
          <span class="icon-bar"></span>
          <span class="icon-bar"></span>
        </a>
        <?php echo $this->tag->linkTo(array(null, 'class' => 'brand', 'Vökuró')); ?>
        <div class="nav-collapse">
          <ul class="nav"><?php $menus = array('Home' => 'index', 'About' => 'about'); ?><?php foreach ($menus as $key => $value) { ?>
              <?php if ($value == $this->dispatcher->getControllerName()) { ?>
              <li class="active"><?php echo $this->tag->linkTo(array($value, $key)); ?></li>
              <?php } else { ?>
              <li><?php echo $this->tag->linkTo(array($value, $key)); ?></li>
              <?php } ?><?php } ?></ul>

          <ul class="nav pull-right"><?php if (!(empty($logged_in))) { ?>
            <li><?php echo $this->tag->linkTo(array('users', 'Users Panel')); ?></li>
            <li><?php echo $this->tag->linkTo(array('session/logout', 'Logout')); ?></li>
            <?php } else { ?>
            <li><?php echo $this->tag->linkTo(array('session/login', 'Login')); ?></li>
            <?php } ?>
          </ul>
        </div><!-- /.nav-collapse -->
      </div>
    </div><!-- /navbar-inner -->
  </div>

<div class="container main-container">
  <?php echo $this->getContent(); ?>
</div>

<footer>
Made with love by the Phalcon Team

    <?php echo $this->tag->linkTo(array('privacy', 'Privacy Policy')); ?>
    <?php echo $this->tag->linkTo(array('terms', 'Terms')); ?>

© <?php echo date('Y'); ?> Phalcon Team.
</footer>
