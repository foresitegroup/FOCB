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

  <meta name="description" content="The Friends of the Cedarburg Bog support stewardship and appreciation of the biologically diverse Bog through land management, preservation, research and education.">
  <meta name="keywords" content="Cedarburg bog, Cedarburg, bog, wildlife, wisconsin wildlife, preservation, nature walks, wetland, southern wisconsin, swamp, marsh, bog, southeast wisconsin, nature, nonprofit, educational programs, wildlife research">

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

  <!-- Global site tag (gtag.js) - Google Analytics [This will stop working 7/1/23] -->
  <script async src="https://www.googletagmanager.com/gtag/js?id=UA-149334021-1"></script>
  <script>
    window.dataLayer = window.dataLayer || [];
    function gtag(){dataLayer.push(arguments);}
    gtag('js', new Date());
    gtag('config', 'UA-149334021-1');
  </script>

  <!-- Global site tag (gtag.js) - Google Analytics -->
  <script async src="https://www.googletagmanager.com/gtag/js?id=G-MXPGE01RVM"></script>
  <script>
    window.dataLayer = window.dataLayer || [];
    function gtag(){dataLayer.push(arguments);}
    gtag('js', new Date());
    gtag('config', 'G-MXPGE01RVM');
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