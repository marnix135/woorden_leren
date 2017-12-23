<?php
$config = parse_ini_file('config.ini');

$conn = new mysqli($config['host'], $config['user'], $config['password'], $config['db']);

if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}

return $conn;
?>
