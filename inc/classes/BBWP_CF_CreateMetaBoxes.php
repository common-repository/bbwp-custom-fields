<?php
// exit if file is called directly
if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

class BBWP_CF_CreateMetaBoxes extends BBWP_CustomFields{

  private $user_created_pages = array();
  private $user_created_metaboxes = array();
  private $post_types_metaboxes = array();
  private $taxonomies_metaboxes = array();
  private $user_profile_metaboxes = array();
  private $pages_metaboxes = array();

  public function __construct(){

    $this->user_created_metaboxes = SerializeStringToArray(get_option($this->prefix('user_created_metaboxes')));

    add_action( 'admin_init', array($this,'admin_init') );


    add_action( 'admin_menu', array($this,'admin_menu'));

    add_action( 'edit_comment', array($this,'save_mycomment_data'));
    //add_filter('comment_save_pre', array($this, 'save_mycomment_data'));

    add_action( 'add_meta_boxes', array($this,'add_meta_boxes'));
    add_action( 'save_post', array($this,'save_post'));

    add_action( 'edit_user_profile', array($this, 'edit_user_profile'));
		add_action( 'show_user_profile', array($this, 'edit_user_profile'));
		add_action( 'profile_update', array($this, 'update_user_profile'));

  }

  public function admin_init(){

    $meta_taxonomy_list = array();
    $args = array('public' => true);
    $post_types = get_post_types( $args, 'names' );
    $taxonomies = get_taxonomies($args);
    if(count($this->user_created_metaboxes) >= 1){
      foreach ($this->user_created_metaboxes as $key => $value) {
        if(isset($value['metabox_location']) && is_array($value['metabox_location']) && count($value['metabox_location']) >= 1){
          if(in_array("user_profile", $value['metabox_location']))
            $this->user_profile_metaboxes[$key] = $value;
          if(in_array("comment", $value['metabox_location']))
            $this->post_types_metaboxes[] = array("comment", $value);
          foreach($post_types as $post_type){
            if($post_type == 'attachment')
              continue;
            if(in_array($post_type, $value['metabox_location']))
              $this->post_types_metaboxes[] = array($post_type, $value);
          }
          foreach ($taxonomies as $taxonomy) {
            if($taxonomy == 'post_format')
              continue;
            if(in_array($taxonomy, $value['metabox_location'])){
              $this->taxonomies_metaboxes[] = array($taxonomy, $value);
              $meta_taxonomy_list[$taxonomy] = $taxonomy;
            }
          }
        }
      }
    }

    if(count($meta_taxonomy_list) >= 1){
      foreach($meta_taxonomy_list as $value){
        add_action( $value.'_add_form_fields',array($this,'taxonomy_add_new_meta_field'),10,2);
        add_action($value.'_edit_form_fields',array($this,'taxonomy_edit_meta_field'),10,2);
        add_action( 'edited_'.$value, array($this,'save_taxonomy_meta_field'), 10, 2 );
        add_action( 'create_'.$value, array($this,'save_taxonomy_meta_field'), 10, 2 );
      }
    }


  }

