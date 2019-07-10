<?php
// We want Featured Images on Pages and Posts
add_theme_support('post-thumbnails');


// Don't resize Featured Images
add_action('after_setup_theme', 'my_thumbnail_size', 11);
function my_thumbnail_size() {
  set_post_thumbnail_size();
}


// Don't wrap images in P tags
add_filter('the_content', 'filter_ptags_on_images');
function filter_ptags_on_images($content){
  return preg_replace('/<p>\s*(<a .*>)?\s*(<img .* \/>)\s*(<\/a>)?\s*<\/p>/iU', '\1\2\3', $content);
}


// Wrap video embed code in DIV for responsive goodness
add_filter('embed_oembed_html', 'my_oembed_filter', 10, 4);
function my_oembed_filter($html, $url, $attr, $post_ID) {
  $return = '<div class="video">'.$html.'</div>';
  return $return;
}


// Remove emojis (and other crud)
add_action('init', 'disable_wp_emojicons');
function disable_wp_emojicons() {
  remove_action('admin_print_styles', 'print_emoji_styles');
  remove_action('wp_head', 'print_emoji_detection_script', 7);
  remove_action('admin_print_scripts', 'print_emoji_detection_script');
  remove_action('wp_print_styles', 'print_emoji_styles');
  remove_filter('wp_mail', 'wp_staticize_emoji_for_email');
  remove_filter('the_content_feed', 'wp_staticize_emoji');
  remove_filter('comment_text_rss', 'wp_staticize_emoji');
  add_filter('emoji_svg_url', '__return_false');
  add_filter('tiny_mce_plugins', 'disable_emojicons_tinymce');

  remove_action('wp_head', 'rsd_link');
  remove_action('wp_head', 'wlwmanifest_link');
  remove_action('wp_head', 'wp_generator');
  remove_action('wp_head', 'start_post_rel_link');
  remove_action('wp_head', 'index_rel_link');
  remove_action('wp_head', 'adjacent_posts_rel_link');
}

function disable_emojicons_tinymce($plugins) {
  if (is_array($plugins)) {
    return array_diff($plugins, array('wpemoji'));
  } else {
    return array();
  }
}


add_action('wp_head', 'meta_og', 5);
function meta_og() {
  global $post;
  if (is_single()) {
    if(has_post_thumbnail($post->ID))
      $img_src = wp_get_attachment_image_src(get_post_thumbnail_id($post->ID), 'thumbnail');
    $excerpt = strip_tags($post->post_content);
    $excerpt_more = '';
    if (strlen($excerpt ) > 155) {
      $excerpt = substr($excerpt,0,155);
      $excerpt_more = ' ...';
    }
    $excerpt = str_replace('"', '', $excerpt);
    $excerpt = str_replace("'", '', $excerpt);
    $excerptwords = preg_split('/[\n\r\t ]+/', $excerpt, -1, PREG_SPLIT_NO_EMPTY);
    array_pop($excerptwords);
    $excerpt = implode(' ', $excerptwords) . $excerpt_more;
    ?>

    <meta name="author" content="HW Woodwork">
    <meta name="description" content="<?php echo $excerpt; ?>">
    <meta property="og:title" content="<?php echo the_title(); ?>">
    <meta property="og:description" content="<?php echo $excerpt; ?>">
    <meta property="og:type" content="article">
    <meta property="og:url" content="<?php echo the_permalink(); ?>">
    <meta property="og:site_name" content="HW Woodwork">
    <meta property="og:image" content="<?php echo $img_src[0]; ?>">
    <?php
  } else {
    return;
  }
}


// Enqueue scripts and styles
add_action('wp_enqueue_scripts', 'my_styles');
function my_styles() {
  wp_enqueue_style('style', get_template_directory_uri() . '/style.css', array(), filemtime(get_template_directory() . '/style.css'));

  wp_deregister_script('jquery');
  wp_enqueue_script('jquery', 'https://ajax.googleapis.com/ajax/libs/jquery/3.3.1/jquery.min.js');
}


