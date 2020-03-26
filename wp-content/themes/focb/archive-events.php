<?php get_header(); ?>

<div id="page-header">
  <h1 class="site-width">Programs &amp; Events</h1>
</div>

<div id="notice">
  <div class="site-width">
    We humans have little to no immunity to threats never before encountered by our ancestors. Hence, the seriousness of the Covid-19 virus and the need to take protective actions! The Friends of the Cedarburg Bog will be monitoring this threat based on reliable information from the State of Wisconsin, local health departments, and the Centers for Disease Control. At the present time, the Friends have suspended all events and meetings and the UWM Field Station is currently closed. Down the road, we hope to hold some or even all of our upcoming Spring and early Summer events; however, this will depend on up to the minute information provided by credible public health professionals. Thank you for your understanding and stay safe!
  </div>
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

          if (date("g:iA", $post->event_date) != "12:00AM") {
            echo '<h5>'.date("g:iA", $post->event_date);
            if (date("g:iA", $post->event_date) != "12:00AM" && date("g:iA", $post->event_end_time) != "12:00AM")
              echo "-".date("g:iA", $post->event_end_time);
            echo "</h5>\n";
          }

          if ($post->event_location != "") echo "<h6>".$post->event_location."</h6>\n";

          echo '<a href="'.$post->post_name.'" class="button">More Info</a>';
          echo '<a href="'.home_url().'/event-registration/" class="button">Register</a>';

          $ecount++;
        endwhile; ?>
      </div> <!-- #event-list -->
    </div> <!-- #events -->

    <div id="calendar"></div>

    <script>
      $(document).ready(function() {
        var ajaxurl = '<?php echo admin_url("admin-ajax.php"); ?>';

        function CalGrid(yearmonth) {
          var caldata = {
            action: 'cal_grid_by_ajax',
            calmonth: yearmonth
          }

          $.post(ajaxurl, caldata, function(response) {
            $('#calendar').html(response);
          });
        }

        CalGrid(<?php echo date("Ym"); ?>);

        $('#calendar').on('click', '.calnav', function(event) {
          event.preventDefault();
          CalGrid($(this).attr("href"));
        });
      });
    </script>

  </div> <!-- .site-width -->
</div> <!-- #events-calendar -->

<?php
echo do_shortcode('[events_prefooter]');

get_footer();
?>