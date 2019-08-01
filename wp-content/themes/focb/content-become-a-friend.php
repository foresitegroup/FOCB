<?php //include_once get_template_directory_uri()."inc/fintoozler.php"; ?>

<br>

<form action="https://www.paypal.com/cgi-bin/webscr" method="POST" id="baf" novalidate>
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
        Organization<br>
        <input type="text" name="organization">
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

    <div class="two-col">
      <label>
        Phone <span class="yellow">*</span><br>
        <input type="text" name="phone" required>
      </label>

      <label>
        Email <span class="yellow">*</span><br>
        <input type="text" name="email" required>
      </label>
    </div>

    <label>
      Comments<br>
      <textarea name="comments"></textarea>
    </label>

    <label>I would like to volunteer for...</label><br>
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

    <label><span class="yellow">WE ARE GOING GREEN!</span> We will now contact you via email and send you our quarterly newsletter, The Bog Haunter, electronically.</label><br>
    <input type="radio" name="newsletter" value="Email" id="nl1">
    <label for="nl1">Add me to the email list</label>

    <input type="radio" name="newsletter" value="Paper" id="nl2">
    <label for="nl2">I prefer a paper copy of The Bog Haunter Newsletter</label>

    <br><br>

    <em>FOCB is a non-profit organization. All contributions are tax-deductible. FOCB will not share your personal information with any other organization.</em>

    <br><br>
    
    <label>Donation Amount <span class="yellow">*</span></label><br>
    <input type="radio" name="donation" value="25" id="amt1">
    <label for="amt1"><span class="yellow">$25</span> [Friends (Individual or Household)]</label>
    <input type="radio" name="donation" value="50" id="amt2">
    <label for="amt2"><span class="yellow">$50</span> [Special Friends]</label>
    <input type="radio" name="donation" value="100" id="amt3">
    <label for="amt3"><span class="yellow">$100</span> [Best Friends]</label>
    <input type="text" name="donation_other" placeholder="Other Amount">

    <input type="hidden" name="cmd" value="_cart">
    <input type="hidden" name="upload" value="1">
    <input type="hidden" name="business" value="bogfriends@gmail.com">
    <input type="hidden" name="currency_code" value="USD">
    <input type="hidden" name="item_name_1" value="Donation Amount">
    <input type="hidden" name="amount_1" required>

    <input type="text" name="fintoozler" autocomplete="off" style="position: absolute; width: 0; height: 0; padding: 0; opacity: 0;">

    <br><br>

    <input type="submit" name="submit" value="Submit My Friends Application & Process Donation">
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

<br><br>