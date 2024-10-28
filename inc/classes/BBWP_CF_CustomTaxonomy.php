<?php
// exit if file is called directly
if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

class BBWP_CF_CustomTaxonomy
{
  private $taxonomy_names;
  public $prefix = 'bbwpcustomfields';

  // Class constructor
  public function __construct()
  {
      $user_created_taxonomies = SerializeStringToArray(get_option($this->prefix('user_created_taxonomies')));

      // Add action to register the post type, if the post type does not already exist
      if($user_created_taxonomies && is_array($user_created_taxonomies) && count($user_created_taxonomies) >= 1){
        $this->taxonomy_names = $user_created_taxonomies;
        add_action( 'init', array( &$this, 'register_taxonomy' ) );
      }


  }

  public function prefix($string = '', $underscore = "_"){
    return $this->prefix.$underscore.$string;
  }

  // Method which registers the post type
  public function register_taxonomy()
  {
      foreach($this->taxonomy_names as $key=>$taxonomy){
        if( ! taxonomy_exists( $key ) )
        {
          // Default labels, overwrite them with the given labels.
          $taxonomy_labels = array();
          $overwrite_labels = array('menu_name', 'all_items', 'edit_item', 'view_item', 'update_item', 'add_new_item', 'new_item_name', 'parent_item', 'parent_item_colon', 'search_items', 'popular_items', 'separate_items_with_commas', 'add_or_remove_items', 'choose_from_most_used', 'not_found', 'no_terms', 'items_list_navigation', 'items_list');
          foreach($overwrite_labels as $value){
            if(isset($taxonomy[$value]) && $taxonomy[$value])
                $taxonomy_labels[$value] = $taxonomy[$value];
          }
          $labels = array_merge(
              // Default
              array(
                  'name'                  => _x( $taxonomy['label'], 'taxonomy general name' ),
                  'singular_name'         => _x( $taxonomy['singular_label'], 'taxonomy singular name' ),
                  'search_items'          => __( 'Search ' . $taxonomy['label'] ),
                  'all_items'             => __( 'All ' . $taxonomy['label'] ),
                  'parent_item'           => __( 'Parent ' . $taxonomy['singular_label'] ),
                  'parent_item_colon'     => __( 'Parent ' . $taxonomy['singular_label'] . ':' ),
                  'edit_item'             => __( 'Edit ' . $taxonomy['singular_label'] ),
                  'update_item'           => __( 'Update ' . $taxonomy['singular_label'] ),
                  'add_new_item'          => __( 'Add New ' . $taxonomy['singular_label'] ),
                  'new_item_name'         => __( 'New ' . $taxonomy['singular_label'] . ' Name' ),
                  'menu_name'             => __( $taxonomy['label'] ),
              ),
              // Given labels
              $taxonomy_labels
          );



          // Default arguments, overwritten with the given arguments
          $overwrite_args = array('public', 'hierarchical', 'show_ui', 'show_in_menu', 'show_in_nav_menus', 'show_admin_column', 'show_in_rest', 'rest_base', 'show_in_quick_edit', 'description');
          $taxonomy_args = array();
          foreach($overwrite_args as $value){
            if(isset($taxonomy[$value]))
              if($taxonomy[$value] === '1')
                $taxonomy_args[$value] = true;
              elseif($taxonomy[$value] === '0')
                $taxonomy_args[$value] = false;
              elseif($taxonomy[$value])
                $taxonomy_args[$value] = $taxonomy[$value];
          }

          if(isset($taxonomy['query_var']) && $taxonomy['query_var'] == 0)
            $taxonomy_args['query_var'] = false;
          elseif(isset($taxonomy['query_var_slug']) && $taxonomy['query_var_slug'] && isset($taxonomy['query_var']) && $taxonomy['query_var'] == 1)
            $taxonomy_args['query_var'] = $taxonomy['query_var_slug'];

          if(isset($taxonomy['rewrite']) && $taxonomy['rewrite'] == 0)
            $taxonomy_args['rewrite'] = false;
          elseif(isset($taxonomy['rewrite']) && $taxonomy['rewrite'] == 1){
            $rewrite = array();
            if(isset($taxonomy['rewrite_slug']) && $taxonomy['rewrite_slug'])
              $rewrite['slug'] = $taxonomy['rewrite_slug'];
            if(isset($taxonomy['rewrite_withfront']))
              $rewrite['with_front'] = $taxonomy['rewrite_withfront'];
            if(isset($taxonomy['rewrite_hierarchical']))
              $rewrite['hierarchical'] = $taxonomy['rewrite_hierarchical'];
            if(count($rewrite) >= 1)
              $taxonomy_args['rewrite'] = $rewrite;
          }


          $args = array_merge(
              // Default
              array(
                  'label'                 => $taxonomy['label'],
                  'labels'                => $labels,
                  'public'                => true,
                  'show_ui'               => true,
                  'hierarchical' 		      => true,
                  'show_in_nav_menus'     => true,
                  '_builtin'              => false,
              ),
              // Given
              $taxonomy_args
          );

          // Register the post type
          if(isset($taxonomy['bbwpcf_posts']) && is_array($taxonomy['bbwpcf_posts']) && count($taxonomy['bbwpcf_posts']) >= 1)
            register_taxonomy( $taxonomy['name'], $taxonomy['bbwpcf_posts'], $args );
        }//if end here
      }// foreach ends here

  }

}
