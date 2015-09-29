<div class="navbar navbar-inverse">
  <div class="navbar-inner">
    <div class="container" style="width: auto;">
      <a class="btn btn-navbar" data-toggle="collapse" data-target=".nav-collapse">
        <span class="icon-bar"></span>
        <span class="icon-bar"></span>
        <span class="icon-bar"></span>
      </a>
      <?php echo $this->tag->linkTo(array(null, 'class' => 'brand', 'Vökuró')); ?>
        <div class="nav-collapse">

          <ul class="nav"><?php $menus = array('Home' => null, 'Users' => 'users', 'Profiles' => 'profiles', 'Permissions' => 'permissions'); ?><?php foreach ($menus as $key => $value) { ?>
              <?php if ($value == $this->dispatcher->getControllerName()) { ?>
              <li class="active"><?php echo $this->tag->linkTo(array($value, $key)); ?></li>
              <?php } else { ?>
              <li><?php echo $this->tag->linkTo(array($value, $key)); ?></li>
              <?php } ?><?php } ?></ul>

        <ul class="nav pull-right">
          <li class="dropdown">
            <a href="#" class="dropdown-toggle" data-toggle="dropdown"><?php echo $this->auth->getName(); ?> <b class="caret"></b></a>
            <ul class="dropdown-menu">
              <li><?php echo $this->tag->linkTo(array('users/changePassword', 'Change Password')); ?></li>
            </ul>
          </li>
          <li><?php echo $this->tag->linkTo(array('session/logout', 'Logout')); ?></li>
        </ul>
      </div>
    </div>
  </div>
</div>

<div class="container">
  <?php echo $this->getContent(); ?>
</div>