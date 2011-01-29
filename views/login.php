<?php foreach ($styles as $style => $media) echo HTML::style($style, array('media' => $media)), "\n" ?>
<div id="login">
<form class="form_login" method="post" action="">
	<p>
		<label>Nazwa użytkownika<br>
		<input type="text" tabindex="10" size="20" value="" class="input" id="user_login" name="login"></label>
	</p>
	<p>
		<label>Hasło<br>
		<input type="password" tabindex="20" size="20" value="" class="input" id="user_pass" name="password"></label>
	</p>
	<p class="forgetmenot"><label><input type="checkbox" tabindex="90" value="forever" id="rememberme" name="rememberme"> Remember Me</label></p>
	<p class="submit">
		<input type="submit" tabindex="100" value="Log In" class="button-primary" id="wp-submit" name="wp-submit">
	</p>
</form>
</div>