// Define menus
add_action('init', 'register_my_menus');
function register_my_menus() {
  register_nav_menus(
    array(
      'primary-menu' => __('Primary Menu'),
      'top-menu' => __('Top Menu'),
      'footer-col1' => __('Footer Menu Column 1'),
      'footer-col2' => __('Footer Menu Column 2'),
      'footer-col3' => __('Footer Menu Column 3'),
      'footer-col4' => __('Footer Menu Column 4')
    )
  );
}


// Disable visual editor on certain pages
add_filter('user_can_richedit', 'disable_visual_editor');
function disable_visual_editor($can) {
  global $post;

  if (
    $post->ID == 2 ||  // Home page
    $post->ID == 42 || // Home section - Plants & Animals
    $post->ID == 44 || // Home section - Support
    $post->ID == 46    // Home section - Map
  ) return false;

  return $can;
}


/*
*  Add second text box to "The Friends Mission" edit page
*/
add_action('add_meta_boxes', 'the_friends_mission_metabox');
function the_friends_mission_metabox() {
  global $post;

  if ($post->post_name == 'the-friends-mission') {
    add_meta_box('tfm_section2_mb', 'Our Committees', 'tfm_section2_mb_content', 'page', 'normal');
  }
}

function tfm_section2_mb_content($post) {
  wp_editor(html_entity_decode($post->tfm_section2, ENT_QUOTES), 'tfm_section2', array('textarea_rows' => 25));
}

add_action('save_post', 'tfm_section2_save');
function tfm_section2_save($post_id) {
  if (!empty($_POST['tfm_section2'])) {
    update_post_meta($post_id, 'tfm_section2', $_POST['tfm_section2']);
  } else {
    delete_post_meta($post_id, 'tfm_section2');
  }
}


/*
*  Add text boxes to "Meet the Friends" edit page
*/
add_action('edit_form_after_title', 'meet_the_friends_metabox');
function meet_the_friends_metabox() {
  global $post;

  if ($post->post_name == 'meet-the-friends') {
    remove_post_type_support('page', 'editor');

    echo '<style>#wp-content-wrap { z-index: 1; }</style>';

    echo '<h3 style="margin: 1.5em 0 0.2em;">Officers</h3>';
    wp_editor(html_entity_decode($post->mtf_officers, ENT_QUOTES), 'mtf_officers', array('textarea_rows' => 20));

    echo '<h3 style="margin: 1.5em 0 0.2em;">Directors</h3>';
    wp_editor(html_entity_decode($post->mtf_directors, ENT_QUOTES), 'mtf_directors', array('textarea_rows' => 20));

    echo '<h3 style="margin: 1.5em 0 0.2em;">Advisors</h3>';
    wp_editor(html_entity_decode($post->mtf_advisors, ENT_QUOTES), 'mtf_advisors', array('textarea_rows' => 20));

    echo '<h3 style="margin: 1.5em 0 0.2em;">Honorary Directors</h3>';
    wp_editor(html_entity_decode($post->mtf_honorary_directors, ENT_QUOTES), 'mtf_honorary_directors', array('textarea_rows' => 20));

    echo '<h3 style="margin: 1.5em 0 0.2em;">Our Friends</h3>';
    wp_editor(html_entity_decode($post->mtf_friends, ENT_QUOTES), 'mtf_friends', array('textarea_rows' => 20, 'wpautop' => false, 'tinymce' => false));
  }
}

add_action('save_post', 'meet_the_friends_save');
function meet_the_friends_save($post_id) {
  if (!empty($_POST['mtf_officers'])) {
    update_post_meta($post_id, 'mtf_officers', $_POST['mtf_officers']);
  } else {
    delete_post_meta($post_id, 'mtf_officers');
  }

  if (!empty($_POST['mtf_directors'])) {
    update_post_meta($post_id, 'mtf_directors', $_POST['mtf_directors']);
  } else {
    delete_post_meta($post_id, 'mtf_directors');
  }

  if (!empty($_POST['mtf_advisors'])) {
    update_post_meta($post_id, 'mtf_advisors', $_POST['mtf_advisors']);
  } else {
    delete_post_meta($post_id, 'mtf_advisors');
  }

  if (!empty($_POST['mtf_honorary_directors'])) {
    update_post_meta($post_id, 'mtf_honorary_directors', $_POST['mtf_honorary_directors']);
  } else {
    delete_post_meta($post_id, 'mtf_honorary_directors');
  }

  if (!empty($_POST['mtf_friends'])) {
    update_post_meta($post_id, 'mtf_friends', $_POST['mtf_friends']);
  } else {
    delete_post_meta($post_id, 'mtf_friends');
  }
}


