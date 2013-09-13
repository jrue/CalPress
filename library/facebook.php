<?php
/**
 * CalPress Facebook Integration
 *
 * Includes integration of Facebook login, comments and facebook graph
 * meta tags. If finds Simple Facebook Connect plugin, uses that instead.
 *
 * CalPress is a project of the University of California
 * Berkeley Graduate School of Journalism
 * http://journalism.berkeley.edu
 *
 * Copyright (c) 2012 The Regents of the University of California
 * Released under the GPL Version 2 license
 * http://www.opensource.org/licenses/gpl-2.0.php
 * This program is distributed in the hope that it will be useful, but
 * WITHOUT ANY WARRANTY; without even the implied warranty of
 * MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.
 *
 * @package WordPress
 * @subpackage CalPress2
 * @since CalPress 0.9.7
 */

/**
 * Checks to see if Facebook info was set in Theme Options. If so, then return true
 *
 * @return bool true if they are both set, false if not.
 * @since CalPress 0.9.7
 */
function capress_is_facebook_set(){
	$options = unserialize(CALPRESSTHEMEOPTIONS);
	if(isset($options['facebook_id']) && isset($options['facebook_secret']))
		if($options['facebook_id'] !== "" && $options['facebook_secret'] !== "")
			return true;


	return false;
}

if (!function_exists('sfc_version') && capress_is_facebook_set()): //if sfc plug installed, use that instead

/**
 * Add the JavaScript SDK to each page. Insert after the opening body tag
 *
 * @param $extra string Extra info to include in the window.fbAsyncInit
 * @since CalPress 0.9.7
 */
function calpress_facebook_javascript_sdk($extra = ""){
	$options = unserialize(CALPRESSTHEMEOPTIONS);
	$facebook_id = $options['facebook_id'];


	//facebook wants us to start urls with // for ssl. Extract just the url.
	preg_match('/^(http|https):\/\/(.*$)/i', home_url(), $match);

	if(isset($match[2]) && isset($facebook_id)):

		echo '
		<div id="fb-root"></div>
		<script>
		// Additional JS functions here
		window.fbAsyncInit = function() {
		FB.init({
		  appId      : \'' . $facebook_id . '\', // App ID
		  channelUrl : \'//' . $match[2] . '?calpress-channel-file=1\', // Channel File
		  status     : true, // check login status
		  cookie     : true, // enable cookies to allow the server to access the session
		  xfbml      : true  // parse XFBML
		});

		// Additional init code here
		' . $extra . '

		};

		// Load the SDK asynchronously
		(function(d){
		 var js, id = \'facebook-jssdk\', ref = d.getElementsByTagName(\'script\')[0];
		 if (d.getElementById(id)) {return;}
		 js = d.createElement(\'script\'); js.id = id; js.async = true;
		 js.src = "//connect.facebook.net/en_US/all.js";
		 ref.parentNode.insertBefore(js, ref);
		}(document));
		</script>
		';

	endif;
}
add_action('calpress_hook_after_opening_body_tag', 'calpress_facebook_javascript_sdk');
add_action('login_footer','calpress_facebook_javascript_sdk',20);

/**
 * Add the channel file facebook needs for cross-browser support
 *
 * @since CalPress 0.9.7
 */
add_action('init', 'calpress_channel_file');
function calpress_channel_file() {
	if (!empty($_GET['calpress-channel-file'])) {
		$cache_expire = 60*60*24*365;
 		header("Pragma: public");
 		header("Cache-Control: max-age=".$cache_expire);
 		header('Expires: ' . gmdate('D, d M Y H:i:s', time()+$cache_expire) . ' GMT');
		echo '<script src="//connect.facebook.net/en_US/all.js"></script>';
		exit;
	}
}

/**
 * Need jQuery on login screen
 *
 * @since CalPress 0.9.7
 */
add_action('login_enqueue_scripts','calpress_register_enqueue_scripts');
function calpress_register_enqueue_scripts() {
	wp_enqueue_script('jquery');
}

/**
 * Allows people to register for new accounts using their Faceook account
 *
 * @since CalPress 0.9.7
 */
function calpress_facebook_register_form(){
	$options = unserialize(CALPRESSTHEMEOPTIONS);

	calpress_facebook_javascript_sdk();

	if(!isset($options['facebook_id']) || $options['facebook_id'] === "") return;

	if (!get_option('users_can_register')) return;

	$fields = "[
		{'name':'name', 'view':'prefilled'},
 		{'name':'email'},
 		{'name':'username', 'description':'" . __('Choose a username', 'calpress') . "', 'type':'text'},
 		{'name':'captcha'}
 	]";

 	echo '<script>jQuery(document).ready(function($){jQuery(\'#registerform p\').hide();jQuery(\'#reg_passmail\').show();});</script>'.PHP_EOL;
	echo '<fb:registration fields="' . $fields . '" redirect_uri="' . apply_filters('calpress_register_redirect', site_url('wp-login.php?action=register', 'login')) . '" width="262"></fb:registration>';

}
add_action('register_form', 'calpress_facebook_register_form');

