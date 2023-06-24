<?php
global $post;
$meta = get_post_meta($post->ID); ?>
<p>
  <label for="submit_date"><strong>Submission Date</strong></label>
  <br>
  <?php
  $submit_date_val = isset($meta['submit_date'][0]) ? $meta['submit_date'][0] : '-'; ?>
  <input type="text" name="submit_date" id="submit_date" class="regular_text" value="<?php echo $submit_date_val; ?>"
    style="width: 100%;" readonly>
</p>

<p>
  <label for="name"><strong>Full Name</strong></label>
  <br>
  <?php
  $name_val = isset($meta['name'][0]) ? $meta['name'][0] : '-'; ?>
  <input type="text" name="name" id="name" class="regular_text" value="<?php echo $name_val; ?>" style="width: 100%;">
</p>

<p>
  <label for="address"><strong>Address</strong></label>
  <br>
  <?php
  $address_val = isset($meta['address'][0]) ? $meta['address'][0] : '-'; ?>
  <input type="text" name="address" id="address" class="regular_text" value="<?php echo $address_val; ?>"
    style="width: 100%;">
</p>

<p>
  <label for="phone"><strong>Contact Number</strong></label>
  <br>
  <?php
  $phone_val = isset($meta['phone'][0]) ? $meta['phone'][0] : '-'; ?>
  <input type="text" name="phone" id="phone" class="regular_text" value="<?php echo $phone_val; ?>" minlength="10"
    style="width: 100%;">
</p>

<p>
  <label for="about"><strong>About User</strong></label>
  <br>
  <?php
  $about_val = isset($meta['about'][0]) ? $meta['about'][0] : '-'; ?>
  <textarea name="about" name="about" rows="4" cols="50" style="width: 100%;"><?php echo $about_val; ?></textarea>
</p>


<p>
  <label for="experience"><strong>Years of Experience</strong></label>
  <br>
  <?php
  $experience_val = isset($meta['experience'][0]) ? $meta['experience'][0] : '0'; ?>
  <input type="number" name="experience" min="1" max="15" id="experience" class="regular_text"
    value="<?php echo $experience_val; ?>" style="width: 100%;">
</p>

<p>
  <label for="education"><strong>Education Background</strong></label>
  <br>
  <?php
  $education_val = isset($meta['education'][0]) ? $meta['education'][0] : '-'; ?>
  <textarea name="education" name="education" rows="4" cols="50"
    style="width: 100%;"><?php echo $education_val; ?></textarea>
</p>