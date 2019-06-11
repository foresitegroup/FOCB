  <div id="footer-gallery">
    <div class="site-width">
      <h2>Bog Friends Gallery Images</h2>
      <a href="#">See All Galleries</a>
    </div>
  </div>

  <div id="footer">
    <a href="<?php echo home_url(); ?>" id="footer-logo"><img src="<?php echo get_template_directory_uri(); ?>/images/logo.png" alt="Friends of the Cedarburg Bog"></a>

    <div class="site-width">
      <?php wp_nav_menu(array('theme_location'=>'footer-col1','container'=>'nav')); ?>
      <?php wp_nav_menu(array('theme_location'=>'footer-col2','container'=>'nav')); ?>
      <?php wp_nav_menu(array('theme_location'=>'footer-col3','container'=>'nav')); ?>
      <?php wp_nav_menu(array('theme_location'=>'footer-col4','container'=>'nav')); ?>
    </div>
  </div>

  <div id="copyright">&copy; <?php echo date("Y"); ?> Friends of the Cedarburg Bog</div>

  <?php wp_footer(); ?>

</body>
</html>