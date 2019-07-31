<br>

<form action="https://www.paypal.com/cgi-bin/webscr" method="POST" id="baf" novalidate>
  <div>
    <div class="two-col">
      <label>
        First Name *<br>
        <input type="text" name="first_name" required>
      </label>

      <label>
        Last Name *<br>
        <input type="text" name="last_name" required>
      </label>
    </div>

    <div class="two-col">
      <label>
        Organization<br>
        <input type="text" name="organization">
      </label>
    </div>

    <div class="two-col">
      <label>
        Address *<br>
        <input type="text" name="address1" required>
      </label>

      <label>
        City *<br>
        <input type="text" name="city" required>
      </label>
    </div>

    <div class="two-col">
      <label>
        State *<br>
        <input type="text" name="state" required>
      </label>

      <label>
        Zip *<br>
        <input type="text" name="zip" required>
      </label>

      <label>
        Country *<br>
        <input type="text" name="country" required>
      </label>
    </div>

    <div class="two-col">
      <label>
        Phone *<br>
        <input type="text" name="phone" required>
      </label>

      <label>
        Email *<br>
        <input type="text" name="email" required>
      </label>
    </div>
    
    <label>Donation Amount</label><br>
    <label><input type="radio" name="donation" value="25"> $25</label>
    <label><input type="radio" name="donation" value="50"> $50</label>
    <label><input type="radio" name="donation" value="100"> $100</label>
    <input type="text" name="donation_other" placeholder="Other Amount">

    <input type="hidden" name="cmd" value="_cart">
    <input type="hidden" name="upload" value="1">
    <input type="hidden" name="business" value="lippert@gmail.com">
    <input type="hidden" name="currency_code" value="USD">
    <input type="hidden" name="item_name_1" value="Donation Amount">
    <input type="hidden" name="amount_1" required>

    <input type="text" name="fintoozler" autocomplete="off" style="position: absolute; width: 0; height: 0; padding: 0; opacity: 0;">

    <input type="submit" name="submit" value="SUBMIT">
  </div>
</form>

<script type="text/javascript">
  $(document).ready(function(){
    $('input[name="donation"]').change(function() {
      $('input[name="donation_other"]').val('');
      $('input[name="amount_1"]').val($(this).val());
    });

    $('input[name="donation_other"]').on('click change', function() {
      $('input[name="donation"]').prop('checked', false);
      $('input[name="amount_1"]').val($(this).val());
    });

    var form = '#baf';
    $(form+' input[type="submit"]').click(function(e) {
      if (!$(this).hasClass('pending')) {
        e.preventDefault();

        function formValidation() {
          var missing = 'no';

          $(form+' [required]').each(function(){
            if ($(this).val() === "") {
              $(this).addClass('alert').attr("placeholder", "REQUIRED");
              missing = 'yes';
            }

            if ($('INPUT[name="amount_1"]').val() === "") {
              $('INPUT[name="donation_other"]').addClass('alert').attr("placeholder", "REQUIRED");
              missing = 'yes';
            }
          });

          return (missing == 'no') ? true : false;
        }
        
        if (formValidation()) {
          $(this).addClass('pending');

          $.ajax({
            type: 'POST',
            url: '<?php echo get_template_directory_uri(); ?>/form-become-a-friend.php',
            data: $(form).serialize(),
            success: function() {
              $(form+' input[type="submit"]').click();
              return true;
            }
          });
        }
      } else {
        $(form).submit();
        $(this).removeClass('pending');
      }
    });
  });
</script>