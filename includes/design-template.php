<?php

class User_registration_design
{
  function __construct()
  {
    //Create User Registration Page 
    $this->create_registration_page();

    //Add Post States for User Registration Page
    add_filter('display_post_states', array($this, 'user_registration_post_state'), 10, 2);

    //Enqueue Style and Scripts for User Registration Page
    add_action('wp_enqueue_scripts', array($this, 'user_registration_styles'));

  }
  /* Auto create a User Registration Form On Plugin Activation */
  public function create_registration_page()
  {
    $page_slug = 'user-registration-page'; // Slug of the Post
    $page_content = '
      <div class="user-registration-section">
        <form action="' . esc_url(home_url('/')) . '" autocomplete="off" accept-charset="utf-8" method="post"
          id="user_registration_form" role="form">
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
              <input class="btn btn-warning submit-btn" type="submit" name="Submit">
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
    } else {
      $created_page = get_page_by_path('user-registration-page');
      $update_post = array(
        'ID' => $created_page->ID,
        'post_content' => $page_content,
      );

      // Update the post into the database
      wp_update_post($update_post);

    }
  }

  /*Set Post State to User Registration Form For Differenciation */
  function user_registration_post_state($post_states, $post)
  {

    if ($post->post_name == 'user-registration-page') {
      $post_states[] = 'User Registration Form Page By Anuj Shrestha';
    }

    return $post_states;
  }

  /*Styles and Scripts for User Registration Page */
  public function user_registration_styles()
  {
    $ver = date("Ymd h:i:s");

    if (is_page('user-registration-page')) {
      wp_enqueue_style('urf-bootstrap', URF_PLUGIN_URL . 'assets/css/bootstrap.css', array(), $ver);
      wp_enqueue_style('urf-main', URF_PLUGIN_URL . 'assets/css/main.css', array(), $ver);
    }
  }

}

new User_registration_design;