=== BBWP Custom Fields ===

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

Allows you to add additional Meta Boxes with custom fields into Post types, Taxonomies, User Profile, Comments and more.

== Description ==

ByteBunch WP Custom Fields is a light weight plugin for Wordpress developers. This plugin include following features.

1. Create new admin pages.
2. Add Meta Boxes in custom created admin pages, Post Types, Comments and Taxonomies
3. Add new Custom Post Types.
4. Add new Taxonomies.

Please visit “Screenshots” section to learn how this plugin works.

You can use standard Wordpress functions to get the meta data added by this plugin.

1. For "Post Types" and "Pages" you can use get_post_meta($post_id, 'meta_key', true);.
2. For Taxonomies you can use get_term_meta($term_id, 'meta_key', true);
3. For Users you can use get_user_meta($user_id, 'meta_key', true);
4. For Comments you can use get_comment_meta($comment_id, 'meta_key', true);
5. For Custom Admin Pages you can use get_option('key');

== Installation ==

1. Upload the plugin files to the `/wp-content/plugins/bbwp-custom-fields` directory, or install the plugin through the WordPress plugins screen directly.
1. Activate the plugin through the 'Plugins' screen in WordPress
1. Use the "Settings" link after plugin activation to configure the plugin or click on "BBWP CF" from main admin menu.


== Screenshots ==

1. How to add new admin pages.
2. How to create new Meta boxes.
3. How to add form fields in Meta Boxes.
4. How to create new Custom Post Types.
5. How to create new Taxonomies.
