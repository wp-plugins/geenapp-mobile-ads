<?php
if(!defined('ABSPATH') && !defined('WP_UNINSTALL_PLUGIN')) {
	exit();
}
delete_option('geenapp_token');
delete_option('geenapp_submit_tab');
?>