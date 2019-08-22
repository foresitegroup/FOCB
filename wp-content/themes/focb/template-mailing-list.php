<?php
/* Template Name: Mailing List */

get_header();
?>

<div id="page-header">
  <h1 class="site-width">Mailing List</h1>
</div>

<div id="contact">
  <div class="site-width">
    <?php
    while(have_posts()) : the_post();
      the_content();
    endwhile;
    ?>

    <br>

    <form action="<?php echo get_template_directory_uri(); ?>/form-mailing-list.php" method="POST" id="mailing-list-form" novalidate>
      <div>
        <div class="two-col">
          <label>
            First Name<span class="yellow">*</span><br>
            <input type="text" name="first_name" required>
          </label>

          <label>
            Last Name<span class="yellow">*</span><br>
            <input type="text" name="last_name" required>
          </label>
        </div> <!-- .two-col -->

        <div class="two-col">
          <label>
            Email<span class="yellow">*</span><br>
            <input type="email" name="email" required>
          </label>

          <label>
            Phone<br>
            <input type="tel" name="phone">
          </label>
        </div> <!-- .two-col -->

        <div class="two-col">
          <label>
            Address<br>
            <input type="text" name="address1">
          </label>

          <label>
            City<br>
            <input type="text" name="city">
          </label>
        </div>

        <div class="two-col">
          <label>
            State<br>
            <input type="text" name="state">
          </label>

          <label>
            Zip Code<br>
            <input type="text" name="zip">
          </label>
        </div>

        <input type="text" name="fintoozler" autocomplete="off" style="position: absolute; width: 0; height: 0; padding: 0; opacity: 0;">

        <input type="submit" name="submit" value="Subscribe">
      </div> <!-- div -->
    </form>
  </div> <!-- .site-width -->
</div> <!-- #contact -->

<link rel="stylesheet" href="<?php echo get_template_directory_uri(); ?>/inc/jquery.fancybox.min.css">
<script src="<?php echo get_template_directory_uri(); ?>/inc/jquery.fancybox.min.js"></script>

<script type="text/javascript">
  $(document).ready(function(){
    $('#mailing-list-form').submit(function(event) {
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

<?php get_footer(); ?>