  /******************************************/
  /***** add_meta_box function start from here *********/
  /******************************************/
  public function admin_menu(){
    $this->user_created_pages = SerializeStringToArray(get_option($this->prefix('user_created_pages')));
    if($this->user_created_pages && count($this->user_created_pages) >= 1){
      foreach($this->user_created_pages as $page){
        if(isset($page['page_slug']) && isset($page['page_title']) && isset($page['parent_menu'])){
          if($page['parent_menu'] == 'new_menu')
            add_menu_page( $page['page_title'], $page['page_title'], 'manage_options', $page['page_slug'], array($this,'user_created_menu_pages')/*, 'dashicons-tagcloud', 87.3*/);
          elseif($page['parent_menu'] == 'dashboard')
            add_dashboard_page($page['page_title'], $page['page_title'], 'manage_options',$page['page_slug'], array($this,'user_created_menu_pages'));
          elseif($page['parent_menu'] == 'posts')
            add_posts_page($page['page_title'], $page['page_title'], 'manage_options',$page['page_slug'], array($this,'user_created_menu_pages'));
          elseif($page['parent_menu'] == 'media')
            add_media_page($page['page_title'], $page['page_title'], 'manage_options',$page['page_slug'], array($this,'user_created_menu_pages'));
          elseif($page['parent_menu'] == 'pages')
            add_pages_page($page['page_title'], $page['page_title'], 'manage_options',$page['page_slug'], array($this,'user_created_menu_pages'));
          elseif($page['parent_menu'] == 'comments')
            add_comments_page($page['page_title'], $page['page_title'], 'manage_options',$page['page_slug'], array($this,'user_created_menu_pages'));
          elseif($page['parent_menu'] == 'theme')
            add_theme_page($page['page_title'], $page['page_title'], 'manage_options',$page['page_slug'], array($this,'user_created_menu_pages'));
          elseif($page['parent_menu'] == 'plugins')
            add_plugins_page($page['page_title'], $page['page_title'], 'manage_options',$page['page_slug'], array($this,'user_created_menu_pages'));
          elseif($page['parent_menu'] == 'users')
            add_users_page($page['page_title'], $page['page_title'], 'manage_options',$page['page_slug'], array($this,'user_created_menu_pages'));
          elseif($page['parent_menu'] == 'management')
            add_management_page($page['page_title'], $page['page_title'], 'manage_options',$page['page_slug'], array($this,'user_created_menu_pages'));
          elseif($page['parent_menu'] == 'options')
            add_options_page($page['page_title'], $page['page_title'], 'manage_options',$page['page_slug'], array($this,'user_created_menu_pages'));
          elseif(array_key_exists($page['parent_menu'], $this->user_created_pages))
            add_submenu_page( $page['parent_menu'], $page['page_title'], $page['page_title'], 'manage_options', $page['page_slug'], array($this,'user_created_menu_pages'));
        }
      }
    }
  }

  /******************************************/
  /***** add_meta_box function start from here *********/
  /******************************************/
  public function add_meta_boxes() {

    /*$args = array('public' => true);

    if(count($this->user_created_metaboxes) >= 1){
      foreach ($this->user_created_metaboxes as $key => $value) {
        if(isset($value['metabox_location']) && is_array($value['metabox_location']) && count($value['metabox_location']) >= 1){

        }
      }
    }*/



    if(count($this->post_types_metaboxes) >= 1){
      foreach($this->post_types_metaboxes as $value){
        $context = 'normal';
        $priority = 'default';
        if(isset($value[1]['metabox_context']) && ($value[1]['metabox_context'] == 'side' || $value[1]['metabox_context'] == 'advanced')){
        $context = $value[1]['metabox_context'];  }
        if(isset($value[1]['metabox_priority']) && ($value[1]['metabox_priority'] == 'high' || $value[1]['metabox_priority'] == 'low'))
          $priority = $value[1]['metabox_priority'];
        if($value[0] == "comment")
          add_meta_box( $this->prefix($value[1]['metabox_id']), $value[1]['metabox_title'], array($this,'add_meta_box'), "comment", 'normal' );
        else
          add_meta_box( $this->prefix($value[1]['metabox_id']), $value[1]['metabox_title'], array($this,'add_meta_box'), $value[0], $context, $priority);
      }
    }
  }

