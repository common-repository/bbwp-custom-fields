<?php
/*
Plugin Name: BBWP Custom Fields
Plugin URI: https://wordpress.org/plugins/bbwp-custom-fields/
Description: Allows you to add additional Meta Boxes with custom fields into Post types, Taxonomies, User Profile, Comments and more.
Author: ByteBunch
Version: 1.3
Stable tag:        1.3
Requires at least: 4.5
Tested up to: 5.5.1
Author URI: https://bytebunch.com
Text Domain:       bbwp-custom-fields
Domain Path:       /languages
License:           GPL v2 or later
License URI:       https://www.gnu.org/licenses/gpl-2.0.txt

This program is free software; you can redistribute it and/or
modify it under the terms of the GNU General Public License
as published by the Free Software Foundation; either version
2 of the License, or (at your option) any later version.

This program is distributed in the hope that it will be useful,
but WITHOUT ANY WARRANTY; without even the implied warranty of
MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
GNU General Public License for more details.

You should have received a copy of the GNU General Public License
with this program. If not, visit: https://www.gnu.org/licenses/

*/

// exit if file is called directly
if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

// constant for plugin directory path
define('BBWP_CF_URL', plugin_dir_url(__FILE__));
define('BBWP_CF_ABS', plugin_dir_path( __FILE__ ));
define('BBWP_CF_PLUGIN_FILE', plugin_basename(__FILE__));

// include the generic functions file.
include_once BBWP_CF_ABS.'inc/functions.php';

if(is_admin_panel()){

	// add the data sanitization and validation class
	if(!class_exists('BBWPSanitization'))
		include_once BBWP_CF_ABS.'inc/classes/BBWPSanitization.php';

	// add the class to dispay data like wp list table class
	if(!class_exists('BBWPListTable'))
		include_once BBWP_CF_ABS.'inc/classes/BBWPListTable.php';

	// add the class for different field types.
	if(!class_exists('BBWPFieldTypes'))
		include_once BBWP_CF_ABS.'inc/classes/BBWPFieldTypes.php';

	//Trigger the plugin initialization class
	if(!class_exists('BBWP_CustomFields')){
		include_once BBWP_CF_ABS.'inc/classes/BBWP_CustomFields.php';
		$BBWP_CustomFields = new BBWP_CustomFields();
	}

	// Setting page for Meta Boxes, Field  and custom admin pages.
	if(!class_exists('BBWP_CF_PageSettings')){
		include_once BBWP_CF_ABS.'inc/classes/BBWP_CF_PageSettings.php';
		$BBWP_CF_PageSettings = new BBWP_CF_PageSettings();
	}

	// Setting page for Custom Post type
	if(!class_exists('BBWP_CF_CPT_Page')){
		include_once BBWP_CF_ABS.'inc/classes/BBWP_CF_CPT_Page.php';
		$BBWP_CF_CPT_Page = new BBWP_CF_CPT_Page();
	}

	// Setting page for Custom Taxonomies
	if(!class_exists('BBWP_CF_CT_Page')){
		include_once BBWP_CF_ABS.'inc/classes/BBWP_CF_CT_Page.php';
		$BBWP_CF_CT_Page = new BBWP_CF_CT_Page();
	}

	// Populate the user created Meta Boxes.
	if(!class_exists('BBWP_CF_CreateMetaBoxes')){
		include_once BBWP_CF_ABS.'inc/classes/BBWP_CF_CreateMetaBoxes.php';
		$BBWP_CF_CreateMetaBoxes = new BBWP_CF_CreateMetaBoxes();
	}

}// if is_admin_panel

// add the class to register new Custom Post Types
if(!class_exists('BBWP_CF_CustomPostType')){
	include_once BBWP_CF_ABS.'inc/classes/BBWP_CF_CustomPostType.php';
	$BBWP_CF_CustomPostType = new BBWP_CF_CustomPostType();
}

// Add the class to register new Custom Taxonomies
if(!class_exists('BBWP_CF_CustomTaxonomy')){
	include_once BBWP_CF_ABS.'inc/classes/BBWP_CF_CustomTaxonomy.php';
	$BBWP_CF_CustomTaxonomy = new BBWP_CF_CustomTaxonomy();
}
