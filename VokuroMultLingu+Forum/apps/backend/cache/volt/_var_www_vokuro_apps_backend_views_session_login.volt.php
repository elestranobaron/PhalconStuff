<?php echo $this->getContent(); ?>

<div align="center" class="well">

	<?php echo $this->tag->form(array('class' => 'form-search')); ?>

	<div align="left">
		<h2>Log In</h2>
	</div>

		<?php echo $form->render('email'); ?>
		<?php echo $form->render('password'); ?>
		<?php echo $form->render('go'); ?>

		<div align="center" class="remember">
			<?php echo $form->render('remember'); ?>
			<?php echo $form->label('remember'); ?>
		</div>

		<?php echo $form->render('csrf', array('value' => $this->security->getToken())); ?>

		<hr>

		<div class="forgot">
			<?php echo $this->tag->linkTo(array('session/forgotPassword', 'Forgot my password')); ?>
		</div>

	</form>

</div>