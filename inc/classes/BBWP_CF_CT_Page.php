<?php
// exit if file is called directly
if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

class BBWP_CF_CT_Page extends BBWP_CustomFields{

  private $edit_taxonomy_values = array();

  public function __construct(){
    add_action('init', array($this, 'input_handle'));
    add_action( 'admin_menu', array($this,'admin_menu'));
  }// construct function end here

  /******************************************/
  /***** page_bboptions_admin_menu function start from here *********/
  /******************************************/
  public function admin_menu(){

    /* add sub menu in our wordpress dashboard main menu */
    add_submenu_page( $this->prefix, 'Custom Taxonomies', 'Custom Taxonomies', 'manage_options', $this->prefix.'ct', array($this,'add_submenu_page') );

  }

  /******************************************/
  /***** add_submenu_page_bboptions function start from here *********/
  /******************************************/
  public function add_submenu_page(){

    echo '<div class="wrap bytebunch_admin_page_container"><div id="icon-tools" class="icon32"></div>';
    echo '<div id="poststuff">
      <div id="postbox-container" class="postbox-container">';


    $user_created_taxonomies = SerializeStringToArray(get_option($this->prefix('user_created_taxonomies')));

    echo '<h3> Add/Edit Taxonomies </h3>';

    if(isset($_GET['action']) && $_GET['action'] == 'edit' && isset($_GET['name']) && $_GET['name'] && count($user_created_taxonomies) >= 1 && array_key_exists($_GET['name'], $user_created_taxonomies)){
      echo '<p><a href="?page='.sanitize_key($_GET['page']).'">← Back to Main Page</a></p>';
      echo '<h2 class="nav-tab-wrapper bbwp_nav_wrapper">
        <a href="#add-new-custom-taxonomies" class="nav-tab">Edit Taxonomy - '.$user_created_taxonomies[$_GET['name']]['label'].'</a>
      </h2>';
      BBWPUpdateErrorMessage();
      $this->CreateTaxonomyForm($user_created_taxonomies, $_GET['name']);
      return;
    }
    BBWPUpdateErrorMessage();
    ?>
    <h2 class="nav-tab-wrapper bbwp_nav_wrapper">
      <a href="#add-new-custom-taxonomies" class="nav-tab">Add New Taxonomy</a>
      <?php if($user_created_taxonomies && is_array($user_created_taxonomies) && count($user_created_taxonomies) >= 1){ ?>
        <a href="#existing-custom-taxonomies" class="nav-tab">Edit Taxonomies</a>
      <?php } ?>
    </h2>


          <div class="bbwp_tab_nav_content" id="add-new-custom-taxonomies">
            <?php  $this->CreateTaxonomyForm($user_created_taxonomies);  ?>
          </div>
          <div class="bbwp_tab_nav_content" id="existing-custom-taxonomies" style="display:none;">
            <?php
            if($user_created_taxonomies && is_array($user_created_taxonomies) && count($user_created_taxonomies) >= 1){
              echo '<form method="post" action="">';
              $tableColumns = array("name" => "Post Type Slug/Name", "label" => "Plural Label");
              $BBWPListTable = new BBWPListTable();
              $BBWPListTable->get_columns($tableColumns);
              $BBWPListTable->bulk_actions = array("delete" => "Delete Selected");
              $BBWPListTable->get_sortable_columns(array("name" => "name"));
              $BBWPListTable->actions = array('name' => array('delete', 'edit'));
              $BBWPListTable->prepare_items($user_created_taxonomies);
              $BBWPListTable->display();
              echo '<input type="hidden" name="sort_fields" value="'.$this->prefix('user_created_taxonomies').'" />';
              submit_button('Save Changes', 'primary alignright');
              echo '</form>';
            }
            ?>
          </div><!-- existing-custom-taxonomies-->
        </div><!-- postbox-container-->
      </div><!-- poststuff-->
    </div><!-- main wrap div end here -->
    <?php
  }


  /******************************************/
  /***** CreateTaxonomyForm function start from here *********/
  /******************************************/

