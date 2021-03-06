<div class="navbar">
    <div class="navbar-inner">
      <div class="container" style="width: auto;">
        <a class="btn btn-navbar" data-toggle="collapse" data-target=".nav-collapse">
          <span class="icon-bar"></span>
          <span class="icon-bar"></span>
          <span class="icon-bar"></span>
        </a>
        {{ link_to(null, 'class': 'brand', 'Vökuró')}}
        <div class="nav-collapse">
          <ul class="nav">

            {%- set menus = [
              'Home': 'index',
		'Forum': 'frontend',
              'About': 'about'
            ] -%}

            {%- for key, value in menus %}
              {% if value == dispatcher.getControllerName() %}
              <li class="active">{{ link_to(value, key) }}</li>
              {% else %}
              <li>{{ link_to(value, key) }}</li>
              {% endif %}
            {%- endfor -%}

          </ul>
          <ul class="nav pull-right">
            {%- if not(logged_in is empty) %}
            <li>{{ link_to('users', 'Users Panel') }}</li>
            <li>{{ link_to('session/logout', 'Logout') }}</li>
            {% else %}
            <li>{{ link_to('session/login', 'Login') }}</li>
            {% endif %}
          </ul>
        </div><!-- /.nav-collapse -->
      </div>
    </div><!-- /navbar-inner -->
  </div>

<div class="container main-container">
  {{ content() }}
</div>

<footer>
<?php echo $translate->_('withLove')?>
	<?php echo $translate['changeLang']?>: <?php echo Phalcon\Tag::linkTo("set-language/en", "English")?> | <?php echo Phalcon\Tag::linkTo("set-language/es", "Espanol")?><!-- wtf �-->
    {{ link_to("privacy", "Privacy Policy") }}
    {{ link_to("terms", "Terms") }}

© {{ date("Y") }} Phalcon Team.
</footer>
