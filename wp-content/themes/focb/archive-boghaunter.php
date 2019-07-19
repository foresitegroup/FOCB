<?php get_header(); ?>

<div id="page-header">
  <h1 class="site-width">Bog Haunter Archive</h1>
</div>

<div id="boghaunter">
  <div class="site-width">
    <div id="boghaunter-content">
      View or download past issues of the Bog Haunter newsletter here<br>
      <br>

      <?php
      global $wp_query;
      $args = array_merge($wp_query->query_vars,
        array(
          'posts_per_page' => 10,
          'meta_query', array(
            'boghaunter_volume' => array('key' => 'boghaunter_volume', 'type' => 'numeric'),
            'boghaunter_number' => array('key' => 'boghaunter_number', 'type' => 'numeric')
          ),
          'orderby', array('boghaunter_volume' => 'DESC', 'boghaunter_number' => 'DESC')
        )
      );
      query_posts($args);

      while(have_posts()) : the_post();
        echo '<a href="'.$post->boghaunter_pdf.'" class="bh_pdf">'.get_the_title();
        if (isset($post->boghaunter_season)) echo ' &bull; '.$post->boghaunter_season;
        echo "</a>\n";

        if (isset($post->boghaunter_featured)) {
          echo '<div class="bh_featured">'."\n";
          echo '<span>Featured Articles:</span> '.$post->boghaunter_featured."\n";
          echo "</div>\n";
        }

        echo '<div class="bh_spacer"></div>'."\n";
      endwhile;
      ?>
      
      <div class="pagination">
        <?php
        $paginate_args = array('show_all' => true, 'prev_text' => '< Prev', 'next_text' => 'Next >');
        echo paginate_links($paginate_args);
        ?>
      </div>
    </div>
  </div>
</div>

<?php get_footer(); ?>