  /******************************************/
  /***** user_created_menu_pages function start from here *********/
  /******************************************/
  public function user_created_menu_pages(){

    if($this->user_created_pages && is_array($this->user_created_pages) && count($this->user_created_pages) >= 1){
      echo '<div class="wrap bytebunch_admin_page_container"><div id="icon-tools" class="icon32"></div>';
      echo '<div id="poststuff">';
      echo '<div id="postbox-container" class="postbox-container">';
      echo '<form method="post" action="">';
      $current_page = false;
      foreach($this->user_created_pages as $page){
        if(isset($_GET['page']) && $_GET['page'] === $page['page_slug'] && $current_page == false){
          $current_page = $page;
          echo '<h3>'.$page['page_title'].'</h3>';
        }
      }
      if(count($this->user_created_metaboxes) >= 1 && $current_page){
        foreach ($this->user_created_metaboxes as $key => $value) {
          if(isset($value["metabox_pages"]) && $value["metabox_pages"] === $current_page['page_slug']){
            $BBWPFieldTypes = new BBWPFieldTypes($this->prefix($key));
            $BBWPFieldTypes->SaveOptions();
            BBWPUpdateErrorMessage();
            echo '<div class="meta-box-sortables ui-sortable">
            <div class="postbox ">
            <button type="button" class="handlediv" aria-expanded="true"><span class="screen-reader-text">Toggle panel </span><span class="toggle-indicator" aria-hidden="true"></span></button>
            <h3 class="hndle"><span>'.$value['metabox_title'].'</span></h3>
            <div class="inside">';
            $BBWPFieldTypes->DisplayOptions();
            echo '</div><!-- inside-->
            </div><!-- postbox-->
            </div><!-- ui-sortable-->';
          }
        }
      }
      submit_button();
      echo '</form>';
      echo '</div><!-- postbox-container-->';
      echo '</div><!-- poststuff-->';
      echo '</div><!-- bytebunch_admin_page_container-->';
    }
  }

  /******************************************/
  /***** add_meta_box function start from here *********/
  /******************************************/
  public function add_meta_box($post, $metabox){
    if(count($this->post_types_metaboxes) >= 1){
      $post_type = '';
      if(isset($post->post_type) && $post->post_type)
        $post_type = $post->post_type;
      if(isset($post->comment_ID))
        $post_type = 'comment';
      foreach($this->post_types_metaboxes as $value){
        if($post_type == $value[0] && $this->prefix($value[1]['metabox_id']) === $metabox['id']){
          $BBWPFieldTypes = new BBWPFieldTypes($this->prefix($value[1]['metabox_id']));
          $BBWPFieldTypes->Set('saveType', 'post');
          $BBWPFieldTypes->Set('dataID', $post->ID);
          if($post_type == "comment"){
            $BBWPFieldTypes->Set('saveType', 'comment');
            $BBWPFieldTypes->Set('dataID', $post->comment_ID);
          }
          $BBWPFieldTypes->DisplayOptions();
        }
      }
    }
  }

  /******************************************/
  /***** save_post function start from here *********/
  /******************************************/
  public function save_post($post_id){

    global $post;
    // Checks save status
    $is_autosave = wp_is_post_autosave( $post_id );
    $is_revision = wp_is_post_revision( $post_id );

    // Exits script depending on save status
    if ( $is_autosave || $is_revision ) {
        return;
    }

    if(count($this->post_types_metaboxes) >= 1){
      foreach($this->post_types_metaboxes as $key=>$value){ //db($post);exit();
        $dbkey = $this->prefix($value[1]['metabox_id']);
        if(isset($post->post_type) && $post->post_type === $value[0] && isset($_POST[$dbkey.'_update_options']) && $_POST[$dbkey.'_update_options'] === $dbkey."_update_options"){
          $BBWPFieldTypes = new BBWPFieldTypes($dbkey);
          $BBWPFieldTypes->Set('saveType', 'post');
          $BBWPFieldTypes->Set('dataID', $post_id);
          $BBWPFieldTypes->SaveOptions();
        }
      }
    }
  }

  /******************************************/
  /***** taxonomy_add_new_meta_field function start from here *********/
  /******************************************/
  public function taxonomy_add_new_meta_field($tag){

    //db($tag);exit();
    if(count($this->taxonomies_metaboxes) >= 1){
      foreach ($this->taxonomies_metaboxes as $value) {
        if($value[0] === $tag){
          echo '<h2>'.$value[1]['metabox_title'].'</h2>';
          $BBWPFieldTypes = new BBWPFieldTypes($this->prefix($value[1]['metabox_id']));
          $BBWPFieldTypes->Set('saveType', 'term');
          $BBWPFieldTypes->Set('displaytype', array(
            "wrapper_open" => '<div class="form-wrap">',
            'wrapper_close' => '</div>',
            'container_open' => '<div class="form-field">',
            'container_close' => '</div>',
            'label_open' => '',
            'label_close' => '',
            'input_open' => '',
            'input_close' => ''
          ));
          //$BBWPFieldTypes->Set('dataID', $user->ID);
          $BBWPFieldTypes->DisplayOptions();
        }

      }
    }
  }

