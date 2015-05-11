<?php
/*
Plugin name: Geenapp Mobile Ads
Plugin URI: https://wordpress.org/plugins/geenapp-mobile-ads/
Description: Monetize your mobile traffic offering Apps to your visitors
Author: Geenapp
Author URI: http://www.geenapp.com/
Version: 0.4
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
  load_plugin_textdomain('geenapp', false, dirname(plugin_basename(__FILE__)).'/languages');
  if(('' != get_option('geenapp_token')) && wp_is_mobile()) {
    $domain_name = $_SERVER['HTTP_HOST'];
    add_action('wp_head', 'geenapp_add_insert_head');
    add_action('wp_footer', 'geenapp_add_insert_footer');
    if ('on' == get_option('geenapp_interstitial_active')) {
      $bol_show_ad = false;
      if(isset($_COOKIE['geenapp_interstitial']) && $_COOKIE['geenapp_interstitial']) {
        $geenapp_interstitial = intval($_COOKIE['geenapp_interstitial']);
      } else {
			  $geenapp_interstitial = 0;
		  }
      if(!$geenapp_interstitial) {
        setcookie('geenapp_interstitial', 1, time() + 86400);
        $bol_show_ad = true;
      } elseif($geenapp_interstitial <= intval(get_option('geenapp_interstitial_frec_visit'))) {
        $geenapp_interstitial++;
        setcookie('geenapp_interstitial', $geenapp_interstitial, time() + 86400);
        $bol_show_ad = true;  
		  }
		  if($bol_show_ad) {
        add_action('wp_footer', 'geenapp_add_interstitial');
        add_action('wp_enqueue_scripts', 'theme_name_scripts');
		  }
    }
  }
}
function theme_name_scripts() {
  wp_register_script('geenappScript', plugin_dir_url(__FILE__).'geenapp_js.js', false);
  wp_enqueue_script('geenappScript'); 
  wp_register_style('geenappInter', plugin_dir_url(__FILE__).'geenapp_popup.css');
  wp_enqueue_style('geenappInter');
}
function geenapp_admin_init() {
  wp_register_style('geenapp_admin_css', plugin_dir_url(__FILE__).'geenapp-mobile-ads.css');
  wp_enqueue_style('geenapp_admin_css');
  if('' == get_option('geenapp_token')) {
    add_action('admin_notices', '_geenapp_register');
  }
  add_action('admin_post_reg', 'prefix_admin_reg');
  add_settings_field('geenapp-publisher-info-settings', 'geenapp-publisher-info-settings', 'update_message_callback', 'geenapp_post');
  register_setting('geenapp-publisher-info-settings', 'geenapp_token', 'sanitize_option_geenapp_token');
}
function _geenapp_register() {
  echo '<div id="message" class="error">';
  echo '  <p>'.__('Geenapp Mobile Ads plugin <a href="admin.php?page=geenapp_post">needs your API Key</a>.', 'geenapp').'</p>';
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
    add_options_page($page_title, $menu_title, $capability, $menu_slug, $function, $icon_url);
    add_action('admin_init', 'update_geenapp_publisher_info');
  }
}
if(!function_exists('update_geenapp_publisher_info')) {
  function update_geenapp_publisher_info() {
    register_setting('geenapp-publisher-info-settings', 'geenapp_faldon_active');
    register_setting('geenapp-publisher-info-settings', 'geenapp_faldon_position');
    register_setting('geenapp-publisher-info-settings', 'geenapp_interstitial_active');
    register_setting('geenapp-publisher-info-settings', 'geenapp_interstitial_frec_visit',  'update_message_callback');
  }
}
if(!function_exists('geenapp_menu_admin')) {
  function geenapp_menu_admin() {
?>
<h1><?php echo __('Geenapp Mobile Ads', 'geenapp'); ?></h1>
<h2><?php echo __('Configure your Geenapp API Key', 'geenapp'); ?></h2>
<?php
    if(get_option('geenapp_token')) {
?>
<p class="description"><?php echo __('Your API Key is OK. Now you can start setting up your Geenapp Mobile Ads plugin.', 'geenapp'); ?></p>
<?php
    }
?>
<form method="post" action="options.php">
<?php
   settings_fields('geenapp-publisher-info-settings');
    do_settings_sections('geenapp-publisher-info-settings');
?>
  <table class="form-table">
    <tr>
      <th scope="row"><label for="geenapp_token"><?php echo __('API Key', 'geenapp'); ?></label></th>
      <td><input name="geenapp_token" type="text" id="geenapp_token" class="regular-text" value="<?php echo get_option('geenapp_token'); ?>"></td>
    </tr>
  </table>
  <p class="description">
<?php 
    if('' == get_option('geenapp_token')) {
      echo __('We need your API Key. Sign in at Geenapp to <a href="https://publisher.geenapp.com/apikey.php" target="_blank">retrieve your API Key</a> or sign up at Geenapp to <a href="https://publisher.geenapp.com/register.php?a=wordpress" target="_blank">create a new account</a>.', 'geenapp');
    }
?>
  </p>
  <div style="top: 250px; border: none;" id="signin_messege"></div>
  <h2><?php echo __('SmartBanner settings', 'geenapp'); ?></h2>
 
  <table class="form-table">
    <tr>
      <th scope="row"><label for="geenapp_faldon_active"><?php echo __('Active', 'geenapp'); ?></label></th>
      <td><input type="checkbox" name="geenapp_faldon_active"<?php if('on' == get_option('geenapp_faldon_active')) echo ' checked'; ?>></td>
    </tr>
    <tr>
      <th scope="row"><label for="geenapp_faldon_position"><?php echo __('Position', 'geenapp'); ?></label></th>
      <td><select name="geenapp_faldon_position" id="geenapp_faldon_position">
        <option value="top"<?php if('top' == get_option('geenapp_faldon_position')) echo ' selected'; ?>><?php echo __('Screen Top', 'geenapp'); ?></option>
        <option value="bottom"<?php if('bottom' == get_option('geenapp_faldon_position')) echo ' selected'; ?>><?php echo __('Screen Bottom', 'geenapp'); ?></option>
      </select></td>
    </tr>
  </table>
  <h2><?php echo __('Interstitial settings', 'geenapp'); ?></h2>
<?php
    settings_fields('geenapp-publisher-info-settings');
    do_settings_sections('geenapp-publisher-info-settings');
?>
  <table class="form-table">
    <tr>
      <th scope="row"><label for="geenapp_interstitial_active"><?php echo __('Active', 'geenapp'); ?></label></th>
      <td><input type="checkbox" name="geenapp_interstitial_active"<?php if('on' == get_option('geenapp_interstitial_active')) echo ' checked'; ?>></td>
    </tr>
    <tr>
      <th scope="row"><label for="geenapp_interstitial_frec_visit"><?php echo __('Daily frequency', 'geenapp') ?></label></th>
      <td><select name="geenapp_interstitial_frec_visit" id="geenapp_interstitial_frec_visit">
        <option value="1"<?php if('1' == get_option('geenapp_interstitial_frec_visit')) echo ' selected'; ?>>1</option>
        <option value="2"<?php if('2' == get_option('geenapp_interstitial_frec_visit')) echo ' selected'; ?>>2</option>
        <option value="3"<?php if('3' == get_option('geenapp_interstitial_frec_visit')) echo ' selected'; ?>>3</option>
        <option value="4"<?php if('4' == get_option('geenapp_interstitial_frec_visit')) echo ' selected'; ?>>4</option>
        <option value="5"<?php if('5' == get_option('geenapp_interstitial_frec_visit')) echo ' selected'; ?>>5</option>
      </select></td>
    </tr>
  </table>
  <p class="description"><?php echo __('Require use of user browser cookie. Daily frequency is the maximum impressions every 24 hours.', 'geenapp'); ?></p>
  <p class="submit"><?php submit_button(); ?></p>
</form>
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
function geenapp_add_interstitial() {
  $adUrls = get_interstitial_ad_url();
  if(isset($adUrls['imageUrl']) && $adUrls['imageUrl'] != '') {
    echo '<div id="geenapp_fullscreen">
  <div id="ad">
    <div onclick="document.getElementById(\'ad\').style.display=\'none\';" style="display: inline; position: relative; float: right; top: -5px;">
      <img height="20" width="20" src="'.plugin_dir_url(__FILE__).'img/closer.png">
    </div>
    <img usemap="#interstitial" src="'.$adUrls['imageUrl'].'">
  </div>
</div>
<map name="interstitial">
  <area shape="rect" coords="0,0,300,250" alt="geenapp" href="'.$adUrls['clickUrl'].'">
</map>';
  }
}
function get_interstitial_ad_url() {
  $apiUrl = 'http://wordpress.geenapptool.com/json.php';
  $var['apikey']          = get_option('geenapp_token');
  $var['ip']              = get_the_user_ip();
  $var['device']          = get_device();
  $var['gee_source']      = $_SERVER['HTTP_HOST'];
  $var['lang']            = substr(get_bloginfo('language'), 0, 2);
  $longIp                 = ip2long($var['ip']);
  $key                    = $longIp.'-'.$var['device'];
  if(false === ($value = get_transient($key))) {
	  $url = $apiUrl.'?apikey='.$var['apikey'].'&ip='.$var['ip'].'&device='.$var['device'].'&gee_source='.$var['gee_source'].'&lang='.$var['lang'].'&gee_tool=wordpress';
	  $json_offers            = get_json($url);
	  $offers                 = json_decode($json_offers, true);
	  $offers                 = $offers['content'];
	  set_transient($key, $offers, 24 * HOUR_IN_SECONDS);
  } else {
	  $offers = get_transient($key);
  }
  $offer                  = $offers[array_rand($offers , 1)];
  return array('clickUrl' => $offer['url'], 'imageUrl' => $offer['ads']['ad300x250']);
}
function get_faldon_ad_url() {
  $apiUrl = 'http://wordpress.geenapptool.com/json.php';
  $var['apikey']          = get_option('geenapp_token');
  $var['ip']              = get_the_user_ip();
  $var['device']          = get_device();
  $var['gee_source']      = $_SERVER['HTTP_HOST'];
  $var['lang']            = substr(get_bloginfo('language'), 0, 2);
  $longIp                 = ip2long($var['ip']);
  $key                    = $longIp.'-'.$var['device'];
  if(false === ($value = get_transient($key))) {
    $url = $apiUrl.'?apikey='.$var['apikey'].'&ip='.$var['ip'].'&device='.$var['device'].'&gee_source='.$var['gee_source'].'&lang='.$var['lang'].'&gee_tool=wordpress';
	  $json_offers            = get_json($url);
	  $offers                 = json_decode($json_offers, true);
    $offers                 = $offers['content'];
	  set_transient($key, $offers, 24 * HOUR_IN_SECONDS);
  } else {
	  $offers = get_transient($key);
  }
  $offer                  = $offers[array_rand($offers , 1)];
  return array('clickUrl' => $offer['url'], 'imageUrl' => $offer['ads']['ad320x50']);
}
function sanitize_option_geenapp_token($value) {
  $url = 'http://wordpress.geenapptool.com/verifiuser.php';
  $var['apikey'] = sanitize_text_field($value);
  $res = send_request($url, $var);
  if($res->verified > 0) {
    add_settings_error('geenapp', 'geenapp',  __('Your API Key is OK. Now you can start setting up your Geenapp Mobile Ads plugin.', 'geenapp'), 'updated');
    return sanitize_text_field($res->apikey);
  } else {
    add_settings_error('geenapp', 'geenapp',  __('Wrong API Key. Please check your API Key.', 'geenapp'), 'error');
    $res = null;
    return $res;
  }
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
	} elseif(stripos($_SERVER['HTTP_USER_AGENT'], 'BB10')) {
	  $device = 'blackberry';
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
function update_message_callback($value) {
  add_settings_error('geenapp', 'geenapp', __( 'The settings have been saved', 'geenapp'), 'updated');
  return $value;
}
add_action('plugins_loaded', 'geenapp_init');
?>