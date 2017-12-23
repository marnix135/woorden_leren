<?php
session_start();

if (isset($_SESSION["user_id"])) {
    header("Location: /");
}

$username_error = $password_error = $password_repeat_error = "";
$username = $password = $password_repeat = "";
$user_created = false;


if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $conn = (include_once('config.php'));

    #Sanitize user input
    $username = sanitize($_POST["username"]);
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

    if ($password == "") {
        $password_error = "Please enter a password";
    }
    if (iconv_strlen($password) <= 4 || iconv_strlen($password) >= 13) {
        if ($password_error == "") {
            $password_error = "Your password should be 5 to 12 characters long";
        }
    }

    if ($password_repeat != $password) {
        $password_repeat_error = "Doesn't match your entered password";
    }

    $query = "SELECT * FROM `users` WHERE `username` = '{$username}'";

    if ($conn->query($query)->num_rows > 0 && $username_error == "") {
        $username_error = "Username already exists";
    }

    #Create new user
    if ($username_error == "" && $password_error == "" && $password_repeat_error == "") {
        $hashed_password = hash("sha512", $password);
        $query = "INSERT INTO `users` (`id`, `username`, `password`) VALUES (NULL, '{$username}', '{$hashed_password}');";

        #Successfully added user to database
        if ($conn->query($query)) {
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
<div class="login_container">
    <div class="container">
        <?php if ($user_created): ?>
            <div class="alert alert-success">
                <strong>Success!</strong> Successfully created user! <a href="/login.php" class="alert-link">Click here to log in</a>.
            </div>
        <?php endif; ?>
        <form action="<?php echo htmlspecialchars($_SERVER["PHP_SELF"]);?>" method="POST">
            <?php if ($username_error == ""): ?>
                <div class="form-group">
                    <label for="username">Username:</label>
                    <input type="text" class="form-control" id="username" name="username" value="<?php echo $username ?>">
                </div>
            <?php else: ?>
                <div class="form-group has-error">
                    <label class="control-label" for="username">Username:</label>
                    <input type="text" class="form-control" id="username" name="username" value="<?php echo $username ?>">
                    <span class="help-block"><?php echo $username_error; ?></span>
                </div>
            <?php endif; if ($password_error == ""): ?>
                <div class="form-group">
                    <label for="password">Password:</label>
                    <input type="password" class="form-control" id="password" name="password" value="<?php echo $password ?>">
                </div>
            <?php else: ?>
                <div class="form-group has-error">
                    <label class="control-label" for="password">Password:</label>
                    <input type="password" class="form-control" id="password" name="password" value="<?php echo $password ?>">
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
            <button type="submit" class="btn btn-default">Submit</button>
        </form>
    </div>
</div>
<?php include('footer.php'); ?>
