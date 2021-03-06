<?php 
require_once($_SERVER['DOCUMENT_ROOT'] ."/rla/php/user.php");
$user_id=fetch_current_user_id();
?>
			<div style='float:right;font-size:12px;text-align:right;'>
				<?php if ($user_id==false): ?>
				Not logged in.
				<a href='<?php echo SITE_ROOT; ?>/signup/' class='text-button' style='margin-left:2px;font-size:12px;float:right;'>[ Sign Up ]</a> 
				<span id='show_login' class='hand text-button' 
					style='margin-left:4px;font-size:12px;float:right;'>[ Login ]</span>
				<div id="login_form" style='margin-top:16px;display:none;'>
                    <div class='login-caption'>
                        Login:
                    </div><div>
                        <input type='text' id='login_input' class='login-input' />
                    </div><div class='login-caption'>
                        Password:
                    </div><div>
                        <input type='password' id='password_input' class='login-input' />
                    </div><div id='login_status'>
                    
                    </div><div style='width:225px;text-align:right;'>
                        <input type='button' id='hide_login' value='Cancel' />
                        <input id='login' type='button' value='Login'/>
                    </div>
				</div>
				<?php elseif ($user_id!=false): ?>
					Logged in as 
                    <a class='user-link'
                    href="<?php echo SITE_ROOT; ?>/user/?id=<?php echo $user_id; ?>">
                    <?php echo fetch_username($user_id); ?>
                    </a>
                    (<?php echo fetch_user_points($user_id); ?>)
					<span id='logout' class='hand text-button'> [ Logout ] </span>
				<?php endif; ?>
			</div>
