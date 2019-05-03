<?php get_header(); ?>

<div id="page-header">
  <h1 class="site-width">Programs &amp; Events</h1>
</div>

<div id="events-calendar">
  <div class="site-width">
    <div id="events">
      <h2>Upcoming Events</h2>
      
      <div id="event-list">
        <?php
        $args = array(
          'post_type' => 'events',
          'showposts' => -1,
          'meta_query' => array(
            array('key' => 'event_date', 'value' => strtotime("Today"), 'compare' => '>=')
          ),
          'orderby' => 'meta_value',
          'order'=> 'ASC'
        );
        $events = new WP_Query($args);

        $ecount = 1;

        while($events->have_posts()) : $events->the_post();
          if ($ecount > 1) echo "<hr>\n";

          echo '<h4>'.date("n/j/Y", $post->event_date).'</h4>';

          the_title('<h3>','</h3>');

          if ($post->event_start_time != "") {
            echo '<h5>'.$post->event_start_time;
            if ($post->event_start_time != "" && $post->event_end_time != "")
              echo " - ".$post->event_end_time;
            echo "</h5>\n";
          }

          if ($post->event_location != "") echo "<h6>".$post->event_location."</h6>\n";

          $ecount++;
        endwhile; ?>
      </div> <!-- #event-list -->
    </div> <!-- #events -->

    <div id="calendar">
      <h2 data-year="2019">April</h2>

      <div id="cal-grid">
        CALENDAR GRID
      </div>
    </div>
  </div> <!-- .site-width -->
</div> <!-- #events-calendar -->

<?php get_footer(); ?>