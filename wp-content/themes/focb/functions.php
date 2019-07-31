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


// Customize post gallery format
add_filter('post_gallery', 'fg_post_gallery', 10, 2);
function fg_post_gallery($output, $attr) {
  wp_enqueue_style('fbstyle', get_template_directory_uri() . '/inc/jquery.fancybox.min.css', array(), filemtime(get_template_directory() . '/inc/jquery.fancybox.min.css'));
  wp_enqueue_script('fbjquery', get_template_directory_uri() . '/inc/jquery.fancybox.min.js');

  global $post;

  if (isset($attr['orderby'])) {
    $attr['orderby'] = sanitize_sql_orderby($attr['orderby']);
    if (!$attr['orderby']) unset($attr['orderby']);
  }

  extract(shortcode_atts(array(
    'order' => 'ASC',
    'orderby' => 'menu_order ID',
    'id' => $post->ID,
    'itemtag' => 'dl',
    'icontag' => 'dt',
    'captiontag' => 'dd',
    'columns' => 5,
    'size' => 'thumbnail',
    'include' => '',
    'exclude' => ''
  ), $attr));

  $id = intval($id);
  if ('RAND' == $order) $orderby = 'none';

  if (!empty($include)) {
    $include = preg_replace('/[^0-9,]+/', '', $include);
    $_attachments = get_posts(array('include' => $include, 'post_status' => 'inherit', 'post_type' => 'attachment', 'post_mime_type' => 'image', 'order' => $order, 'orderby' => $orderby));

    $attachments = array();

    foreach ($_attachments as $key => $val) {
      $attachments[$val->ID] = $_attachments[$key];
    }
  }

  if (empty($attachments)) return '';

  // Here's your actual output, you may customize it to your need
  $output = '<div class="single-post-gallery gallery-columns-' . $columns . '">'."\n";

  // Now you loop through each attachment
  foreach ($attachments as $id => $attachment) {
    $img = wp_get_attachment_image_src($id, 'full');

    $caption = (wp_get_attachment_caption($id)) ? ' data-caption="'.wp_get_attachment_caption($id).'"' : '';

    $output .= '<a href="' . $img[0] . '" data-sb="sb' . $columns . '" style="background-image: url(' . $img[0] . ');" aria-label="' . basename($img[0]) . '" data-fancybox="gallery" '.$caption.'></a>'."\n";
  }

  $output .= "</div>\n";

  return $output;
}


// ...and set default columns to 5
add_filter('media_view_settings', 'fg_gallery_defaults');
function fg_gallery_defaults($settings) {
  $settings['galleryDefaults']['columns'] = 5;
  return $settings;
}


// Custom excerpt
function fg_excerpt($limit, $more = '') {
  return wp_trim_words(strip_shortcodes(get_the_content()), $limit, $more);
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

    <meta name="author" content="<?php bloginfo('name'); ?>">
    <meta name="description" content="<?php echo $excerpt; ?>">
    <meta property="og:title" content="<?php echo the_title(); ?>">
    <meta property="og:description" content="<?php echo $excerpt; ?>">
    <meta property="og:type" content="article">
    <meta property="og:url" content="<?php echo the_permalink(); ?>">
    <meta property="og:site_name" content="<?php bloginfo('name'); ?>">
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
      'footer-col4' => __('Footer Menu Column 4'),
      'gi-tabs' => __('Get Involved Tabs')
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
    $post->ID == 46 // Home section - Map
  ) return false;

  return $can;
}

// add_filter('the_content', 'specific_no_wpautop', 9);
// function specific_no_wpautop($content) {
//   if (is_page('donate')) remove_filter('the_content', 'wpautop');
//   return $content;
// }


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


/*
*  Gallery
*/
add_action('init', 'gallery');
function gallery() {
  register_post_type('gallery', array(
    'labels' => array(
      'name' => 'Gallery',
      'singular_name' => 'Image',
      'add_new_item' => 'Add New Image',
      'edit_item' => 'Edit Image',
      'search_items' => 'Search Images',
      'not_found' => 'No Images found'
    ),
    'show_ui' => true,
    'menu_position' => 53,
    'menu_icon' => 'dashicons-camera-alt',
    'supports' => array('thumbnail'),
    'taxonomies' => array('gallery-category'),
    'has_archive' => true,
    'exclude_from_search' => false,
    'publicly_queryable' => true,
    'show_in_nav_menus' => true
  ));
}

