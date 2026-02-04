<?php
// Database connection parameters
$host = "localhost";
$user = "root";
$pass = "";
$dbname = "56food";

$conn = mysqli_connect($host, $user, $pass, $dbname);

if (!$conn) {
    die("connection failed:" . mysqli_connect_error());
}
