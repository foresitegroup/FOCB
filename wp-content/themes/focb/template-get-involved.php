<?php
/* Template Name: Get Involved */

get_header();
?>

<div id="page-header">
  <?php the_title('<h1 class="site-width">', '</h1>'); ?>
</div>

<div id="get-involved">
  <div class="site-width">
    <?php
    while (have_posts()) {
      the_post();

      the_content();
    }
    ?>

    <h3>How Can I Get Involved in the Cedarburg Bog?</h3>

    <?php
    $meta = get_post_meta(get_the_ID());

    if (count(preg_grep('/^get_involved_option/', array_keys($meta))) !== 0) {
      echo '<div id="get-involved-options">';
        for ($i = 1; $i <= 12; $i++) {
          if (isset($meta['get_involved_option'.$i])) {
            echo '<div class="get-involved-option">';
              echo $meta['get_involved_option'.$i][0];
            echo '</div>';
          }
        }
      echo '</div>';
    }
    ?>
  </div>
</div>

<?php get_footer(); ?>