add_action('init', 'gallery_create_taxonomy');
function gallery_create_taxonomy() {
  register_taxonomy('gallery-category', 'gallery', array('labels' => array('name' => 'Gallery Categories', 'singular_name' => 'Gallery Category'), 'hierarchical' => true));
}

add_action('do_meta_boxes', 'gallery_image_box');
function gallery_image_box() {
  remove_meta_box('postimagediv', 'gallery', 'side');
  add_meta_box('postimagediv', "Featured Image", 'post_thumbnail_meta_box', 'gallery', 'normal', 'high');

  add_meta_box('gallery_mb', 'Caption', 'gallery_mb_content', 'gallery', 'normal');

  add_meta_box('gallery_mb_footer', 'Make This a Footer Image?', 'gallery_mb_footer_content', 'gallery', 'side');
}

function gallery_mb_content($post) {
  echo '<input type="text" name="gallery_caption" value="';
  if ($post->gallery_caption != "") echo $post->gallery_caption;
  echo '" id="gallery_caption">';
}

function gallery_mb_footer_content($post) {
  echo '<select name="gallery_footer">
    <option value="">Select Position...</option>
    <option value="Left"';
    if ($post->gallery_footer == "Left") echo " selected";
    echo '>Left</option>
    <option value="Middle"';
    if ($post->gallery_footer == "Middle") echo " selected";
    echo '>Middle</option>
    <option value="Right"';
    if ($post->gallery_footer == "Right") echo " selected";
    echo '>Right</option>
  </select><br><br>';

  echo '<em style="font-size: 80%;">Note: if you set this, it will replace the current footer image (in whichever position you set).</em>';
}

add_filter('wp_insert_post_data', 'set_gallery_title', '99', 1 );
function set_gallery_title($data) {
  if($data['post_type'] == 'gallery') {
    $gallery_timestamp = (empty($data['post_date'])) ? time() : strtotime($data['post_date']);
    $gallery_title = "gallery_" . $gallery_timestamp;
    $data['post_title'] =  $gallery_title;
    $data['post_name'] = $gallery_title;
  }
  return $data;
}

add_action('save_post', 'gallery_save');
function gallery_save($post_id) {
  if (get_post_type() != 'gallery') return;

  if (!empty($_POST['gallery_caption'])) {
    update_post_meta($post_id, 'gallery_caption', $_POST['gallery_caption']);
  } else {
    delete_post_meta($post_id, 'gallery_caption');
  }

  if (!empty($_POST['gallery_footer'])) {
    global $wpdb;
    $wpdb->query("DELETE FROM $wpdb->postmeta WHERE meta_value = '" . $_POST['gallery_footer'] . "'");

    update_post_meta($post_id, 'gallery_footer', $_POST['gallery_footer']);
  } else {
    delete_post_meta($post_id, 'gallery_footer');
  }
}

add_action('admin_head', 'gallery_css');
function gallery_css() {
  if (get_post_type() == 'gallery') {
    echo '<style>
      #gallery_mb #gallery_caption { padding: 3px 8px; font-size: 1.7em; line-height: 100%; height: 1.7em; width: 100%; outline: 0; }
      .row-title { display: none; }
      .row-actions { position: static; }
    </style>';
  }
}

add_action('admin_notices', 'gallery_admin_notice');
function gallery_admin_notice() {
  $gallery_notice = "";

  global $wpdb;

  $gleft = $wpdb->get_row("SELECT * FROM $wpdb->postmeta WHERE meta_value = 'Left'");
  if ($gleft == null) $gallery_notice .= "<p>The left position site footer image has not been set. A random gallery image will used in its place.</p>";

  $gmiddle = $wpdb->get_row("SELECT * FROM $wpdb->postmeta WHERE meta_value = 'Middle'");
  if ($gmiddle == null) $gallery_notice .= "<p>The middle position site footer image has not been set. A random gallery image will used in its place.</p>";

  $gright = $wpdb->get_row("SELECT * FROM $wpdb->postmeta WHERE meta_value = 'Right'");
  if ($gright == null) $gallery_notice .= "<p>The right position site footer image has not been set. A random gallery image will used in its place.</p>";

  global $pagenow;

  if ($pagenow == 'edit.php' && get_post_type() == 'gallery' && $gallery_notice != "")
    echo '<div class="notice notice-warning">'.$gallery_notice.'</div>';
}

add_filter('bulk_actions-edit-gallery','gallery_remove_bulk_actions');
function gallery_remove_bulk_actions($actions) {
  unset( $actions['edit'] );
  return $actions;
}