/*
*  Add additional text boxes to "About the Bog" edit page
*/
add_action('add_meta_boxes', 'about_the_bog_metabox');
function about_the_bog_metabox() {
  global $post;

  if ($post->post_name == 'about-the-bog') {
    add_meta_box('atb_section2_mb', 'Visiting the Bog', 'atb_section2_mb_content', 'page', 'normal');
    add_meta_box('atb_altmaps_mb', 'Download Maps', 'atb_altmaps_mb_content', 'page', 'normal');
  }
}

function atb_section2_mb_content($post) {
  wp_editor(html_entity_decode($post->atb_section2, ENT_QUOTES), 'atb_section2', array('textarea_rows' => 25));
}

function atb_altmaps_mb_content($post) {
  wp_editor(html_entity_decode($post->atb_altmaps, ENT_QUOTES), 'atb_altmaps', array('textarea_rows' => 5, 'tinymce' => false));
}

add_action('save_post', 'atb_section2_save');
function atb_section2_save($post_id) {
  if (!empty($_POST['atb_section2'])) {
    update_post_meta($post_id, 'atb_section2', $_POST['atb_section2']);
  } else {
    delete_post_meta($post_id, 'atb_section2');
  }

  if (!empty($_POST['atb_altmaps'])) {
    update_post_meta($post_id, 'atb_altmaps', $_POST['atb_altmaps']);
  } else {
    delete_post_meta($post_id, 'atb_altmaps');
  }
}


/*
*  Add additional text boxes to "Plants & Animals" edit page
*/
add_action('add_meta_boxes', 'animal_lists_metabox');
function animal_lists_metabox() {
  global $post;

  if ($post->post_name == 'plants-and-animals') {
    add_meta_box('plants_mb', 'Animal Lists', 'animal_lists_mb_content', 'page', 'normal');
  }
}

function animal_lists_mb_content($post) {
  wp_editor(html_entity_decode($post->animal_lists, ENT_QUOTES), 'animal_lists');
}

add_action('save_post', 'animal_lists_save');
function animal_lists_save($post_id) {
  if (!empty($_POST['animal_lists'])) {
    update_post_meta($post_id, 'animal_lists', $_POST['animal_lists']);
  } else {
    delete_post_meta($post_id, 'animal_lists');
  }
}


/*
*  Add additional text boxes to "Get Involved" edit page
*/
add_action('add_meta_boxes', 'get_involved_metabox');
function get_involved_metabox() {
  global $post;

  if ($post->post_name == 'get-involved') {
    add_meta_box('get_involved_options_mb', 'How Can I Get Involved in the Cedarburg Bog?', 'get_involved_options_mb_content', 'page', 'normal');
  }
}

function get_involved_options_mb_content($post) {
  $meta = get_post_meta($post->ID);

  for ($i = 1; $i <= 12; $i++) {
    if (array_key_exists('get_involved_option'.$i, $meta)) echo '<textarea name="get_involved_option'.$i.'" style="margin: 1em 0; width: 100%; height: 6em;">'.$meta['get_involved_option'.$i][0].'</textarea>';
  }

  ?>
  <input type="button" class="button add-another" value="Add An Option">

  <script>
    var i = $('#get_involved_options_mb .inside TEXTAREA').size() + 1;

    $(".add-another").click(function(e){
      e.preventDefault();
      $("#get_involved_options_mb .inside").append('<textarea name="get_involved_option'+i+'" style="margin: 1em 0; width: 100%; height: 6em;"></textarea>');
      i++;
    });
  </script>
  <?php
}

add_action('save_post', 'get_involved_save');
function get_involved_save($post_id) {
  for ($i = 1; $i <= 12; $i++) {
    if (!empty($_POST['get_involved_option'.$i])) {
      update_post_meta($post_id, 'get_involved_option'.$i, $_POST['get_involved_option'.$i]);
    } else {
      delete_post_meta($post_id, 'get_involved_option'.$i);
    }
  }
}