function calpress_add_facebook_user(){
	global $wpdb;
	$options = unserialize(CALPRESSTHEMEOPTIONS);
	if($options['facebook_id'] || $options['facebook_secret']){
		define('FACEBOOK_APP_ID', $options['facebook_id']);
		define('FACEBOOK_SECRET', $options['facebook_secret']);
	} else {
		return;
	}

	if (!empty($_POST['signed_request'])) {
		list($encoded_sig, $payload) = explode('.', $_POST['signed_request'], 2);


		// decode the data
		$sig = fb_base64_url_decode($encoded_sig);
		$data = json_decode(fb_base64_url_decode($payload), true);
		if (!isset($data['algorithm']) || strtoupper($data['algorithm']) !== 'HMAC-SHA256') {
			return;
		}

		// check sig
		$expected_sig = hash_hmac('sha256', $payload, FACEBOOK_SECRET, true);
		if ($sig !== $expected_sig) {
			return;
		}

		if (isset($data['registration'])) {
			$info = $data['registration'];
			if (isset($info['username']) && isset($info['email'])) {

				// first check to see if this user already exists in the db
				$user_id = $wpdb->get_var( $wpdb->prepare("SELECT ID FROM $wpdb->users WHERE user_email = %s", $info['email']) );

				_log($user_id);

				if ($user_id) {
					$fbuid = $data['user_id'];

					update_user_meta($user_id, 'fbuid', $fbuid); // connect the account so we don't have to query this again

					// redirect to admin and exit
					wp_redirect( add_query_arg( array('updated' => 'true'), self_admin_url( 'profile.php' ) ) );
					exit;
				} else {
					// new user, set the registration info
					$_POST['user_login'] = $info['username'];
					$_POST['user_email'] = $info['email'];
				}
			}
		}
	}
}
add_action('login_form_register','calpress_add_facebook_user');


/**
 * Add Facebook login button to login page
 *
 * @since CalPress 0.9.7
 */
add_action('login_form', 'calpress_register_add_login_button');
function calpress_register_add_login_button() {
	global $action;

	calpress_facebook_javascript_sdk();

	if ($action == 'login') echo '<p><fb:login-button v="2" registration-url="'.site_url('wp-login.php?action=register', 'login').'" scope="email,user_website" onlogin="window.location.reload();" /></p><br />';
}

/**
 * Reauth redirect fix
 *
 * @since CalPress 0.9.7
 */
add_action('login_form_login', 'calpress_login_reauth_disable');
function calpress_login_reauth_disable() {
	$_REQUEST['reauth'] = false;
}

/**
 * Add Facebook profile information on profile page
 *
 * @since CalPress 0.9.7
 */