add_filter('post_row_actions', 'gallery_row_actions', 10, 2);
function gallery_row_actions($actions, $post) {
  if (get_post_type() == 'gallery') {
    unset( $actions['inline hide-if-no-js'] ); // Removes the "Quick Edit" action.
  }

  return $actions;
}

add_action('admin_head', 'gallery_remove_date_filter');
function gallery_remove_date_filter() {
  if (get_post_type() == 'gallery') add_filter('months_dropdown_results', '__return_empty_array');
}

add_action('restrict_manage_posts', 'fg_filter_post_type_by_taxonomy');
function fg_filter_post_type_by_taxonomy() {
  global $typenow;
  $post_type = 'gallery'; // change to your post type
  $taxonomy  = 'gallery-category'; // change to your taxonomy
  if ($typenow == $post_type) {
    $selected      = isset($_GET[$taxonomy]) ? $_GET[$taxonomy] : '';
    $info_taxonomy = get_taxonomy($taxonomy);
    wp_dropdown_categories(array(
      'show_option_all' => __("Show All Categories"),
      'taxonomy'        => $taxonomy,
      'name'            => $taxonomy,
      'orderby'         => 'name',
      'selected'        => $selected,
      'show_count'      => true,
      'hide_empty'      => true,
    ));
  };
}

add_filter('parse_query', 'fg_convert_id_to_term_in_query');
function fg_convert_id_to_term_in_query($query) {
  global $pagenow;
  $post_type = 'gallery'; // change to your post type
  $taxonomy  = 'gallery-category'; // change to your taxonomy
  $q_vars    = &$query->query_vars;
  if ($pagenow == 'edit.php' && isset($q_vars['post_type']) && $q_vars['post_type'] == $post_type && isset($q_vars[$taxonomy]) && is_numeric($q_vars[$taxonomy]) && $q_vars[$taxonomy] != 0 ) {
    $term = get_term_by('id', $q_vars[$taxonomy], $taxonomy);
    $q_vars[$taxonomy] = $term->slug;
  }
}

add_filter('manage_gallery_posts_columns', 'set_custom_edit_gallery_columns');
function set_custom_edit_gallery_columns($columns) {
  unset($columns['title']);
  unset($columns['date']);

  $columns['title'] = "Edit Image";
  $columns['gallery_image'] = "Image";
  $columns['gallery_caption'] = "Caption";
  $columns['gallery_category'] = "Category";
  $columns['gallery_footer'] = "Footer";
  $columns['date'] = "Date";

  return $columns;
}

add_action('manage_gallery_posts_custom_column', 'custom_gallery_column', 10, 2);
function custom_gallery_column($column, $post_id) {
  switch ($column) {
    case 'gallery_image':
      echo get_the_post_thumbnail($post_id, array(100, 100));
      break;
    case 'gallery_caption':
      echo get_post_meta($post_id, 'gallery_caption', true);
      break;
    case 'gallery_category':
      $terms = get_the_terms($post_id, 'gallery-category');

      if (!empty( $terms)) {
        $out = array();

        foreach ($terms as $term) {
          $out[] = sprintf('<a href="%s">%s</a>',
            esc_url(add_query_arg(array('post_type' => $post->post_type, 'gallery-category' => $term->slug), 'edit.php')),
            esc_html(sanitize_term_field('name', $term->name, $term->term_id, 'gallery-category', 'display'))
          );
        }

        echo join(', ', $out);
      }
      break;
    case 'gallery_footer':
      echo get_post_meta($post_id, 'gallery_footer', true);
      break;
  }
}

// TO DO: This breaks entire site on live server for some reason
// add_filter('manage_edit-gallery_sortable_columns', 'set_custom_gallery_sortable_columns');
// function set_custom_gallery_sortable_columns($columns) {
//   unset($columns['title']);
//   $columns['gallery_category'] = 'gallery_category';
//   return $columns;
// }

// add_filter('posts_clauses', 'sort_gallery_taxonomy_column', 10, 2);
// function sort_gallery_taxonomy_column($clauses, $wp_query){
//   global $wpdb;

//   if (isset($wp_query->query['orderby']) && $wp_query->query['orderby'] == 'gallery_category') {
//     $clauses['join'] .= <<<SQL
//     LEFT OUTER JOIN {$wpdb->term_relationships} ON {$wpdb->posts}.ID={$wpdb->term_relationships}.object_id
//     LEFT OUTER JOIN {$wpdb->term_taxonomy} USING (term_taxonomy_id)
//     LEFT OUTER JOIN {$wpdb->terms} USING (term_id)
//     SQL;

