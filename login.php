<?php

ini_set('display_errors', '1'); #DEBUGGING

session_start();

if (isset($_SESSION["user_id"])) {
    header("Location: /");
}

$login_failed = false;
$not_confirmed = false;

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $conn = (include_once('config.php'));

    #Sanitize user input
    $username = sanitize($_POST["username"]);
    $password = sanitize($_POST["password"]);

    $query = "SELECT * FROM `users` WHERE `username` = '{$username}'";

    if ($result = $conn->query($query)) {
        $row = $result->fetch_assoc();

        if ($row["confirmed"] == 0) {
            $not_confirmed = true;

            sendConfirmEmail($row["username"], $row["email"], $row["confirm_token"]);

        } else {
            $hashed_password = hash("sha512", $password);
            $user_password = $row["password"];

            if ($user_password == $hashed_password) {
                $_SESSION["user_id"] = $row["id"];
                header("Location: /");
            } else {
                $login_failed = true;
            }
        }

        $result->free();
    } else {
        $login_failed = true;
    }
}

function sanitize($data) {
    $data = trim($data);
    $data = stripslashes($data);
    $data = htmlspecialchars($data);

    return $data;
}

include('header.php');
?>
<div class="full-page bg-light center_parent">
    <div class="col-xs-12 col-sm-11 col-md-8 col-lg-6">
        <?php if ($login_failed): ?>
            <div class="alert alert-danger fade in">
                Incorrect username or password.
            </div>
        <?php endif; if ($not_confirmed): ?>
            <div class="alert alert-danger fade in">
                Please confirm your email address before logging in. We've sent another email to your email address. If you haven't received it, check your spam folder.
            </div>
        <?php endif; ?>
        <form action="<?php echo htmlspecialchars($_SERVER["PHP_SELF"]);?>" method="POST" class="well well-white">
            <h1 class="title">Sign in</h1>
            <div class="form-group form-group-spacious">
                <label for="username">Username:</label>
                <input type="text" class="form-control" id="username" name="username">
            </div>
            <div class="form-group form-group-spacious">
                <label for="password">Password:</label><a class="float-right" href="/resetpassword.php">Forgot password?</a>
                <input type="password" class="form-control" id="password" name="password">
            </div>
            <button type="submit" class="btn btn-success">Sign in</button>
        </form>
        <div class="well well-white text-center">Don't have an account? <a href="/register.php">Sign up</a></div>
    </div>
</div>

<?php
include('footer.php');

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

    return $result;
}
?>
