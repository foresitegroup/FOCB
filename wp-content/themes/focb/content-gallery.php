<div id="page-header">
  <h1 class="site-width">The Bog Friends Gallery</h1>
</div>

<div id="gallery">
  <div class="site-width gallery-cats">
    <h3>Filter By Category:</h3>

    <a href="<?php echo home_url('gallery/'); ?>"<?php if (is_post_type_archive('gallery')) echo ' class="current-cat"'; ?>>All</a>

    <?php
    global $wp;

    $tax = 'gallery-category';
    $cats = get_terms($tax);

    foreach ($cats as $cat) {
      echo '<a href="'.get_term_link($cat->slug, $tax).'"';
      if (get_term_link($cat->slug, $tax) == home_url($wp->request.'/')) echo ' class="current-cat"';
      echo '>'.$cat->name.'</a>';
    }

    $currentcat = get_queried_object();
    $cathead = ($currentcat->name == 'gallery') ? "All Photos" : "Photos of " . $currentcat->name;
    ?>
  </div>

  <div id="gallery-header">
    <h2><?php echo $cathead; ?></h2>
  </div>

  <link rel="stylesheet" href="<?php echo get_template_directory_uri(); ?>/inc/jquery.fancybox.min.css">
  <script src="<?php echo get_template_directory_uri(); ?>/inc/jquery.fancybox.min.js"></script>

  <div class="site-width gallery-index">
    <?php
    global $wp_query;
    $args = array_merge($wp_query->query_vars, array('posts_per_page' => 16));
    query_posts($args);

    while(have_posts()) : the_post();
      if (has_post_thumbnail()) {
        echo '<a href="'.get_the_post_thumbnail_url().'" style="background-image: url('.get_the_post_thumbnail_url().');" data-fancybox="gallery"';
        if ($post->gallery_caption) echo ' data-caption="'.$post->gallery_caption.'"';
        echo '></a>';
      }
    endwhile;
    ?>
  </div>

  <div class="site-width pagination">
    <?php
    $paginate_args = array('prev_text' => '< Prev', 'next_text' => 'Next >');
    echo paginate_links($paginate_args);
    ?>
  </div>
</div>