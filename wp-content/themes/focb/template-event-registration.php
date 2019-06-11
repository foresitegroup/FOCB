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

<div id="workshops">
  <div class="site-width">
    <h2>Natural History Workshops</h2>
    
    The UWM Field Station located at the Cedarburg Bog offers a series of natural history workshops. These classes offer a unique opportunity to explore focused topics in natural history under the guidance of noted authorities. Hands-on field and laboratory investigations teach ecology, evolution, use of taxonomic keys, and techniques.<br>

    <a href="#" class="button">Learn More &amp; Register Here</a>
  </div>
</div>

<?php get_footer(); ?>