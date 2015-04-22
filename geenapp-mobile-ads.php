<?php
/*
Plugin name: Geenapp Mobile Ads
Plugin URI: https://wordpress.org/plugins/geenapp-mobile-ads/
Description: Monetize your mobile traffic offering Apps to your visitors
Author: Geenapp
Author URI: http://www.geenapp.com/
Version: 0.2
License: GPL
*/
if(!defined('ABSPATH')) {
  exit;
}
if(is_admin()) {
  add_action('admin_menu', 'geenapp_menu');
	add_action('admin_init', 'geenapp_admin_init');
}
function geenapp_init() {
  global $plugin_dir;
  $plugin_dir = basename(dirname(__FILE__));
  load_plugin_textdomain('geenapp', false, $plugin_dir);
  if(('' != get_option('geenapp_token')) && wp_is_mobile()) {
    add_action('wp_head', 'geenapp_add_insert_head');
    add_action('wp_footer', 'geenapp_add_insert_footer');
  }
}
add_action('plugins_loaded', 'geenapp_init');
add_action('admin_footer', 'script_javascript');
add_action('wp_ajax_signin', 'signin_callback');
function script_javascript() {
?>
<script type="text/javascript">
  jQuery(document).ready(function($) {
    $("#signin").click(function() {
      var apikey = $("#geenapp_token").val();
      var data = {
        'action': 'signin',
        'apikey': apikey
      };
      $.post(ajaxurl, data, function(response) {
        var txt;
        switch(response) {
          case '0':
            txt = '<?php echo __('Wrong API Key. Please check your API Key.', 'geenapp'); ?>';
            $("#signin_messege").text(txt);
              break;
          case '1':
            txt = '<?php echo __('Your API Key is OK. Now you can start setting up your Geenapp Mobile Ads Plugin.', 'geenapp'); ?>';
            $("#geenapp_signin").html(txt);
              break;
          default:
            txt = '<?php echo __('Something went wrong. Please try again.', 'geenapp'); ?>';
            $("#signin_messege").text(txt);
        }
      });
    });
  });
</script>
<?php
}
function geenapp_admin_init() {
  wp_register_style('geenapp_admin_css', plugin_dir_url(__FILE__).'geenapp-mobile-ads.css');
  wp_enqueue_style('geenapp_admin_css');
  update_option('geenapp_submit_tab', 'reg');
  if(get_option('geenapp_token') == '') {
    add_action('admin_notices', '_geenapp_register');
  }
  add_action('admin_post_reg', 'prefix_admin_reg');
  add_action('admin_post_signin', 'prefix_admin_signin');
  add_action('admin_post_signout', 'prefix_admin_signout');
}
function _geenapp_register() {
  echo '<div id="message" class="error">';
  echo '  <p>'.__('Geenapp Wordpress Plugin <a href="admin.php?page=geenapp_post">needs your API Key</a>.', 'geenapp').'</p>';
  echo '</div>';
}
if(!function_exists('geenapp_menu')) {
  function geenapp_menu() {
    $page_title = __('Geenapp Mobile Ads', 'geenapp');
    $menu_title = __('Mobile Ads', 'geenapp');
    $capability = 'manage_options';
    $menu_slug  = 'geenapp_post';
    $function   = 'geenapp_menu_admin'; 
    $icon_url   = plugin_dir_url(__FILE__).'img/geenapp.png';
    add_menu_page($page_title, $menu_title, $capability, $menu_slug, $function, $icon_url, $position);
    add_action('admin_init', 'update_geenapp_publisher_info');
  }
}
if(!function_exists('update_geenapp_publisher_info')) {
  function update_geenapp_publisher_info() {
    register_setting('geenapp-publisher-info-settings', 'geenapp_faldon_active');
    register_setting('geenapp-publisher-info-settings', 'geenapp_faldon_position');
    register_setting('geenapp-publisher-info-settings', 'geenapp_submit_tab');
  }
}
if(!function_exists('geenapp_menu_admin')) {
  function geenapp_menu_admin() {
?>
  <div class="tabbed-area cur-nav-fix">
    <div class="box-wrap">
      <div id="box-account" class="tab-content"<?php if('reg' == get_option('geenapp_submit_tab')) echo ' style="z-index: 1;"'; ?>>
        <div class="geenapp_tab_contact">
<?php
    if ('' == get_option('geenapp_token')) {
      echo '<div id="geenapp_signin">
        <h1>'.__('Configure your Geenapp API Key', 'geenapp').'</h1>
        <form method="post" action="admin-ajax.php">
          <input type="hidden" value="signin" name="action">
          <p>'.__('API Key', 'geenapp').': <input autocomplete="off" name="geenapp_token" id="geenapp_token" type="text"></p>
          <p><button type="button" class="button button-primary" id="signin">'.__('Verify', 'geenapp').'</button></p>
        </form>
        <p>'.__('We need your API Key. Sign in at Geenapp to <a href="https://publisher.geenapp.com/apikey.php">retrieve your API Key</a> or sign up at Geenapp to <a href="https://publisher.geenapp.com/register.php?a=wordpress">create a new account</a>.', 'geenapp').'</p>
        <div style="top: 250px; border: none;" id="signin_messege"></div>
      </div>';
    } else {
      echo '<div id="geenapp_signin">
        <h1>'.__('Your Geenapp API Key', 'geenapp').'</h1>
        <p>'.__('Your API Key is OK. Now you can start setting up your Geenapp Mobile Ads Plugin.', 'geenapp').'</p>
      </div>';
    }
?>
        </div>
        <ul class="tabs group">
          <li class="cur"><a href="#box-account"><?php echo __('Account', 'geenapp'); ?></a></li>
          <li><a href="#box-smartbanner"><?php echo __('SmartBanner', 'geenapp'); ?></a></li>
        </ul>
      </div>
      <div id="box-smartbanner" class="tab-content"<?php if('fal' == get_option('geenapp_submit_tab')) echo ' style="z-index: 1;"'; ?>>
        <div class="geenapp_tab_contact">
          <h1><?php echo __('SmartBanner settings', 'geenapp'); ?></h1>
          <form method="post" action="options.php">
            <input type="hidden" value="fal" id="geenapp_submit_tab" name="geenapp_submit_tab">
<?php
    settings_fields('geenapp-publisher-info-settings');
    do_settings_sections('geenapp-publisher-info-settings');
?>
            <p><?php echo __('Active', 'geenapp'); ?>: <input type="checkbox" name="geenapp_faldon_active"<?php if('on' == get_option('geenapp_faldon_active')) echo ' checked'; ?>></p>
            <p><?php echo __('Position', 'geenapp'); ?>: <select name="geenapp_faldon_position" id="geenapp_faldon_position">
              <option value="top"<?php if('top' == get_option('geenapp_faldon_position')) echo ' selected'; ?>><?php echo __('Screen Top', 'geenapp'); ?></option>
              <option value="bottom"<?php if('bottom' == get_option('geenapp_faldon_position')) echo ' selected'; ?>><?php echo __('Screen Bottom', 'geenapp'); ?></option>
            </select></p>
            <p><?php submit_button(); ?></p>
          </form>
        </div>
        <ul class="tabs group">
          <li><a href="#box-account"><?php echo __('Account', 'geenapp'); ?></a></li>
          <li class="cur"><a href="#box-smartbanner"><?php echo __('SmartBanner', 'geenapp'); ?></a></li>
        </ul>
      </div>
    </div>
<?php
  }
}
function geenapp_add_insert_footer() {
  if('on' == get_option('geenapp_faldon_active') && ('bottom' == get_option('geenapp_faldon_position'))) {
    $buffer = '';
    $adUrls = get_faldon_ad_url();
    if(isset($adUrls['imageUrl'])) {
      $buffer .= '<div id="geenapp_faldon" style="width: 90%; margin-left: auto; margin-right: auto; position: fixed; left: 0; bottom: 0; width: 100%; z-index: 100;">
  <div onclick="document.getElementById(\'geenapp_faldon\').style.display=\'none\';" style="display: block; margin: auto; width: 320px; height: 50px;">
    <div style="position: relative; margin-bottom: -20px; float: right; right: -10px;"><img height="20" width="20" src="'.plugin_dir_url(__FILE__).'img/closer.png"></div>
    <img usemap="#admapfoot" id="geenapp_img" src="'.$adUrls['imageUrl'].'">
  </div>
</div>
<div id="geenapp_faldon_helper" style="height: 50px"></div>
<map name="admapfoot">
  <area shape="rect" coords="0,0,320,50" alt="geenapp" href="'.$adUrls['clickUrl'].'">
</map>';
      ob_start();
      eval('?>' . $buffer);
    }
  }
}
function geenapp_add_insert_head() {
  if(('on' == get_option('geenapp_faldon_active')) && ('top' == get_option('geenapp_faldon_position'))) {
    global $wp_query, $wpd;
    $buffer = '';
    $adUrls =  get_faldon_ad_url();
    if(isset($adUrls['imageUrl']) && $adUrls['imageUrl'] != '') {
      $buffer .= '<div id="geenapp_faldon" style="bottom: 0; right: 0; position: fixed; left: 0; top: 0;">
  <div onclick="document.getElementById(\'geenapp_faldon\').style.display=\'none\'" style="display: block; margin: auto; width: 320px; height:50px;">
    <div style="position: relative; margin-bottom: -20px; float: right; right: -10px; top: 50px;"><img height="20" width="20" src="'.plugin_dir_url(__FILE__).'img/closer.png"></div>
    <img usemap="#admaphead" id="geenapp_img" src="'.$adUrls['imageUrl'].'">
  </div>
</div>
<map name="admaphead">
  <area shape="rect" coords="0,0,320,50" alt="geenapp" href="'.$adUrls['clickUrl'].'">
</map>';
      ob_start();
      eval('?>' . $buffer);
    }
	}
}
function get_faldon_ad_url() {
  $apiUrl = 'http://wordpress.geenapptool.com/json.php';
  global $current_site;
  $var['apikey']          = get_option('geenapp_token');
  $var['ip']              = get_the_user_ip();
  $var['device']          = get_device();
  $var['gee_source']      = $current_site;
  $var['lang']            = substr(get_bloginfo('language'), 0, 2);
  $url = $apiUrl.'?apikey='.$var['apikey'].'&ip='.$var['ip'].'&device='.$var['device'].'&gee_source='.$var['gee_source'].'&lang='.$var['lang'];
  $json_offers            = get_json($url);	
  $offers                 = json_decode($json_offers, true);
  $offers                 = $offers['content'];
  $offer                  = $offers[array_rand($offers , 1)];
  return array('clickUrl' => $offer['url'], 'imageUrl' => $offer['ads']['ad320x50']);
}
function signin_callback() {
  global $wpdb;
  $url = 'http://wordpress.geenapptool.com/verifiuser.php';
  $var['apikey'] = $_REQUEST['apikey'];
  $res = send_request($url, $var);
  echo $res->verified;
  if($res->verified > 0) {
    update_option('geenapp_token', sanitize_text_field($res->apikey));
  }
  wp_die();
}
function get_device() {
  if(stripos($_SERVER['HTTP_USER_AGENT'], 'iphone')) {
    $device = 'iphone';
  } elseif(stripos($_SERVER['HTTP_USER_AGENT'], 'ipad')) {
	  $device = 'ipad';
	} elseif(stripos($_SERVER['HTTP_USER_AGENT'], 'ipod')) {
	  $device = 'ipod';
	} elseif(stripos($_SERVER['HTTP_USER_AGENT'], 'android')) {
	  $device = 'android';
	} elseif(stripos($_SERVER['HTTP_USER_AGENT'], 'windows phone')) {
	  $device = 'windows';
	} else {
	  $device = 'none';
	}
	return $device;
}
function get_the_user_ip() {
  if(!empty($_SERVER['HTTP_CLIENT_IP'])) {
    $ip = $_SERVER['HTTP_CLIENT_IP'];
	} elseif(!empty($_SERVER['HTTP_X_FORWARDED_FOR'])) {
    $ip = $_SERVER['HTTP_X_FORWARDED_FOR'];
	} else {
    $ip = $_SERVER['REMOTE_ADDR'];
	}
	return apply_filters('wpb_get_ip', $ip);
}
function get_json($url) {
  $ch = curl_init();
  curl_setopt($ch, CURLOPT_URL, $url);
  curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
  $return = curl_exec($ch);
  curl_close ($ch);
  return $return;
}
function send_request($url, $params, $return_json = false) {
	$ch = curl_init($url);
	curl_setopt($ch, CURLOPT_POST, true);
	curl_setopt($ch, CURLOPT_POSTFIELDS, $params);
	curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
	$r = curl_exec($ch);
	curl_close($ch);
	return $return_json ? json_encode(json_decode($r)) : json_decode($r);
}
?>