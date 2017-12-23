<?php
session_start();

if (isset($_SESSION["user_id"])) {
    header("Location: /");
}

$login_failed = false;

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $conn = (include_once('config.php'));

    #Sanitize user input
    $username = sanitize($_POST["username"]);
    $password = sanitize($_POST["password"]);

    $query = "SELECT * FROM `users` WHERE `username` = '{$username}'";

    if ($result = $conn->query($query)) {
        $row = $result->fetch_assoc();

        $hashed_password = hash("sha512", $password);
        $user_password = $row["password"];

        if ($user_password == $hashed_password) {
            $_SESSION["user_id"] = $row["id"];
            header("Location: /");
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
<div class="container">
    <div class="row">
        <div class="col-sm-12 col-md-8 col-lg-6 col-md-offset-2 col-lg-offset-3" id="login_section">
            <?php if ($login_failed): ?>
                <div class="alert alert-danger fade in">
                    Incorrect username or password.
                </div>
            <?php endif; ?>
            <form action="<?php echo htmlspecialchars($_SERVER["PHP_SELF"]);?>" method="POST">
                <div class="form-group">
                    <label for="username">Username:</label>
                    <input type="text" class="form-control" id="username" name="username">
                </div>
                <div class="form-group">
                    <label for="password">Password:</label>
                    <input type="password" class="form-control" id="password" name="password">
                </div>
                <button type="submit" class="btn btn-default">Submit</button>
            </form>
        </div>
    </div>
</div>

<?php include('footer.php'); ?>
