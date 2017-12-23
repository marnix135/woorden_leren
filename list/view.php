<?php

ini_set('display_errors', '1');
session_start();

if (!isset($_SESSION["user_id"])) {
    header("Location: /login.php");
}
include('../header.php');

$conn = (include_once('../config.php'));

if (!$_GET) {
    not_found();
} else {
    $id = filter_input(INPUT_GET, 'id', FILTER_SANITIZE_NUMBER_FLOAT);

    if (!$id) {
        not_found();
    } else {
        $query = "SELECT * FROM `lists` WHERE `id` = " . $id;

        $result = $conn->query($query);

        if (!$result) {
            not_found();
        } elseif ($result->num_rows <= 0) {
            not_found();
        } else {
            $row = $result->fetch_assoc(); ?>

<div class="container list_view_container">
    <h2><?php echo $row['title']; ?></h2>
    <p><?php echo $row['date']; ?></p>
    <table class="table words_table">
        <thead>
            <tr>
                <th><?php echo $row["language_1"]?></th>
                <th><?php echo $row["language_2"]?></th>
            </tr>
        </thead>
        <tbody>
            <?php display_words($row, $conn); ?>
        <tbody>
    </table>
    <a href="/list/prepare.php?id=<?php echo $row["id"]; ?>" type="button" class="btn btn-success bottom-right">Study</a>
</div>
<?php
            $result->free();
        }
    }
}

$conn->close();

include('../footer.php');


function not_found() {
    echo "<h1>List not found.</h1>";
    http_response_code(404);
}

function display_words($row, $conn) {
    $list_id = $row["id"];
    $query = "SELECT * FROM `word_pair` WHERE `list_id` = " . $list_id;

    $result = $conn->query($query);

    if (!$result) {
        echo "No words in list.";
    } elseif ($result->num_rows <= 0) {
        echo "No words in list.";
    } else {
        while ($row = $result->fetch_assoc()) {
            $word1 = $row["word1"];
            $word2 = $row["word2"];
            echo "<tr><td>{$word1}</td><td>{$word2}</td></tr>";
        }
    }
}
?>
