<?php

$host = "localhost";
$user = "root";
$password = "";
$database = "visual_inspection";


$conn = mysqli_connect(
    $host,
    $user,
    $password,
    $database
);


if(!$conn){

    die("Database gagal terhubung");

}

?>