add_action('profile_personal_options','calpress_login_profile_page');
function calpress_login_profile_page($profile) {
?>
	<table class="form-table">
		<tr>
			<th><label><?php _e('Facebook Connect', 'calpress'); ?></label></th>
<?php
	$fbuid = get_user_meta($profile->ID, 'fbuid', true);
	if (empty($fbuid)) :
?>
			<td><p><fb:login-button scope="email" v="2" size="large" onlogin="calpress_login_update_fbuid(0);"><?php _e('Connect this WordPress account to your Facebook profile', 'calpress'); ?></fb:login-button></p></td>
		</tr>
	</table>
	<?php else : ?>
		<td><p><?php _e('Connected Facebook Profile:', 'calpress'); ?><br />
		<div style="background:#ddd; height:32px; padding:5px; display:inline-block;">
		<fb:profile-pic size="square" width="32" height="32" uid="<?php echo $fbuid; ?>" linked="true" style="vertical-align:middle;"></fb:profile-pic>&nbsp;
		<fb:name useyou="false" uid="<?php echo $fbuid; ?>" style="font-weight:bold;"></fb:name></div><br /><br />
		<input type="button" class="button-primary" value="<?php _e('Disconnect this Facebook account from your login on this site', 'calpress'); ?>" onclick="calpress_login_update_fbuid(1); return false;" />
		</p></td>
	<?php endif; ?>
	</tr>
	</table>
	<?php
}

/**
 * Allow people to disconnect their Facebook profile
 *
 * @since CalPress 0.9.7
 */
add_action('admin_footer','calpress_login_update_js', 30);
function calpress_login_update_js() {
	if (defined('IS_PROFILE_PAGE') && IS_PROFILE_PAGE) {

		calpress_facebook_javascript_sdk();
		?>
		<script type="text/javascript">
		function calpress_login_update_fbuid(disconnect) {
			var ajax_url = '<?php echo admin_url("admin-ajax.php"); ?>';
			if (disconnect == 1) {
				var fbuid = 0;
			} else {
				var fbuid = 1; // it gets it from the cookie
			}
			var data = {
				action: 'update_fbuid',
				fbuid: fbuid
			}
			jQuery.post(ajax_url, data, function(response) {
				if (response == '1') {
					location.reload(true);
				}
			});
		}
		</script>
		<?php
	}
}

/**
 * Ajax hook for updating the Facebook user id
 *
 * @since CalPress 0.9.7
 */
add_action('wp_ajax_update_fbuid', 'calpress_login_ajax_update_fbuid');
function calpress_login_ajax_update_fbuid() {
	$user = wp_get_current_user();

	$fbuid = (int)($_POST['fbuid']);

	if ($fbuid) {
		// get the id from the cookie
		$cookie = calpress_cookie_parse();
		if (empty($cookie)) { echo 1; exit; }
		$fbuid = $cookie['user_id'];
	} else {
		$fbuid = 0;
	}

	update_user_meta($user->ID, 'fbuid', $fbuid);
	echo 1;
	exit();
}


/**
 * Add facebook to the menu bar
 *
 * @since CalPress 0.9.7
 */
// add_action( 'add_admin_bar_menus', 'calpress_add_admin_bar' );
// function calpress_add_admin_bar() {

// }
add_action( 'admin_bar_menu', 'calpress_admin_bar_my_account_menu', 11 );
function calpress_admin_bar_my_account_menu( $wp_admin_bar ) {
	$user = wp_get_current_user();
	$fbuid = get_user_meta($user->ID, 'fbuid', true);

	if ($fbuid) {
		$wp_admin_bar->add_node( array(
			'parent' => 'my-account',
			'id'     => 'facebook-profile',
			'title'  => __( '<img src="//www.google.com/s2/u/0/favicons?domain=facebook.com" style="vertical-align:middle;" /> Facebook Profile' ),
			'href'   => "http://www.facebook.com/profile.php?id={$fbuid}",
			'meta'   => array(
				'class' => 'user-info-item',
			),
		) );
	}
}

