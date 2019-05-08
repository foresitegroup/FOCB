<?php get_header(); ?>

<div id="page-header" class="single-event">
  <div class="site-width">
    <?php
    echo date("n/j/Y", $post->event_date);

    the_title('<h1>', '</h1>');

    if (date("g:iA", $post->event_date) != "12:00AM") {
      echo date("g:iA", $post->event_date);
      if (date("g:iA", $post->event_date) != "12:00AM" && date("g:iA", $post->event_end_time) != "12:00AM")
        echo "-".date("g:iA", $post->event_end_time);
    }
    ?>
    <br>

    <a href="<?php echo home_url(); ?>/events/" class="back">Back to Calendar</a>

    <a href="<?php echo home_url(); ?>/event-registration/" class="button">Register</a>
  </div>
</div>

<div id="single-event">
  <div class="site-width">
    <?php
    if ($post->post_content != "") {
      echo '<h2>Summary</h2>';
      echo apply_filters('the_content', $post->post_content).'<br>';
    }

    if ($post->event_location != "") { ?>
      <h2>Location</h2>
      <div class="location">
        <?php echo apply_filters('the_content', $post->event_location); ?>
      </div>

      <div class="location-map">
        <?php $address = str_replace(array(',',' '), array('%2C','+'), $post->event_location); ?>
        <iframe src="https://maps.google.com/maps?q=<?php echo $address; ?>&z=14&output=embed" frameborder="0" allowfullscreen></iframe>
      </div>
      <br>
    <?php } ?>

    <div style="text-align: center;">
      <a href="<?php echo home_url(); ?>/event-registration/" class="button">Register For This Event</a>
    </div>
  </div>
</div>

<div id="workshops">
  <div class="site-width">
    <h2>Natural History Workshops</h2>
    
    The bog's onsite UWM Field Station's natural history workshops offer a unique opportunity to explore focused topics in natural history under the guidance of noted authorities. Hands-on field and laboratory investigations teach ecology, evolution, use of taxonomic keys, and techniques.<br>

    <a href="#" class="button">Learn More &amp; Register Here</a>
  </div>
</div>

<?php get_footer(); ?>