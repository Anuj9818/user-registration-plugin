<?php
//Here Goes the Single Custom Post Code  ?>
<!DOCTYPE html>
<html lang="en">

<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>Document</title>
  <link rel="stylesheet" href="<?php echo URF_PLUGIN_URL . 'assets/css/bootstrap.css'; ?>">
  <link rel="stylesheet" href="<?php echo URF_PLUGIN_URL . 'assets/css/main.css'; ?>">
</head>

<body>
  <?php
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

  $meta = get_post_meta(get_the_ID());
  //print_r($meta);
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
  $terms = get_the_terms(get_the_ID(), 'occupation_type');
  if (isset($terms[0]))
    $selected_term = $terms[0]->name; ?>
  <section class="user-bio-view">
    <div class="container">
      <div class="user-registration-bio-section">
        <form autocomplete="off" method="post" id="user_registration_bio_form"
          onsubmit="submitForm(<?= get_the_ID(); ?>, event)">
          <h3>User Bio of:
            <?php echo get_the_title(); ?>
          </h3>
          <div class="form-elements-container">
            <div class="form-element form-group">
              <input type="text" class="form-control" name="name" id="name" value="<?= $name_val; ?>"
                placeholder="Enter your Full Name" required>
            </div>
            <div class="form-element form-group">
              <input type="text" class="form-control" name="address" id="address" value="<?= $address_val; ?>"
                placeholder="Enter your Address" required>
            </div>
            <div class="form-element form-group">
              <input type="text" class="form-control" name="phone" id="phone" value="<?= $phone_val; ?>" maxlength="10"
                placeholder="Enter your Contact Number" required>
            </div>

            <div class="form-element form-group">
              <textarea id="about" placeholder="Something about yourself..." class="form-control" name="about" rows="4"
                cols="50" required><?= $about_val; ?></textarea>
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
                      $term_children = get_term_children($term->term_id, 'occupation_type'); ?>
                      <option value="<?= $term->slug; ?>" <?php if ($term->name == $selected_term) {
                          echo 'selected';
                        } ?>>
                        <?php echo $term->name; ?></option>
                      <?php
                    }
                  endforeach;
                } ?>
              </select>
            </div>

            <div class="form-element form-group">
              <input class="form-control" type="number" id="years_of_experience" value="<?= $experience_val; ?>"
                name="years_of_experience" min="1" max="15" placeholder="Total Years of Experience" required>
            </div>

          </div>

          <h3>Educational Background</h3>
          <div class="form-elements-container">

            <div class="form-element form-group">
              <textarea id="education" placeholder="Enter your education background..." class="form-control"
                name="education" rows="4" cols="50" required><?= $education_val; ?></textarea>
            </div>

            <div class="form-element form-group">
              <input id="bio-submit-date" type="hidden" name="bio-submit-date" value="<?= $current_date; ?>">
              <input name="bio-submit" class="btn btn-warning submit-btn" type="submit" name="Submit">
            </div>
          </div>
        </form>
      </div>
    </div>
  </section>
  <?php
  $ajax_url = array(admin_url('admin-ajax.php'), site_url()); ?>
  <script src="<?php echo URF_PLUGIN_URL . 'assets/js/custom-jquery.js'; ?>"> </script>
  <script type="text/javascript">
    //User Bio Form Submission
    function submitForm(post_id, event) {
      event.preventDefault();
      var url = '<?php echo $ajax_url[0]; ?>';
      var submit_date = $('#bio-submit-date').val();
      var name = $('#name').val();
      var address = $('#address').val();
      var phone = $('#phone').val();
      var about = $('#about').val();
      var occupation = $('#occupation').val();
      var experience = $('#years_of_experience').val();
      var education = $('#education').val();
      var register_bio_nonce_check = $('#register-bio-form-nonce').val();
      console.log(submit_date);
      $.ajax({
        type: "post",
        url: url,
        data: {
          action: 'registration_bio_form_submit_function',
          post_id: post_id,
          submit_date: submit_date,
          name: name,
          address: address,
          phone: phone,
          about: about,
          occupation: occupation,
          experience: experience,
          education: education,
          register_bio_nonce_check: register_bio_nonce_check
        },
        error: function (err) {
          console.log(err);
        },
        success: function (response) {
          //console.log(response);
          $('#user_registration_bio_form').remove();
          $('.user-registration-bio-section').fadeIn().append('<h2 style="text-align:center;">Thank you for your response. We will get in touch with you shortly.</h2>');
        },
      });
    }

  </script>
</body>

</html>