// do the actual authentication
//
// note: Because of the way auth works in WP, sometimes you may appear to login
// with an incorrect username and password. This is because FB authentication
// worked even though normal auth didn't.
add_filter('authenticate','calpress_login_check',90);
function calpress_login_check($user) {
	if ( is_a($user, 'WP_User') ) { return $user; } // check if user is already logged in, skip FB stuff

	// check for the valid cookie
	$cookie = calpress_cookie_parse();
	if (empty($cookie)) return $user;

	_log("cookie: " . $cookie);

	// the cookie is signed using our secret, so if we get it back from calpress_cookie_parse, then it's authenticated. So just log the user in.
	$fbuid=$cookie['user_id'];

	if($fbuid) {
		global $wpdb;
		$user_id = $wpdb->get_var( $wpdb->prepare("SELECT user_id FROM $wpdb->usermeta WHERE meta_key = 'fbuid' AND meta_value = %s", $fbuid) );

		if ($user_id) {
			$user = new WP_User($user_id);
		} else {
			$data = calpress_remote($fbuid, '', array(
				'fields'=>'email',
				'code'=>$cookie['code'],
			));

			if (!empty($data['email'])) {
				$user_id = $wpdb->get_var( $wpdb->prepare("SELECT ID FROM $wpdb->users WHERE user_email = %s", $data['email']) );
			}

			if ($user_id) {
				$user = new WP_User($user_id);
				update_user_meta($user->ID, 'fbuid', $fbuid); // connect the account so we don't have to query this again
			}

			if (!$user_id) {
				//do_action('sfc_login_new_fb_user'); // TODO hook for creating new users if desired
				global $error;
				$error = '<strong>'.__('ERROR', 'calpress').'</strong>: '.__('Cannot log you in. There is no account on this site connected to that Facebook user identity.', 'calpress');
			}
		}
	}

	return $user;
}

/**
 * Connecting to Facebook
 *
 * @since CalPress 0.9.7
 */
function calpress_remote($obj, $connection='', $args=array(), $type = 'GET') {

	// save the access tokens for later use in the same request
	static $saved_access_tokens;

	if (empty($args['access_token']) && isset($saved_access_tokens[$obj]) && $saved_access_tokens[$obj] = $obj) {
		$args['access_token'] = $saved_access_tokens[$obj];
	}

	$options = unserialize(CALPRESSTHEMEOPTIONS);

	// get the access token
	if (empty($args['access_token']) && !empty($args['code'])) {
		$resp = wp_remote_get("https://graph.facebook.com/oauth/access_token?client_id={$options['facebook_id']}&redirect_uri=" . site_url('wp-login.php?action=register', 'login') . "&client_secret={$options['facebook_secret']}&code={$args['code']}");
		if (!is_wp_error($resp) && 200 == wp_remote_retrieve_response_code( $resp )) {
			$args['access_token'] = str_replace('access_token=','',$resp['body']);
			$saved_access_tokens[$obj] = $args['access_token'];
		} else {
			return false;
		}
	}

	$type = strtoupper($type);

	if (empty($obj)) return null;

	$url = 'https://graph.facebook.com/'. $obj;
	if (!empty($connection)) $url .= '/'.$connection;
	if ($type == 'GET') $url .= '?'.http_build_query($args);
	$args['sslverify']=0;

	if ($type == 'POST') {
		$data = wp_remote_post($url, $args);
	} else if ($type == 'GET') {
		$data = wp_remote_get($url, $args);
	}

	if ($data && !is_wp_error($data)) {
		$resp = json_decode($data['body'],true);
		return $resp;
	}

	return false;
}

// we have to change the logout to use a javascript redirect. No other way to make FB log out properly and stop giving us the cookie.
add_action('wp_logout','calpress_login_logout');
function calpress_login_logout() {
	$options = unserialize(CALPRESSTHEMEOPTIONS);

	// check for FB cookies, if not found, do nothing
	$cookie = calpress_cookie_parse();
	if (empty($cookie)) return;

	// force remove the cookie, since FB can't be relied on to do it properly
	$domain = '.'.parse_url(home_url('/'), PHP_URL_HOST);
	setcookie('fbsr_' . $options['facebook_id'], ' ', time() - 31536000, "/", $domain);

	// we have an FB login, log them out with a redirect
	//add_action('calpress_async_init','calpress_login_logout_js');
	$logout_redirect = calpress_login_logout_js();
?>
	<html><head></head><body>
	<?php calpress_facebook_javascript_sdk($logout_redirect); ?>
	</body></html>
<?php
exit;
}

// add logout code to async init
function calpress_login_logout_js() {
	$redirect_to = !empty( $_REQUEST['redirect_to'] ) ? $_REQUEST['redirect_to'] : 'wp-login.php?loggedout=true';
	$logout_code = '
FB.getLoginStatus(function(response) {
	if (response.status === \'connected\') {
		FB.logout(function(response) {
			window.location.href = \'' . $redirect_to . '\';
		});
	} else {
		window.location.href = \'' . $redirect_to . '\';
	}
});';
	return $logout_code;
}

