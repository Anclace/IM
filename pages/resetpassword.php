<?php 
/**
 * Template name: Reset password
 * Description:   A reset password page
 */
_moloader('mo_get_user_rp', false);
$http_post = ('POST' == $_SERVER['REQUEST_METHOD']);
$action = isset($_REQUEST['action']) ? $_REQUEST['action'] : 'lostpassword';
$checkemail = isset($_REQUEST['checkemail']);
if ( isset($_REQUEST['key']) )
	$action = 'resetpass';
if ( !in_array( $action, array( 'lostpassword', 'resetpass', 'success' ), true ) )
	$action = 'lostpassword';
$classactive1 = '';
$classactive2 = '';
$classactive3 = '';
switch ($action) {
	case 'lostpassword' :
		$errors = new WP_Error();
		if ( $http_post ) {
			$errors = retrieve_password();
			if ( !is_wp_error($errors) ) {
				$redirect_to = mo_get_user_rp().'?checkemail=confirm';
				wp_safe_redirect( $redirect_to );
				exit();
			}
		}
		if ( isset( $_REQUEST['error'] ) ) {
			if ( 'invalidkey' == $_REQUEST['error'] )
				$errors->add( 'invalidkey', __( 'Your password reset link appears to be invalid. Please request a new link below.' ) );
			elseif ( 'expiredkey' == $_REQUEST['error'] )
				$errors->add( 'expiredkey', __( 'Your password reset link has expired. Please request a new link below.' ) );
		}
		$classactive1 = ' class="active"';
		break;
	case 'resetpass' :
		$user = check_password_reset_key($_REQUEST['key'], $_REQUEST['login']);
		if ( is_wp_error($user) ) {
			if ( $user->get_error_code() === 'expired_key' ){
				wp_redirect( mo_get_user_rp() . '?action=lostpassword&error=expiredkey' );
			}
			else{
				wp_redirect( mo_get_user_rp() . '?action=lostpassword&error=invalidkey' );
			}
			exit;
		}
		$errors = new WP_Error();
		if ( isset($_POST['pass1']) && $_POST['pass1'] != $_POST['pass2'] )
			$errors->add( 'password_reset_mismatch', __( 'The passwords do not match.' ) );
		if( isset($_POST['pass1']) && strlen($_POST['pass1']) < 8 ) {  
			$errors->add( 'password_reset_mismatch2', '密码至少8位。' );
	    }
		do_action( 'validate_password_reset', $errors, $user );
		if ( ( ! $errors->get_error_code() ) && isset( $_POST['pass1'] ) && !empty( $_POST['pass1'] ) ) {
			reset_password($user, $_POST['pass1']);
			wp_redirect( mo_get_user_rp() . '?action=success' );
			exit;
		}
		$classactive2 = ' class="active"';
		break;
	case 'success' :
		$classactive3 = ' class="active"';
		break;
}
get_header();
?>
<section class="container">	
<div class="content-wrap">
	<div class="content resetpass">
		<h1 class="hide"><?php the_title(); ?></h1>
		<ul class="resetpasssteps">
			<li<?php echo $classactive1 ?>>获取密码重置邮件<span class="glyphicon glyphicon-chevron-right"></span></li>
			<li<?php echo $classactive2 ?>>设置新密码<span class="glyphicon glyphicon-chevron-right"></span></li>
			<li<?php echo $classactive3 ?>>成功修改密码</li>
		</ul>
		
		<?php 
		if( $classactive1 ){ 
			if($checkemail){
				echo '<h3><span class="text-success">已向注册邮箱发送邮件！</span></h3>';
				echo '<p>去邮箱查收邮件并点击重置密码链接</p>';
			}else if( $errors !== true ){
		?>
		<form action="<?php echo esc_url( mo_get_user_rp() . '?action=lostpassword' ); ?>" method="post">
			<?php errormsg($errors); ?>
			<h3>填写用户名或邮箱：</h3>
			<p><input type="text" name="user_login" class="form-control input-lg" placeholder="用户名或邮箱" autofocus></p>
			<p><input type="submit" value="获取密码重置邮件" class="btn btn-block btn-primary btn-lg"></p>
		</form>
		<?php 
			}
		}?>

		<?php if( $classactive2 ){ ?>
		<form action="" method="post">
			<?php errormsg($errors); ?>
			<h3>设置新密码：</h3>
			<p><input type="password" name="pass1" class="form-control input-lg" placeholder="输入新密码" autofocus></p>
			<h5>重复新密码：</h5>
			<p><input type="password" name="pass2" class="form-control input-lg" placeholder="重复新密码"></p>
			<p><input type="submit" value="确认提交" class="btn btn-block btn-primary btn-lg"></p>
		</form>
		<?php } ?>

		<?php if( $classactive3 ){ ?>
		<form>
			<h3><span class="text-success"><span class="glyphicon glyphicon-ok-circle"></span> 恭喜，您的密码已重置！</span></h3>
			<p> &nbsp; </p>
			<p class="text-center"><a class="btn btn-success btn-lg" href="<?php echo get_bloginfo('url') ?>">回首页</a></p>
		</form>
		<?php } ?>

	</div>
