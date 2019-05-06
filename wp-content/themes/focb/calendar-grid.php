<?php
// $displaymonth = "201906";

if (!empty($displaymonth)) {
  $date = mktime(0,0,0,substr($displaymonth,-2), 1, substr($displaymonth,0,4));
  $lastmonth = mktime(0,0,0,substr($displaymonth,-2)-1, 1, substr($displaymonth,0,4));
  $nextmonth = mktime(0,0,0,substr($displaymonth,-2)+1, 1, substr($displaymonth,0,4));
} else {
  $date = time();
  $lastmonth = mktime(1, 1, 1, date('m')-1, 1, date('Y'));
  $nextmonth = mktime(1, 1, 1, date('m')+1, 1, date('Y'));
}

$firstday = strtotime("First day of " . date("F Y", $date) . " 00:00");
$lastday = strtotime("First day of " . date("F Y", $nextmonth) . " 00:00");
?>

<h2 data-year="<?php echo date("Y", $date); ?>"><?php echo date("F", $date); ?></h2>

<a href="<?php echo get_template_directory_uri(); ?>/calendar-grid.php?<?php echo $lastmonth; ?>">PREV</a>
<a href="<?php echo get_template_directory_uri(); ?>/calendar-grid.php?<?php echo $nextmonth; ?>">NEXT</a>

<div id="cal-grid">
  <?php
  require('../../../wp-load.php');
  $calargs = array(
    'post_type' => 'events',
    'showposts' => -1,
    'meta_query' => array(
      array('key' => 'event_date', 'value' => array($firstday, $lastday), 'type' => 'numeric', 'compare' => 'BETWEEN')
    ),
    'orderby' => 'meta_value',
    'order'=> 'ASC'
  );
  $cal = new WP_Query($calargs);

  while($cal->have_posts()) : $cal->the_post();
    echo '<h4>'.date("n/j/Y", $post->event_date).'</h4>';

    the_title('<h3>','</h3>');

    if ($post->event_start_time != "") {
      echo '<h5>'.$post->event_start_time;
      if ($post->event_start_time != "" && $post->event_end_time != "")
        echo " - ".$post->event_end_time;
      echo "</h5>\n";
    }

    if ($post->event_location != "") echo "<h6>".$post->event_location."</h6>\n";
  endwhile;
  ?>
</div>