/**
 * Placeholder for Facebook button
 *
 * @since CalPress 0.9.7
 */
add_action('comment_form','calpress_facebook_button');
function calpress_facebook_button() {
	echo '<p id="calpress_facebook_send"></p>'.PHP_EOL;
}

/**
 * Actual facebook login button
 *
 * @since CalPress 0.9.7
 */
add_action('comment_form_before_fields', 'capress_facebook_login_button',10,0); // WP 3.0 support
function capress_facebook_login_button() {
	echo '<p><fb:login-button v="2" scope="email,publish_stream" onlogin="calpress_update_user_details();">'.__('Connect with Facebook', 'sfc').'</fb:login-button></p>';
}

/**
 * Redirect user after successful login
 *
 * @since CalPress 0.9.7
 */
function calpress_register_redirect() {
	wp_redirect(site_url('wp-login.php?action=register', 'login'));
	exit;
}

/**
 * Create a div tag around comment user details
 *
 * @since CalPress 0.9.7
 */
if (!function_exists('comment_user_details_begin') && !function_exists('comment_form_after_fields')) {

add_action('comment_form_before_fields', 'comment_user_details_begin',1,0);
function comment_user_details_begin() { echo '<div id="comment-user-details">'; }

add_action('comment_form_after_fields', 'comment_user_details_end',20,0);
function comment_user_details_end() { echo '</div>'; }

}

/**
 * Facebook avatar code
 *
 * @since CalPress 0.9.7
 */
add_filter('get_avatar','calpress_comm_avatar', 10, 5);
function calpress_comm_avatar($avatar, $id_or_email, $size = '96', $default = '', $alt = false) {
	// check to be sure this is for a comment
	if ( !is_object($id_or_email) || !isset($id_or_email->comment_ID) || $id_or_email->user_id)
		 return $avatar;

	// check for fbuid comment meta
	$fbuid = get_comment_meta($id_or_email->comment_ID, 'fbuid', true);
	if ($fbuid) {
		// return the avatar code
		return "<img width='{$size}' height='{$size}' class='avatar avatar-{$size} fbavatar' src='http://graph.facebook.com/{$fbuid}/picture?type=square' />";
	}

	return $avatar;
}


/**
 * Store the FB user ID as comment meta data ('fbuid')
 *
 * @since CalPress 0.9.7
 */
add_action('comment_post','calpress_comm_add_meta', 10, 1);
function calpress_comm_add_meta($comment_id) {
	$uid   = $_POST['calpress_user_id'];
	$token = $_POST['calpress_user_token'];

	// did the user select to share the post on FB?
	if (!empty($_POST['calpress_comm_share']) && !empty($uid) && !empty($token)) {

		$comment   = get_comment($comment_id);
		$postid    = $comment->comment_post_ID;
		$permalink = get_comment_link($comment_id);

		$attachment['name']        = get_the_title($postid);
		$attachment['link']        = $permalink;
		$attachment['description'] = sfc_base_make_excerpt($post);
		$attachment['caption']     = '{*actor*} left a comment on '.get_the_title($postid);
		$attachment['message']     = get_comment_text($comment_id);

		$actions[0]['name'] = 'Read Post';
		$actions[0]['link'] = $permalink;

		$attachment['actions'] = json_encode($actions);

		$url = "https://graph.facebook.com/{$uid}/feed&access_token={$token}";
		$attachment['access_token'] = $token;

		$data = wp_remote_post($url, array('sslverify'=>0, 'body'=>$attachment));

		if (!is_wp_error($data)) {
			$resp = json_decode($data['body'],true);
			if ($resp['id']) update_comment_meta($comment_id,'_fb_post_id',$resp['id']);
		}
	}

	if ( !empty($uid) && !empty($token) ) {
		// validate token
		$url = "https://graph.facebook.com/{$uid}/?fields=name,email&access_token={$token}";

		$data = wp_remote_get($url, array('sslverify'=>0));

		if (!is_wp_error($data)) {
			$json = json_decode($data['body'],true);
			if ( !empty( $json['name'] ) ) {
				update_comment_meta($comment_id, 'fbuid', $uid);
			}
		}
	}

}

/**
 * add user fields for FB commenters
 *
 * @since CalPress 0.9.7
 */