  /******************************************/
  /***** taxonomy_edit_meta_field function start from here *********/
  /******************************************/
  public function taxonomy_edit_meta_field($term){
    if(count($this->taxonomies_metaboxes) >= 1){
      foreach ($this->taxonomies_metaboxes as $value) {
        if($value[0] === $term->taxonomy){
          echo '<tr class="form-field" colspan="2"><td><h3 style="margin:0px;">'.$value[1]['metabox_title'].'</h3></td></tr>';
          $BBWPFieldTypes = new BBWPFieldTypes($this->prefix($value[1]['metabox_id']));
          $BBWPFieldTypes->Set('saveType', 'term');
          $BBWPFieldTypes->Set('dataID', $term->term_id);
          $BBWPFieldTypes->Set('displaytype', array(
            "wrapper_open" => '',
            'wrapper_close' => '',
            'container_open' => '<tr class="form-field">',
            'container_close' => '</tr>',
            'label_open' => '<th scope="row">',
            'label_close' => '</th>',
            'input_open' => '<td>',
            'input_close' => '</td>'
          ));
          $BBWPFieldTypes->DisplayOptions();
        }

      }
    }
  }

  /******************************************/
  /***** save_taxonomy_custom_meta function start from here *********/
  /******************************************/
  public function save_taxonomy_meta_field($term_id){
    if(count($this->taxonomies_metaboxes) >= 1){
      foreach ($this->taxonomies_metaboxes as $value) {
        $dbkey = $this->prefix($value[1]['metabox_id']);
        if(isset($_POST[$dbkey.'_update_options']) && $_POST[$dbkey.'_update_options'] === $dbkey."_update_options"){
          $BBWPFieldTypes = new BBWPFieldTypes($dbkey);
          $BBWPFieldTypes->Set('saveType', 'term');
          $BBWPFieldTypes->Set('dataID', $term_id);
          $BBWPFieldTypes->SaveOptions();
        }
      }
    }
  }

  /******************************************/
  /***** function to edit user profile start from here **********/
  /******************************************/
  function edit_user_profile($user){
    if(count($this->user_profile_metaboxes) >= 1){
      foreach ($this->user_profile_metaboxes as $key => $value) {
        echo '<h3>'.$value['metabox_title'].'</h3>';
        $BBWPFieldTypes = new BBWPFieldTypes($this->prefix($key));
        $BBWPFieldTypes->Set('saveType', 'user');
        $BBWPFieldTypes->Set('dataID', $user->ID);
        $BBWPFieldTypes->DisplayOptions();
      }
    }
  }

  /******************************************/
  /***** function to update user profile start from here **********/
  /******************************************/
  public function update_user_profile($user_id/*, $old_user_data*/){
    if(count($this->user_profile_metaboxes) >= 1){
      foreach ($this->user_profile_metaboxes as $key => $value) {
        echo '<h3>'.$value['metabox_title'].'</h3>';
        $BBWPFieldTypes = new BBWPFieldTypes($this->prefix($key));
        $BBWPFieldTypes->Set('saveType', 'user');
        $BBWPFieldTypes->Set('dataID', $user_id);
        $BBWPFieldTypes->SaveOptions();
      }
    }
  }

  /******************************************/
  /***** function to get the current user id start from here **********/
  /******************************************/
  public function save_mycomment_data($comment_ID){
    if(count($this->post_types_metaboxes) >= 1){
      foreach($this->post_types_metaboxes as $key=>$value){
        $dbkey = $this->prefix($value[1]['metabox_id']);
        if('comment' === $value[0] && isset($_POST[$dbkey.'_update_options']) && $_POST[$dbkey.'_update_options'] === $dbkey."_update_options"){
          $BBWPFieldTypes = new BBWPFieldTypes($dbkey);
          $BBWPFieldTypes->Set('saveType', 'comment');
          $BBWPFieldTypes->Set('dataID', $comment_ID);
          $BBWPFieldTypes->SaveOptions();
        }
      }
    }
  }

}
