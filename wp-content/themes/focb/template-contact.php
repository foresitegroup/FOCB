<?php
/* Template Name: Contact */

get_header();
?>

<div id="page-header">
  <h1 class="site-width">Contact the Friends</h1>
</div>

<div id="contact">
  <div class="site-width">
    Send the friends a message by completing the form below. You may also send an email to <a href="mailto:fieldstn@uwm.edu">fieldstn@uwm.edu</a> or call <a href="tel:262-675-6844">262-675-6844</a>.<br>
    <br>

    <h2>Contact Us</h2>

    <form action="<?php echo get_template_directory_uri(); ?>/form-contact.php" method="POST" id="contact-form" novalidate>
      <div>
        <div class="two-col">
          <label>
            First Name<span class="yellow">*</span><br>
            <input type="text" name="firstname" required>
          </label>

          <label>
            Last Name<span class="yellow">*</span><br>
            <input type="text" name="lastname" required>
          </label>
        </div> <!-- .two-col -->

        <div class="two-col">
          <label>
            Phone<span class="yellow">*</span><br>
            <input type="tel" name="phone" required>
          </label>

          <label>
            Email<span class="yellow">*</span><br>
            <input type="email" name="email" required>
          </label>
        </div> <!-- .two-col -->

        <label>
          Subject<br>
          <input type="text" name="subject">
        </label>

        <label>
          Comments<br>
          <textarea name="comments"></textarea>
        </label>

        <input type="text" name="fintoozler" autocomplete="off" style="position: absolute; width: 0; height: 0; padding: 0; opacity: 0;">

        <br>
        <input type="submit" name="submit" value="Send">
      </div> <!-- div -->
    </form>
  </div> <!-- .site-width -->
</div> <!-- #contact -->

<link rel="stylesheet" href="<?php echo get_template_directory_uri(); ?>/inc/jquery.fancybox.min.css">
<script src="<?php echo get_template_directory_uri(); ?>/inc/jquery.fancybox.min.js"></script>

<script type="text/javascript">
  $(document).ready(function(){
    $('#contact-form').submit(function(event) {
      event.preventDefault();

      var form = '#'+$(this).attr('id');
      var formData = new FormData(this);

      function formValidation() {
        var missing = 'no';

        $(form+' [required]').each(function(){
          if ($(this).val() === "") {
            $(this).addClass('alert').attr("placeholder", "REQUIRED");
            missing = 'yes';
          }
        });

        return (missing == 'no') ? true : false;
      }

      if (formValidation()) {
        $.ajax({
          type: 'POST',
          url: $(form).attr('action'),
          data: formData,
          processData: false,
          contentType: false,
          success: function(data){
            if (data) $.fancybox.open('<div id="alert-modal">'+data+'</div>');
            $(form).find('input[type="text"], input[type="email"], input[type="tel"], textarea').val('');
          }
        });
      }
    });
  });
</script>

<div id="contact-visit">
  <div class="site-width<?php if (has_post_thumbnail()) echo ' has-image'; ?>">
    <?php
    while (have_posts()) {
      the_post();

      the_content();
    }

    if ($post->contact_altmaps != "") {
      echo '<div id="alt-maps">';
        echo '<h4>Alternate Maps:</h4>';
        echo $post->contact_altmaps;
      echo '</div>';
    }

    if (has_post_thumbnail()) echo '<img src="'.get_the_post_thumbnail_url(get_the_ID(),'full').'" alt="">';
    ?>
  </div>
</div>

<div id="public-access">
  Please note, public access to the bog is located at <div>parking lots at the north and south ends of the bog.</div> The UWM field station is <u>not</u> open for public visits.
</div>

<div id="contact-map">
  <iframe src="https://www.google.com/maps/embed?pb=!1m18!1m12!1m3!1d5799.327228172728!2d-88.01429686347832!3d43.3840572165508!2m3!1f0!2f0!3f0!3m2!1i1024!2i768!4f13.1!3m3!1m2!1s0x8804ee0383622e35%3A0x123177376c94f269!2sCedarburg+Bog+State+Natural+Area!5e0!3m2!1sen!2sus!4v1562768922602!5m2!1sen!2sus" width="600" height="450" frameborder="0" style="border:0" allowfullscreen></iframe>
</div>

<?php get_footer(); ?>