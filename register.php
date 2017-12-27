<?php
session_start();

if (isset($_SESSION["user_id"])) {
    header("Location: /");
}

$username_error = $email_error = $password_error = $password_repeat_error = "";
$username = $email = $password = $password_repeat = "";
$user_created = false;

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $conn = (include_once('config.php'));

    #Sanitize user input
    $username = sanitize($_POST["username"]);
    $email = sanitize($_POST["email"]);
    $password = sanitize($_POST["password"]);
    $password_repeat = sanitize($_POST["password_repeat"]);

    #Display errors
    if ($username == "") {
        $username_error = "Please enter a username";
    }
    if (iconv_strlen($username) <= 4 || iconv_strlen($username) >= 13) {
        if ($username_error == "") {
            $username_error = "Your username should be 5 to 12 characters long";
        }
    }

    if ($email == "") {
        $email_error = "Please enter an email address";
    }
    if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
        if ($email_error == "") {
            $email_error = "Please enter a valid email address";
        }
    }

    if ($password == "") {
        $password_error = "Please enter a password";
    }
    if (iconv_strlen($password) <= 4) {
        if ($password_error == "") {
            $password_error = "Your password should be at least 5 characters long";
        }
    }

    if ($password_repeat != $password) {
        $password_repeat_error = "Doesn't match your entered password";
    }

    $query = "SELECT * FROM `users` WHERE `username` = '{$username}' OR `email` = '{$email}'";

    $result = $conn->query($query);
    if ($result) {
        $row = $result->fetch_assoc();

        if ($row["username"] == $username && $username_error == "") {
            $username_error = "Username already exists";
        }

        if ($row["email"] == $email && $email_error == "") {
            $email_error = 'Email already in use. Try to <a href="/login.php">log in</a>.';
        }
    }

    #Create new user
    if ($username_error == "" && $email_error == "" && $password_error == "" && $password_repeat_error == "") {
        $hashed_password = hash("sha512", $password);
        $confirm_token = generateRandomString(128);
        $query = "INSERT INTO `users` (`id`, `username`, `email`, `password`, `confirmed`, `confirm_token`) VALUES (NULL, '{$username}', '{$email}', '{$hashed_password}', 0, '{$confirm_token}');";

        #Successfully added user to database
        $email_sent = sendConfirmEmail($username, $email, $confirm_token);

        if ($conn->query($query) && $email_sent) {
            $user_created = true;
        }
    }

    $conn->close();
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
        <?php if ($user_created): ?>
            <div class="alert alert-success">
                <strong>Almost done!</strong> A conformation email has been sent to <?php echo $email; ?> please click the 'confirm' button in the email. Check your spam folder if the email doesn't show up.
            </div>
        <?php endif; ?>
        <form novalidate action="<?php echo htmlspecialchars($_SERVER["PHP_SELF"]);?>" method="POST" class="well well-white">
            <h1 class="title">Sign up</h1>
            <?php if ($username_error == ""): ?>
                <div class="form-group">
                    <label for="username">Username:</label>
                    <input type="text" class="form-control" id="username" name="username" value="<?php echo $username; ?>">
                </div>
            <?php else: ?>
                <div class="form-group has-error">
                    <label class="control-label" for="username">Username:</label>
                    <input type="text" class="form-control" id="username" name="username" value="<?php echo $username; ?>">
                    <span class="help-block"><?php echo $username_error; ?></span>
                </div>
            <?php endif; if ($email_error == ""): ?>
                <div class="form-group">
                    <label for="email">Email address:</label>
                    <input type="email" class="form-control" id="email" name="email" value="<?php echo $email; ?>">
                </div>
            <?php else: ?>
                <div class="form-group has-error">
                    <label class="control-label" for="email">Email address:</label>
                    <input type="email" class="form-control" id="email" name="email" value="<?php echo $email; ?>">
                    <span class="help-block"><?php echo $email_error; ?></span>
                </div>
            <?php endif; if ($password_error == ""): ?>
                <div class="form-group">
                    <label for="password">Password:</label>
                    <input type="password" class="form-control" id="password" name="password" value="<?php echo $password; ?>">
                </div>
            <?php else: ?>
                <div class="form-group has-error">
                    <label class="control-label" for="password">Password:</label>
                    <input type="password" class="form-control" id="password" name="password" value="<?php echo $password; ?>">
                    <span class="help-block"><?php echo $password_error; ?></span>
                </div>
            <?php endif; if ($password_repeat_error == ""): ?>
                <div class="form-group">
                    <label for="password_repeat">Repeat password:</label>
                    <input type="password" class="form-control" id="password_repeat" name="password_repeat">
                </div>
            <?php else: ?>
                <div class="form-group has-error">
                    <label class="control-label" for="password_repeat">Repeat password:</label>
                    <input type="password" class="form-control" id="password_repeat" name="password_repeat">
                    <span class="help-block"><?php echo $password_repeat_error; ?></span>
                </div>
            <?php endif; ?>
            <button type="submit" class="btn btn-success">Submit</button>
        </form>
        <div class="well well-white text-center">Already have an account? <a href="/login.php">Sign in</a></div>
    </div>
</div>
<?php
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


function generateRandomString($length) {
    $characters = '0123456789abcdefghijklmnopqrstuvwxyzABCDEFGHIJKLMNOPQRSTUVWXYZ';
    $charactersLength = strlen($characters);
    $randomString = '';
    for ($i = 0; $i < $length; $i++) {
        $randomString .= $characters[rand(0, $charactersLength - 1)];
    }
    return $randomString;
}

include('footer.php'); ?>
