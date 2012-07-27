<?php
/*
Plugin Name: Optional Email
Plugin URI: http://zamzamlab.com
Description: Makes email optional field for registration
Version: 1.0
Author: <a href="http://lab.ixblogs.com/">Naeel Abu Djamyl</a>

GNU General Public License, Free Software Foundation <http://creativecommons.org/licenses/GPL/2.0/>
This program is free software; you can redistribute it and/or modify
it under the terms of the GNU General Public License as published by
the Free Software Foundation; either version 2 of the License, or
(at your option) any later version.

This program is distributed in the hope that it will be useful,
but WITHOUT ANY WARRANTY; without even the implied warranty of
MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
GNU General Public License for more details.

You should have received a copy of the GNU General Public License
along with this program; if not, write to the Free Software
Foundation, Inc., 59 Temple Place, Suite 330, Boston, MA  02111-1307  USA
*/



add_filter( 'comment_form_default_fields', 'oe_comment_form' );
function oe_comment_form( $fields ){
	unset($fields['email']);
	return $fields;
}

add_filter('registration_errors','oe_registration_errors');
function oe_registration_errors($errors){
	unset($errors->errors['empty_email']);
	if( !trim($_POST['user_pass']) )
			$errors->add( 'empty_pass', __( '<strong>ERROR</strong>: Please type your password.' ) );
	return $errors;
}

add_filter('user_profile_update_errors','oe_profile_update_errors');
function oe_profile_update_errors($errors){
	unset($errors->errors['empty_email']);
}

add_filter('random_password','oe_reg_password');
function oe_reg_password($password){
	if( trim($_POST['user_pass']) )
		$password = $_POST['user_pass'];
	return $password;
}

add_action('register_form','oe_regform_changes',1);
function oe_regform_changes(){
	?>
	<p>
		<label for="user_pass"><?php _e('Password') ?><br />
			<input type="password" name="user_pass" id="user_pass" class="input" value="<?php echo esc_attr(stripslashes($user_pass)); ?>" size="25" tabindex="20" />
		</label>
	</p>
	<?php
}

add_action('admin_footer','oe_admin_footer',1);
function oe_admin_footer(){
	?>
	<script type="text/javascript">
		jQuery('label[for="email"] > span.description').hide();
	</script>
	<?php
}

add_action('login_footer','oe_login_footer',1);
function oe_login_footer(){
	?>
	<script type="text/javascript">
		jQuery('#reg_passmail').hide();
		var text = jQuery('label[for=user_email]').html();
		text = text.replace("E-mail", "E-mail (optional)");
		jQuery('label[for=user_email]').html(text);

	</script>
	<?php
}

add_action('login_enqueue_scripts','oe_login_scripts');
function oe_login_scripts(){
	wp_enqueue_script('jquery');
}

add_action('user_register', 'oe_autologin');
function oe_autologin($user_id){
	$user = get_userdata($user_id);
	$credentials['user_login'] = $user->user_login;
	$credentials['user_password'] = $_POST['user_pass'];
	wp_signon($credentials);
}