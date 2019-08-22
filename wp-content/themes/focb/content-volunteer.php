<br>

<form action="<?php echo get_template_directory_uri(); ?>/form-volunteer.php" method="POST" id="volunteer-form" novalidate>
  <div>
    <div class="two-col">
      <label>
        First Name <span class="yellow">*</span><br>
        <input type="text" name="first_name" required>
      </label>

      <label>
        Last Name <span class="yellow">*</span><br>
        <input type="text" name="last_name" required>
      </label>
    </div>

    <div class="two-col">
      <label>
        Email <span class="yellow">*</span><br>
        <input type="text" name="email" required>
      </label>

      <label>
        Phone <span class="yellow">*</span><br>
        <input type="text" name="phone" required>
      </label>
    </div>

    <div class="two-col">
      <label>
        Address <span class="yellow">*</span><br>
        <input type="text" name="address1" required>
      </label>

      <label>
        City <span class="yellow">*</span><br>
        <input type="text" name="city" required>
      </label>
    </div>

    <div class="two-col">
      <label class="two-col-half">
        State <span class="yellow">*</span><br>
        <input type="text" name="state" required>
      </label>

      <label class="two-col-half">
        Zip <span class="yellow">*</span><br>
        <input type="text" name="zip" required>
      </label>

      <label>
        Country <span class="yellow">*</span><br>
        <input type="text" name="country" required>
      </label>
    </div>

    <label>Preferred contact method <span class="yellow">*</span></label><br>
    <input type="radio" name="contact" value="Email" id="c1">
    <label for="c1">Email</label>

    <input type="radio" name="contact" value="Phone" id="c2">
    <label for="c2">Phone</label>

    <br><br>

    <label>I would like to volunteer for... <span class="yellow">*</span></label><br>
    <input type="checkbox" name="volunteer[]" id="vol1" value="Scientific Studies">
    <label for="vol1">Scientific Studies</label>
    
    <input type="checkbox" name="volunteer[]" id="vol2" value="Office Support">
    <label for="vol2">Office Support</label>

    <input type="checkbox" name="volunteer[]" id="vol3" value="Fundraising">
    <label for="vol3">Fundraising</label>

    <input type="checkbox" name="volunteer[]" id="vol4" value="Educational Programs">
    <label for="vol4">Educational Programs</label>

    <input type="checkbox" name="volunteer[]" id="vol5" value="Invasive Species Control">
    <label for="vol5">Invasive Species Control</label>

    <input type="checkbox" name="volunteer[]" id="vol6" value="Special Events">
    <label for="vol6">Special Events</label>

    <input type="checkbox" name="volunteer[]" id="vol7" value="Trail Maintenance & Construction">
    <label for="vol7">Trail Maintenance & Construction</label>

    <br><br>

    <label>
      Other Ways I'd Like To Help<br>
      <textarea name="other_ways"></textarea>
    </label>

    <br>

    <!-- <label><span class="yellow">WE ARE GOING GREEN!</span> We will now contact you via email and send you our quarterly newsletter, The Bog Haunter, electronically.</label><br>
    <input type="checkbox" name="newsletter[]" value="Email" id="nl1">
    <label for="nl1">Add me to the email list</label>

    <br><br> -->

    <input type="text" name="fintoozler" autocomplete="off" style="position: absolute; width: 0; height: 0; padding: 0; opacity: 0;">

    <input type="submit" name="submit" value="Submit My Volunteer Form">
  </div>
</form>

<link rel="stylesheet" href="<?php echo get_template_directory_uri(); ?>/inc/jquery.fancybox.min.css">
<script src="<?php echo get_template_directory_uri(); ?>/inc/jquery.fancybox.min.js"></script>

<script type="text/javascript">
  $(document).ready(function(){
    $('#volunteer-form').submit(function(event) {
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

        if (!$("input:radio[name='contact']").is(":checked")) {
          $("input:radio[name='contact']").addClass('alert');
          missing = 'yes';
        }

        if (!$("input:checkbox[name='volunteer[]']").is(":checked")) {
          $("input:checkbox[name='volunteer[]']").addClass('alert');
          missing = 'yes';
        }

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
            $(form).find('input[type="checkbox"], input[type="radio"]').prop('checked', false);
          }
        });
      }
    });
  });
</script>

<br><br>