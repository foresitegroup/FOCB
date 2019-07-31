<?php
/* Template Name: About the Bog */

get_header();
?>

<div id="page-header" class="about">
  <?php the_title('<h1 class="site-width">', '</h1>'); ?>
</div>

<div id="tabs">
  <input id="tab1" type="radio" name="tabs" checked>
  <label for="tab1">About the Bog</label>

  <input id="tab2" type="radio" name="tabs""<?php if ($_SERVER['QUERY_STRING'] == "plants-and-animals") echo ' checked'; ?>>
  <label for="tab2">Plants &amp; Animals</label>

  <div id="content1">
    <div id="about-section1">
      <?php
      $about = get_posts(array('name' => 'about-the-bog', 'post_type' => 'page'));
      echo apply_filters('the_content', $about[0]->post_content);
      ?>
    </div>

    <div id="about-section2" class="visiting">
      <div id="visiting-text">
        <?php
        echo apply_filters('the_content', $about[0]->atb_section2);

        if ($about[0]->atb_altmaps != "") {
          echo '<div id="alt-maps">';
            echo '<h4>Download Maps</h4>';
            echo $about[0]->atb_altmaps;
          echo '</div>';
        }
        ?>
      </div>

      <div id="visiting-map">
        <img src="<?php echo get_template_directory_uri(); ?>/images/bog-aerial-view-access-map.jpg" alt="">

        <link rel="stylesheet" href="<?php echo get_template_directory_uri(); ?>/inc/jquery.fancybox.min.css">
        <script src="<?php echo get_template_directory_uri(); ?>/inc/jquery.fancybox.min.js"></script>

        <a href="<?php echo get_template_directory_uri(); ?>/images/bog-aerial-view-access-map.jpg" data-fancybox>View Full Size</a>
      </div>
    </div>

    <div id="public-access">
      Please note, public access to the bog is located at <div>parking lots at the north and south ends of the bog.</div> The UWM field station is <u>not</u> open for public visits.
    </div>
  </div>

  <div id="content2" class="plants-tab">
    <div id="plants-section1">
      <?php
      $plants = get_posts(array('name' => 'plants-and-animals', 'post_type' => 'page'));
      echo apply_filters('the_content', $plants[0]->post_content);
      ?>
    </div> <!-- #plants-section1 -->

    <div id="plants-section2">
      <div<?php if (has_post_thumbnail($plants[0]->ID)) echo ' class="has-image"'; ?>>
        <?php
        echo apply_filters('the_content', $plants[0]->animal_lists);
        ?>
      </div>
    </div> <!-- #plants-section2 -->

    <?php if (has_post_thumbnail($plants[0]->ID)) echo '<img src="'.get_the_post_thumbnail_url($plants[0]->ID,'full').'" alt="" id="snake">'; ?>
  </div> <!-- #content2 -->
</div> <!-- #tabs -->

<?php get_footer(); ?>