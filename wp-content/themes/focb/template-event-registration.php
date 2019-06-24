<?php
/* Template Name: Event Registration */

get_header();
?>

<div id="page-header">
  <?php the_title('<h1 class="site-width">', '</h1>'); ?>
</div>

<div id="event-registration">
  <div class="site-width">
    <?php the_post(); the_content(); ?>

    <div class="ds-form">
      <iframe src="https://forms.donorsnap.com/form?id=c6d808e7-a9cc-42c4-ade3-e30817483527"></iframe>
    </div>
  </div>
</div> <!-- #event-registration -->

<?php
echo do_shortcode('[events_prefooter]');

get_footer();
?>