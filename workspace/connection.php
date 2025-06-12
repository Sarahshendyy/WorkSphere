<?php
$localhost= "localhost";
$username= "root";
$password= "";
$database= "grad_proj";

$connect=mysqli_connect($localhost,$username,$password,$database);

session_start();
ob_start(); // CRUC

if(isset($_POST['logout'])){
    session_unset();
    session_destroy();
    header("location: ../signup&login.php");
}
?>
