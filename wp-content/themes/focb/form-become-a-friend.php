<?php
include_once "inc/fintoozler.php";

if ($_POST['fintoozler'] == "") {
  if (
      $_POST['first_name'] != "" && $_POST['last_name'] != "" &&
      $_POST['address1'] != "" && $_POST['city'] != "" &&
      $_POST['state'] != "" && $_POST['zip'] != "" && $_POST['country'] != "" &&
      $_POST['phone'] != "" && $_POST['email'] != ""
     )
  {
    $Subject = "Become A Friend";
    $SendTo = "bogfriends@gmail.com";
    $Headers = "From: Become A Friend Form <donotreply@bogfriends.org>\r\n";
    $Headers .= "Reply-To: " . $_POST['email'] . "\r\n";
    // $Headers .= "Cc: cboetchr@uwm.edu\r\n";
    $Headers .= "Bcc: foresitegroupllc@gmail.com\r\n";

    $Message = $_POST['first_name'] . " " . $_POST['last_name'] . "\n";

    if ($_POST['organization'] != "") $Message .= "Organization: " . $_POST['organization'] . "\n";

    $Message .= $_POST['address1'] . "\n";
    $Message .= $_POST['city'] . ", " . $_POST['state'] . " " . $_POST['zip'] . "\n";
    $Message .= $_POST['country'] . "\n";

    $Message .= $_POST['phone'] . "\n";
    $Message .= $_POST['email'] . "\n\n";

    if ($_POST['comments'] != "") $Message .= $_POST['comments'] . "\n\n";
    
    $volunteer = "";
    if (isset($_POST['volunteer'])) {
      $volunteer = implode(", ", $_POST['volunteer']);
      $Message .= "I would like to volunteer for... " . $volunteer . "\n\n";
    }

    if ($_POST['other_ways'] != "") $Message .= "Other Ways I'd Like To Help: " . $_POST['other_ways'] . "\n\n";
    
    $newsletter = "";
    if (isset($_POST['newsletter'])) {
      $newsletter = $_POST['newsletter'];

      if ($_POST['newsletter'] == "Email") {
        $Message .= "[This person's info has been added to MailChimp.]\n\n";

        // Add info to MailChimp
        $mcdata = array(
          'email'  => $_POST['email'],
          'status' => 'subscribed',
          'firstname' => $_POST['first_name'],
          'lastname' => $_POST['last_name']
        );

        function syncMailchimp($mcdata) {
         $memberId = md5(strtolower($mcdata['email']));
          $dataCenter = substr(MAILCHIMP_API,strpos(MAILCHIMP_API,'-')+1);
          $url = 'https://' . $dataCenter . '.api.mailchimp.com/3.0/lists/' . MAILCHIMP_LIST_ID . '/members/' . $memberId;

          $json = json_encode(array(
            'email_address' => $mcdata['email'],
            'status'        => $mcdata['status'],
            'merge_fields'  => [
              'FNAME' => $mcdata['firstname'],
              'LNAME' => $mcdata['lastname']
            ]
          ));

          $ch = curl_init($url);

          curl_setopt($ch, CURLOPT_USERPWD, 'user:' . MAILCHIMP_API);
          curl_setopt($ch, CURLOPT_HTTPHEADER, array('Content-Type: application/json'));
          curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
          curl_setopt($ch, CURLOPT_TIMEOUT, 10);
          curl_setopt($ch, CURLOPT_CUSTOMREQUEST, 'PUT');
          curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);
          curl_setopt($ch, CURLOPT_POSTFIELDS, $json);

          $result = curl_exec($ch);
          $httpCode = curl_getinfo($ch, CURLINFO_HTTP_CODE);
          curl_close($ch);

          return $httpCode;
        }

        syncMailchimp($mcdata);
      } else {
        $Message .= "I prefer a paper copy of The Bog Haunter Newsletter\n\n";
      }
    }

    $Message .= "Donation Amount: $" . $_POST['amount_1'] . " [This should have been processed by PayPal; please confirm.]\n";

    $Message = stripslashes($Message);

    mail($SendTo, $Subject, $Message, $Headers);

    // Add info to local database
    require_once('../../../wp-load.php');
    global $wpdb;
    $wpdb->insert('become_a_friend',
      array(
        'first_name' => $_POST['first_name'], 'last_name' => $_POST['last_name'],
        'organization' => $_POST['organization'], 'address1' => $_POST['address1'],
        'city' => $_POST['city'], 'state' => $_POST['state'], 'zip' => $_POST['zip'],
        'country' => $_POST['country'], 'phone' => $_POST['phone'], 'email' => $_POST['email'],
        'comments' => $_POST['comments'], 'volunteer' => $volunteer,
        'other_ways' => $_POST['other_ways'], 'newsletter' => $newsletter,
        'donation' => $_POST['amount_1'], 'date_submitted' => time()
      )
    );
  }
}
?>