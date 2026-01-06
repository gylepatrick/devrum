<?php
session_start();
session_destroy();

$_SESSION["toast"] = ["success", "Logout succcess!"];
header("Location: login.php");
