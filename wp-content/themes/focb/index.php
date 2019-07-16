<?php get_header(); ?>

<div id="page-header" class="blog-header<?php if (is_single()) echo ' is-single'; ?>">
  <div class="site-width">
    <?php
    echo '<div class="blog-name">'.get_the_title(get_option('page_for_posts')).'</div>';
    echo '<h1>'.wp_title('', false).'</h1>';
    echo '<div class="blog-date">'.get_the_date('F jS, Y').'</div>';
    echo '<a href="'.get_permalink(get_option('page_for_posts')).'">&laquo; Back to News</a>';
    ?>
  </div>
</div>

<div id="blog">
  <div class="site-width<?php if (is_single()) echo ' blog-content'; ?>">
    <?php
    if (!is_single()) :
      global $wp_query;
      $args = array_merge($wp_query->query_vars, array('posts_per_page' => 5));
      query_posts($args);
    endif;

  	while (have_posts()) : the_post();
      if (!is_single()) :
        the_title('<h2>', '</h2>');
        echo '<div class="blog-date">'.get_the_date('F jS, Y').'</div>';

        echo fg_excerpt(82, '...');

        echo '<br><br><a href="'.get_permalink().'" class="blog-permalink">Read More &raquo;</a>';

        echo "<hr>";
      else:
        the_content();
      endif;
    endwhile;
    
    if (!is_single()) :
      echo '<div class="pagination">';
        $paginate_args = array('prev_text' => '&laquo;', 'next_text' => '&raquo;');
        echo paginate_links($paginate_args);
      echo '</div>';
    endif
    ?>
  </div>
</div>

<?php get_footer(); ?>