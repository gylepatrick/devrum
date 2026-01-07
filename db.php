<?php

$host = "localhost";
$user = "root";
$pass = "";
$db = "devrum";


$conn = mysqli_connect($host, $user, $pass, $db);

if(!$conn) {
  echo("Problem occured! Please double check your db credentials");
}