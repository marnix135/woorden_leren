<?php

ini_set('display_errors', '1');
session_start();

if (!isset($_SESSION["user_id"])) {
    header("Location: /login.php");
}

$conn = (include_once('../config.php'));

if (!$_GET) {
    not_found();
} else {
    $id = filter_input(INPUT_GET, 'id', FILTER_SANITIZE_NUMBER_FLOAT);

    if (!$id) {
        not_found();
    } else {
        $list_data = get_list($id, $conn);
        $word_data = get_words($id, $conn);

        $list_data["words"] = $word_data;

        header('Content-Type: application/json');
        echo json_encode($list_data);
    }
}

$conn->close();


function get_list($id, $conn) {
    $query = "SELECT * FROM `lists` WHERE `id` = " . $id;

    $result = $conn->query($query);

    $row = $result->fetch_assoc();
    $result->free();

    return $row;

}

function get_words($id, $conn) {
    $query = "SELECT * FROM `word_pair` WHERE `list_id` = " . $id;

    $result = $conn->query($query);

    $rows=array();

    while ($row = $result->fetch_assoc()) {
        $rows[] = $row;
    }

    return $rows;
}

function not_found() {
    http_response_code(404);
    $result = array();
    $result["success"] = false;
    $result["message"] = "Failed to load list.";

    header('Content-Type: application/json');
    echo json_encode($result);
}
?>
