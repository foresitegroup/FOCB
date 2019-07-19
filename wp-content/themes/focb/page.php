<?php get_header(); ?>

<div id="page-header">
  <?php the_title('<h1 class="site-width">', '</h1>'); ?>
</div>

<div id="page">
  <div class="site-width">
    <?php
    while(have_posts()) : the_post();
      the_content();
    endwhile;
    ?>
  </div>
</div>

<?php get_footer(); ?>