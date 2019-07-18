<?php
/* Template Name: Get Involved Tabs */

get_header();
?>

<div id="page-header" class="gi-tabs">
  <h1 class="site-width">Get Involved</h1>
  <?php wp_nav_menu(array('theme_location'=>'gi-tabs','container'=>'nav')); ?>
</div>

<div id="get-involved-tabs" class="<?php echo get_post_field('post_name', get_post()); ?>">
  <div class="site-width">
    <?php
    while (have_posts()) : the_post();
      the_content();
    endwhile;
    ?>
  </div>
</div>

<?php get_footer(); ?>