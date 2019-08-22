<?php get_header(); ?>

<div id="page-header" class="blog-header<?php if (is_single()) echo ' is-single'; ?>">
  <div class="site-width">
    <?php
    echo '<div class="blog-name">'.get_the_title(get_option('page_for_posts')).'</div>'."\n";
    echo '<h1>'.wp_title('', false).'</h1>'."\n";
    //echo '<div class="blog-date">'.get_the_date('F jS, Y').'</div>'."\n";
    echo '<a href="'.get_permalink(get_option('page_for_posts')).'">&laquo; Back to News</a>'."\n";
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
        //echo '<div class="blog-date">'.get_the_date('F jS, Y').'</div>'."\n";

        echo fg_excerpt(82, '...') . "<br><br>\n";

        if (get_post_gallery()) {
          $gallery = get_post_gallery(get_the_ID(), false);
          $ids = explode(",", $gallery['ids']);
          $totalimgs = count($ids);
          $count = 1;
          
          echo '<div class="blog-index-gallery">'."\n";
          foreach($ids as $id) {
            if ($count < 6) {
              echo '<div style="background-image: url('.wp_get_attachment_url($id).');">';
              if ($count == 5 && $totalimgs > 5) {
                $remainingimgs = $totalimgs - 5;
                echo '<div>+'.$remainingimgs.'</div>';
              }
              echo "</div>\n";
            }

            $count++;
          }
          echo "</div>\n";
        }

        echo '<a href="'.get_permalink().'" class="blog-permalink">Read More &raquo;</a>'."\n";
        
        echo "<hr>\n";
      else:
        the_content();
      endif;
    endwhile;
    
    if (!is_single()) :
      echo '<div class="pagination">'."\n";
        $paginate_args = array('prev_text' => '&laquo;', 'next_text' => '&raquo;');
        echo paginate_links($paginate_args);
      echo '</div>'."\n";
    endif
    ?>
  </div>
</div>

<?php get_footer(); ?>