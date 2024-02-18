<?php
ini_set("include_path", 'src:' . ini_get("include_path") );

require("src/PHPMailer.php");
require("src/SMTP.php");
require("src/Exception.php");

//Import PHPMailer classes into the global namespace
//These must be at the top of your script, not inside a function
use PHPMailer\PHPMailer\PHPMailer;
use PHPMailer\PHPMailer\SMTP;
use PHPMailer\PHPMailer\Exception;

// Config is stored OUTSIDE of the document root to avoid leaking
// credentials
$configFile = "{$_SERVER['DOCUMENT_ROOT']}/../.config.json";

if (!file_exists($configFile)) {
    // Cannot locate config file
    die('MF253');
}

$formConfigFile = file_get_contents($configFile);
$formConfig = json_decode($formConfigFile, true);

date_default_timezone_set($formConfig['default_timezone']);

//Create an instance; passing `true` enables exceptions
$mail = new PHPMailer($formConfig['enable_exceptions']);

$recipients = $formConfig['recipient_email'];

preg_match_all("/([\w-]+(?:\.[\w-]+)*)@((?:[\w-]+\.)*\w[\w-]{0,66})\.([a-z]{2,6}(?:\.[a-z]{2})?)/", $recipients, $addresses, PREG_OFFSET_CAPTURE);

if (!count($addresses[0])) {
    die('MF001');
}

function getRemoteIPAddress() {
    if (!empty($_SERVER['HTTP_CLIENT_IP'])) {
        return $_SERVER['HTTP_CLIENT_IP'];

    } else if (!empty($_SERVER['HTTP_X_FORWARDED_FOR'])) {
        return $_SERVER['HTTP_X_FORWARDED_FOR'];
    }
    return $_SERVER['REMOTE_ADDR'];
}

if (preg_match('/^(127\.|192\.168\.|::1)/', getRemoteIPAddress())) {
    die('MF002');
}

$template = file_get_contents('rd-mailform.tpl');

if (isset($_POST['form-type'])) {
    switch ($_POST['form-type']){
        case 'subscribe':
            $subject = $formConfig['subscribe_subject'];
            break;
        default:
            $subject = 'A message from your site visitor';
            break;
    }
} else{
    die('MF004');
}

if (isset($_POST['email'])) {
    $template = str_replace(
        array("<!-- #{FromState} -->", "<!-- #{FromEmail} -->"),
        array("Email:", $_POST['email']),
        $template);
}

if (isset($_POST['message'])) {
    $template = str_replace(
        array("<!-- #{MessageState} -->", "<!-- #{MessageDescription} -->"),
        array("Message:", $_POST['message']),
        $template);
}

// In a regular expression, the character \v is used as "anything", since this character is rare
preg_match("/(<!-- #\{BeginInfo\} -->)([^\v]*?)(<!-- #\{EndInfo\} -->)/", $template, $matches, PREG_OFFSET_CAPTURE);
foreach ($_POST as $key => $value) {
    if ($key != "counter" && $key != "email" && $key != "message" && $key != "form-type" && $key != "g-recaptcha-response" && !empty($value)){
        $info = str_replace(
            array("<!-- #{BeginInfo} -->", "<!-- #{InfoState} -->", "<!-- #{InfoDescription} -->"),
            array("", ucfirst($key) . ':', $value),
            $matches[0][0]);

        $template = str_replace("<!-- #{EndInfo} -->", $info, $template);
    }
}

$template = str_replace(
    array("<!-- #{Subject} -->", "<!-- #{SiteName} -->"),
    array($subject, $_SERVER['SERVER_NAME']),
    $template);


try {
    //Server settings
    $mail->SMTPDebug = $formConfig['smtp_debug_level']; //Enable verbose debug output
    $mail->isSMTP();
    $mail->Host       = $formConfig['smtp_host'];
    $mail->SMTPAuth   = true;
    $mail->Username   = $formConfig['smtp_user'];
    $mail->Password   = $formConfig['smtp_password'];
    $mail->Port       = $formConfig['smtp_port'];

    //Recipients
    $mail->setFrom($formConfig['email_from'], $formConfig['email_label']);
    $mail->addAddress($formConfig['recipient_email']);
    //$mail->addCC($_POST['email']);

    //Content
    $mail->isHTML(true);
    $mail->Subject = $subject;
    $mail->Body    = $template;
    // $mail->AltBody = 'This is the body in plain text for non-HTML mail clients';

    $mail->send();
    // echo 'Message has been sent';
    die('MF000');
} catch (Exception $e) {
    // echo "Message could not be sent. Mailer Error: {$mail->ErrorInfo}";
    die('MF255');
}
?>