//     $clauses['where'] .= " AND (taxonomy = 'gallery-category' OR taxonomy IS NULL)";
//     $clauses['groupby'] = "object_id";
//     $clauses['orderby'] = "GROUP_CONCAT({$wpdb->terms}.name ORDER BY name ASC)";

//     if (strtoupper($wp_query->get('order')) == 'ASC') {
//       $clauses['orderby'] .= 'ASC';
//     } else {
//       $clauses['orderby'] .= 'DESC';
//     }
//   }

//   return $clauses;
// }


/*
*  BogHaunter
*/
add_action('init', 'boghaunter');
function boghaunter() {
  register_post_type('boghaunter', array(
    'labels' => array(
      'name' => 'Bog Haunter Archive',
      'singular_name' => 'Issue',
      'add_new_item' => 'Add New Issue',
      'edit_item' => 'Edit Issue',
      'search_items' => 'Search Issues',
      'not_found' => 'No Issues found'
    ),
    'show_ui' => true,
    'menu_position' => 53,
    'menu_icon' => 'dashicons-media-document',
    'supports' => false,
    'has_archive' => true,
    'exclude_from_search' => false,
    'publicly_queryable' => true,
    'show_in_nav_menus' => true
  ));
}

add_action('do_meta_boxes', 'boghaunter_box');
function boghaunter_box() {
  add_meta_box('boghaunter_mb', 'Bog Haunter Issue', 'boghaunter_mb_content', 'boghaunter', 'normal');
}

function boghaunter_mb_content($post) {
  ?>
  <script type="text/javascript">
    jQuery(document).ready(function(){
      jQuery('#post').submit(function() {
        if (jQuery('#boghaunter_volume').val() == '') { alert("Volume REQUIRED"); jQuery('#boghaunter_volume').focus(); return false; }
        if (jQuery('#boghaunter_number').val() == '') { alert("Number REQUIRED"); jQuery('#boghaunter_number').focus(); return false; }
      });
    });
  </script>

  <?php
  if ($post->post_title != "") echo '<h2 id="bh_title">'.$post->post_title."</h2>";

  echo '<input type="text" name="boghaunter_pdf" value="';
  if ($post->boghaunter_pdf != "") echo $post->boghaunter_pdf;
  echo '" placeholder="PDF" id="boghaunter_pdf">';

  echo '<input type="button" id="boghaunter_pdf_button" class="button" value="Add/Edit PDF">';
  ?>
  <script>
    function AddEditMedia($media_id) {
      wp.media.editor.send.attachment = function(props, attachment) {
        jQuery($media_id).val(attachment.url);
      }
      wp.media.editor.open();
      return false;
    }
    jQuery('#boghaunter_pdf_button').click(function(){ AddEditMedia("#boghaunter_pdf");});
  </script>
  <?php

  echo '<input type="number" name="boghaunter_volume" value="';
  if ($post->boghaunter_volume != "") echo $post->boghaunter_volume;
  echo '" placeholder="Volume" id="boghaunter_volume" autocomplete="off">';

  echo '<input type="number" name="boghaunter_number" value="';
  if ($post->boghaunter_number != "") echo $post->boghaunter_number;
  echo '" placeholder="Number" id="boghaunter_number" autocomplete="off">';

  echo '<input type="text" name="boghaunter_season" value="';
  if ($post->boghaunter_season != "") echo $post->boghaunter_season;
  echo '" placeholder=\'Season & Year (e.g. "Fall 2012")\' id="boghaunter_season" autocomplete="off">';

  echo '<input type="text" name="boghaunter_featured" value="';
  if ($post->boghaunter_featured != "") echo $post->boghaunter_featured;
  echo '" placeholder="Featured Articles" id="boghaunter_featured" autocomplete="off">';
}

add_filter('wp_insert_post_data', 'set_boghaunter_title', '99', 1 );
function set_boghaunter_title($data) {
  if($data['post_type'] == 'boghaunter') {
    $data['post_title'] =  "Bog Haunter Vol. ".$_POST['boghaunter_volume']." No. ".$_POST['boghaunter_number'];
    $data['post_name'] = sanitize_title($data['post_title']);
  }
  return $data;
}