/*
*  Add additional text boxes to "Contact" edit page
*/
add_action('add_meta_boxes', 'contact_metabox');
function contact_metabox() {
  global $post;

  if ($post->post_name == 'contact') {
    add_meta_box('contact_altmaps_mb', 'Alternate Maps', 'contact_altmaps_mb_content', 'page', 'normal');
  }
}

function contact_altmaps_mb_content($post) {
  wp_editor(html_entity_decode($post->contact_altmaps, ENT_QUOTES), 'contact_altmaps', array('textarea_rows' => 5, 'tinymce' => false));
}

add_action('save_post', 'contact_save');
function contact_save($post_id) {
  if (!empty($_POST['contact_altmaps'])) {
    update_post_meta($post_id, 'contact_altmaps', $_POST['contact_altmaps']);
  } else {
    delete_post_meta($post_id, 'contact_altmaps');
  }
}


/*
*  Events
*/
date_default_timezone_set(get_option('timezone_string'));

add_action('init', 'events');
function events() {
  register_post_type('events',
    array(
      'labels' => array(
        'name' => 'Events',
        'singular_name' => 'Event',
        'add_new' => 'Add New',
        'add_new_item' => 'Add New Event',
        'edit_item' => 'Edit Event',
        'new_item' => 'New Event',
        'view_item' => 'View Event',
        'search_items' => 'Search Events',
        'not_found' => 'No Events found',
        'not_found_in_trash' => 'No Events found in Trash',
      ),
      'show_ui' => true,
      'menu_position' => 50,
      'menu_icon' => 'dashicons-calendar-alt',
      'supports' => array('title', 'editor'),
      'has_archive' => true,
      'publicly_queryable' => true,
      'show_in_nav_menus' => true
    )
  );
}

// Place fields after the title
add_action('edit_form_after_title', 'events_after_title');
function events_after_title($post) {
  if (get_post_type() == 'events') {
    wp_enqueue_script('datepicker', 'https://cdnjs.cloudflare.com/ajax/libs/bootstrap-datepicker/1.3.1/js/bootstrap-datepicker.min.js', true);
    wp_enqueue_style('datepicker-style', 'https://cdnjs.cloudflare.com/ajax/libs/bootstrap-datepicker/1.3.1/css/datepicker3.min.css', true);
    wp_enqueue_script('timepicker', 'https://cdn.jsdelivr.net/npm/timepicker/jquery.timepicker.min.js', true);
    wp_enqueue_style('timepicker-style', 'https://cdn.jsdelivr.net/npm/timepicker/jquery.timepicker.min.css', true);
    ?>

    <script>
      jQuery(document).ready(function(){
        jQuery('#event_date').datepicker({ autoclose: true });
        jQuery('#event_start_time, #event_end_time').timepicker({'scrollDefault': 'now', 'timeFormat': 'g:i A'});
      });
    </script>
    
    <br>

    <input type="text" name="event_date" placeholder="Event Date" value="<?php if ($post->event_date != "") echo date("m/d/Y", $post->event_date); ?>" id="event_date" autocomplete="off"><br>
    <br>
    
    <input type="text" name="event_start_time" placeholder="Start Time" value="<?php if ($post->event_date != "" && date("g:i A", $post->event_date) != "12:00 AM") echo date("g:i A", $post->event_date); ?>" id="event_start_time" autocomplete="off">
    &mdash;
    <input type="text" name="event_end_time" placeholder="End Time" value="<?php if ($post->event_end_time != "") echo date("g:i A", $post->event_end_time); ?>" id="event_end_time" autocomplete="off"><br>
    You may set a Start Time with no End Time.<br>
    <br>

    <input type="text" name="event_location" placeholder="Location" value="<?php if ($post->event_location != "") echo $post->event_location; ?>" id="event_location"><br>
    Try to make the location address as detailed as possible so a Google map can be generated.<br>
    For example, "3095 Blue Goose Rd, Saukville, WI 53080".
    <?php
  }
}

