<?php

class User_registration_process
{
  function __construct()
  {
    //Admin Styles & Scripts Enqueue
    add_action('admin_enqueue_scripts', array($this, 'urf_admin_styles_scripts'));

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

    //Add custom meta box to the Post Type
    add_action('add_meta_boxes', array($this, 'add_user_fields_meta_box'));
    //Save Metafields Value
    add_action('save_post', array($this, 'save_user_meta_box_fields'));

    //AJAX Registration Form Submit
    add_action('wp_ajax_registration_bio_form_submit_function', array($this, 'registration_bio_form_submit_function'));
    add_action('wp_ajax_nopriv_registration_bio_form_submit_function', array($this, 'registration_bio_form_submit_function'));

    //Add Additional Option Under CPT Menu called User Bio List
    add_action('admin_menu', array($this, 'user_bio_list_panel'));

  }

  /*Styles and Scripts for Dashboard Admin Page*/
  public function urf_admin_styles_scripts()
  {
    if (isset($_GET['page']) && isset($_GET['post_type'])) {
      if ($_GET['page'] == 'user-bio-list' && $_GET['post_type'] == 'user_bio')
        wp_enqueue_style('urf-admin-css', URF_PLUGIN_URL . 'assets/css/admin-css.css');
    }
  }

  /*Register Custom Post Type User Bio*/
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

  /*Register Custom Taxonomy*/
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

  /*Dashboard Access Denial*/
  public function dashboard_access_denial()
  {
    if (is_admin() && !defined('DOING_AJAX') && (current_user_can('subscriber') || current_user_can('contributor'))) {
      wp_redirect(home_url());
      exit;
    }
  }

  /*AJAX Login Form Submit*/
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

  /*AJAX Registration Form Submit*/
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

  /*Adding User Post Metafields*/
  public function add_user_fields_meta_box()
  {
    add_meta_box(
      'user_fields_meta_box',
      'Additional User Information',
      array($this, 'show_user_fields_meta_box'),
      'user_bio',
      'normal',
      'high'
    );
  }

  /*Showing Metafields on User Post Posttype Screen*/
  public function show_user_fields_meta_box($post)
  {
    include(URF_PLUGIN_PATH . '/includes/meta-box/meta-box-layout.php');
  }

  /*Save Metabox Data to its respective field*/
  public function save_user_meta_box_fields($post_id)
  {
    //Check Autosave
    if (defined('DOING_AUTOSAVE') && DOING_AUTOSAVE) {
      return $post_id;
    }
    //Check for Revisons
    if ($parent_id = wp_is_post_revision($post_id)) {
      $post_id = $parent_id;
    }

    $post_meta = get_post_meta($post_id);

    if ($_POST['name'] && $_POST['name'] !== $post_meta['name'][0]) {
      update_post_meta($post_id, 'name', $_POST['name']);
    } elseif ('' === $_POST['name'] && $post_meta['name'][0]) {
      delete_post_meta($post_id, 'name', $post_meta['name'][0]);
    }

    if ($_POST['address'] && $_POST['address'] !== $post_meta['address'][0]) {
      update_post_meta($post_id, 'address', $_POST['address']);
    } elseif ('' === $_POST['address'] && $post_meta['address'][0]) {
      delete_post_meta($post_id, 'address', $post_meta['address'][0]);
    }

    if ($_POST['phone'] && $_POST['phone'] !== $post_meta['phone'][0]) {
      update_post_meta($post_id, 'phone', $_POST['phone']);
    } elseif ('' === $_POST['phone'] && $post_meta['phone'][0]) {
      delete_post_meta($post_id, 'phone', $post_meta['phone'][0]);
    }

    if ($_POST['email'] && $_POST['email'] !== $post_meta['email'][0]) {
      update_post_meta($post_id, 'email', $_POST['email']);
    } elseif ('' === $_POST['email'] && $post_meta['email'][0]) {
      delete_post_meta($post_id, 'email', $post_meta['email'][0]);
    }

    if ($_POST['about'] && $_POST['about'] !== $post_meta['about'][0]) {
      update_post_meta($post_id, 'about', $_POST['about']);
    } elseif ('' === $_POST['about'] && $post_meta['about'][0]) {
      delete_post_meta($post_id, 'about', $post_meta['about'][0]);
    }

    if ($_POST['years_of_experience'] && $_POST['years_of_experience'] !== $post_meta['years_of_experience'][0]) {
      update_post_meta($post_id, 'years_of_experience', $_POST['years_of_experience']);
    } elseif ('' === $_POST['years_of_experience'] && $post_meta['years_of_experience'][0]) {
      delete_post_meta($post_id, 'years_of_experience', $post_meta['years_of_experience'][0]);
    }

    if ($_POST['education'] && $_POST['education'] !== $post_meta['education'][0]) {
      update_post_meta($post_id, 'education', $_POST['education']);
    } elseif ('' === $_POST['education'] && $post_meta['education'][0]) {
      delete_post_meta($post_id, 'education', $post_meta['education'][0]);
    }
  }

  /*Registration Bio Form Sumbit*/
  public function registration_bio_form_submit_function()
  {
    $curr_user_id = get_current_user_id();
    $user_login = get_user_meta($curr_user_id);
    echo $user_login['nickname'][0];
    print_r($_POST);
    wp_die();
  }

  /*User Bio List Panel to Backend */
  public function user_bio_list_panel()
  {
    add_submenu_page(
      'edit.php?post_type=user_bio',
      'User Bio List',
      'User Bio List',
      'manage_options',
      'user-bio-list',
      array($this, 'user_bio_list_function'), // $callback
    );
  }

  /*User Bio List Panel Callback */
  public function user_bio_list_function()
  {
    include(URF_PLUGIN_PATH . '/includes/template-parts/user-bio-table.php');
  }
}

new User_registration_process;