add_action('save_post', 'boghaunter_save');
function boghaunter_save($post_id) {
  if (get_post_type() != 'boghaunter') return;

  if (!empty($_POST['boghaunter_pdf'])) {
    update_post_meta($post_id, 'boghaunter_pdf', $_POST['boghaunter_pdf']);
  } else {
    delete_post_meta($post_id, 'boghaunter_pdf');
  }

  if (!empty($_POST['boghaunter_volume'])) {
    update_post_meta($post_id, 'boghaunter_volume', $_POST['boghaunter_volume']);
  } else {
    delete_post_meta($post_id, 'boghaunter_volume');
  }

  if (!empty($_POST['boghaunter_number'])) {
    update_post_meta($post_id, 'boghaunter_number', $_POST['boghaunter_number']);
  } else {
    delete_post_meta($post_id, 'boghaunter_number');
  }

  if (!empty($_POST['boghaunter_season'])) {
    update_post_meta($post_id, 'boghaunter_season', $_POST['boghaunter_season']);
  } else {
    delete_post_meta($post_id, 'boghaunter_season');
  }

  if (!empty($_POST['boghaunter_featured'])) {
    update_post_meta($post_id, 'boghaunter_featured', $_POST['boghaunter_featured']);
  } else {
    delete_post_meta($post_id, 'boghaunter_featured');
  }
}

add_action('admin_head', 'boghaunter_css');
function boghaunter_css() {
  if (get_post_type() == 'boghaunter') {
    wp_enqueue_media();

    echo '<style>
      #boghaunter_mb #bh_title { padding: 0 0 0.25em; font-weight: 700; }
      #boghaunter_mb INPUT { margin: 0.5em 0; padding: 3px 8px; font-size: 1.2em; line-height: 100%; height: 1.7em; width: 100%; outline: 0; }
      #boghaunter_mb INPUT#boghaunter_pdf { width: calc(100% - 115px - 0.75em); margin-right: 0.75em; }
      #boghaunter_mb INPUT#boghaunter_pdf_button { width: 115px; }
      #boghaunter_mb INPUT[type="number"] { width: 25%; margin-right: 2%; }
      #boghaunter_mb INPUT#boghaunter_season { width: 46%; }
    </style>';
  }
}

add_filter('bulk_actions-edit-boghaunter','boghaunter_remove_bulk_actions');
function boghaunter_remove_bulk_actions($actions) {
  unset( $actions['edit'] );
  return $actions;
}

add_filter('post_row_actions', 'boghaunter_row_actions', 10, 2);
function boghaunter_row_actions($actions, $post) {
  if (get_post_type() == 'boghaunter') {
    unset( $actions['inline hide-if-no-js'] ); // Removes the "Quick Edit" action.
  }

  return $actions;
}

add_action('admin_head', 'boghaunter_remove_date_filter');
function boghaunter_remove_date_filter() {
  if (get_post_type() == 'boghaunter') add_filter('months_dropdown_results', '__return_empty_array');
}

add_action('pre_get_posts','boghaunter_default_order', 9);
function boghaunter_default_order($query){
  if ($query->get('post_type')=='boghaunter') {
    $query->set('meta_query', array(
      'boghaunter_volume' => array('key' => 'boghaunter_volume', 'type' => 'numeric'),
      'boghaunter_number' => array('key' => 'boghaunter_number', 'type' => 'numeric')
    ));
    $query->set('orderby', array('boghaunter_volume' => 'DESC', 'boghaunter_number' => 'DESC')); 
  }
}

add_filter('manage_edit-boghaunter_sortable_columns', 'set_custom_boghaunter_sortable_columns');
function set_custom_boghaunter_sortable_columns($columns) {
  unset($columns['title']);
  return $columns;
}

add_filter('manage_boghaunter_posts_columns', 'set_custom_edit_boghaunter_columns');
function set_custom_edit_boghaunter_columns($columns) {
  unset($columns['date']);
  $columns['boghaunter_season'] = "Season";
  $columns['boghaunter_featured'] = "Featured Articles";
  return $columns;
}

add_action('manage_boghaunter_posts_custom_column', 'custom_boghaunter_column', 10, 2);
function custom_boghaunter_column($column, $post_id) {
  switch ($column) {
    case 'boghaunter_season':
      echo get_post_meta($post_id, 'boghaunter_season', true);
      break;
    case 'boghaunter_featured':
      echo get_post_meta($post_id, 'boghaunter_featured', true);
      break;
  }
}
?>