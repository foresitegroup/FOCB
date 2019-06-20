<?php
/**
 * The header for our theme
 *
 * This is the template that displays all of the <head> section and everything up until <div id="content">
 *
 * @link https://developer.wordpress.org/themes/basics/template-files/#template-partials
 *
 * @package WordPress
 * @subpackage Twenty_Seventeen
 * @since 1.0
 * @version 1.0
 */

?><!DOCTYPE html>
<html <?php language_attributes(); ?> class="no-js no-svg">
<head>
	<meta charset="<?php bloginfo( 'charset' ); ?>">
	<meta name="viewport" content="width=device-width, initial-scale=1">
	<link rel="profile" href="http://gmpg.org/xfn/11">

	<title><?php echo get_bloginfo('name'); if(!is_home() || !is_front_page()) wp_title('|', true, 'left'); ?></title>
	<link rel="shortcut icon" type="image/x-icon" href="<?php echo get_template_directory_uri(); ?>/images/favicon.ico">
	<link rel="apple-touch-icon" href="<?php echo get_template_directory_uri(); ?>/images/apple-touch-icon.png">

  <link rel="stylesheet" href="https://use.fontawesome.com/releases/v5.3.1/css/all.css" integrity="sha384-mzrmE5qonljUremFsqc01SB46JvROS7bZs3IO2EmfFsd15uHvIt+Y8vEf7N7fWAU" crossorigin="anonymous">

  <?php wp_head(); ?>

  <script type="text/javascript">
    $(document).ready(function() {
      $("a[href^='http']").not("[href*='" + window.location.host + "']").prop('target','new');
      $("a[href$='.pdf']").prop('target', 'new');

      if ($(window).scrollTop() >= 105) {
        $("#main-header").addClass("mh-scroll");
      } else {
        if (!$('#toggle-menu').is(":checked")) $("#main-header").removeClass("mh-scroll");
      }

      $(window).scroll(function() {    
		    if ($(window).scrollTop() >= 105) {
		      $("#main-header").addClass("mh-scroll");
		    } else {
		      if (!$('#toggle-menu').is(":checked")) $("#main-header").removeClass("mh-scroll");
		    }
			});
      
      $('#toggle-menu').change(function() {
        if ($(this).is(":checked")) {
          $("#main-header").addClass("mh-scroll");
        } else {
          $("#main-header").removeClass("mh-scroll");
        }
      });
    });
  </script>
</head>

<body <?php body_class(); ?>>
	<div id="main-header-spacer"></div>

  <div id="main-header">
  	<div class="site-width">
  		<a href="<?php echo home_url(); ?>" id="logo"><img src="<?php echo get_template_directory_uri(); ?>/images/logo.png" alt="Friends of the Cedarburg Bog"></a>

	  	<?php wp_nav_menu(array('theme_location'=>'top-menu','container'=>'nav')); ?>
	  	
	  	<input type="checkbox" id="toggle-menu" role="button">
      <label for="toggle-menu"><div></div></label>
	  	<?php wp_nav_menu(array('theme_location'=>'primary-menu','container'=>'nav')); ?>
	  </div>
  </div>