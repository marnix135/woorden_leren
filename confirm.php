<?php

if (isset($_SESSION["user_id"])) {
    session_destroy();
}

$conn = (include_once('config.php'));

if (!$_GET) {
    header("Location: /");
} else {
    $token = pg_escape_string($_GET['token']);

    if (!$token) {
        header("Location: /");
    } else {
        $query = "SELECT * FROM `users` WHERE `confirm_token` = '{$token}'";

        if ($result = $conn->query($query)) {
            $row = $result->fetch_assoc();

            if ($row["confirmed"] == 0) {
                $query = "UPDATE `users` SET `confirmed` = '1', `confirm_token` = '' WHERE `users`.`id` = " . $row["id"];

                if ($conn->query($query)) {
                    session_start();

                    $_SESSION["user_id"] = $row["id"];
                    header("Location: /");
                } else {
                    header("Location: /");
                }
            } else {
                header("Location: /");
            }
        } else {
            header("Location: /");
        }
    }
}
?>
