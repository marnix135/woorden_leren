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
        <div class="col-md-8 col-md-offset-2">
            <form action="<?php echo htmlspecialchars($_SERVER["PHP_SELF"]);?>" id="edit_list" method="POST">
                <input type="hidden" name="id" value="<?php echo $row["id"]; ?>">
                <div class="row">
                    <div class="col-md-6">
                        <h1 class="title">Edit list</h1>
                    </div>
                    <div class="col-md-6">
                        <button type="submit" class="btn btn-success" style="float: right; margin-top: 20px; margin-right: 18px;">Save</button>
                    </div>
                </div>
                <div class="form-group row">
                    <div class="col-md-6">
                        <label class="align-middle" for="title_input">Title</label>
                    </div>
                    <div class="col-md-6">
                        <input type="text" class="form-control" id="title_input" value="<?php echo $row["title"];?>">
                    </div>
                </div>
                <div class="form-group row">
                    <div class="col-md-6">
                        <label for="language_1_input">Language 1</label>
                    </div>
                    <div class="col-md-6">
                        <input type="text" class="form-control" id="language_1_input" value="<?php echo $row["language_1"];?>">
                    </div>
                </div>
                <div class="form-group row">
                    <div class="col-md-6">
                        <label for="language_2_input">Language 2</label>
                    </div>
                    <div class="col-md-6">
                        <input type="text" class="form-control" id="language_2_input" value="<?php echo $row["language_2"];?>">
                    </div>
                </div>
                <hr>
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
            </form>
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
        while ($row = $result->fetch_assoc()):
            $word1 = $row["word1"];
            $word2 = $row["word2"];
?>
            <tr>
                <td>
                    <input type="text" class="form-control" id="word1_input" value="<?php echo $word1; ?>">
                </td>
                <td>
                    <input type="text" class="form-control" id="word2_input" value="<?php echo $word2; ?>">
                </td>
            </tr>
<?php   endwhile; ?>

<?php
    }
}


#POST
if ($_SERVER["REQUEST_METHOD"] == "POST") {

}
?>
