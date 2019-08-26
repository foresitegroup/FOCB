<?php
/* Template Name: About */

get_header();
?>

<div id="page-header" class="about">
  <?php the_title('<h1 class="site-width">', '</h1>'); ?>
</div>

<div id="tabs">
  <input id="tab1" type="radio" name="tabs" checked>
  <label for="tab1">The Friend's Mission</label>

  <input id="tab2" type="radio" name="tabs"<?php if ($_SERVER['QUERY_STRING'] == "meet-the-friends") echo ' checked'; ?>>
  <label for="tab2">Meet the Friends</label>

  <div id="content1">
    <div id="about-section1">
      <h2>Our Mission</h2>
      <?php
      $about = get_posts(array('name' => 'the-friends-mission', 'post_type' => 'page'));
      echo apply_filters('the_content', $about[0]->post_content);
      ?>
    </div>

    <div id="about-section2">
      <h2>Our Committees</h2>
      <?php echo apply_filters('the_content', $about[0]->tfm_section2); ?>
    </div>
  </div>

  <div id="content2">
    <div id="sub-tabs">
      <h2>Board of Directors</h2>

      We are a group of people who care about the Cedarburg Bog. We are supported by our members, donors, and volunteers.<br>
      <br>

      <span class="meet-our">Meet Our:</span>

      <input id="sub-tab-officers" type="radio" name="sub-tabs" checked>
      <label for="sub-tab-officers">Officers</label>

      <input id="sub-tab-directors" type="radio" name="sub-tabs">
      <label for="sub-tab-directors">Directors</label>

      <input id="sub-tab-advisors" type="radio" name="sub-tabs">
      <label for="sub-tab-advisors">Advisors</label>

      <input id="sub-tab-honorary-directors" type="radio" name="sub-tabs">
      <label for="sub-tab-honorary-directors">Honorary Directors</label>

      <div id="content-officers">
        <h2>OFFICERS</h2>
        <div class="site-width">
          <?php
          $meet = get_posts(array('name' => 'meet-the-friends', 'post_type' => 'page'));
          echo apply_filters('the_content', $meet[0]->mtf_officers);
          ?>
        </div>
      </div>

      <div id="content-directors">
        <h2>DIRECTORS</h2>
        <div class="site-width">
          <?php echo apply_filters('the_content', $meet[0]->mtf_directors); ?>
        </div>
      </div>

      <div id="content-advisors">
        <h2>ADVISORS</h2>
        <div class="site-width">
          <?php echo apply_filters('the_content', $meet[0]->mtf_advisors); ?>
        </div>
      </div>

      <div id="content-honorary-directors">
        <h2>HONORARY DIRECTORS</h2>
        <div class="site-width">
          <?php echo apply_filters('the_content', $meet[0]->mtf_honorary_directors); ?>
        </div>
      </div>
    </div> <!-- #sub-tabs -->

    <div id="our-friends">
      <h2>Our Friends</h2>

      <div class="site-width">
        <?php echo $meet[0]->mtf_friends; ?>
      </div>
    </div>
  </div> <!-- #content2 -->
</div> <!-- #tabs -->

<?php get_footer(); ?>