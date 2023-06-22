<?php

class User_registration_design
{
  function __construct()
  {
    //Create Login Form
    $this->create_login_form();

    //Create User Registration Page 
    $this->create_registration_form_bio_page();

    //Redirect User to Login Form
    add_action('init', array($this, 'redirect_login_form'));

    //Add Post States for User Registration Page
    add_filter('display_post_states', array($this, 'user_registration_post_state'), 10, 2);

    //Enqueue Style and Scripts for User Registration Page
    add_action('wp_enqueue_scripts', array($this, 'user_registration_styles'));

  }

  /*Create Login Custom Form Page*/
  public function create_login_form()
  {
    $page_slug = 'urf-user-login-form-page'; // Slug of the Post
    if (isset($_GET['login_check'])) {
      if ($_GET['login_check'] == 'no') {
        $page_content = '
          <style>
            .user-login-form-section .form-elements-container .form-element .passowrd-check-err{
              display: block;
            }
          </style>';
      }
    }
    $page_content .= '
      <div class="login-register">
        <div class="login-wrapper">
          <a class="login-form" href="javascript:void(0)">Login</a>
        </div>
        <div class="register-wrapper">
          <a class="register-form" href="javascript:void(0)">Register</a>
        </div>
      </div>
      <div class="user-registration-form-section">
        <form action="' . esc_url(home_url('/')) . '" method="post" id="user_registration_form">
          <h3>Registration Form</h3><br>
          <div class="form-elements-container">
            <div class="form-element form-group">
              <label for="username">Enter Your Email</label>
              <input id="user-email" type="email" class="form-control" name="user-email" placeholder="" required>
            </div>
            <div class="form-element form-group">
              <label for="password">Enter Your Password</label>
              <input id="user-password" type="password" class="form-control" name="password" minlength="8" placeholder="" required>
            </div>
            <div class="form-element form-group">
              <label for="password">Re-enter Your Password</label>
              <input id="user-re-password" type="password" class="form-control" name="re-password" minlength="8" placeholder="" required>
              <p class="passowrd-check-err">Passwords do not match. Please re-enter. </p>
              <p class="passowrd-check-ok">Passwords matched.</p>
            </div>
            <div class="form-element form-group">
              <input id="register-form-nonce" type="hidden" name="register_ajax_nonce" value="' . wp_create_nonce('register_ajax_nocne_value') . '">
              <input name="register-submit" class="btn btn-warning submit-btn register-submit" type="submit" value="Register" name="register-submit">
            </div>
          </div>
        </form>
      </div>
      <div class="user-login-form-section">
        <form action="' . esc_url(home_url('/')) . '" method="post" id="user_login_form">
          <h3>Login Form</h3><br>
          <div class="form-elements-container">
            <div class="form-element form-group">
              <label for="username">Enter Your Email</label>
              <input id="user-login-email" type="email" class="form-control" name="user-email" placeholder="" required>
            </div>
            <div class="form-element form-group">
              <label for="password">Enter Your Password</label>
              <input id="user-login-password" type="password" class="form-control" name="password" minlength="8" placeholder="" required>
            </div>
            <div class="form-element form-group">
              <p class="passowrd-check-err">Username or Password Incorrect.</p>
              <input id="login-form-nonce" type="hidden" name="login_ajax_nonce" value="' . wp_create_nonce('ajax_login_nocne_value') . '">
              <input name="login-submit" class="btn btn-warning submit-btn login-submit" type="submit" name="login-submit" value="Log In">
            </div>
          </div>
        </form>
      </div>';
    $new_page = array(
      'post_type' => 'page',
      'post_title' => 'User Login Page',
      'post_content' => $page_content,
      'post_status' => 'publish',
      'post_name' => $page_slug // Slug of the Post
    );

    if (!get_page_by_path($page_slug, OBJECT, 'page')) { // Check If Page Not Exits
      $new_page_id = wp_insert_post($new_page);
    }
    // else {
    //   $created_page = get_page_by_path($page_slug);
    //   $update_post = array(
    //     'ID' => $created_page->ID,
    //     'post_content' => $page_content,
    //   );

    //   // Update the post into the database
    //   wp_update_post($update_post);
    // }
  }

