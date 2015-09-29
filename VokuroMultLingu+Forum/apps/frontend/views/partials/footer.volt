{% include 'partials/statistics.volt' %}

<div id="footer" align="center" class="container-fluid">
	<hr>
	<div id="footer-container" class="row-fluid">
		<?php echo Phalcon\Tag::linkTo("..", "Home")?><br>
		<span class='version'>Powered by Phalcon {{ version() }}</span>
	</div>
</div>
