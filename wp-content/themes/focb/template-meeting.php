<?php
/* Template Name: Virtual Annual Member Meeting */

get_header();
?>

<div id="page-header" class="vamm-header">
  <?php
  echo get_bloginfo('name');
  the_title('<h1>', '</h1>');
  echo "<h2>".get_the_date('F Y')."</h2>\n";
  ?>
</div>

<div id="vamm">
  <?php while(have_posts()) : the_post(); ?>
    <div class="site-width">
      <?php
      if (isset($post->vamm_section1_title)) echo "<h2>".$post->vamm_section1_title."</h2>\n"; 
      the_content();
      ?>
    </div>

    <div class="site-width hline">
      <?php
      if (isset($post->vamm_section2_title)) echo "<h2>".$post->vamm_section2_title."</h2>\n";

      if (isset($post->vamm_section2)) echo apply_filters('the_content', $post->vamm_section2);

      if (isset($post->vamm_dirsection1_title)) echo "<h3>".$post->vamm_dirsection1_title."</h3>\n";

      if (!empty($post->vamm_newdir)) {
        for($i = 0, $j = count($post->vamm_newdir['name']); $i < $j; $i++) {
          ?>
          <div class="dir<?php if ($post->vamm_newdir['image_url'][$i] == "") echo ' noimg'; ?>">
            <div class="image"<?php if ($post->vamm_newdir['image_url'][$i] != "") echo ' style="background-image: url('.$post->vamm_newdir['image_url'][$i].');"'; ?>></div>

            <div class="text">
              <h4>
                <?php
                echo $post->vamm_newdir['name'][$i];
                if ($post->vamm_newdir['term'][$i] != "") echo " <span>".$post->vamm_newdir['term'][$i]."</span>\n";
                ?>
              </h4>

              <?php
              if ($post->vamm_newdir['text'][$i] != "") echo apply_filters('the_content', $post->vamm_newdir['text'][$i]);

              if ($post->vamm_newdir['link'][$i] != "") echo '<a href="'.home_url().'/about-the-friends/?mtf-dir#'.$post->vamm_newdir['link'][$i].'">See Full Qualifications and Bio</a>';
              ?>
            </div>
          </div>
          <?php
        }
      }

      if (isset($post->vamm_dirsection2_title)) echo "<h3>".$post->vamm_dirsection2_title."</h3>\n";

      if (!empty($post->vamm_retdir)) {
        for($i = 0, $j = count($post->vamm_retdir['name']); $i < $j; $i++) {
          ?>
          <div class="dir<?php if ($post->vamm_retdir['image_url'][$i] == "") echo ' noimg'; ?>">
            <div class="image"<?php if ($post->vamm_retdir['image_url'][$i] != "") echo ' style="background-image: url('.$post->vamm_retdir['image_url'][$i].');"'; ?>></div>

            <div class="text">
              <h4>
                <?php
                echo $post->vamm_retdir['name'][$i];
                if ($post->vamm_retdir['term'][$i] != "") echo " <span>".$post->vamm_retdir['term'][$i]."</span>\n";
                ?>
              </h4>

              <?php
              if ($post->vamm_retdir['text'][$i] != "") echo apply_filters('the_content', $post->vamm_retdir['text'][$i]);

              if ($post->vamm_retdir['link'][$i] != "") echo '<a href="'.home_url().'/about-the-friends/?mtf-dir#'.$post->vamm_retdir['link'][$i].'">See Full Qualifications and Bio</a>';
              ?>
            </div>
          </div>
          <?php
        }
      }

      if (isset($post->vamm_vote_button_text)) {
        echo '<a href="';
        echo (isset($post->vamm_vote_button_link)) ? $post->vamm_vote_button_link : "#";
        echo '" class="button">'.$post->vamm_vote_button_text."</a>\n";
      }
      ?>
    </div> <!-- .site-width hline -->

    <div class="site-width hline">
      <?php
      if (isset($post->vamm_section3_title)) echo "<h2>".$post->vamm_section3_title."</h2>\n";

      if (isset($post->vamm_section3)) echo apply_filters('the_content', $post->vamm_section3);

      if (!empty($post->vamm_reports)) {
        for($i = 0, $j = count($post->vamm_reports['title']); $i < $j; $i++) {
          ?>
          <div class="report<?php if ($post->vamm_reports['link'][$i] == "") echo ' noimg'; ?>">
            <h3><?php echo $post->vamm_reports['title'][$i]; ?></h3>

            <?php
            if ($post->vamm_reports['subtitle'][$i] != "") echo '<h5>'.$post->vamm_reports['subtitle'][$i].'</h5>';
            ?>
            
            <div class="report-content">
              <?php
              if ($post->vamm_reports['link'][$i] != "") {
                $vidid = (preg_match('%(?:youtube(?:-nocookie)?\.com/(?:[^/]+/.+/|(?:v|e(?:mbed)?)/|.*[?&]v=)|youtu\.be/)([^"&?/ ]{11})%i', $post->vamm_reports['link'][$i], $match)) ? $match[1] : "";
                ?>
              <div class="image"<?php if ($vidid == "") echo ' style="background-image: url('.$post->vamm_reports['link'][$i].');"'; ?>>
                <?php if ($vidid != "") { ?>
                  <iframe src="https://www.youtube.com/embed/<?php echo $vidid; ?>" frameborder="0" allow="accelerometer; autoplay; encrypted-media; gyroscope; picture-in-picture" allowfullscreen></iframe>
                <?php } ?>
              </div>
              <?php } ?>

              <div class="text">
                <?php
                if ($post->vamm_reports['text'][$i] != "") echo apply_filters('the_content', $post->vamm_reports['text'][$i]);
                ?>
              </div>
            </div>
          </div>
          <?php
        }
      }
      ?>
    </div> <!-- .site-width hline -->

    <div class="site-width hline">
      <?php
      if (isset($post->vamm_section4_title)) echo "<h2>".$post->vamm_section4_title."</h2>\n";

      if (isset($post->vamm_section4)) echo apply_filters('the_content', $post->vamm_section4);
      ?>
    </div> <!-- .site-width hline -->
  <?php endwhile; ?>
</div> <!-- #vamm -->

<?php get_footer(); ?>