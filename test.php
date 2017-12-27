<?php
ini_set('display_errors', '1');

echo sendConfirmEmail("marnix135", "barendregtmarnix@gmail.com", "fasdlfk32rosiadjqt0jasdflk");

function sendConfirmEmail($username, $email, $confirm_token) {
    $config = parse_ini_file("config.ini");
    $from_email = $config['email'];
    $domain = $config['domain'];

    $to = $email;
    $subject = "Confirm your email address";

    $headers = "From: " . $from_email . "\r\n";
    $headers .= "Content-Type: text/html; charset=ISO-8859-1\r\n";
    $headers .= "MIME-Version: 1.0\r\n";
    $headers .= "X-Mailer: PHP/" . phpversion();

    $message = file_get_contents('email_templates/confirm.html');
    $message = str_replace("#domain#", $domain, $message);
    $message = str_replace("#token#", $confirm_token, $message);
    $message = str_replace("#username#", $username, $message);

    $result = mail($email, $subject, $message, $headers);
    echo $result;

    return $result;
}

?>