  private function TrueFalse($svalue, $dvalue){
    $trueFalse = array('0' => 'False', '1' => 'True');
    $selected_value = $dvalue;
    if(isset($this->edit_taxonomy_values) && isset($this->edit_taxonomy_values[$svalue]) && ($this->edit_taxonomy_values[$svalue] == 1 || $this->edit_taxonomy_values[$svalue] == 0))
      $selected_value = $this->edit_taxonomy_values[$svalue];
    echo '<select id="'.$svalue.'" name="user_created_taxonomy['.$svalue.']">'.ArraytoSelectList($trueFalse, $selected_value).'</select>';
  }

  private function selectedText($svalue, $dvalue = '', $esc = true){
    $selected_value = $dvalue;
    if(isset($this->edit_taxonomy_values) && isset($this->edit_taxonomy_values[$svalue]) && $this->edit_taxonomy_values[$svalue]){
      $selected_value = $this->edit_taxonomy_values[$svalue];
    }
		if($esc != true)
    	echo $selected_value;
		else
			echo esc_attr($selected_value);
  }

  private function CreateTaxonomyForm($user_created_taxonomies = array(), $edit_taxonomy = false){
    $edit_taxonomy_values = array();
    echo '<form method="post" action="">';
    if($edit_taxonomy && is_array($user_created_taxonomies) && count($user_created_taxonomies) >= 1 && array_key_exists($edit_taxonomy, $user_created_taxonomies)){
      $edit_taxonomy_values = $user_created_taxonomies[$edit_taxonomy];
      $this->edit_taxonomy_values = $edit_taxonomy_values;
      echo '<input type="hidden" name="update_created_taxonomy" value="'.$edit_taxonomy.'" />';
    }

      ?>
      <input type="hidden" name="create_new_taxonomy" value="<?php echo $this->prefix('create_new_taxonomy'); ?>" />
      <div class="meta-box-sortables ui-sortable">
        <div class="postbox ">
          <button type="button" class="handlediv" aria-expanded="true"><span class="screen-reader-text">Toggle panel </span><span class="toggle-indicator" aria-hidden="true"></span></button>
          <h3 class="hndle"><span>Basic Settings</span></h3>
          <div class="inside">
            <table class="form-table bbwp_cf_table_ui">
              <tr>
                <th scope="row"><label for="name">Taxonomy Slug: <span class="require_star">*</span></label></th>
                <td>
                  <?php $selected_value = ''; if(isset($edit_taxonomy_values['name'])){ $selected_value = $edit_taxonomy_values['name']; } ?>
                  <input type="text" name="user_created_taxonomy[name]" id="name" class="regular-text" required="required" value="<?php echo esc_attr($selected_value); ?>" />
                  <br /><span class="bbwpcf-field-description">The Taxonomy name/slug. Used for various queries for Taxonomy content.</span>
                  <p>Slugs should only contain alphanumeric, latin characters. Underscores should be used in place of spaces. Set "Custom Rewrite Slug" field to make slug use dashes for URLs.</p>
                </td>
              </tr>
              <tr>
                <th scope="row"><label for="label">Plural Label <span class="require_star">*</span></label></th>
                <td>
                  <?php $selected_value = ''; if(isset($edit_taxonomy_values['label'])){ $selected_value = $edit_taxonomy_values['label']; } ?>
                  <input type="text" name="user_created_taxonomy[label]" id="label" class="regular-text" required="required" value="<?php echo esc_attr($selected_value); ?>" />
                  <br /><span class="bbwpcf-field-description">Used for the taxonomy admin menu item.</span>
                </td>
              </tr>
              <tr>
                <th scope="row"><label for="singular_label">Singular Label <span class="require_star">*</span></label></th>
                <td>
                  <?php $selected_value = ''; if(isset($edit_taxonomy_values['singular_label'])){ $selected_value = $edit_taxonomy_values['singular_label']; } ?>
                  <input type="text" name="user_created_taxonomy[singular_label]" id="singular_label" class="regular-text" required="required" value="<?php echo esc_attr($selected_value); ?>" />
                  <br /><span class="bbwpcf-field-description">Used when a singular label is needed.</span>
                </td>
              </tr>
              <tr valign="top">
                <th scope="row">Attach to Post Type <span class="required">*</span><p>Add support for available registered post types. At least one is required.</p></th>
                <td>
                  <fieldset tabindex="0">
                    <?php
                    $selected_value = array();
                    if(isset($edit_taxonomy_values['bbwpcf_posts']) && is_array($edit_taxonomy_values['bbwpcf_posts'])){
                      $selected_value = $edit_taxonomy_values['bbwpcf_posts'];
                    }
                    $args = array('public' => true);
                    $post_types = get_post_types( $args, 'names' );
                    foreach ( $post_types as $post_type ) {
                      if($post_type == 'attachment')
                        continue;
                        if(in_array($post_type, $selected_value))
                          echo '<input type="checkbox" id="'.$post_type.'" name="bbwpcf_posts[]" value="'.esc_attr($post_type).'" checked="checked"><label for="'.$post_type.'">'.ucfirst(str_ireplace(array("-","_"), array(" ", " "), $post_type)).'</label><br>';
                        else
                          echo '<input type="checkbox" id="'.$post_type.'" name="bbwpcf_posts[]" value="'.esc_attr($post_type).'"><label for="'.$post_type.'">'.ucfirst(str_ireplace(array("-","_"), array(" ", " "), $post_type)).'</label><br>';
                    }
                    ?>
                  </fieldset>
                </td>
              </tr>
            </table>
            <?php submit_button('Save Changes'); ?>
          </div>
        </div><!-- postbox-->
      </div><!-- meta-box-sortables -->


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
                    <span class="bbwpcf-field-description">(default: true) Whether or not the taxonomy should be publicly queryable.</span>
                  </td>
              </tr>
              <tr valign="top">
                <th scope="row"><label for="hierarchical">Hierarchical</label></th>
                <td>
                  <?php $this->TrueFalse('hierarchical', '0'); ?>
                  <span class="bbwpcf-field-description">(default: false) Whether the taxonomy can have parent-child relationships.</span>
                </td>
              </tr>
              <tr valign="top">
                <th scope="row"><label for="show_ui">Show UI</label></th>
                <td>
                  <?php $this->TrueFalse('show_ui', '1'); ?>
                  <span class="bbwpcf-field-description">(default: true) Whether to generate a default UI for managing this custom taxonomy.</span>
                </td>
              </tr>
              <tr valign="top">
                <th scope="row"><label for="show_in_menu">Show in menu</label></th>
                <td>
                  <?php $this->TrueFalse('show_in_menu', '1'); ?>
                  <span class="bbwpcf-field-description">(default: value of show_ui) Whether to show the taxonomy in the admin menu.</span>
                </td>
              </tr>
              <tr valign="top">
                <th scope="row"><label for="show_in_nav_menus">Show in nav menus</label></th>
                <td>
                   <?php $this->TrueFalse('show_in_nav_menus', '1'); ?>
                  <span class="bbwpcf-field-description">(default: value of public) Whether to make the taxonomy available for selection in navigation menus.</span></td>
              </tr>
              <tr valign="top">
                <th scope="row"><label for="query_var">Query Var</label></th>
                <td>
                  <?php $this->TrueFalse('query_var', '1'); ?>
                  <span class="bbwpcf-field-description">(default: true) Sets the query_var key for this taxonomy.</span>
                </td>
              </tr>
              <tr valign="top">
                <th scope="row"><label for="query_var_slug">Custom Query Var String</label></th>
                <td>
                  <?php $selected_value = ''; if(isset($edit_taxonomy_values['query_var_slug'])){ $selected_value = $edit_taxonomy_values['query_var_slug']; } ?>
                  <input type="text" id="query_var_slug" name="user_created_taxonomy[query_var_slug]" value="<?php echo esc_attr($selected_value); ?>" aria-required="false" placeholder="(default: taxonomy slug). Query var needs to be true to use.">
                  <span class="visuallyhidden">(default: taxonomy slug). Query var needs to be true to use.</span><br><span class="bbwpcf-field-description">Sets a custom query_var slug for this taxonomy.</span>
                </td>
              </tr>
              <tr valign="top">
                <th scope="row"><label for="rewrite">Rewrite</label></th>
                <td>
                  <?php $this->TrueFalse('rewrite', '1'); ?>
                  <span class="bbwpcf-field-description">(default: true) Whether or not WordPress should use rewrites for this taxonomy.</span>
                </td>
              </tr>
              <tr valign="top">
                <th scope="row"><label for="rewrite_slug">Custom Rewrite Slug</label></th>
                <td>
                  <?php $selected_value = ''; if(isset($edit_taxonomy_values['rewrite_slug'])){ $selected_value = $edit_taxonomy_values['rewrite_slug']; } ?>
                  <input type="text" id="rewrite_slug" name="user_created_taxonomy[rewrite_slug]" aria-required="false" placeholder="(default: taxonomy name)" value="<?php echo esc_attr($selected_value); ?>">
                  <span class="visuallyhidden">(default: taxonomy name)</span><br>
                  <span class="bbwpcf-field-description">Custom taxonomy rewrite slug.</span>
                </td>
              </tr>
              <tr valign="top">
                <th scope="row"><label for="rewrite_withfront">Rewrite With Front</label></th>
                <td>
                  <?php $this->TrueFalse('rewrite_withfront', '1'); ?>
                  <span class="bbwpcf-field-description">(default: true) Should the permastruct be prepended with the front base.</span>
                </td>
              </tr>
              <tr valign="top">
                <th scope="row"><label for="rewrite_hierarchical">Rewrite Hierarchical</label></th>
                <td>
                  <?php $this->TrueFalse('rewrite_hierarchical', '0'); ?>
                  <span class="bbwpcf-field-description">(default: false) Should the permastruct allow hierarchical urls.</span>
                </td>
              </tr>
              <tr valign="top">
                <th scope="row"><label for="show_admin_column">Show Admin Column</label></th>
                <td>
                  <?php $this->TrueFalse('show_admin_column', '0'); ?>
                  <span class="bbwpcf-field-description">(default: false) Whether to allow automatic creation of taxonomy columns on associated post-types.</span>
                </td>
              </tr>
              <tr valign="top">
                <th scope="row"><label for="show_in_rest">Show in REST API</label></th>
                <td>
                  <?php $this->TrueFalse('show_in_rest', '0'); ?>
                  <span class="bbwpcf-field-description">(default: false) Whether to show this taxonomy data in the WP REST API.</span>
                </td>
              </tr>
              <tr valign="top">
                <th scope="row"><label for="rest_base">REST API base slug</label></th>
                <td>
                  <?php $selected_value = ''; if(isset($edit_taxonomy_values['rewrite_slug'])){ $selected_value = $edit_taxonomy_values['rewrite_slug']; } ?>
                  <input type="text" id="rest_base" name="cpt_custom_tax[rest_base]" value="<?php echo esc_attr($selected_value); ?>" aria-required="false"><br>
                  <span class="bbwpcf-field-description">Slug to use in REST API URLs.</span>
                </td>
              </tr>
              <tr valign="top">
                <th scope="row"><label for="show_in_quick_edit">Show in quick/bulk edit panel.</label></th>
                <td>
                  <?php $this->TrueFalse('show_in_quick_edit', '1'); ?>
                  <span class="bbwpcf-field-description">(default: true) Whether to show the taxonomy in the quick/bulk edit panel.</span></td>
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
                <th scope="row"><label for="description">Description</label></th>
                <td>
                  <textarea id="description" name="user_created_taxonomy[description]" rows="4" cols="40"><?php $this->selectedText('description', '', false); ?></textarea><br>
                  <span class="bbwpcf-field-description">Describe what your taxonomy is used for.</span>
                </td>
              </tr>
              <tr valign="top">
                <th scope="row"><label for="menu_name">Menu Name</label></th>
                <td>
                  <input type="text" id="menu_name" name="user_created_taxonomy[menu_name]" value="<?php $this->selectedText('menu_name'); ?>" aria-required="false" placeholder="(e.g. Actors)">
                  <span class="visuallyhidden">(e.g. Actors)</span><br>
                  <span class="bbwpcf-field-description">Custom admin menu name for your taxonomy.</span>
                </td>
              </tr>
              <tr valign="top">
                <th scope="row"><label for="all_items">All Items</label></th>
                <td>
                  <input type="text" id="all_items" name="user_created_taxonomy[all_items]" value="<?php $this->selectedText('all_items'); ?>" aria-required="false" placeholder="(e.g. All Actors)">
                  <span class="visuallyhidden">(e.g. All Actors)</span><br>
                  <span class="bbwpcf-field-description">Used as tab text when showing all terms for hierarchical taxonomy while editing post.</span>
                </td>
              </tr>
              <tr valign="top">
                <th scope="row"><label for="edit_item">Edit Item</label></th>
                <td>
                  <input type="text" id="edit_item" name="user_created_taxonomy[edit_item]" value="<?php $this->selectedText('edit_item'); ?>" aria-required="false" placeholder="(e.g. Edit Actor)">
                  <span class="visuallyhidden">(e.g. Edit Actor)</span><br>
                  <span class="bbwpcf-field-description">Used at the top of the term editor screen for an existing taxonomy term.</span>
                </td>
              </tr>
              <tr valign="top">
                <th scope="row"><label for="view_item">View Item</label></th>
                <td>
                  <input type="text" id="view_item" name="user_created_taxonomy[view_item]" value="<?php $this->selectedText('view_item'); ?>" aria-required="false" placeholder="(e.g. View Actor)">
                  <span class="visuallyhidden">(e.g. View Actor)</span><br>
                  <span class="bbwpcf-field-description">Used in the admin bar when viewing editor screen for an existing taxonomy term.</span>
                </td>
              </tr>
              <tr valign="top">
                <th scope="row"><label for="update_item">Update Item Name</label></th>
                <td>
                  <input type="text" id="update_item" name="user_created_taxonomy[update_item]" value="<?php $this->selectedText('update_item'); ?>" aria-required="false" placeholder="(e.g. Update Actor Name)">
                  <span class="visuallyhidden">(e.g. Update Actor Name)</span><br>
                  <span class="bbwpcf-field-description">Custom taxonomy label. Used in the admin menu for displaying taxonomies.</span>
                </td>
              </tr>
              <tr valign="top">
                <th scope="row"><label for="add_new_item">Add New Item</label></th>
                <td>
                  <input type="text" id="add_new_item" name="user_created_taxonomy[add_new_item]" value="<?php $this->selectedText('add_new_item'); ?>" aria-required="false" placeholder="(e.g. Add New Actor)">
                  <span class="visuallyhidden">(e.g. Add New Actor)</span><br>
                  <span class="bbwpcf-field-description">Used at the top of the term editor screen and button text for a new taxonomy term.</span>
                </td>
              </tr>
              <tr valign="top">
                <th scope="row"><label for="new_item_name">New Item Name</label></th>
                <td>
                  <input type="text" id="new_item_name" name="user_created_taxonomy[new_item_name]" value="<?php $this->selectedText('new_item_name'); ?>" aria-required="false" placeholder="(e.g. New Actor Name)">
                  <span class="visuallyhidden">(e.g. New Actor Name)</span><br>
                  <span class="bbwpcf-field-description">Custom taxonomy label. Used in the admin menu for displaying taxonomies.</span>
                </td>
              </tr>
              <tr valign="top">
                <th scope="row"><label for="parent_item">Parent Item</label></th>
                <td>
                  <input type="text" id="parent_item" name="user_created_taxonomy[parent_item]" value="<?php $this->selectedText('parent_item'); ?>" aria-required="false" placeholder="(e.g. Parent Actor)">
                  <span class="visuallyhidden">(e.g. Parent Actor)</span><br>
                  <span class="bbwpcf-field-description">Custom taxonomy label. Used in the admin menu for displaying taxonomies.</span>
                </td>
              </tr>
              <tr valign="top">
                <th scope="row"><label for="parent_item_colon">Parent Item Colon</label></th>
                <td>
                  <input type="text" id="parent_item_colon" name="user_created_taxonomy[parent_item_colon]" value="<?php $this->selectedText('parent_item_colon'); ?>" aria-required="false" placeholder="(e.g. Parent Actor:)">
                  <span class="visuallyhidden">(e.g. Parent Actor:)</span><br>
                  <span class="bbwpcf-field-description">Custom taxonomy label. Used in the admin menu for displaying taxonomies.</span>
                </td>
              </tr>
              <tr valign="top">
                <th scope="row"><label for="search_items">Search Items</label></th>
                <td>
                  <input type="text" id="search_items" name="user_created_taxonomy[search_items]" value="<?php $this->selectedText('search_items'); ?>" aria-required="false" placeholder="(e.g. Search Actors)">
                  <span class="visuallyhidden">(e.g. Search Actors)</span><br>
                  <span class="bbwpcf-field-description">Custom taxonomy label. Used in the admin menu for displaying taxonomies.</span>
                </td>
              </tr>
              <tr valign="top">
                <th scope="row"><label for="popular_items">Popular Items</label></th>
                <td>
                  <input type="text" id="popular_items" name="user_created_taxonomy[popular_items]" value="<?php $this->selectedText('popular_items'); ?>" aria-required="false" placeholder="(e.g. Popular Actors)">
                  <span class="visuallyhidden">(e.g. Popular Actors)</span><br>
                  <span class="bbwpcf-field-description">Custom taxonomy label. Used in the admin menu for displaying taxonomies.</span>
                </td>
              </tr>
              <tr valign="top">
                <th scope="row"><label for="separate_items_with_commas">Separate Items with Commas</label></th>
                <td>
                  <input type="text" id="separate_items_with_commas" name="user_created_taxonomy[separate_items_with_commas]" value="<?php $this->selectedText('separate_items_with_commas'); ?>" aria-required="false" placeholder="(e.g. Separate Actors with commas)">
                  <span class="visuallyhidden">(e.g. Separate Actors with commas)</span><br>
                  <span class="bbwpcf-field-description">Custom taxonomy label. Used in the admin menu for displaying taxonomies.</span>
                </td>
              </tr>
              <tr valign="top">
                <th scope="row"><label for="add_or_remove_items">Add or Remove Items</label></th>
                <td>
                  <input type="text" id="add_or_remove_items" name="user_created_taxonomy[add_or_remove_items]" value="<?php $this->selectedText('add_or_remove_items'); ?>" aria-required="false" placeholder="(e.g. Add or remove Actors)">
                  <span class="visuallyhidden">(e.g. Add or remove Actors)</span><br>
                  <span class="bbwpcf-field-description">Custom taxonomy label. Used in the admin menu for displaying taxonomies.</span>
                </td>
              </tr>
              <tr valign="top">
                <th scope="row"><label for="choose_from_most_used">Choose From Most Used</label></th>
                <td>
                  <input type="text" id="choose_from_most_used" name="user_created_taxonomy[choose_from_most_used]" value="<?php $this->selectedText('choose_from_most_used'); ?>" aria-required="false" placeholder="(e.g. Choose from the most used Actors)">
                  <span class="visuallyhidden">(e.g. Choose from the most used Actors)</span><br>
                  <span class="bbwpcf-field-description">Custom taxonomy label. Used in the admin menu for displaying taxonomies.</span>
                </td>
              </tr>
              <tr valign="top">
                <th scope="row"><label for="not_found">Not found</label></th>
                <td>
                  <input type="text" id="not_found" name="user_created_taxonomy[not_found]" value="<?php $this->selectedText('not_found'); ?>" aria-required="false" placeholder="(e.g. No Actors found)">
                  <span class="visuallyhidden">(e.g. No Actors found)</span><br>
                  <span class="bbwpcf-field-description">Custom taxonomy label. Used in the admin menu for displaying taxonomies.</span>
                </td>
              </tr>
              <tr valign="top">
                <th scope="row"><label for="no_terms">No terms</label></th>
                <td>
                  <input type="text" id="no_terms" name="user_created_taxonomy[no_terms]" value="<?php $this->selectedText('no_terms'); ?>" aria-required="false" placeholder="(e.g. No actors)">
                  <span class="visuallyhidden">(e.g. No actors)</span><br>
                  <span class="bbwpcf-field-description">Used when indicating that there are no terms in the given taxonomy associated with an object.</span>
                </td>
              </tr>
              <tr valign="top">
                <th scope="row"><label for="items_list_navigation">Items List Navigation</label></th>
                <td>
                  <input type="text" id="items_list_navigation" name="user_created_taxonomy[items_list_navigation]" value="<?php $this->selectedText('items_list_navigation'); ?>" aria-required="false" placeholder="(e.g. Actors list navigation)">
                  <span class="visuallyhidden">(e.g. Actors list navigation)</span><br>
                  <span class="bbwpcf-field-description">Screen reader text for the pagination heading on the term listing screen.</span>
                </td>
              </tr>
              <tr valign="top">
                <th scope="row"><label for="items_list">Items List</label></th>
                <td>
                  <input type="text" id="items_list" name="user_created_taxonomy[items_list]" value="<?php $this->selectedText('items_list'); ?>" aria-required="false" placeholder="(e.g. Actors list)">
                  <span class="visuallyhidden">(e.g. Actors list)</span><br>
                  <span class="bbwpcf-field-description">Screen reader text for the items list heading on the term listing screen.</span>
                </td>
              </tr>
              </table>
          </div>
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
    if(isset($_GET['page']) && $_GET['page'] === $this->prefix.'ct'){

      if(isset($_GET['action']) && $_GET['action'] == 'delete' && isset($_GET['name']) && $_GET['name'])
        BBWPFieldTypes::DeleteFields($_GET['name'], $this->prefix("user_created_taxonomies"));

      if(isset($_POST['sort_fields']) && $_POST['sort_fields'] === $this->prefix('user_created_taxonomies')){
        if(isset($_POST['bulk_action']) && $_POST['bulk_action'] === 'delete' && isset($_POST['fields']) && is_array($_POST['fields']) && count($_POST['fields']) >= 1)
        {
          BBWPFieldTypes::DeleteFields($_POST['fields'], $this->prefix("user_created_taxonomies"));
        }
        elseif(isset($_POST['sort_field']) && is_array($_POST['sort_field']) && count($_POST['sort_field']) >= 1)
          BBWPFieldTypes::SortFields($_POST['sort_field'], $this->prefix("user_created_taxonomies"));
      }
      if(isset($_POST['create_new_taxonomy']) && $_POST['create_new_taxonomy'] === $this->prefix('create_new_taxonomy')){
        if(isset($_POST['user_created_taxonomy']) && $_POST['user_created_taxonomy'] && is_array($_POST['user_created_taxonomy']) && count($_POST['user_created_taxonomy']) >= 1 && isset($_POST['user_created_taxonomy']['name']) && $_POST['user_created_taxonomy']['name']){
          $update = false;
          $update_message = 'Your setting have been updated.';
          $existing_values = SerializeStringToArray(get_option($this->prefix('user_created_taxonomies')));
          $new_values = array();
          $array_index = '';
          foreach($_POST['user_created_taxonomy'] as $key=>$value){
          if(isset($_POST['user_created_taxonomy'][$key]) && ($_POST['user_created_taxonomy'][$key] || $_POST['user_created_taxonomy'][$key] == 0)){
              if($key == 'name'){
                $new_values[$key] = BBWPSanitization::Textfield(strtolower($value));
                $array_index = $new_values['name'];

                if($_POST['bbwpcf_posts'] && is_array($_POST['bbwpcf_posts']) && count($_POST['bbwpcf_posts']) >= 1)
                  $new_values['bbwpcf_posts'] = $_POST['bbwpcf_posts'];
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

          if(isset($_POST['update_created_taxonomy']) && array_key_exists($_POST['update_created_taxonomy'], $existing_values)){
            unset($existing_values[$_POST['update_created_taxonomy']]);
            //$array_index = $_POST['update_created_taxonomy'];
            $update = true;
            $update_message = '<p>Your setting have been updated.</p>';
          }
          if($update == false && array_key_exists($array_index, $existing_values)){
            update_option("bbwp_error_message", 'There was some problem. Please try again with different taxonomy name.');
          }elseif($new_values && is_array($new_values) && count($new_values) >= 1 && $array_index ){
            $existing_values[$array_index] = $new_values;
            update_option($this->prefix('user_created_taxonomies'), ArrayToSerializeString($existing_values));
            update_option("bbwp_update_message", $update_message);
          }
        }

      }
    } // if isset page end here
  } // input handle function end here

}// class end here
