<?php
require "../db.php";
$postId = $_GET['post_id'];

$count = $conn->query("SELECT COUNT(*) AS total FROM likes WHERE post_id=$postId")
              ->fetch_assoc()['total'];

echo $count;
