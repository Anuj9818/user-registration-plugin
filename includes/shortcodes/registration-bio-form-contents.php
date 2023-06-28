<?php
//Add shortcode for page register bio content
if (!function_exists('urf_user_bio_page_shortcode')) {
  add_shortcode('urf_registration_form_content', 'urf_user_bio_page_shortcode');
  function urf_user_bio_page_shortcode()
  {

    global $wpdb;

    $current_date = date('Y-m-d');

    $curr_user_id = get_current_user_id();
    $user_login = get_user_meta($curr_user_id);

    //Get User Post ID by Current User Email/Nickname
    $name_val = '';
    $address_val = '';
    $phone_val = '';
    $about_val = '';
    $experience_val = '';
    $education_val = '';
    if (isset($user_login['nickname'])) {
      $user_post_id = $wpdb->get_var("SELECT ID FROM $wpdb->posts WHERE post_title = '" . $user_login['nickname'][0] . "'");

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
      //print_r($meta);
    }
    ob_start(); ?>
    <div class="user-registration-bio-section">
      <form action="<?= esc_url(home_url('/')); ?>" autocomplete="off" method="post" id="user_registration_bio_form">
        <h3>Personal Information</h3>
        <div class="form-elements-container">
          <div class="form-element form-group">
            <input type="text" class="form-control" name="name" id="name" value="" placeholder="Enter your Full Name"
              required>
          </div>
          <div class="form-element form-group">
            <input type="text" class="form-control" name="address" id="address" value="" placeholder="Enter your Address"
              required>
          </div>
          <div class="form-element form-group">
            <input type="text" class="form-control" name="phone" id="phone" value="" maxlength="10"
              placeholder="Enter your Contact Number" required>
          </div>

          <div class="form-element form-group">
            <textarea id="about" placeholder="Something about yourself..." class="form-control" name="about" rows="4"
              cols="50" required></textarea>
          </div>

        </div><br>

        <h3>Professional Experience</h3>
        <div class="form-elements-container">
          <div class="form-element form-group" style="height:60px;">

            <select name="occupation" id="occupation" class="form-control" required>
              <option value="">Select your Occupation Type</option>
              <?php
              $terms = get_terms(['hide_empty' => false]);
              if (!empty($terms)) {
                foreach ($terms as $term):
                  if ($term->taxonomy == 'occupation_type') {
                    echo '<option value="' . $term->slug . '">' . $term->name . '</option>';
                  }
                endforeach;
              } ?>
            </select>
          </div>

          <div class="form-element form-group">
            <input class="form-control" type="number" id="years_of_experience" name="years_of_experience" min="1" max="15"
              placeholder="Total Years of Experience" required>
          </div>

        </div>

        <h3>Educational Background</h3>
        <div class="form-elements-container">

          <div class="form-element form-group">
            <textarea id="education" placeholder="Enter your education background..." class="form-control" name="education"
              rows="4" cols="50" required></textarea>
          </div>

          <div class="form-element form-group">
            <input id="register-bio-form-nonce" type="hidden" name="ajax_nonce"
              value="<?= wp_create_nonce('ajax_nocne_value'); ?>">
            <input id="bio-submit-date" type="hidden" name="bio-submit-date" value="<?= $current_date; ?>">
            <input name="bio-submit" class="btn btn-warning submit-btn" type="submit" name="Submit">
          </div>

        </div>
      </form>
    </div>
    <?php
    $output = ob_get_clean();
    return $output;
  }
}