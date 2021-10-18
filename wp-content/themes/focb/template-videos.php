<?php
/* Template Name: Videos */

get_header();
?>

<div id="page-header">
  <?php the_title('<h1 class="site-width">', '</h1>'); ?>
</div>

<div id="videos">
  <div class="site-width featured">
    <?php
    $featured = new WP_Query(array('post_type' => 'videos', 'meta_key' => 'video_featured', 'meta_compare' => 'EXISTS'));
    while ($featured->have_posts()) : $featured->the_post();
      echo '<div class="video-wrap">';
        echo wp_oembed_get($post->videos_url);
      echo "</div>\n";

      the_title('<h3>','</h3>');
    endwhile;
    wp_reset_postdata();
    ?>
  </div>

  <hr>

  <div class="site-width">
    <?php
    $videos = new WP_Query(array('post_type' => 'videos', 'orderby' => 'menu_order', 'order'  => 'ASC', 'posts_per_page' => -1, 'meta_key' => 'video_featured', 'meta_compare' => 'NOT EXISTS'));

    while ($videos->have_posts() ) : $videos->the_post();
      echo '<div class="video">';
        echo '<div class="vid">';
          echo '<div class="video-wrap">';
            echo wp_oembed_get($post->videos_url);
          echo "</div>\n";
        echo "</div>\n";

        echo '<div class="text">';
          the_title('<h3>','</h3>');
          the_content();
        echo "</div>\n";
      echo "</div>\n";
    endwhile;

    wp_reset_postdata();
    ?>
  </div>
</div>

<?php get_footer(); ?>