<?php

class User_registration_process
{
  function __construct()
  {
    // Create Custom Post Type: User Bio
    add_action('init', array($this, 'urf_create_post_type'));

    //Register Custom Taxonomy
    add_action('init', array($this, 'register_custom_taxonomy'));

    //Deny Access To Dashboard Accept Admin
    add_action('init', array($this, 'dashboard_access_denial'));

    //AJAX Login Form Submit
    add_action('wp_ajax_login_submit_function', array($this, 'login_submit_function'));
    add_action('wp_ajax_nopriv_login_submit_function', array($this, 'login_submit_function'));

    //AJAX Registration Form Submit
    add_action('wp_ajax_registration_submit_function', array($this, 'registration_submit_function'));
    add_action('wp_ajax_nopriv_registration_submit_function', array($this, 'registration_submit_function'));

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
      'supports' => array('title', 'revisions'),
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

  //Dashboard Access Denial
  public function dashboard_access_denial()
  {
    if (is_admin() && !defined('DOING_AJAX') && (current_user_can('subscriber') || current_user_can('contributor'))) {
      wp_redirect(home_url());
      exit;
    }
  }

  //AJAX Login Form Submit
  public function login_submit_function()
  {
    global $user_ID;
    global $wpdb;
    $username = $wpdb->escape($_POST['email']);
    $password = $wpdb->escape($_POST['password']);
    $login_array = [];
    $login_array['user_login'] = $username;
    $login_array['user_password'] = $password;
    $verify_user = wp_signon($login_array, true);
    if (!is_wp_error($verify_user)) {
      $url = site_url() . '/urf-user-registration-bio-page/';
      echo $url;
    } else {
      $url = site_url() . '/urf-user-login-form-page?login_check=no/';
      echo $url;
    }
    wp_die();
  }

  //AJAX Registration Form Submit
  public function registration_submit_function()
  {
    $username = sanitize_user($_POST['email']);
    if (!username_exists($username)) {
      //Create  User 
      $user_id = wp_create_user($_POST['email'], $_POST['password'], $_POST['email']);
      $user = new WP_User($user_id);
      $user->set_role('subscriber');

      //Insert User Post
      $new_user_post = array(
        'post_type' => 'user_bio',
        'post_title' => sanitize_email($_POST['email']),
        'post_status' => 'pending',
      );
      if (!get_page_by_path($_POST['email'], OBJECT, 'user_bio')) { // Check If Page Not Exits
        $new_user_post_id = wp_insert_post($new_user_post);
      }

      //Direct Login After Register
      global $user_ID;
      global $wpdb;
      $username = $wpdb->escape($_POST['email']);
      $password = $wpdb->escape($_POST['password']);
      $login_array = [];
      $login_array['user_login'] = $username;
      $login_array['user_password'] = $password;
      $verify_user = wp_signon($login_array, true);
      if (!is_wp_error($verify_user)) {
        $url = site_url() . '/urf-user-registration-bio-page/';
        echo $url;
      } else {
        $url = site_url() . '/urf-user-login-form-page?login_check=no/';
        echo $url;
      }

    } else {
      global $user_ID;
      global $wpdb;
      $username = $wpdb->escape($_POST['email']);
      $password = $wpdb->escape($_POST['password']);
      $login_array = [];
      $login_array['user_login'] = $username;
      $login_array['user_password'] = $password;
      $verify_user = wp_signon($login_array, true);
      if (!is_wp_error($verify_user)) {
        $url = site_url() . '/urf-user-registration-bio-page/';
        echo $url;
      } else {
        $url = site_url() . '/urf-user-login-form-page?login_check=no/';
        echo $url;
      }
    }
    wp_die();
  }

}

new User_registration_process;