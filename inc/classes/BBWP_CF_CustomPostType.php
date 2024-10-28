<?php
// exit if file is called directly
if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

class BBWP_CF_CustomPostType
{
  private $post_type_names;
  public $prefix = 'bbwpcustomfields';

  // Class constructor
  public function __construct()
  {
      $user_created_post_types = SerializeStringToArray(get_option($this->prefix('user_created_post_types')));

      // Add action to register the post type, if the post type does not already exist
      if($user_created_post_types && is_array($user_created_post_types) && count($user_created_post_types) >= 1){
        $this->post_type_names = $user_created_post_types;
        add_action( 'init', array( &$this, 'register_post_type' ) );
      }


  }

  public function prefix($string = '', $underscore = "_"){
    return $this->prefix.$underscore.$string;
  }

  // Method which registers the post type
  public function register_post_type()
  {
      foreach($this->post_type_names as $key=>$postType){
        if( ! post_type_exists( $key ) )
        {
          // We set the default labels based on the post type name and plural. We overwrite them with the given labels.
          $overwrite_labels = array('menu_name', 'all_items', 'add_new', 'add_new_item', 'edit_item', 'new_item', 'view_item', 'view_items', 'search_items', 'not_found', 'not_found_in_trash', 'parent_item_colon', 'featured_image', 'set_featured_image', 'remove_featured_image', 'use_featured_image', 'archives', 'insert_into_item', 'uploaded_to_this_item', 'filter_items_list', 'items_list_navigation', 'items_list', 'attributes');
          $post_type_labels = array();
          foreach($overwrite_labels as $value){
            if(isset($postType[$value]) && $postType[$value])
                $post_type_labels[$value] = $postType[$value];
          }
          $labels = array_merge(
              // Default
              array(
                  'name'                  => _x( $postType['label'], 'post type general name' ),
                  'singular_name'         => _x( $postType['singular_label'], 'post type singular name' ),
                  'add_new'               => _x( 'Add New', strtolower( $postType['singular_label'] ) ),
                  'add_new_item'          => __( 'Add New ' . $postType['singular_label'] ),
                  'edit_item'             => __( 'Edit ' . $postType['singular_label'] ),
                  'new_item'              => __( 'New ' . $postType['singular_label'] ),
                  'all_items'             => __( 'All ' . $postType['label'] ),
                  'view_item'             => __( 'View ' . $postType['singular_label'] ),
                  'search_items'          => __( 'Search ' . $postType['label'] ),
                  'not_found'             => __( 'No ' . strtolower( $postType['label'] ) . ' found'),
                  'not_found_in_trash'    => __( 'No ' . strtolower( $postType['label'] ) . ' found in Trash'),
                  'parent_item_colon'     => '',
                  'menu_name'             => $postType['label']
                ),
                // Given labels
                $post_type_labels
            );


          // Same principle as the labels. We set some defaults and overwrite them with the given arguments.
        $overwrite_args = array('public', 'publicly_queryable', 'show_ui', 'show_in_nav_menus', 'show_in_rest', 'rest_base', 'has_archive', 'exclude_from_search', 'capability_type', 'hierarchical', 'rewrite', 'query_var', 'menu_position', 'show_in_menu', 'menu_icon', 'description');
          $post_type_args = array();
          foreach($overwrite_args as $value){
            if(isset($postType[$value])){
              if($postType[$value] === '1')
                $post_type_args[$value] = true;
              elseif($postType[$value] === '0'){
                $post_type_args[$value] = false;
              }
              elseif($postType[$value])
                $post_type_args[$value] = $postType[$value];
            }
          }

          if(isset($postType['has_archive']) && $postType['has_archive'] == 1 && isset($postType['has_archive_string']) && $postType['has_archive_string'])
            $post_type_args['has_archive'] = $postType['has_archive_string'];

          if(isset($postType['query_var_slug']) && $postType['query_var_slug'] && isset($postType['query_var']) && $postType['query_var'] == 1)
            $post_type_args['query_var'] = $postType['query_var_slug'];

          if(isset($postType['show_in_menu_string']) && $postType['show_in_menu_string'] && isset($postType['show_in_menu']) && $postType['show_in_menu'] == 1)
            $post_type_args['show_in_menu'] = $postType['show_in_menu_string'];

          if(isset($postType['rewrite']) && $postType['rewrite'] == 1){
            $rewrite = array();
            if(isset($postType['rewrite_slug']) && $postType['rewrite_slug'])
              $rewrite['slug'] = $postType['rewrite_slug'];
            if(isset($postType['rewrite_withfront']))
              $rewrite['with_front'] = $postType['rewrite_withfront'];
            if(count($rewrite) >= 1)
              $post_type_args['rewrite'] = $rewrite;
          }

          if(isset($postType['bbwpcf_pt_supports']) && is_array($postType['bbwpcf_pt_supports']) && count($postType['bbwpcf_pt_supports']) >= 1){
            if(in_array('none', $postType['bbwpcf_pt_supports']))
              $post_type_args['supports'] = false;
            else
              $post_type_args['supports'] = $postType['bbwpcf_pt_supports'];
          }

          if(isset($postType['bbwpcf_pt_taxonomies']) && is_array($postType['bbwpcf_pt_taxonomies']) && count($postType['bbwpcf_pt_taxonomies']) >= 1){
              $post_type_args['taxonomies'] = $postType['bbwpcf_pt_taxonomies'];
          }

          $args = array_merge(
              // Default
              array(
                  'label'                 => $postType['label'],
                  'labels'                => $labels,
                  'public'                => true,
                  'show_ui'               => true,
                  'capability_type'       => 'post',
                  'has_archive'           => true,
                  'hierarchical'          => false,
                  'show_in_nav_menus'     => true,
                  'menu_position'         => null,
                  '_builtin'              => false,
                  'supports' => array( 'title', 'editor', 'thumbnail')
              ),
              $post_type_args
          );
          //db($args);exit();
          // Register the post type
          register_post_type( $key, $args );

        }//if end here
      }// foreach ends here

  }

}
