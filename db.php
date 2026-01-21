<?php

$host = "localhost";
$user = "root";
$pass = "";
$db = "dev_rum";


$conn = mysqli_connect($host, $user, $pass, $db);

if(!$conn) {
  echo("Problem occured! Please double check your db credentials");
}