add_filter('pre_comment_on_post','calpress_comm_fill_in_fields');
function calpress_comm_fill_in_fields($comment_post_ID) {
	if (is_user_logged_in()) return; // do nothing to WP users

	$uid   = $_POST['calpress_user_id'];
	$token = $_POST['calpress_user_token'];

	if (empty($uid) || empty($token)) return; // need both of these to get the data from FB

	$url = "https://graph.facebook.com/{$uid}/?fields=name,email&access_token={$token}";

	$data = wp_remote_get($url, array('sslverify'=>0));

	if (!is_wp_error($data)) {
		$json = json_decode($data['body'],true);
		if ($json) {
			$json            = apply_filters('calpress_comm_user_data', $json, $uid);
			$_POST['author'] = $json['name'];
			$_POST['url']    = "http://www.facebook.com/profile.php?id={$uid}";
			$_POST['email']  = $json['email'];
		}
	}
}

/**
 * hook to the footer to add our scripting
 *
 * @since CalPress 0.9.7
 */
add_action('wp_footer','calpress_comm_footer_script',30); // 30 to ensure we happen after sfc base
function calpress_comm_footer_script() {
	global $sfc_comm_comments_form;
	if ($sfc_comm_comments_form != true) return; // nothing to do, not showing comments

	if ( is_user_logged_in() ) return; // don't bother with this stuff for logged in users

	$options = get_option('sfc_options');
?>
<style type="text/css">
#fb-user { border: 1px dotted #C0C0C0; padding: 5px; display: block; }
#fb-user .fb_profile_pic_rendered { margin-right: 5px; float:left; }
#fb-user .end { display:block; height:0px; clear:left; }
</style>

<script type="text/javascript">
function sfc_update_user_details() {
	FB.getLoginStatus(function(response) {
		if (response.authResponse) {
			// Show their FB details TODO this should be configurable, or at least prettier...
			if (!jQuery('#fb-user').length) {
				jQuery('#comment-user-details').hide().after("<span id='fb-user'>" +
				"<fb:profile-pic uid='loggedinuser' facebook-logo='true' size='s'></fb:profile-pic>" +
				"<span id='fb-msg'><strong><?php echo esc_js(__('Hi', 'sfc')); ?><fb:name uid='loggedinuser' useyou='false'></fb:name>!</strong><br /><?php echo esc_js(__('You are connected with your Facebook account.', 'sfc')); ?>" +
				"<a href='#' onclick='FB.logout(function(response) { window.location = \"<?php the_permalink() ?>\"; }); return false;'> <?php echo esc_js(__('Logout', 'sfc')); ?></a>" +
				"</span><span class='end'></span></span>" +
				"<input type='hidden' name='sfc_user_id' value='"+response.authResponse.userID+"' />"+
				"<input type='hidden' name='sfc_user_token' value='"+response.authResponse.accessToken+"' />");
				jQuery('#sfc_comm_send').html('<input style="width: auto;" type="checkbox" id="sfc_comm_share" name="sfc_comm_share" /><label for="sfc_comm_share"><?php echo esc_js(__('Share Comment on Facebook', 'sfc')); ?></label>');
			}

			// Refresh the DOM
			FB.XFBML.parse();
		}
	});
}
</script>
<?php
}

/**
 * Cookie is encoded using Facebook app secret
 *
 * @since CalPress 0.9.7
 */
function calpress_cookie_parse() {
	$options = unserialize(CALPRESSTHEMEOPTIONS);
	$args = array();

	if (!empty($_COOKIE['fbsr_'. $options['facebook_id']])) {
		if (list($encoded_sig, $payload) = explode('.', $_COOKIE['fbsr_'. $options['facebook_id']], 2) ) {
			$sig = fb_base64_url_decode($encoded_sig);
			if (hash_hmac('sha256', $payload, $options['facebook_secret'], true) == $sig) {
				$args = json_decode(fb_base64_url_decode($payload), true);
			}
		}
	}

	return $args;
}

/**
 * Decodes string for facebook validation
 *
 * @since CalPress 0.9.7
 */
function fb_base64_url_decode($input) {
    return base64_decode(strtr($input, '-_', '+/'));
}


endif;

?>