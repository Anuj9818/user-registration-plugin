<?php

class User_registration_process
{
  function __construct()
  {
    // Create Custom Post Type: User Bio
    add_action('init', array($this, 'urf_create_post_type'));

    //Register Custom Taxonomy
    add_action('init', array($this, 'register_custom_taxonomy'));
    // $this->register_custom_taxonomy();
  }

  //Register Custom Post Type User Bio
  public function urf_create_post_type()
  {
    $labels = array(
      'name' => _x('User Bio', 'post type general name'),
      'singular_name' => _x('User Bio', 'post type singular name'),
      'menu_name' => _x('User Bio', 'admin menu'),
      'name_admin_bar' => _x('User Bio', 'add new on admin bar'),
      'add_new' => _x('Add New', 'User Bio'),
      'add_new_item' => __('Add New User Bio'),
      'new_item' => __('New User Bio'),
      'edit_item' => __('Edit User Bio'),
      'view_item' => __('View User Bio'),
      'all_items' => __('All User Bio'),
      'search_items' => __('Search User Bio'),
      'parent_item_colon' => __('Parent User Bio:'),
      'not_found' => __('No User Bio found.'),
      'not_found_in_trash' => __('No User Bio found in Trash.')
    );
    $args = array(
      'labels' => $labels,
      'description' => __('Description.'),
      'menu_icon' => 'dashicons-screenoptions',
      'rewrite' => true,
      'public' => true,
      'publicly_queryable' => true,
      'show_ui' => true,
      'show_in_menu' => true,
      'show_in_nav_menus' => false,
      'query_var' => true,
      'capability_type' => 'post',
      'capabilities' => array(
        'create_posts' => false,
      ),
      'map_meta_cap' => true,
      'has_archive' => false,
      'hierarchical' => true,
      'menu_position' => null,
      'supports' => array('title', 'editor', 'thumbnail', 'revisions'),
    );
    register_post_type('user_bio', $args);
  }
  //Register Custom Taxonomy
  public function register_custom_taxonomy()
  {
    //Regestering Custom Taxonomy 
    $labels = array(
      'name' => _x('Occupation Types', 'taxonomy general name'),
      'singular_name' => _x('Occupation Type', 'taxonomy singular name'),
      'search_items' => __('Search Occupation Types'),
      'all_items' => __('All Occupation Types'),
      'parent_item' => __('Parent Occupation Type'),
      'parent_item_colon' => __('Parent Occupation Type:'),
      'edit_item' => __('Edit Occupation Type'),
      'update_item' => __('Update Occupation Type'),
      'add_new_item' => __('Add New Occupation Type'),
      'new_item_name' => __('New Occupation Type Name'),
      'menu_name' => __('Occupation Type'),
    );

    $args = array(
      'hierarchical' => true,
      'labels' => $labels,
      'show_ui' => true,
      'show_admin_column' => true,
      'query_var' => true,
      'rewrite' => array('slug' => 'occupation_type'),
    );

    register_taxonomy('occupation_type', array('user_bio'), $args);
  }
}
if (is_admin()) {
  new User_registration_process;
}