<?php

if (!class_exists('User_registration_process')):
  class User_registration_process
  {
    function __construct()
    {
      //Admin Styles & Scripts Enqueue
      add_action('admin_enqueue_scripts', array($this, 'urf_admin_styles_scripts'));

      // Create Custom Post Type: User Bio
      add_action('init', array($this, 'urf_create_post_type'));

      //Register Custom Taxonomy
      add_action('init', array($this, 'urf_register_custom_taxonomy'));

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

      //AJAX Registration Bio Form Submit
      add_action('wp_ajax_registration_bio_form_submit_function', array($this, 'registration_bio_form_submit_function'));
      add_action('wp_ajax_nopriv_registration_bio_form_submit_function', array($this, 'registration_bio_form_submit_function'));

      //Add Additional Option Under CPT Menu called User Bio List
      add_action('admin_menu', array($this, 'user_bio_list_panel'));

      //AJAX CSV Export Function
      add_action('wp_ajax_filter_user_data', array($this, 'filter_user_data'));
      add_action('wp_ajax_nopriv_filter_user_data', array($this, 'filter_user_data'));

      //AJAX CSV Export Function
      add_action('wp_ajax_user_csv_export', array($this, 'user_csv_export'));
      add_action('wp_ajax_nopriv_user_csv_export', array($this, 'user_csv_export'));

      //Custom Single Page Template for Post Type 'user-bio'
      add_filter('single_template', array($this, 'user_bio_post_custom_template'));

    }

    /*Styles and Scripts for Dashboard Admin Page*/
    public function urf_admin_styles_scripts()
    {
      if (isset($_GET['page']) && isset($_GET['post_type'])) {
        if ($_GET['page'] == 'user-bio-list' && $_GET['post_type'] == 'user_bio') {
          wp_enqueue_style('urf-admin-css', URF_PLUGIN_URL . 'assets/css/admin-css.css');

          $ajax_url = admin_url('admin-ajax.php');
          wp_enqueue_script('urf-jQuery', URF_PLUGIN_URL . '/assets/js/custom-jquery.js', array(), '3.6.4', true);
          wp_enqueue_script('urf-admin-js', URF_PLUGIN_URL . '/assets/js/admin-js.js', array('urf-jQuery'), '1.0', true);
          wp_localize_script('urf-admin-js', 'ajax_url', array($ajax_url, site_url()));
        }
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
          'delete_published_posts' => false,
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
    public function urf_register_custom_taxonomy()
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
        'rewrite' => array('slug' => 'occupation'),
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

        //Create New User 
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
        //Redirect to User Bio Page for Already existing Users
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

      if (isset($_POST['submit_date'])) {
        if ($_POST['submit_date'] !== $post_meta['submit_date'][0]) {
          update_post_meta($post_id, 'submit_date', $_POST['submit_date']);
        } elseif ('' === $_POST['submit_date'] && $post_meta['submit_date'][0]) {
          delete_post_meta($post_id, 'submit_date', $post_meta['submit_date'][0]);
        }
      }

      if (isset($_POST['name'])) {
        if ($_POST['name'] !== $post_meta['name'][0]) {
          update_post_meta($post_id, 'name', $_POST['name']);
        } elseif ('' === $_POST['name'] && $post_meta['name'][0]) {
          delete_post_meta($post_id, 'name', $post_meta['name'][0]);
        }
      }

      if (isset($_POST['address'])) {
        if ($_POST['address'] !== $post_meta['address'][0]) {
          update_post_meta($post_id, 'address', $_POST['address']);
        } elseif ('' === $_POST['address'] && $post_meta['address'][0]) {
          delete_post_meta($post_id, 'address', $post_meta['address'][0]);
        }
      }

      if (isset($_POST['phone'])) {
        if ($_POST['phone'] !== $post_meta['phone'][0]) {
          update_post_meta($post_id, 'phone', $_POST['phone']);
        } elseif ('' === $_POST['phone'] && $post_meta['phone'][0]) {
          delete_post_meta($post_id, 'phone', $post_meta['phone'][0]);
        }
      }

      if (isset($_POST['email'])) {
        if ($_POST['email'] !== $post_meta['email'][0]) {
          update_post_meta($post_id, 'email', $_POST['email']);
        } elseif ('' === $_POST['email'] && $post_meta['email'][0]) {
          delete_post_meta($post_id, 'email', $post_meta['email'][0]);
        }
      }

      if (isset($_POST['about'])) {
        if ($_POST['about'] !== $post_meta['about'][0]) {
          update_post_meta($post_id, 'about', $_POST['about']);
        } elseif ('' === $_POST['about'] && $post_meta['about'][0]) {
          delete_post_meta($post_id, 'about', $post_meta['about'][0]);
        }
      }

      if (isset($_POST['experience'])) {
        if ($_POST['experience'] !== $post_meta['experience'][0]) {
          update_post_meta($post_id, 'experience', $_POST['experience']);
        } elseif ('' === $_POST['experience'] && $post_meta['experience'][0]) {
          delete_post_meta($post_id, 'experience', $post_meta['experience'][0]);
        }
      }

      if (isset($_POST['education'])) {
        if ($_POST['education'] !== $post_meta['education'][0]) {
          update_post_meta($post_id, 'education', $_POST['education']);
        } elseif ('' === $_POST['education'] && $post_meta['education'][0]) {
          delete_post_meta($post_id, 'education', $post_meta['education'][0]);
        }
      }
    }

    /*AJAX Registration Bio Form Sumbit*/
    public function registration_bio_form_submit_function()
    {
      $curr_user_id = get_current_user_id();
      $user_login = get_user_meta($curr_user_id);

      //Get User Post ID by Current User Email/Nickname
      $user_post = get_page_by_title($user_login['nickname'][0], OBJECT, 'user_bio');
      echo $user_post->ID;

      //Update Related Post's Meta
      update_post_meta($user_post->ID, 'submit_date', $_POST['submit_date']);
      update_post_meta($user_post->ID, 'name', $_POST['name']);
      update_post_meta($user_post->ID, 'address', $_POST['address']);
      update_post_meta($user_post->ID, 'phone', $_POST['phone']);
      update_post_meta($user_post->ID, 'about', $_POST['about']);
      update_post_meta($user_post->ID, 'occupation', $_POST['occupation']);
      update_post_meta($user_post->ID, 'experience', $_POST['experience']);
      update_post_meta($user_post->ID, 'education', $_POST['education']);

      // Get term by name 
      $user_post_term = get_term_by('slug', $_POST['occupation'], 'occupation_type');
      wp_set_post_terms($user_post->ID, array($user_post_term->term_id), 'occupation_type');

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

    /*Filter User Bio Data */
    public function filter_user_data()
    {
      if (isset($_POST['filter_value'])) {
        if ($_POST['filter_value'] != '') {
          //Show on the basis of value selected
          $query_array = ['post_type' => 'user_bio', 'posts_per_page' => -1, 'post_status' => [$_POST['filter_value']], 'orderby' => 'post_title'];
        } else {
          //Show All
          $query_array = ['post_type' => 'user_bio', 'posts_per_page' => -1, 'orderby' => 'post_title'];
        }
      }
      $user_list = new WP_Query($query_array);
      if ($user_list->have_posts()): ?>
        <table class="styled-table">
          <thead>
            <tr>
              <th>Date</th>
              <th>Email</th>
              <th>Name</th>
              <th>Address</th>
              <th>Phone</th>
              <th>Occupation</th>
              <th>Exp in yrs.</th>
              <th>Status</th>
              <th></th>
            </tr>
          </thead>
          <tbody>
            <?php
            while ($user_list->have_posts()):
              $user_list->the_post();
              $meta = get_post_meta(get_the_ID());
              $submit_date_val = isset($meta['submit_date'][0]) ? $meta['submit_date'][0] : '-';
              $name_val = isset($meta['name'][0]) ? $meta['name'][0] : '-';
              $address_val = isset($meta['address'][0]) ? $meta['address'][0] : '-';
              $phone_val = isset($meta['phone'][0]) ? $meta['phone'][0] : '-';
              $experience_val = isset($meta['experience'][0]) ? $meta['experience'][0] : '0';
              $status = get_post_status(get_the_ID()); ?>
              <tr>
                <td>
                  <?= $submit_date_val; ?>
                </td>
                <td>
                  <?php the_title(); ?>
                </td>
                <td>
                  <?= $name_val; ?>
                </td>
                <td>
                  <?= $address_val; ?>
                </td>
                <td>
                  <?= $phone_val; ?>
                </td>
                <td>
                  <?php
                  $terms = get_the_terms(get_the_ID(), 'occupation_type');
                  $counter = 1;
                  foreach ($terms as $term) {
                    if ($counter > 1)
                      echo ',';
                    echo $term->name;
                    $counter++;
                  } ?>
                </td>
                <td>
                  <?= $experience_val; ?>
                </td>
                <td>
                  <?php if ($status == 'pending') {
                    echo 'Pending Request';
                  } elseif ($status == 'publish') {
                    echo 'Verified Request';
                  } else {
                    echo '-';
                  } ?>
                </td>
                <td><button id="CSV-<?= get_the_ID(); ?>" type="button" class="csv-download"
                    onclick="downloadCSV(this.id, event)">Export
                    CSV</button></td>
              </tr>
              <?php
            endwhile;
            wp_reset_postdata(); ?>
          </tbody>
        </table>
        <?php
      else:
        echo '<h3 style="text-align:center;">No Results Found</h3>';
      endif;
      wp_die();
    }

    /*CSV Export Function */
    public function user_csv_export()
    {
      if (isset($_POST['post_id'])) {
        $timestamp = time();
        $filename = 'USR-' . $_POST['post_id'] . $timestamp;
        $user_post_id = explode('-', $_POST['post_id'])[1];

        //Fetch Metadata
        $meta = get_post_meta($user_post_id);
        if (isset($meta['name'][0]))
          $name_val = $meta['name'][0];
        if (isset($meta['address'][0]))
          $address_val = $meta['address'][0];
        if (isset($meta['phone'][0]))
          $phone_val = $meta['phone'][0];
        if (isset($meta['about'][0]))
          $about_val = $meta['about'][0];
        if (isset($meta['experience'][0]))
          $experience_val = $meta['experience'][0];
        if (isset($meta['education'][0]))
          $education_val = $meta['education'][0];
        $email = get_the_title($user_post_id);
        $terms = get_the_terms($user_post_id, 'occupation_type');
        // print_r($terms);
        $term_vals = '';
        $counter = 1;
        if (!empty($terms)) {
          foreach ($terms as $term) {
            if ($counter > 1)
              $term_vals .= ',';
            $term_vals .= $term->name;
            $counter++;
          }
        } else {
          echo $term_vals .= '-';
        }

        //CSV Code 
        header('Content-type: text/csv');
        header('Content-Disposition: attachment; filename="' . $filename . '"');
        header('Pragma: no-cache');
        header('Expires: 0');

        $file = fopen('php://output', 'w');

        fputcsv($file, array('Email', 'FullName', 'Address', 'Contact', 'About', 'Occupation Type', 'Experience', 'Education'));

        fputcsv($file, array($email, $name_val, $address_val, $phone_val, $about_val, $term_vals, $experience_val, $education_val));

        exit();

      }
      wp_die();
    }

    /*Custom Single Page Template */
    function user_bio_post_custom_template($template)
    {

      global $post;

      if ('user_bio' === $post->post_type && locate_template(array('user_bio.php')) !== $template) {
        return URF_PLUGIN_PATH . 'includes/single/single-user_bio.php';
      }

      return $template;

    }
  }

  new User_registration_process;
endif;