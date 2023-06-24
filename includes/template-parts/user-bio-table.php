<section class="table-section">
  <div class="container-fluid">
    <div class="table-header">
      <h1>List of Users</h1>
      <div class="filter-select">
        <select id="status-filter" class="form-control" name="status-filter">
          <option value="">Show All</option>
          <option value="pending">Pending Request</option>
          <option value="publish">Verified Request</option>
        </select>
      </div>
    </div>
    <div class="row">
      <div class="col-md-12 col-xs-12 table-container">
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
            $user_list = new WP_Query(['post_type' => 'user_bio', 'posts_per_page' => -1, 'post_status' => ['pending', 'publish'], 'orderby' => 'post_title']);
            if ($user_list->have_posts()):
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
                    if (!empty($terms)) {
                      foreach ($terms as $term) {
                        if ($counter > 1)
                          echo ',';
                        echo $term->name;
                        $counter++;
                      }
                    } else {
                      echo '-';
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
                  <td><button id="CSV-<?= get_the_ID(); ?>" type="button" class="csv-download">Export CSV</button></td>
                </tr>
                <?php
              endwhile;
              wp_reset_postdata();
            endif; ?>
          </tbody>
        </table>
      </div>
    </div>
  </div>
</section>