  /* Auto create a User Registration Form Bio Page after Login/Register  */
  public function create_registration_form_bio_page()
  {
    $page_slug = 'urf-user-registration-bio-page'; // Slug of the Post
    $page_content = '
      <div class="user-registration-bio-section">
        <form action="' . esc_url(home_url('/')) . '" autocomplete="off" method="post"
          id="user_registration_bio_form">
          <h3>Personal Information</h3>
          <div class="form-elements-container">
            <div class="form-element form-group">
              <input type="text" class="form-control" name="name" placeholder="Enter your Full Name" required>
            </div>
            <div class="form-element form-group">
              <input type="text" class="form-control" name="address" placeholder="Enter your Address" required>
            </div>                
            <div class="form-element form-group">
              <input type="text" class="form-control" name="phone" minlength="10" placeholder="Enter your Contact Number" required>
            </div>
            <div class="form-element form-group" style="height: 70px;">
              <p>Select your Gender</p>

              <div class="form-radio">
                <input type="radio" id="html" name="gender" value="male" checked>
                <label for="html">Male</label><br>
                
                <input type="radio" id="css" name="gender" value="female">
                <label for="css">Female</label><br>
                
                <input type="radio" id="javascript" name="gender" value="other">
                <label for="javascript">Others</label>
              </div>
            
            </div>

            <div class="form-element form-group">
              <input type="email" class="form-control" name="email" placeholder="Enter your Mail Address" required>
            </div>

            <div class="form-element form-group">
              <textarea placeholder="Something about yourself..." class="form-control" name="about" rows="4" cols="50"></textarea>
            </div> 

          </div><br>

          <h3>Professional Experience</h3>
          <div class="form-elements-container">
            <div class="form-element form-group" style="height:60px;">

              <select name="cars" id="cars" class="form-control">
                <option value="">Select your Occupation Type</option>';
    $terms = get_terms(['occupation_type', 'hide_empty' => false]);
    foreach ($terms as $term):
      if ($term->slug != 'uncategorized' && $term->slug != 'twentytwentytwo')
        $page_content .= '<option value="' . $term->slug . '">' . $term->name . '</option>';
    endforeach;
    $page_content .= '
              </select>
            </div>

            <div class="form-element form-group">
              <input class="form-control" type="number" name="years of experience" min="1"  max="15" placeholder="Total Years of Experience" required>
            </div>
          
          </div>

          <h3>Educational Background</h3>
          <div class="form-elements-container">

            <div class="form-element form-group">
              <textarea placeholder="Enter your education background..." class="form-control" name="education" rows="4" cols="50"></textarea>
            </div>

            <div class="form-element form-group">
              <input id="register-bio-form-nonce" type="hidden" name="ajax_nonce" value="' . wp_create_nonce('ajax_nocne_value') . '">
              <input name="bio-submit" class="btn btn-warning submit-btn" type="submit" name="Submit">
            </div>

          </div>
        </form>         
      </div>';

    $new_page = array(
      'post_type' => 'page',
      'post_title' => 'User Registration Page',
      'post_content' => $page_content,
      'post_status' => 'publish',
      'post_name' => $page_slug // Slug of the Post
    );

    if (!get_page_by_path($page_slug, OBJECT, 'page')) { // Check If Page Not Exits
      $new_page_id = wp_insert_post($new_page);
    }
    // else {
    //   $created_page = get_page_by_path($page_slug);
    //   $update_post = array(
    //     'ID' => $created_page->ID,
    //     'post_content' => $page_content,
    //   );

    //   // Update the post into the database
    //   wp_update_post($update_post);

    // }
  }

  /*Set Post State to User Registration Form For Differenciation */
  function user_registration_post_state($post_states, $post)
  {

    if ($post->post_name == 'urf-user-registration-bio-page') {
      $post_states[] = 'User Registration Form Page By Anuj Shrestha';
    }
    if ($post->post_name == 'urf-user-login-form-page') {
      $post_states[] = 'User Login Form Page By Anuj Shrestha';
    }

    return $post_states;
  }

  /*Redirect User to Login Form first*/
  public function redirect_login_form()
  {
    if (isset($_SERVER['HTTPS']) && $_SERVER['HTTPS'] === 'on')
      $url = "https://";
    else
      $url = "http://";

    // Append the host(domain name, ip) to the URL.   
    $url .= $_SERVER['HTTP_HOST'];

    // Append the requested resource location to the URL   
    $url .= $_SERVER['REQUEST_URI'];
    $target_url_register = site_url() . '/urf-user-registration-bio-page/';
    $redirect_register_page_url = site_url() . '/urf-user-login-form-page/';

    //Redirection from Registration Page for Logged Out Users
    if ($target_url_register === $url) {
      if (!is_user_logged_in()) {
        wp_redirect($redirect_register_page_url);
        exit;
      }
    }

    //Append the requested resource location to the URL   
    $target_url_login = site_url() . '/urf-user-login-form-page/';
    $redirect_login_page_url = site_url() . '/urf-user-registration-bio-page/';

    //Redirection from Login Page  for Logged In Users
    if ($target_url_login === $url) {
      if (is_user_logged_in()) {
        wp_redirect($redirect_login_page_url);
        exit;
      }
    }
  }

  /*Styles and Scripts for User Registration Page */
  public function user_registration_styles()
  {
    $ver = date("Ymd h:i:s");

    if (is_page('urf-user-registration-bio-page') || is_page('urf-user-login-form-page')) {
      wp_enqueue_style('urf-bootstrap', URF_PLUGIN_URL . 'assets/css/bootstrap.css', array(), $ver);
      wp_enqueue_style('urf-main', URF_PLUGIN_URL . 'assets/css/main.css', array(), $ver);

      $ajax_url = admin_url('admin-ajax.php');
      wp_enqueue_script('urf-jQuery', URF_PLUGIN_URL . '/assets/js/custom-jquery.js', array(), '3.6.4', true);
      wp_enqueue_script('urf-form-submission-handler', URF_PLUGIN_URL . '/assets/js/form-submission-handler.js', array('urf-jQuery'), '1.0', true);
      wp_localize_script('urf-form-submission-handler', 'ajax_url', array($ajax_url, site_url()));
    }
  }

}

new User_registration_design;