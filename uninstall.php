<?php
if ( ! defined( 'WP_UNINSTALL_PLUGIN' ) ) {
	exit;
}

$prefix = 'bbcustomfields';
$user_created_metaboxes = get_option($prefix.'_user_created_metaboxes');
if($user_created_metaboxes && is_array($user_created_metaboxes) && count($user_created_metaboxes) >= 1){
	foreach($user_created_metaboxes as $key=>$value){
		delete_option($prefix."_".$key);
	}
	delete_option($prefix.'_user_created_metaboxes');
}
delete_option($prefix."_user_created_pages");
delete_option($prefix."_options");