add_action('admin_head', 'events_css');
function events_css() {
  if (get_post_type() == 'events') {
    echo '<style>
      #post-body-content #event_date, #post-body-content #event_start_time,
      #post-body-content #event_end_time, #post-body-content #event_location {
        width: 7em;
        padding: 3px 8px;
        font-size: 1.6em;
        line-height: 100%;
        height: 1.6em;
        outline: 0;
        margin: 0 0 3px;
        background-color: #fff;
      }
      #post-body-content #event_location { width: 100%; }
    </style>';
  }
}

add_filter('wp_insert_post_data', 'events_custom_permalink');
function events_custom_permalink($data) {
  if ($data['post_type'] == 'events') {
    $data['post_name'] = sanitize_title($data['post_title'].'-'.date("m-d-Y", strtotime($_POST['event_date'])));
  }
  return $data;
}

add_action('save_post', 'events_save');
function events_save($post_id) {
  if (!empty($_POST['event_date'])) {
    update_post_meta($post_id, 'event_date', strtotime($_POST['event_date']." ".$_POST['event_start_time']));
  } else {
    delete_post_meta($post_id, 'event_date');
  }

  if (!empty($_POST['event_end_time'])) {
    update_post_meta($post_id, 'event_end_time', strtotime($_POST['event_date']." ".$_POST['event_end_time']));
  } else {
    delete_post_meta($post_id, 'event_end_time');
  }

  if (!empty($_POST['event_location'])) {
    update_post_meta($post_id, 'event_location', $_POST['event_location']);
  } else {
    delete_post_meta($post_id, 'event_location');
  }
}

add_action('wp_trash_post', 'events_skip_trash');
function events_skip_trash($post_id) {
  if (get_post_type($post_id) == 'events') {
    wp_delete_post($post_id, true);
    remove_action('deleted_post', 'action_deleted_post', 10, 1);
  }
}

add_filter('manage_events_posts_columns', 'set_custom_edit_events_columns');
function set_custom_edit_events_columns($columns) {
  $columns['event_date'] = "Event Date";
  $columns['event_date_time'] = "Time";

  unset($columns['date']);

  return $columns;
}

add_action('manage_events_posts_custom_column', 'custom_events_column', 10, 2);
function custom_events_column($column, $post_id) {
  global $post;

  switch ($column) {
    case 'event_date':
      $edate = date("m/d/Y", $post->event_date);
      echo $edate;
      break;
    case 'event_date_time':
      if (date("g:iA", $post->event_date) != "12:00AM") echo date("g:iA", $post->event_date);
      if (date("g:iA", $post->event_date) != "12:00AM" && date("g:iA", $post->event_end_time) != "12:00AM")
        echo "-".date("g:iA", $post->event_end_time);
      break;
  }
}

add_filter('manage_edit-events_sortable_columns', 'set_custom_events_sortable_columns');
function set_custom_events_sortable_columns($columns) {
  $columns['event_date'] = 'event_date';
  return $columns;
}

add_action('pre_get_posts', 'events_custom_orderby', 4);
function events_custom_orderby($query) {
  if (!$query->is_main_query() || $query->get('post_type') != 'events') return;

  $orderby = $query->get('orderby');

  if ($orderby == '' || $orderby == 'event_date') {
    $query->set('meta_key', 'event_date');
    $query->set('orderby', 'meta_value_num');
  }
}

add_filter('post_row_actions', 'disable_events_quick_edit', 10, 2);
function disable_events_quick_edit($actions, $post) {
  if ('events' === $post->post_type) unset($actions['inline hide-if-no-js']);
  return $actions;
}

