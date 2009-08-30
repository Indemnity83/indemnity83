<?php
	$session->flash();
	$session->flash('auth');
?>

<div class="register">
<h2>Register</h2> 
	<?php
	echo $form->create('User', array('action' => 'register'));
	echo $form->input('username');
	echo $form->input('password');
	echo $form->input('password_confirm', array('type' => 'password'));
	echo $form->input('email_address');
	echo $form->input('full_name');
	echo $form->submit();
	echo $form->end();
	?>
</div>