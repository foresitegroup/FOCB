<?php
include_once "inc/fintoozler.php";

class Captcha {
  public function getCaptcha($SecretKey) {
    $cresponse = file_get_contents("https://www.google.com/recaptcha/api/siteverify?secret=".RECAPTCHA_SECRET_KEY."&response={$SecretKey}");
    $creturn = json_decode($cresponse);
    return $creturn;
  }
}

$oc = new Captcha();
$creturn = $oc->getCaptcha($_POST['g-recaptcha-response']);

if ($creturn->success) {
  if (
      $_POST['firstname'] != "" && $_POST['lastname'] != "" &&
      $_POST['phone'] != "" && $_POST['email'] != ""
     )
  {
    $Subject = ($_POST['subject'] != "") ? $_POST['subject'] : "Contact From Website";
    $SendTo = "bogfriends@gmail.com";
    $Headers = "From: Contact Form <donotreply@bogfriends.org>\r\n";
    $Headers .= "Reply-To: " . $_POST['email'] . "\r\n";
    $Headers .= "Bcc: foresitegroupllc@gmail.com\r\n";

    $Message = $_POST['firstname'] . " " . $_POST['lastname'] . "\n";
    $Message .= $_POST['phone'] . "\n";
    $Message .= $_POST['email'] . "\n\n";

    if ($_POST['subject'] != "") $Message .= $_POST['subject'] . "\n";
    if ($_POST['comments'] != "") $Message .= $_POST['comments'] . "\n";

    $Message = stripslashes($Message);

    mail($SendTo, $Subject, $Message, $Headers);

    $feedback = "Thank you for your inquiry. You will be contacted soon.";
  } else {
    $feedback = "Some required information is missing! Please go back and make sure all required fields are filled.";
  }

  echo $feedback;
}
?>