<?php
global $post;
$meta = get_post_meta($post->ID); ?>
<p>
  <label for="name"><strong>Full Name</strong></label>
  <br>
  <input type="text" name="name" id="name" class="regular_text" value="<?php echo $meta['name'][0]; ?>"
    style="width: 100%;">
</p>

<p>
  <label for="name"><strong>Address</strong></label>
  <br>
  <input type="text" name="address" id="address" class="regular_text" value="<?php echo $meta['address'][0]; ?>"
    style="width: 100%;">
</p>

<p>
  <label for="name"><strong>Contact Number</strong></label>
  <br>
  <input type="text" name="phone" id="phone" class="regular_text" value="<?php echo $meta['phone'][0]; ?>"
    minlength="10" style="width: 100%;">
</p>

<p>
  <label for="email"><strong>Email</strong></label>
  <br>
  <input type="email" name="email" id="email" class="regular_text" value="<?php echo $meta['email'][0]; ?>"
    style="width: 100%;">
</p>

<p>
  <label for="about"><strong>About User</strong></label>
  <br>
  <textarea name="about" name="about" rows="4" cols="50"
    style="width: 100%;"><?php echo $meta['about'][0]; ?></textarea>
</p>


<p>
  <label for="years_of_experience"><strong>Years of Experience</strong></label>
  <br>
  <input type="number" name="years_of_experience" min="1" max="15" id="years_of_experience" class="regular_text"
    value="<?php echo $meta['years_of_experience'][0]; ?>" style="width: 100%;">
</p>

<p>
  <label for="education"><strong>Education Background</strong></label>
  <br>
  <textarea name="education" name="education" rows="4" cols="50"
    style="width: 100%;"><?php echo $meta['education'][0]; ?></textarea>
</p>