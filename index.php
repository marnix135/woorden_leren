<?php
session_start();

if (!isset($_SESSION["user_id"])) {
    header("Location: /login.php");
}
include('header.php') ?>

<a href="/logout.php">Log out</a>
<ul class="list-group">
<?php
$conn = (include_once('config.php'));

$user_id = $_SESSION["user_id"];
$query = "SELECT * FROM `lists` WHERE `user_id` = {$user_id}";

if ($result = $conn->query($query)) {
    while ($row = $result->fetch_assoc()):
 ?>
        <a href="/list/view.php?id=<?php echo $row["id"]; ?>" class="list-group-item">
            <h4 class="list-group-item-heading"><?php echo $row["title"];?></h4>
            <p class="list-group-item-text"><?php echo $row["language_1"];?> - <?php echo $row["language_2"];?></p>
        </a>
<?php
    endwhile;

    $result->free();
}

$conn->close();
?>
</ul>
<?php include('footer.php') ?>