add_action('wp_ajax_cal_grid_by_ajax', 'cal_grid_by_ajax_callback');
add_action('wp_ajax_nopriv_cal_grid_by_ajax', 'cal_grid_by_ajax_callback');
function cal_grid_by_ajax_callback() {
  $date = mktime(0,0,0,substr($_POST['calmonth'],-2), 1, substr($_POST['calmonth'],0,4));
  $lastmonth = mktime(0,0,0,substr($_POST['calmonth'],-2)-1, 1, substr($_POST['calmonth'],0,4));
  $nextmonth = mktime(0,0,0,substr($_POST['calmonth'],-2)+1, 1, substr($_POST['calmonth'],0,4));

  $firstday = strtotime("First day of " . date("F Y", $date) . " 00:00");
  $lastday = strtotime("First day of " . date("F Y", $nextmonth) . " 00:00");

  $days_in_month = date("j", $lastday-1);

  $start_blanks = date("w", $firstday);
  $end_blanks = (7 - date("w", $lastday));

  if ($start_blanks > $end_blanks || $start_blanks == $end_blanks || $end_blanks == 7 || $start_blanks > 3) {
    $start_blanks_content = $title;
    $end_blanks_content = "";
  } else {
    $start_blanks_content = "";
    $end_blanks_content = $title;
  }
  ?>

  <h2 data-year="<?php echo date("Y", $date); ?>"><?php echo date("F", $date); ?></h2>
  
  <div id="cal-grid">
    <?php
    global $post;

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

    $calevents = array();

    while($cal->have_posts()) : $cal->the_post();
      $MyDay = date("j", $post->event_date);
      $calevents[$MyDay][] = get_the_title() . '|' . $post->event_date . '|' . $post->event_end_time . '|' . $post->post_name;
    endwhile;
    ?>

    <table cellspacing="0">
      <tr>
        <th>Sunday</th>
        <th>Monday</th>
        <th>Tuesday</th>
        <th>Wednesday</th>
        <th>Thursday</th>
        <th>Friday</th>
        <th>Saturday</th>
      </tr>
      <tr>
        <?php if ($start_blanks > 0 && $start_blanks < 7) { ?>
        <td colspan="<?php echo $start_blanks; ?>" class="blank">&nbsp;</td>
        <?php } ?>

        <?php
        $day_count = $start_blanks;
        $day_num = 1;

        while ($day_num <= $days_in_month) {
          echo "<td>";
            echo "<div class=\"cal-date\">$day_num</div>";

            if (isset($calevents[$day_num])) {
              $i = 1;

              foreach($calevents[$day_num] as $row) {
                $calevent = explode('|', $row);
                ?>
                <span class="<?php echo date("a", $calevent[1]); ?>">
                  <div class="popup">
                    <?php
                    echo $calevent[0];

                    if (date("g:iA", $calevent[1]) != "12:00AM") {
                      echo '<div class="date">'.date("g:iA", $calevent[1]);
                      if (date("g:iA", $calevent[1]) != "12:00AM" && date("g:iA", $calevent[2]) != "12:00AM")
                        echo "-".date("g:iA", $calevent[2]);
                      echo "</div>\n";
                    }
                    ?>
                    
                    <div class="buttons">
                      <a href="<?php echo $calevent[3]; ?>" class="button">More Info</a>
                      <a href="<?php echo home_url(); ?>/event-registration/" class="button">Register</a>
                    </div>
                  </div>
                </span>
                <?php
              }

              $i++;
            }
          echo "</td>\n";

          $day_count++;

          // Start a new row every week
          if ($day_count > 6) {
            if ($day_num != $days_in_month) echo "</tr>\n<tr>\n";
            $day_count = 0;
          }

          $day_num++;
        }
        ?>

        <?php if ($end_blanks > 0 && $end_blanks < 7) { ?>
        <td colspan="<?php echo $end_blanks; ?>" class="blank">&nbsp;</td>
        <?php } ?>
      </tr>
    </table>
    
    <div id="cal-grid-footer">
      <a href="<?php echo date("Ym", $lastmonth); ?>" class="calnav">Prev Month</a>

      <div>
        <span class="am"></span> = AM Event
        <span class="pm"></span> = PM Event
      </div>

      <a href="<?php echo date("Ym", $nextmonth); ?>" class="calnav">Next Month</a>
    </div>
  </div>
  
  <?php
  wp_die();
}


add_shortcode('events_prefooter','display_events_prefooter');
function display_events_prefooter() {
  ob_start();
  ?>

  <div id="workshops">
    <div class="site-width">
      <h2>Natural History Workshops</h2>
      
      The UWM Field Station located at the Cedarburg Bog offers a series of natural history workshops. These classes offer a unique opportunity to explore focused topics in natural history under the guidance of noted authorities. Hands-on field and laboratory investigations teach ecology, evolution, use of taxonomic keys, and techniques.<br>

      <a href="https://uwm.edu/field-station/workshops/" class="button">Learn More &amp; Register Here</a>
    </div>
  </div>

  <?php
  return ob_get_clean();
}
?>