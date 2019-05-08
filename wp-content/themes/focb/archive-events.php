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

<div id="workshops">
  <div class="site-width">
    <h2>Natural History Workshops</h2>
    
    The bog's onsite UWM Field Station's natural history workshops offer a unique opportunity to explore focused topics in natural history under the guidance of noted authorities. Hands-on field and laboratory investigations teach ecology, evolution, use of taxonomic keys, and techniques.<br>

    <a href="#" class="button">Learn More &amp; Register Here</a>
  </div>
</div>

<?php get_footer(); ?>