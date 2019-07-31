<?php
if ($_POST['fintoozler'] == "") {
  if (
      $_POST['first_name'] != "" && $_POST['last_name'] != "" &&
      $_POST['address1'] != "" && $_POST['city'] != "" &&
      $_POST['state'] != "" && $_POST['zip'] != "" && $_POST['country'] != "" &&
      $_POST['phone'] != "" && $_POST['email'] != ""
     )
  {
    $Subject = "Become A Friend";
    $SendTo = "lippert@gmail.com";
    $Headers = "From: Become A Friend Form <donotreply@bogfriends.org>\r\n";
    $Headers .= "Reply-To: " . $_POST['email'] . "\r\n";
    $Headers .= "Bcc: mark@foresitegrp.com\r\n";

    $Message = $_POST['first_name'] . " " . $_POST['last_name'] . "\n";
    $Message .= $_POST['phone'] . "\n";
    $Message .= $_POST['email'] . "\n\n";

    // if ($_POST['subject'] != "") $Message .= $_POST['subject'] . "\n";
    // if ($_POST['comments'] != "") $Message .= $_POST['comments'] . "\n";

    $Message = stripslashes($Message);

    mail($SendTo, $Subject, $Message, $Headers);

    // $feedback = "Thank you for your inquiry. You will be contacted soon.";
  // } else {
    // $feedback = "Some required information is missing! Please go back and make sure all required fields are filled.";
  }

  // echo $feedback;
}
?>