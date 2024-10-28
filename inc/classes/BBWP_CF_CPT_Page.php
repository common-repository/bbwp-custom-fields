<?php
// exit if file is called directly
if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

class BBWP_CF_CPT_Page extends BBWP_CustomFields{

  private $edit_post_type_values = array();

  public function __construct(){
    add_action('init', array($this, 'input_handle'));
    add_action( 'admin_menu', array($this,'admin_menu'));
  }// construct function end here

  /******************************************/
  /***** page_bboptions_admin_menu function start from here *********/
  /******************************************/
  public function admin_menu(){
    /* add sub menu in our wordpress dashboard main menu */
    add_submenu_page( $this->prefix, 'Custom Post Types', 'Custom Post Types', 'manage_options', $this->prefix.'cpt', array($this,'add_submenu_page') );
  }

  /******************************************/
  /***** add_submenu_page_bboptions function start from here *********/
  /******************************************/
  public function add_submenu_page(){

    echo '<div class="wrap bytebunch_admin_page_container"><div id="icon-tools" class="icon32"></div>';
    echo '<div id="poststuff">
      <div id="postbox-container" class="postbox-container">';


    $user_created_post_types = SerializeStringToArray(get_option($this->prefix('user_created_post_types')));

    echo '<h3> '.__('Add/Edit Post Types', 'bbwp-custom-fields').' </h3>';

    if(isset($_GET['action']) && $_GET['action'] == 'edit' && isset($_GET['name']) && $_GET['name'] && count($user_created_post_types) >= 1 && array_key_exists($_GET['name'], $user_created_post_types)){
      echo '<p><a href="?page='.sanitize_key($_GET['page']).'">← '.__('Back to Main Page', 'bbwp-custom-fields').'</a></p>';
      echo '<h2 class="nav-tab-wrapper bbwp_nav_wrapper">
        <a href="#add-new-custom-taxonomies" class="nav-tab">'.__('Edit Post Type', 'bbwp-custom-fields').' - '.$user_created_post_types[$_GET['name']]['label'].'</a>
      </h2>';
      BBWPUpdateErrorMessage();
      $this->CreatePostTypeForm($user_created_post_types, $_GET['name']);
      return;
    }

    BBWPUpdateErrorMessage();
    ?>
          <h2 class="nav-tab-wrapper bbwp_nav_wrapper">
            <a href="#add-new-custom-post-types" class="nav-tab"><?php _e('Add New Post Type', 'bbwp-custom-fields'); ?></a>
            <?php if($user_created_post_types && is_array($user_created_post_types) && count($user_created_post_types) >= 1){ ?>
              <a href="#existing-custom-post-types" class="nav-tab"><?php _e('Edit Custom Post Types', 'bbwp-custom-fields'); ?></a>
            <?php } ?>
          </h2>

          <div class="bbwp_tab_nav_content" id="add-new-custom-post-types">
            <?php  $this->CreatePostTypeForm($user_created_post_types); ?>
          </div>
          <div class="bbwp_tab_nav_content" id="existing-custom-post-types" style="display:none;">
            <?php
            if($user_created_post_types && is_array($user_created_post_types) && count($user_created_post_types) >= 1){
              echo '<form method="post" action=""><h3>'.__('Existing Post Types', 'bbwp-custom-fields').'</h3>';
              $tableColumns = array("name" => __("Post Type Slug/Name", 'bbwp-custom-fields'), "label" => __("Plural Label", 'bbwp-custom-fields'));
              $BBWPListTable = new BBWPListTable();
              $BBWPListTable->get_columns($tableColumns);
              $BBWPListTable->bulk_actions = array("delete" => "Delete Selected");
              $BBWPListTable->get_sortable_columns(array("name" => "name"));
              $BBWPListTable->actions = array('name' => array('delete', 'edit'));
              $BBWPListTable->prepare_items($user_created_post_types);
              $BBWPListTable->display();
              echo '<input type="hidden" name="sort_fields" value="'.esc_attr($this->prefix('user_created_post_types')).'" />';
              submit_button(__('Save Changes', 'bbwp-custom-fields'), 'primary alignright');
              echo '</form>';
            }
            ?>
          </div><!-- bbwp_tab_nav_content -->
        </div><!-- postbox-container-->
      </div><!-- poststuff-->
    </div><!-- main wrap div end here -->
    <?php
  }

  /******************************************/
  /***** CreatePostTypeForm function start from here *********/
  /******************************************/

