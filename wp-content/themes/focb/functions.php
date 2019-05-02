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
      'footer-col3' => __('Footer Menu Column 3')
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
*  Add second text box to "About the Friends" edit page
*/
add_action('add_meta_boxes', 'about_the_friends_metabox');
function about_the_friends_metabox() {
  global $post;

  if ($post->post_name == 'about-the-friends') {
    add_meta_box('what_we_do_mb', 'What We Do', 'what_we_do_mb_content', 'page', 'normal');
  }
}

function what_we_do_mb_content($post) {
  wp_editor(html_entity_decode($post->what_we_do, ENT_QUOTES), 'what_we_do', array('textarea_rows' => 25));
}

add_action('save_post', 'what_we_do_save');
function what_we_do_save($post_id) {
  if (!empty($_POST['what_we_do'])) {
    update_post_meta($post_id, 'what_we_do', $_POST['what_we_do']);
  } else {
    delete_post_meta($post_id, 'what_we_do');
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
      'menu_position' => 5,
      'menu_icon' => 'dashicons-calendar-alt',
      'supports' => array('title', 'editor')
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
    
    <input type="text" name="event_start_time" placeholder="Start Time" value="<?php if ($post->event_start_time != "") echo $post->event_start_time; ?>" id="event_start_time" autocomplete="off">
    &mdash;
    <input type="text" name="event_end_time" placeholder="End Time" value="<?php if ($post->event_end_time != "") echo $post->event_end_time; ?>" id="event_end_time" autocomplete="off"><br>
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
    $data['post_name'] = sanitize_title($data['post_title']);
  }
  return $data;
}

add_action('save_post', 'events_save');
function events_save($post_id) {
  if (!empty($_POST['event_date'])) {
    update_post_meta($post_id, 'event_date', strtotime($_POST['event_date']));
  } else {
    delete_post_meta($post_id, 'event_date');
  }

  if (!empty($_POST['event_start_time'])) {
    update_post_meta($post_id, 'event_start_time', $_POST['event_start_time']);
  } else {
    delete_post_meta($post_id, 'event_start_time');
  }

  if (!empty($_POST['event_end_time'])) {
    update_post_meta($post_id, 'event_end_time', $_POST['event_end_time']);
  } else {
    delete_post_meta($post_id, 'event_end_time');
  }

  if (!empty($_POST['event_location'])) {
    update_post_meta($post_id, 'event_location', $_POST['event_location']);
  } else {
    delete_post_meta($post_id, 'event_location');
  }
}

add_filter('manage_events_posts_columns', 'set_custom_edit_events_columns');
function set_custom_edit_events_columns($columns) {
  $columns['event_date'] = "Event Date";
  $columns['event_start_time'] = "Time";

  unset($columns['date']);

  return $columns;
}

add_action('manage_events_posts_custom_column', 'custom_events_column', 10, 2);
function custom_events_column($column, $post_id) {
  global $post;
  switch ($column) {
    case 'event_date':
      $edate = ($post->event_date != "TBD") ? date("m/d/Y", $post->event_date) : "TBD";
      echo $edate;
      break;
    case 'event_start_time':
      if ($post->event_start_time != "") echo $post->event_start_time;
      if ($post->event_start_time != "" && $post->event_end_time != "")
        echo " - ".$post->event_end_time;
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
  if (!$query->is_main_query() || 'events' != $query->get('post_type')) return;

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
?>