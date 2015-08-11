<?php
if (!defined('ABSPATH') && !defined('WP_UNINSTALL_PLUGIN')) {
	exit();
}
delete_option('geenapp_token');
delete_option('geenapp_submit_tab');
delete_option('geenapp_interstitial_active');
delete_option('geenapp_faldon_active');
delete_option('geenapp_faldon_position');
delete_option('geenapp_interstitial_active');
delete_option('geenapp_interstitial_frec_visit');
?>