  private function TrueFalse($svalue, $dvalue){
    $trueFalse = array('0' => 'False', '1' => 'True');
    $selected_value = $dvalue;
    if(isset($this->edit_post_type_values) && isset($this->edit_post_type_values[$svalue]) && ($this->edit_post_type_values[$svalue] == 1 || $this->edit_post_type_values[$svalue] == 0)){
      $selected_value = $this->edit_post_type_values[$svalue];
    }
    echo '<select id="'.$svalue.'" name="user_created_post_type['.$svalue.']">'.ArraytoSelectList($trueFalse, $selected_value).'</select>';
  }

  private function selectedText($svalue, $dvalue = '', $esc = true){
    $selected_value = $dvalue;
    if(isset($this->edit_post_type_values) && isset($this->edit_post_type_values[$svalue]) && $this->edit_post_type_values[$svalue]){
      $selected_value = $this->edit_post_type_values[$svalue];
    }
		if($esc != true)
    	echo $selected_value;
		else
			echo esc_attr($selected_value);
  }

  private function CreatePostTypeForm($user_created_post_types = array(), $edit_post_type = false){
    $edit_post_type_values = array();
    echo '<form method="post" action="">';
    if($edit_post_type && is_array($user_created_post_types) && count($user_created_post_types) >= 1 && array_key_exists($edit_post_type, $user_created_post_types)){
      $edit_post_type_values = $user_created_post_types[$edit_post_type];
      $this->edit_post_type_values = $edit_post_type_values;
      echo '<input type="hidden" name="update_created_post_type" value="'.esc_attr($edit_post_type).'" />';
    }else{
      $edit_post_type_values['bbwpcf_pt_supports'] = array('title', 'editor', 'thumbnail');
    }

      ?>
      <input type="hidden" name="create_new_post_type" value="<?php echo esc_attr($this->prefix('create_new_post_type')); ?>" />
      <div class="meta-box-sortables ui-sortable">
        <div class="postbox ">
          <button type="button" class="handlediv" aria-expanded="true"><span class="screen-reader-text">Toggle panel </span><span class="toggle-indicator" aria-hidden="true"></span></button>
          <h3 class="hndle"><span>Basic Settings</span></h3>
          <div class="inside">
            <table class="form-table">
              <tr>
                <th scope="row"><label for="name">Post Type Slug: <span class="require_star">*</span></label></th>
                <td>
                  <input type="text" name="user_created_post_type[name]" id="name" class="regular-text" required="required" value="<?php $this->selectedText('name'); ?>" />
                  <br /><span class="bbwpcf-field-description">The post type name/slug. Used for various queries for post type content.</span>
                  <p>Slugs should only contain alphanumeric, latin characters. Underscores should be used in place of spaces. Set "Custom Rewrite Slug" field to make slug use dashes for URLs.</p>
                </td>
              </tr>
              <tr>
                <th scope="row"><label for="label">Plural Label <span class="require_star">*</span></label></th>
                <td>
                  <input type="text" name="user_created_post_type[label]" id="label" class="regular-text" required="required" value="<?php $this->selectedText('label'); ?>" />
                  <br /><span class="bbwpcf-field-description">Used for the post type admin menu item.</span>
                </td>
              </tr>
              <tr>
                <th scope="row"><label for="label">Singular Label <span class="require_star">*</span></label></th>
                <td>
                  <input type="text" name="user_created_post_type[singular_label]" id="singular_label" class="regular-text" required="required" value="<?php $this->selectedText('singular_label'); ?>" />
                  <br /><span class="bbwpcf-field-description">Used when a singular label is needed.</span>
                </td>
              </tr>
            </table>
            <?php submit_button('Save Changes'); ?>
          </div><!-- inside -->
        </div><!-- postbox--->
      </div>


      <div class="meta-box-sortables ui-sortable">
          <div class="postbox ">
              <button type="button" class="handlediv" aria-expanded="true"><span class="screen-reader-text">Toggle panel </span><span class="toggle-indicator" aria-hidden="true"></span></button>
              <h3 class="hndle"><span>Additional Settings</span></h3>
              <div class="inside">
                <table class="form-table bbwp_cf_table_ui">
                  <tr valign="top">
                    <th scope="row"><label for="public">Public</label></th>
                      <td>
                        <?php $this->TrueFalse('public', '1'); ?>
                        <span class="bbwpcf-field-description">(default: true) Whether or not posts of this type should be shown in the admin UI and is publicly queryable.</span>
                      </td>
                  </tr>
                  <tr valign="top">
                    <th scope="row"><label for="publicly_queryable">Publicly Queryable</label></th>
                    <td>
                      <?php $this->TrueFalse('publicly_queryable', '1'); ?>
                      <span class="bbwpcf-field-description">(default: true) Whether or not queries can be performed on the front end as part of parse_request()</span>
                    </td>
                  </tr>
                  <tr valign="top">
                    <th scope="row"><label for="show_ui">Show UI</label></th>
                    <td>
                      <?php $this->TrueFalse('show_ui', '1'); ?>
                      <span class="bbwpcf-field-description">(default: true) Whether or not to generate a default UI for managing this post type.</span>
                    </td>
                  </tr>
                  <tr valign="top">
                    <th scope="row"><label for="show_in_nav_menus">Show in Nav Menus</label></th>
                    <td>
                      <?php $this->TrueFalse('show_in_nav_menus', '1'); ?>
                      <span class="bbwpcf-field-description">(default: true) Whether or not this post type is available for selection in navigation menus.</span>
                    </td>
                  </tr>
                  <tr valign="top">
                    <th scope="row"><label for="show_in_rest">Show in REST API</label></th>
                    <td>
                      <?php $this->TrueFalse('show_in_rest', '0'); ?>
                      <span class="bbwpcf-field-description">(default: false) Whether or not to show this post type data in the WP REST API.</span>
                    </td>
                  </tr>
                  <tr valign="top">
                    <th scope="row"><label for="rest_base">REST API base slug</label></th>
                    <td>
                      <input type="text" id="rest_base" name="user_created_post_type[rest_base]" value="<?php $this->selectedText('rest_base'); ?>" aria-required="false" placeholder="Slug to use in REST API URLs.">
                      <span class="visuallyhidden">Slug to use in REST API URLs.</span>
                    </td>
                  </tr>
                  <tr valign="top">
                    <th scope="row"><label for="has_archive">Has Archive</label><p>If left blank, the archive slug will default to the post type slug.</p></th>
                    <td>
                      <?php $this->TrueFalse('has_archive', '0'); ?>
                      <span class="bbwpcf-field-description">(default: false) Whether or not the post type will have a post type archive URL.</span><br>
                      <input type="text" id="has_archive_string" name="user_created_post_type[has_archive_string]" value="<?php $this->selectedText('has_archive_string'); ?>" aria-required="false" placeholder="Slug to be used for archive URL.">
                      <span class="visuallyhidden">Slug to be used for archive URL.</span>
                    </td>
                  </tr>
                  <tr valign="top">
                    <th scope="row"><label for="exclude_from_search">Exclude From Search</label></th>
                    <td>
                      <?php $this->TrueFalse('exclude_from_search', '0'); ?>
                      <span class="bbwpcf-field-description">(default: false) Whether or not to exclude posts with this post type from front end search results.</span>
                    </td>
                  </tr>
                  <tr valign="top">
                    <th scope="row"><label for="capability_type">Capability Type</label></th>
                    <td>
                      <input type="text" id="capability_type" name="user_created_post_type[capability_type]" value="<?php $this->selectedText('capability_type', 'post'); ?>" aria-required="false"><br>
                      <span class="bbwpcf-field-description">The post type to use for checking read, edit, and delete capabilities.</span>
                    </td>
                  </tr>
                  <tr valign="top">
                    <th scope="row"><label for="hierarchical">Hierarchical</label></th>
                    <td>
                      <?php $this->TrueFalse('hierarchical', '0'); ?>
                      <span class="bbwpcf-field-description">(default: false) Whether or not the post type can have parent-child relationships.</span>
                    </td>
                  </tr>
                  <tr valign="top">
                    <th scope="row"><label for="rewrite">Rewrite</label></th>
                    <td>
                      <?php $this->TrueFalse('rewrite', '1'); ?>
                      <span class="bbwpcf-field-description">(default: true) Whether or not WordPress should use rewrites for this post type.</span>
                    </td>
                  </tr>
                  <tr valign="top">
                    <th scope="row"><label for="rewrite_slug">Custom Rewrite Slug</label></th>
                    <td>
                      <input type="text" id="rewrite_slug" name="user_created_post_type[rewrite_slug]" value="<?php $this->selectedText('rewrite_slug'); ?>" aria-required="false" placeholder="(default: post type slug)">
                      <span class="visuallyhidden">(default: post type slug)</span><br>
                      <span class="bbwpcf-field-description">Custom post type slug to use instead of the default.</span>
                    </td>
                  </tr>
                  <tr valign="top">
                    <th scope="row"><label for="rewrite_withfront">With Front</label></th>
                    <td>
                      <?php $this->TrueFalse('rewrite_withfront', '1'); ?>
                      <span class="bbwpcf-field-description">(default: true) Should the permastruct be prepended with the front base.</span>
                    </td>
                  </tr>
                  <tr valign="top">
                    <th scope="row"><label for="query_var">Query Var</label></th>
                    <td>
                      <?php $this->TrueFalse('query_var', '1'); ?>
                      <span class="bbwpcf-field-description">(default: true) Sets the query_var key for this post type.</span>
                    </td>
                  </tr>
                  <tr valign="top">
                    <th scope="row"><label for="query_var_slug">Custom Query Var Slug</label></th>
                    <td>
                      <input type="text" id="query_var_slug" name="user_created_post_type[query_var_slug]" value="<?php $this->selectedText('query_var_slug'); ?>" aria-required="false" placeholder="(default: post type slug) Query var needs to be true to use.">
                      <span class="visuallyhidden">(default: post type slug) Query var needs to be true to use.</span><br>
                      <span class="bbwpcf-field-description">Custom query var slug to use instead of the default.</span>
                    </td>
                  </tr>
                  <tr valign="top">
                    <th scope="row"><label for="menu_position">Menu Position</label><p>See <a href="http://codex.wordpress.org/Function_Reference/register_post_type#Parameters" target="_blank">Available options</a> in the "menu_position" section. Range of 5-100</p></th>
                    <td>
                      <input type="text" id="menu_position" name="user_created_post_type[menu_position]" value="<?php $this->selectedText('menu_position'); ?>" aria-required="false"><br>
                      <span class="bbwpcf-field-description">The position in the menu order the post type should appear. show_in_menu must be true.</span>
                    </td>
                  </tr>
                  <tr valign="top">
                    <th scope="row"><label for="show_in_menu">Show in Menu</label><p>"Show UI" must be "true". If an existing top level page such as "tools.php" is indicated for second input, post type will be sub menu of that.</p></th>
                    <td>
                      <?php $this->TrueFalse('show_in_menu', '1'); ?>
                      <span class="bbwpcf-field-description">(default: true) Whether or not to show the post type in the admin menu and where to show that menu.</span><br>
                      <input type="text" id="show_in_menu_string" name="user_created_post_type[show_in_menu_string]" value="<?php $this->selectedText('show_in_menu_string'); ?>" aria-required="false"><br>
                      <span class="bbwpcf-field-description">The top-level admin menu page file name for which the post type should be in the sub menu of.</span>
                    </td>
                  </tr>
                  <tr valign="top">
                    <th scope="row"><label for="menu_icon">Menu Icon</label></th>
                    <td>
                      <input type="text" id="menu_icon" name="user_created_post_type[menu_icon]" value="<?php $this->selectedText('menu_icon'); ?>" aria-required="false" placeholder="(Full URL for icon or Dashicon class)">
                      <span class="visuallyhidden">(Full URL for icon or Dashicon class)</span><br>
                      <span class="bbwpcf-field-description">Image URL or Dashicon class name to use for icon. Custom image should be 20px by 20px.</span>
                      </td>
                    </tr>
                    <tr valign="top">
                      <th scope="row">Supports<p>Add support for various available post editor features on the right. A checked value means the post type means the feature is supported.</p><p>Use the "None" option to explicitly set "supports" to false.</p></th>
                      <td>
                        <fieldset tabindex="0">
                          <?php
                          $bbwpcf_pt_support = array(
                            'title' => 'Title',
                            'editor' => 'Editor',
                            'thumbnail' => 'Featured Image',
                            'excerpts' => 'Excerpt',
                            'trackbacks' => 'Trackbacks',
                            'custom-fields' =>'Custom Fields',
                            'comments' => 'Comments',
                            'revisions' => 'Revisions',
                            'author' => 'Author',
                            'page-attributes' => 'Page Attributes',
                            'post-formats' => 'Post Formats',
                            'none' => 'None'
                          );
                          foreach ($bbwpcf_pt_support as $key => $value) {
                            $checked = '';
                            if(isset($edit_post_type_values['bbwpcf_pt_supports']) && is_array($edit_post_type_values['bbwpcf_pt_supports']) && in_array($key, $edit_post_type_values['bbwpcf_pt_supports'])){ $checked = 'checked="checked"'; }
                            echo '<input type="checkbox" id="'.$key.'" name="bbwpcf_pt_supports[]" value="'.esc_attr($key).'" '.$checked.'><label for="'.$key.'">'.$value.'</label><br>';
                          }
                          ?>
                        </fieldset>
                      </td>
                    </tr>
                    <?php /*<tr valign="top">
                      <th scope="row"><label for="custom_supports">Custom "Supports"</label><p>Use this input to register custom "supports" values, separated by commas. Learn about this at <a href="http://docs.pluginize.com/article/28-third-party-support-upon-registration" target="_blank">Custom "Supports"</a></p></th>
                      <td>
                        <?php $selected_value = ''; if(isset($edit_post_type_values['custom_supports'])){ $selected_value = $edit_post_type_values['custom_supports']; } ?>
                        <input type="text" id="custom_supports" name="cpt_custom_post_type[custom_supports]" value="<?php echo $selected_value; ?>" aria-required="false"><br>
                        <span class="bbwpcf-field-description">Provide custom support slugs here.</span>
                      </td>
                    </tr>*/ ?>
                    <tr valign="top">
                      <th scope="row">Taxonomies<p>Add support for available registered taxonomies.</p></th>
                      <td>
                        <fieldset tabindex="0">
                          <?php
                          $args = array('public' => true);
                          $registeredTaxonomies = get_taxonomies($args, 'objects');
                          foreach ($registeredTaxonomies as $key => $value) {
                            if($key == 'post_format')
                              continue;
                            $checked = '';
                            if(isset($edit_post_type_values['bbwpcf_pt_taxonomies']) && is_array($edit_post_type_values['bbwpcf_pt_taxonomies']) && in_array($key, $edit_post_type_values['bbwpcf_pt_taxonomies'])){ $checked = 'checked="checked"'; }
                            echo '<input type="checkbox" id="'.$key.'" name="bbwpcf_pt_taxonomies[]" value="'.esc_attr($key).'" '.$checked.'><label for="'.$key.'">'.$value->label.'</label><br>';
                          }
                          ?>

                        </fieldset>
                      </td>
                    </tr>
                </table>
              </div>
          </div><!-- postbox-->
      </div><!-- ui-sortable-->

      <div class="meta-box-sortables ui-sortable">
        <div class="postbox ">
          <button type="button" class="handlediv" aria-expanded="true"><span class="screen-reader-text">Toggle panel </span><span class="toggle-indicator" aria-hidden="true"></span></button>
          <h3 class="hndle"><span>Additional Labels</span></h3>
          <div class="inside">
            <table class="form-table bbwp_cf_table_ui">
							<tr valign="top">
                <th scope="row"><label for="description">Post Type Description</label></th>
                <td>
                  <textarea id="description" name="user_created_post_type[description]" rows="4" cols="40"><?php $this->selectedText('description', '', false); ?></textarea><br>
                  <span class="bbwpcf-field-description">Perhaps describe what your custom post type is used for?</span>
                </td>
              </tr>
              <tr valign="top">
                <th scope="row"><label for="menu_name">Menu Name</label></th>
                <td>
                  <input type="text" id="menu_name" name="user_created_post_type[menu_name]" value="<?php $this->selectedText('menu_name'); ?>" aria-required="false" placeholder="(e.g. My Movies)">
                  <span class="visuallyhidden">(e.g. My Movies)</span><br>
                  <span class="bbwpcf-field-description">Custom admin menu name for your custom post type.</span>
                </td>
              </tr>
              <tr valign="top">
                <th scope="row"><label for="all_items">All Items</label></th>
                <td>
                  <input type="text" id="all_items" name="user_created_post_type[all_items]" value="<?php $this->selectedText('all_items'); ?>" aria-required="false" placeholder="(e.g. All Movies)">
                  <span class="visuallyhidden">(e.g. All Movies)</span><br>
                  <span class="bbwpcf-field-description">Used in the post type admin submenu.</span>
                </td>
              </tr>
              <tr valign="top">
                <th scope="row"><label for="add_new">Add New</label></th>
                <td>
                  <input type="text" id="add_new" name="user_created_post_type[add_new]" value="<?php $this->selectedText('add_new'); ?>" aria-required="false" placeholder="(e.g. Add New)">
                  <span class="visuallyhidden">(e.g. Add New)</span><br>
                  <span class="bbwpcf-field-description">Used in the post type admin submenu.</span>
                </td>
              </tr>
              <tr valign="top">
                <th scope="row"><label for="add_new_item">Add New Item</label></th>
                <td><input type="text" id="add_new_item" name="user_created_post_type[add_new_item]" value="<?php $this->selectedText('add_new_item'); ?>" aria-required="false" placeholder="(e.g. Add New Movie)">
                  <span class="visuallyhidden">(e.g. Add New Movie)</span><br>
                  <span class="bbwpcf-field-description">Used at the top of the post editor screen for a new post type post.</span>
                </td>
              </tr>
              <tr valign="top"><th scope="row"><label for="edit_item">Edit Item</label></th>
                <td>
                  <input type="text" id="edit_item" name="user_created_post_type[edit_item]" value="<?php $this->selectedText('edit_item'); ?>" aria-required="false" placeholder="(e.g. Edit Movie)">
                  <span class="visuallyhidden">(e.g. Edit Movie)</span><br>
                  <span class="bbwpcf-field-description">Used at the top of the post editor screen for an existing post type post.</span>
                </td>
              </tr>
              <tr valign="top">
                <th scope="row"><label for="new_item">New Item</label></th>
                <td>
                  <input type="text" id="new_item" name="user_created_post_type[new_item]" value="<?php $this->selectedText('new_item'); ?>" aria-required="false" placeholder="(e.g. New Movie)">
                  <span class="visuallyhidden">(e.g. New Movie)</span><br>
                  <span class="bbwpcf-field-description">Post type label. Used in the admin menu for displaying post types.</span>
                </td>
              </tr>
              <tr valign="top">
                <th scope="row"><label for="view_item">View Item</label></th>
                <td>
                  <input type="text" id="view_item" name="user_created_post_type[view_item]" value="<?php $this->selectedText('view_item'); ?>" aria-required="false" placeholder="(e.g. View Movie)">
                  <span class="visuallyhidden">(e.g. View Movie)</span><br>
                  <span class="bbwpcf-field-description">Used in the admin bar when viewing editor screen for a published post in the post type.</span>
                </td>
              </tr>
              <tr valign="top">
                <th scope="row"><label for="view_items">View Items</label></th>
                <td>
                  <input type="text" id="view_items" name="user_created_post_type[view_items]" value="<?php $this->selectedText('view_items'); ?>" aria-required="false" placeholder="(e.g. View Movies)">
                  <span class="visuallyhidden">(e.g. View Movies)</span><br>
                  <span class="bbwpcf-field-description">Used in the admin bar when viewing editor screen for a published post in the post type.</span>
                </td>
              </tr>
              <tr valign="top">
                <th scope="row"><label for="search_items">Search Item</label></th>
                <td>
                  <input type="text" id="search_items" name="user_created_post_type[search_items]" value="<?php $this->selectedText('search_items'); ?>" aria-required="false" placeholder="(e.g. Search Movie)">
                  <span class="visuallyhidden">(e.g. Search Movie)</span><br>
                  <span class="bbwpcf-field-description">Used as the text for the search button on post type list screen.</span>
                </td>
              </tr>
              <tr valign="top">
                <th scope="row"><label for="not_found">Not Found</label></th>
                <td>
                  <input type="text" id="not_found" name="user_created_post_type[not_found]" value="<?php $this->selectedText('not_found'); ?>" aria-required="false" placeholder="(e.g. No Movies found)">
                  <span class="visuallyhidden">(e.g. No Movies found)</span><br>
                  <span class="bbwpcf-field-description">Used when there are no posts to display on the post type list screen.</span>
                </td>
              </tr>
              <tr valign="top">
                <th scope="row"><label for="not_found_in_trash">Not Found in Trash</label></th>
                <td>
                  <input type="text" id="not_found_in_trash" name="user_created_post_type[not_found_in_trash]" value="<?php $this->selectedText('not_found_in_trash'); ?>" aria-required="false" placeholder="(e.g. No Movies found in Trash)">
                  <span class="visuallyhidden">(e.g. No Movies found in Trash)</span><br>
                  <span class="bbwpcf-field-description">Used when there are no posts to display on the post type list trash screen.</span>
                </td>
              </tr>
              <tr valign="top">
                <th scope="row"><label for="parent">Parent</label></th>
                <td>
                  <input type="text" id="parent" name="user_created_post_type[parent_item_colon]" value="<?php $this->selectedText('parent_item_colon'); ?>" aria-required="false" placeholder="(e.g. Parent Movie:)">
                  <span class="visuallyhidden">(e.g. Parent Movie:)</span><br>
                  <span class="bbwpcf-field-description">Used for hierarchical types that need a colon.</span>
                </td>
              </tr>
              <tr valign="top">
                <th scope="row"><label for="featured_image">Featured Image</label></th>
                <td>
                  <input type="text" id="featured_image" name="user_created_post_type[featured_image]" value="<?php $this->selectedText('featured_image'); ?>" aria-required="false" placeholder="(e.g. Featured image for this movie)">
                  <span class="visuallyhidden">(e.g. Featured image for this movie)</span><br>
                  <span class="bbwpcf-field-description">Used as the "Featured Image" phrase for the post type.</span>
                </td>
              </tr>
              <tr valign="top">
                <th scope="row"><label for="set_featured_image">Set Featured Image</label></th>
                <td>
                  <input type="text" id="set_featured_image" name="user_created_post_type[set_featured_image]" value="<?php $this->selectedText('set_featured_image'); ?>" aria-required="false" placeholder="(e.g. Set featured image for this movie)">
                  <span class="visuallyhidden">(e.g. Set featured image for this movie)</span><br>
                  <span class="bbwpcf-field-description">Used as the "Set featured image" phrase for the post type.</span>
                </td>
              </tr>
              <tr valign="top">
                <th scope="row"><label for="remove_featured_image">Remove Featured Image</label></th>
                <td>
                  <input type="text" id="remove_featured_image" name="user_created_post_type[remove_featured_image]" value="<?php $this->selectedText('remove_featured_image'); ?>" aria-required="false" placeholder="(e.g. Remove featured image for this movie)">
                  <span class="visuallyhidden">(e.g. Remove featured image for this movie)</span><br><span class="bbwpcf-field-description">Used as the "Remove featured image" phrase for the post type.</span>
                </td>
              </tr>
              <tr valign="top">
                <th scope="row"><label for="use_featured_image">Use Featured Image</label></th>
                <td>
                  <input type="text" id="use_featured_image" name="user_created_post_type[use_featured_image]" value="<?php $this->selectedText('use_featured_image'); ?>" aria-required="false" placeholder="(e.g. Use as featured image for this movie)">
                  <span class="visuallyhidden">(e.g. Use as featured image for this movie)</span><br>
                  <span class="bbwpcf-field-description">Used as the "Use as featured image" phrase for the post type.</span>
                </td>
              </tr>
              <tr valign="top">
                <th scope="row"><label for="archives">Archives</label></th>
                <td>
                  <input type="text" id="archives" name="user_created_post_type[archives]" value="<?php $this->selectedText('archives'); ?>" aria-required="false" placeholder="(e.g. Movie archives)">
                  <span class="visuallyhidden">(e.g. Movie archives)</span><br>
                  <span class="bbwpcf-field-description">Post type archive label used in nav menus.</span>
                </td>
              </tr>
              <tr valign="top">
                <th scope="row"><label for="insert_into_item">Insert into item</label></th>
                <td>
                  <input type="text" id="insert_into_item" name="user_created_post_type[insert_into_item]" value="<?php $this->selectedText('insert_into_item'); ?>" aria-required="false" placeholder="(e.g. Insert into movie)">
                  <span class="visuallyhidden">(e.g. Insert into movie)</span><br>
                  <span class="bbwpcf-field-description">Used as the "Insert into post" or "Insert into page" phrase for the post type.</span>
                </td>
              </tr>
              <tr valign="top">
                <th scope="row"><label for="uploaded_to_this_item">Uploaded to this Item</label></th>
                <td>
                  <input type="text" id="uploaded_to_this_item" name="user_created_post_type[uploaded_to_this_item]" value="<?php $this->selectedText('uploaded_to_this_item'); ?>" aria-required="false" placeholder="(e.g. Uploaded to this movie)">
                  <span class="visuallyhidden">(e.g. Uploaded to this movie)</span><br>
                  <span class="bbwpcf-field-description">Used as the "Uploaded to this post" or "Uploaded to this page" phrase for the post type.</span>
                </td>
              </tr>
              <tr valign="top">
                <th scope="row"><label for="filter_items_list">Filter Items List</label></th>
                <td>
                  <input type="text" id="filter_items_list" name="user_created_post_type[filter_items_list]" value="<?php $this->selectedText('filter_items_list'); ?>" aria-required="false" placeholder="(e.g. Filter movies list)">
                  <span class="visuallyhidden">(e.g. Filter movies list)</span><br>
                  <span class="bbwpcf-field-description">Screen reader text for the filter links heading on the post type listing screen.</span>
                </td>
              </tr>
              <tr valign="top">
                <th scope="row"><label for="items_list_navigation">Items List Navigation</label></th>
                <td>
                  <input type="text" id="items_list_navigation" name="user_created_post_type[items_list_navigation]" value="<?php $this->selectedText('items_list_navigation'); ?>" aria-required="false" placeholder="(e.g. Movies list navigation)">
                  <span class="visuallyhidden">(e.g. Movies list navigation)</span><br>
                  <span class="bbwpcf-field-description">Screen reader text for the pagination heading on the post type listing screen.</span>
                </td>
              </tr>
              <tr valign="top">
                <th scope="row"><label for="items_list">Items List</label></th>
                <td>
                  <input type="text" id="items_list" name="user_created_post_type[items_list]" value="<?php $this->selectedText('items_list'); ?>" aria-required="false" placeholder="(e.g. Movies list)">
                  <span class="visuallyhidden">(e.g. Movies list)</span><br>
                  <span class="bbwpcf-field-description">Screen reader text for the items list heading on the post type listing screen.</span>
                </td>
              </tr>
              <tr valign="top">
                <th scope="row"><label for="attributes">Attributes</label></th>
                <td>
                  <input type="text" id="attributes" name="user_created_post_type[attributes]" value="<?php $this->selectedText('attributes'); ?>" aria-required="false" placeholder="(e.g. Movies Attributes)"><span class="visuallyhidden">(e.g. Movies Attributes)</span><br>
                  <span class="bbwpcf-field-description">Used for the title of the post attributes meta box.</span>
                </td>
              </tr>
            </table>
          </div><!-- inside-->
        </div><!-- postbox-->
      </div><!-- ui-sortable-->


    <?php
    submit_button('Save Changes');
    echo '</form>';
  }



