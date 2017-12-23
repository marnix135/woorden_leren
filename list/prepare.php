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
    <div class="row">
        <div class="col-md-3">
            <form action="/list/run.php">
                <input type="hidden" name="id" value="<?php echo $row["id"]; ?>" />
                <div class="form-group">
                    <label for="optradio">Method: </label>
                    <div class="radio">
                        <label><input type="radio" name="a" value="study" checked>Study - <span class="light">repeat words until you know them</light></label>
                    </div>
                    <div class="radio">
                        <label><input type="radio" name="a" value="test" >Test - <span class="light">ask every words once</light></label>
                    </div>
                </div>
                <div class="form-group">
                    <label for="optradio">What to ask: </label>
                    <div class="radio">
                        <label><input type="radio" name="b" value="ltr" checked><?php echo $row["language_1"] . " -> " . $row["language_2"]; ?></label>
                    </div>
                    <div class="radio">
                        <label><input type="radio" name="b" value="rtl"><?php echo $row["language_2"] . " -> " . $row["language_1"]; ?></label>
                    </div>
                    <div class="radio">
                        <label><input type="radio" name="b" value="mixed">Mixed</label>
                    </div>
                </div>
                <button type="submit" class="btn btn-success">Start</button>
            </form>
        </div>
        <div class="col-md-9" style="border-left: 1px solid #ccc; padding-left: 40px;">
            <table class="table">
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
        </div>
    </div>
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