</div>
</section>
<?php
function errormsg($wp_error='') {
	if ( empty($wp_error) )
		$wp_error = new WP_Error();
	if ( $wp_error->get_error_code() ) {
		$errors = '';
		$messages = '';
		foreach ( $wp_error->get_error_codes() as $code ) {
			$severity = $wp_error->get_error_data($code);
			foreach ( $wp_error->get_error_messages($code) as $error ) {
				if ( 'message' == $severity )
					$messages .= '	' . $error . "<br />\n";
				else
					$errors .= '	' . $error . "<br />\n";
			}
		}
		if ( ! empty( $errors ) ) {
			echo '<p class="errtip">' . apply_filters( 'login_errors', $errors ) . "</p>\n";
		}
		if ( ! empty( $messages ) ) {
			echo '<p class="errtip">' . apply_filters( 'login_messages', $messages ) . "</p>\n";
		}
	}
}
function retrieve_password() {
	$errors = new WP_Error();

	if ( empty( $_POST['user_login'] ) || ! is_string( $_POST['user_login'] ) ) {
		$errors->add('empty_username', __('<strong>ERROR</strong>: Enter a username or email address.'));
	} else if ( strpos( $_POST['user_login'], '@' ) ) {
		$user_data = get_user_by( 'email', trim( $_POST['user_login'] ) );
		if ( empty( $user_data ) )
			$errors->add('invalid_email', __('<strong>ERROR</strong>: There is no user registered with that email address.'));
	} else {
		$login = trim($_POST['user_login']);
		$user_data = get_user_by('login', $login);
	}

	do_action( 'lostpassword_post', $errors );

	if ( $errors->get_error_code() )
		return $errors;

	if ( !$user_data ) {
		$errors->add('invalidcombo', __('<strong>ERROR</strong>: Invalid username or email.'));
		return $errors;
	}

	// redefining user_login ensures we return the right case in the email
	$user_login = $user_data->user_login;
	$user_email = $user_data->user_email;
	$key = get_password_reset_key( $user_data );

	if ( is_wp_error( $key ) ) {
		return $key;
	}

	if ( is_multisite() ) {
		$blogname = $GLOBALS['current_site']->site_name;
	}
	else{
		$blogname = wp_specialchars_decode(get_option('blogname'), ENT_QUOTES);
	}

	$message = __('Someone has requested a password reset for the following account:') . "\r\n\r\n";
	$message .= sprintf( __( 'Site Name: %s'), $blogname ) . "(";
	$message .= network_home_url( '/' ) . ")\r\n\r\n";
	$message .= sprintf(__('Username: %s'), $user_login) . "\r\n\r\n";
	$message .= __('If this was a mistake, just ignore this email and nothing will happen.') . "\r\n\r\n";
	$message .= __('To reset your password, visit the following address:') . "\r\n\r\n";
	$message .= network_site_url(mo_get_user_rp() . "?action=resetpass&key=$key&login=" . rawurlencode($user_login), 'login');
	$message = str_replace( site_url('/') . site_url('/'), site_url('/'), $message );

	$title = sprintf( __('[%s] Password Reset'), $blogname );

	$title = apply_filters( 'retrieve_password_title', $title, $user_login, $user_data );

	$message = apply_filters( 'retrieve_password_message', $message, $key, $user_login, $user_data );

	if ( $message && !wp_mail( $user_email, wp_specialchars_decode( $title ), $message ) )
		wp_die( __('The email could not be sent.') . "<br />\n" . __('Possible reason: your host may have disabled the mail() function.') );

	return true;
}
?>
<?php
get_footer();