  /******************************************/
  /***** input_handle function start from here *********/
  /******************************************/
  public function input_handle(){
    if(isset($_GET['page']) && $_GET['page'] === $this->prefix.'cpt'){


      if(isset($_GET['action']) && $_GET['action'] == 'delete' && isset($_GET['name']) && $_GET['name'])
        BBWPFieldTypes::DeleteFields($_GET['name'], $this->prefix("user_created_post_types"));

      if(isset($_POST['sort_fields']) && $_POST['sort_fields'] === $this->prefix('user_created_post_types')){
        if(isset($_POST['bulk_action']) && $_POST['bulk_action'] === 'delete' && isset($_POST['fields']) && is_array($_POST['fields']) && count($_POST['fields']) >= 1)
        {
          BBWPFieldTypes::DeleteFields($_POST['fields'], $this->prefix("user_created_post_types"));
        }
        elseif(isset($_POST['sort_field']) && is_array($_POST['sort_field']) && count($_POST['sort_field']) >= 1)
          BBWPFieldTypes::SortFields($_POST['sort_field'], $this->prefix("user_created_post_types"));
      }
      if(isset($_POST['create_new_post_type']) && $_POST['create_new_post_type'] === $this->prefix('create_new_post_type')){
        if(isset($_POST['user_created_post_type']) && $_POST['user_created_post_type'] && is_array($_POST['user_created_post_type']) && count($_POST['user_created_post_type']) >= 1 && isset($_POST['user_created_post_type']['name']) && $_POST['user_created_post_type']['name']){
          $update = false;
          $update_message = 'Your setting have been updated.';
          $existing_values = SerializeStringToArray(get_option($this->prefix('user_created_post_types')));
          $new_values = array();
          $array_index = '';
          foreach($_POST['user_created_post_type'] as $key=>$value){
            if(isset($_POST['user_created_post_type'][$key]) && ($_POST['user_created_post_type'][$key] || $_POST['user_created_post_type'][$key] == 0)){
              if($key == 'name'){
                $new_values[$key] = BBWPSanitization::Textfield(strtolower($value));
                $array_index = $new_values['name'];

                if(isset($_POST['bbwpcf_pt_supports']) && is_array($_POST['bbwpcf_pt_supports']) && count($_POST['bbwpcf_pt_supports']) >= 1)
                  $new_values['bbwpcf_pt_supports'] = $_POST['bbwpcf_pt_supports'];

                if(isset($_POST['bbwpcf_pt_taxonomies']) && is_array($_POST['bbwpcf_pt_taxonomies']) && count($_POST['bbwpcf_pt_taxonomies']) >= 1)
                  $new_values['bbwpcf_pt_taxonomies'] = $_POST['bbwpcf_pt_taxonomies'];

              }
							elseif($key == 'description'){
								$new_values[$key] = BBWPSanitization::Textarea($value);
							}
              else{
                if($value === '0')
                  $new_values[$key] = $value;
                else
                  $new_values[$key] = BBWPSanitization::Textfield($value);
              }

            }
          }
          if(isset($_POST['update_created_post_type']) && array_key_exists($_POST['update_created_post_type'], $existing_values)){
            unset($existing_values[$_POST['update_created_post_type']]);
            $update = true;
            $update_message = '<p>Your setting have been updated.</p>';
          }
          if($update == false && array_key_exists($array_index, $existing_values)){
            update_option("bbwp_error_message", 'There was some problem. Please try again with different page name.');
          }elseif($new_values && is_array($new_values) && count($new_values) >= 1 && $array_index ){
            $existing_values[$array_index] = $new_values;
            update_option($this->prefix('user_created_post_types'), ArrayToSerializeString($existing_values));
            update_option("bbwp_update_message", $update_message);
          }

        }
      }

    } // if isset page end here
  } // input handle function end here

}// class end here
