  <?php if (!is_post_type_archive('gallery') && !is_tax('gallery-category')) { ?>
  <div id="footer-gallery">
    <div id="gallery-images">
      <?php
      global $wpdb;

      $args = array(
        'post_type' => 'gallery',
        'orderby'=>'rand',
        'posts_per_page'=>'1',
        'meta_query' => array(array('key' => 'gallery_footer', 'compare' => 'NOT EXISTS'))
      );

      $gleft = $wpdb->get_row("SELECT * FROM $wpdb->postmeta WHERE meta_value = 'Left'");
      if ($gleft == null) {
        $gleftrand = new WP_Query($args);
        $gleftrand->the_post();
        $gleftimg = get_the_post_thumbnail_url();
      } else {
        $gleftimg = get_the_post_thumbnail_url($gleft->post_id);
      }

      $gmiddle = $wpdb->get_row("SELECT * FROM $wpdb->postmeta WHERE meta_value = 'Middle'", ARRAY_A);
      if ($gmiddle == null) {
        $gmiddlerand = new WP_Query($args);
        $gmiddlerand->the_post();
        $gmiddleimg = get_the_post_thumbnail_url();
      } else {
        $gmiddleimg = get_the_post_thumbnail_url($gmiddle['post_id']);
      }

      $gright = $wpdb->get_row("SELECT * FROM $wpdb->postmeta WHERE meta_value = 'Right'", ARRAY_A);
      if ($gright == null) {
        $grightrand = new WP_Query($args);
        $grightrand->the_post();
        $grightimg = get_the_post_thumbnail_url();
      } else {
        $grightimg = get_the_post_thumbnail_url($gright['post_id']);
      }
      ?>

      <div class="gallery-image" style="background-image: url(<?php echo $gleftimg; ?>);"></div>
      <div class="gallery-image" style="background-image: url(<?php echo $gmiddleimg; ?>);"></div>
      <div class="gallery-image" style="background-image: url(<?php echo $grightimg; ?>);"></div>
    </div>

    <div class="site-width">
      <h2>Bog Friends Gallery Images</h2>
      <a href="<?php echo home_url('gallery/'); ?>">See All Galleries</a>
    </div>
  </div>
  <?php } ?>

  <div id="footer">
    <a href="<?php echo home_url(); ?>" id="footer-logo"><img src="<?php echo get_template_directory_uri(); ?>/images/dnr-uwm.png" alt=""></a>

    <div class="site-width">
      <?php wp_nav_menu(array('theme_location'=>'footer-col1','container'=>'nav')); ?>
      <?php wp_nav_menu(array('theme_location'=>'footer-col2','container'=>'nav')); ?>
      <?php wp_nav_menu(array('theme_location'=>'footer-col3','container'=>'nav')); ?>
      <?php wp_nav_menu(array('theme_location'=>'footer-col4','container'=>'nav')); ?>
    </div>
  </div>

  <div id="copyright">&copy; <?php echo date("Y"); ?> Friends of the Cedarburg Bog</div>

  <?php wp_footer(); ?>

</body>
</html>