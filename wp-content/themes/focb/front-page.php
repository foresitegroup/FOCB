<?php get_header(); ?>

<div class="site-width welcome-wrap">
  <div id="welcome">
    <h3>Welcome, from the</h3>
    <h2>Friends of Cedarburg Bog</h2>
    Scroll
    <span></span>
  </div>
</div>

<div id="home-intro">
  <img src="<?php echo get_template_directory_uri(); ?>/images/intro.png" alt="">

  <div class="site-width">
    <?php
    while (have_posts()) {
      the_post();
      echo get_the_content();
    }
    ?>
  </div>
</div>

<div id="home-plants-animals">
  <img src="<?php echo get_template_directory_uri(); ?>/images/home-plant.png" alt="" id="plant">

  <div class="site-width">
    <div>
      <?php
      $home_pa = get_post(42);
      echo $home_pa->post_content;
      ?>
    </div>
  </div>

  <img src="<?php echo get_template_directory_uri(); ?>/images/home-bird.png" alt="" id="bird">
</div>

<div id="home-support">
  <div class="site-width">
    <?php
    $home_support = get_post(44);
    echo $home_support->post_content;
    ?>
  </div>
</div>

<div id="home-map">
  <div class="site-width">
    <div>
      <?php
      $home_map = get_post(46);
      echo $home_map->post_content;
      ?>
    </div>
  </div>

  <div id="home-map-image"></div>
</div>

<?php get_footer(); ?>